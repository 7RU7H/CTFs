<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/includes/common.inc.php');


// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars(false);
check_prereqs();
check_authentication(false);

route_request();


function route_request()
{
    global $request;

    // Make sure we have some query specified
    if (!isset($request['term'])) {
        exit();
    }

    $query = $request['term'];
    $cmd = "";

    // Hostname might be passed with service queries
    $hostname = grab_request_var("host", "");

    if (isset($request['type'])) {
        $cmd = strtolower($request['type']);
    }

    switch ($cmd) {

        case "users":
            suggest_users($query);
            break;

        case "services":
            suggest_services($query, $hostname);
            break;

        case "hostgroups":
            suggest_hostgroups($query);
            break;

        case "servicegroups":
            suggest_servicegroups($query);
            break;

        case "objects":
            suggest_objects($query);
            break;

        default:
        case "hosts":
            suggest_hosts($query);
            break;

        case "multi":
            suggest_multi($query);
            break;

    }

    exit();
}


/**
 * Get suggested users from a query and output JSON
 *
 * @param   string  $query  Query string
 */
function suggest_users($query)
{
    $names = array();

    // Get usernames, names, and emails
    $searchstring = "lks:" . $query;
    $res1 = get_users(array("username" => $searchstring));
    $res2 = get_users(array("name" => $searchstring));
    $res3 = get_users(array("email" => $searchstring));

    // Load the results into xml
    if (!empty($res1)) {
        foreach ($res1 as $u) {
            $names[] = strtolower($u['username']);
        }
    }

    if (!empty($res2)) {
        foreach ($res2 as $u) {
            $names[] = strtolower($u['name']);
        }
    }

    if (!empty($res3)) {
        foreach ($res3 as $u) {
            $names[] = strtolower($u['email']);
        }
    }

    natcasesort($names);
    $names = array_flip(array_flip($names));

    echo json_encode($names);
}


/**
 * Suggest hosts from query and output JSON
 *
 * @param   string  $query  Query string
 */
function suggest_hosts($query)
{
    $names = array();

    // Search on host name
    $args = array(
        "host_name" => "lk:" . $query . ";alias=lk:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        'orderby' => 'host_name:a',
        'records' => 10,
    );

    $res1 = get_host_objects_xml_output($args);
    $xres1 = simplexml_load_string($res1);

    if ($xres1) {
        foreach ($xres1->host as $obj) {
            $names[] = (object) array('url' => get_base_url().'/includes/components/xicore/status.php?show=services&host='.urlencode(strval($obj->host_name)), 
                                      'value' => strval($obj->host_name),
                                      'category' => _('Host'),
                                      'label' => (stripos(strval($obj->host_name),$query) !== false) ? strval($obj->host_name) : _('[A] ') . strval($obj->alias));
        }
    }

    echo json_encode($names);
}


/**
 * Get suggested hostgroups from qeury string and output JSON
 *
 * @param   string  $query  Query string
 */
function suggest_hostgroups($query)
{
    $names = array();

    // Search on hostgroup name
    $args = array(
        "hostgroup_name" => "lks:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        'orderby' => 'hostgroup_name:a',
        'records' => 10,
    );

    $res1 = get_hostgroup_objects_xml_output($args);
    $xres1 = simplexml_load_string($res1);

    if ($xres1) {
        foreach ($xres1->hostgroup as $obj) {
            $names[] = (object) array('url' => get_base_url().'/includes/components/xicore/status.php?show=services&hostgroup='.urlencode(strval($obj->hostgroup_name)), 
                                      'value' => strval($obj->hostgroup_name),
                                      'category' => _('Hostgroup'),
                                      'label' => (stripos(strval($obj->hostgroup_name),$query) !== false) ? strval($obj->hostgroup_name) : _('[A] '). strval($obj->alias));
        }
    }

    echo json_encode($names);
}


/**
 * Get suggested services based on query and output as JSON
 *
 * @param   string  $query      Query string
 * @param   string  $hostname   Host name
 */
function suggest_services($query, $hostname="")
{
    $names = array();

    // Search on service name
    $args = array(
        "service_description" => "lks:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        'orderby' => 'service_description:a',
        'records' => 10,
    );

    if (!empty($hostname)) {
        $args["host_name"] = $hostname;
    }

    $res1 = get_service_objects_xml_output($args);
    $xres1 = simplexml_load_string($res1);

    if ($xres1) {
        foreach ($xres1->service as $obj) {
            $names[] = (object) array('url' => get_base_url().'/includes/components/xicore/status.php?show=services&search='.urlencode(strval($obj->service_description)), 
                                      'value' => strval($obj->service_description),
                                      'category' => _('Service'),
                                      'label' => strval($obj->service_description));
        }
    }

    echo json_encode($names);
}


/**
 * Get suggested servicegroups from query and output JSON
 *
 * @param   string  $query  Query string
 */
