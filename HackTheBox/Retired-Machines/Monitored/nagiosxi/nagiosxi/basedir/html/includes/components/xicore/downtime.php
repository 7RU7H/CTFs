<?php
//
// Scheduled Downtime
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);


route_request();


function route_request()
{
    licensed_feature_check();
    
    $cmd = grab_request_var("cmd", "");
    $type = grab_request_var("type", "");

    switch ($cmd) {

        case "cancel":
            cancel_downtime();
            break;

        case "schedule":
            if ($type == "hostgroup" || $type == "servicegroup") {
                show_add_group_downtime();
            } else {
                show_add_downtime();
            }
            break;

        case "submit":
            if ($type == "hostgroup" || $type == "servicegroup") {
                do_submit_group_downtime();
            } else {
                do_submit_downtime();
            }
            break;

        case "getservices":
            $host = grab_request_var("host", "");
            $args = array('brevity' => 1, 'host_name' => $host, 'orderby' => 'service_description:a');
            $oxml = get_xml_service_objects($args);
            if ($oxml) {
                foreach ($oxml->service as $serviceobj) {
                    $name = strval($serviceobj->service_description);
                    echo "<option value='" . $name . "'>$name</option>\n";
                }
            }
            break;

        case "getpageinfo":
            // Paging information
            $search = grab_request_var('search', '');
            $page = grab_request_var('page', 0);
            $records = grab_request_var("records", get_user_meta(0, 'downtime_recordlimit', 10));

            $total_records = get_downtimes(true);
            $pager_results = get_table_pager_info("", $total_records, $page, $records);
            $clear_args = array();
            $html = table_record_count_text($pager_results, $search, true, $clear_args, '', true);

            $data = array('html' => $html, 'total' => $total_records, 'pages' => $pager_results['total_pages']);
            print json_encode($data);
            break;

        case "getdowntimes":
            get_downtimes();
            break;

        default:
            show_downtime();
            break;

    }
}


