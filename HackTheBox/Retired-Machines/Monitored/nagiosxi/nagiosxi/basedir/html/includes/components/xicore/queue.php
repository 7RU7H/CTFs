<?php
//
// Copyright (c) 2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Start session
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);


route_request();


function route_request()
{
    $cmd = grab_request_var("cmd", "");

    switch ($cmd) {

        case "disable":
            do_disable_active();
            break;

        case "reschedule":
            do_reschedule_check();
            break;

        case "getqueue":
            get_schedule_queue_json();
            break;

        case "setrecords":
            $records = grab_request_var('records');
            if (is_numeric($records)) {
                set_user_meta(0, 'scheduling_queue_page_records', $records);
            }
            break;

        case "setrefresh":
            $refresh = grab_request_var('refresh');
            if (is_numeric($refresh)) {
                set_user_meta(0, 'scheduling_queue_page_refresh', $refresh);
            }
            break;

        default:
            show_queue();
            break;

    }
}


function show_queue()
{
    $refresh = intval(get_user_meta(0, 'scheduling_queue_page_refresh', 60));
    if ($refresh < 1) {
        $refresh = 1;
    }

    $url = get_base_url()."includes/components/xicore/queue.php";

    $queue = get_schedule_queue();
    $total = count($queue);
    $per_page = grab_request_var('records', get_user_meta(0, 'scheduling_queue_page_records', 10));
    $total_pages = ceil($total/$per_page);
    if ($per_page == -1) {
        $total_pages = 1;
    }
    $pager_results = array('records_per_page' => $per_page,
                           'current_page' => grab_request_var('page', 1),
                           'total_records' => $total,
                           'total_pages' => $total_pages);

    // Get timezone datepicker format
    if (isset($_SESSION['date_format'])) {
        $format = $_SESSION['date_format'];
    } else {
        if (is_null($format = get_user_meta(0, 'date_format')))
            $format = get_option('default_date_format');
    }
    $f = get_date_formats();

    $js_date = 'mm/dd/yy';
    if ($format == DF_ISO8601) {
        $js_date = 'yy-mm-dd';
    } else if ($format == DF_US) {
        $js_date = 'mm/dd/yy';
    } else if ($format == DF_EURO) {
        $js_date = 'dd/mm/yy';
    }

    do_page_start(array("page_title" => _('Scheduling Queue')), true);
?>

<script type="text/javascript">
// Selected ids (to preserve when ajax calls happen)
var SEL_IDS = [];
var PAGE_TO_ID = 0;
var ORDER_BY = [];
var SEM = 0;

var record_limit = <?php echo $pager_results['records_per_page']; ?>;
var current_page = <?php echo $pager_results['current_page']; ?>;
var total_records = <?php echo $pager_results['total_records']; ?>;
var total_pages = <?php echo $pager_results['total_pages']; ?>;

function get_queue(force)
{
    var data = { records: $('.num-records').val(), page: current_page };

    // Grab ORDER BY
    if (ORDER_BY != '') {
        data.orderby = ORDER_BY;
    }

    $('.refresh').html('<i class="fa fa-spinner fa-14 fa-spin"></i>');
    SEM = 1;
    setTimeout('loading_data()', 500);

    data.cmd = 'getqueue';
    $.ajax({
        type: "GET",
        url: '<?php echo $url; ?>',
        data: data,
        dataType: 'json',
        success: function(qdata) {
            SEM = 0;

            // Update paging
            $('#paging-info').html(qdata.pagination.html);
            total_pages = qdata.pagination.pages;
            total_records = qdata.pagination.total;
            update_pages(false);

            // Update table
            var html_table = '';
            if (Object.keys(qdata.objects).length > 0) {
                $.each(qdata.objects, function(k, o) {

                    html = '<tr>';
                    html += '<td><a class="host_name" href="status.php?show=hostdetail&host=' + encodeURIComponent(o.host_name) + '">' + o.host_name + '</a></td>';
                    if (o.service_description === null) {
                        html += '<td></td>';
                    } else {
                        html += '<td><a class="service_description" href="status.php?show=servicedetail&host=' + encodeURIComponent(o.host_name) + '&service=' + encodeURIComponent(o.service_description) + '">' + o.service_description + '</a></td>';
                    }
                    html += '<td>' + o.last_check + '</td>';
                    html += '<td class="check_time">' + o.next_check + '</td>';
                    html += '<td>' + o.check_options + '</td>';
                    html += '<td style="text-align: center;" class="serviceok">' + o.active_checks + '</td>';
                    html += '<td style="text-align: center;"><a class="q-tt-bind disable-checks" title="<?php echo _('Disable active checks'); ?>"><img src="<?php echo theme_image('cross.png'); ?>"></a>&nbsp;&nbsp;<a class="q-tt-bind reschedule" title="<?php echo _('Re-schedule'); ?>"><img src="<?php echo theme_image('time.png'); ?>"></a>&nbsp;&nbsp;<a class="q-tt-bind force-check" title="<?php echo _('Force immediate'); ?>"><img src="<?php echo theme_image('time_go.png'); ?>"></a></td>';
                    html += '</tr>';

                    html_table += html;

                });
            }

            if (html_table == '') {
                html_table = '<tr><td colspan="14"><?php echo _("No scheduled downtime results matching your current filters."); ?></td></tr>';
            }

            $('#queue').html(html_table);
            $('#loadscreen').hide();
            $('#loadscreen-spinner').hide();

            $('.q-tt-bind').tooltip();

            $('.refresh').html('<img src="<?php echo theme_image("arrow_refresh.png"); ?>">');
        }
    });

}

function loading_data()
{
    if (SEM == 1) {
        var h = $('.table-queue').outerHeight();
        var w = $('.table-queue').outerWidth();
        var margin = (h-$('#loadscreen-spinner').outerHeight())/2;
        var leftmargin = (w-$('#loadscreen-spinner').outerWidth())/2;
        $('#loadscreen').height(h);
        $('#loadscreen').width(w);
        $('#loadscreen-spinner').css('margin-top', margin+'px')
                                .css('margin-left', leftmargin+'px')
                                .show();
        $('#loadscreen').show();
    }
}

$(document).ready(function() {

    get_queue();
    PAGE_TO_ID = setInterval(get_queue, <?php echo intval($refresh) * 1000; ?>);

    $('.refresh').click(function() {
        get_queue(true);
    });

    $('#queue').on('click', '.reschedule', function() {
        var row = $(this).parents('tr');
        var host = row.find('.host_name').text();
        var service = row.find('.service_description').text();
        var check_time = row.find('.check_time').text();

        // Add data into the modal
        $('#re-schedule .host').val(host);
        $('#re-schedule .service').val(service);
        $('#re-schedule .check_time').val(check_time);

        if (service != '') {
            $('.service-section').show();
        } else {
            $('.service-section').hide();
        }

        // Set up the modal
        whiteout();
        $('#re-schedule').center().show();
    });

    $('#re-schedule').on('click', '.btn-cancel', function() {
        clear_whiteout();
        $('#re-schedule').hide();
    });

    $('.btn-schedule').click(function() {
        var host = $('#re-schedule .host').val();
        var service = $('#re-schedule .service').val();

        cmdargs = { cmd: <?php echo NAGIOSCORE_CMD_SCHEDULE_FORCED_HOST_CHECK; ?>,
                    host_name: host }

        if ($('#re-schedule .force').is(':checked')) {
            if (service != '') {
                cmdargs.cmd = <?php echo NAGIOSCORE_CMD_SCHEDULE_FORCED_SVC_CHECK; ?>;
                cmdargs.service_name = service;
            }
        } else {
            cmdargs.cmd = <?php echo NAGIOSCORE_CMD_SCHEDULE_HOST_CHECK; ?>;
            if (service != '') {
                cmdargs.cmd = <?php echo NAGIOSCORE_CMD_SCHEDULE_SVC_CHECK; ?>;
                cmdargs.service_name = service;
            }
        }

        // Add timestamp
        date = $('.datetimepicker').datetimepicker('getDate');
        timestamp = date.getTime() / 1000;
        cmdargs.start_time = timestamp;

        // Remove modal so we can submit command
        clear_whiteout();
        $('#re-schedule').hide();

        submit_command(<?php echo COMMAND_NAGIOSCORE_SUBMITCOMMAND; ?>, cmdargs);
    });

    $('#queue').on('click', '.disable-checks', function() {
        var row = $(this).parents('tr');
        var host = row.find('.host_name').text();
        var service = row.find('.service_description').text();

        cmdargs = { cmd: <?php echo NAGIOSCORE_CMD_DISABLE_HOST_CHECK; ?>,
                    host_name: host }

        // Set up for service
        if (service != '') {
            cmdargs.cmd = <?php echo NAGIOSCORE_CMD_DISABLE_SVC_CHECK; ?>;
            cmdargs.service_name = service;
        }

        submit_command(<?php echo COMMAND_NAGIOSCORE_SUBMITCOMMAND; ?>, cmdargs);
    });

    $('#queue').on('click', '.force-check', function() {
        var row = $(this).parents('tr');
        var host = row.find('.host_name').text();
        var service = row.find('.service_description').text();

        cmdargs = { cmd: <?php echo NAGIOSCORE_CMD_SCHEDULE_FORCED_HOST_CHECK; ?>,
                    host_name: host }

        // Set up for service
        if (service != '') {
            cmdargs.cmd = <?php echo NAGIOSCORE_CMD_SCHEDULE_FORCED_SVC_CHECK; ?>;
            cmdargs.service_name = service;
        }

        submit_command(<?php echo COMMAND_NAGIOSCORE_SUBMITCOMMAND; ?>, cmdargs);
    });

    // PAGINATION

    $('.next-page').click(function() {
        current_page++;
        update_pages();
    });

    $('.first-page').click(function() {
        current_page = 1;
        update_pages();
    });

    $('.last-page').click(function() {
        current_page = total_pages;
        update_pages();
    });

    $('.previous-page').click(function() {
        if (current_page > 1) {
            current_page--;
        }
        update_pages();
    });

    $('.num-records').change(function() {
        $('.num-records').val($(this).val());
        record_limit = $(this).val();
        current_page = 1;
        update_pages();
        $.get('queue.php', { cmd: 'setrecords', records: record_limit });
    });

    // ORDER BY

    $('.table-queue').on('click', '.orderby', function() {
        $('.table-queue .orderby .fa').remove();
        var ob = $(this).data('orderby');
        if (ORDER_BY[0] == ob) {
            if (ORDER_BY[1] == "a") {
                ORDER_BY[1] = "d";
                $(this).append(' <i class="fa fa-chevron-up"></i>');
            } else {
                ORDER_BY[1] = "a";
                $(this).append(' <i class="fa fa-chevron-down"></i>');
            }
        } else {
            ORDER_BY = [ob, "a"];
            $(this).append(' <i class="fa fa-chevron-down"></i>');
        }
        get_queue(true);
    });

    // PAGE SETTINGS

    if ($('#open-qform').length > 0) {
        var p = $('#open-qform').position();
        var left = Math.floor(p.left+13) - ($('#settings-dropdown').outerWidth()/2);
        var top = Math.floor(p.top+22);
        $('#settings-dropdown').css('left', left+'px');
        $('#settings-dropdown').css('top', top+'px');

        $("#open-qform").click(function () {
            var d = $("#settings-dropdown").css("display");
            if (d == "none") {
                $("#settings-dropdown").fadeIn("fast");
            } else {
                $("#settings-dropdown").fadeOut("fast");
            }
        });
    }

    $('.btn-close-settings').click(function() {
        $("#settings-dropdown").fadeOut("fast");
    });
    $('.btn-update-settings').click(function() {
        var refresh = parseInt($('.page-refresh').val());
        if (refresh) {
            clearInterval(PAGE_TO_ID);
            var rval = refresh * 1000;
            PAGE_TO_ID = setInterval(get_queue, rval);

            // Set refresh value in user meta
            $.get('queue.php', { cmd: 'setrefresh', refresh: refresh });
        }
        $("#settings-dropdown").fadeOut("fast");
    });

    // JUMP TO PAGE

    $('.jump-to').keyup(function(e) {
        if (e.keyCode == 13) {
            var page = $(this).val();
            $(this).val('');
            if (page == '' || page < 1 ) { page = 1; }
            else if (page > total_pages) { page = total_pages; }
            current_page = page;
            update_pages();
        }
    });

    $('.jump').click(function() {
        var page = $(this).parent().find('.jump-to').val();
        $(this).parent().find('.jump-to').val('');
        if (page == '' || page < 1 ) { page = 1; }
        else if (page > total_pages) { page = total_pages; }
        current_page = page;
        update_pages();
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

});

function update_pages(reload) {
    if (reload === undefined) { reload = true; }

    var curr_page_html = '<?php echo _("Page"); ?> ' + current_page + ' <?php echo _("of"); ?> ' + total_pages;
    $('.ajax-pagination span').html(curr_page_html);

    // Do hide/show options
    if (current_page > 1) {
        $('.previous-page').attr('disabled', false);
    } else if (current_page == 1) {
        $('.previous-page').attr('disabled', true);
    }

    if (current_page == total_pages) {
        $('.next-page').attr('disabled', true);
    } else {
        $('.next-page').attr('disabled', false);
    }

    if (reload) {
        get_queue(true);
    }
}
</script>

<div class="well report-options">
    <div class="fl" style="height: 29px; margin-right: 30px; margin-bottom: 15px;">
        <h1><?php echo _('Scheduling Queue'); ?></h1>
        <a class="tt-bind refresh" data-placement="right" title="<?php echo _('Refresh'); ?>"><img src="<?php echo theme_image('arrow_refresh.png'); ?>"></a>
        <a class="tt-bind settings-dropdown" id="open-qform" data-placement="right" title="<?php echo _('Edit page settings'); ?>"><img src="<?php echo theme_image('cog.png'); ?>"></a>
        <div id="settings-dropdown">
            <div class="content">
                <div class="input-group" style="width: 200px;">
                    <label class="input-group-addon"><?php echo _('Refresh every'); ?></label>
                    <input type="text" class="page-refresh form-control" value="<?php echo $refresh; ?>" placeholder="30">
                    <label class="input-group-addon"><?php echo _('sec'); ?></label>
                </div>
                <div style="margin-top: 10px">
                    <button type="button" class="btn btn-update-settings btn-xs btn-primary fl"><i class="fa fa-check fa-l"></i> <?php echo _('Update'); ?></button>
                    <button type="button" class="btn btn-close-settings btn-xs btn-default fr"><i class="fa fa-times"></i> <?php echo _('Close'); ?></button>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>

<div style="padding: 0 0 10px 0;">
    <div class="fl">
        <div class="fl" id="paging-info" style="height: 29px; line-height: 29px;"></div>
        <div class="clear"></div>
    </div>
    <div class="fr">
        <div class="ajax-pagination" style="margin: 3px 0 0 0;">
            <button class="btn btn-xs btn-default first-page" title="<?php echo _('First Page'); ?>"><i class="fa fa-fast-backward"></i></button>
            <button class="btn btn-xs btn-default previous-page" title="<?php echo _('Previous Page'); ?>" disabled><i class="fa fa-chevron-left l"></i></button>
            <span style="margin: 0 10px;"><?php echo _('Page'); ?> <span class="pagenum">1 <?php echo _('of'); ?> <?php echo $pager_results['total_pages']; ?></span></span>
            <button class="btn btn-xs btn-default next-page" title="<?php echo _('Next Page'); ?>"><i class="fa fa-chevron-right r"></i></button>
            <button class="btn btn-xs btn-default last-page" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-forward"></i></button>

            <select class="form-control condensed num-records">
                <option value="5"<?php if ($pager_results['records_per_page'] == 5) { echo ' selected'; } ?>>5 <?php echo _('Per Page'); ?></option>
                <option value="10"<?php if ($pager_results['records_per_page'] == 10) { echo ' selected'; } ?>>10 <?php echo _('Per Page'); ?></option>
                <option value="25"<?php if ($pager_results['records_per_page'] == 25) { echo ' selected'; } ?>>25 <?php echo _('Per Page'); ?></option>
                <option value="50"<?php if ($pager_results['records_per_page'] == 50) { echo ' selected'; } ?>>50 <?php echo _('Per Page'); ?></option>
                <option value="100"<?php if ($pager_results['records_per_page'] == 100) { echo ' selected'; } ?>>100 <?php echo _('Per Page'); ?></option>
                <option value="250"<?php if ($pager_results['records_per_page'] == 250) { echo ' selected'; } ?>>250 <?php echo _('Per Page'); ?></option>
                <option value="500"<?php if ($pager_results['records_per_page'] == 500) { echo ' selected'; } ?>>500 <?php echo _('Per Page'); ?></option>
                <option value="-1"<?php if ($pager_results['records_per_page'] == -1) { echo ' selected'; } ?>><?php echo _('No Limit'); ?></option>
            </select>

            <input type="text" class="form-control condensed jump-to">
            <button class="btn btn-xs btn-default tt-bind jump" title="<?php echo _('Jump to Page'); ?>"><i class="fa fa-chevron-circle-right fa-12"></i></button>
        </div>
    </div>
    <div class="clear"></div>
</div>

<div style="min-height: 100px;">
    <div id="loadscreen" class="hide"></div>
    <div id="loadscreen-spinner" class="sk-spinner sk-spinner-rotating-plane hide"></div>
    <table class="table table-condensed table-hover table-queue table-bordered table-striped table-no-margin" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 20%;"><span class="orderby" data-orderby="host_name"><?php echo _('Host'); ?></span></th>
                <th><span class="orderby" data-orderby="service_description"><?php echo _('Service'); ?></span></th>
                <th style="min-width: 150px; width: 10%;"><span class="orderby" data-orderby="last_check"><?php echo _('Last Check'); ?></span></th>
                <th style="min-width: 150px; width: 10%;"><span class="orderby" data-orderby="next_check"><?php echo _('Next Check'); ?></span></th>
                <th style="width: 100px;"><span class="orderby" data-orderby="type"><?php echo _('Type'); ?></span></th>
                <th style="text-align: center; width: 140px;"><?php echo _('Active Checks'); ?></th>
                <th style="width: 120px;" class="center"><?php echo _('Actions'); ?></th>
            </tr>
        </thead>
        <tbody id="queue">
        </tbody>
    </table>
</div>

<div>
    <div class="fr">
        <div class="ajax-pagination" style="margin: 20px 0 0 0;">
            <button class="btn btn-xs btn-default first-page" title="<?php echo _('First Page'); ?>"><i class="fa fa-fast-backward"></i></button>
            <button class="btn btn-xs btn-default previous-page" title="<?php echo _('Previous Page'); ?>" disabled><i class="fa fa-chevron-left l"></i></button>
            <span style="margin: 0 10px;"><?php echo _('Page'); ?> <span class="pagenum">1 <?php echo _('of'); ?> <?php echo $pager_results['total_pages']; ?></span></span>
            <button class="btn btn-xs btn-default next-page" title="<?php echo _('Next Page'); ?>"><i class="fa fa-chevron-right r"></i></button>
            <button class="btn btn-xs btn-default last-page" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-forward"></i></button>

            <select class="form-control condensed num-records">
                <option value="5"<?php if ($pager_results['records_per_page'] == 5) { echo ' selected'; } ?>>5 <?php echo _('Per Page'); ?></option>
                <option value="10"<?php if ($pager_results['records_per_page'] == 10) { echo ' selected'; } ?>>10 <?php echo _('Per Page'); ?></option>
                <option value="25"<?php if ($pager_results['records_per_page'] == 25) { echo ' selected'; } ?>>25 <?php echo _('Per Page'); ?></option>
                <option value="50"<?php if ($pager_results['records_per_page'] == 50) { echo ' selected'; } ?>>50 <?php echo _('Per Page'); ?></option>
                <option value="100"<?php if ($pager_results['records_per_page'] == 100) { echo ' selected'; } ?>>100 <?php echo _('Per Page'); ?></option>
                <option value="250"<?php if ($pager_results['records_per_page'] == 250) { echo ' selected'; } ?>>250 <?php echo _('Per Page'); ?></option>
                <option value="500"<?php if ($pager_results['records_per_page'] == 500) { echo ' selected'; } ?>>500 <?php echo _('Per Page'); ?></option>
                <option value="-1"<?php if ($pager_results['records_per_page'] == -1) { echo ' selected'; } ?>><?php echo _('No Limit'); ?></option>
            </select>

            <input type="text" class="form-control condensed jump-to">
            <button class="btn btn-xs btn-default tt-bind jump" title="<?php echo _('Jump to Page'); ?>"><i class="fa fa-chevron-circle-right fa-12"></i></button>
        </div>
    </div>
    <div class="clear"></div>
</div>


<div id="re-schedule" class="xi-modal hide">
    <h2><?php echo _('Re-Schedule Check'); ?></h2>
    <div style="margin: 20px 0;">
        <table class="table table-condensed table-no-border table-auto-width table-no-margin">
            <tr>
                <td><label><?php echo _('Host Name'); ?></label></td>
                <td>
                    <input type="text" class="form-control host" style="width: 260px;" value="" readonly>
                </td>
            </tr>
            <tr class="service-section hide">
                <td><label><?php echo _('Service Name'); ?></label></td>
                <td>
                    <input type="text" class="form-control service" style="width: 260px;" value="" readonly>
                </td>
            </tr>
            <tr>
                <td><label><?php echo _('Check Time'); ?></label></td>
                <td>
                    <input class="form-control check_time datetimepicker req" style="width: 150px;" type="text" id='startdateBox' name="check_time" value="" size="16">
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="checkbox">
                    <label><input type="checkbox" class="force" name="force" checked="checked" value="1"><?php echo _('Force the check'); ?></label>
                    <i class="fa fa-question-circle pop" data-content="<?php echo _('Force a check regardless of what time the scheduled check occurs and whether or not checkd are enabled for the object.'); ?>"></i>
                </td>
            </tr>
        </table>
    </div>
    <div>
        <button type="button" data-loading-text="<?php echo _('Scheduling...'); ?>" class="btn-schedule btn btn-sm btn-primary"><?php echo _('Schedule'); ?></button>
        <button type="button" class="btn-cancel btn btn-sm btn-default"><?php echo _('Cancel'); ?></button>
    </div>
</div>

<?php
    do_page_end(true);
}


function get_schedule_queue_json()
{
    $search = grab_request_var('search', '');
    $filters = grab_request_var('filters', array());
    $records = grab_request_var('records', 0);
    $orderby = grab_request_var('orderby', array());
    $page = grab_request_var('page', 0);

    $pagination = array();

    // Get the queue
    $queue = get_schedule_queue();
    $pagination['total'] = count($queue);

    // If records are -1 then show all or get page data
    if ($records == -1) {
        $page_records_start = 0;
        $page_records_end = $pagination['total'];
        $pagination['pages'] = 1;
    } else {
        $pagination['pages'] = ceil($pagination['total']/$records);
        $page_records_start = (($page - 1) * $records);
        $page_records_end = ($page*$records);
        if ($page_records_end > $pagination['total']) {
            $page_records_end = $pagination['total'];
        }
    }

    $pagination['html'] = _("Showing") . ' ' . ($page_records_start + 1) . '-' . $page_records_end . ' ' . _('of') . ' ' . $pagination['total'] . ' ' . _('scheduled checks');

    // Do record set
    if ($records != -1) {
        $queue = array_slice($queue, $page_records_start, $records);
    }

    // Add pagination
    $data = array('objects' => $queue,
                  'pagination' => $pagination);

    print json_encode($data);
    exit();
}


function get_schedule_queue()
{
    $queue = array();

    // Get the queue output
    $html = coreui_do_proxy("extinfo.cgi", array("type" => 7), true);

    // Parse the DOM elements
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $tables = $dom->getElementsByTagName('table');
    foreach ($tables->item(2)->getElementsByTagName('tr') as $row) {
        $cols = $row->getElementsByTagName('td');

        // Check for header row
        if (empty($cols->item(0)->nodeValue)) {
            continue;
        }

        $obj = array();
        $obj['host_name'] = trim($cols->item(0)->nodeValue);
        $obj['service_description'] = str_replace('&nbsp;', '', trim(htmlentities($cols->item(1)->nodeValue, null, 'utf-8')));
        $obj['last_check'] = get_datetime_string(nstrtotime($cols->item(2)->nodeValue));
        $obj['next_check'] = get_datetime_string(nstrtotime($cols->item(3)->nodeValue));
        $obj['check_options'] = trim($cols->item(4)->nodeValue);
        $obj['active_checks'] = $cols->item(5)->nodeValue;

        $queue[] = $obj;
    }

    return $queue;
}
