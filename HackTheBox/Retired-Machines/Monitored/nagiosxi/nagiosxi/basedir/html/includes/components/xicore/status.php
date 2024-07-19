<?php
//
// XI Status Functions
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
include_once(dirname(__FILE__).'/../nagioscore/coreuiproxy.inc.php');


// Initialization stuff
pre_init();
init_session(true);

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();


route_request();


function route_request()
{
    $view = grab_request_var("show","");

    switch ($view)
    {
        case "process":
            show_monitoring_process();
            break;
        case "performance":
            show_monitoring_performance();
            break;
        case "comments":
            show_comments();
            break;
        case "services":
            show_services();
            break;
        case "hosts":
            show_hosts();
            break;
        case "hostgroups":
            show_hostgroups();
            break;
        case "servicegroups":
            show_servicegroups();
            break;
        case "servicedetail":
            show_service_detail();
            break;
        case "hostdetail":
            show_host_detail();
            break;
        case "tac":
            show_tac();
            break;
        case "outages":
            show_network_outages();
            break;
        case "map":
            show_status_map();
            break;
        default:
            show_services();
            break;
    }
}


function show_comments()
{
    global $request;

    licensed_feature_check();
    
    $search = trim(grab_request_var("search", ""));
    $args = array();
    
    if ($search) {
        $args["comment_data"] = "lk:" . $search . ";host_name=lk:" . $search .";service_description=lk:". $search .";author_name=lk:". $search;
    }
    
    do_page_start(array("page_title" => _('Acknowledgements and Comments')), true);
?>

<h1><?php echo _('Acknowledgements and Comments'); ?></h1>

<form method="get" action="<?php echo encode_form_val($_SERVER["REQUEST_URI"]); ?>">
    <div style="position: absolute; right: 60px; top: 20px;">
        <input type="hidden" name="show" value="comments">
        <input type="text" size="15" name="search" id="searchBox" value="<?php echo encode_form_val($search); ?>" placeholder="<?php echo _("Search..."); ?>" class="form-control">
    </div>
</form>

<div style="margin-top: 20px;">
<?php
    $dargs = array(
        DASHLET_ARGS => $args
    );
    display_dashlet("xicore_comments", "", $dargs, DASHLET_MODE_OUTBOARD);
?>
</div>

<?php
    do_page_end(true);
}


