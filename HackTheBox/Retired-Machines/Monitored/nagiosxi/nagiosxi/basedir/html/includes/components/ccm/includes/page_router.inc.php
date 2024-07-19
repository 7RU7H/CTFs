<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: page_router.inc.php
//  Desc: Functions for directing the user to certain pages inside the CCM.
//


/**
 * Main page content routing function. Handles ALL requests for action in the CCM to build appropriate page.
 *
 * @global bool $AUTH main authorization boolean
 * @return string $html html page output
 */
function page_router()
{
    global $ccm;
    global $AUTH;
    $ccm_restricted = false;

    // Debug output
    if ($debug = ccm_grab_array_var($_SESSION, 'debug', false)) {
        ccm_array_dump($_REQUEST);
    }

    // Process input variables   
    $cmd = ccm_grab_request_var('cmd', "");
    $type = ccm_grab_request_var('type', "");
    $id = ccm_grab_request_var('id', false);
    $objectName = ccm_grab_request_var('objectName', '');
    $token = ccm_grab_request_var('token', grab_array_var($_SESSION, 'token', ''));

    // Do a quick authorization check and verify that the command was submitted from the
    // form, route to login page if it's an illegal operation
    if ($AUTH !== true) { $cmd = 'login'; }
    verify_token($cmd, $token);
    verify_type($cmd, $type); // XSS check

    // Check for permissions
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $ccm_restricted = true;
    }

    switch ($cmd)
    {
        case 'login':
            include_once(TPLDIR.'login.inc.php');
            $html = build_login_form();
            return $html;

        // Kill the session on log out
        case 'logout':
            $username = $_SESSION['ccm_username'];
            unset($_SESSION['ccm_username']);
            unset($_SESSION['ccm_login']);
            unset($_SESSION['token']);
            unset($_SESSION['loginMessage']);
            unset($_SESSION['startsite']);
            unset($_SESSION['keystring']);
            unset($_SESSION['strLoginMessage']);
            audit_log(AUDITLOGTYPE_SECURITY, $username._(" logged out of the Core Config Manager"));
            include_once(TPLDIR.'login.inc.php');
            $html = build_login_form();
            return $html;

        // Creating views (all tables in the CCM are views)
        case 'view':
            $args = route_view($type);
            $html = ccm_table($args);
            return $html;

        // Admin only functions
        case 'admin':
            $html = route_admin_view($type);
            return $html;

        // Redirect users from delete page or do the deletion
        case 'delete':
            if (ccm_has_access_for($type)) {
                if ($type == 'user') {
                    $html = route_admin_view($type);
                }
                else {
                    if (!empty($id)) {
                        include_once(INCDIR.'delete_object.inc.php');
                        $returnContent = delete_object($type, $id);
                        if ($returnContent[0] == 0) {
                            set_option("ccm_apply_config_needed", 1);

                            nagiosccm_set_table_modified($type);
                        }
                    }
                    $args = route_view($type);
                    $msgtype = FLASH_MSG_INFO;
                    if ($returnContent[0] == 1) {
                        $msgtype = FLASH_MSG_ERROR;
                    }
                    flash_message($returnContent[1], $msgtype);
                    $html = ccm_table($args);
                }
                return $html;
            } else {
                return default_page();
            }

        case 'delete_multi':
            if (ccm_has_access_for($type)) {
                include_once(INCDIR.'delete_object.inc.php');
                $returnContent = delete_multi($type);
                $args = route_view($type);
                if ($returnContent[0] == 0) {
                    set_option("ccm_apply_config_needed", 1);

                    nagiosccm_set_table_modified($type);
                }
                $msgtype = FLASH_MSG_INFO;
                if ($returnContent[0] == 1) {
                    $msgtype = FLASH_MSG_ERROR;
                }
                flash_message($returnContent[1], $msgtype);
                $html = ccm_table($args);
                return $html;
            } else {
                default_page();
            }

        case 'deactivate':
        case 'deactivate_multi':
        case 'activate':
        case 'activate_multi':
            if (ccm_has_access_for($type)) {
                include_once(INCDIR.'activate.inc.php');
                $returnContent = route_activate($cmd, $type, $id);
                $args = route_view($type);
                $msgtype = FLASH_MSG_INFO;
                if ($returnContent[0] == 1) {
                    $msgtype = FLASH_MSG_ERROR;
                }
                flash_message($returnContent[1], $msgtype);
                return ccm_table($args);
            } else {
                return default_page();
            }

        // Creates the form for modify or insert actions
        case 'modify':
        case 'insert':
            $FIELDS = array();
            if ($type == '') { return; }

            // Check for ability to add new hosts
            if ($cmd == 'insert') {

                // Check if we can configure more hosts/services
                if (!can_add_more_objects() && is_v2_license_type('cloud') && ($type == 'service' || $type == 'host')) {
                    $license = get_v2_license();
                    echo '<h1>'._(ucfirst($type) . ' Management').'</h1>';
                    ?>
                    <div class="alert alert-warning alert-max-checks">
                        <h2><?php echo _('Max Checks Reached'); ?></h2>
                        <p><?php echo _('You are using') . ' <b>' . get_active_checks_count() . '</b> ' . _('of') . ' <b>' . $license['restriction_max'] . '</b> ' . _('the') . ' ' . $license['restriction'] . ' ' . _('available for this instance.'); ?><br><?php echo _('To add more, either upgrade the instance via your'); ?> <a href="https://nagios.cloud/login" target="_new"><?php echo _('cloud account'); ?></a> <i class="fa fa-external-link"></i> <?php echo _('or remove old') . ' ' . $license['restriction'] . ' ' . _('you no longer need.'); ?></p>
                    </div>
                    <?php
                    break;
                }

                // Check if we can configure more hosts
                if (!can_add_more_objects() && is_v2_license_type('subscription') && $type == 'host') {
                    $license = get_v2_license();
                    echo '<h1>'._(ucfirst($type) . ' Management').'</h1>';
                    ?>
                    <div class="alert alert-warning alert-max-checks">
                        <h2><?php echo _('Max Checks Reached'); ?></h2>
                        <p><?php echo _('You are using') . ' <b>' . get_active_host_license_count() . '</b> ' . _('of') . ' <b>' . $license['restriction_max'] . '</b> ' . _('the') . ' ' . $license['restriction'] . ' ' . _('available for this instance.'); ?><br><?php echo _('To add more, upgrade your license or remove old') . ' ' . $license['restriction'] . ' ' . _('you no longer need.'); ?></p>
                    </div>
                    <?php
                    break;
                }

                // Check if license limit is exceeded
                if ($type == 'host' && is_license_exceeded()) {
                    $keyurl = get_base_url() . "admin/license.php";
                    echo '<h1>'._('License Limit Exceeded').'</h1>';
                    echo _("You have exceeded your")." <a href='" . $keyurl . "'>"._("license limits")."</a>.";
                    break;
                }

            }

            // Build form to display based on type and cmd (modify or insert)
            $Form = new Form($type, $cmd);
            $Form->prepare_data();
            $Form->build_form();
            break;

        // Copy a single object and return to the table
        case 'copy':

            if (!can_add_more_objects() && is_v2_license_type('cloud') && ($type == 'service' || $type == 'host')) {
                $license = get_v2_license();
                $msg = _('Cannot copy object') . '. ' . _('You are using') . ' <b>' . get_active_checks_count() . '</b> ' . _('of') . ' <b>' . $license['restriction_max'] . _('the checks available for this instance') . '. ' . _('To add more checks, either upgrade the instance via your') . '<a href="https://nagios.cloud/login" target="_new"> ' . _('cloud account') . '</a> <i class="fa fa-external-link"></i> ' . _('or remove old checks you no longer need.');
                flash_message($msg, FLASH_MSG_WARNING);
            } else if (!can_add_more_objects() && is_v2_license_type('subscription') && $type == 'host') {
                $license = get_v2_license();
                $msg = _('Cannot copy object') . '. ' . _('You are using') . ' <b>' . get_active_host_license_count() . '</b> ' . _('of') . ' <b>' . $license['restriction_max'] . _('the checks available for this instance') . '. ' . _('To add more checks, either upgrade the license or remove old hosts you no longer need.');
                flash_message($msg, FLASH_MSG_WARNING);
            } else if ($type == 'host' && is_license_exceeded()) {
                $msg = _('Cannot copy object, license limits have been exceeded.');
                flash_message($msg, FLASH_MSG_WARNING);
            } else {
                if (ccm_has_access_for($type)) {

                    // Services should use service_description not config_name
                    $desc = false;
                    if ($type == "service") {
                        $desc = true;
                    }
                    $keyField = $ccm->data->getKeyField($type, $desc);
                    $bool = $ccm->data->dataCopyEasy('tbl_'.$type, $keyField, $id);
                    $returnContent = array($bool, $ccm->data->strDBMessage);

                    $msgtype = FLASH_MSG_INFO;
                    if ($returnContent[0] == 1) {
                        $msgtype = FLASH_MSG_ERROR;
                    } else {

                        // Add last copy ID to temporary session-based viewable objects
                        if (($type == 'service' || $type == 'host') && get_user_meta(0, 'ccm_access') == 2) {
                            ccm_copy_user_permissions($type, $id, $ccm->data->lastCopyID, true);
                        }

                    }
                    flash_message($returnContent[1], $msgtype);

                }
            }

            // Display actual table page
            $args = route_view($type);
            return ccm_table($args);

        // Copy multiple objects and return to the table
        case 'copy_multi':

            if (!can_add_more_objects() && is_v2_license_type('cloud') && ($type == 'service' || $type == 'host')) {
                $license = get_v2_license();
                $msg = _('Cannot copy object') . '. ' . _('You are using') . ' <b>' . get_active_checks_count() . '</b> ' . _('of') . ' <b>' . $license['restriction_max'] . _('the checks available for this instance') . '. ' . _('To add more checks, either upgrade the instance via your') . '<a href="https://nagios.cloud/login" target="_new"> ' . _('cloud account') . '</a> <i class="fa fa-external-link"></i> ' . _('or remove old checks you no longer need.');
                flash_message($msg, FLASH_MSG_WARNING);
            } else if (!can_add_more_objects() && is_v2_license_type('subscription') && $type == 'host') {
                $license = get_v2_license();
                $msg = _('Cannot copy object') . '. ' . _('You are using') . ' <b>' . get_active_host_license_count() . '</b> ' . _('of') . ' <b>' . $license['restriction_max'] . _('the checks available for this instance') . '. ' . _('To add more checks, either upgrade the license or remove old hosts you no longer need.');
                flash_message($msg, FLASH_MSG_WARNING);
            } else if ($type == 'host' && is_license_exceeded()) {
                $msg = _('Cannot copy object, license limits have been exceeded.');
                flash_message($msg, FLASH_MSG_WARNING);
            } else {
                if (ccm_has_access_for($type)) {

                    $checks = ccm_grab_request_var('checked', array());
                    $copyCount = 0;
                    $failCount = 0;
                    $returnMessage = '';
                    $desc = false;

                    // Services should use service_description not config_name
                    if ($type == "service") {
                        $desc = true;
                    }

                    $keyField = $ccm->data->getKeyField($type, $desc);
                    foreach ($checks as $id) {
                        $bool = $ccm->data->dataCopyEasy('tbl_'.$type, $keyField, $id);
                        if ($bool == 0) {

                            // Add last copy ID to temporary session-based viewable objects
                            if (($type == 'service' || $type == 'host') && get_user_meta(0, 'ccm_access') == 2) {
                                ccm_copy_user_permissions($type, $id, $ccm->data->lastCopyID, true);
                            }

                            $copyCount++;
                        } else {
                            $failCount++;
                        }
                        $returnMessage .= $ccm->data->strDBMessage."<br />";
                    }

                    // Determine return status and message 
                    if ($copyCount == 0) {
                        $returnContent = array(1, "<strong>"._("No objects copied.")."</strong><br />".$returnMessage);
                    } else if ($failCount > 0) {
                        $returnContent = array(1, "$copyCount "._("objects copied").".<strong>$failCount "._("objects failed to copy.")."</strong><br />".$returnMessage);
                    } else {
                        $returnContent = array(0, "$copyCount "._("objects copied succesfully!")."<br />".$returnMessage);
                    }

                }
            }

            // Display actual table page
            $args = route_view($type);
            $html = ccm_table($args,$returnContent);
            return $html;

        case 'info':
            $table = 'tbl_'.$type;
            $ccm->data->fullTableRelations($table, $arrRelations);

            // Strip slashes since they were added so that it would pass through Js
            $objectName = stripslashes($objectName);

            if ($ccm_restricted) {
                $ccm_user_access = array();
                $ccm_user_access['host'] = ccm_get_user_object_ids('host');
                $ccm_user_access['service'] = ccm_get_user_object_ids('service');
            }

            // If service dependency then service only
            $so = false;
            if ($type == 'servicedependency') {
                $so = true;
            }

            $bool = $ccm->data->infoRelation($table, $id, "id", 1, $so, false);

            switch ($type) {
                case 'hostescalation':
                    $hr_type = _('Host Escalation');
                    break;
                case 'serviceescalation':
                    $hr_type = _('Service Escalation');
                    break;
                default:
                    $hr_type = $type;
                    break;
            }

            $deps = '';
            if ($ccm->data->hasDepRels) {
                $deps = '<span class="label label-danger label-10">'._('Dependent relationships denoted by').' <i class="fa fa-link"></i></span>';
            }

            if (!empty($myDataClass->arrInactive)) {
                $deps .= '<span class="label label-danger label-10">'._('Inactive objects denoted by red text and ').' <i class="fa fa-exclamation-circle"></i></span>';
            }

            $returnMessage = '
<div>
    <div class="close"><i class="fa fa-times"></i></div>
    <h1 style="padding: 0; margin: 0;">'.encode_form_val($objectName).'</h1>
    <p style="padding: 5px 0 10px 0; margin: 0;">'._('Object relationships').' '.$deps.'</p>
    <div id="rel-tabs" style="border: 0;">
        <ul>';

            foreach ($ccm->data->arrRR as $tab => $data) {

                $dep = false;
                foreach ($data as $d) {
                    if (is_array($d)) {
                        if (array_key_exists('dependent', $d)) {
                            $dep = true;
                            break;
                        }
                    }
                }

                $depicon = '';
                if ($dep) {
                    $depicon = '<i class="fa fa-link l rt-tt-bind" title="'._('Dependent relationships').'"></i> ';
                }

                $returnMessage .= '<li><a href="#tab-'.$tab.'">'.$depicon.ucfirst($tab).'s <span class="badge">'.count($data).'</span></a></li>';
            }

            $returnMessage .= '</ul>';

            foreach ($ccm->data->arrRR as $tab => $data) {

                $returnMessage .= '<div id="tab-'.$tab.'"><div class="bounding-box">
                    <table class="table table-condensed table-striped">';

                if ($tab == 'service' && $type != 'servicedependency') {
                    $returnMessage .= '<thead><tr>
                                <th>'._('Config Name').'</th>
                                <th>'._('Service Description').'</th>
                            </tr></thead>';
                }

                foreach ($data as $oid => $obj) {
                    $restricted_object = false;

                    if ($ccm_restricted && ($tab == 'service' || $tab == 'host')) {
                        if (!in_array($oid, $ccm_user_access[$tab])) {
                            $restricted_object = true;
                        }
                    }

                    $returnMessage .= '<tr>';
                    $dep = '';
                    $name = _('Unknown');
                    $inactive = false;

                    if (is_array($obj)) {
                        
                        if (!empty($obj['dependent'])) {
                            $dep = '<i title="'._('Dependent relationship').'" class="rt-tt-bind fa fa-13 fa-link fa-fw"></i> ';
                        }
                        
                        if (!$restricted_object) {
                            if (!empty($obj['cfg'])) { $returnMessage .= '<td>'.encode_form_val($obj['cfg']).'</td>'; }
                            if (array_key_exists('service', $obj)) {
                                $name = $obj['service'];
                            } else if (array_key_exists('name', $obj)) {
                                $name = $obj['name'];
                            }
                        } else {
                            if ($tab == 'service') {
                                $returnMessage .= '<td>'._('Unknown Config').'</td>';
                            }
                        }

                    } else {
                        if (!$restricted_object) {
                            $name = $obj;
                        }

                        if (is_array($myDataClass->arrInactive) && in_array($oid, $myDataClass->arrInactive)) {
                            $inactive = true;
                            $dep .= '<i title="'._('Inactive object').'" class="rt-tt-bind fa fa-13 fa-exclamation-circle fa-fw"></i> ';
                        }
                    }

                    if ($type == 'servicedependency' && $tab == 'service' || $restricted_object) {
                        $r = $name;
                    } else {
                        $class = '';
                        if ($inactive) { $class = 'urgent'; }
                        $r = $dep.'<a href="index.php?cmd=modify&type='.$tab.'&id='.$oid.'" class="'.$class.'">'.encode_form_val($name).'</a>';
                    }

                    $returnMessage .= '<td>'.$r.'</td></tr>';

                }

                $returnMessage .= '</table></div></div>';
            }

            $returnMessage .= '</div>
    <script type="text/javascript">
    $("#rel-tabs").tabs().show();
    $(".rt-tt-bind").tooltip();
    </script>
</div>
            ';

            return $returnMessage;

        // Submit results to the ccm table page
        case 'submit':
            route_submission($type);

            // If the user came from a page (which should be every time) then redirect them back
            // the the page that they were already on... this should make sense!
            $page = ccm_grab_request_var('page');

            header("Location: index.php?cmd=view&type=".$type."&page=".$page);
            exit;

        case 'apply':
            if (!ccm_has_access_for('configmanagement')) {
                return default_page();
            }
            require_once(INCDIR.'applyconfig.inc.php');
            $html = apply_config($type);
            return $html;

        case 'default':
        default:
            $html = default_page();
            return $html;
    }
}


