<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/common.inc.php');


// BACKEND TICKET *************************************************************************
// used by third party apps to get the currently authenticated user's backend ticket 
function fetch_backend_ticket()
{
    $ticket = get_user_attr(0, "backend_ticket");
    $outputtype = grab_request_var("outputtype", "");

    if (have_value($ticket)) {
        if ($outputtype == "json") {
            $output = array("ticket" => $ticket);
        } else {
            $output = "<ticket>" . $ticket . "</ticket>\n";
        }
        print backend_output($output);
    } else {
        handle_backend_error("NO TICKET");
    }
}


// MAGIC PIXEL *************************************************************************
// used by Nagios Fusion for auto-login
function fetch_magic_pixel()
{
    $imgfile = get_root_dir() . "/html/images/pixel.png";
    header("Content-Type: image/png");

    $fh = fopen($imgfile, "r");
    if ($fh) {
        $contents = fread($fh, filesize($imgfile));
        fclose($fh);
        echo $contents;
    }
}


// NDO DB VERSION INFO *************************************************************************
function fetch_ndodbversion()
{
    global $sqlquery;
    global $db_tables;

    // generate query
    $fieldmap = array(
        "name" => $db_tables[DB_NDOUTILS]["dbversion"] . ".name",
        "version" => $db_tables[DB_NDOUTILS]["dbversion"] . ".version"
    );
    $args = array(
        "sql" => $sqlquery['GetNDODBVersionInfo'],
        "fieldmap" => $fieldmap
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        handle_backend_db_error(DB_NDOUTILS);
    } else {

        // Generate the XML
        $output = "<dbversioninfo>\n";
        while (!$rs->EOF) {

            $output .= "  <packageinfo>\n";
            $output .= xml_db_field(2, $rs, 'name', '', true);
            $output .= xml_db_field(2, $rs, 'version', '', true);
            $output .= "  </packageinfo>\n";

            $rs->MoveNext();
        }
        $output .= "</dbversioninfo>\n";

        print backend_output($output);
    }
}


// INSTANCES *************************************************************************
function fetch_instances()
{
    global $sqlquery;
    global $db_tables;
    global $request;

    // generate query
    $fieldmap = array(
        "instance_id" => $db_tables[DB_NDOUTILS]["instances"] . ".instance_id",
        "instance_name" => $db_tables[DB_NDOUTILS]["instances"] . ".instance_name",
        "instance_description" => $db_tables[DB_NDOUTILS]["instances"] . ".instance_description"
    );
    $instanceauthfields = array(
        "instance_id"
    );
    $args = array(
        "sql" => $sqlquery['GetInstances'],
        "fieldmap" => $fieldmap,
        "instanceauthfields" => $instanceauthfields,
        "instanceauthperms" => P_LIST
    );
    $sql = generate_sql_query(DB_NDOUTILS, $args);

    if (!($rs = exec_sql_query(DB_NDOUTILS, $sql))) {
        handle_backend_db_error(DB_NDOUTILS);
    } else {

        // Generate the XML
        $outputtype = grab_request_var("outputtype", "");
        $output = "<instancelist>\n";
        $output .= "  <recordcount>" . $rs->RecordCount() . "</recordcount>\n";

        if (!isset($request["totals"])) {
            while (!$rs->EOF) {

                if ($outputtype == "json") {
                    $output .= "  <instance>\n";
                } else {
                    $output .= "  <instance id='" . db_field($rs, 'instance_id') . "'>\n";
                }

                $output .= xml_db_field(2, $rs, 'instance_id', 'id', true);
                $output .= xml_db_field(2, $rs, 'instance_name', 'name', true);
                $output .= xml_db_field(2, $rs, 'instance_description', 'description', true);
                $output .= "  </instance>\n";

                $rs->MoveNext();
            }
        }
        $output .= "</instancelist>\n";

        print backend_output($output);
    }
}
