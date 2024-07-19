<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: ccm_table.inc.php
//  Desc: Functions for building the table layouts in the CCM.
//

/**
 * Generates an html data table from an SQL associative array. This loads the object listings for
 * all config objects and handles copy, download, info, delete commands as well as the search bar,
 * pagination, etc.
 *
 * @param   array   $args               Storage array for all relevant information for 
 *                  $args['data']       - Associative array of all DB table data for the selected table 
 *                  $args['nameKey']    - Array index to identify item (also table header) 
 *                  $args['descKey']    - Descriptive array index to call (also table header)
 * @param   array   $returnContent      array(int, string) Code and status message to send back to the main table 
 * @global  array   $_REQUEST           Strings: limit, cmd, type, id, search - Used to determine table data 
 * @return  string  $html               Returns large html string of table data for nagios objects 
 */
function ccm_table($args, $returnContent=array(0, ''), $page=1)
{
    global $ccm;
    global $request;
    $ccm_restricted = false;
    $ccm_read_only = false;

    // Limit request var
    $limit = ccm_grab_request_var('pagelimit', null);
    if ($limit === null) {
        $limit = ccm_grab_array_var($_SESSION, 'limit', get_option('ccm_default_lines', 15));
    } else {
        $limit = intval($limit);
        $_SESSION['limit'] = $limit;
    }

    // Input request vars
    $type = ccm_grab_request_var('type', ''); 
    $id = intval(ccm_grab_request_var('id', 0)); 
    $search = trim(ccm_grab_request_var('search', '')); 
    $session_search = trim(ccm_grab_array_var($_SESSION, $type.'_search', ''));   
    $page = intval(ccm_grab_request_var('page', $page)); 
    $orderby = ccm_grab_request_var('orderby', ccm_grab_array_var($_SESSION, $type.'_orderby', '')); 
    $sort = ccm_grab_request_var('sort', ccm_grab_array_var($_SESSION, $type.'_sort', 'ASC'));
    if (!in_array($sort, array("ASC", "DESC"))) { $sort = ''; }
    $sortlist = ccm_grab_request_var('sortlist', false);

    // Check for permissions
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $ccm_restricted = true;
    }

    // Initializing variables 
    $sqlData = $args['data'];
    $returnCode = $returnContent[0];
    $returnMessage = $returnContent[1];
    $selectConfigNames = config_names_html($type); // Used for services page only 
    $sync_status = ''; // Either a single line at the page top, or a td value for hosts/services 
    $sync_header = ''; // Table header for sync status (host/service only), else empty string 
    
    // Process args and prepare variables for html string
    $th_name = ccm_get_full_title($type) . ' ' . _('Name');

    // we use config name everywhere else for this #11170 -bh
    if ($type == 'service')
        $th_name = _('Config Name');

    $th_desc = ($args['keyDesc'] != '' ? _(ucwords(str_replace('_', ' ', $args['keyDesc']))) : _("Description")); // Turn array key into description 

    // Get the order icon...
    if ($sort == "ASC") {
        $sort_icon = 'fa-long-arrow-down';
    } else {
        $sort_icon = 'fa-long-arrow-up';
    }
    $sort_icon_html = '<i class="fa '.$sort_icon.'"></i> ';
    $nosort_icon_html = '<i class="fa fa-arrows-v"></i> ';

    // Generate the no-sort icons for each of the table heads    
    $th_desc_sort = $nosort_icon_html;
    $th_name_sort = $nosort_icon_html;
    $th_active_sort = $nosort_icon_html;
    $th_id_sort = $nosort_icon_html;
    $th_sync_sort = $nosort_icon_html;

    // If we are ordering stuff let's append the orderby for certain descriptions
    if ($orderby == strtolower($args['keyDesc'])) {
        $th_desc_sort = $sort_icon_html;
    } else if ($orderby == strtolower($args['keyName'])) {
        $th_name_sort = $sort_icon_html;
    } else if ($orderby == "active") {
        $th_active_sort = $sort_icon_html;
    } else if ($orderby == "id") {
        $th_id_sort = $sort_icon_html;
    } else if ($orderby == "last_modified") {
        $th_sync_sort = $sort_icon_html;
    }

    // If we're here, our command is now view, otherwise we resubmit the last command that was given
    $cmd = (($type == 'log' || $type == 'user') ? 'admin' : 'view');
    $returnUrl = (($cmd != '' && $type != '') ? "index.php?cmd=".encode_form_valq($cmd)."&type=".encode_form_valq($type)."&page=$page" : '');

    // Create the apply config url by checking to see what environment we are in
    $apply_config_url = "/nagiosxi/includes/components/nagioscorecfg/applyconfig.php";
    
    // If the user is searching (or if we are using the stored search value...)
    if ($search != '') {
        $_SESSION[$type.'_search'] = $search;
    } else if ($session_search != '') {
        $search = $session_search; 
    }
    if ($search == 'false') {
        $search = '';
    }

    $cancel_search = '';
    if (!empty($search)) {
        $cancel_search = "<button class='btn btn-sm btn-primary' type='button' id='clear' name='clear'><i class='fa fa-times'></i></button>";
    }

    // Objects with single config file will display sync status at the top of the page
    $sync_table_status = '';
    if ($type == 'host' || $type == 'service') {
        $sync_header = "<th class='sortsync'>".$th_sync_sort." "._("Status")."</th>";
    }

    if (nagiosccm_get_table_modified($type)) {
        $sync_table_status = "<div id='singleSyncDiv'><span class='urgent'><i class='fa fa-exclamation-triangle'></i> "._("Changes detected! <strong>Apply Configuration</strong> for new changes to take effect.")."</span></div>";
    }
    
    // Return messages content? 
    if ($returnContent[1] == '') {
        $retClass = 'invisible';
    } else {
        $retClass = (($returnCode == 1) ? "error" : "success");
    } 
    
    // Pagination
    $resultCount = $args['count'];
    $pagenumbers = '';

    // Override limit if "Limit" is unlimited
    $olimit = $limit;
    $limit = (($limit === 0) ? $resultCount : $limit);

    // Do pagination if necessary or display with no pagination at all
    if ($resultCount > $limit) {
        // Figure results for current table
        $start = (($page == 1) ? 0 : (($page-1) * $limit));
        $end = ((($start + $limit) > $resultCount) ? $resultCount : ($start + $limit));
        
        // Figure results for pagenumbers, pass to function 
        $pagenumbers .= do_pagenumbers($page, $start, $limit, $resultCount, $type);
        $pagejumpto = do_pagejumpto($page, $start, $limit, $resultCount, $type);
    } else {
        $start = 0; 
        $end = $resultCount;
        $pagenumbers = '';
        $pagejumpto = '';
    }
    $start_display = $start+1;

    // If this is returning from a submitted change let's check the value again
    $ac_needed_js_inject = '';
    if (!empty($returnMessage)) {
        $ac_needed = get_option("ccm_apply_config_needed", 0);
        if ($ac_needed == 1) {
            $ac_needed_js_inject = '
            <script type="text/javascript">
                $(document).ready(function() {
                    window.parent.$("#ccm-apply-menu-link").html(\'<span class="tooltip-apply" data-placement="right" title="'._("There are modifications to objects that have not been applied yet. Apply configuration for new changes to take affect.").'"><i class="fa fa-fw fa-asterisk urgent"></i> '._("Apply Configuration").'</span>\');
                    window.parent.$(".tooltip-apply").tooltip({ template: "<div class=\"tooltip ccm-tooltip\" role=\"tooltip\"><div class=\"tooltip-arrow\"></div><div class=\"tooltip-inner\"></div></div>", container: "body", triger: "hover" });
                });
            </script>';
        }
    }

    /////////////////BEGIN HTML BUILD //////////////////////

    $add_new_button = "";
    if (ccm_has_access_for($type)) {
        $add_new_button = "<a class='btn btn-sm btn-primary vtop fl' style='margin-right: 15px;' href='index.php?cmd=insert&type=".encode_form_valq($type)."&returnUrl=".urlencode($returnUrl)."'><i class='fa fa-plus'></i> "._("Add New")."</a>";
    }

    $html = "
    {$ac_needed_js_inject}

    <div id='rel-popup'></div>

    <div id='contentWrapper'>
        <form id='frmDatalist' method='post' action='index.php'>

        <div id='objectHeader' style='margin: 10px 0 20px 0;'>
            <h1 class='fl' style='margin: 0; padding: 0; line-height: 29px;'>".ccm_get_full_title($type, true)."</h1>
            <div class='fr' style='margin-left: 20px;'>
                <div id='searchBox'>
                    <input type='text' name='search' id='search' style='vertical-align: top;' class='form-control' placeholder='"._('Search')."' value=\"".encode_form_valq($search)."\">
                    <button class='btn btn-sm btn-default' type='button' style='vertical-align: top; margin-right: 10px' onclick='actionPic(\"".encode_form_valq($cmd)."\",\"\",\"\")' id='submitSearch'><i class='fa fa-search'></i></button>
                    {$cancel_search}
                </div>
            </div>
            {$sync_table_status}
            <div class='clear'></div>
        </div>

        <div id='returnContent' class='{$retClass}'>{$returnMessage}
            <div id='closeReturn'>
                <a href='javascript:void(0)' id='closeReturnLink' title='Close'>"._("Close")."</a>
            </div>
        </div>

        <div id='ccmtablewrapper'>
            <div id='tableTopper'>
                ".$add_new_button."
                <div id='resultCounter' class='ccm-label fl'>"._("Displaying")." {$start_display}-{$end} "._("of")." {$resultCount} "._("results")."</div>
                {$selectConfigNames}
                {$pagenumbers}
                <div class='clear'></div>
            </div>
            <table class='table table-condensed table-bordered table-hover table-ccm table-striped'>
                <thead>
                    <tr>
                        <th class='tbl-checkbox'><input type='checkbox' onclick='javascript:checkAll()' title='"._("Toggle All Checkboxes")."'></th>
                        <th class='sortname name_left_align'>{$th_name_sort} {$th_name}</th>
                        <th class='sortdesc name_left_align'>{$th_desc_sort} {$th_desc}</th>
                        <th class='sortactive'>{$th_active_sort} "._("Active")."</th>
                        {$sync_header}
                        <th>"._("Actions")."</th>
                        <th class='sortid tbl-id'>{$th_id_sort} ID</th>
                    </tr>
                </thead>";

    //////////////////////////////////Table Rows Loop////////////////////////// 
    foreach ($sqlData as $data)
    {   
        $id = $data['id']; // Object ID 
        $name = $data[$args['keyName']]; // Name field
        if ($type == 'host' || $type == 'service') {
            $ccm->config->lastModifiedDir($name, $id, $type, $strTime, $strTimeFile, $intOlder);
            if ($strTimeFile == "undefined" && $data['active'] != 1) {
                $sync_status = '<td class="tbl-sync">-</td>';
            } else {
                $sync_status = (($intOlder == 0) ? '<td class="tbl-sync">'._('Applied').'</td>' : '<td class="tbl-sync"><span class="urgent">'._('Not Applied').'</span></td>');
            }
        } else {
            $sync_status = '';
        }

        // Set $desc if blank 
        $desc = (isset($data[$args['keyName']]) ? encode_form_val($data[$args['keyDesc']]) : "");
        $name = encode_form_val($name);

        // Special case for service escalations 
        process_desc_exceptions($desc, $type, $data, $args, $id);

        if (ccm_has_access_for($type)) {
            $active = is_active($data['active'], $id, $name);
        } else {
            if ($data['active']) {
                $active = _("Yes");
            } else {
                $active = '<strong class="urgent">'._("No").'</strong>';
            }
        }

        $cfg_name = 0;
        if ($type == 'host' || $type == 'service') {
            $cfg_name = $name;
        }

        $active_class = '';
        if ($data['active'] == 0) {
            $active_class = 'not-active';
        }

        $info_name = $name;
        if ($type == 'service') {
            $info_name = $desc;
        }

        // Generate the action icons HTML
        $icons = "";

        if (ccm_has_access_for($type)) {
            $icons .= "<a class='action ccm-tt-bind' href='?cmd=modify&type=".encode_form_valq($type)."&id={$id}&page={$page}&returnUrl=".urlencode($returnUrl)."' title='"._('Edit')."'><img src='images/editsettings.png' alt='Edit'></a>";
            $icons .= "<span class='action ccm-tt-bind' title='"._("Copy")."' onclick=\"actionPic('copy', '{$id}', '')\"><img src='images/page_white_copy.png' alt='Copy'></span>";
        }

        if (!$ccm_restricted) {
            $icons .= "<span class='action ccm-tt-bind' title='"._('View config')."' onclick=\"actionPic('download', '". addslashes($cfg_name)."', '{$_SESSION['domain']}')\"><img src='images/detail.png' alt='Download'></span>";
        }

        $icons .= "<span class='action ccm-tt-bind' title='"._('Relationships')."' onclick=\"show_relationship_popup('{$type}', '".addslashes($info_name)."', '{$id}', '".$_SESSION['token']."')\"><img src='images/information.png' alt='Info'></span>";

        if (ccm_has_access_for($type)) {
            $icons .= "<span class='action ccm-tt-bind' title='Delete' onclick=\"actionPic('delete', '{$id}', '".addslashes($name)."')\"><img src='images/cross.png' alt='Delete'></span>";
        }

        $row = "
            <tr>
                <td class='tbl-checkbox'><input type='checkbox' name='checked[]' value='{$id}'></td>
                <td class='name_left_align'><a href='?cmd=modify&type=".encode_form_valq($type)."&id={$id}&page={$page}&returnUrl=".urlencode($returnUrl)."' title='Edit'>".$name."</a></td>
                <td class='name_left_align'>{$desc}</td>
                <td class='tbl-active {$active_class}'>{$active}</td>
                {$sync_status}
                <td class='tbl-actions' align='left' style='text-align: left;'>
                    {$icons}
                </td>
                <td class='tbl-id'>{$id}</td>
            </tr>";

        $html .= $row;
    }

    /////////////////////////End Table Rows Loop ////////////////
    //handle empty table sets
    if ($start == 0 && $end == 0) {
        $html .= "<tr><td colspan='6'>"._("No results returned from")." ".encode_form_valq($type)." "._("table")."</td></tr>";  
    }

    // Close out table after loop 
    $html .= "</table>";

    $add_new_button = "";
    $withchecked = "";
    $extra_actions = "";
    if ($type != 'user') {
        $extra_actions = "<option value='delete_multi'>"._("Delete")."</option>
                    <option value='copy_multi'>"._("Copy")."</option>";
    }
    if (ccm_has_access_for($type)) {
        $add_new_button = "<a class='btn btn-sm btn-primary' style='margin-right: 15px;' href='index.php?cmd=insert&type=".encode_form_valq($type)."&returnUrl=".urlencode($returnUrl)."'><i class='fa fa-plus'></i> "._("Add New")."</a>";
        $withchecked = "<div id='withCheckedDiv'>
            <div class='input-group'>
                <span class='input-group-addon'>"._("With checked")."</span>
                <select name='selModify' id='selModify' class='form-control'>
                    <option value='none'> </option>
                    $extra_actions
                    <option value='activate_multi'>"._("Activate")."</option>
                    <option value='deactivate_multi'>"._("Deactivate")."</option>
                </select>
                <span class='input-group-btn'>
                    <button class='btn btn-sm btn-default' id='goButton' type='button'>"._("Go")."</button>
                </span>
            </div>
        </div>";
    }

    $tableControls = "
    <div id='tableControlsBottom'>
        <div id='addApplyButtons'>
            ".$add_new_button."
            <a class='btn btn-sm btn-default' href='{$apply_config_url}'><i class='fa fa-download'></i> "._("Apply Configuration")."</a>
            
            <!-- hidden nav arguments -->                     
            <input name='action' type='hidden' id='hiddenAction' value='false' />
            <input name='submitted' type='hidden' id='submitted' value='true' />
            <input name='cmd' id='cmd' type='hidden' value='".encode_form_valq($cmd)."' />
            <input name='type' id='type' type='hidden' value='".encode_form_valq($type)."' />
            <input name='id' id='id' type='hidden' value='{$id}' />
            <input name='objectName' id='objectName' type='hidden' value='' />";

    if ($type != 'user' && $cmd != 'admin') {
        $tableControls.="<input name='mode' type='hidden' id='mode' value='insert' />";
    }

    $tableControls.="<input name='returnUrl' id='returnUrl' type='hidden' value='index.php?cmd=".encode_form_valq($cmd)."&type=".encode_form_valq($type)."&page={$page}' />
              <input name='token' id='token' type='hidden' value='{$_SESSION['token']}' /> 
              <input name='orderby' id='orderby' type='hidden' value='".encode_form_valq($orderby)."' /> 
              <input name='sort' id='sort' type='hidden' value='{$sort}' />
              <input name='typeName' id='typeName' type='hidden' value='".encode_form_valq($args['keyName'])."' />
              <input name='typeDesc' id='typeDesc' type='hidden' value='".encode_form_valq($args['keyDesc'])."' />
              <input name='sortlist' id='sortlist' type='hidden' value='".encode_form_valq($sortlist)."' />
        </div>
        {$withchecked}
        {$pagenumbers}
        {$pagejumpto}
        <div id='pageLimitDiv'>
            <div class='input-group'>
                <span class='input-group-addon'>"._("Results per page")."</span>
                <select name='pagelimit' id='pagelimit' class='form-control' onchange=\"actionPic('".encode_form_valq($cmd)."','','')\">
                    <option id='limit15' value='15'>15</option>
                    <option id='limit30' value='30'>30</option>
                    <option id='limit50' value='50'>50</option>
                    <option id='limit100' value='100'>100</option>
                    <option id='limit250' value='250'>250</option>
                    <option id='limit500' value='500'>500</option>
                    <option id='limit1000' value='1000'>1000</option>
                    <option id='limit0' value='0'>"._('All')."</option> 
                </select>
                <script type='text/javascript'> 
                    limit ='{$olimit}';
                    $('#limit'+limit).attr('selected','selected'); 
                </script>
            </div>
        </div>
        <div class='clear'></div>
    </div>
    </form>"; 

    $html .= $tableControls;
    $html .= "</div>
            </div>";

    return $html;
}


