<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');


// GENERIC OBJECTS *************************************************************************
function fetch_objects()
{
    global $request;
    $output = get_objects_xml_output($request);
    print backend_output($output);
}


// HOSTS *************************************************************************
function fetch_hosts()
{
    global $request;
    $output = get_host_objects_xml_output($request);
    print backend_output($output);
}


// PARENT HOSTS *************************************************************************
function fetch_parenthosts()
{
    global $request;
    $output = get_host_parents_xml_output($request);
    print backend_output($output);
}


// SERVICES *************************************************************************
function fetch_services()
{
    global $request;
    $output = get_service_objects_xml_output($request);
    print backend_output($output);
}


// CONTACTS *************************************************************************
function fetch_contacts()
{
    global $request;
    $output = get_contact_objects_xml_output($request);
    print backend_output($output);
}


// HOSTGROUPS *************************************************************************
function fetch_hostgroups()
{
    global $request;
    $output = get_hostgroup_objects_xml_output($request);
    print backend_output($output);
}


// HOSTGROUP MEMBERS **********************************************************************
function fetch_hostgroupmembers()
{
    global $request;
    $output = get_hostgroup_member_objects_xml_output($request);
    print backend_output($output);
}


// SERVICEGROUPS *************************************************************************
function fetch_servicegroups()
{
    global $request;
    $output = get_servicegroup_objects_xml_output($request);
    print backend_output($output);
}


// SERVICEGROUP MEMBERS **********************************************************************
function fetch_servicegroupmembers()
{
    global $request;
    $output = get_servicegroup_member_objects_xml_output($request);
    print backend_output($output);
}

// SERVICEGROUP HOST MEMBERS **********************************************************************
function fetch_servicegrouphostmembers()
{
    global $request;
    $output = get_servicegroup_host_member_objects_xml_output($request);
    print backend_output($output);
}


// CONTACTGROUPS *************************************************************************
function fetch_contactgroups()
{
    global $request;
    $output = get_contactgroup_objects_xml_output($request);
    print backend_output($output);
}


// CONTACTGROUP MEMBERS **********************************************************************
function fetch_contactgroupmembers()
{
    global $request;
    $output = get_contactgroup_member_objects_xml_output($request);
    print backend_output($output);
}
