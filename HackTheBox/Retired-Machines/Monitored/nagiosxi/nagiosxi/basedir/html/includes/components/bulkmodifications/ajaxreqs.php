<?php
//
// Bulk Modifications Component
// Copyright (c) 2010-2021 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');

// Initialization stuff
pre_init();
init_session();
grab_request_vars();

// Check prereqs and authentication
check_prereqs();
check_authentication(false);

// Only admins and people with CCM access can access this component
if (get_user_meta(0, 'ccm_access') == 0 && !is_admin()) {
    echo _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    exit();
}

// Route the request to the porper area
$cmd = grab_request_var('cmd', false);
switch ($cmd) {

    case 'getcontacts':
        print get_ajax_relationship_table();
        break;

    case 'getcontactgroups':
        print get_cg_ajax_relationship_table();
        break;

    case 'gethostgroups':
        print get_hg_ajax_relationship_table();
        break;

    case 'getservicegroups':
        print get_sg_ajax_relationship_table();
        break;

    case 'getparentshosts':
        print get_ph_ajax_relationship_table();
        break;

    case 'gethostsinhostgroup':
        // NOTE: These MEMBERS are not TRUE to the DB. They are from Nagios Core.
        //       This means that you may have to apply configuration to see member objects appear in host/service groups.
        $hostgroup = grab_request_var('hostgroup', '');
        $args = array('id' => $hostgroup);
        $xml = get_xml_hostgroup_member_objects($args);
        $hosts = array();
        if ($xml->recordcount > 0) {
            foreach ($xml->hostgroup->members->host as $obj) {
                $hosts[intval($obj->attributes()->id)] = strval($obj->host_name);
            }
        }
        print json_encode($hosts);
        break;

    case 'getservicesinservicegroup':
        // NOTE: These MEMBERS are not TRUE to the DB. They are from Nagios Core.
        //       This means that you may have to apply configuration to see member objects appear in host/service groups.
        $servicegroup = grab_request_var('servicegroup', '');
        $args = array('id' => $servicegroup);
        $xml = get_xml_servicegroup_member_objects($args);
        $services = array();
        if ($xml->recordcount > 0) {
            foreach ($xml->servicegroup->members->service as $obj) {
                $services[intval($obj->attributes()->id)] = array('service_name' => strval($obj->service_name), 'service_description' => strval($obj->service_description));
            }
        }
        print json_encode($services);
        break;

    case 'getvariables':
        print get_free_variables_ajax_relationship_table();
    default:
        break;
}

// Gets relationship table for contacts
function get_ajax_relationship_table($opt = 'host')
{
    $contact = grab_request_var('contact', false);
    $id = grab_request_var('id', false);
    $html = '<div class="relation-tables">';

    $query = "SELECT `id`,`host_name` FROM `tbl_lnkHostToContact` LEFT JOIN `tbl_host` ON `idMaster` = `id` WHERE `idSlave` = ".intval($id)." ORDER BY host_name ASC";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    $html .= "<div class='leftBox'>
            <h5>" . _("Hosts directly assigned to contact") . ": {$contact}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                <tr>
                    <th>" . _("Host") . "</th>
                    <th>" . _("Assigned as Contact") . "
                        (<a id='checkAllhost' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"host\");'>" . _("Check All") . "</a>)
                    </th>
                </tr>
                </thead>
                <tbody>";

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='2'>" . _("No relationships for this contact") . "</td></tr>";
    }

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $hosts = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $hosts[] = $o['object_id'];
        }
    }

    foreach ($results as $r) {
        $checkbox = "<input class='host' type='checkbox' name='hostschecked[]' value='" . $r['id'] . "' />";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $hosts)) {
                $checkbox = "";
                $r['host_name'] = _('Unknown');
            }
        }

        $html .= "<tr><td>" . $r['host_name'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= "</tbody></table></div>";
    $html .= "<div class='rightBox'>
            <h5>" . _("Service directly assigned to contact") . ": {$contact}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                    <tr>
                        <th>" . _("Config Name") . "</th>
                        <th>" . _("Service Description") . "</th>
                        <th>" . _("Assigned as Contact") . "
                            (<a id='checkAllservice' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"service\");'>" . _("Check All") . "</a>)
                        </th>
                    </tr>
                </thead><tbody>";

    // Get option list
    $query = "SELECT `id`,`config_name`,`service_description` FROM `tbl_lnkServiceToContact` LEFT JOIN `tbl_service` ON `idMaster` = `id` WHERE `idSlave` = ".intval($id)." ORDER BY config_name, service_description ASC";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $services = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $services[] = $o['object_id'];
        }
    }

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='3'>" . _("No relationships for this contact") . "</td></tr>";
    }
    
    // Display list
    foreach ($results as $r) {
        $checkbox = "<input class='service' type='checkbox' name='serviceschecked[]' value='" . $r['id'] . "' />";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $services)) {
                $checkbox = "";
                $r['config_name'] = _('Unknown');
                $r['service_description'] = _('Unknown');
            }
        }

        $html .= "<tr><td>" . $r['config_name'] . "</td><td>" . $r['service_description'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= '</tbody></table>
    </div>
    <div class="clear"></div>
    </div>';

    return $html;
}