/**
 * If the object type is service, creates html for "config_name" filter for services,
 * otherwise it returns an empty string.
 *
 * @param   string  $type   Nagios object type (host, service, contact, etc) 
 * @param   array   $names  Array of config_name tbl options, or empty array if type is not service
 * @return  string          HTML selection list or a blank string
 */ 
function config_names_html($type)
{
    global $ccm;
    if ($type != 'service') { return ''; }
    $filter = ccm_grab_array_var($_SESSION, 'name_filter', '');

    // Grab all config names
    $query = "SELECT DISTINCT(config_name) FROM tbl_service";
    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $query .= " WHERE id IN (SELECT object_id FROM tbl_permission WHERE type = ".OBJECTTYPE_SERVICE." AND user_id = ".$_SESSION['user_id'].")";
    }
    $query .= " ORDER BY config_name ASC";
    $cfnames = $ccm->db->query($query);

    $options = '';
    foreach ($cfnames as $n) {
        $selected = (($filter != '' && $filter == $n['config_name']) ? ' selected' : '');
        $options .= '<option value="'.encode_form_val($n['config_name']).'" '.$selected.'>'.encode_form_val($n['config_name']).'</option>';
    }

    $html = '<div id="config_filter_box">
                <div class="input-group">
                    <label class="input-group-addon">'._('Config Name').'</label>
                    <select name="name_filter" id="name_filter" class="form-control" onchange="actionPic(\'view\',\'\',\'\')">
                        <option value="null">&nbsp;</option>
                        '.$options.'
                    </select>
                </div>
            </div>';

    return $html;
}