function show_downtime()
{
    $host_dt = array();
    $service_dt = array();
    $in_effect_count = 0;

    $refresh = intval(get_user_meta(0, 'downtime_page_refresh', 30));
    if ($refresh < 1) {
        $refresh = 1;
    }

    $format = '';
    if (is_null($format = get_user_meta(0, 'date_format'))) { $format = get_option('default_date_format'); }

    $url = get_base_url()."includes/components/xicore/downtime.php";

    // Initial pager information for giving to JS / inital page load
    $search = grab_request_var('search', '');
    $page = intval(grab_request_var('page', 0));
    $records = intval(grab_request_var("records", get_user_meta(0, 'downtime_recordlimit', 10)));
    $total_records = get_downtimes(true);
    $pager_results = get_table_pager_info("", $total_records, $page, $records);

    do_page_start(array("page_title" => _('Scheduled Downtime'), 'jquery_plugins' => array('sumoselect')), true);
?>

<script type="text/javascript">
// Selected ids (to preserve when ajax calls happen)
var SEL_IDS = [];
var PAGE_TO_ID = 0;
var ORDER_BY = [];
var sem = 0;

var record_limit = <?php echo $pager_results['records_per_page']; ?>;
var current_page = <?php echo $pager_results['current_page']; ?>;
var total_records = <?php echo $pager_results['total_records']; ?>;
var total_pages = <?php echo $pager_results['total_pages']; ?>;

function get_datetime_string_dt(t)
{
    var format = parseInt('<?php echo $format; ?>');
    var formatted = '';
    var date = new Date(t);

    if (format == 2) {
        // MM/DD/YYYY HH:MM:SS
        formatted += ('0'+(date.getMonth()+1)).slice(-2)+'/';
        formatted += ('0'+date.getDate()).slice(-2)+'/';
        formatted += date.getFullYear()+' ';
        
    } else if (format == 3) {
        // DD/MM/YYYY HH:MM:SS
        formatted += ('0'+date.getDate()).slice(-2)+'/';
        formatted += ('0'+(date.getMonth()+1)).slice(-2)+'/';
        formatted += date.getFullYear()+' ';
    } else {
        // YYYY-MM-DD HH:MM:SS
        formatted += date.getFullYear()+'-';
        formatted += ('0'+(date.getMonth()+1)).slice(-2)+'-';
        formatted += ('0'+date.getDate()).slice(-2)+' ';
    }

    formatted += ('0'+date.getHours()).slice(-2)+':';
    formatted += ('0'+date.getMinutes()).slice(-2)+':';
    formatted += ('0'+date.getSeconds()).slice(-2);

    return formatted;
}

function get_duration_dt(seconds)
{
    x = seconds;
    seconds = Math.floor(seconds % 60);
    x /= 60;
    minutes = Math.floor(x % 60);
    x /= 60;
    hours = Math.floor(x % 24);
    x /= 24;
    days = Math.floor(x);

    var formatted = '';
    if (days != 0) { formatted += days + 'd '; }
    if (days > 0 || hours != 0 ) { formatted += hours + 'h '; }
    if (minutes > 0) { formatted += minutes + 'm '; }
    if (seconds > 0) { formatted += seconds + 's'; }

    return formatted;
}

function get_downtimes(force)
{
    var data = { records: $('.num-records').val(), page: current_page };

    // Grab data from search-holder (once search is done, it's placed here until deleted)
    if ($('#search-holder').val() != '') {
        data.search = $('#search-holder').val();
    }

    // Grab ORDER BY
    if (ORDER_BY != '') {
        data.orderby = ORDER_BY;
    }

    // Don't continue if we are currently changing sumoselect
    if ($('.SumoSelect .optWrapper').is(':visible') && !force) {
        return;
    }

    // Grab filters
    if ($('select[name="filters"]').val() != '') {
        data.filters = $('select[name="filters"]').val();
    }

    $('.refresh').html('<i class="fa fa-spinner fa-14 fa-spin"></i>');
    sem = 1;
    setTimeout('loading_data()', 500);

    // Update page info
    data.cmd = 'getpageinfo';
    $.ajax({
        type: "GET",
        url: '<?php echo $url; ?>',
        data: data,
        dataType: 'json',
        success: function(data) {
            $('#paging-info').html(data.html);
            total_pages = data.pages;
            total_records = data.total;
            update_pages(false);
        }
    });

    data.cmd = 'getdowntimes';
    $.ajax({
        type: "GET",
        url: '<?php echo $url; ?>',
        data: data,
        dataType: 'json',
        success: function(dt) {
            sem = 0;

            var dt_hosts = '';
            var dt_services = '';
            var dt_in_effect = '';
            var dt_hosts_count = 0;
            var dt_services_count = 0;
            var dt_in_effect_count = 0;
            var html_table = '';

            if (Object.keys(dt).length > 0) {
                $.each(dt, function(k, d) {

                    // Add checkboxes to preserve checked objects during ajax calls
                    var checked = '';
                    var htc = '';
                    if ($.inArray(parseInt(d.downtime_id), SEL_IDS) >= 0) {
                        htc = ' highlight';
                        checked = ' checked';
                    }

                    html = '<tr class="dt-' + d.downtime_id + htc + '">';
                    html += '<td><input type="checkbox" class="dt-check" data-id="' + d.downtime_id + '"' + checked + '></td>';
                    html += '<td>'+d.host_name+'</td>';
                    
                    if (d.service_description != undefined) {
                        html += '<td>'+d.service_description+'</td>';
                    } else {
                        html += '<td>-</td>';
                    }

                    html += '<td>'+get_datetime_string_dt(d.entry_time)+'</td>';
                    html += '<td>'+d.author+'</td>';
                    html += '<td>'+d.comment+'</td>';
                    html += '<td>'+get_datetime_string_dt(d.start_time)+'</td>';
                    html += '<td>'+get_datetime_string_dt(d.end_time)+'</td>';

                    if (d.fixed == 1) {
                        html += '<td><?php echo _("Fixed"); ?></td>';
                    } else {
                        html += '<td><?php echo _("Flexible"); ?></td>';
                    }

                    html += '<td>'+get_duration_dt(d.duration)+'</td>';
                    html += '<td>'+d.downtime_id+'</td>';

                    if (d.triggered_by == 0) { d.triggered_by = "<?php echo _('None'); ?>"; }
                    html += '<td>'+d.triggered_by+'</td>';

                    if (d.is_in_effect == 1) { d.is_in_effect = "<?php echo _('Yes'); ?>"; } else { d.is_in_effect = "<?php echo _('No'); ?>" }
                    html += '<td>'+d.is_in_effect+'</td>';

                    html += '<td class="center"><a class="remove dt-tt-bind" data-downtime-type="'+d.type+'" data-downtime-id="'+d.downtime_id+'" data-placement="left" title="<?php echo _("Remove"); ?>"><img src="<?php echo theme_image("cross.png"); ?>"></a></td>';

                    html += '</tr>';
                    html_table += html;
                });
            }

            if (html_table == '') {
                html_table = '<tr><td colspan="14"><?php echo _("No scheduled downtime results matching your current filters."); ?></td></tr>';
            }

            $('#dt').html(html_table);
            $('#loadscreen').hide();
            $('#loadscreen-spinner').hide();

            $('.dt-tt-bind').tooltip();

            $('.refresh').html('<img src="<?php echo theme_image("arrow_refresh.png"); ?>">');
        }
    });
}

function close_confirm_removal() {
    clear_whiteout();
    $('#confirm-rd').hide();
    $('#confirm-rd .list-box').hide();
    $('#confirm-rd tbody.list').html('');
    $('.btn-confirm').button('reset');
    $('.btn-cancel').prop('disabled', false);
}

function remove_downtime(downtime_id, downtime_type, remove_from_sel) {
    var type = 78;

    if (downtime_type == 1) {
        type = 79;
    }

    var args = {
        nsp: '<?php echo get_nagios_session_protector_id(); ?>',
        cmd_typ: type,
        cmd_mod: 2,
        down_id: downtime_id,
        username: '<?php echo $_SESSION["username"]; ?>'
    }

    // Add host name and/or service name for audit log purposes
    var tds = $('.dt-' + downtime_id).children();
    var hostname = $(tds[1]).text();
    var servicename = $(tds[2]).text();
    args['host'] = hostname;
    if (servicename != '-' && servicename != '') {
        args['service'] = servicename;
    }

    $.post('<?php echo get_base_url()."includes/components/nagioscore/ui/cmd.php"; ?>', args, function(data) {
        $('.dt-' + downtime_id).remove();
        if (remove_from_sel) {
            SEL_IDS.splice(SEL_IDS.indexOf(downtime_id), 1);
        }
    });
}

function reset_selections() {
    SEL_IDS = [];
    $('.check-all').prop('checked', false);
    $('.dt-check:checked').prop('checked', false);
    $('tr').removeClass('highlight');
}

function loading_data()
{
    if (sem == 1) {
        var h = $('.table-downtime').outerHeight();
        var w = $('.table-downtime').outerWidth();
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

    get_downtimes();
    PAGE_TO_ID = setInterval(get_downtimes, <?php echo intval($refresh) * 1000; ?>);

    $('.refresh').click(function() {
        get_downtimes(true);
    });

    $('.table-downtime').on('click', '.remove', function() {
        var downtime_id = $(this).data('downtime-id');
        var downtime_type = $(this).data('downtime-type');
        remove_downtime(downtime_id, downtime_type);
    });

    $('.do-for-selected').change(function() {
        if ($(this).val() == 'remove') {
            if (SEL_IDS.length > 0) {

                // Display popup for deletion
                //console.log(SEL_IDS);
                whiteout();
                $('#confirm-rd-count').html(SEL_IDS.length);

                // Populate confirmation
                $('#confirm-rd tbody.list').html('');
                $(SEL_IDS).each(function(i, id) {
                    var row = $('.table-downtime .dt-' + id).clone();
                    row.find('td:first').remove();
                    row.find('td:nth-child(3)').remove();
                    row.find('td:last').remove();
                    row.find('td:last').remove();
                    row.find('td:last').remove();
                    row.find('td:last').remove();
                    row.removeClass('highlight');
                    $('#confirm-rd tbody.list').append(row);
                });

                // Show remove confirmation
                var rd_width = $('#confirm-rd').outerWidth();
                var width = $(window).width();
                if (width > 1000) {
                    rd_width = width * 0.65;
                } else {
                    rd_width = width - 200;
                }
                if (rd_width < 400) {
                    rd_width = width;
                }
                $('#confirm-rd .list-box').data('max-width', rd_width-62);
                $('#confirm-rd').css('max-width', rd_width).show();

                if ($("body").height() > $(window).height()) {
                    $('#confirm-rd').center();
                } else {
                    $('#confirm-rd').center().css('top', 160);
                }

            }
            $(this).val('');
        }
    });

    // Table checkboxes
    $('.table-downtime').on('click', 'tr td:not(:first-child, :last-child)', function() {
        var checkbox = $(this).parent().find('input[type="checkbox"]');
        if (checkbox.is(':checked')) {
            checkbox.prop('checked', false);
            $(this).parent().removeClass('highlight');
            SEL_IDS.splice(SEL_IDS.indexOf(checkbox.data('id')), 1);
        } else {
            checkbox.prop('checked', true);
            $(this).parent().addClass('highlight');
            SEL_IDS.push(checkbox.data('id'));
        }
    });

    $('.table-downtime').on('click', '.dt-check', function() {
        if (!$(this).is(':checked')) {
            $(this).parent().parent().removeClass('highlight');
            SEL_IDS.splice(SEL_IDS.indexOf($(this).data('id')), 1);
            $(this).prop('checked', false);
        } else {
            $(this).parent().parent().addClass('highlight');
            SEL_IDS.push($(this).data('id'));
            $(this).prop('checked', true);
        }
    });

    $('.check-all').click(function() {
        var check_all = false;
        if ($(this).is(':checked')) {
            check_all = true;
        }
        $('.dt-check').each(function(i, obj) {
            if ($(obj).is(':checked')) {
                if (!check_all) {
                    $(obj).prop('checked', false);
                    $(obj).parents('tr').removeClass('highlight');
                    SEL_IDS.splice(SEL_IDS.indexOf($(obj).data('id')), 1);
                }
            } else {
                if (check_all) {
                    $(obj).prop('checked', true);
                    $(obj).parents('tr').addClass('highlight');
                    SEL_IDS.push($(obj).data('id'));
                }
            }
        });      
    });

    $('.table-downtime').on('click', '.orderby', function() {
        $('.table-downtime .orderby .fa').remove();
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
        get_downtimes(true);
    });

    $('#search-form').submit(function(e) {
        e.preventDefault();
        if ($('#search').val() == '') {
            $('#search-holder').val('');
        } else {
            $('#search-holder').val($('#search').val());
        }
        get_downtimes(true);
    });

    $('body').on('click', '.clear-search', function() {
        $('#search').val('');
        $('#search-holder').val('');
        $(this).hide();
        get_downtimes(true);
    });

    // Confirm Remove Downtime

    $('#confirm-rd').on('click', '.btn-cancel', function() {
        clear_whiteout();
        $('#confirm-rd').hide();
        $('#confirm-rd .list-box').hide();
        $('#confirm-rd tbody.list').html('');
    });

    $('#confirm-rd').on('click', '.show-rds', function() {
        if (!$('#confirm-rd .list').is(':visible')) {
            var w = $('#confirm-rd .list-box').data('max-width');
            $('#confirm-rd .list-box').css('width', w);
            $(this).html("<?php echo _('Hide selected'); ?>");
            $('#confirm-rd .list-box').show();
            if ($("body").height() > $(window).height()) {
                $('#confirm-rd').center();
            } else {
                $('#confirm-rd').center().css('top', 160);
            }
        } else {
            $('#confirm-rd .list-box').css('width', 'auto');
            $(this).html("<?php echo _('Show selected'); ?>");
            $('#confirm-rd .list-box').hide();
            if ($("body").height() > $(window).height()) {
                $('#confirm-rd').center();
            } else {
                $('#confirm-rd').center().css('top', 160);
            }
        }
    });

    $('#confirm-rd').on('click', '.btn-confirm', function() {
        $('.btn-confirm').button('loading');
        $('.btn-cancel').prop('disabled', true);
        $(SEL_IDS).each(function(i, id) {
            var type = $('.table-downtime .dt-' + id).find('a.remove').data('downtime-type');
            remove_downtime(id, type, true);
        });

        // Wait for selected IDs to be removed or forcefully exit > 10 seconds
        var lid = setInterval(function() {
            if (SEL_IDS.length == 0) {
                close_confirm_removal();
                clearTimeout(lid);
            }
        }, 1000);

    });

    // Add sumoselect to filters selection
    $('.filters').SumoSelect({
        placeholder: "<?php echo _('Select filters'); ?>",
        captionFormatAllSelected: "<?php echo _('All scheduled downtimes'); ?>",
        triggerChangeCombined: true,
        okCancelInMulti: true
    });
    $('.sumo-hide').removeClass('sumo-hide');

    $('.filters').change(function() {
        get_downtimes(true);
    });

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
    });

    // Page-specific settings functions

    if ($('#open-dtform').length > 0) {
        var p = $('#open-dtform').position();
        var left = Math.floor(p.left+13) - ($('#settings-dropdown').outerWidth()/2);
        var top = Math.floor(p.top+22);
        $('#settings-dropdown').css('left', left+'px');
        $('#settings-dropdown').css('top', top+'px');

        $("#open-dtform").click(function () {
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
            PAGE_TO_ID = setInterval(get_downtimes, rval);

            // Set refresh value in user meta
            var optsarr = {
                "keyname": "downtime_page_refresh",
                "keyvalue": refresh
            };
            var opts = JSON.stringify(optsarr);
            var result = get_ajax_data("setusermeta", opts);
        }
        $("#settings-dropdown").fadeOut("fast");
    });

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
        get_downtimes(true);
    }
}
</script>

