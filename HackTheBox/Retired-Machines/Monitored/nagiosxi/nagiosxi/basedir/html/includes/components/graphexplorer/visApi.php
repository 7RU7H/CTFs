<?php
//
// API for Graph Generation
// Copyright (c) 2016-2019 Nagios Enterprises, LLC. All rights reserved.
//

ini_set('display_errors', 'off');
@require_once(dirname(__FILE__) . '/../../common.inc.php');
@include_once(dirname(__FILE__) . '/dashlet.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables 
grab_request_vars();

// Check prereqs
check_prereqs();

// If this request is from Fusion we need to log out of our current session
$from = grab_request_var("from", "");
if ($from == "fusion") {
    // Validate that they are able to view this page
    if (!is_fusion_authenticated(true)) {
        die(_("You are not authorized to view this."));
    }
} else {
    check_authentication(false); // Check authentication if not a fusion request
}

$vtkArgs = array(); // Necessary args for different functions of VTK
require_once(dirname(__FILE__) . '/visFunctions.inc.php');

// Fetch Jquery code based on request
print "<script type='text/javascript'>";
get_jquery();
print "</script>";