/**
 * Creates page numbers based on how many results are being processed for the tables.
 *
 * @param   int     $page           The current page
 * @param   int     $start          Calculated starting number for results
 * @param   int     $limit          The session result limit 
 * @param   int     $resultCount    Total number of results for the object selected  
 * @param   string  $type           The nagios object type (host,service, contact, etc) 
 * @return  string                  HTML string of page numbers with link 
 */
function do_pagenumbers($page, $start, $limit, $resultCount, $type)
{
    $cmd = (($type == 'log' || $type == 'user') ? 'admin' : 'view');
    $resultCount = (($resultCount == 0) ? 1 : $resultCount);
    $pageCount = $resultCount / $limit;
    if (($resultCount % $limit) > 0) { $pageCount++; }
    $pageCount = floor($pageCount);

    // Sorting options 
    $sortlist = ccm_grab_request_var('sortlist', false);
    $sort = ccm_grab_request_var('sort', 'ASC');
    $orderby = ccm_grab_request_var('orderby', '');

    $link_base = "index.php?cmd={$cmd}&type={$type}";

    // If the list is being sorted
    if ($sortlist != false && $sortlist != 'false') {
        $link_base .= "&sortlist=true&orderby={$orderby}&sort={$sort}";
    }

    $pagenums = '<div class="paging-div">';
    
    // Go to "Previous Page" arrow 
    $back_arrow = '';
    $back_arrow_entities = '<i class="fa fa-chevron-left"></i>';
    if ($page > 1) {
        $link = $link_base.'&page='.($page-1);
        $back_arrow = '<a href="'.$link.'" title="'._("Previous page").'" class="pagenav-ends tt-delay-bind">'.$back_arrow_entities.'</a>';
    } 
    $pagenums .= $back_arrow;

    // New pagination setup (forward and backwards)
    $max_forward = 3;
    $max_backward = 3;
    if ($page > $max_backward+1) {
        $link = $link_base.'&page=1';
        $pagenums .= '<a href="'.$link.'" title="'._("First page").'" class="pagenumbers tt-delay-bind">1</a><div class="pagenumbers">..</div>';
    }

    // If they aren't on page 3, make how many pages they have to prepend
    if ($page <= $max_backward) {
        $max_backward = $page - 1;
    }

    // Build the page links BACKWARDS
    for ($i = $max_backward; $i >= 1; $i--) {
        $link = $link_base.'&page='.($page - $i);
        $pagenums .= '<a class="pagenumbers" href="'.$link.'">'.($page-$i).'</a>';
    }

    // Add the current page
    $lastpage = '';
    if ($page == $pageCount) { $lastpage = ' end'; }
    $pagenums .= '<div class="pagenumbers deselect'.$lastpage.'">'.$page.'</div>';

    // Make sure the max forward is within range
    if ($page + $max_forward >= $pageCount) {
        $max_forward = $pageCount - $page;
    }

    // Build the page links FORWARD
    for ($i = 1; $i <= $max_forward; $i++) {
        $link = $link_base.'&page='.($page + $i);
        $pagenums .= '<a class="pagenumbers" href="'.$link.'">'.($page+$i).'</a>';
    }

    // If there are more pages than what we have already printed, lets do a ..
    if (($page + $max_forward) < $pageCount) {
        $link = $link_base.'&page='.$pageCount;
        $pagenums .= '<div class="pagenumbers">..</div><a class="pagenumbers tt-delay-bind" title="'._("Last page").'" href="'.$link.'">'.$pageCount.'</a>';
    }

    // FORWARD arrow 
    $forward_arrow = '';
    $forward_arrow_entities = '<i class="fa fa-chevron-right"></i>';

    if ( ($start + $limit)  < $resultCount) {
        $link = $link_base.'&page='.($page+1);
        $forward_arrow = '<a href="'.$link.'" title="'._("Next page").'" class="pagenav-ends tt-delay-bind" data-placement="left">'.$forward_arrow_entities.'</a>';
    } 
    $pagenums .= $forward_arrow;
    $pagenums .= '</div>';

    return $pagenums;
}


