<?php

////////////////////////////////////////////////////////////////////////////////////////
//
//                               AUTHENTICATION FUNCTIONS
//
// These functions are used to authenticate, currently basically the same as the normal
// XI code base, but with different redirects for mobile
////////////////////////////////////////////////////////////////////////////////////////



function mobile_check_authentication($redirect = true, $backend = false)
{
    global $request;

    // Check if we are authenticated, if not - redirect us (or not)
    if (!is_authenticated($backend)) {

        // Redirect the user if we need to
        if ($redirect) {
            mobile_redirect_to_login();
        }

        // If we haven't authenticated or redirected, let's let them know
        // that they are not able to access this page
        echo _("Your session has timed out.");
        exit();
    }

    // Update session
    user_update_session();

    // Check restrictions on session and redirect or state session issues
    if (user_is_restricted_area()) {
        if ($redirect) {
            redirect_to_login(true, _('You cannot access that page with a restricted session.<br>Please log into your account.'));
        }
        echo _("Your current session is not authorized to view this page.");
        exit();
    }

    // Redirect to mobile home page
    $url = get_base_url() . 'mobile/views/home.php';
    header('Location: ' . $url);
}