/**
 * Determines and fetches information to be presented in in the main CCM display tables based on object $type
 *
 * @param string $type Nagios object type (host,service,contact, etc)
 * @return array $return_args['data'] array of filtered DB results
 *                           ['keyName'] string used for table header
 *                           ['keyDesc'] string used for table description 
 *                           ['config_names'] array of config_names for services table | empty array                     
 */
function route_view($type, $returnData=array())
{
    global $ccm;
    global $request;

    $txt_search = trim(ccm_grab_request_var('search', ''));
    $name_filter = ccm_grab_request_var('name_filter', '');
    $orderby = ccm_grab_request_var('orderby', ccm_grab_array_var($_SESSION, $type.'_orderby', ''));
    $sort = ccm_grab_request_var('sort', ccm_grab_array_var($_SESSION, $type.'_sort', 'ASC'));
    $sortlist = ccm_grab_request_var('sortlist', false);
    $session_search = trim(ccm_grab_array_var($_SESSION, $type.'_search', ''));
    $username = ccm_grab_array_var($_SESSION, 'username', '');
    $query = '';

    if ($orderby != '') {
        $_SESSION[$type.'_orderby'] = $orderby;
        $_SESSION[$type.'_sort']= $sort;
        $sortlist = 'true';
    }
    // Get relevant entries  
    list($table, $typeName, $typeDesc) = get_table_and_fields($type);

    // Required params for standard views
    if (isset($typeName, $typeDesc)) {

        // we need some additional data to determine if we have mrtg data for this service
        $mrtg_check = "";
        if ($type == "service") {
            $mrtg_check = ", check_command";
        }

        // Build SQL query based on filters and type
        $query_select = "SELECT id, {$typeName}, {$typeDesc}, last_modified, active{$mrtg_check}";
        $query = " FROM `{$table}` ";
        if ($type != "user") { // no config_id column in tbl_users tps#7540 -bh
            $query .= "WHERE `config_id`={$_SESSION['domain']} ";
        }

        // Search filters 
        $searchQuery = '';
        $config_names = array();

        // If clear has been pressed, clear search values
        if ($txt_search == 'false' || (isset($_POST['search']) && $_POST['search'] == '')) {
            $txt_search = '';
            $session_search = '';
            unset($_SESSION[$type.'_search']);
            unset($request['search']);
        }

        // If we are searching use text search first, else use what is in the session
        if ($txt_search != "" || $session_search != '') {
            $search = (($txt_search != '') ? $txt_search : $session_search); 
            $searchQuery = "AND (`$typeName` LIKE '%".$ccm->db->escape_string($search)."%' COLLATE utf8_general_ci OR `$typeDesc` LIKE '%".$ccm->db->escape_string($search)."%' COLLATE utf8_general_ci";
            if ($type == 'host') {
                $searchQuery.=" OR `display_name` LIKE '%".$ccm->db->escape_string($search)."%' COLLATE utf8_general_ci OR `address` LIKE '%".$ccm->db->escape_string($search)."%' COLLATE utf8_general_ci";
            }
            $searchQuery .=')';
        }

        // "config_name" filter only used on services page
        if ($name_filter != '' && $name_filter != 'null') {
            $_SESSION['name_filter'] = $name_filter;
        }

        if (isset($_SESSION['name_filter'])) {
            // Verify named filter exists and remove it if it doesn't
            $result = $ccm->db->query("SELECT DISTINCT config_name FROM tbl_service WHERE config_name = '".$ccm->db->escape_string($_SESSION['name_filter'])."';");
            if (empty($result)) {
                unset($_SESSION['name_filter']);
            }
        }

        // Clear name filter is empty has been selected OR if clear button has been pressed
        if ($name_filter == 'null' || $txt_search == 'false') {
            unset($_SESSION['name_filter']);
        }

        // Add to query if relevant
        if ($type == 'service' && isset($_SESSION['name_filter']) && $_SESSION['name_filter'] != '' && $_SESSION['name_filter'] != 'null') {
            $query .= "AND `config_name`='{$_SESSION['name_filter']}' ";
        }

        // If we are not an admin, we should find all objects we have this contact applied for
        if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {

            // Limit hosts and services
            if ($type == 'host' || $type == 'service') {
                $query .= " AND (";

                $type_id = OBJECTTYPE_HOST;
                if ($type == 'service') {
                    $type_id = OBJECTTYPE_SERVICE;
                }
                
                $query .= "id IN (SELECT object_id FROM tbl_permission WHERE type = ".$type_id." AND user_id = ".$_SESSION['user_id'].")";
                $query .= " OR id IN (SELECT object_id FROM tbl_permission_inactive WHERE type = ".$type_id." AND user_id = ".$_SESSION['user_id'].")";

                if ($type == 'service') {
                    $query .= " OR host_name = 2";
                }

                $query .= ")";

            }

        }

        if ($sortlist != 'false' && $sortlist != false) {
            $query .= "$searchQuery ORDER BY `".$ccm->db->escape_string($orderby)."` ";
            if ($orderby == "config_name" && $type == "service") {
                $query .= $sort.", `service_description` ASC";
            }
        } else {
            $query .= "$searchQuery ORDER BY `$typeName`";
            if ($typeName == "config_name" && $type == "service") {
                $query .= ", `service_description`";
            }
        }

        // Finally, sort by either ASC or DESC
        if ($orderby != "config_name") {
            $query .= " {$sort} ";
        }

        // Grab config names for services page if needed
        if ($typeName == 'config_name') {
            $config_names = $ccm->db->query("SELECT DISTINCT config_name FROM tbl_service;");
        }

        // Get total table data count
        $rs = $ccm->db->query("SELECT COUNT(*) AS total" . $query);
        $count = $rs[0]['total'];

        // Limit data
        $page = intval(ccm_grab_request_var('page', 1)); // Limit request var
        $limit = ccm_grab_request_var('pagelimit', null);
        if ($limit === null) {
            $limit = ccm_grab_array_var($_SESSION, 'limit', get_option('ccm_default_lines', 15));
        } else {
            $limit = intval($limit);
            $_SESSION['limit'] = $limit;
        }
        if ($limit > 0) {
            $start = (($page == 1) ? 0 : (($page-1) * $limit));
            $query .= " LIMIT $start, $limit";
        }

        // Return the query
        $data = $ccm->db->query($query_select . $query);

        $return_args = array('data'         => $data,
                             'count'        => $count,
                             'keyName'      => $typeName,
                             'keyDesc'      => $typeDesc,
                             'config_names' => $config_names);

        return $return_args;
    } else {
        // Can't route the request so we can just exit...
        exit();
    }
}