// Gets relationship table for contact groups
function get_cg_ajax_relationship_table($opt = 'host')
{
    $contactgroup = grab_request_var('contactgroup', false);
    $id = grab_request_var('id', false);

    $query = "SELECT `id`,`host_name` FROM `tbl_lnkHostToContactgroup` LEFT JOIN `tbl_host` ON `idMaster` = `id` WHERE `idSlave` = ".intval($id)." ORDER BY host_name ASC";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    $html = "<div class='bulk_wizard'>";
    $html .= "<div class='leftBox'>
            <h5>" . _("Hosts directly assigned to contact") . ": {$contactgroup}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                    <tr>
                        <th>" . _("Host") . "</th>
                        <th>" . _("Assigned as Contact Group") . "
                            (<a id='checkAllhost' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"host\");'>" . _("Check All") . "</a>)
                        </th>
                    </tr>
                </thead>
                <tbody>";

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='2'>" . _("No relationships for this contact group") . "</td></tr>";
    }

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $hosts = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $hosts[] = $o['object_id'];
        }
    }

    foreach ($results as $r) {
        $checkbox = "<input class='host' type='checkbox' name='hostschecked[]' value='" . $r['id'] . "' />";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $hosts)) {
                $checkbox = "";
                $r['host_name'] = _('Unknown');
            }
        }

        $html .= "<tr><td>" . $r['host_name'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= "</tbody></table></div>";
    $html .= "<div class='rightBox'>
            <h5>" . _("Service directly assigned to contact") . ": {$contactgroup}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                    <tr>
                        <th>" . _("Config Name") . "</th>
                        <th>" . _("Service Description") . "</th>
                        <th>" . _("Assigned as Contact") . "
                            (<a id='checkAllservice' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"service\");'>" . _("Check All") . "</a>)
                        </th>
                    </tr>
                </thead>
                <tbody>";

    // Get option list
    $query = "SELECT `id`,`config_name`,`service_description` FROM `tbl_lnkServiceToContactgroup` LEFT JOIN `tbl_service` ON `idMaster` = `id` WHERE `idSlave` = ".intval($id)." ORDER BY config_name, service_description ASC";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='3'>" . _("No relationships for this contact group") . "</td></tr>";
    }

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $services = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $services[] = $o['object_id'];
        }
    }

    // Display list
    foreach ($results as $r) {
        $checkbox = "<input class='service' type='checkbox' name='serviceschecked[]' value='" . $r['id'] . "' />";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $services)) {
                $checkbox = "";
                $r['config_name'] = _('Unknown');
                $r['service_description'] = _('Unknown');
            }
        }
        
        $html .= "<tr><td>" . $r['config_name'] . "</td><td>" . $r['service_description'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= '</tbody></table>
    </div>
    <div class="clear"></div>
    </div>';

    return $html;
}

function get_hg_ajax_relationship_table()
{
    $hostgroup = grab_request_var('hostgroup', false);
    $id = grab_request_var('id', false);

    $query = "SELECT `id`,`host_name` FROM `tbl_lnkHostToHostgroup` LEFT JOIN `tbl_host` ON `idMaster` = `id` WHERE `idSlave` = ".intval($id)." ORDER BY host_name ASC";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    $html = "<div class='bulk_wizard'>";
    $html .= "<div>
            <h5>" . _("Hosts directly assigned to Host Group") . ": {$hostgroup}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                    <tr>
                        <th>" . _("Host") . "</th>
                        <th>
                            " . _("Assigned as Host Group") . "
                            (<a id='checkAllhost' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"host\");'>" . _("Check All") . "</a>)
                        </th>
                    </tr>
                </thead>
                <tbody>";

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='2'>" . _("No relationships for this host group") . "</td></tr>";
    }

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $hosts = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $hosts[] = $o['object_id'];
        }
    }

    foreach ($results as $r) {
        $checkbox = "<input class='host' type='checkbox' name='hostschecked[]' value='" . $r['id'] . "'>";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $hosts)) {
                $checkbox = "";
                $r['host_name'] = _('Unknown');
            }
        }

        $html .= "<tr><td>" . $r['host_name'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= "</tbody></table></div>";

    return $html;
}

