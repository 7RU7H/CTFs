<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and prereqs
grab_request_vars();
check_prereqs();
check_authentication();

do_page();

function do_page()
{
    $default_page_title = "Nagios XI";
    $default_destination = "default";

    // do callbacks to see if components override title or redirect us elsewhere
    $cbargs = array(
        "destination" => $default_destination,
        "page_title" => $default_page_title,
        "page_url" => "",
        "redirect_url" => false,
    );
    do_callbacks(CALLBACK_HOME_PAGE_OPTIONS, $cbargs);

    // get returned values
    $destination = grab_array_var($cbargs, "destination", $default_destination);
    $page_title = grab_array_var($cbargs, "page_title", '');
    $page_url = grab_array_var($cbargs, "page_url", "");
    $redirect_url = grab_array_var($cbargs, "redirect_url", false);

    // component wants to redirect to another page
    if ($destination == "custom" && $redirect_url == true && $page_url != "") {
        header("Location: $page_url");
        exit();
    } // component wants to show home dashboard
    else if ($destination == "homedashboard") {

        // add some dashboards for the user if they don't have any
        add_default_dashboards();
        ?>
        <h1 style="margin: 10px 0 0 20px;"><?php echo $page_title; ?></h1>
        <?php

        // show the homepage dashboard
        $dashboard = get_dashboard_by_id(0, HOMEPAGE_DASHBOARD_ID);

        $s = "background: #" . encode_form_val($dashboard["opts"]["background"]) . ";";

        do_page_start(
            array(
                "body_id" => "dashboard-" . encode_form_val($dashboard["id"]),
                "body_class" => "dashboard dashboard-" . encode_form_val($dashboard["id"]),
                "body_style" => $s,
                "page_id" => "dashboards-page" . encode_form_val($dashboard["id"]),
                "page_class" => "dashboard dashboard-" . encode_form_val($dashboard["id"]),
                "page_title" => "Dashboard - " . encode_form_val($dashboard["title"]),
            ),
            true
        );

        display_dashboard_dashlets($dashboard);

        do_page_end(true);
        exit();
    } // show default home splash
    else {
        header("Location: page-default-splash.php");
        exit();
    }
}