/**
 * Get all contactgroups for a contact including ones that are applied
 * via templates/inherited.
 *
 * @param   string  $contact_name   Contact name
 * @return  array                   Contact groups
 */
function get_contactgroups_for_contact($contact_name)
{
    global $ccm;

    $contactgroups = array();
    $contact_name = $ccm->db->escape_string($contact_name);

    $c = $ccm->db->query("SELECT * FROM tbl_contact WHERE contact_name = '$contact_name';");
    $c = $c[0];

    // Get contacgroups -> contact
    $cgs = $ccm->db->query("SELECT contactgroup_name FROM tbl_lnkContactgroupToContact AS CGtC
                          LEFT JOIN tbl_contactgroup AS C ON C.id = CGtC.idMaster
                          WHERE idSlave = {$c['id']};");
    foreach ($cgs as $cg) {
        $contactgroups[] = $cg['contactgroup_name'];
    }

    // If the contact is set to null contact groups, return nothing
    if ($c['contactgroups_tploptions'] == 1) {
        return $contactgroups;
    }

    // Check if we have all contact groups with a *
    if ($c['contactgroups'] == 2) {
        $cgs = $ccm->db->query("SELECT contactgroup_name FROM tbl_contactgroup;");
        foreach ($cgs as $cg) {
            $contactgroups[] = $cg['contactgroup_name'];
        }
        return $contactgroups;
    }

    // Find all applied contact groups
    $cgs = $ccm->db->query("SELECT contactgroup_name FROM tbl_contactgroup AS CG
                          LEFT JOIN tbl_lnkContactToContactgroup AS CtCG ON CtCG.idSlave = CG.id
                          WHERE CtCG.idMaster = {$c['id']};");
    foreach ($cgs as $cg) {
        $contactgroups[] = $cg['contactgroup_name'];
    }

    // If we have additive inheritence (+) then we need to continue down the template path
    // OR if we have standard inheritence and no contact groups defined
    if ($c['contactgroups_tploptions'] == 0 || ($c['contactgroups_tploptions'] == 2 && $c['contactgroups'] == 0)) {
        $cgs = get_contactgroups_from_templates($c['id']);
        $contactgroups = array_merge($contactgroups, $cgs);
    }

    return array_unique($contactgroups);
}


/**
 * Get the contact groups from the templates for a contact
 * - Recursive function looks through all templates until it gets the contact groups
 *   that are applied to the contact and stops
 *
 * @param   int     $id         Contact or Template ID
 * @param   int     $template   Template ID
 * @return  array               Contact groups
 */
function get_contactgroups_from_templates($id, $template=false)
{
    global $ccm;

    $table = "tbl_lnkContactToContacttemplate";
    $contactgroups = array();
    $id = intval($id);

    if ($template) {
        $table = "tbl_lnkContacttemplateToContacttemplate";
    }

    $tpls = $ccm->db->query("SELECT id, template_name, contactgroups, contactgroups_tploptions FROM tbl_contacttemplate AS CT
                           LEFT JOIN $table AS CTL ON CTL.idSlave = CT.id
                           WHERE CTL.idMaster = {$id} ORDER BY idSort DESC;");

    foreach ($tpls as $tpl) {

        // Check if contactgroups is set to null
        if ($tpl['contactgroups_tploptions'] == 1) {
            return $contactgroups;
        }

        // Check if we have all contact groups with a *
        if ($tpl['contactgroups'] == 2) {
            $cgs = $ccm->db->query("SELECT contactgroup_name FROM tbl_contactgroup;");
            foreach ($cgs as $cg) {
                $contactgroups[] = $cg['contactgroup_name'];
            }
            return $contactgroups;
        }

        // Get all contactgroups defined for this template
        $cgs = $ccm->db->query("SELECT contactgroup_name FROM tbl_contactgroup AS CG
                              LEFT JOIN tbl_lnkContacttemplateToContactgroup AS CTtCG ON CTtCG.idSlave = CG.id
                              WHERE CTtCG.idMaster = {$tpl['id']};");
        foreach ($cgs as $cg) {
            $contactgroups[] = $cg['contactgroup_name'];
        }

        // If we have additive inheritence (+) then we need to continue down the template path
        // OR if we have standard inheritence and no contact groups defined
        if ($tpl['contactgroups_tploptions'] == 0 || ($tpl['contactgroups_tploptions'] == 2 && $tpl['contactgroups'] == 0)) {
            $cgs = get_contactgroups_from_templates($tpl['id'], true);
            $contactgroups = array_merge($contactgroups, $cgs);
        }
    }

    return array_unique($contactgroups);
}


/**
 * Switch that handles submissions for adding and modifying config objects
 *
 * @param   string  $type           Nagios object type (host,service,contact, etc) 
 * @return  array   $returnData     (int exitCode, string exitMessage)
 */
function route_submission($type)
{
    $returnData = array(0, '');

    if (ccm_has_access_for($type)) {
        switch ($type)
        {
            case 'host':
            case 'service':
            case 'hosttemplate';
            case 'servicetemplate': 
                require_once('hostservice.inc.php');
                $returnData = process_ccm_submission();
                break;

            case 'hostgroup':
            case 'servicegroup':
            case 'contactgroup':
                require_once(INCDIR.'group.inc.php');
                $returnData = process_ccm_group();
                break;

            case 'timeperiod':
                require_once(INCDIR.'objects.inc.php');
                $returnData = process_timeperiod_submission();
                break;

            case 'command':
                require_once(INCDIR.'objects.inc.php');
                $returnData = process_command_submission();
                break;

            case 'contact':
            case 'contacttemplate':
                require_once(INCDIR.'contact.inc.php');
                $returnData = process_contact_submission();
                break;

            case 'serviceescalation':
            case 'hostescalation':
                require_once(INCDIR.'objects.inc.php');
                $returnData = process_escalation_submission();
                break;

            case 'servicedependency':
            case 'hostdependency':
                require_once(INCDIR.'objects.inc.php');
                $returnData = process_dependency_submission();
                break;

            default:
                $returnData = array(1, _("Missing arguments! No type specified for route."));
                $ccm->data->writeLog(_('Submitted modify or insert form without proper object type').' ('.$type.')');
                break;

        }
    } else {
        $returnData = array(1, ("<b>Object not saved.</b> You do not have access to manage the object type submitted."));
        $ccm->data->writeLog(_('Permission denied for object type').' ('.$type.')');
    }

    if ($returnData[0] == 1) {
        flash_message($returnData[1], FLASH_MSG_ERROR);
    } else {
        flash_message($returnData[1]);
    }
}


/**
 * Routes the views for admin pages such as the CCM Log, User Management, and  CCM Settings
 * @param   string  $type
 * @return  string
 */
function route_admin_view($type)
{
    global $ccm;
    global $request;

    require_once(INCDIR.'admin_views.inc.php');

    $txt_search = trim(ccm_grab_request_var('search', ''));
    $query = '';
    $session_search = trim(ccm_grab_array_var($_SESSION, $type.'_search', ''));
    $ccm_restricted = false;

    if (get_user_meta(0, 'ccm_access') == 2 && !is_admin()) {
        $ccm_restricted = true;
    }

    switch ($type)
    {
        case 'user':
            if ($ccm_restricted) {
                die(_("You do not have access to this page."));
            }
            $mode = ccm_grab_request_var('mode', false);
            $id = ccm_grab_request_var('id', false);
            $cmd = ccm_grab_request_var('cmd', "");
            $returnData = array(0, '');

            // Handle submissions on the Users page
            if (($mode == 'insert') || ($mode == 'modify') || ($cmd == 'delete')) {
                $returnData = process_user_submission();
            }

            // Query all users
            $query = "FROM `tbl_user` WHERE 1 ";
            list($table, $typeName, $typeDesc) = get_table_and_fields($type);

            // Required params for standard views 
            if (isset($typeName, $typeDesc)) {
                $config_names = array();
                // If clear has been pressed, clear search values
                if ($txt_search == 'false' || (isset($_POST['search']) && $_POST['search']=='') ) {
                    $txt_search='';
                    $session_search='';
                    unset($_SESSION[$type.'_search']);
                    unset($request['search']);
                }
                if ($txt_search != "" || $session_search != '') {
                    // Use text search first, else use what is in the session
                    $search = ($txt_search!='') ? $txt_search : $session_search;
                    $query .= "AND (`$typeName` LIKE '%".$search."%' OR `$typeDesc` LIKE '%".$search."%'";
                    $query .=')';
                }
            }

            // Get total table data count
            $rs = $ccm->db->query("SELECT COUNT(*) AS total " . $query);
            $count = $rs[0]['total'];

            // Limit data
            $page = ccm_grab_request_var('page', 1);// Limit request var
            $limit = ccm_grab_request_var('pagelimit', null);
            if ($limit === null) {
                $limit = ccm_grab_array_var($_SESSION, 'limit', get_option('ccm_default_lines', 15));
            } else {
                $limit = intval($limit);
                $_SESSION['limit'] = $limit;
            }
            if ($limit > 0) {
                $start = (($page == 1) ? 0 : (($page-1) * $limit));
                $query .= " LIMIT $start, $limit";
            }

            // Get the actual data for the table
            $query = "SELECT `id`,`username`,`alias`,`active` " . $query;

            $return_args = array('data' => $ccm->db->query($query),
                                 'keyName' => 'username',
                                 'keyDesc' => 'alias',
                                 'config_names' => array(),
                                 'count' => $count);
            return ccm_table($return_args, $returnData);

        case 'import':
            if (is_v2_license_type('cloud')) {
                return default_page();
            }
            return ccm_import_page();

        case 'corecfg':
            return ccm_corecfg();

        case 'log':
            if ($ccm_restricted) {
                die(_("You do not have access to this page."));
            }
            require_once(INCDIR.'ccm_log.inc.php');
            return ccm_log();

        case 'settings':
            if ($ccm_restricted) {
                die(_("You do not have access to this page."));
            }
            return ccm_settings();

        case 'static':
            if (is_v2_license_type('cloud')) {
                return default_page();
            }
            return ccm_static_editor();

        default:
            return default_page();
    }
}

// for backwards compatibility with xi < 5.3.0
if (!function_exists("sensitive_field_autocomplete")) {
    function sensitive_field_autocomplete() {
        return "";
    }
}