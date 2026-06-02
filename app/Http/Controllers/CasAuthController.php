<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\SchoolUsernameMappings;

class CasAuthController extends Controller
{
    public function login()
    {
        // Load the phpCAS library
        require_once app_path('Libraries/phpCAS/CAS.php');

        // Initialize phpCAS with SAML
        \phpCAS::client(SAML_VERSION_1_1, 'sso.sch.gr', 443, '');

        // Disable SSL validation (only for testing!)

        \phpCAS::setNoCasServerValidation();

        // Handle logout requests
        \phpCAS::handleLogoutRequests(['sso.sch.gr']);

        // Force authentication
        if (!\phpCAS::checkAuthentication()) {
            \phpCAS::forceAuthentication();
        }

        // Get authenticated user and attributes
        $user = \phpCAS::getUser();
        $attributes = \phpCAS::getAttributes();
        //dd($user, $attributes);
        if (!$user) {
        //    dd('User not authenticated1');
            // If user is not authenticated, redirect to login page
            return redirect()->route('index')->withErrors(['error' => 'Αποτυχία ταυτοποίησης.']);
        }
        // Check if user is a teacher
        if (isset($attributes['employeenumber'])) {
	
            if(strlen($attributes['employeenumber']) == 6) { // 6-digit ΑΜ
                try{
                    $teacher = Teacher::where('am', $attributes['employeenumber'])->firstOrFail();
                    //dd('teacher', $teacher);
                    Auth::guard('teacher')->login($teacher);
                    session()->regenerate();
                    $teacher->logged_in_at = Carbon::now();   
                    $teacher->save();
                    return redirect(url('/index_teacher'))->with('success',"$teacher->name καλωσήρθατε!");
                } catch(\Exception $e) {
                    // If teacher not found, redirect to index with error
                    return redirect()->route('index')->withErrors(['error' => 'Ο εκπαιδευτικός δεν ανήκει στη Διεύθυνση.']);
                }
                
            }
            if(strlen($attributes['employeenumber']) == 9) { // 9-digit ΑΦΜ
                try{
                    $teacher = Teacher::where('afm', $attributes['employeenumber'])->firstOrFail();
                    Auth::guard('teacher')->login($teacher);
                    session()->regenerate();
                    $teacher->logged_in_at = Carbon::now();   
                    $teacher->save();
                    return redirect(url('/index_teacher'))->with('success',"$teacher->name καλωσήρθατε!");
                } catch(\Exception $e) {
                    // If teacher not found, redirect to index with error
                    return redirect()->route('index')->withErrors(['error' => 'Ο εκπαιδευτικός δεν ανήκει στη Διεύθυνση.']);
                }
            }
            
        }
        // //////
        // $attributes['l'] = 'ou=drgn,ou=schools,dc=sch,dc=gr'; // Example value for testing
        // $attributes['uid'] = '9060169'; // Example value for testing 10dimpat
        // $attributes['clientIpAddress'] = '90.60.123.456'; // Example value for testing


        /////
        // Check if user is a school. At schools there is an l in the attributes ////
        //// If the user is a school, we will try to find it by the uid or l attribute. Schools can login with either their MySchool credentials (uid -> numeric) or their ΠΣΔ credentials (uid -> username).
        if (isset($attributes['l'])) {
            // Check if school tries to login with sch credentials
            if(!is_numeric($attributes['uid'])) {
                // If uid is not numeric, it's a username for ΠΣΔ credentials
                try{
                    $school = $this->findSchoolByUsername($attributes['uid']);
                    //dd('school', $school);
                    if ($school) {
                        // Login successful with UID fallback
                        $this->loginSchool($school, $attributes);
                        Log::channel('login_as')->info('School login successful by username', [
                            'school_code' => $school->code,
                            'school_name' => $school->name,
                            'uid' => $attributes['uid'],
                            'l' => $attributes['l'],
                            'time' => now(),
                        ]);
                        return redirect(url('/index_school'))->with('success', "$school->name καλωσήρθατε!");
                    } else {
                        throw new \Exception('School not found by Username');
                    }
                } catch(\Exception $uidException) {
                    // Login by username failed
                        Log::channel('login_as')->error('Login by Username failed: ', [
                            'school_code' => 'Δε βρέθηκε.',
                            'school_name' => 'Δε βρέθηκε.',
                            'uid' => $attributes['uid'],
                            'l' => $attributes['l'],
                            'time' => now()
                        ]);
                    //extract the school name from the attributes['l'] (DN format)
                    $dn = $attributes['l']; // Example: "ou=50dim-patron,ou=schools,dc=sch,dc=gr"
                    $start = strpos($dn, '=') + 1;
                    $end = strpos($dn, ',');
                    $length = $end - $start;
                    $value = substr($dn, $start, $length);
                    try {
                        
                        // Second attempt: Search by extracted value from DN
                        $school = School::where('mail', 'like', '%' . $value . '%')->firstOrFail();
                        
                        $this->loginSchool($school, $attributes);
                        Log::channel('login_as')->info('Login by mail success: ', [
                            'school_code' => $school->code,
                            'school_name' => $school->name,
                            'uid' => $attributes['uid'],
                            'l' => $attributes['l'],
                            'time' => now()
                        ]);
                        return redirect(url('/index_school'))->with('success', "$school->name καλωσήρθατε!");
        
                    } catch(\Exception $e) {
                        // Both methods failed
                        Log::channel('login_as')->error('Login by username and mail failed: ', [
                            'school_code' => 'Δε βρέθηκε.',
                            'school_name' => 'Δε βρέθηκε.',
                            'uid' => $attributes['uid'],
                            'l' => $attributes['l'],
                            'time' => now(),
                        ]);
                        // Redirect to index with error
                        return redirect()->route('index')->withErrors([
                            'error' => 'Δεν αναγνωρίστηκε το Σχολείο. Δοκιμάστε να συνδεθείτε με τους κωδικούς του Myschool.'
                        ]);
                    }
                }
            }
            try{
                $school = School::where('code', $attributes['uid'])->firstOrFail();
                $this->loginSchool($school, $attributes);
                Log::channel('login_as')->info('School login successful with MySchool credentials', [
                    'school_code' => $school->code,
                    'school_name' => $school->name,
                    'uid' => $attributes['uid'],
                    'ip' => $attributes['clientIpAddress'],
                    'time' => now(),
                ]);
                return redirect(url('/index_school'))->with('success',"$school->name καλωσήρθατε!");
            } catch(\Exception $e) {
                if(!isset($school)) {
                    return redirect()->route('index')->withErrors(['error' => 'Παρακαλούμε συνδεθείτε με Λογαριασμό Εκπαιδευτικού!!! Αν το σφάλμα επιμένει συνδεθείτε με ανώνυμη περιήγηση.']);
                }

                Log::channel('login_as')->error('School login failed with MySchool credentials', [
                    'school_code' => $school->code,
                    'school_name' => $school->name,
                    'uid' => $attributes['uid'],
                    'ip' => $attributes['clientIpAddress'],
                    'time' => now(),
                ]);
                // If school not found, redirect to index with error
                return redirect()->route('index')->withErrors(['error' => 'Δεν αναγνωρίστηκε το Σχολείο.']);
            }
        }
    }

    private function findSchoolByUsername($username)
    {
        $school_code = SchoolUsernameMappings::getSchoolCodeByUsername($username);
        if ($school_code) {
            return School::where('code', $school_code)->first();    
        } else {
            return null;
        }
    }

    private function loginSchool($school, $attributes)
    {
        Auth::guard('school')->login($school);
        session()->regenerate();
        $school->logged_in_at = Carbon::now();  
        $school->save();
        
        Log::channel('login_as')->info('School login successful', [
            // 'school_code' => $school->code,
            // 'school_name' => $school->name,
            // 'uid' => $attributes['uid'],
            // 'ip' => $attributes['clientIpAddress'],
            // 'time' => now(),
        ]);
    }
}