// Servicegroup Table
function get_sg_ajax_relationship_table()
{
    $servicegroup = grab_request_var('servicegroup', false);
    $id = grab_request_var('id', false);

    $query = "SELECT `id`,`config_name`, `service_description` FROM `tbl_lnkServiceToServicegroup` LEFT JOIN `tbl_service` ON `idMaster` = `id` WHERE `idSlave` = ".intval($id);
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    $html = "<div class='bulk_wizard'>";
    $html .= "<div>
            <h5>" . _("Services directly assigned to Service Group") . ": {$servicegroup}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                    <tr>
                        <th>" . _("Service") . "</th>
                        <th>" . _("Host") . "</th>
                        <th>
                            " . _("Assigned as Service Group") . "
                            (<a id='checkAllservice' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"service\");'>" . _("Check All") . "</a>)
                        </th>
                    </tr>
                </thead>
                <tbody>";

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='3'>" . _("No relationships for this service group") . "</td></tr>";
    }

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $services = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $services[] = $o['object_id'];
        }
    }

    foreach ($results as $r) {
        $checkbox = "<input class='service' type='checkbox' name='serviceschecked[]' value='" . $r['id'] . "'>";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $services)) {
                $checkbox = "";
                $r['service_description'] = _('Unknown');
            }
        }

        $html .= "<tr><td>" . $r['service_description'] . "</td><td>" . $r['config_name'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= "</tbody></table></div>";

    return $html;
}

function get_ph_ajax_relationship_table()
{
    $parenthost = grab_request_var('parenthost', false);
    $id = grab_request_var('id', false);

    $query = "SELECT `id`,`host_name` FROM `tbl_lnkHostToHost` LEFT JOIN `tbl_host` ON `idMaster` = `id` WHERE `idSlave` = ".intval($id)." ORDER BY host_name ASC";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    $html = "<div class='bulk_wizard'>";
    $html .= "<div>
            <h5>" . _("Hosts directly assigned as children of the Parent Host") . ": {$parenthost}</h5>
            <p class='label'>" . _("Check any child hosts you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                    <tr>
                        <th>" . _("Host") . "</th>
                        <th>
                            " . _("Assigned as Child Host") . "
                            (<a id='checkAllhost' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"host\");'>" . _("Check All") . "</a>)
                        </th>
                    </tr>
                </thead>
                <tbody>";

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='2'>" . _("No child hosts") . "</td></tr>";
    }

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $hosts = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $hosts[] = $o['object_id'];
        }
    }

    foreach ($results as $r) {
        $checkbox = "<input class='host' type='checkbox' name='hostschecked[]' value='" . $r['id'] . "'>";
        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $hosts)) {
                $checkbox = "";
                $r['host_name'] = _('Unknown');
            }
        }
        $html .= "<tr><td>" . $r['host_name'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= "</tbody></table></div>";

    return $html;
}

