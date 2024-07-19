<?php
//
// Objects XML Utils Functions
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//


////////////////////////////////////////////////////////////////////////////////
// GENERIC OBJECTS
////////////////////////////////////////////////////////////////////////////////


/**
 * @param $request_args
 * @return string
 */
function get_objects_xml_output($request_args, $authlimit=true, $xml=true, $user_id=0)
{
    global $sqlquery;
    global $db_tables;

    $output = "";

    // Generate query
    $fieldmap = array(
        "name1" => $db_tables[DB_NDOUTILS]["objects"] . ".name1",
        "name2" => $db_tables[DB_NDOUTILS]["objects"] . ".name2",
        "instance_id" => $db_tables[DB_NDOUTILS]["objects"] . ".instance_id",
        "object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "objecttype_id" => $db_tables[DB_NDOUTILS]["objects"] . ".objecttype_id",
        "is_active" => $db_tables[DB_NDOUTILS]["objects"] . ".is_active",
    );
    $objectauthfields = array(
        "object_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );

    // Set default active only
    if (!isset($request_args['is_active'])) {
        $request_args['is_active'] = 1;
    }

    $args = array(
        "sql" => $sqlquery['GetObjects'],
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );

    // Sending user ID
    if ($authlimit && !empty($user_id)) {
        $args['uid'] = $user_id;
    }

    $sql = generate_sql_query(DB_NDOUTILS, $args, $authlimit);

    if ($rs = exec_sql_query(DB_NDOUTILS, $sql, false)) {
        if ($xml) {
            //output_backend_header();
            $output .= "<objectlist>\n";
            $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

            if (!isset($request_args["totals"])) {
                $results = $rs->GetArray();

                foreach ($results as $rs) {
                    $output .= "  <object id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
                    $output .= get_xml_db_field(2, $rs, 'instance_id');
                    $output .= get_xml_db_field(2, $rs, 'object_id');
                    $output .= get_xml_db_field(2, $rs, 'objecttype_id');
                    $output .= get_xml_db_field(2, $rs, 'is_active');
                    $output .= get_xml_db_field(2, $rs, 'name1');
                    $output .= get_xml_db_field(2, $rs, 'name2');
                    $output .= "  </object>\n";
                }
            }

            $output .= "</objectlist>\n";
        } else {
            return $rs->GetArray();
        }
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// HOSTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_host_objects_xml_output($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = "";

    $totals = grab_array_var($request_args, 'totals', 0);
    $brevity = grab_array_var($request_args, 'brevity', 0);
    $customvars = grab_array_var($request_args, 'customvars', 0);

    $hosts = get_data_host($request_args);

    $output .= "<hostlist>\n";

    if ($totals) {
        $count = empty($hosts[0]['total']) ? 0 : $hosts[0]['total'];
        $output .= "  <recordcount>" . $count . "</recordcount>\n";
    } else {
        $output .= "  <recordcount>" . count($hosts) . "</recordcount>\n";

        foreach ($hosts as $rs) {

            $output .= "  <host id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
            $output .= get_xml_db_field(2, $rs, 'instance_id');
            $output .= get_xml_db_field(2, $rs, 'host_name');
            $output .= get_xml_db_field(2, $rs, 'is_active');
            $output .= get_xml_db_field(2, $rs, 'config_type');
            $output .= get_xml_db_field(2, $rs, 'alias');
            $output .= get_xml_db_field(2, $rs, 'display_name');
            $output .= get_xml_db_field(2, $rs, 'address');

            if ($brevity < 1) {
                $output .= get_xml_db_field(2, $rs, 'check_interval');
                $output .= get_xml_db_field(2, $rs, 'retry_interval');
                $output .= get_xml_db_field(2, $rs, 'max_check_attempts');
                $output .= get_xml_db_field(2, $rs, 'first_notification_delay');
                $output .= get_xml_db_field(2, $rs, 'notification_interval');
                $output .= get_xml_db_field(2, $rs, 'passive_checks_enabled');
                $output .= get_xml_db_field(2, $rs, 'active_checks_enabled');
                $output .= get_xml_db_field(2, $rs, 'notifications_enabled');
                $output .= get_xml_db_field(2, $rs, 'notes');
                $output .= get_xml_db_field(2, $rs, 'notes_url');
                $output .= get_xml_db_field(2, $rs, 'action_url');
                $output .= get_xml_db_field(2, $rs, 'icon_image');
                $output .= get_xml_db_field(2, $rs, 'icon_image_alt');
                $output .= get_xml_db_field(2, $rs, 'statusmap_image');
            }

            // If we are adding custom vars
            if ($customvars) {
                $sql = "SELECT * FROM  " . $db_tables[DB_NDOUTILS]['customvariables'] . " WHERE object_id = " . $rs['object_id'];
                $rs2 = exec_sql_query(DB_NDOUTILS, $sql, false);
                $res = $rs2->GetArray();
                $output .= '<customvars>';
                foreach ($res as $cv) {
                    $output .= get_xml_field(3, $cv['varname'], $cv['varvalue']);
                }
                $output .= '</customvars>';
            }

            $output .= "  </host>\n";
        }

        $output .= "</hostlist>\n";
    }

    return $output;
}


function get_data_host($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', 0);
    $brevity = grab_array_var($request_args, 'brevity', 0);
    $customvars = grab_array_var($request_args, 'customvars', 0);

    // Default to only showing active objects unless overriden by request
    if (!isset($request_args["is_active"])) {
        $request_args["is_active"] = 1;
    }

    $fieldmap = array(
        "name" => $db_tables[DB_NDOUTILS]["objects"] . ".name1",
        "host_name" => $db_tables[DB_NDOUTILS]["objects"] . ".name1",
        "instance_id" => $db_tables[DB_NDOUTILS]["objects"] . ".instance_id",
        "host_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "host_object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "is_active" => $db_tables[DB_NDOUTILS]["objects"] . ".is_active",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type",
        "display_name" => $db_tables[DB_NDOUTILS]["hosts"] . ".display_name",
        "address" => $db_tables[DB_NDOUTILS]["hosts"] . ".address",
        "alias" => $db_tables[DB_NDOUTILS]["hosts"] . ".alias",
    );

    $objectauthfields = array(
        "host_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    if ($totals) {
        $q = $sqlquery['GetHostsCount'];
    } else {
        switch ($brevity) {
            case 1:
                $q = $sqlquery['GetHostsBrevity1'];
                break;
            case 0:
            default:
                $q = $sqlquery['GetHosts'];
                break;
        }
    }

    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

     if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $output = $rs->GetArray();
        if ($customvars) {
            foreach ($output as $i => $host) {
                $sql = "SELECT varname, varvalue FROM  " . $db_tables[DB_NDOUTILS]['customvariables'] . " WHERE object_id = " . $host['object_id'];
                $rs2 = exec_sql_query(DB_NDOUTILS, $sql, false);
                $res = $rs2->GetArray();
                $output[$i]['customvars'] = array();
                foreach ($res as $r) {
                    $output[$i]['customvars'][$r['varname']] = $r['varvalue'];
                }
            }
        }
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// PARENT HOSTS
////////////////////////////////////////////////////////////////////////////////


/**
 * @param $request_args
 *
 * @return string
 */
function get_host_parents_xml_output($request_args)
{

    global $sqlquery;
    global $db_tables;

    $output = "";

    // default to only showing active objects unless overriden by request
    if (!isset($request_args["is_active"]))
        $request_args["is_active"] = 1;

    // generate query
    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["host_parenthosts"] . ".instance_id",
        "host_id" => $db_tables[DB_NDOUTILS]["hosts"] . ".host_object_id",
        "host_object_id" => $db_tables[DB_NDOUTILS]["hosts"] . ".host_object_id",
        "host_name" => "obj2.name1",
        "parent_host_id" => $db_tables[DB_NDOUTILS]["host_parenthosts"] . ".parent_host_object_id",
        "parent_host_object_id" => $db_tables[DB_NDOUTILS]["host_parenthosts"] . ".parent_host_object_id",
        "parent_host_name" => "obj1.name1",
    );
    $objectauthfields = array(
        "host_id",
        "parent_host_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );
    $args = array(
        "sql" => $sqlquery['GetParentHosts'],
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args, // ADDED 1/1/10 FOR NEW NON-BACKEND CALLS
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);
    //$output.="SQL: ".$sql."<BR>\n";
    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        //output_backend_header();

        $output .= "<parenthostlist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request_args["totals"])) {
            while (!$rs->EOF) {

                $output .= "  <parenthost id='" . get_xml_db_field_val($rs, 'host_parenthost_id ') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'host_object_id', 'host_id');
                $output .= get_xml_db_field(2, $rs, 'host_name');
                $output .= get_xml_db_field(2, $rs, 'parent_host_object_id', 'parent_host_id');
                $output .= get_xml_db_field(2, $rs, 'parent_host_name');
                $output .= "  </parenthost>\n";

                $rs->MoveNext();
            }
        }

        $output .= "</parenthostlist>\n";
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// SERVICE OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_service_objects_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', 0);
    $brevity = grab_array_var($request_args, 'brevity', 0);
    $customvars = grab_array_var($request_args, 'customvars', 0);

    $services = get_data_service($request_args);

    $output .= "<servicelist>\n";

    if ($totals) {
        $count = empty($services[0]['total']) ? 0 : $services[0]['total'];
        $output .= "  <recordcount>" . $count . "</recordcount>\n";
    } else {

        $output .= "  <recordcount>" . count($services) . "</recordcount>\n";

        foreach ($services as $rs) {

            $output .= "  <service id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
            $output .= get_xml_db_field(2, $rs, 'instance_id');
            $output .= get_xml_db_field(2, $rs, 'host_name');
            $output .= get_xml_db_field(2, $rs, 'service_description');
            $output .= get_xml_db_field(2, $rs, 'is_active');
            $output .= get_xml_db_field(2, $rs, 'config_type');
            $output .= get_xml_db_field(2, $rs, 'display_name');

            if ($brevity < 1) {
                $output .= get_xml_db_field(2, $rs, 'check_interval');
                $output .= get_xml_db_field(2, $rs, 'retry_interval');
                $output .= get_xml_db_field(2, $rs, 'max_check_attempts');
                $output .= get_xml_db_field(2, $rs, 'first_notification_delay');
                $output .= get_xml_db_field(2, $rs, 'notification_interval');
                $output .= get_xml_db_field(2, $rs, 'passive_checks_enabled');
                $output .= get_xml_db_field(2, $rs, 'active_checks_enabled');
                $output .= get_xml_db_field(2, $rs, 'notifications_enabled');
                $output .= get_xml_db_field(2, $rs, 'notes');
                $output .= get_xml_db_field(2, $rs, 'notes_url');
                $output .= get_xml_db_field(2, $rs, 'action_url');
                $output .= get_xml_db_field(2, $rs, 'icon_image');
                $output .= get_xml_db_field(2, $rs, 'icon_image_alt');
            }

            // If we are adding custom vars
            if ($customvars) {
                $sql = "SELECT * FROM " . $db_tables[DB_NDOUTILS]['customvariables'] . " WHERE object_id = " . $rs['object_id'];
                $rs2 =  exec_sql_query(DB_NDOUTILS, $sql, false);
                $res = $rs2->GetArray();
                $output .= '<customvars>';
                foreach ($res as $cv) {
                    $output .= get_xml_field(3, $cv['varname'], $cv['varvalue']);
                }
                $output .= '</customvars>';
            }

            $output .= "  </service>\n";
        }
    }

    $output .= "</servicelist>\n";

    return $output;
}


function get_data_service($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', 0);
    $brevity = grab_array_var($request_args, 'brevity', 0);
    $customvars = grab_array_var($request_args, 'customvars', 0);

    // Default to only showing active objects unless overriden by request
    if (!isset($request_args["is_active"])) {
        $request_args["is_active"] = 1;
    }

    $fieldmap = array(
        "host_name" => $db_tables[DB_NDOUTILS]["objects"] . ".name1",
        "service_description" => $db_tables[DB_NDOUTILS]["objects"] . ".name2",
        "instance_id" => $db_tables[DB_NDOUTILS]["objects"] . ".instance_id",
        "service_id" => $db_tables[DB_NDOUTILS]["services"] . ".service_object_id",
        "object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "host_id" => $db_tables[DB_NDOUTILS]["services"] . ".host_object_id",
        "host_object_id" => $db_tables[DB_NDOUTILS]["services"] . ".host_object_id",
        "is_active" => $db_tables[DB_NDOUTILS]["objects"] . ".is_active",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type"
    );

    $objectauthfields = array(
        "service_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    if ($totals) {
        $q = $sqlquery['GetServicesCount'];
    } else {
        switch ($brevity) {
            case 1:
                $q = $sqlquery['GetServicesBrevity1'];
                break;
            case 0:
            default:
                $q = $sqlquery['GetServices'];
                break;
        }
    }

    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $output = $rs->GetArray();
        if ($customvars) {
            foreach ($output as $i => $service) {
                $sql = "SELECT varname, varvalue FROM  " . $db_tables[DB_NDOUTILS]['customvariables'] . " WHERE object_id = " . $service['object_id'];
                $rs2 = exec_sql_query(DB_NDOUTILS, $sql, false);
                $res = $rs2->GetArray();
                $output[$i]['customvars'] = array();
                foreach ($res as $r) {
                    $output[$i]['customvars'][$r['varname']] = $r['varvalue'];
                }
            }
        }
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// CONTACT OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_contact_objects_xml_output($request_args)
{
    global $db_tables;

    $output = "";

    $totals = grab_array_var($request_args, 'totals', false);
    $brevity = grab_array_var($request_args, 'brevity', 0);
    $customvars = grab_array_var($request_args, 'customvars', 0);

    $contacts = get_data_contact($request_args);

    if (!empty($contacts)) {

        $output .= "<contactlist>\n";

        if ($totals) {
            $contacts = $contacts[0];
            $count = empty($contacts['total']) ? 0 : $contacts['total'];
            $output .= "  <recordcount>" . $count . "</recordcount>\n";
        } else {
        
            $output .= "  <recordcount>" . count($contacts) . "</recordcount>\n";

            foreach ($contacts as $rs) {

                $output .= "  <contact id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'contact_name');
                $output .= get_xml_db_field(2, $rs, 'is_active');
                $output .= get_xml_db_field(2, $rs, 'config_type');
                $output .= get_xml_db_field(2, $rs, 'alias');
                $output .= get_xml_db_field(2, $rs, 'email_address');
                if ($brevity < 1) {
                    $output .= get_xml_db_field(2, $rs, 'pager_address');
                    $output .= get_xml_db_field(2, $rs, 'host_timeperiod_object_id', 'host_timeperiod_id');
                    $output .= get_xml_db_field(2, $rs, 'service_timeperiod_object_id', 'service_timeperiod_id');
                    $output .= get_xml_db_field(2, $rs, 'host_notifications_enabled');
                    $output .= get_xml_db_field(2, $rs, 'service_notifications_enabled');
                    $output .= get_xml_db_field(2, $rs, 'can_submit_commands');
                    $output .= get_xml_db_field(2, $rs, 'notify_service_recovery');
                    $output .= get_xml_db_field(2, $rs, 'notify_service_warning');
                    $output .= get_xml_db_field(2, $rs, 'notify_service_unknown');
                    $output .= get_xml_db_field(2, $rs, 'notify_service_critical');
                    $output .= get_xml_db_field(2, $rs, 'notify_service_flapping');
                    $output .= get_xml_db_field(2, $rs, 'notify_service_downtime');
                    $output .= get_xml_db_field(2, $rs, 'notify_host_recovery');
                    $output .= get_xml_db_field(2, $rs, 'notify_host_down');
                    $output .= get_xml_db_field(2, $rs, 'notify_host_unreachable');
                    $output .= get_xml_db_field(2, $rs, 'notify_host_flapping');
                    $output .= get_xml_db_field(2, $rs, 'notify_host_downtime');
                }
                // If we are adding custom vars
                if ($customvars) {
                    $sql = "SELECT * FROM  " . $db_tables[DB_NDOUTILS]['customvariables'] . " WHERE object_id = " . $rs['object_id'];
                    $rs2 = exec_sql_query(DB_NDOUTILS, $sql, false);
                    $res = $rs2->GetArray();
                    $output .= '<customvars>';
                    foreach ($res as $cv) {
                        $output .= get_xml_field(3, $cv['varname'], $cv['varvalue']);
                    }
                    $output .= '</customvars>';
                }
                $output .= "  </contact>\n";

            }
        }

        $output .= "</contactlist>\n";
    }

    return $output;
}


function get_data_contact($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', false);
    $brevity = grab_array_var($request_args, 'brevity', 0);
    $customvars = grab_array_var($request_args, 'customvars', 0);

    // Default to only showing active objects unless overriden by request
    if (!isset($request_args["is_active"])) {
        $request_args["is_active"] = 1;
    }

    $fieldmap = array(
        "contact_name" => $db_tables[DB_NDOUTILS]["objects"] . ".name1",
        "instance_id" => $db_tables[DB_NDOUTILS]["objects"] . ".instance_id",
        "contact_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "contact_object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "is_active" => $db_tables[DB_NDOUTILS]["objects"] . ".is_active",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type",
        "alias" => $db_tables[DB_NDOUTILS]["contacts"] . ".alias"
    );

    $objectauthfields = array(
        "contact_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    if ($totals) {
        $q = $sqlquery['GetContactsCount'];
    } else {
        switch ($brevity) {
            case 1:
                $q = $sqlquery['GetContactsBrevity1'];
                break;
            default:
            case 0:
                $q = $sqlquery['GetContacts'];
                break;
        }
    }

    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $output = $rs->GetArray();
        if ($customvars) {
            foreach ($output as $i => $contact) {
                $sql = "SELECT varname, varvalue FROM  " . $db_tables[DB_NDOUTILS]['customvariables'] . " WHERE object_id = " . $contact['object_id'];
                $rs2 = exec_sql_query(DB_NDOUTILS, $sql, false);
                $res = $rs2->GetArray();
                $output[$i]['customvars'] = array();
                foreach ($res as $r) {
                    $output[$i]['customvars'][$r['varname']] = $r['varvalue'];
                }
            }
        }
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// HOSTGROUP OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_hostgroup_objects_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', false);

    $hostgroups = get_data_hostgroup($request_args);

    if (!empty($hostgroups)) {

        $output .= "<hostgrouplist>\n";

        if ($totals) {
            $hostgroups = $hostgroups[0];
            $count = empty($hostgroups['total']) ? 0 : $hostgroups['total'];
            $output .= "  <recordcount>" . $count . "</recordcount>\n";
        } else {

            $output .= "  <recordcount>" . count($hostgroups) . "</recordcount>\n";

            foreach ($hostgroups as $rs) {

                $output .= "  <hostgroup id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'hostgroup_name');
                $output .= get_xml_db_field(2, $rs, 'is_active');
                $output .= get_xml_db_field(2, $rs, 'config_type');
                $output .= get_xml_db_field(2, $rs, 'alias');
                $output .= "  </hostgroup>\n";

            }
        }

        $output .= "</hostgrouplist>\n";
    } else {
        $output = "<hostgrouplist>\n
   <recordcount>0</recordcount>\n
</hostgrouplist>\n";
    }

    return $output;
}


function get_data_hostgroup($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', false);

    // Default to only showing active objects unless overriden by request
    if (!isset($request_args["is_active"])) {
        $request_args["is_active"] = 1;
    }

    // Generate fieldmap
    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["objects"] . ".instance_id",
        "hostgroup_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "hostgroup_object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "hostgroup_name" => $db_tables[DB_NDOUTILS]["objects"] . ".name1",
        "is_active" => $db_tables[DB_NDOUTILS]["objects"] . ".is_active",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type",
        "alias" => $db_tables[DB_NDOUTILS]["hostgroups"] . ".alias"
    );

    $objectauthfields = array(
        "hostgroup_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    $q = $sqlquery['GetHostGroups'];
    if ($totals) {
        $q = $sqlquery['GetHostGroupsCount'];
    }

    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $output = $rs->GetArray();
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// HOSTGROUP HOST MEMBER OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_hostgroup_member_objects_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', 0);

    $members = get_data_hostgroup_members($request_args, true);

    $output .= "<hostgrouplist>\n";

    if ($totals) {
        $count = empty($members[0]['total']) ? 0 : $members[0]['total'];
        $output .= "  <recordcount>" . $count . "</recordcount>\n";
    } else {
        
        $output .= "  <recordcount>" . count($members) . "</recordcount>\n";

        $last_id = 0;
        foreach ($members as $rs) {

            $this_id = get_xml_db_field_val($rs, 'hostgroup_object_id');
            if ($this_id != $last_id) {
                if ($last_id > 0) {
                    $output .= "    </members>\n";
                    $output .= "  </hostgroup>\n";
                }
                $output .= "  <hostgroup id='" . get_xml_db_field_val($rs, 'hostgroup_object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'hostgroup_name');
                $output .= "    <members>\n";
            }

            $output .= "      <host id='" . get_xml_db_field_val($rs, 'host_object_id') . "'>\n";
            $output .= get_xml_db_field(4, $rs, 'host_name');
            $output .= "      </host>\n";

            $last_id = $this_id;
        }

        if ($last_id > 0) {
            $output .= "    </members>\n";
            $output .= "  </hostgroup>\n";
        }
        
    }

    $output .= "</hostgrouplist>\n";

    return $output;
}


function get_data_hostgroup_members($request_args, $raw = false, $arr_return = false)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', 0);
    $raw =  grab_array_var($request_args, 'raw', $raw);

    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["hostgroups"] . ".instance_id",
        "hostgroup_id" => $db_tables[DB_NDOUTILS]["hostgroups"] . ".hostgroup_object_id",
        "hostgroup_object_id" => $db_tables[DB_NDOUTILS]["hostgroups"] . ".hostgroup_object_id",
        "object_id" => $db_tables[DB_NDOUTILS]["hostgroups"] . ".hostgroup_object_id",
        "hostgroup_name" => "obj1.name1",
        "host_id" => $db_tables[DB_NDOUTILS]["hostgroup_members"] . ".host_object_id",
        "host_object_id" => $db_tables[DB_NDOUTILS]["hostgroup_members"] . ".host_object_id",
        "host_name" => "obj2.name1",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type"
    );

    $objectauthfields = array(
        "hostgroup_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    $q = $sqlquery['GetHostGroupMembers'];
    if ($totals) {
        $q = $sqlquery['GetHostGroupMembersCount'];
    }

    // Override sort order, as its critical in the membership list logic below...
    $request_args["orderby"] = "hostgroup_name";
    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $objects = $rs->GetArray();
        if ($raw) {
            return $objects;
        }
        if (!empty($objects) && !$totals) {

            // Set first hostgroup id
            $last_id = $objects[0]['hostgroup_object_id'];
            $members = array('host' => array());

            foreach ($objects as $i => $o) {
                if ($last_id != $o['hostgroup_object_id']) {
                    $output[] = array(
                        'hostgroup_object_id' => $objects[$i-1]['hostgroup_object_id'],
                        'hostgroup_name' => $objects[$i-1]['hostgroup_name'],
                        'instance_id' => $objects[$i-1]['instance_id'],
                        'members' => $members
                    );
                    $members = array('host' => array());
                    $last_id = $o['hostgroup_object_id'];
                }
                $members['host'][] = array(
                    'host_object_id' => $o['host_object_id'],
                    'host_name' => $o['host_name']
                );
            }

            $o = end($objects);
            $output[] = array(
                'hostgroup_object_id' => $o['hostgroup_object_id'],
                'hostgroup_name' => $o['hostgroup_name'],
                'instance_id' => $o['instance_id'],
                'members' => $members
            );

        } else if ($totals) {
            $output = $objects;
        }
    }

    // This is done for the API so that we can have the members count #
    if ($arr_return) {
        return array($output, count($objects));
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// SERVICEGROUP OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_servicegroup_objects_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', false);

    $servicegroups = get_data_servicegroup($request_args);
    
    if (!empty($servicegroups)) {

        $output .= "<servicegrouplist>\n";

        if ($totals) {
            $servicegroups = $servicegroups[0];
            $count = empty($servicegroups['total']) ? 0 : $servicegroups['total'];
            $output .= "  <recordcount>" . $count . "</recordcount>\n";
        } else {

            $output .= "  <recordcount>" . count($servicegroups) . "</recordcount>\n";

            foreach ($servicegroups as $rs) {

                $output .= "  <servicegroup id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'servicegroup_name');
                $output .= get_xml_db_field(2, $rs, 'is_active');
                $output .= get_xml_db_field(2, $rs, 'config_type');
                $output .= get_xml_db_field(2, $rs, 'alias');
                $output .= "  </servicegroup>\n";

            }
        }

        $output .= "</servicegrouplist>\n";
    } else {
        $output = "<servicegrouplist>\n
   <recordcount>0</recordcount>\n
</servicegrouplist>\n";
    }

    return $output;
}


function get_data_servicegroup($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', false);

    // default to only showing active objects unless overriden by request
    if (!isset($request_args["is_active"])) {
        $request_args["is_active"] = 1;
    }

    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["objects"] . ".instance_id",
        "servicegroup_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "servicegroup_object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "servicegroup_name" => $db_tables[DB_NDOUTILS]["objects"] . ".name1",
        "is_active" => $db_tables[DB_NDOUTILS]["objects"] . ".is_active",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type",
        "alias" => $db_tables[DB_NDOUTILS]["servicegroups"] . ".alias",
    );

    $objectauthfields = array(
        "servicegroup_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    $q = $sqlquery['GetServiceGroups'];
    if ($totals) {
        $q = $sqlquery['GetServiceGroupsCount'];
    }

    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $output = $rs->GetArray();
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// SERVICEGROUP SERVICE MEMBER OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_servicegroup_member_objects_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', 0);

    $members = get_data_servicegroup_members($request_args, true);

    $output = "<servicegrouplist>\n";
    
    if ($totals) {
        $count = empty($members[0]['total']) ? 0 : $members[0]['total'];
        $output .= "  <recordcount>" . $count . "</recordcount>\n";
    } else {
    
        $output .= "  <recordcount>" . count($members) . "</recordcount>\n";

        $last_id = 0;
        foreach ($members as $rs) {

            $this_id = get_xml_db_field_val($rs, 'servicegroup_object_id');
            if ($this_id != $last_id) {
                if ($last_id > 0) {
                    $output .= "    </members>\n";
                    $output .= "  </servicegroup>\n";
                }
                $output .= "  <servicegroup id='" . get_xml_db_field_val($rs, 'servicegroup_object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'servicegroup_name');
                $output .= "    <members>\n";
            }
            $output .= "      <service id='" . get_xml_db_field_val($rs, 'service_object_id') . "'>\n";
            $output .= get_xml_db_field(4, $rs, 'host_name');
            $output .= get_xml_db_field(4, $rs, 'service_description');
            $output .= "      </service>\n";

            $last_id = $this_id;
        }

        if ($last_id > 0) {
            $output .= "    </members>\n";
            $output .= "  </servicegroup>\n";
        }
    }

    $output .= "</servicegrouplist>\n";

    return $output;
}


function get_data_servicegroup_members($request_args, $raw = false, $arr_return = false)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', 0);
    $raw =  grab_array_var($request_args, 'raw', $raw);

    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["servicegroups"] . ".instance_id",
        "servicegroup_id" => $db_tables[DB_NDOUTILS]["servicegroups"] . ".servicegroup_object_id",
        "servicegroup_object_id" => $db_tables[DB_NDOUTILS]["servicegroups"] . ".servicegroup_object_id",
        "object_id" => $db_tables[DB_NDOUTILS]["servicegroups"] . ".servicegroup_object_id",
        "servicegroup_name" => "obj1.name1",
        "host_name" => "obj2.name1",
        "service_id" => $db_tables[DB_NDOUTILS]['servicegroup_members'] . ".service_object_id",
        "service_object_id" => $db_tables[DB_NDOUTILS]['servicegroup_members'] . ".service_object_id",
        "service_description" => "obj2.name2",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type"
    );

    $objectauthfields = array(
        "servicegroup_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    $q = $sqlquery['GetServiceGroupMembers'];
    if ($totals) {
        $q = $sqlquery['GetServiceGroupMembersCount'];
    }

    // Override sort order, as its critical in the membership list logic below...
    $request_args["orderby"] = "servicegroup_name";
    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );

    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $objects = $rs->GetArray();
        if ($raw) {
            return $objects;
        }
        if (!empty($objects) && !$totals) {

            // Set first servicegroup id
            $last_id = $objects[0]['servicegroup_object_id'];
            $members = array('service' => array());

            foreach ($objects as $i => $o) {
                if ($last_id != $o['servicegroup_object_id']) {
                    $output[] = array(
                        'servicegroup_object_id' => $objects[$i-1]['servicegroup_object_id'],
                        'servicegroup_name' => $objects[$i-1]['servicegroup_name'],
                        'instance_id' => $objects[$i-1]['instance_id'],
                        'members' => $members
                    );
                    $members = array('service' => array());
                    $last_id = $o['servicegroup_object_id'];
                }
                $members['service'][] = array(
                    'service_object_id' => $o['service_object_id'],
                    'host_name' => $o['host_name'],
                    'service_description' => $o['service_description']
                );
            }

            $o = end($objects);
            $output[] = array(
                'servicegroup_object_id' => $o['servicegroup_object_id'],
                'servicegroup_name' => $o['servicegroup_name'],
                'instance_id' => $o['instance_id'],
                'members' => $members
            );

        } else if ($totals) {
            $output = $objects;
        }
    }

    // This is done for the API so that we can have the members count #
    if ($arr_return) {
        return array($output, count($objects));
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// SERVICEGROUP HOST MEMBER OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_servicegroup_host_member_objects_xml_output($request_args)
{
    global $sqlquery;
    global $db_tables;

    $output = "";

    // generate query
    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["servicegroups"] . ".instance_id",
        "servicegroup_id" => $db_tables[DB_NDOUTILS]["servicegroups"] . ".servicegroup_object_id",
        "servicegroup_name" => "obj1.name1",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type"
    );
    $objectauthfields = array(
        "servicegroup_id"
    );
    $instanceauthfields = array(
        "instance_id"
    );
    // override sort order, as its critical in the membership list logic below...
    $request_args["orderby"] = "servicegroup_name";
    $args = array(
        "sql" => $sqlquery['GetServiceGroupHostMembers'],
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args, // ADDED 1/1/10 FOR NEW NON-BACKEND CALLS
    );

    $sql = generate_sql_query(DB_NDOUTILS, $args);
    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        //output_backend_header();

        $output .= "<servicegrouplist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request_args["totals"])) {
            $last_id = 0;
            while (!$rs->EOF) {

                $this_id = get_xml_db_field_val($rs, 'servicegroup_object_id');
                if ($this_id != $last_id) {
                    if ($last_id > 0) {
                        $output .= "    </members>\n";
                        $output .= "  </servicegroup>\n";
                    }
                    $output .= "  <servicegroup id='" . get_xml_db_field_val($rs, 'servicegroup_object_id') . "'>\n";
                    $output .= get_xml_db_field(2, $rs, 'instance_id');
                    $output .= get_xml_db_field(2, $rs, 'servicegroup_name');
                    $output .= "    <members>\n";
                }
                $output .= "      <host id='" . get_xml_db_field_val($rs, 'host_object_id') . "'>\n";
                $output .= get_xml_db_field(4, $rs, 'host_name');
                $output .= "      </host>\n";

                $last_id = $this_id;

                $rs->MoveNext();
            }
            if ($last_id > 0) {
                $output .= "    </members>\n";
                $output .= "  </servicegroup>\n";
            }
        }

        $output .= "</servicegrouplist>\n";
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// CONTACTGROUP OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_contactgroup_objects_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', 0);

    $contactgroups = get_data_contactgroup($request_args);

    if (!empty($contactgroups)) {

        $output .= "<contactgrouplist>\n";

        if ($totals) {
            $contactgroups = $contactgroups[0];
            $count = empty($contactgroups['total']) ? 0 : $contactgroups['total'];
            $output .= "  <recordcount>" . $count . "</recordcount>\n";
        } else {
        
            $output .= "  <recordcount>" . count($contactgroups) . "</recordcount>\n";

            foreach ($contactgroups as $rs) {

                $output .= "  <contactgroup id='" . get_xml_db_field_val($rs, 'object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'contactgroup_name');
                $output .= get_xml_db_field(2, $rs, 'is_active');
                $output .= get_xml_db_field(2, $rs, 'config_type');
                $output .= get_xml_db_field(2, $rs, 'alias');
                $output .= "  </contactgroup>\n";

            }
        }

        $output .= "</contactgrouplist>\n";
    }

    return $output;
}


function get_data_contactgroup($request_args)
{
    global $sqlquery;
    global $db_tables;

    $totals = grab_array_var($request_args, 'totals', 0);

    // Default to only showing active objects unless overriden by request
    if (!isset($request_args["is_active"])) {
        $request_args["is_active"] = 1;
    }

    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["objects"] . ".instance_id",
        "contactgroup_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "contactgroup_object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "object_id" => $db_tables[DB_NDOUTILS]["objects"] . ".object_id",
        "contactgroup_name" => $db_tables[DB_NDOUTILS]["objects"] . ".name1",
        "is_active" => $db_tables[DB_NDOUTILS]["objects"] . ".is_active",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type",
        "alias" => $db_tables[DB_NDOUTILS]["contactgroups"] . ".alias"
    );

    $objectauthfields = array(
        "contactgroup_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    $q = $sqlquery['GetContactGroups'];
    if ($totals) {
        $q = $sqlquery['GetContactGroupsCount'];
    }

    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $output = $rs->GetArray();
    }

    return $output;
}


