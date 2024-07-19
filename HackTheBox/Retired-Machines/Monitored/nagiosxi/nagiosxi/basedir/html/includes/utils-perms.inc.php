<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// PERMISSIONS FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param int $userid
 *
 * @return bool
 */
function is_authorized_to_configure_objects($userid = 0)
{
    if ($userid == 0 && isset($_SESSION["user_id"])) {
        $userid = $_SESSION["user_id"];
    }

    // Admins can do everything
    if (is_admin($userid)) {
        return true;
    }

    // Block users who are not authorized to configure objects
    $authcfgobjects = get_user_meta($userid, "authorized_to_configure_objects");
    if ($authcfgobjects == 1) {
        return true;
    }
    
    return false;
}


/**
 * @param int $userid
 *
 * @return bool
 */
function is_authorized_for_monitoring_system($userid = 0)
{
    if ($userid == 0 && isset($_SESSION["user_id"])) {
        $userid = $_SESSION["user_id"];
    }

    // Admins can do everything
    if (is_admin($userid)) {
        return true;
    }

    $authsys = get_user_meta($userid, "authorized_for_monitoring_system");
    if ($authsys == 1) {
        return true;
    }
    
    return false;
}


/**
 * Check if a user is authorized for all objects
 *
 * @param   int     $userid
 * @return  bool
 */
function is_authorized_for_all_objects($userid = 0)
{
    if ($userid == 0 && isset($_SESSION["user_id"])) {
        $userid = $_SESSION["user_id"];
    }

    // Admins can do everything
    if (is_admin($userid)) {
        return true;
    }

    // Some other users can see everything
    $authallobjects = get_user_meta($userid, "authorized_for_all_objects");
    if ($authallobjects == 1) {
        return true;
    }

    return false;
}


/**
 * @param int $userid
 *
 * @return bool
 */
function is_authorized_for_all_object_commands($userid = 0)
{
    if ($userid == 0 && isset($_SESSION['user_id'])) {
        $userid = $_SESSION["user_id"];
    }

    // Admins can do everything
    if (is_admin($userid)) {
        return true;
    }

    // Some other users can control everything
    $authallobjects = get_user_meta($userid, "authorized_for_all_object_commands");
    if ($authallobjects == 1) {
        return true;
    }

    return false;
}