/**
 * Creates a jump to page box that allows the user to jump to a page in the current
 * table view without having to click through the pages.
 * 
 * @param   int     $page           The current page
 * @param   int     $start          Calculated starting number for results
 * @param   int     $limit          The session result limit 
 * @param   int     $resultCount    Total number of results for the object selected 
 * @param   string  $type           The nagios object type (host, service, contact, etc) 
 * @return  string                  HTML for the jumpto box
 */
function do_pagejumpto($page, $start, $limit, $resultCount, $type)
{
    $cmd = (($type == 'log' || $type == 'user') ? 'admin' : 'view');

    // Main function variables
    $pageCount = $resultCount / $limit;
    if (($resultCount % $limit) > 0) { $pageCount++; }
    $pageCount = floor($pageCount);

    // Sorting options
    $sortlist = ccm_grab_request_var('sortlist', false);
    $sort = ccm_grab_request_var('sort', 'ASC');
    $orderby = ccm_grab_request_var('orderby', '');

    $link_base = "index.php?cmd={$cmd}&type={$type}";

    // If the list is being sorted
    if ($sortlist != false && $sortlist != 'false') {
        $link_base .= "&sortlist=true&orderby={$orderby}&sort={$sort}";
    }

    $jumpbox = "<div id='innerPageJumpDiv'>
                    <div class='input-group'>
                        <span class='input-group-addon'>"._("Jump to page")."</span>
                        <select id='jumpToPageBox' class='form-control'>";

                        // Generate the select box
                        for ($i = 1; $i <= $pageCount; $i++) {
                            $link = $link_base . "&page=".($i);
                            if ($i == $page) { $selected = ' selected'; } else { $selected = ''; }
                            $jumpbox .= "<option value='{$link}'{$selected}>{$i}</option>";
                        }

    $jumpbox .="</select>
            </div>
        </div>";

    return $jumpbox;
}


