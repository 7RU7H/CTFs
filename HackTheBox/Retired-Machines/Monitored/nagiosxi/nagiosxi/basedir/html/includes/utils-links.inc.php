<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////
// STATUS LINK FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Link to the host status screen
 *
 * @param   string  $hostname
 * @param   string  $dest
 * @return  string
 */
function get_host_status_link($hostname, $dest = "auto")
{
    $url = get_base_url() . "includes/components/xicore/status.php?host=" . urlencode($hostname);
    if ($dest == "core") {
        $url = get_base_url() . "includes/components/nagioscore/ui/status.php?host=" . urlencode($hostname) . "&usecoreui=1";
    }
    return $url;
}


/**
 * Link to service status screen
 *
 * @param   string  $hostname
 * @param   string  $servicename
 * @param   string  $dest
 * @return  string
 */
function get_service_status_link($hostname, $servicename, $dest = "auto")
{
    $url = get_service_status_detail_link($hostname, $servicename, $dest);
    return $url;
}


/**
 * Create a URL to the host detials page
 *
 * @param   string  $hostname
 * @param   string  $dest
 * @return  string
 */
function get_host_status_detail_link($hostname, $dest = "auto", $full = true)
{
    $url = "includes/components/xicore/status.php?show=hostdetail&host=" . urlencode($hostname);

    if ($dest == "core") {
        $url = "includes/components/nagioscore/ui/extinfo.php?type=1&host=" . urlencode($hostname) . "&usecoreui=1";
    }

    if ($full) {
        $url = get_base_url() . $url;
    }

    return $url;
}


/**
 * Create a URL to the service details page
 *
 * @param   string  $hostname
 * @param   string  $servicename
 * @param   string  $dest
 * @param   bool    $full
 * @return  string
 */
function get_service_status_detail_link($hostname, $servicename, $dest = "auto", $full = true)
{
    $url = "includes/components/xicore/status.php?show=servicedetail&host=" . urlencode($hostname) . "&service=" . urlencode($servicename) . "&dest=" . urlencode($dest);

    if ($dest == "core") {
        $url = "includes/components/nagioscore/ui/extinfo.php?type=2&host=" . urlencode($hostname) . "&service=" . urlencode($servicename) . "&usecoreui=1";
    }

    if ($full) {
        $url = get_base_url() . $url;
    }

    return $url;
}


/**
 * Link to hostgroup status details screen
 *
 * @param   string  $hostgroupname
 * @param   string  $style
 * @param   string  $dest
 * @return  string
 */
function get_hostgroup_status_link($hostgroupname, $style = "overview", $dest = "auto")
{
    $url = get_base_url() . "includes/components/xicore/status.php?show=hostgroups&hostgroup=" . urlencode($hostgroupname) . "&style=" . urlencode($style);
    if ($dest == "core") {
        $url = get_base_url() . "includes/components/nagioscore/ui/status.php&hostgroup=" . urlencode($hostgroupname) . "&style=" . urlencode($style);
    }
    return $url;
}


/**
 * Link to servicegroup status details screen
 *
 * @param   string  $servicegroupname
 * @param   string  $style
 * @param   string  $dest
 * @return  string
 */
function get_servicegroup_status_link($servicegroupname, $style = "overview", $dest = "auto")
{
    $url = get_base_url() . "includes/components/xicore/status.php?show=servicegroups&servicegroup=" . urlencode($servicegroupname) . "&style=" . urlencode($style);
    if ($dest == "core") {
        $url = get_base_url() . "includes/components/nagioscore/ui/status.php&servicegroup=" . urlencode($servicegroupname) . "&style=" . urlencode($style);
    }
    return $url;
}


/**
 * Link to outages status screen
 *
 * @param   string  $dest
 * @return  string
 */
function get_network_outages_link($dest = "auto")
{
    $url = get_base_url() . "includes/components/xicore/status.php?show=outages";
    if ($dest == "core") {
        $url = get_base_url() . "includes/components/nagioscore/ui/outages.php";
    }
    return $url;
}


/**
 * Link to status map
 *
 * @param   int     $layout
 * @param   string  $dest
 * @return  string
 */
function get_statusmap_link($layout = 6, $dest = "auto")
{
    $url = get_base_url() . "includes/components/xicore/status.php?show=map&layout=" . $layout;
    if ($dest == "core") {
        $url = get_base_url() . "includes/components/nagioscore/ui/statusmap.php?layout=" . $layout;
    }
    return $url;
}


////////////////////////////////////////////////////////////////////////
// NOTIFICATION LINK FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * Link to host notification screen
 *
 * @param   string  $hostname
 * @return  string
 */
function get_host_notifications_link($hostname)
{
    $url = get_base_url() . "reports/notifications.php?host=" . urlencode($hostname);
    return $url;
}


/**
 * Link to service status screen
 *
 * @param   string  $hostname
 * @param   string  $servicename
 * @return  string
 */
function get_service_notifications_link($hostname, $servicename)
{
    $url = get_base_url() . "reports/notifications.php?host=" . urlencode($hostname) . "&service=" . urlencode($servicename);
    return $url;
}


////////////////////////////////////////////////////////////////////////
// HISTORY LINK FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param   string  $hostname
 * @return  string
 */
function get_host_history_link($hostname)
{
    $url = get_base_url() . "reports/statehistory.php?host=" . urlencode($hostname);
    return $url;
}


/**
 * @param   string  $hostname
 * @param   string  $servicename
 * @return  string
 */
function get_service_history_link($hostname, $servicename)
{
    $url = get_base_url() . "reports/statehistory.php?host=" . urlencode($hostname) . "&service=" . urlencode($servicename);
    return $url;
}


////////////////////////////////////////////////////////////////////////
// TRENDS LINK FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param   string  $hostname
 * @return  string
 */
function get_host_trends_link($hostname)
{
    $url = get_base_url() . "reports/trends.php?host=" . urlencode($hostname);
    return $url;
}


/**
 * @param   string  $hostname
 * @param   string  $servicename
 * @return  string
 */
function get_service_trends_link($hostname, $servicename)
{
    $url = get_base_url() . "reports/trends.php?host=" . urlencode($hostname) . "&service=" . urlencode($servicename);
    return $url;
}


////////////////////////////////////////////////////////////////////////
// AVAILABILITY LINK FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param   string  $hostname
 * @return  string
 */
function get_host_availability_link($hostname)
{
    $url = get_base_url() . "reports/availability.php?host=" . urlencode($hostname);
    return $url;
}


/**
 * @param   string  $hostname
 * @param   string  $servicename
 * @return  string
 */
function get_service_availability_link($hostname, $servicename)
{
    $url = get_base_url() . "reports/availability.php?host=" . urlencode($hostname) . "&service=" . urlencode($servicename);
    return $url;
}


////////////////////////////////////////////////////////////////////////
// HISTOGRAM LINK FUNCTIONS
////////////////////////////////////////////////////////////////////////


/**
 * @param   string  $hostname
 * @return  string
 */
function get_host_histogram_link($hostname)
{
    $url = get_base_url() . "reports/histogram.php?host=" . urlencode($hostname);
    return $url;
}


/**
 * @param   string  $hostname
 * @param   string  $servicename
 * @return  string
 */
function get_service_histogram_link($hostname, $servicename)
{
    $url = get_base_url() . "reports/histogram.php?host=" . urlencode($hostname) . "&service=" . urlencode($servicename);
    return $url;
}