////////////////////////////////////////////////////////////////////////////////
// CONTACTGROUP MEMBER OBJECTS
////////////////////////////////////////////////////////////////////////////////

/**
 * @param $request_args
 *
 * @return string
 */
function get_contactgroup_member_objects_xml_output($request_args)
{
    $output = "";

    $totals = grab_array_var($request_args, 'totals', 0);

    $members = get_data_contactgroup_members($request_args, true);

    $output .= "<contactgrouplist>\n";
    
    if ($totals) {
        $count = empty($members[0]['total']) ? 0 : $members[0]['total'];
        $output .= "  <recordcount>" . $count . "</recordcount>\n";
    } else {

        $output .= "  <recordcount>" . count($members) . "</recordcount>\n";

        $last_id = 0;
        foreach ($members as $rs) {

            $this_id = get_xml_db_field_val($rs, 'contactgroup_object_id');
            if ($this_id != $last_id) {
                if ($last_id > 0) {
                    $output .= "    </members>\n";
                    $output .= "  </contactgroup>\n";
                }
                $output .= "  <contactgroup id='" . get_xml_db_field_val($rs, 'contactgroup_object_id') . "'>\n";
                $output .= get_xml_db_field(2, $rs, 'instance_id');
                $output .= get_xml_db_field(2, $rs, 'contactgroup_name');
                $output .= "    <members>\n";
            }
            $output .= "      <contact id='" . get_xml_db_field_val($rs, 'contact_object_id') . "'>\n";
            $output .= get_xml_db_field(4, $rs, 'contact_name');
            $output .= "      </contact>\n";

            $last_id = $this_id;
        }
        
        if ($last_id > 0) {
            $output .= "    </members>\n";
            $output .= "  </contactgroup>\n";
        }
    }

    $output .= "</contactgrouplist>\n";

    return $output;
}