function get_free_variables_ajax_relationship_table()
{
    $variable_name = grab_request_var('variablename', false);
    $variable_name = escape_sql_param($variable_name, DB_NAGIOSQL);
    $html = '';

    $id = grab_request_var('id', false);
    // id must be a list of numbers that we can put into mysql, so only 0-9, whitespace, and commas are allowed.
    if (preg_match('/[^0-9,\s]/', $id)) {
        return "<p>" ._("id should be a comma-delimited list of integers, optionally with whitespace") . '</p>';
    }

    // Populate hosts table
    $query = "SELECT `tbl_host`.`id` as id, `host_name`,`tbl_variabledefinition`.`value` FROM `tbl_lnkHostToVariabledefinition` LEFT JOIN `tbl_host` ON `idMaster` = `tbl_host`.`id` JOIN `tbl_variabledefinition` ON `idSlave` = `tbl_variabledefinition`.`id` WHERE `idSlave` IN ({$id})";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    $html .= "<div class='leftBox'>
            <h5>" . _("Hosts using variable") . ": {$variable_name}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                <tr>
                    <th>" . _("Host") . "</th>
                    <th>" . _("Value") . "</th>
                    <th>" . _("Remove This Variable") . "
                        (<a id='checkAllhost' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"host\");'>" . _("Check All") . "</a>)
                    </th>
                </tr>
                </thead>
                <tbody>";

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='3'>" . _("No host relationships for this variable") . "</td></tr>";
    }

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $hosts = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_HOST." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $hosts[] = $o['object_id'];
        }
    }

    foreach ($results as $r) {
        $checkbox = "<input class='host' type='checkbox' name='hostschecked[]' value='" . $r['id'] . "' />";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $hosts)) {
                $checkbox = "";
                $r['host_name'] = _('Unknown');
            }
        }

        $html .= "<tr><td>" . $r['host_name'] . "</td><td>". $r['value'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= "</tbody></table></div>";


    // Populate services table
    $query = "SELECT `tbl_service`.`id`,`config_name`,`service_description`,`tbl_variabledefinition`.`value` FROM `tbl_lnkServiceToVariabledefinition` LEFT JOIN `tbl_service` ON `idMaster` = `id` JOIN `tbl_variabledefinition` ON `idSlave` = `tbl_variabledefinition`.`id` WHERE `idSlave` IN ({$id})";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    $html .= "<div class='leftBox'>
            <h5>" . _("Services using variable") . ": {$variable_name}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                <tr>
                    <th>" . _("Config Name") . "</th>
                    <th>" . _("Service Description") . "</th>
                    <th>" . _("Value") . "</th>
                    <th>" . _("Remove This Variable") . "
                        (<a id='checkAllservice' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"service\");'>" . _("Check All") . "</a>)
                    </th>
                </tr>
                </thead>
                <tbody>";

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $services = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $services[] = $o['object_id'];
        }
    }

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='4'>" . _("No service relationships for this variable") . "</td></tr>";
    }
    
    // Display list
    foreach ($results as $r) {
        $checkbox = "<input class='service' type='checkbox' name='serviceschecked[]' value='" . $r['id'] . "' />";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $services)) {
                $checkbox = "";
                $r['config_name'] = _('Unknown');
                $r['service_description'] = _('Unknown');
            }
        }

        $html .= "<tr><td>" . $r['config_name'] . "</td><td>" . $r['service_description'] . "</td><td>". $r['value'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= '</tbody></table></div>';


    // Populate contacts table
    $query = "SELECT `tbl_contact`.`id`, `contact_name`,`tbl_variabledefinition`.`value` FROM `tbl_lnkContactToVariabledefinition` LEFT JOIN `tbl_contact` ON `idMaster` = `id` JOIN `tbl_variabledefinition` ON `idSlave` = `tbl_variabledefinition`.`id` WHERE `idSlave` IN ({$id})";
    $results = exec_sql_query(DB_NAGIOSQL, $query, true);

    $html .= "<div class='leftBox'>
            <h5>" . _("Contacts using variable") . ": {$variable_name}</h5>
            <p class='label'>" . _("Check any relationships you wish to") . " <strong>" . _("remove") . "</strong></p>
            <table class='table table-condensed table-bordered table-striped table-auto-width table-no-margin'>
                <thead>
                <tr>
                    <th>" . _("Contact Name") . "</th>
                    <th>" . _("Value") . "</th>
                    <th>" . _("Remove This Variable") . "
                        (<a id='checkAllcontact' style='float:none;' title='"._("Check All")."' href='javascript:checkAllType(\"contact\");'>" . _("Check All") . "</a>)
                    </th>
                </tr>
                </thead>
                <tbody>";

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $contacts = array();
        $sql = "SELECT object_id FROM tbl_permission WHERE type = " . OBJECTTYPE_CONTACT. " AND user_id = ".intval($_SESSION['user_id']);
        $rs = exec_sql_query(DB_NAGIOSQL, $sql, true);
        $objs = $rs->GetArray();
        foreach ($objs as $o) {
            $contacts[] = $o['object_id'];
        }
    }

    if ($results->recordCount() == 0) {
        $html .= "<tr style='width:300px;'><td colspan='3'>" . _("No contact relationships for this variable") . "</td></tr>";
    }

    foreach ($results as $r) {
        $checkbox = "<input class='contact' type='checkbox' name='contactschecked[]' value='" . $r['id'] . "' />";

        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
            if (!in_array($r['id'], $contacts)) {
                $checkbox = "";
                $r['contact_name'] = _('Unknown');
            }
        }

        $html .= "<tr><td>" . $r['contact_name'] . "</td><td>". $r['value'] . "</td><td style='text-align:center;'>".$checkbox."</td></tr>";
    }

    $html .= '</tbody></table></div>
    <div class="clear"></div>';

    return $html;
}