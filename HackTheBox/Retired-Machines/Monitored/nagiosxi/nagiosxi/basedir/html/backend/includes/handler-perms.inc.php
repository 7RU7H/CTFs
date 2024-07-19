<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');


// INSTANCE PERMS *************************************************************************
function fetch_instanceperms()
{
    global $sqlquery;
    global $db_tables;
    global $request;

    // generate query
    $fieldmap = array(
        "instance_id" => $db_tables["ndoutils"]["instances"] . ".instance_id",
        "instance_name" => $db_tables["ndoutils"]["instances"] . ".instance_name",
        "instance_description" => $db_tables["ndoutils"]["instances"] . ".instance_description"
    );
    $instanceauthfields = array(
        "instance_id"
    );
    $args = array(
        "sql" => $sqlquery['GetInstances'],
        "fieldmap" => $fieldmap,
        "instanceauthfields" => $instanceauthfields,
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    $instance_perms = get_cached_instance_perms(0);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        handle_backend_db_error();
    } else {

        // Generate the XML
        $outputtype = grab_request_var("outputtype", "");
        $output = "<instancepermlist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request["totals"])) {
            while (!$rs->EOF) {

                $eperms = get_effective_instance_perms($rs->fields["instance_id"]);
                if ($eperms == P_NONE) {
                    $rs->MoveNext();
                    continue;
                }

                $perms = P_NONE;
                if (array_key_exists($rs->fields["instance_id"], $instance_perms)) {
                    $perms = $instance_perms[$rs->fields["instance_id"]];
                }

                if ($outputtype == "json") {
                    $output .= "  <instance>\n";
                    $output .= xml_db_field(2, $rs, 'instance_id', 'id', true);
                } else {
                    $output .= "  <instance id='" . db_field($rs, 'instance_id') . "'>\n";
                }

                xml_field(2, 'perms', $perms, '', true);
                xml_field(2, 'perms_s', get_perm_string($perms), '', true);
                xml_field(2, 'eperms', $eperms, '', true);
                xml_field(2, 'eperms_s', get_perm_string($eperms), '', true);
                $output .= "  </instance>\n";
                $rs->MoveNext();
            }
        }
        $output .= "</instancepermlist>\n";

        print backend_output($output);
    }
}


// OBJECT PERMS  *************************************************************************
function fetch_objectperms()
{
    global $sqlquery;
    global $db_tables;
    global $request;

    // generate query
    $fieldmap = array(
        "instance_id" => $db_tables["ndoutils"]["objects"] . ".instance_id",
        "object_id" => $db_tables["ndoutils"]["objects"] . ".object_id",
        "objecttype_id" => $db_tables["ndoutils"]["objects"] . ".objecttype_id",
        "name1" => $db_tables["ndoutils"]["objects"] . ".name1",
        "name2" => $db_tables["ndoutils"]["objects"] . ".name2",
        "is_active" => $db_tables["ndoutils"]["objects"] . ".is_active",
    );
    $instanceauthfields = array(
        "instance_id"
    );

    $limitopts = array("is_active" => 1);

    $args = array(
        "sql" => $sqlquery['GetObjects'],
        "fieldmap" => $fieldmap,
        "useropts" => $limitopts,
        "instanceauthfields" => $instanceauthfields,
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    $object_perms = get_cached_object_perms(0);
    $object_id_perms = $object_perms["0"];

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        handle_backend_db_error();
    } else {

        // Generate the XML
        $outputtype = grab_request_var("outputtype", "");
        $output = "<objectpermlist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request["totals"])) {
            while (!$rs->EOF) {

                $eperms = get_effective_object_perms($rs->fields["object_id"]);
                if ($eperms == P_NONE) {
                    $rs->MoveNext();
                    continue;
                }

                $perms = P_NONE;
                if (array_key_exists($rs->fields["object_id"], $object_id_perms)) {
                    $perms = $object_id_perms[$rs->fields["object_id"]];
                }

                if ($outputtype == "json") {
                    $output .= "  <object>\n";
                    $output .= xml_db_field(2, $rs, 'object_id', 'id', true);
                } else {
                    $output .= "  <object id='" . db_field($rs, 'object_id') . "'>\n";
                }

                $output .= xml_field(2, 'perms', $perms, '', true);
                $output .= xml_field(2, 'perms_s', get_perm_string($perms), '', true);
                $output .= xml_field(2, 'eperms', $eperms, '', true);
                $output .= xml_field(2, 'eperms_s', get_perm_string($eperms), '', true);
                $output .= "  </object>\n";
                $rs->MoveNext();
            }
        }
        $output .= "</objectpermlist>\n";

        print backend_output($output);
    }
}
