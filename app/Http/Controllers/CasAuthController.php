<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            // If user is not authenticated, redirect to login page
            return redirect()->route('index')->withErrors(['error' => 'Αποτυχία ταυτοποίησης.']);
        }
        // Check if user is a teacher
        if (isset($attributes['employeenumber'])) {
            if(strlen($attributes['employeenumber']) == 6) { // 6-digit ΑΜ
                $teacher = Teacher::where('am', $attributes['employeenumber'])->firstOrFail();
                Auth::guard('teacher')->login($teacher);
                session()->regenerate();
                $teacher->logged_in_at = Carbon::now();   
                $teacher->save();
                return redirect(url('/index_teacher'))->with('success',"$teacher->name καλωσήρθατε!");
            }
            if(strlen($attributes['employeenumber']) == 9) { // 9-digit ΑΦΜ
                $teacher = Teacher::where('afm', $attributes['employeenumber'])->firstOrFail();
                Auth::guard('teacher')->login($teacher);
                session()->regenerate();
                $teacher->logged_in_at = Carbon::now();   
                $teacher->save();
                return redirect(url('/index_teacher'))->with('success',"$teacher->name καλωσήρθατε!");
            }
            
        }

        // Check if user is a school
        if (isset($attributes['l'])) {
        
        }
    }
}