/**
 * Get a list of hosts or services that are in an escalation or dependency
 *
 * @param   string  $desc       Comma separated list of objects
 * @param   string  $type       Object type
 * @param   array   $data       SQL info array
 * @param   array   $args       SQL info array key
 * @param   int     $id         ID of the object to get list for
 */
function process_desc_exceptions(&$desc, $type, $data, $args, $id)
{
    global $ccm;
    $ccm_restricted = false;

    if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
        $ccm_restricted = true;
    }

    if ($type == 'serviceescalation' || $type == 'servicedependency') {
        if ($ccm_restricted) {
            $ccm_restricted_services = ccm_get_user_object_ids('service');
        }

        if ($data[$args['keyDesc']] == 1) {
            $table = (($type == 'servicedependency') ? "tbl_lnkServicedependencyToService_S" : "tbl_lnkServiceescalationToService");
            $opts = (($type ==  'servicedependency') ? "" : ", `exclude`");
            $query = "SELECT `id`, `service_description`{$opts} FROM `tbl_service` LEFT JOIN `{$table}` ON `id`=`idSlave` WHERE `idMaster`=".$id;
            $names = $ccm->db->query($query);
            $tmp = array();
            foreach ($names as $array) {
                if ($ccm_restricted && !in_array($array['id'], $ccm_restricted_services)) {
                    $tmp[] = _('Unknown');
                    continue;
                }
                if (array_key_exists('exclude', $array) && $array['exclude']) {
                    $tmp[] = '!'.$array['service_description'];
                } else {
                    $tmp[] = $array['service_description'];
                }
            }
            $desc = implode(', ', $tmp);
        } else {
            $desc = '*';
            if ($type == "serviceescalation") {
                $query = "SELECT `id`, `service_description` FROM `tbl_service` LEFT JOIN `tbl_lnkServiceescalationToService` ON `id`=`idSlave` WHERE `exclude` = 1 AND `idMaster`=".$id;
                $names = $ccm->db->query($query);
                $tmp = array();
                foreach ($names as $array) {
                    if ($ccm_restricted && !in_array($array['id'], $ccm_restricted_services)) {
                        $tmp[] = _('Unknown');
                        continue;
                    }
                    $tmp[] = '!'.$array['service_description'];
                }
                $desc .= ', ' . implode(', ', $tmp);
            }
        }
    }

    if ($type == 'hostescalation' || $type == 'hostdependency') {
        if ($ccm_restricted) {
            $ccm_restricted_hosts = ccm_get_user_object_ids('host');
        }

        if ($data[$args['keyDesc']] == 1) {       
            $table = (($type == 'hostdependency') ? "tbl_lnkHostdependencyToHost_H" : "tbl_lnkHostescalationToHost");
            $query = "SELECT `id`, `host_name` FROM `tbl_host` LEFT JOIN `{$table}` ON `id`=`idSlave` WHERE `idMaster`=".$id;
            $names = $ccm->db->query($query);
            $tmp = array();
            foreach ($names as $array) {
                if ($ccm_restricted && !in_array($array['id'], $ccm_restricted_hosts)) {
                    $tmp[] = _('Unknown');
                    continue;
                }
                $tmp[] = $array['host_name'];
            }
            $desc = implode(', ', $tmp);
        } else {
            $desc = '*';
        }
    }
}