<div class="well report-options">
    <div class="fl" style="height: 29px; margin-right: 30px; margin-bottom: 15px;">
        <h1><?php echo _('Scheduled Downtime'); ?></h1>
        <a class="tt-bind refresh" data-placement="right" title="<?php echo _('Refresh'); ?>"><img src="<?php echo theme_image('arrow_refresh.png'); ?>"></a>
        <a class="tt-bind settings-dropdown" id="open-dtform" data-placement="right" title="<?php echo _('Edit page settings'); ?>"><img src="<?php echo theme_image('cog.png'); ?>"></a>
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
    <div class="input-group" style="width: 240px; display: inline-table;">
        <label class="input-group-addon sumo-hide"><?php echo _('Filter by'); ?></label>
        <select class="filters hide" name="filters" multiple>
            <option value="host"><?php echo _("Host"); ?></option>
            <option value="service"><?php echo _("Service"); ?></option>
            <option value="ineffect"><?php echo _("In effect"); ?></option>
            <option value="notineffect"><?php echo _("Not in effect"); ?></option>
        </select>
    </div>
    <div class="fr">
        <form action="" method="get" id="search-form" style="margin: 0;">
            <input type="text" id="search" class="form-control" placeholder="<?php echo _('Search'); ?>..." style="vertical-align: top; border-right: 0;"><button type="submit" class="btn btn-sm btn-default tt-bind" data-placement="bottom" title="<?php echo _('Search'); ?>"><i class="fa fa-search"></i></button>
            <input type="hidden" id="search-holder" value="<?php echo encode_form_val($search); ?>">
        </form>
    </div>
    <div class="clear"></div>
</div>

<div style="padding: 0 0 10px 0;">
    <div class="fl">
        <?php if (!is_readonly_user(0)) { ?>
        <div class="btn-group fl" style="margin-right: 20px;">
            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-clock-o l"></i> <?php echo _('Schedule Downtime'); ?> <span class="caret" style="margin-left: 3px;"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="?cmd=schedule&type=host"><?php echo _('For Hosts'); ?></a></li>
                <li><a href="?cmd=schedule&type=service"><?php echo _('For Services'); ?></a></li>
                <li><a href="?cmd=schedule&type=hostgroup"><?php echo _('For Host Groups'); ?></a></li>
                <li><a href="?cmd=schedule&type=servicegroup"><?php echo _('For Service Groups'); ?></a></li>
            </ul>
        </div>
        <?php } ?>
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

<div id="confirm-rd" class="xi-modal hide">
    <h2><?php echo _('Confirm: Remove Scheduled Downtime'); ?></h2>
    <p><?php echo _('You have selected to remove multiple scheduled downtimes. This action cannot be undone.'); ?></p>
    <div style="background-color: #FFF;">
        <table class="table table-striped table-condensed table-bordered table-no-margin">
            <thead>
                <tr>
                    <th style="border-bottom-width: 1px;">
                        <div class="fl"><span id="confirm-rd-count">0</span> <?php echo _('downtimes selected'); ?></div>
                        <div class="fr"><a class="show-rds"><?php echo _('Show selected'); ?></a></div>
                        <div class="clear"></div>
                    </th>
                </tr>
            </thead>
        </table>
        <div class="list-box hide" data-max-width="" style="max-height: 286px; border-left: 1px solid #DDD; border-right: 1px solid #DDD; border-bottom: 1px solid #DDD; overflow-y: auto;">
            <table class="table table-striped table-hover table-bordered table-condensed table-no-margin" style="border: 0;">
                <tbody class="list">
                </tbody>
            </table>
        </div>
    </div>
    <div style="padding-top: 20px;">
        <button type="button" data-loading-text="<?php echo _('Removing...'); ?>" class="btn-confirm btn btn-sm btn-primary"><?php echo _('Continue'); ?></button>
        <button type="button" class="btn-cancel btn btn-sm btn-default"><?php echo _('Cancel'); ?></button>
    </div>
</div>

<div style="min-height: 100px;">
    <div id="loadscreen" class="hide"></div>
    <div id="loadscreen-spinner" class="sk-spinner sk-spinner-rotating-plane hide"></div>
    <table class="table table-condensed table-hover table-downtime table-bordered table-striped table-no-margin" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 24px;"><input type="checkbox" class="check-all tt-bind" data-tab-id="in-effect" title="<?php echo _('Toggle all'); ?>"></th>
                <th><span class="orderby" data-orderby="host_name"><?php echo _('Host'); ?></span></th>
                <th><span class="orderby" data-orderby="service_description"><?php echo _('Service'); ?></span></th>
                <th class="time-fw"><span class="orderby" data-orderby="entry_time"><?php echo _('Entry Time'); ?></span></th>
                <th><span class="orderby" data-orderby="author_name"><?php echo _('Author'); ?></span></th>
                <th><?php echo _('Comment'); ?></th>
                <th class="time-fw"><span class="orderby" data-orderby="scheduled_start_time"><?php echo _('Start Time'); ?></span></th>
                <th class="time-fw"><span class="orderby" data-orderby="scheduled_end_time"><?php echo _('End Time'); ?></span></th>
                <th><?php echo _('Type'); ?></th>
                <th><?php echo _('Duration'); ?></th>
                <th><?php echo _('Downtime ID'); ?></th>
                <th><span class="orderby" data-orderby="triggered_by"><?php echo _('Trigger ID'); ?></span></th>
                <th><?php echo _('In Effect'); ?></th>
                <th style="width: 60px;" class="center"><?php echo _('Actions'); ?></th>
            </tr>
        </thead>
        <tbody id="dt">
        </tbody>
    </table>
</div>

<div>
    <div class="fl" style="margin: 20px 0;">
        <div class="input-group" style="width: 200px;">
            <label class="input-group-addon"><?php echo _('With selected'); ?></label>
            <select class="form-control do-for-selected">
                <option></option>
                <option value="remove"><?php echo _('Remove'); ?></option>
            </select>
        </div>
    </div>
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

<?php
    do_page_end(true);
}


