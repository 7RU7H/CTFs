<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// THEME FUNCTIONS
////////////////////////////////////////////////////////////////////////


// Gets the current theme based on what the user or application default
// is currently set to
function get_theme()
{
    $theme = get_option('theme', 'xi5');
    $user_theme = get_user_meta(0, 'theme');

    if (!empty($user_theme)) {
        if ($user_theme != "NULL") {
            $theme = $user_theme;
        }
    }

    return $theme;
}


function is_dark_theme()
{
    if (get_theme() == 'xi5dark') {

        // Check for mode types
        $mode = grab_request_var("mode", "");
        if ($mode == "pdf" || $mode == "getreport") {
            return false;
        }

        // Check if it's a page pdf generation
        $pdfrender = grab_request_var("pdfrender", 0);
        if ($pdfrender) {
            return false;
        }

        // Check for exporting
        $export = grab_request_var("export", 0);
        if ($export) {
            return false;
        }

        return true;
    }
    return false;
}


function theme_image($img = "")
{
    $default_directory = get_base_url() . "images/";
    $theme_directory = $default_directory . get_theme() . '/';
    if (file_exists(get_root_dir() . '/html/images/' . get_theme() . '/' . $img)) {
        return $theme_directory . $img;
    }
    return $default_directory . $img;
}


function wizard_logo($img = "")
{
    $default_directory = get_base_url() . '/includes/components/nagioscore/ui/images/logos/';
    $theme_directory = $default_directory . get_theme() . '/';
    if (file_exists(get_root_dir() . '/includes/components/nagioscore/ui/images/logos/' . get_theme() . '/' . $img)) {
        return $theme_directory . $img;
    }
    return $default_directory . $img;
}
