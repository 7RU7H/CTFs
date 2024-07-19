<?php
//
// Notification Method Extensions
// Allows creating additional components that can notify
//
// Copyright (c) 2010-2020 Nagios Enterprises, LLC. All rights reserved.
//


$notificationmethods = array();


////////////////////////////////////////////////////////////////////////
// NOTIFICATION METHOD  FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Register a new notification method globally
 *
 * @param   string  $name   Name of notification method
 * @param   array   $args   Arguments to pass to notification method
 * @return  bool            True if added, false if error
 */
function register_notificationmethod($name = "", $args = null)
{
    global $notificationmethods;

    if (empty($name)) {
        return false;
    }

    if (!isset($notificationmethods)) {
        $notificationmethods = array();
    }

    $notificationmethods[$name] = $args;

    return true;
}


/**
 * Get a notification method based on the method name
 *
 * @param   string  $name   Name of notification method
 * @return  array           Notification method arguments
 */
function get_notificationmethod_by_name($name)
{
    global $notificationmethods;
    $notificationmethod = null;

    if (array_key_exists($name, $notificationmethods)) {
        $notificationmethod = $notificationmethods[$name];
    }

    return $notificationmethod;
}


/**
 * Run the notification method callbacks
 *
 * @param   string  $name       Name of notification method
 * @param   string  $mode       Callback mode
 * @param   array   $inargs     Arguments passed to callback
 * @param   array   $outargs    Arguments returned from callback
 * @param   mixed   $result     Result returned from callback
 * @return  mixed               Returned output from callback
 */
function make_notificationmethod_callback($name = "", $mode = "", $inargs, &$outargs, &$result)
{
    $w = get_notificationmethod_by_name($name);
    if (empty($w)) {
        return "";
    }

    $output = "";

    // Run the function and return output (or error)
    if (array_key_exists(NOTIFICATIONMETHOD_FUNCTION, $w) && have_value($w[NOTIFICATIONMETHOD_FUNCTION]) == true) {
        $f = $w[NOTIFICATIONMETHOD_FUNCTION];
        if (function_exists($f)) {
            $output = $f($mode, $inargs, $outargs, $result);
        } else {
            $output = "NOTIFICATION METHOD FUNCTION '" . $f . "' DOES NOT EXIST";
        }
    }

    return $output;
}