function show_add_downtime($error = '')
{
    $type = grab_request_var('type', 'host');
    if ($type != 'host' && $type != 'service') { $type = 'host'; }

    $com_data = grab_request_var('com_data', '');
    $trigger = grab_request_var('trigger', 0);
    $start_time = grab_request_var('start_time', get_datetime_string(time()));
    $end_time = grab_request_var('end_time', get_datetime_string(strtotime('now + 2 hours')));
    $fixed = grab_request_var('fixed', 1);
    $hours = grab_request_var('hours', 2);
    $minutes = grab_request_var('minutes', 0);
    $childoptions = grab_request_var('childoptions', 0);
    $hosts = grab_request_var('hosts', array());
    $services = grab_request_var('services', array());

    $total_services = 0;
    foreach ($services as $host_services) {
        foreach ($host_services as $service) {
            $total_services++;
        }
    }

    // Get timezone datepicker format
    if (isset($_SESSION['date_format']))
        $format = $_SESSION['date_format'];
    else {
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

    do_page_start(array("page_title" => _('Scheduled Downtime')), true);
?>

<script type="text/javascript">

function select_hosts() {
    $('.hosts option:selected').each(function(k, v) {
        if ($(v).css('display') != 'none') {
            var hostname = $(v).val();
            $('.selected-hosts').append('<tr><td>' + hostname + '<input type="hidden" name="hosts[]" value="' + hostname + '"> <img style="float: right; cursor: pointer;" rel="tooltip" class="remove-host" data-placement="left" title="<?php echo _("Remove") ?>" src="<?php echo theme_image("cross.png"); ?>"><div class="clear"></div></td></tr>');
            $(v).hide();
        }
    });

    // Verify there are hosts or not and hide "no hosts" message
    if ($('.selected-hosts tr').length > 1) {
        $('.selected-hosts .no-hosts').hide();
        $('#num-selected-hosts').html($('.selected-hosts tr').length - 1);
    }
}

function select_services() {
    var host = $('.filter-services-by-host').val();
    $('.services option:selected').each(function(k, v) {
        if ($(v).css('display') != 'none') {
            var service = $(v).val();
            $('.selected-services').append('<tr><td>' + host + ' :: ' + service + '<input type="hidden" name="services[' + host + '][]" value="' + service + '"> <img style="float: right; cursor: pointer;" rel="tooltip" class="remove-service" data-placement="left" title="<?php echo _("Remove") ?>" src="<?php echo theme_image("cross.png"); ?>"><div class="clear"></div></td></tr>');
            $(v).hide();
        }
    });

    // Verify there are services or not and hide "no services" message
    if ($('.selected-services tr').length > 1) {
        $('.selected-services .no-services').hide();
        $('#num-selected-services').html($('.selected-services tr').length - 1);
    }
}

// Hide the selected ones from the list if they are in it
function hide_selected_services() {
    var host = $('.filter-services-by-host').val();
    if ($('.selected-services tr input[name="services[' + host + '][]"]').length > 0) {
        $('.selected-services tr input[name="services[' + host + '][]"]').each(function(i, v) {
            var service = $(v).val();
            $('.services option[value="' + service + '"]').hide();
        });
    }
}

$(document).ready(function() {

    // Get first set of hosts
    var host = $('.filter-services-by-host').val();
    $.get(base_url + '/includes/components/xicore/downtime.php?cmd=getservices&host='+host, function(data) {
        $('.services').html(data);
        hide_selected_services();
    });

    $('.filter-services-by-host').searchable({ maxMultiMatch: 9999 });

    $('.selected-hosts').on('click', '.remove-host', function() {

        // Add back in as selectable
        $('.hosts option[value="'+ $(this).closest('td').find('input').val() +'"]').show().attr('selected', true);

        // Remove from the list
        $(this).tooltip('destroy');
        $(this).closest('tr').remove();

        // Verify there are hosts or not and hide "no hosts" message
        $('#num-selected-hosts').html($('.selected-hosts tr').length - 1);
        if ($('.selected-hosts tr').length == 1) {
            $('.selected-hosts .no-hosts').show();
        } else {
            $('.selected-hosts .no-hosts').hide();
        }
    });

    $('.selected-services').on('click', '.remove-service', function() {

        // Add back into selectable if option exists
        var host = $('.filter-services-by-host').val();
        var service = $(this).closest('td').find('input').val();
        var tr = $(this).closest('tr');
        if (tr.find('input[name="services[' + host + '][]"]').length == 1) {
            $('.services option[value="' + service + '"]').show().attr('selected', true);;
        }

        // Remove from the list
        $(this).tooltip('destroy');
        $(this).closest('tr').remove();

        // Verify there are services or not and hide "no services" message
        $('#num-selected-services').html($('.selected-services tr').length - 1);
        if ($('.selected-services tr').length > 1) {
            $('.selected-services .no-services').hide();
        } else {
            $('.selected-services .no-services').show();
        }
    });

    $('.remove-all').click(function() {
        $('.remove-host').each(function(k, v) {
            $(v).trigger('click');
        });
        $('.remove-service').each(function(k, v) {
            $(v).trigger('click');
        });
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

    $('#fixed').change(function() {
        if ($(this).val() == 0) {
            $('#flexible-box').show();
        } else {
            $('#flexible-box').hide();
        }
    });

    $('form').submit(function() {
        whiteout();
        show_throbber();
    })

    $('.filter-services-by-host').change(function() {
        var host = $(this).val();
        $.get(base_url + '/includes/components/xicore/downtime.php?cmd=getservices&host='+host, function(data) {
            $('.services').html(data);
            hide_selected_services();
        });
    });

    // Selecting hosts
    $('.hosts').filterByText($('#filter-hosts'), true);
    $('.hosts').dblclick(function() { select_hosts(); });
    $('.btn-select-hosts').click(function() { select_hosts(); });

    // Selecting services
    $('.services').dblclick(function() { select_services(); });
    $('.btn-select-services').click(function() { select_services(); });

    // Info popup

    $('.info-popup.info .btn-close').click(function() {
        clear_whiteout();
        $('.info-popup.info').hide();
    });

    $(window).resize(function() {
        $('.info-popup.info').center().css('top', '140px');
    });
    
    $('.import-info').click(function() {
        whiteout();
        $('.info-popup.info').show().center().css('top', '140px');
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

});
</script>

<h1><?php echo _('Scheduled Downtime'); ?></h1>

<h2><?php echo _('Add') . ' ' . ucfirst($type) . ' ' . _('Downtime'); ?></h2>

<?php
if (!empty($error)) {
    echo '<div class="message"><ul class="errorMessage" style="margin-top: 0;"><li><i class="fa fa-exclamation-triangle"></i> <strong>'._('Error').'</strong>: '.$error.'</li></ul></div>';
}
?>

<?php get_downtime_page_explanation($type); ?>

<style type="text/css">
/* Special styles for downtime selection box only */
.selected-hosts-box, .selected-services-box { border: 1px solid #DDD; border-top: 0; max-height: 270px; overflow-y: auto; }
.selected-hosts tr:nth-of-type(1) > td, .selected-hosts tr:nth-of-type(2) > td, .selected-services tr:nth-of-type(1) > td, .selected-services tr:nth-of-type(2) > td  { border: 0; }
.table.table-no-border>tbody>tr>td { padding: 8px 0; }
</style>

<form action="?cmd=submit" method="post">
    <input type="hidden" name="type" value="<?php echo $type; ?>">
    <table class="table table-auto-width table-no-border" style="width: 50%; min-width: 800px;">
        <?php if ($type == 'host') { ?>
        <tr>
            <td colspan="2">
                <?php echo _('Host Name(s)'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="well" style="margin: 0; min-height: 340px;">
                    <div style="width: 50%; padding-right: 20px;" class="fl hosts-select">
                        <div><input type="text" id="filter-hosts" class="form-control" style="border-bottom: none; width: 100%;" placeholder="<?php echo _('Filter'); ?>..."></div>
                        <div>
                            <select class="form-control hosts" style="height: 237px; width: 100%;" multiple>
                                <?php
                                $args = array('brevity' => 1, 'orderby' => 'host_name:a');
                                $oxml = get_xml_host_objects($args);
                                if ($oxml) {
                                    foreach ($oxml->host as $hostobject) {
                                        $name = strval($hostobject->host_name);
                                        $style = '';
                                        if (in_array($name, $hosts)) {
                                            $style = "style='display: none;'";
                                        }
                                        echo "<option value='" . $name . "' ".$style.">$name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin-top: 5px;">
                            <button type="button" class="btn btn-select-hosts btn-sm btn-primary"><?php echo _('Add Selected'); ?> <i class="fa fa-chevron-right fa-r"></i></button>
                        </div>
                    </div>
                    <div style="width: 50%;" class="fr hosts-selected">
                        <div>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php echo _('Selected Hosts'); ?> <span id="num-selected-hosts" style="vertical-align: text-top; font-size: 9px; display: inline-block;" class="label label-default"><?php echo count($hosts); ?></span>
                                            <div class="fr"><a class="remove-all"><?php echo _('Remove All'); ?></a></div>
                                            <div class="clear"></div>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="selected-hosts-box">
                                <table class="table table-condensed">
                                    <tbody class="selected-hosts">
                                        <?php
                                        $display = '';
                                        if (count($hosts) > 0) { $display = ' style="display:none;"'; }
                                        ?>
                                        <tr class="no-hosts"<?php echo $display; ?>>
                                            <td><?php echo _('No hosts selected'); ?></td>
                                        </tr>
                                        <?php
                                        if (count($hosts) > 0) {
                                            foreach ($hosts as $h) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $h; ?>
                                                    <input type="hidden" name="hosts[]" value="<?php echo $h; ?>">
                                                    <img style="float: right; cursor: pointer;" class="tt-bind remove-host" data-placement="left" title="<?php echo _('Remove') ?>" src="<?php echo theme_image('cross.png'); ?>">
                                                    <div class="clear"></div>
                                                </td>
                                            </tr>
                                        <?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <?php } else { ?>
        <tr>
            <td colspan="2">
                <?php echo _('Service Name(s)'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php
                // Set object type based on if the user can see all objects or not
                $objtype = OBJECTTYPE_SERVICE;
                if (is_authorized_for_all_objects()) {
                    $objtype = OBJECTTYPE_HOST;
                }

                // Get list of hosts the user can see
                $host_array = array();
                $args = array('orderby' => 'name1:a', 'objecttype_id' => $objtype);
                $oxml = get_xml_objects($args);
                foreach ($oxml->object as $obj) {
                    $h = strval($obj->name1);
                    if (!in_array($h, $host_array)) {
                        $host_array[] = $h;
                    }
                }
                ?>
                <div class="well" style="margin: 0; min-height: 340px;">
                    <div style="width: 50%; padding-right: 20px;" class="fl hosts-select">
                        <div>
                            <select class="form-control filter-services-by-host" data-full-width="1">
                                <?php
                                foreach ($host_array as $h) {
                                    $style = '';
                                    if (in_array($h, $hosts)) {
                                        $style = "style='display: none;'";
                                    }
                                    echo "<option value='" . $h . "' ".$style.">$h</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <select class="form-control services" style="height: 237px; width: 100%; border-top: 0;" multiple></select>
                        </div>
                        <div style="margin-top: 5px;">
                            <button type="button" class="btn btn-select-services btn-sm btn-primary"><?php echo _('Add Selected'); ?> <i class="fa fa-chevron-right fa-r"></i></button>
                        </div>
                    </div>
                    <div style="width: 50%;" class="fr services-selected">
                        <div>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php echo _('Selected Services'); ?> <span id="num-selected-services" style="vertical-align: text-top; font-size: 9px; display: inline-block;" class="label label-default"><?php echo $total_services; ?></span>
                                            <div class="fr"><a class="remove-all"><?php echo _('Remove All'); ?></a></div>
                                            <div class="clear"></div>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="selected-services-box">
                                <table class="table table-condensed">
                                    <tbody class="selected-services">
                                        <?php
                                        $display = '';
                                        if (count($services) > 0) { $display = ' style="display:none;"'; }
                                        ?>
                                        <tr class="no-services"<?php echo $display; ?>>
                                            <td><?php echo _('No services selected'); ?></td>
                                        </tr>
                                        <?php
                                        if (count($services) > 0) {
                                            foreach ($services as $host => $host_services) {
                                                foreach ($host_services as $service) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $host . ' :: '. $service; ?>
                                                    <input type="hidden" name="services[<?php echo $host; ?>][]" value="<?php echo encode_form_val($service); ?>">
                                                    <img style="float: right; cursor: pointer;" class="tt-bind remove-service" data-placement="left" title="<?php echo _('Remove') ?>" src="<?php echo theme_image('cross.png'); ?>">
                                                    <div class="clear"></div>
                                                </td>
                                            </tr>
                                        <?php } } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td style="width: 100px;"><?php echo _('Author'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" name="com_author" value="<?php echo get_user_attr(0, 'name'); ?>" class="form-control" readonly></td>
        </tr>
        <tr>
            <td><?php echo _('Comment'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control" name="com_data" style="width: 400px;" value="<?php echo encode_form_val($com_data); ?>"></td>
        </tr>
        <tr>
            <td><?php echo _('Triggered By'); ?></td>
            <td>
                <?php
                $downtimes = get_scheduled_downtime();
                ?>
                <select class="form-control" name="trigger" id="trigger">
                    <option value="0" selected><?php echo _('None'); ?></option>
                    <?php
                    $options = '';
                    $s = '';
                    $h = '';

                    foreach ($downtimes as $dt) { 
                        if (!empty($dt['service_description'])) {
                            $s .= '<option value="' . $dt['internal_downtime_id'] . '">' . $dt['host_name'] . ' - '. $dt['service_description'] .' @ ' . get_datetime_string(strtotime($dt['scheduled_start_time'])) . ' (ID ' . $dt['internal_downtime_id'] . ')</option>';
                        } else {
                            $h .= '<option value="' . $dt['internal_downtime_id'] . '">' . $dt['host_name'] . ' @ ' . get_datetime_string(strtotime($dt['scheduled_start_time'])) . ' (ID ' . $dt['internal_downtime_id'] . ')</option>';
                        }
                    }

                    if (!empty($h)) {
                        $options .= '<optgroup label="'._('Host Downtimes').'">' . $h . '</optgroup>';
                    }

                    if (!empty($s)) {
                        $options .= '<optgroup label="'._('Service Downtimes').'">' . $s . '</optgroup>';
                    }

                    echo $options;
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo _('Type'); ?></td>
            <td>
                <select id="fixed" name="fixed" class="form-control">
                    <option value="1"><?php echo _('Fixed'); ?></option>
                    <option value="0"><?php echo _('Flexible'); ?></option>
                </select>
            </td>
        </tr>
        <tr id="flexible-box" class="hide">
            <td><?php echo _('Duration'); ?></td>
            <td>
                <input type="text" class="form-control" name="hours" style="width: 40px;" id="flexible-hours" value="2"> Hours
                <input type="text" class="form-control" name="minutes" style="width: 40px; margin-left: 5px;" id="flexible-minutes" value="0"> Minutes
            </td>
        </tr>
        <tr>
            <td><?php echo _("Start Time"); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <input class="form-control datetimepicker req" style="width: 150px;" type="text" id='startdateBox' name="start_time" value="<?php echo encode_form_val($start_time); ?>" size="16">
            </td>
        </tr>
        <tr>
            <td><?php echo _("End Time"); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <input class="form-control datetimepicker req" style="width: 150px;" type="text" id='enddateBox' name="end_time" value="<?php echo encode_form_val($end_time); ?>" size="16">
            </td>
        </tr>
        <tr <?php if ($type != "host") { echo 'class="hide"'; } ?>>
            <td><?php echo _('Child Hosts'); ?></td>
            <td>
                <select id="childoptions" name="childoptions" class="form-control req">
                    <option value="0"><?php echo _('Do nothing with child hosts'); ?></option>
                    <option value="1"><?php echo _('Schedule triggered downtime for all child hosts'); ?></option>
                    <option value="2"><?php echo _('Schedule non-triggered downtime for all child hosts'); ?></option>
                </select>
            </td>
        </tr>
        <tr <?php if ($type != "host") { echo 'class="hide"'; } ?>>
            <td><?php echo _('Services'); ?></td>
            <td>
                <select id="serviceoptions" name="serviceoptions" class="form-control req">
                    <option value="0"><?php echo _('Do nothing with associated services'); ?></option>
                    <option value="1"><?php echo _('Schedule downtime for all associated services'); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Schedule'); ?></button>
    <a href="downtime.php" class="btn btn-sm btn-default"><?php echo _('Cancel'); ?></a>
</form>

<?php
    do_page_end(true);
}

function do_submit_downtime()
{
    $type = grab_request_var('type', 'host');
    $hosts = grab_request_var('hosts', array());
    $services = grab_request_var('services', array());
    $com_data = trim(grab_request_var('com_data', ''));
    $trigger_id = grab_request_var('trigger', 0);
    $start_time = grab_request_var('start_time', '');
    $end_time = grab_request_var('end_time', '');
    $fixed = grab_request_var('fixed', 1);
    $hours = intval(grab_request_var('hours', 2));
    $minutes = intval(grab_request_var('minutes', 0));
    $childoptions = grab_request_var('childoptions', 0);
    $serviceoptions = grab_request_var('serviceoptions', 0);
    $errors = 0;
    $output = "";

    // Verify permissions to be able to add downtimes
    if (is_readonly_user(0)) {
        exit('Read only users cannot submit downtimes.');
    }

    // Do checks to verify if hosts/service are selected
    if ($type == 'host') {
        if (empty($hosts)) {
            flash_message(_('You must select at least one host to apply downtime to.'), FLASH_MSG_ERROR);
            show_add_downtime();
            return;
        }
    } else {
        if (empty($services)) {
            flash_message(_('You must select at least one service to apply downtime to.'), FLASH_MSG_ERROR);
            show_add_downtime();
            return;
        }
    }

    // Check for errors
    if (empty($com_data) || empty($start_time) || empty($end_time)) {
        flash_message(_("You must fill out the entire form."), FLASH_MSG_ERROR);
        show_add_downtime();
        return;
    }

    // Check to make sure start_time is not before end_time
    $start_time = nstrtotime($start_time);
    $end_time = nstrtotime($end_time);
    if ($start_time > $end_time) {
        flash_message(_('You must select a start time that is before end time.'), FLASH_MSG_ERROR);
        show_add_downtime();
        return;
    }

    // Generate command arguments
    $args = array("comment_data" => $com_data,
                  "comment_author" => get_user_attr(0, 'name'),
                  "trigger_id" => $trigger_id,
                  "start_time" => $start_time,
                  "end_time" => $end_time,
                  "fixed" => $fixed);

    // If flexible, add a duration value
    if ($fixed == 0) {
        $args['duration'] = ($hours * 60 * 60) + ($minutes * 60);
    }

    if ($type == 'host') {
        $cmd = NAGIOSCORE_CMD_SCHEDULE_HOST_DOWNTIME;
        if ($childoptions == 1) {
            $cmd = NAGIOSCORE_CMD_SCHEDULE_AND_PROPAGATE_TRIGGERED_HOST_DOWNTIME;
        } else if ($childoptions == 2) {
            $cmd = NAGIOSCORE_CMD_SCHEDULE_AND_PROPAGATE_HOST_DOWNTIME;
        }

        // Schedule each host in the list of hosts we selected
        foreach ($hosts as $host) {
            $args['host_name'] = $host;

            $core_cmd = core_get_raw_command($cmd, $args);
            $x = core_submit_command($core_cmd, $output);
            if (!$x) { $errors++; }

            // Check if we should schedule all service downtimes
            if ($serviceoptions != 0) {
                $core_cmd = core_get_raw_command(NAGIOSCORE_CMD_SCHEDULE_HOST_SVC_DOWNTIME, $args);
                $x = core_submit_command($core_cmd, $output);
                if (!$x) { $errors++; }
            }
        }
    } else {
        // Send only one scheduled downtime
        foreach ($services as $host => $host_services) {
            foreach ($host_services as $service) {
                $args['host_name'] = $host;
                $args['service_name'] = $service;
                $core_cmd = core_get_raw_command(NAGIOSCORE_CMD_SCHEDULE_SVC_DOWNTIME, $args);
                $x = core_submit_command($core_cmd, $output);
                if (!$x) { $errors++; }
            }
        }
    }

    $msg = '';
    if ($errors == 0) {
        $msg = _('Successfully added all downtime. It may take up to a minute to show up on the list.');
        flash_message($msg, FLASH_MSG_SUCCESS);
    } else {
        $msg = _('One or more scheduled commands could not be sent to Nagios Core.');
        flash_message($msg, FLASH_MSG_ERROR);
    }

    header('Location: downtime.php');
}

function do_submit_group_downtime()
{
    $type = grab_request_var('type', 'hostgroup');
    $hosts = grab_request_var('hosts', 0);
    $services = grab_request_var('services', 0);
    $hostgroups = grab_request_var('hostgroups', array());
    $servicegroups = grab_request_var('servicegroups', array());
    $com_data = trim(grab_request_var('com_data', ''));
    $trigger_id = grab_request_var('trigger', 0);
    $start_time = grab_request_var('start_time', '');
    $end_time = grab_request_var('end_time', '');
    $fixed = grab_request_var('fixed', 1);
    $hours = intval(grab_request_var('hours', 2));
    $minutes = intval(grab_request_var('minutes', 0));
    $childoptions = grab_request_var('childoptions', 0);
    $serviceoptions = grab_request_var('serviceoptions', 0);
    $errors = 0;
    $output = "";

    // Verify permissions to be able to add downtimes
    if (is_readonly_user(0)) {
        exit('Read only users cannot submit downtimes.');
    }

    // Do checks to verify if hosts/service are selected
    if ($type == 'hostgroup') {
        if (empty($hostgroups)) {
            flash_message(_('You must select at least one host group to apply downtime to.'), FLASH_MSG_ERROR);
            show_add_group_downtime();
            return;
        }
    } else {
        if (empty($servicegroups)) {
            flash_message(_('You must select at least one service group to apply downtime to.'), FLASH_MSG_ERROR);
            show_add_group_downtime();
            return;
        }
    }

    // Check for errors
    if (empty($com_data) || empty($start_time) || empty($end_time) || (empty($hosts) && empty($services))) {
        flash_message(_("You must fill out the entire form."), FLASH_MSG_ERROR);
        show_add_group_downtime();
        return;
    }

    // Check to make sure start_time is not before end_time
    $start_time = nstrtotime($start_time);
    $end_time = nstrtotime($end_time);
    if ($start_time > $end_time) {
        flash_message(_('You must select a start time that is before end time.'), FLASH_MSG_ERROR);
        show_add_group_downtime();
        return;
    }

    // Generate command arguments
    $args = array("comment_data" => $com_data,
                  "comment_author" => get_user_attr(0, 'name'),
                  "trigger_id" => $trigger_id,
                  "start_time" => $start_time,
                  "end_time" => $end_time,
                  "fixed" => $fixed);

    // If flexible, add a duration value
    if ($fixed == 0) {
        $args['duration'] = ($hours * 60 * 60) + ($minutes * 60);
    }

    if ($type == 'hostgroup') {
        foreach ($hostgroups as $hg) {
            $args['host_group'] = $hg;

            if ($hosts) {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_HOSTGROUP_HOST_DOWNTIME;
                $core_cmd = core_get_raw_command($cmd, $args);
                $x = core_submit_command($core_cmd, $output);
                if (!$x) { $errors++; }
            }
            if ($services) {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_HOSTGROUP_SVC_DOWNTIME;
                $core_cmd = core_get_raw_command($cmd, $args);
                $x = core_submit_command($core_cmd, $output);
                if (!$x) { $errors++; }
            }

        }
    } else {
        foreach ($servicegroups as $sg) {
            $args['service_group'] = $sg;

            if ($hosts) {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_SERVICEGROUP_HOST_DOWNTIME;
                $core_cmd = core_get_raw_command($cmd, $args);
                $x = core_submit_command($core_cmd, $output);
                if (!$x) { $errors++; }
            }
            if ($services) {
                $cmd = NAGIOSCORE_CMD_SCHEDULE_SERVICEGROUP_SVC_DOWNTIME;
                $core_cmd = core_get_raw_command($cmd, $args);
                $x = core_submit_command($core_cmd, $output);
                if (!$x) { $errors++; }
            }

        }
    }

    $msg = '';
    if ($errors == 0) {
        $msg = _('Successfully added all downtime. It may take up to a minute to show up on the list.');
        flash_message($msg, FLASH_MSG_SUCCESS);
    } else {
        $msg = _('One or more scheduled commands could not be sent to Nagios Core.');
        flash_message($msg, FLASH_MSG_ERROR);
    }

    header('Location: downtime.php');
}

function show_add_group_downtime()
{
    $type = grab_request_var('type', 'host');
    if ($type != 'hostgroup' && $type != 'servicegroup') { $type = 'hostgroup'; }

    $type_hr = _('Host Group');
    if ($type == 'servicegroup') {
        $type_hr = _('Service Group');
    }

    $com_data = grab_request_var('com_data', '');
    $trigger = grab_request_var('trigger', 0);
    $start_time = grab_request_var('start_time', get_datetime_string(time()));
    $end_time = grab_request_var('end_time', get_datetime_string(strtotime('now + 2 hours')));
    $fixed = grab_request_var('fixed', 1);
    $hours = grab_request_var('hours', 2);
    $minutes = grab_request_var('minutes', 0);
    $childoptions = grab_request_var('childoptions', 0);
    $hostgroups = grab_request_var('hostgroups', array());
    $servicegroups = grab_request_var('servicegroups', array());

    // Get timezone datepicker format
    if (isset($_SESSION['date_format']))
        $format = $_SESSION['date_format'];
    else {
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

    do_page_start(array("page_title" => _('Scheduled Downtime')), true);
?>

<script type="text/javascript">

function select_hostgroups() {
    $('.hostgroups option:selected').each(function(k, v) {
        if ($(v).css('display') != 'none') {
            var hostname = $(v).val();
            $('.selected-hostgroups').append('<tr><td>' + hostname + '<input type="hidden" name="hostgroups[]" value="' + hostname + '"> <img style="float: right; cursor: pointer;" rel="tooltip" class="remove-host" data-placement="left" title="<?php echo _("Remove") ?>" src="<?php echo theme_image("cross.png"); ?>"><div class="clear"></div></td></tr>');
            $(v).hide();
        }
    });

    // Verify there are hostgroups or not and hide "no hostgroups" message
    $('#num-selected-hostgroups').html($('.selected-hostgroups tr').length - 1);
    if ($('.selected-hostgroups tr').length > 1) {
        $('.selected-hostgroups .no-hostgroups').hide();
    } else {
        $('.selected-hostgroups .no-hostgroups').show();
    }
}

function select_servicegroups() {
    $('.servicegroups option:selected').each(function(k, v) {
        if ($(v).css('display') != 'none') {
            var servicegroup = $(v).val();
            $('.selected-servicegroups').append('<tr><td>' + servicegroup + '<input type="hidden" name="servicegroups[]" value="' + servicegroup + '"> <img style="float: right; cursor: pointer;" rel="tooltip" class="remove-servicegroups" data-placement="left" title="<?php echo _("Remove") ?>" src="<?php echo theme_image("cross.png"); ?>"><div class="clear"></div></td></tr>');
            $(v).hide();
        }
    });

    // Verify there are servicegroups or not and hide "no servicegroups" message
    $('#num-selected-servicegroups').html($('.selected-servicegroups tr').length - 1);
    if ($('.selected-servicegroups tr').length > 1) {
        $('.selected-servicegroups .no-servicegroups').hide();
    } else {
        $('.selected-servicegroups .no-servicegroups').show();
    }
}

$(document).ready(function() {

    $('.selected-hostgroups').on('click', '.remove-host', function() {

        // Add back in as selectable
        $('.hostgroups option[value="'+ $(this).closest('td').find('input').val() +'"]').show().attr('selected', true);

        // Remove from the list
        $(this).tooltip('destroy');
        $(this).closest('tr').remove();

        // Verify there are hosts or not and hide "no hosts" message
        $('#num-selected-hostgroups').html($('.selected-hostgroups tr').length - 1);
        if ($('.selected-hostgroups tr').length > 1) {
            $('.selected-hostgroups .no-hostgroups').hide();
        } else {
            $('.selected-hostgroups .no-hostgroups').show();
        }
    });

    $('.selected-servicegroups').on('click', '.remove-servicegroups', function() {

        // Add back into selectable if option exists
        $('.servicegroups option[value="'+ $(this).closest('td').find('input').val() +'"]').show().attr('selected', true);

        // Remove from the list
        $(this).tooltip('destroy');
        $(this).closest('tr').remove();

        // Verify there are servicegroups or not and hide "no servicegroups" message
        $('#num-selected-servicegroups').html($('.selected-servicegroups tr').length - 1);
        if ($('.selected-servicegroups tr').length > 1) {
            $('.selected-servicegroups .no-servicegroups').hide();
        } else {
            $('.selected-servicegroups .no-servicegroups').show();
        }
    });

    $('.remove-all').click(function() {
        $('.remove-host').each(function(k, v) {
            $(v).trigger('click');
        });
        $('.remove-servicegroups').each(function(k, v) {
            $(v).trigger('click');
        });
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

    $('#fixed').change(function() {
        if ($(this).val() == 0) {
            $('#flexible-box').show();
        } else {
            $('#flexible-box').hide();
        }
    });

    $('form').submit(function() {
        whiteout();
        show_throbber();
    })

    // Selecting hostgroups
    $('.hostgroups').filterByText($('#filter-hostgroups'), true);
    $('.hostgroups').dblclick(function() { select_hostgroups(); });
    $('.btn-select-hostgroups').click(function() { select_hostgroups(); });

    // Selecting servicegroups
    $('.servicegroups').filterByText($('#filter-servicegroups'), true);
    $('.servicegroups').dblclick(function() { select_servicegroups(); });
    $('.btn-select-servicegroups').click(function() { select_servicegroups(); });

    // Info popup

    $('.info-popup.info .btn-close').click(function() {
        clear_whiteout();
        $('.info-popup.info').hide();
    });

    $(window).resize(function() {
        $('.info-popup.info').center().css('top', '140px');
    });
    
    $('.import-info').click(function() {
        whiteout();
        $('.info-popup.info').show().center().css('top', '140px');
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

});
</script>

<h1><?php echo _('Scheduled Downtime'); ?></h1>

<h2><?php echo _('Add') . ' ' . $type_hr . ' ' . _('Downtime'); ?></h2>

<?php
if (!empty($error)) {
    echo '<div class="message"><ul class="errorMessage" style="margin-top: 0;"><li><i class="fa fa-exclamation-triangle"></i> <strong>'._('Error').'</strong>: '.$error.'</li></ul></div>';
}
?>

<?php get_downtime_page_explanation($type_hr); ?>

<style type="text/css">
/* Special styles for downtime selection box only */
.selected-hostgroups-box, .selected-servicegroups-box { border: 1px solid #DDD; border-top: 0; max-height: 270px; overflow-y: auto; }
.selected-hostgroups tr:nth-of-type(1) > td, .selected-hostgroups tr:nth-of-type(2) > td, .selected-servicegroups tr:nth-of-type(1) > td, .selected-servicegroups tr:nth-of-type(2) > td  { border: 0; }
.table.table-no-border>tbody>tr>td { padding: 8px 0; }
</style>

<form action="?cmd=submit" method="post">
    <input type="hidden" name="type" value="<?php echo $type; ?>">
    <table class="table table-auto-width table-no-border" style="width: 50%; min-width: 800px;">
        <?php if ($type == 'hostgroup') { ?>
        <tr>
            <td colspan="2">
                <?php echo _('Host Group(s)'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="well" style="margin: 0; min-height: 340px;">
                    <div style="width: 50%; padding-right: 20px;" class="fl hostgroups-select">
                        <div><input type="text" id="filter-hostgroups" class="form-control" style="border-bottom: none; width: 100%;" placeholder="<?php echo _('Filter'); ?>..."></div>
                        <div>
                            <select class="form-control hostgroups" style="height: 237px; width: 100%;" multiple>
                                <?php
                                $args = array('brevity' => 1, 'orderby' => 'hostgroup_name:a');
                                $oxml = get_xml_hostgroup_objects($args);
                                if ($oxml) {
                                    foreach ($oxml->hostgroup as $hostobject) {
                                        $name = strval($hostobject->hostgroup_name);
                                        $style = '';
                                        if (in_array($name, $hostgroups)) {
                                            $style = "style='display: none;'";
                                        }
                                        echo "<option value='" . $name . "' ".$style.">$name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin-top: 5px;">
                            <button type="button" class="btn btn-select-hostgroups btn-sm btn-primary"><?php echo _('Add Selected'); ?> <i class="fa fa-chevron-right fa-r"></i></button>
                        </div>
                    </div>
                    <div style="width: 50%;" class="fr hostgroups-selected">
                        <div>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php echo _('Selected Host Groups'); ?> <span id="num-selected-hostgroups" style="vertical-align: text-top; font-size: 9px; display: inline-block;" class="label label-default"><?php echo count($hostgroups); ?></span>
                                            <div class="fr"><a class="remove-all"><?php echo _('Remove All'); ?></a></div>
                                            <div class="clear"></div>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="selected-hostgroups-box">
                                <table class="table table-condensed">
                                    <tbody class="selected-hostgroups">
                                        <?php
                                        $display = '';
                                        if (count($hostgroups) > 0) { $display = ' style="display:none;"'; }
                                        ?>
                                        <tr class="no-hostgroups"<?php echo $display; ?>>
                                            <td><?php echo _('No Host Groups selected'); ?></td>
                                        </tr>
                                        <?php
                                        if (count($hostgroups) > 0) {
                                            foreach ($hostgroups as $h) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $h; ?>
                                                    <input type="hidden" name="hostgroups[]" value="<?php echo $h; ?>">
                                                    <img style="float: right; cursor: pointer;" class="tt-bind remove-host" data-placement="left" title="<?php echo _('Remove') ?>" src="<?php echo theme_image('cross.png'); ?>">
                                                    <div class="clear"></div>
                                                </td>
                                            </tr>
                                        <?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <?php } else if ($type == 'servicegroup') { ?>
        <tr>
            <td colspan="2">
                <?php echo _('Service Group(s)'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="well" style="margin: 0; min-height: 340px;">
                    <div style="width: 50%; padding-right: 20px;" class="fl servicegroups-select">
                        <div><input type="text" id="filter-servicegroups" class="form-control" style="border-bottom: none; width: 100%;" placeholder="<?php echo _('Filter'); ?>..."></div>
                        <div>
                            <select class="form-control servicegroups" style="height: 237px; width: 100%;" multiple>
                                <?php
                                $args = array('brevity' => 1, 'orderby' => 'servicegroup_name:a');
                                $oxml = get_xml_servicegroup_objects($args);
                                if ($oxml) {
                                    foreach ($oxml->servicegroup as $obj) {
                                        $name = strval($obj->servicegroup_name);
                                        $style = '';
                                        if (in_array($name, $servicegroups)) {
                                            $style = "style='display: none;'";
                                        }
                                        echo "<option value='" . $name . "' ".$style.">$name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div style="margin-top: 5px;">
                            <button type="button" class="btn btn-select-servicegroups btn-sm btn-primary"><?php echo _('Add Selected'); ?> <i class="fa fa-chevron-right fa-r"></i></button>
                        </div>
                    </div>
                    <div style="width: 50%;" class="fr servicegroups-selected">
                        <div>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>
                                            <?php echo _('Selected Service Groups'); ?> <span id="num-selected-servicegroups" style="vertical-align: text-top; font-size: 9px; display: inline-block;" class="label label-default"><?php echo count($servicegroups); ?></span>
                                            <div class="fr"><a class="remove-all"><?php echo _('Remove All'); ?></a></div>
                                            <div class="clear"></div>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="selected-servicegroups-box">
                                <table class="table table-condensed">
                                    <tbody class="selected-servicegroups">
                                        <?php
                                        $display = '';
                                        if (count($servicegroups) > 0) { $display = ' style="display:none;"'; }
                                        ?>
                                        <tr class="no-servicegroups"<?php echo $display; ?>>
                                            <td><?php echo _('No Service Groups selected'); ?></td>
                                        </tr>
                                        <?php
                                        if (count($servicegroups) > 0) {
                                            foreach ($servicegroups as $h) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $h; ?>
                                                    <input type="hidden" name="servicegroups[]" value="<?php echo $h; ?>">
                                                    <img style="float: right; cursor: pointer;" class="tt-bind remove-host" data-placement="left" title="<?php echo _('Remove') ?>" src="<?php echo theme_image('cross.png'); ?>">
                                                    <div class="clear"></div>
                                                </td>
                                            </tr>
                                        <?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td><?php echo _('Types'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td class="checkbox">
                <label>
                    <input type="checkbox" name="hosts" value="1"> <?php echo _("Schedule downtime for all"); ?> <b><?php echo _("hosts"); ?></b>
                </label>
                <br>
                <label>
                    <input type="checkbox" name="services" value="1"> <?php echo _("Schedule downtime for all"); ?> <b><?php echo _("services"); ?></b>
                </label>
            </td>
        </tr>
        <tr>
            <td style="width: 100px;"><?php echo _('Author'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" name="com_author" value="<?php echo get_user_attr(0, 'name'); ?>" class="form-control" readonly></td>
        </tr>
        <tr>
            <td><?php echo _('Comment'); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td><input type="text" class="form-control" name="com_data" style="width: 400px;" value="<?php echo encode_form_val($com_data); ?>"></td>
        </tr>
        <tr>
            <td><?php echo _('Triggered By'); ?></td>
            <td>
                <?php
                $downtimes = get_scheduled_downtime();
                ?>
                <select class="form-control" name="trigger" id="trigger">
                    <option value="0" selected><?php echo _('None'); ?></option>
                    <?php
                    $options = '';
                    $s = '';
                    $h = '';

                    foreach ($downtimes as $dt) { 
                        if (!empty($dt['service_description'])) {
                            $s .= '<option value="' . $dt['internal_downtime_id'] . '">' . $dt['host_name'] . ' - '. $dt['service_description'] .' @ ' . get_datetime_string(strtotime($dt['scheduled_start_time'])) . ' (ID ' . $dt['internal_downtime_id'] . ')</option>';
                        } else {
                            $h .= '<option value="' . $dt['internal_downtime_id'] . '">' . $dt['host_name'] . ' @ ' . get_datetime_string(strtotime($dt['scheduled_start_time'])) . ' (ID ' . $dt['internal_downtime_id'] . ')</option>';
                        }
                    }

                    if (!empty($h)) {
                        $options .= '<optgroup label="'._('Host Downtimes').'">' . $h . '</optgroup>';
                    }

                    if (!empty($s)) {
                        $options .= '<optgroup label="'._('Service Downtimes').'">' . $s . '</optgroup>';
                    }

                    echo $options;
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo _('Type'); ?></td>
            <td>
                <select id="fixed" name="fixed" class="form-control">
                    <option value="1"><?php echo _('Fixed'); ?></option>
                    <option value="0"><?php echo _('Flexible'); ?></option>
                </select>
            </td>
        </tr>
        <tr id="flexible-box" class="hide">
            <td><?php echo _('Duration'); ?></td>
            <td>
                <input type="text" class="form-control" name="hours" style="width: 40px;" id="flexible-hours" value="2"> Hours
                <input type="text" class="form-control" name="minutes" style="width: 40px; margin-left: 5px;" id="flexible-minutes" value="0"> Minutes
            </td>
        </tr>
        <tr>
            <td><?php echo _("Start Time"); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <input class="form-control datetimepicker req" style="width: 150px;" type="text" id='startdateBox' name="start_time" value="<?php echo encode_form_val($start_time); ?>" size="16">
            </td>
        </tr>
        <tr>
            <td><?php echo _("End Time"); ?> <i class="fa fa-asterisk fa-req tt-bind" title="<?php echo _('Required'); ?>"></i></td>
            <td>
                <input class="form-control datetimepicker req" style="width: 150px;" type="text" id='enddateBox' name="end_time" value="<?php echo encode_form_val($end_time); ?>" size="16">
            </td>
        </tr>
        <tr <?php if ($type != "host") { echo 'class="hide"'; } ?>>
            <td><?php echo _('Child Hosts'); ?></td>
            <td>
                <select id="childoptions" name="childoptions" class="form-control req">
                    <option value="0"><?php echo _('Do nothing with child hosts'); ?></option>
                    <option value="1"><?php echo _('Schedule triggered downtime for all child hosts'); ?></option>
                    <option value="2"><?php echo _('Schedule non-triggered downtime for all child hosts'); ?></option>
                </select>
            </td>
        </tr>
        <tr <?php if ($type != "host") { echo 'class="hide"'; } ?>>
            <td><?php echo _('Services'); ?></td>
            <td>
                <select id="serviceoptions" name="serviceoptions" class="form-control req">
                    <option value="0"><?php echo _('Do nothing with associated services'); ?></option>
                    <option value="1"><?php echo _('Schedule downtime for all associated services'); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Schedule'); ?></button>
    <a href="downtime.php" class="btn btn-sm btn-default"><?php echo _('Cancel'); ?></a>
</form>

<?php
    do_page_end(true);
}

function get_downtimes($total=false) {

    $search = grab_request_var('search', '');
    $filters = grab_request_var('filters', array());
    $records = grab_request_var('records', 0);
    $orderby = grab_request_var('orderby', array());
    $page = grab_request_var('page', 0);

    $backendargs = array();

    if (!empty($search)) {
        $backendargs["comment"] = "lk:" . $search . ";host_name=lk:" . $search .";service_description=lk:". $search;
    }

    if (!empty($orderby)) {
        $backendargs["orderby"] = $orderby[0].":".$orderby[1];
    }

    if ($total) {
        $backendargs['totals'] = 1;
    } else {
        if (!empty($records)) {
            if ($page >= 1) {
                $records = $records . ':' . (($page - 1) * $records);
            }
            $backendargs['records'] = $records;
        }
    }

    // Downtime types: 1 - service, 2 - host
    if (!empty($filters)) {

        // Apply host/service filter
        $types = array();
        if (in_array('host', $filters)) { $types[] = 2; }
        if (in_array('service', $filters)) { $types[] = 1; }
        if (!empty($types)) {
            $backendargs["downtime_type"] = "in:" . implode(',', $types);
        }

        // Apply ineffect filter (was_started = 1)
        $effects = array();
        if (in_array('ineffect', $filters)) { $effects[] = 1; }
        if (in_array('notineffect', $filters)) { $effects[] = 0; }
        if (!empty($effects)) {
            $backendargs["was_started"] = "in:" . implode(',', $effects);
        }

    }

    $dt_arr = get_scheduled_downtime($backendargs);

    if ($total) {
        $recordcount = 0;
        if ($dt_arr !== null) {
            $recordcount = count($dt_arr);
        } else {
            return false;
        }
        return $recordcount;
    }

    $downtimes = array();
    if ($dt_arr !== null) {
        $i = 0;
        foreach ($dt_arr as $dt) {
            $start_ts = convert_datetime_to_timestamp($dt['scheduled_start_time']);
            $end_ts = convert_datetime_to_timestamp($dt['scheduled_end_time']);

            $duration = intval($dt['duration']);
            if (empty($duration)) {
                $duration = ($end_ts - $start_ts) / 1000;
            }
            $downtimes[$i] = array(
                "downtime_id" =>            $dt['internal_downtime_id'],
                "type" =>                   $dt['downtime_type'],
                "host_name" =>              $dt['host_name'],
                "entry_time" =>             convert_datetime_to_timestamp($dt['entry_time']),
                "start_time" =>             $start_ts,
                "end_time" =>               $end_ts,
                "author" =>                 $dt['author_name'],
                "comment" =>                $dt['comment_data'],
                "fixed" =>                  $dt['is_fixed'],
                "duration" =>               $duration,
                "triggered_by" =>           $dt['triggered_by_id'],
                "is_in_effect" =>           $dt['was_started'],
            );
            if (!empty($dt['service_description'])) {
                $downtimes[$i]["service_description"] = $dt['service_description'];
            }
            $i++;
        }
    } else {
        return false;
    }

    echo json_encode($downtimes);
    exit();
}

function get_downtime_page_explanation($type)
{
    ?>
<p>
<?php echo _('Schedule downtime for a particular'); echo ' ' . $type . ' '; echo _('or multiple'); echo ' ' . $type . 's'; ?>.
<a class="import-info"><?php echo _('More information about downtimes'); ?></a>.
</p>

<div class="info-popup info hide">
    <h2 style="margin-top: 0; padding-top: 0;"><?php echo _('What is Downtime in Nagios XI'); ?></h2>
    <p><em><?php echo _('During the specified downtime, no notifications will be sent out for the'); ?> <?php echo $type; ?>.</em></p>
    <p><?php echo _("Scheduled downtime allows users to set downtime for upcoming events, maintenance, and more without getting notifications. The downtime is also recorded in the log so that availability reports can show the difference between something being down or down during a scheduled downtime."); ?></p>
    <p><?php echo _('When the scheduled downtime expires, notifications for this'); ?> <?php echo $type; ?> <?php echo _('will resume sending as normal.'); ?> <?php echo _('Scheduled downtimes are preserved during program shutdowns and restarts.'); ?></p>
    <h6><?php echo _('Downtime Types'); ?> (<?php echo _('Fixed or Flexible'); ?>)</h6>
    <p><?php echo _('Fixed'); ?> - (<?php echo _('Default'); ?>) <?php echo _('Using this option will cause the downtime to be in effect only between the start and end times you specify. This is the straightforward downtime type.'); ?></p>
    <p><?php echo _('Flexible'); ?> - <?php echo _('When this option is selected, the downtime will be treated as "flexible" which means the downtime will start when the host actually goes down or becomes unreachable'); ?> - <em><?php echo _('during the start and end times specified'); ?></em> - <?php echo _('and lasts as long as the duration you enter in the duration fields.'); ?></p>
    <div style="margin-top: 10px;">
        <button type="button" class="btn-close btn btn-sm btn-default"><?php echo _('Close'); ?></button>
    </div>
</div>
    <?php
}
