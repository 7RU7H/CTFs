<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: ccm_log.inc.php
//  Desc: Displays the CCM log table.
//


/**
 * Handles the displaying of the CCM log management pages. Only available to Nagios XI
 * admins if the automatic login is enabled. New XI integration also allows for logging
 * of Apply Configurations ran through the nagioscorecfg component.
 */
function ccm_log()
{
    global $ccm;

    // Limit request var
    $limit = ccm_grab_request_var('pagelimit', null);
    if ($limit === null) {
        $limit = ccm_grab_array_var($_SESSION, 'limit', get_option('ccm_default_lines', 15));
    } else {
        $limit = intval($limit);
        $_SESSION['limit'] = $limit;
    }
    
    // Request vars for page
    $cmd = ccm_grab_request_var('cmd', '');
    $id = ccm_grab_request_var('id', '');
    $search = trim(ccm_grab_request_var('search', ''));
    $page = ccm_grab_request_var('page', 1);
    $submitted = ccm_grab_request_var('submitted', false);
    $delete = ccm_grab_request_var('delete_single', false);
    $delete_multi = ccm_grab_request_var('delete_multi', false);
    
    // Query the log database
    $query = 'SELECT COUNT(*) from tbl_logbook';
    $resultCount = $ccm->db->count_results($query);
        
    $retClass = 'invisible';
    $returnMessage = '';
        
    if ($submitted) {
        $errors = 0;
        require_once(INCDIR.'delete_object.inc.php');

        // Delete or search for any requested items
        if (intval($delete) != 0) {
            $returnMessage .= $ccm->db->delete_entry('logbook', 'id', intval($delete));
            $retClass = (strpos($returnMessage, 'failed') ? 'error' : 'success');
        }

        if ($delete_multi == 'true') {
            $startcount = $resultCount;
            $checks = ccm_grab_request_var('checked', array());
            $selectedcount = count($checks);
            foreach ($checks as $c) {
                $ccm->db->delete_entry('logbook', 'id', $c);
            }

            $diff = $ccm->db->count_results($query); 
            
            // Verify correct number deleted 
            if ($diff == ($startcount - $selectedcount)) {
                $returnMessage = "$selectedcount "._("items deleted successfully")."!<br />";
                $retClass = 'success';
            } else {
                $returnMessage = ($startcount - $diff)._(" of ").$selectedcount._("selected items were deleted").".<br />";
                $retClass = 'error';
            }
        }
    }

    // Page limit... if no post was submitted, use session limit or update session
    // limit if post was submitted with a new page limit
    $limit = (($limit === 0) ? $resultCount : $limit);

    // Initializing variables
    $query = 'SELECT * FROM tbl_logbook';
    if ($search != '') {
        $query .= " WHERE (`user` LIKE '%".$ccm->db->escape_string($search)."%' OR `entry` LIKE '%".$ccm->db->escape_string($search)."%')";
    }
    $query .=" ORDER BY `time` DESC";  
    
    // Get the main result set
    $sqlData = $ccm->db->query($query);
    $query = 'SELECT count(*) FROM tbl_logbook';
    if ($search != '') {
        $query .= " WHERE (`user` LIKE '%".$ccm->db->escape_string($search)."%' OR `entry` LIKE '%".$ccm->db->escape_string($search)."%')"; 
    }
    $resultCount = $ccm->db->count_results($query);

    // Pagination
    $pagenumbers = '';
     
    // Do pagination if necessary 
    if ($resultCount > $limit) {
        $start = (($page == 1) ? 0 : (($page-1) * $limit));
        $end = ((($start + $limit) > $resultCount) ? $resultCount : ($start + $limit));
        $pagenumbers .= do_pagenumbers($page, $start, $limit, $resultCount, 'log');
    } else {   
        $start = 0; 
        $end = $resultCount;    
    }
    $start_display = $start+1;
    
    /////////////////BEGIN HTML BUILD //////////////////////
    $html = "
    <div id='contentWrapper'> 
        <h1 id='objectHeader' style='margin-bottom: 0; padding-bottom: 5px;'>"._('Audit Log')."</h1>
        <p>"._('Shows recent actions in the CCM. These are only kept here in the CCM for the max logbook age setting.')."<br>"._('Set the threshold of "Max Logbook Age" in the Database tab of the')." <a href='../../../admin/?xiwindow=performance.php' class='ccm-tt-bind' title='"._('Opens in main window')."' target='_parent'>"._('Performance Settings')."</a> <i class='fa fa-external-link'></i> "._('admin page').". "._('Full logs are kept in the')." <a href='../../../admin/?xiwindow=auditlog.php' class='ccm-tt-bind' title='"._('Opens in main window')."' target='_parent'>"._('Audit Log')."</a> <i class='fa fa-external-link'></i> "._('for future reference.')."</p>
        <div id='returnContent' class='{$retClass}'>".encode_form_valq($returnMessage)."
            <div id='closeReturn'>
                <a href='javascript:void(0)' id='closeReturnLink' title='Close'>"._("Close")."</a>
            </div>
        </div>
        <div id='ccmtablewrapper'> 
            <form id='frmDatalist' method='post' action='index.php?cmd=admin&type=log'>    
                <div id='tableTopper'>
                    <div id='resultCounter' style='margin: 0;' class='ccm-label'>"._("Displaying")." {$start_display}-{$end} "._("of")." {$resultCount} "._("results")."</div>
                    <div id='searchBox'>
                        <input type='text' class='form-control' name='search' id='search' value='".encode_form_valq($search)."' placeholder='"._('Search')."...'>
                        <button class='btn btn-sm btn-default' type='button' onclick='actionPic(\"admin\",\"\",\"\")' id='submitSearch'><i class='fa fa-search'></i></button>";

    if (!empty($search)) {
        $html .= " <button class='btn btn-sm btn-default' type='button' id='clear' name='clear'><i class='fa fa-times'></i></button>";
    }

    $html .= "
                    </div>  
                    
                    <div class='clear'></div>
                </div>
                <table class='table table-condensed table-outside-border table-striped table-hover tbl-ccm-log'>                                  
                    <thead>
                        <tr>
                            <th class='tbl-checkbox' style='width: 30px; text-align: center;'>
                                <input type='checkbox' class='checkbox ccm-tt-bind' onclick='javascript:checkAll()' title='"._("Toggle All Checkboxes")."'>
                            </th>
                            <th class='tbl-time' style='width: 160px;'>"._("Time")."</th>
                            <th class='tbl-ip' style='width: 124px;'>"._("IP Address")."</th>
                            <th class='name_left_align'>"._("User")."</th>
                            <th class='name_left_align'>"._("Entry")."</th>
                            <th class='tbl-id'>ID</th>
                            <th style='width: 30px;'></th>
                        </tr>
                    </thead>";
    
    //////////////////////////////////Table Rows Loop////////////////////////// 
    if ($resultCount != 0) {
        $rowCounter = 0;
        for ($i=$start; $i < $end; $i++)
        {
            $d = $sqlData[$i];
            //for table row class 
            $rowCounter % 2 == 1 ? $class = 'odd' : $class = 'even';
            $rowCounter++;      
            
            //begin heredoc string
            $entry = utf8_decode($d['entry']);
            $row="
                <tr>
                    <td class='tbl-checkbox'><input type='checkbox' class='checkbox' name='checked[]' value='{$d['id']}'  id='chbId{$rowCounter}' /></td>
                    <td class='tbl-time'>".encode_form_val($d['time'])."</td>
                    <td class='tbl-ip'>".encode_form_val($d['ipadress'])."</td>
                    <td class='name_left_align'>".encode_form_val($d['user'])."</td>
                    <td class='name_left_align'>".encode_form_val($entry)."</td>
                    <td class='tbl-id'>".encode_form_val($d['id'])."</td>
                    <td class='tbl-checkbox action-icons'>
                        <img src='images/cross.png' class='ccm-tt-bind' data-placement='left' title='"._('Delete')."' onclick=\"delete_single_log('".encode_form_val($d['id'])."')\">
                    </td>
                </tr>";
            $html .= $row;
        }
    }
    /////////////////////////End Table Rows Loop ////////////////
    //handle empty table sets
    if ($start==0 && $end ==0) $html.="<tr><td colspan='7'>"._("No results returned from logbook table")."</td></tr>";     
    //close out table after loop 
    $html .= "</table>";
    
    $tableControls = "
    <div id='tableControlsBottom'>
        <input type='hidden' name='submitted' value='1'>
        <input type='hidden' name='delete_single' id='delete_single'>
        <div id='withCheckedDiv'>
            <div class='input-group'>
                <span class='input-group-addon'>"._("With checked")."</span>
                <select name='delete_multi' class='form-control' id='delete_multi'>
                  <option value='false'>&nbsp;</option>
                  <option value='true'>"._("Delete")."</option> 
                </select>
                <span class='input-group-btn'>
                    <button type='submit' class='btn btn-sm btn-default'>"._("Go")."</button>
                </span>
            </div>
        </div>
        ".$pagenumbers."
        <div id='pageLimitDiv'>
            <div class='input-group'>
                <span class='input-group-addon'>"._("Results per page")."</span>
                <select name='pagelimit' class='form-control' id='pagelimit' onchange=\"actionPic('admin','','')\">
                    <option id='limit15' value='15'>15</option>
                    <option id='limit30' value='30'>30</option>
                    <option id='limit50' value='50'>50</option>
                    <option id='limit100' value='100'>100</option>
                    <option id='limit250' value='250'>250</option>
                    <option id='limitnone' value='none'>"._('All')."</option>
                </select>
                <script type='text/javascript'>
                    limit ='".$limit."'; 
                    $('#limit'+limit).attr('selected','selected');
                </script>
            </div>
        </div>
    </div>
    </form>";

    $html .= $tableControls;
    $html .= "  </div>
            </div>";

    echo $html;
}