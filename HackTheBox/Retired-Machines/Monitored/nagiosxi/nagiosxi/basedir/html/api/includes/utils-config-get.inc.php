<?php
//
// New API utils and classes for Nagios XI 5
// Copyright (c) 2019-2020 Nagios Enterprises, LLC. All rights reserved.
//

// Bootstrap CCM functionality
include_once(dirname(__FILE__) . '/../../includes/components/ccm/bootstrap.php');


function api_config_view_cfg_host($request)
{
	global $ccm;

	$host_name = grab_array_var($request, 'host_name', '');

    $sql = "SELECT host_name FROM tbl_host WHERE true";

    // Add host_name to get a limited amount
    if (!empty($host_name)) {
    	$sql .= " AND host_name = BINARY " . escape_sql_param($host_name, DB_NAGIOSQL, true);
    }

    $rs = exec_sql_query(DB_NAGIOSQL, $sql);
    $objs = $rs->GetArray();

    // Loop over data and return array
    $hosts = array();
    foreach ($objs as $h) {
        $tmp = $ccm->config->createConfigSingle('tbl_host', $h['host_name'], 2);
        $hosts = array_merge($hosts, $tmp);
    }

    return $hosts;
}


function api_config_view_cfg_service($request)
{
	global $ccm;

	$config_name = grab_array_var($request, 'config_name', '');
    $service_description = grab_array_var($request, 'service_description', '');

    $sql = "SELECT config_name FROM tbl_service WHERE true";

    // Set config_name to get a limited amount
    if (!empty($config_name)) {
    	$sql .= " AND config_name = BINARY " . escape_sql_param($config_name, DB_NAGIOSQL, true);
    }

    // Add group by
    $sql .= " GROUP BY config_name";

    $rs = exec_sql_query(DB_NAGIOSQL, $sql);
    $objs = $rs->GetArray();

    $services = array();
    foreach ($objs as $s) {
    	$tmp = $ccm->config->createConfigSingle('tbl_service', $s['config_name'], 2, $service_description);
    	$services = array_merge($services, $tmp);
    }

    return $services;
}


function api_config_view_cfg_hostgroup($request)
{
	global $ccm;
	$hostgroup_name = grab_array_var($request, 'hostgroup_name', '');
	$hostgroups = $ccm->config->createConfig('tbl_hostgroup', 2, $hostgroup_name);
	return $hostgroups;
}


function api_config_view_cfg_servicegroup($request)
{
	global $ccm;
	$servicegroup_name = grab_array_var($request, 'servicegroup_name', '');
	$servicegroups = $ccm->config->createConfig('tbl_servicegroup', 2, $servicegroup_name);
	return $servicegroups;
}


function api_config_view_cfg_contact($request)
{
    global $ccm;
    $contact_name = grab_array_var($request, 'contact_name', '');
    $contacts = $ccm->config->createConfig('tbl_contact', 2, $contact_name);
    return $contacts;
}


function api_config_view_cfg_contactgroup($request)
{
    global $ccm;
    $contactgroup_name = grab_array_var($request, 'contactgroup_name', '');
    $contactgroups = $ccm->config->createConfig('tbl_contactgroup', 2, $contactgroup_name);
    return $contactgroups;
}


function api_config_view_cfg_command($request)
{
    global $ccm;
    $command_name = grab_array_var($request, 'command_name', '');
    $commands = $ccm->config->createConfig('tbl_command', 2, $command_name);
    return $commands;
}


function api_config_view_cfg_timeperiod($request)
{
    global $ccm;
    $timeperiod_name = grab_array_var($request, 'timeperiod_name', '');
    $timeperiods = $ccm->config->createConfig('tbl_timeperiod', 2, $timeperiod_name);
    return $timeperiods;
}

