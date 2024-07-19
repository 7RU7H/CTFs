<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/includes/common.inc.php');


// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();


route_request_main();


function route_request_main()
{
    $default_page = PAGE_HOME;

    if (is_authenticated() == false) {
        header("Location: " . get_base_url() . PAGEFILE_LOGIN);
    }

    $page = grab_request_var("page", $default_page);

    display_page($page);
}


function display_page($page = PAGE_HOME)
{
    $filename = dirname(__FILE__) . '/includes/page-' . clean_path($page) . '.php';
    $errorfile = dirname(__FILE__) . '/includes/page-missing.php';

    if (file_exists($filename)) {
        include_once($filename);
    } else {
        include_once($errorfile);
    }
}
