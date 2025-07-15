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
	
        // Pass to view
        return view('cas.success', compact('user', 'attributes'));
    }
}