function show_services()
{
    licensed_feature_check(true, true);
    
    $show = grab_request_var("show", "services");
    $host = grab_request_var("host", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $hostattr = grab_request_var("hostattr", 0);
    $serviceattr = grab_request_var("serviceattr", 0);
    $hoststatustypes = grab_request_var("hoststatustypes", 0);
    $servicestatustypes = grab_request_var("servicestatustypes", 0);
    $hidedashlets = grab_request_var("hidedashlets", 0);

    $ps_service_status_hide_summary_dashlets = get_user_meta(0, "ps_service_status_hide_summary_dashlets", 0);
    if ($ps_service_status_hide_summary_dashlets) {
        $hidedashlets = 1;
    }
    
    $search = trim(grab_request_var("search", ""));

    // Fix for "all" options
    if ($hostgroup == "all")
        $hostgroup = "";
    if ($servicegroup == "all")
        $servicegroup = "";
    if ($host == "all")
        $host = "";

    // tps#7852 fix for search -bh
    if (empty($search) && !empty($host)) {
        $search = $host;
    }

    // If user was searching for a host, and no matching services are found, redirect them to the host status screen
    if (!empty($search)) {
        $backendargs = array();
        $backendargs["cmd"] = "getservicestatus";
        $backendargs["host_name"] = "lk:".$search.";name=lk:".$search.";host_address=lk:".$search.";host_display_name=lk:".$search.";display_name=lk:".$search;
        $backendargs["combinedhost"] = true;  // Need combined view for host search fields
        $backendargs["limitrecords"] = false;  // Don't limit records
        $backendargs["totals"] = 1; // Only get recordcount       

        // Get result from backend
        $xml = get_xml_service_status($backendargs);

        // How many total services do we have?
        $total_records = 0;
        if ($xml) {
            $total_records = intval($xml->recordcount);
        }

        // Redirect to host status screen
        if ($total_records == 0) {
            header("Location: status.php?show=hosts&search=".urlencode($search)."&noservices=1");
        }
    }

    $target_text = _("All services");
    if ($hostgroup != "") {
        $trans_hostgroup = _("Hostgroup");
        $target_text = "$trans_hostgroup: <b>".encode_form_val($hostgroup)."</b>";
    }
    if (!empty($servicegroup)) {
        $trans_servicegroup = _("Servicegroup");
        $target_text = "$trans_servicegroup: <b>".encode_form_val($servicegroup)."</b>";
    }
    if (!empty($host)) {
        $trans_host = _("Host");
        $target_text = "$trans_host: <b>".encode_form_val($host)."</b>";
    }
    
    do_page_start(array("page_title" => _("Service Status")), true);
?>

<script type="text/javascript">
$(document).ready(function() {

    // Once our setings have been saved, and we recieve the saved event, we need to do some changes
    // to the page depending on if we want to hide/show the dashlets
    $('#settings-dropdown').on("ps_saved", function(e, settings) { 
        $(settings).each(function(i, s) {
            if (s.keyname == "ps_service_status_hide_summary_dashlets") {
                if (s.keyvalue == 1) {
                    $('.xicore_host_status_summary_outboard').hide();
                    $('.xicore_service_status_summary_outboard').hide();
                } else {
                    if ($('.xicore_host_status_summary_outboard').is(":hidden")) {
                        $('.xicore_host_status_summary_outboard').show();
                    $('.xicore_service_status_summary_outboard').show();
                    } else {
                        window.location.reload();
                    }
                }
            }
        });
    });

});
</script>

<div class="fl">
    <div>
        <h1 style="display: inline-block;"><?php echo _("Service Status");?></h1>
        <a class="tt-bind settings-dropdown" id="open-settings" data-placement="right" title="<?php echo _('Edit page settings'); ?>" style="display: inline-block; margin-left: 5px; line-height: 43px; height: 43px; vertical-align: top;"><img src="<?php echo theme_image('cog.png'); ?>"></a>
        <div id="settings-dropdown">
            <div class="content">
                <div class="checkbox-setting">
                    <div class="checkbox">
                        <label><input type="checkbox" name="ps_service_status_hide_summary_dashlets" style="cursor: pointer;" <?php echo is_checked($ps_service_status_hide_summary_dashlets, 1); ?> value="1"> <?php echo _("Hide status summary dashlets"); ?></label>
                    </div>
                </div>
                <div style="margin-top: 10px">
                    <button type="button" class="btn btn-update-settings btn-xs btn-primary fl"><i class="fa fa-check fa-l"></i> <?php echo _('Update'); ?></button>
                    <button type="button" class="btn btn-close-settings btn-xs btn-default fr"><i class="fa fa-times"></i> <?php echo _('Close'); ?></button>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="servicestatustargettext"><?php echo $target_text;?></div>
</div>

<?php if (!$hidedashlets) { ?>
<div class="fr" style="margin: 10px 0 20px 0;">
    <div class="fl" style="margin-right: 20px;">
        <?php
        $dargs = array(
            DASHLET_ARGS => array(
                "host" => $host,
                "hostgroup" => $hostgroup,
                "servicegroup" => $servicegroup,
                "hostattr" => $hostattr,
                "serviceattr" => $serviceattr,
                "hoststatustypes" => $hoststatustypes,
                "servicestatustypes" => $servicestatustypes,
                "show" => $show
            )
        );
        display_dashlet("xicore_host_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
        ?>
    </div>
    <div class="fl">
        <?php display_dashlet("xicore_service_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD); ?>
    </div>
</div>
<?php } ?>

<br clear="all">

<?php draw_servicestatus_table(); ?>

<?php
    do_page_end(true);
}


/**
 * Show host status table (with filters)
 *
 * @param   bool    $error
 * @param   string  $msg
 */
function show_hosts($error = false, $msg = "")
{
    licensed_feature_check(true,true);

    $show = grab_request_var("show", "hosts");
    $host = grab_request_var("host", "");
    $hostgroup = grab_request_var("hostgroup", "");
    $servicegroup = grab_request_var("servicegroup", "");
    $hostattr = grab_request_var("hostattr", 0);
    $serviceattr = grab_request_var("serviceattr", 0);
    $hoststatustypes = grab_request_var("hoststatustypes", 0);
    $servicestatustypes = grab_request_var("servicestatustypes", 0);
    $noservices = grab_request_var("noservices", 0);
    $hidedashlets = grab_request_var("hidedashlets", 0);

    $ps_host_status_hide_summary_dashlets = get_user_meta(0, "ps_host_status_hide_summary_dashlets", 0);
    if ($ps_host_status_hide_summary_dashlets) {
        $hidedashlets = 1;
    }

    // No services found during search - user was redirected
    if ($noservices == 1) {
        $error = false;
        $msg = _("No matching services found - showing matching hosts instead.");
    }

    if ($hostgroup == "all")
        $hostgroup = "";
    if ($servicegroup == "all")
        $servicegroup = "";
    if ($host == "all")
        $host = "";

    $target_text = _("All hosts");
    if (!empty($hostgroup)) {
        $target_text = _('Hostgroup').": <b>".encode_form_val($hostgroup)."</b>";
    }
    if (!empty($servicegroup)) {
        $target_text = _('Servicegroup').": <b>".encode_form_val($servicegroup)."</b>";
    }
    if (!empty($host)) {
        $target_text = _('Host').": <b>".encode_form_val($host)."</b>";
    }
    
    do_page_start(array("page_title" => _("Host Status")), true);
?>

<script type="text/javascript">
$(document).ready(function() {

    // Once our setings have been saved, and we recieve the saved event, we need to do some changes
    // to the page depending on if we want to hide/show the dashlets
    $('#settings-dropdown').on("ps_saved", function(e, settings) { 
        $(settings).each(function(i, s) {
            if (s.keyname == "ps_host_status_hide_summary_dashlets") {
                if (s.keyvalue == 1) {
                    $('.xicore_host_status_summary_outboard').hide();
                    $('.xicore_service_status_summary_outboard').hide();
                } else {
                    if ($('.xicore_host_status_summary_outboard').is(":hidden")) {
                        $('.xicore_host_status_summary_outboard').show();
                    $('.xicore_service_status_summary_outboard').show();
                    } else {
                        window.location.reload();
                    }
                }
            }
        });
    });

});
</script>

<div class="fl">
    <div>
        <h1 style="display: inline-block;"><?php echo _("Host Status");?></h1>
        <a class="tt-bind settings-dropdown" id="open-settings" data-placement="right" title="<?php echo _('Edit page settings'); ?>"><img src="<?php echo theme_image('cog.png'); ?>"></a>
        <div id="settings-dropdown">
            <div class="content">
                <div class="checkbox-setting">
                    <div class="checkbox">
                        <label><input type="checkbox" name="ps_host_status_hide_summary_dashlets" style="cursor: pointer;" <?php echo is_checked($ps_host_status_hide_summary_dashlets, 1); ?> value="1"> <?php echo _("Hide status summary dashlets"); ?></label>
                    </div>
                </div>
                <div style="margin-top: 10px">
                    <button type="button" class="btn btn-update-settings btn-xs btn-primary fl"><i class="fa fa-check fa-l"></i> <?php echo _('Update'); ?></button>
                    <button type="button" class="btn btn-close-settings btn-xs btn-default fr"><i class="fa fa-times"></i> <?php echo _('Close'); ?></button>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="hoststatustargettext"><?php echo $target_text;?></div>
</div>

<?php if (!$hidedashlets) { ?>
<div class="fr" style="margin: 10px 0 20px 0;;">
    <div class="fl" style="margin-right: 25px;">
        <?php
        $dargs = array(
            DASHLET_ARGS => array(
                "host" => $host,
                "hostgroup" => $hostgroup,
                "servicegroup" => $servicegroup,
                "hostattr" => $hostattr,
                "serviceattr" => $serviceattr,
                "hoststatustypes" => $hoststatustypes,
                "servicestatustypes" => $servicestatustypes,
                "show" => $show
            )
        );
        display_dashlet("xicore_host_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
        ?>
    </div>
    <div class="fl">
        <?php display_dashlet("xicore_service_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD); ?>
    </div>
</div>
<?php } ?>

<?php
if (is_array($msg) || !empty($msg)) {
    echo "<br clear='all'>";
    display_message($error, false, $msg);
}
?>

<br clear="all">

<div style="clear: both;">
<?php draw_hoststatus_table(); ?>
</div>

<?php
    do_page_end(true);
}


function show_hostgroups()
{
    licensed_feature_check(true, true);

    $hostgroup = grab_request_var("hostgroup", "all");
    $style = grab_request_var("style", "overview");

    // Performance optimization
    $opt = get_option("use_unified_hostgroup_screens");
    if ($opt == 1) {
        header("Location: ".get_base_url()."includes/components/nagioscore/ui/status.php?hostgroup=".urlencode($hostgroup)."&style=".$style);
    }

    do_page_start(array("page_title" => _("Host Group Status")), true);

    $target_text = "";
    switch ($style) {
        case "summary":
            $target_text = _("Summary View");
            break;
        case "overview":
            $target_text = _("Overview");
            break;
        case "grid":
            $target_text = _("Grid View");
            break;
        default:
            break;
    }

    // Get timezone datepicker format
    if (isset($_SESSION['date_format'])) {
        $dformat = $_SESSION['date_format'];
    } else {
        if (is_null($dformat = get_user_meta(0, 'date_format'))) {
            $dformat = get_option('default_date_format');
        }
    }
    $dfs = get_date_formats();

    $js_date = 'mm/dd/yy';
    if ($dformat == DF_ISO8601) {
        $js_date = 'yy-mm-dd';
    } else if ($dformat == DF_US) {
        $js_date = 'mm/dd/yy';
    } else if ($dformat == DF_EURO) {
        $js_date = 'dd/mm/yy';
    }

?>

<script type="text/javascript">
$(document).ready(function() {

    $(window).resize(function() {
        $('.xi-modal').position({ my: "center", at: "center", of: window });
    });

    $('#whiteout').click(function() {
        if ($('#hostgroup-commands').is(":visible")) {
            clear_whiteout();
            $('#hostgroup-commands').hide();
        }
    });

    $('.cancel').click(function() {
        $(this).parent('div').hide();
        $(this).parent('div').find('.ahas').prop('checked', false);
        clear_whiteout();
    });

    $('#fixed').change(function() {
        if ($(this).val() == 0) {
            $('#flexible-box').show();
            $(this).parents('.xi-modal').position({ my: "center", at: "center", of: window });
        } else {
            $('#flexible-box').hide();
            $(this).parents('.xi-modal').position({ my: "center", at: "center", of: window });
        }
    });

    $('.datetimepicker').datetimepicker({
        showOn: 'button',
        buttonImage: '../../../images/datetimepicker.png',
        buttonImageOnly: true,
        dateFormat: '<?php echo $js_date; ?>',
        timeFormat: 'HH:mm:ss',
        showHour: true,
        showMinute: true,
        showSecond: true
    });

    // Check Date range accuracy
    $('#startdateBox').change(function() {
        var start_input = $('#startdateBox');
        var end_input = $('#enddateBox');
        var startdate_tp = start_input.datetimepicker('getDate');
        var enddate_tp = end_input.datetimepicker('getDate');

        dstartdate = Date.parse(startdate_tp)/1000;
        denddate = Date.parse(enddate_tp)/1000;

        if (dstartdate > denddate) {
            var new_ntp = startdate_tp;
            new_ntp.setHours(startdate_tp.getHours() + 2);
            end_input.datetimepicker('setDate', new_ntp);
        }
    });

    $('.hg-dashlets').on('click', '.group-dt-popup', function() {

        var name = $(this).data('name');
        $('#hostgroup-commands .hg-name').html(name);

        $('#hostgroup-commands').show();
        whiteout();
        $('#hostgroup-commands').position({ my: "center", at: "center", of: window });

    });

    // Do commands

    $('.cmdlink').click(function() {

        var cmd = $(this).data('cmd-type');
        var hostgroup = $('.hg-name').text();

        $('#hostgroup-commands').hide();
        $('.cmd-info').hide();

        if (cmd == 84 || cmd == 85) {
            $('.'+cmd).show();
            $("#schedule-downtime .cmd-type").val(cmd);
            $('#schedule-downtime .hostgroup_name').val(hostgroup);
            $('#schedule-downtime').show();
            $('#schedule-downtime').position({ my: "center", at: "center", of: window });
        } else if (cmd == 63 || cmd == 64 || cmd == 65 || cmd == 66) {
            $('.'+cmd).show();
            $("#set-notifications .cmd-type").val(cmd);
            $('#set-notifications .hostgroup_name').val(hostgroup);
            $('#set-notifications').show();
            $('#set-notifications').position({ my: "center", at: "center", of: window });
        } else if (cmd == 67 || cmd == 68) {
            $('.'+cmd).show();
            $("#set-checks .cmd-type").val(cmd);
            $('#set-checks .hostgroup_name').val(hostgroup);
            $('#set-checks').show();
            $('#set-checks').position({ my: "center", at: "center", of: window });
        }

    });

    $('.submit-schedule-downtime').click(function() {

        var error = 0;
        $('#schedule-downtime .req').each(function(k, i) {
            if ($(i).val().trim() == '') {
                error++;
            }
        });

        if (error) {
            alert("<?php echo encode_form_val(_('Please fill out all required fields.')); ?>"); 
            return;
        }

        var cmd = $('#schedule-downtime .cmd-type').val();
        var args = { cmd_typ: cmd,
                     cmd_mod: 2,
                     nsp: '<?php echo get_nagios_session_protector_id(); ?>',
                     hostgroup: $('#schedule-downtime .hostgroup_name').val(),
                     com_author: $('#schedule-downtime .com_author').val(),
                     com_data: $('#schedule-downtime .com_data').val(),
                     start_time: $('#startdateBox').val(),
                     end_time: $('#enddateBox').val(),
                     fixed: $('#fixed').val(),
                     hours: parseInt($('#flexible-hours').val()),
                     minutes: parseInt($('#flexible-minutes').val())
                    }

        // Special thing for services (schedule downtime for all hosts also)
        if (cmd == 85) {
            if ($('#schedule-downtime .ahas').is(':checked')) {
                args['ahas'] = "on";
            }
        }

        // Send the cmd & data to Core
        $.post('<?php echo get_base_url(); ?>includes/components/nagioscore/ui/cmd.php', args, function(d) {
            $('#schedule-downtime').hide();
            $('#schedule-downtime .com_data').val('');
            $('#schedule-downtime .hostgroup_name').val('');

            var content = "<div id='popup_header'><b>"+_('Command Submitted')+"</b></div><div style='margin-top: 10px;' id='popup_data'><ul class='ajaxcommandresult'><li class='commandresultok'><?php echo _('Command submitted successfully.'); ?></li></ul></div>";
            set_child_popup_content(content);
            display_child_popup();
            fade_child_popup("green");
            clear_whiteout();
        });

    });

    $('.submit-set-notifications').click(function() {

        var cmd = $('#set-notifications .cmd-type').val();
        var args = { cmd_typ: cmd,
                     cmd_mod: 2,
                     nsp: '<?php echo get_nagios_session_protector_id(); ?>',
                     hostgroup: $('#set-notifications .hostgroup_name').val()
                    }

        // Special thing for services (set notifications for all hosts also)
        if (cmd == 63 || cmd == 64) {
            if ($('#set-notifications .ahas').is(':checked')) {
                args['ahas'] = "on";
            }
        }

        // Send the cmd & data to Core
        $.post('<?php echo get_base_url(); ?>includes/components/nagioscore/ui/cmd.php', args, function(d) {
            $('#set-notifications').hide();
            $('#set-notifications .hostgroup_name').val('');
            $('#set-notifications .ahas').prop('checked', false);

            var content = "<div id='popup_header'><b>"+_('Command Submitted')+"</b></div><div style='margin-top: 10px;' id='popup_data'><ul class='ajaxcommandresult'><li class='commandresultok'><?php echo _('Command submitted successfully.'); ?></li></ul></div>";
            set_child_popup_content(content);
            display_child_popup();
            fade_child_popup("green");
            clear_whiteout();
        });

    });

    $('.submit-set-checks').click(function() {

        var cmd = $('#set-checks .cmd-type').val();
        var args = { cmd_typ: cmd,
                     cmd_mod: 2,
                     nsp: '<?php echo get_nagios_session_protector_id(); ?>',
                     hostgroup: $('#set-checks .hostgroup_name').val()
                    }

        // Set checks either enabled or disabled
        if ($('#set-checks .ahas').is(':checked')) {
            args['ahas'] = "on";
        }

        // Send the cmd & data to Core
        $.post('<?php echo get_base_url(); ?>includes/components/nagioscore/ui/cmd.php', args, function(d) {
            $('#set-checks').hide();
            $('#set-checks .hostgroup_name').val('');
            $('#set-checks .ahas').prop('checked', false);

            var content = "<div id='popup_header'><b>"+_('Command Submitted')+"</b></div><div style='margin-top: 10px;' id='popup_data'><ul class='ajaxcommandresult'><li class='commandresultok'><?php echo _('Command submitted successfully.'); ?></li></ul></div>";
            set_child_popup_content(content);
            display_child_popup();
            fade_child_popup("green");
            clear_whiteout();
        });

    });

});
</script>

<div class="xi-modal hide" id="hostgroup-commands" style="max-width: 400px;">
    <h2><?php echo _('Hostgroup Commands'); ?></h2>
    <div style="margin: 0 0 10px 0; font-size: 13px;"><b><span class="hg-name"></span></b></div>
    <div>
        <p><?php echo _('Submit a bulk command for all the hosts or services in the selected hostgroup.'); ?></p>
        <div style="margin: 10px 0;">
            <div><a class="cmdlink dt" data-cmd-type="84"><img src="<?php echo theme_image('time_add.png'); ?>"><?php echo _('Schedule downtime for all hosts'); ?></a></div>
            <div><a class="cmdlink dt" data-cmd-type="85"><img src="<?php echo theme_image('time_add.png'); ?>"><?php echo _('Schedule downtime for all services'); ?></a></div>
        </div>
        <div style="margin-bottom: 10px;">
            <div><a class="cmdlink" data-cmd-type="65"><img src="<?php echo theme_image('notifications2.png'); ?>"><?php echo _('Enable notifications for all hosts'); ?></a></div>
            <div><a class="cmdlink" data-cmd-type="66"><img src="<?php echo theme_image('nonotifications.png'); ?>"><?php echo _('Disable notifications for all hosts'); ?></a></div>
        </div>
        <div style="margin-bottom: 10px;">
            <div><a class="cmdlink" data-cmd-type="63"><img src="<?php echo theme_image('notifications2.png'); ?>"><?php echo _('Enable notifications for all services'); ?></a></div>
            <div><a class="cmdlink" data-cmd-type="64"><img src="<?php echo theme_image('nonotifications.png'); ?>"><?php echo _('Disable notifications for all services '); ?></a></div>
        </div>
        <div><a class="cmdlink" data-cmd-type="67"><img src="<?php echo theme_image('enable_small.gif'); ?>"><?php echo _('Enable active checks of all services'); ?></a></div>
        <div><a class="cmdlink" data-cmd-type="68"><img src="<?php echo theme_image('cross.png'); ?>"><?php echo _('Disable active checks of all services'); ?></a></div>
    </div>
</div>

<div class="xi-modal hide" id="schedule-downtime">
    <h2>
        <span id="sd-title"><?php echo _('Schedule Downtime'); ?></span>
        <i class="fa fa-question-circle fa-14 pop" style="margin-left: 5px;" data-title="<?php echo _('Schedule Downtime'); ?>" data-content="<?php echo _('This command is used to schedule downtime for objects. During the specified downtime, Nagios will not send notifications out about the object. When the scheduled downtime expires, Nagios will send out notifications for this object as it normally would. Scheduled downtimes are preserved across program shutdowns and restarts. Both the start and end times should be specified in the following format:'). ' ' . $dfs[$dformat] . '. ' ._('If you select the fixed option, the downtime will be in effect between the start and end times you specify. If you do not select the fixed option, Nagios will treat this as flexible downtime. Flexible downtime starts when the object goes into a non-OK/UP state (sometime between the start and end times you specified) and lasts as long as the duration of time you enter. The duration fields do not apply for fixed downtime.'); ?>"></i>
    </h2>
    <p class='cmd-info 84 hide'><?php echo _('Schedule downtime for all hosts in the selected hostgroup.'); ?></p>
    <p class='cmd-info 85 hide'><?php echo _('Schedule downtime for all services in the selected hostgroup.'); ?></p>
    <input type="hidden" class="cmd-type" value="">
    <table class="table table-condensed table-no-border table-auto-width">
        <tr>
            <td><?php echo _('Hostgroup Name'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control hostgroup_name req" readonly value=""></td>
        </tr>
        <tr>
            <td><?php echo _('Author'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control com_author req" readonly value="<?php echo get_user_attr(0, 'name'); ?>"></td>
        </tr>
        <tr>
            <td><?php echo _('Comment'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control com_data req" style="width: 360px;"></td>
        </tr>
        <tr>
            <td><?php echo _('Type'); ?></td>
            <td>
                <select id="fixed" class="form-control">
                    <option value="1"><?php echo _('Fixed'); ?></option>
                    <option value="0"><?php echo _('Flexible'); ?></option>
                </select>
            </td>
        </tr>
        <tr id="flexible-box" class="hide">
            <td><?php echo _('Duration'); ?></td>
            <td>
                <input type="text" class="form-control" style="width: 40px;" id="flexible-hours" value="2"> Hours
                <input type="text" class="form-control" style="width: 40px; margin-left: 5px;" id="flexible-minutes" value="0"> Minutes
            </td>
        </tr>
        <tr>
            <td><?php echo _("Start Time"); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <input class="form-control datetimepicker req" type="text" id='startdateBox' name="startdate" value="<?php echo get_datetime_string(time()); ?>" size="18">
            </td>
        </tr>
        <tr>
            <td><?php echo _("End Time"); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <input class="form-control datetimepicker req" type="text" id='enddateBox' name="enddate" value="<?php echo get_datetime_string(strtotime('now + 2 hours')); ?>" size="18">
            </td>
        </tr>
        <tr class="cmd-info 85 hide">
            <td></td>
            <td class="checkbox">
                <label><input type="checkbox" class="ahas" value="1"><?php echo _('Schedule downtime for all hosts too'); ?></label>
            </td>
        </tr>
    </table>
    <button type="button" class="btn btn-sm btn-primary submit-schedule-downtime"><?php echo _('Submit'); ?></button>
    <button type="button" class="btn btn-sm btn-default cancel"><?php echo _('Cancel'); ?></button>
</div>

<div class="xi-modal hide" id="set-notifications">
    <h2>
        <span id="n-title"><?php echo _('Set Notificaitons'); ?></span>
    </h2>
    <p class="cmd-info 63 64 hide"><?php echo _('Disable or enable notifications on all services (and hosts if selected) in the hostgroup.'); ?></p>
    <p class="cmd-info 65 66 hide"><?php echo _('Disable or enable notifications on all hosts (and hosts if selected) in the hostgroup.'); ?></p>
    <input type="hidden" class="cmd-type" value="">
    <table class="table table-condensed table-no-border table-auto-width">
        <tr>
            <td><?php echo _('Hostgroup Name'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control hostgroup_name req" readonly value=""></td>
        </tr>
        <tr>
            <td><?php echo _('Notifications'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <div class="cmd-info 63 65 hide"><?php echo _('Enable'); ?></div>
                <div class="cmd-info 64 66 hide"><?php echo _('Disable'); ?></div>
            </td>
        </tr>
        <tr class="cmd-info 63 64 hide">
            <td></td>
            <td class="checkbox">
                <label><input type="checkbox" class="ahas" value="1"><?php echo _('Set for all hosts too'); ?></label>
            </td>
        </tr>
    </table>
    <button type="button" class="btn btn-sm btn-primary submit-set-notifications"><?php echo _('Submit'); ?></button>
    <button type="button" class="btn btn-sm btn-default cancel"><?php echo _('Cancel'); ?></button>
</div>

<div class="xi-modal hide" id="set-checks">
    <h2>
        <span id="n-title"><?php echo _('Set Active Checks'); ?></span>
    </h2>
    <p><?php echo _('Disable or enable active checks on all services (and hosts if selected) in the hostgroup.'); ?></p>
    <input type="hidden" class="cmd-type" value="">
    <table class="table table-condensed table-no-border table-auto-width">
        <tr>
            <td><?php echo _('Hostgroup Name'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control hostgroup_name req" readonly value=""></td>
        </tr>
        <tr>
            <td><?php echo _('Active Checks'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <div class="cmd-info 67 hide"><?php echo _('Enable'); ?></div>
                <div class="cmd-info 68 hide"><?php echo _('Disable'); ?></div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="checkbox">
                <label><input type="checkbox" class="ahas"> <?php echo _('Set for all hosts too'); ?></label>
            </td>
        </tr>
    </table>
    <button type="button" class="btn btn-sm btn-primary submit-set-checks"><?php echo _('Submit'); ?></button>
    <button type="button" class="btn btn-sm btn-default cancel"><?php echo _('Cancel'); ?></button>
</div>

<div class="fl">
    <h1><?php echo _("Host Group Status");?></h1>
    <div class="servicestatustargettext"><?php echo $target_text;?></div>
    <?php draw_hostgroup_viewstyle_links($hostgroup);?>
</div>

<div class="fr" style="margin: 10px 0 20px 0;">
    <div class="fl" style="margin-right: 25px;">
        <?php
        $dargs = array(
            DASHLET_ARGS => array(
                "hostgroup" => $hostgroup,
                "show" => "services"
            )
        );
        display_dashlet("xicore_host_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
        ?>
    </div>
    <div class="fl">
        <?php display_dashlet("xicore_service_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD); ?>
    </div>
</div>  

<div style="clear: both; margin-bottom: 35px;"></div>

<div class="fl hg-dashlets">
<?php
// Summary style
if ($style == "summary") {
    $dargs = array(
        DASHLET_ARGS => array(
            "style" => $style
        )
    );
    display_dashlet("xicore_hostgroup_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
}
// Grid style
else {

    // Get hostgroups
    $args = array("orderby" => "hostgroup_name:a");
    if (!empty($hostgroup) && $hostgroup != "all") {
        $args["hostgroup_name"] = $hostgroup;
    }
    $xml = get_xml_hostgroup_objects($args);
    
    if ($xml) {
        foreach ($xml->hostgroup as $hg) {
            $hgname = strval($hg->hostgroup_name);
            $hgalias = strval($hg->alias);
            
            echo "<div class=\"hostgroup" . encode_form_val($style) . "-hostgroup\">";
            $dargs = array(
                DASHLET_ARGS => array(
                    "hostgroup" => $hgname,
                    "hostgroup_alias" => $hgalias,
                    "style" => $style
                )
            );
            display_dashlet("xicore_hostgroup_status_".$style, "", $dargs, DASHLET_MODE_OUTBOARD);
            echo "</div>";
        }
    }
}
?>
</div>
<div class="clear"></div>

<?php
    do_page_end(true);
}


function show_servicegroups()
{
    licensed_feature_check(true, true);

    $servicegroup = grab_request_var("servicegroup", "all");
    $style = grab_request_var("style", "overview");

    // Performance optimization
    $opt = get_option("use_unified_servicegroup_screens");
    if ($opt == 1) {
        header("Location: ".get_base_url()."includes/components/nagioscore/ui/status.php?servicegroup=".urlencode($servicegroup)."&style=".$style);
    }

    do_page_start(array("page_title" => _("Service Group Status")), true);

    $target_text = "";
    switch ($style) {
        case "summary":
            $target_text = _("Summary View");
            break;
        case "overview":
            $target_text = _("Overview");
            break;
        case "grid":
            $target_text = _("Grid View");
            break;
        default:
            break;
    }

    // Get timezone datepicker format
    if (isset($_SESSION['date_format'])) {
        $dformat = $_SESSION['date_format'];
    } else {
        if (is_null($dformat = get_user_meta(0, 'date_format'))) {
            $dformat = get_option('default_date_format');
        }
    }
    $dfs = get_date_formats();

    $js_date = 'mm/dd/yy';
    if ($dformat == DF_ISO8601) {
        $js_date = 'yy-mm-dd';
    } else if ($dformat == DF_US) {
        $js_date = 'mm/dd/yy';
    } else if ($dformat == DF_EURO) {
        $js_date = 'dd/mm/yy';
    }

?>

<script type="text/javascript">
$(document).ready(function() {

    $(window).resize(function() {
        $('.xi-modal').position({ my: "center", at: "center", of: window });
    });

    $('#whiteout').click(function() {
        if ($('#servicegroup-commands').is(":visible")) {
            clear_whiteout();
            $('#servicegroup-commands').hide();
        }
    });

    $('.cancel').click(function() {
        $(this).parent('div').hide();
        $(this).parent('div').find('.ahas').prop('checked', false);
        clear_whiteout();
    });

    $('#fixed').change(function() {
        if ($(this).val() == 0) {
            $('#flexible-box').show();
            $(this).parents('.xi-modal').position({ my: "center", at: "center", of: window });
        } else {
            $('#flexible-box').hide();
            $(this).parents('.xi-modal').position({ my: "center", at: "center", of: window });
        }
    });

    $('.datetimepicker').datetimepicker({
        showOn: 'button',
        buttonImage: '../../../images/datetimepicker.png',
        buttonImageOnly: true,
        dateFormat: '<?php echo $js_date; ?>',
        timeFormat: 'HH:mm:ss',
        showHour: true,
        showMinute: true,
        showSecond: true
    });

    // Check Date range accuracy
    $('#startdateBox').change(function() {
        var start_input = $('#startdateBox');
        var end_input = $('#enddateBox');
        var startdate_tp = start_input.datetimepicker('getDate');
        var enddate_tp = end_input.datetimepicker('getDate');

        dstartdate = Date.parse(startdate_tp)/1000;
        denddate = Date.parse(enddate_tp)/1000;

        if (dstartdate > denddate) {
            var new_ntp = startdate_tp;
            new_ntp.setHours(startdate_tp.getHours() + 2);
            end_input.datetimepicker('setDate', new_ntp);
        }
    });

    $('.sg-dashlets').on('click', '.group-dt-popup', function() {

        var name = $(this).data('name');
        $('#servicegroup-commands .sg-name').html(name);

        $('#servicegroup-commands').show();
        whiteout();
        $('#servicegroup-commands').position({ my: "center", at: "center", of: window });

    });

    // Do commands

    $('.cmdlink').click(function() {

        var cmd = $(this).data('cmd-type');
        var servicegroup = $('.sg-name').text();

        $('#servicegroup-commands').hide();
        $('.cmd-info').hide();

        if (cmd == 121 || cmd == 122) {
            $('.'+cmd).show();
            $("#schedule-downtime .cmd-type").val(cmd);
            $('#schedule-downtime .servicegroup_name').val(servicegroup);
            $('#schedule-downtime').show();
            $('#schedule-downtime').position({ my: "center", at: "center", of: window });
        } else if (cmd == 109 || cmd == 110 || cmd == 111 || cmd == 112) {
            $('.'+cmd).show();
            $("#set-notifications .cmd-type").val(cmd);
            $('#set-notifications .servicegroup_name').val(servicegroup);
            $('#set-notifications').show();
            $('#set-notifications').position({ my: "center", at: "center", of: window });
        } else if (cmd == 113 || cmd == 114) {
            $('.'+cmd).show();
            $("#set-checks .cmd-type").val(cmd);
            $('#set-checks .servicegroup_name').val(servicegroup);
            $('#set-checks').show();
            $('#set-checks').position({ my: "center", at: "center", of: window });
        }

    });

    $('.submit-schedule-downtime').click(function() {

        var error = 0;
        $('#schedule-downtime .req').each(function(k, i) {
            if ($(i).val().trim() == '') {
                error++;
            }
        });

        if (error) {
            alert("<?php echo encode_form_val(_('Please fill out all required fields.')); ?>"); 
            return;
        }

        var cmd = $('#schedule-downtime .cmd-type').val();
        var args = { cmd_typ: cmd,
                     cmd_mod: 2,
                     nsp: '<?php echo get_nagios_session_protector_id(); ?>',
                     servicegroup: $('#schedule-downtime .servicegroup_name').val(),
                     com_author: $('#schedule-downtime .com_author').val(),
                     com_data: $('#schedule-downtime .com_data').val(),
                     start_time: $('#startdateBox').val(),
                     end_time: $('#enddateBox').val(),
                     fixed: $('#fixed').val(),
                     hours: parseInt($('#flexible-hours').val()),
                     minutes: parseInt($('#flexible-minutes').val())
                    }

        // Special thing for services (schedule downtime for all hosts also)
        if (cmd == 122) {
            if ($('#schedule-downtime .ahas').is(':checked')) {
                args['ahas'] = "on";
            }
        }

        // Send the cmd & data to Core
        $.post('<?php echo get_base_url(); ?>includes/components/nagioscore/ui/cmd.php', args, function(d) {
            $('#schedule-downtime').hide();
            $('#schedule-downtime .com_data').val('');
            $('#schedule-downtime .servicegroup_name').val('');

            var content = "<div id='popup_header'><b>"+_('Command Submitted')+"</b></div><div style='margin-top: 10px;' id='popup_data'><ul class='ajaxcommandresult'><li class='commandresultok'><?php echo _('Command submitted successfully.'); ?></li></ul></div>";
            set_child_popup_content(content);
            display_child_popup();
            fade_child_popup("green");
            clear_whiteout();
        });

    });

    $('.submit-set-notifications').click(function() {

        var cmd = $('#set-notifications .cmd-type').val();
        var args = { cmd_typ: cmd,
                     cmd_mod: 2,
                     nsp: '<?php echo get_nagios_session_protector_id(); ?>',
                     servicegroup: $('#set-notifications .servicegroup_name').val()
                    }

        // Special thing for services (set notifications for all hosts also)
        if (cmd == 109 || cmd == 110) {
            if ($('#set-notifications .ahas').is(':checked')) {
                args['ahas'] = "on";
            }
        }

        // Send the cmd & data to Core
        $.post('<?php echo get_base_url(); ?>includes/components/nagioscore/ui/cmd.php', args, function(d) {
            $('#set-notifications').hide();
            $('#set-notifications .servicegroup_name').val('');
            $('#set-notifications .ahas').prop('checked', false);

            var content = "<div id='popup_header'><b>"+_('Command Submitted')+"</b></div><div style='margin-top: 10px;' id='popup_data'><ul class='ajaxcommandresult'><li class='commandresultok'><?php echo _('Command submitted successfully.'); ?></li></ul></div>";
            set_child_popup_content(content);
            display_child_popup();
            fade_child_popup("green");
            clear_whiteout();
        });

    });

    $('.submit-set-checks').click(function() {

        var cmd = $('#set-checks .cmd-type').val();
        var args = { cmd_typ: cmd,
                     cmd_mod: 2,
                     nsp: '<?php echo get_nagios_session_protector_id(); ?>',
                     servicegroup: $('#set-checks .servicegroup_name').val()
                    }

        // Set checks either enabled or disabled
        if ($('#set-checks .ahas').is(':checked')) {
            args['ahas'] = "on";
        }

        // Send the cmd & data to Core
        $.post('<?php echo get_base_url(); ?>includes/components/nagioscore/ui/cmd.php', args, function(d) {
            $('#set-checks').hide();
            $('#set-checks .servicegroup_name').val('');
            $('#set-checks .ahas').prop('checked', false);

            var content = "<div id='popup_header'><b>"+_('Command Submitted')+"</b></div><div style='margin-top: 10px;' id='popup_data'><ul class='ajaxcommandresult'><li class='commandresultok'><?php echo _('Command submitted successfully.'); ?></li></ul></div>";
            set_child_popup_content(content);
            display_child_popup();
            fade_child_popup("green");
            clear_whiteout();
        });service  });

});
</script>

<div class="xi-modal hide" id="servicegroup-commands" style="max-width: 400px;">
    <h2><?php echo _('Servicegroup Commands'); ?></h2>
    <div style="margin: 0 0 10px 0; font-size: 13px;"><b><span class="sg-name"></span></b></div>
    <div>
        <p><?php echo _('Submit a bulk command for all the hosts or services in the selected servicegroup.'); ?></p>
        <div style="margin: 10px 0;">
            <div><a class="cmdlink dt" data-cmd-type="121"><img src="<?php echo theme_image('time_add.png'); ?>"><?php echo _('Schedule downtime for all hosts'); ?></a></div>
            <div><a class="cmdlink dt" data-cmd-type="122"><img src="<?php echo theme_image('time_add.png'); ?>"><?php echo _('Schedule downtime for all services'); ?></a></div>
        </div>
        <div style="margin-bottom: 10px;">
            <div><a class="cmdlink" data-cmd-type="111"><img src="<?php echo theme_image('notifications2.png'); ?>"><?php echo _('Enable notifications for all hosts'); ?></a></div>
            <div><a class="cmdlink" data-cmd-type="112"><img src="<?php echo theme_image('nonotifications.png'); ?>"><?php echo _('Disable notifications for all hosts'); ?></a></div>
        </div>
        <div style="margin-bottom: 10px;">
            <div><a class="cmdlink" data-cmd-type="109"><img src="<?php echo theme_image('notifications2.png'); ?>"><?php echo _('Enable notifications for all services'); ?></a></div>
            <div><a class="cmdlink" data-cmd-type="110"><img src="<?php echo theme_image('nonotifications.png'); ?>"><?php echo _('Disable notifications for all services'); ?></a></div>
        </div>
        <div><a class="cmdlink" data-cmd-type="113"><img src="<?php echo theme_image('enable_small.gif'); ?>"><?php echo _('Enable active checks of all services'); ?></a></div>
        <div><a class="cmdlink" data-cmd-type="114"><img src="<?php echo theme_image('cross.png'); ?>"><?php echo _('Disable active checks of all services'); ?></a></div>
    </div>
</div>

<div class="xi-modal hide" id="schedule-downtime">
    <h2>
        <span id="sd-title"><?php echo _('Schedule Downtime'); ?></span>
        <i class="fa fa-question-circle fa-14 pop" style="margin-left: 5px;" data-title="<?php echo _('Schedule Downtime'); ?>" data-content="<?php echo _('This command is used to schedule downtime for objects. During the specified downtime, Nagios will not send notifications out about the object. When the scheduled downtime expires, Nagios will send out notifications for this object as it normally would. Scheduled downtimes are preserved across program shutdowns and restarts. Both the start and end times should be specified in the following format:'). ' ' . $dfs[$dformat] . '. ' ._('If you select the fixed option, the downtime will be in effect between the start and end times you specify. If you do not select the fixed option, Nagios will treat this as flexible downtime. Flexible downtime starts when the object goes into a non-OK/UP state (sometime between the start and end times you specified) and lasts as long as the duration of time you enter. The duration fields do not apply for fixed downtime.'); ?>"></i>
    </h2>
    <p class='cmd-info 121 hide'><?php echo _('Schedule downtime for all hosts in the selected servicegroup.'); ?></p>
    <p class='cmd-info 122 hide'><?php echo _('Schedule downtime for all services in the selected servicegroup.'); ?></p>
    <input type="hidden" class="cmd-type" value="">
    <table class="table table-condensed table-no-border table-auto-width">
        <tr>
            <td><?php echo _('Servicegroup Name'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control servicegroup_name req" readonly value=""></td>
        </tr>
        <tr>
            <td><?php echo _('Author'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control com_author req" readonly value="<?php echo get_user_attr(0, 'name'); ?>"></td>
        </tr>
        <tr>
            <td><?php echo _('Comment'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control com_data req" style="width: 360px;"></td>
        </tr>
        <tr>
            <td><?php echo _('Type'); ?></td>
            <td>
                <select id="fixed" class="form-control">
                    <option value="1"><?php echo _('Fixed'); ?></option>
                    <option value="0"><?php echo _('Flexible'); ?></option>
                </select>
            </td>
        </tr>
        <tr id="flexible-box" class="hide">
            <td><?php echo _('Duration'); ?></td>
            <td>
                <input type="text" class="form-control" style="width: 40px;" id="flexible-hours" value="2"> Hours
                <input type="text" class="form-control" style="width: 40px; margin-left: 5px;" id="flexible-minutes" value="0"> Minutes
            </td>
        </tr>
        <tr>
            <td><?php echo _("Start Time"); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <input class="form-control datetimepicker req" type="text" id='startdateBox' name="startdate" value="<?php echo get_datetime_string(time()); ?>" size="18">
            </td>
        </tr>
        <tr>
            <td><?php echo _("End Time"); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <input class="form-control datetimepicker req" type="text" id='enddateBox' name="enddate" value="<?php echo get_datetime_string(strtotime('now + 2 hours')); ?>" size="18">
            </td>
        </tr>
        <tr class="cmd-info 85 hide">
            <td></td>
            <td class="checkbox">
                <label><input type="checkbox" class="ahas" value="1"><?php echo _('Schedule downtime for all hosts too'); ?></label>
            </td>
        </tr>
    </table>
    <button type="button" class="btn btn-sm btn-primary submit-schedule-downtime"><?php echo _('Submit'); ?></button>
    <button type="button" class="btn btn-sm btn-default cancel"><?php echo _('Cancel'); ?></button>
</div>

<div class="xi-modal hide" id="set-notifications">
    <h2>
        <span id="n-title"><?php echo _('Set Notificaitons'); ?></span>
    </h2>
    <p class="cmd-info 109 110 hide"><?php echo _('Disable or enable notifications on all services (and hosts if selected) in the servicegroup.'); ?></p>
    <p class="cmd-info 111 112 hide"><?php echo _('Disable or enable notifications on all hosts (and hosts if selected) in the servicegroup.'); ?></p>
    <input type="hidden" class="cmd-type" value="">
    <table class="table table-condensed table-no-border table-auto-width">
        <tr>
            <td><?php echo _('Servicegroup Name'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control servicegroup_name req" readonly value=""></td>
        </tr>
        <tr>
            <td><?php echo _('Notifications'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <div class="cmd-info 109 111 hide"><?php echo _('Enable'); ?></div>
                <div class="cmd-info 110 112 hide"><?php echo _('Disable'); ?></div>
            </td>
        </tr>
        <tr class="cmd-info 109 110 hide">
            <td></td>
            <td class="checkbox">
                <label><input type="checkbox" class="ahas" value="1"><?php echo _('Set for all hosts too'); ?></label>
            </td>
        </tr>
    </table>
    <button type="button" class="btn btn-sm btn-primary submit-set-notifications"><?php echo _('Submit'); ?></button>
    <button type="button" class="btn btn-sm btn-default cancel"><?php echo _('Cancel'); ?></button>
</div>

<div class="xi-modal hide" id="set-checks">
    <h2>
        <span id="n-title"><?php echo _('Set Active Checks'); ?></span>
    </h2>
    <p><?php echo _('Disable or enable active checks on all services (and hosts if selected) in the servicegroup.'); ?></p>
    <input type="hidden" class="cmd-type" value="">
    <table class="table table-condensed table-no-border table-auto-width">
        <tr>
            <td><?php echo _('Servicegroup Name'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control servicegroup_name req" readonly value=""></td>
        </tr>
        <tr>
            <td><?php echo _('Active Checks'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <div class="cmd-info 113 hide"><?php echo _('Enable'); ?></div>
                <div class="cmd-info 114 hide"><?php echo _('Disable'); ?></div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="checkbox">
                <label><input type="checkbox" class="ahas"> <?php echo _('Set for all hosts too'); ?></label>
            </td>
        </tr>
    </table>
    <button type="button" class="btn btn-sm btn-primary submit-set-checks"><?php echo _('Submit'); ?></button>
    <button type="button" class="btn btn-sm btn-default cancel"><?php echo _('Cancel'); ?></button>
</div>

<div class="fl">
    <h1><?php echo _("Service Group Status");?></h1>
    <div class="servicestatustargettext"><?php echo $target_text;?></div>
    <?php draw_servicegroup_viewstyle_links($servicegroup);?>
</div>

<div class="fr" style="margin: 10px 0 20px 0;">
    <div class="fl" style="margin-right: 25px;">
        <?php
        $dargs = array(
            DASHLET_ARGS => array(
                "servicegroup" => $servicegroup,
                "show" => "services"
            )
        );
        display_dashlet("xicore_host_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
        ?>
    </div>
    <div class="fl">
    <?php display_dashlet("xicore_service_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD); ?>
    </div>
</div>  

<div style="clear: both; margin-bottom: 35px;"></div>

<div class="fl sg-dashlets">
<?php
// Summary style
if ($style == "summary") {
    $dargs = array(
        DASHLET_ARGS => array(
            "style" => $style
        )
    );
    display_dashlet("xicore_servicegroup_status_summary", "", $dargs, DASHLET_MODE_OUTBOARD);
}
// Grid style
else {

    // Get service groups
    $args = array("orderby" => "servicegroup_name:a");
    if (!empty($servicegroup) && $servicegroup != "all") {
        $args["servicegroup_name"] = $servicegroup;
    }
    $xml = get_xml_servicegroup_objects($args);

    if ($xml) {
        foreach ($xml->servicegroup as $sg) {
            $sgname = strval($sg->servicegroup_name);
            $sgalias = strval($sg->alias);

            echo "<div class=servicegroup".encode_form_val($style)."-servicegroup>";
            $dargs = array(
                DASHLET_ARGS => array(
                    "servicegroup" => $sgname,
                    "servicegroup_alias" => $sgalias,
                    "style" => $style
                )
            );
            display_dashlet("xicore_servicegroup_status_".$style, "", $dargs, DASHLET_MODE_OUTBOARD);
            echo "</div>";
        }
    }
}
?>
</div>
<div class="clear"></div>

<?php
    do_page_end(true);
}


function show_tac()
{
    do_page_start(array("page_title" => _("Tactical Overview")), true);
?>

    <h1><?php echo _("Tactical Overview"); ?></h1>

<?php
    do_page_end(true);
}


function show_open_problems()
{
    do_page_start(array("page_title" => _("Open Problems")), true);
?>

    <h1><?php echo _("Open Problems"); ?></h1>

<?php
    do_page_end(true);
}


function show_host_problems()
{
    do_page_start(array("page_title" => _("Host Problems")), true);
?>

    <h1><?php echo _("Host Problems"); ?></h1>

<?php
    do_page_end(true);
}


function show_service_problems()
{
    do_page_start(array("page_title" => _("Service Problems")), true);
?>

    <h1><?php echo _("Service Problems"); ?></h1>

<?php
    do_page_end(true);
}


function show_network_outages()
{
    do_page_start(array("page_title" => _("Network Outages")), true);
?>

    <h1><?php echo _("Network Outages");?></h1>

    <div class="fl">
    <?php
    $dargs = array(
        DASHLET_ARGS => array()
    );
    display_dashlet("xicore_network_outages", "", $dargs, DASHLET_MODE_OUTBOARD);
    ?>
    </div>

<?php
    do_page_end(true);
}


function show_status_map()
{
    global $request;
    do_page_start(array( "page_title" => _("Legacy Network Status Map")), true);
?>

    <style type="text/css">
    #mappage { padding: 0; margin: 0; }
    #mappage h1 { font-family: verdana, serif; }
    </style>

    <h1><?php echo _("Legacy Network Status Map"); ?></h1>

    <?php draw_statusmap_viewstyle_links(); ?>

    <?php
    // Add request arguments to the url
    $url = "statusmap.php?noheader";
    foreach ($request as $var => $val) {
        if ($var == "show" || empty($var)) {
            continue;
        }
        $url .= "&".encode_form_val($var)."=".encode_form_val($val);
    }
    $args = array("url" => $url);
    
    // Build args for JS
    $jargs = json_encode($args);

    $id = "nagioscore_cgi_output_" . random_string(6);
    $output = '
    <div class="nagioscore_cgi_output" id="' . $id . '">
    ' . xicore_ajax_get_nagioscore_cgi_html($args) . '
    </div><!--nagioscore_cgi_output-->
    <script type="text/javascript">
    $(document).ready(function() {
        $("#' . $id . '").everyTime(' . get_dashlet_refresh_rate(30, 'legacy_statusmap') . ', "timer-' . $id . '", function(i) {
            var optsarr = {
                "func": "get_nagioscore_cgi_html",
                "args": ' . $jargs . '
            }
            var opts=JSON.stringify(optsarr);
            get_ajax_data_innerHTML("getxicoreajax",opts,true,this);
        });
    });
    </script>';
    ?>

    <div class="statusmap">
        <?php echo $output;?>
    </div>

<?php
    do_page_end(true);
}


function show_does_not_exist_object_page()
{
    do_page_start(array("page_title" => _('Object Does Not Exist')), true);
?>
    <h1><?php echo _("Object Does Not Exist"); ?></h1>

<?php
    do_page_end(true);
    exit();
}


function show_not_authorized_for_object_page()
{
    do_page_start(array("page_title" => _("Not Authorized")), true);
?>
    <h1><?php echo _("Not Authorized");?></h1>

    <?php echo _("You are not authorized to view the requested object, or the object does not exist."); ?>
    
<?php
    do_page_end(true);
    exit();
}


function show_monitoring_process()
{
    do_page_start(array("page_title" => _("Monitoring Process")), true);
?>

    <h1><?php echo _("Monitoring Process");?></h1>

    <div class="fl" style="margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_monitoring_process", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

    <div class="fl" style="margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_eventqueue_chart", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

<?php
    do_page_end(true);
}


function show_monitoring_performance()
{
    do_page_start(array("page_title" => _("Monitoring Performance")), true);
?>

    <h1><?php echo _("Monitoring Performance");?></h1>

    <div class="fl" style="margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_monitoring_stats", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

    <div class="fl" style="margin: 0 30px 30px 0;">
        <?php display_dashlet("xicore_monitoring_perf", "", null, DASHLET_MODE_OUTBOARD); ?>
    </div>

<?php
    do_page_end(true);
}
