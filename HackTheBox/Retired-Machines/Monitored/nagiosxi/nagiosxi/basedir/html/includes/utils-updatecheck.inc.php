<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// UPDATE  FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param bool $forced
 * @param bool $firstcheck
 * @return bool
 */
function do_update_check($forced=false, $firstcheck=false, $bootcheck=false, $upgrade=false)
{
    global $cfg;

    $currentDate = date('Y-m-d');
    $updateFailureCount = get_option('update_failure_count');
    $updateFailureCount = json_decode($updateFailureCount);

    if (!$forced && $updateFailureCount && isset($updateFailureCount->$currentDate) && $updateFailureCount->$currentDate >= 5) {
        return false;
    }


    $now = time();

    if ($forced == false) {

        // we're not supposed to automatically check for updates
        $auto_check_updates = get_option('auto_update_check');
        if ($auto_check_updates == false) {
            //echo "NO AUTO CHECK<BR>\n";
            return false;
        }
    }

    // force check if we've never checked
    $last_update_check_time = get_option("last_update_check_time");
    if ($last_update_check_time == "")
        $forced = true;

    // force check if last check didn't succeed
    $last_success = get_option("last_update_check_succeeded");
    if ($last_success == 0)
        $forced = true;

    // if not forced, see if we should check for updates yet
    if ($forced == false) {

        // we haven't waited long enough
        $last_check = get_option("last_update_check_time");
        if ($last_check) {
            // at least 24 hours should have passed since last auto check
            $timediff = $now - $last_check;
            if ($timediff < (60 * 60 * 24)) {
                //echo "TOO SOON<BR>\n";
                return false;
            }
        }
    }

    // save last check time
    set_option("last_update_check_time", $now);

    // build url
    $theurl = "https://api.nagios.com/versioncheck/";

    // options to send
    $theurl .= "?product=" . get_product_name(true, true) . "&version=" . get_product_version() . "&build=" . get_product_build() . "&stableonly=1&output=xml";
    if ($firstcheck == true)
        $theurl .= "&firstcheck=1";
    if ($bootcheck == true)
        $theurl .= "&bootcheck=1";
    if ($upgrade == true)
        $theurl .= "&upgradecheck=1";

    // If it's licensed, add the hash
    if (is_trial_license()) {
        $theurl .= '&k5=T';
    } else if (is_free_license()) {
        $theurl .= "&k5=F";
    } else {
        $k = get_license_key();
        if (!empty($k)) {
            $theurl .= '&k5=' . md5($k);
        }
    }

    // Was a quickstart performed?
    $qs = get_option("quickstart_id");
    if (!empty($qs)) {
        $theurl .= "&qs=".intval($qs);
    }

    // Get info on host/services amounts
    $h = get_active_host_license_count();
    $s = get_active_service_license_count();
    $theurl .= "&h=" . intval($h) . "&s=" . intval($s);

    // Add OS type to check
    $ostype = get_os_type();
    $theurl .= "&ostype=" . urlencode($ostype);

    // Add special installed type/method to the check update request
    $install_type = get_product_install_type();
    $theurl .= "&installtype=" . urlencode($install_type);

    // Send the UUID if it exists
    $uuid = get_product_uuid();
    $theurl .= "&uuid=" . urlencode($uuid);

    // Add architecture of server (32b or 64b)
    $arch = '64';
    if (2147483647 == PHP_INT_MAX) {
        $arch = '32';
    }
    $theurl .= '&arch='.$arch;

    $options = array(
        'return_info' => true,
        'method' => 'post',
        'timeout' => 15
    );

    $proxy = false;

    if (have_value(get_option('use_proxy')))
        $proxy = true;

    // fetch the url
    $result = load_url($theurl, $options, $proxy);
    $body = $result["body"];

    $xres = simplexml_load_string($body);
    // an error occurred
    if ($xres == false) {
        set_option("last_update_check_succeeded", 0);

        if ($updateFailureCount && isset($updateFailureCount->$currentDate)) {
            $updateFailureCount->$currentDate++;
        } else {
            $updateFailureCount = [$currentDate => 1];
        }

        set_option("update_failure_count", json_encode($updateFailureCount));

        return false;
    }

    $update_available = $xres->update_available;
    $update_version = $xres->update_version->version;
    $release_date = $xres->update_version->release_date;
    $release_notes = $xres->update_version->release_notes;

    // Save this information
    set_option("update_available", $update_available);
    set_option("update_version", $update_version);
    set_option("update_release_date", $release_date);
    set_option("update_release_notes", $release_notes);

    set_option("last_update_check_succeeded", 1);
    
    do_check_maintenance(true);

    return true;
}