function get_data_contactgroup_members($request_args, $raw = false, $arr_return = false)
{
    global $sqlquery;
    global $db_tables;

    $output = array();

    $totals = grab_array_var($request_args, 'totals', 0);
    $raw =  grab_array_var($request_args, 'raw', $raw);

    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["contactgroups"] . ".instance_id",
        "contactgroup_id" => $db_tables[DB_NDOUTILS]["contactgroups"] . ".contactgroup_object_id",
        "contactgroup_name" => "obj1.name1",
        "contact_name" => "obj2.name1",
        "config_type" => $db_tables[DB_NDOUTILS]["objects"] . ".config_type"
    );

    $objectauthfields = array(
        "contactgroup_id"
    );

    $instanceauthfields = array(
        "instance_id"
    );

    $q = $sqlquery['GetContactGroupMembers'];
    if ($totals) {
        $q = $sqlquery['GetContactGroupMembersCount'];
    }

    // Override sort order, as its critical in the membership list logic below...
    $request_args["orderby"] = "contactgroup_name";
    $args = array(
        "sql" => $q,
        "fieldmap" => $fieldmap,
        "objectauthfields" => $objectauthfields,
        "objectauthperms" => P_READ,
        "instanceauthfields" => $instanceauthfields,
        "useropts" => $request_args
    );

    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql, false))) {
        //handle_backend_db_error(DB_NDOUTILS);
    } else {
        $objects = $rs->GetArray();
        if ($raw) {
            return $objects;
        }
        if (!empty($objects) && !$totals) {

            // Set first servicegroup id
            $last_id = $objects[0]['contactgroup_object_id'];
            $members = array('contact' => array());

            foreach ($objects as $i => $o) {
                if ($last_id != $o['contactgroup_object_id']) {
                    $output[] = array(
                        'contactgroup_object_id' => $objects[$i-1]['contactgroup_object_id'],
                        'contactgroup_name' => $objects[$i-1]['contactgroup_name'],
                        'instance_id' => $objects[$i-1]['instance_id'],
                        'members' => $members
                    );
                    $members = array('contact' => array());
                    $last_id = $o['contactgroup_object_id'];
                }
                $members['contact'][] = array(
                    'contact_object_id' => $o['contact_object_id'],
                    'contact_name' => $o['contact_name']
                );
            }

            $o = end($objects);
            $output[] = array(
                'contactgroup_object_id' => $o['contactgroup_object_id'],
                'contactgroup_name' => $o['contactgroup_name'],
                'instance_id' => $o['instance_id'],
                'members' => $members
            );

        } else if ($totals) {
            $output = $objects;
        }
    }

    // This is done for the API so that we can have the members count #
    if ($arr_return) {
        return array($output, count($objects));
    }

    return $output;
}