function suggest_servicegroups($query)
{
    $names = array();

    // Search on servicegroup name
    $args = array(
        "servicegroup_name" => "lks:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        'orderby' => 'servicegroup_name:a',
    );

    $res1 = get_servicegroup_objects_xml_output($args);
    $xres1 = simplexml_load_string($res1);

    if ($xres1) {
        foreach ($xres1->servicegroup as $obj) {
            $names[] = (object) array('url' => get_base_url().'/includes/components/xicore/status.php?show=services&servicegroup='.urlencode(strval($obj->servicegroup_name)), 
                                      'value' => strval($obj->servicegroup_name),
                                      'category' => _('Servicegroup'),
                                      'label' => (stripos(strval($obj->servicegroup_name),$query) !== false) ? strval($obj->servicegroup_name) : _('[A] '). strval($obj->alias));
        }
    }

    echo json_encode($names);
}


/**
 * Get suggested objects based on name and description and output JSON
 *
 * @param   string  $query  Query string
 */
function suggest_objects($query)
{
    $names = array();

    // Get name1 (name)
    $args = array(
        "name1" => "lks:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        "records" => 10,
    );
    $res1 = get_objects_xml_output($args);

    // Get name2 (description)
    $args = array(
        "name2" => "lks:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        "records" => 10,
    );
    $res2 = get_objects_xml_output($args);

    $xres1 = simplexml_load_string($res1);
    $xres2 = simplexml_load_string($res2);

    if ($xres1) {
        foreach ($xres1->object as $obj) {
            $names[] = strval($obj->name1);
        }
    }

    if ($xres2) {
        foreach ($xres2->object as $obj) {
            $names[] = strval($obj->name2);
        }
    }

    natcasesort($names);
    $names = array_flip(array_flip($names));

    echo json_encode($names);
}


/**
 * Get suggested hosts, services, hostgroups, and service groups
 * and output JSON
 *
 * @param   string  $query  Query string
 */
function suggest_multi($query)
{
    $names = array();

    // Services
    $args = array(
        "service_description" => "lk:" . $query . ";alias=lk:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        'orderby' => 'service_description:a',
    );

    $res1 = get_service_objects_xml_output($args);
    $xres1 = simplexml_load_string($res1);

    debug($xres1);

    if ($xres1) {
        $services_count = 0;
        $existing_services = array();
        foreach ($xres1->service as $obj) {
            if (!in_array(strval($obj->service_description), $existing_services)) {
                $names[] = (object) array('url' => get_base_url().'/includes/components/xicore/status.php?show=services&search='.urlencode(strval($obj->service_description)),
                                          'value' => strval($obj->service_description),
                                          'category' => _("Service"),
                                          'label' => (stripos(strval($obj->service_description),$query) !== false) ? strval($obj->service_description) : _('[A] ') . strval($obj->display_name));
                $existing_services[] = strval($obj->service_description);
                if ($services_count++ == 10) {
                    break;
                }
            }
        }
    }

    // Hosts
    $args = array(
        "host_name" => "lk:" . $query . ";alias=lk:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        'orderby' => 'host_name:a',
        'records' => 10,
    );

    $res1 = get_host_objects_xml_output($args);
    $xres1 = simplexml_load_string($res1);

    if ($xres1) {
        foreach ($xres1->host as $obj) {
           $names[] = (object) array('url' => get_base_url().'/includes/components/xicore/status.php?show=hosts&host='.urlencode(strval($obj->host_name)), 
                                     'value' => strval($obj->host_name),
                                     'category' => _('Host'),
                                     'label' => (stripos(strval($obj->host_name),$query) !== false) ? strval($obj->host_name) : _('[A] ') . strval($obj->alias));
        }
    }

    // Hostgroups
    $args = array(
        "hostgroup_name" => "lk:" . $query . ";alias=lk:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        'orderby' => 'hostgroup_name:a',
        'records' => 10,
    );

    $res1 = get_hostgroup_objects_xml_output($args);
    $xres1 = simplexml_load_string($res1);

    if ($xres1) {
        foreach ($xres1->hostgroup as $obj) {
            $names[] = (object) array('url' => get_base_url().'/includes/components/xicore/status.php?show=hostgroups&hostgroup='.urlencode(strval($obj->hostgroup_name)), 
                                      'value' => strval($obj->hostgroup_name),
                                      'category' => _('Hostgroup'),
                                      'label' => (stripos(strval($obj->hostgroup_name),$query) !== false) ? strval($obj->hostgroup_name) : _('[A] '). strval($obj->alias));
        }
    }

    // Servicegroups
    $args = array(
        "servicegroup_name" => "lk:" . $query . ";alias=lk:" . $query,
        "brevity" => 1,
        "is_active" => 1,
        'orderby' => 'servicegroup_name:a',
    );

    $res1 = get_servicegroup_objects_xml_output($args);
    $xres1 = simplexml_load_string($res1);

    if ($xres1) {
        foreach ($xres1->servicegroup as $obj) {
            $names[] = (object) array('url' => get_base_url().'/includes/components/xicore/status.php?show=servicegroups&servicegroup='.urlencode(strval($obj->servicegroup_name)), 
                                      'value' => strval($obj->servicegroup_name),
                                      'category' => _('Servicegroup'),
                                      'label' => (stripos(strval($obj->servicegroup_name),$query) !== false) ? strval($obj->servicegroup_name) : _('[A] '). strval($obj->alias));
        }
    }

    echo json_encode(array_slice($names, 0, 10));
}
