<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
            dd('User not authenticated1');
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
        // Check if user is a school there is an l in the attributes
        if (isset($attributes['l'])) {
            // Check if school tries to login with sch credentials
            if(!is_numeric($attributes['uid'])) {
                 Log::channel('login_as')->warning('Non-numeric UID attempted during login.', [
                    'uid' => $attributes['uid'],
                    'l' => $attributes['l'],
                    'ip' => $attributes['clientIpAddress'],
                    'time' => now(),
                ]);
                return redirect()->route('index')->withErrors(['error' => 'Για τη σύνδεση παρακαλούμε να χρησιμοποιήσετε τους κωδικούς του Myschool.']);
            }
            //extract the school name from the DN
            // $dn = $attributes['l']; // Example: "ou=50dim-patron,ou=schools,dc=sch,dc=gr"
            // $start = strpos($dn, '=') + 1;
            // $end = strpos($dn, ',');
            // $length = $end - $start;
            // $value = substr($dn, $start, $length);
            try{
                $school = School::where('code', $attributes['uid'])->firstOrFail();
                Auth::guard('school')->login($school);
                session()->regenerate();
                $school->logged_in_at = Carbon::now();   
                $school->save();
                return redirect(url('/index_school'))->with('success',"$school->name καλωσήρθατε!");
            } catch(\Exception $e) {
                // If school not found, redirect to index with error
                return redirect()->route('index')->withErrors(['error' => 'Δεν αναγνωρίστηκε το Σχολείο.']);
            }
        }
    }
}
