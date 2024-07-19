/**
 * Graph explorer include for global scope of XI.
 *
 * Used mostly to make the graph icon on the host/service tables. Updated to now use the perfdata
 * dashlet instead of just the regular timeline graph.
 */


var PF_GRAPH_HEIGHT = 500;
var PF_HOST, PF_SERVICE;
var reload_timer;


$(document).ready(function() {
    $(window).resize(function() {
        if (PF_HOST != undefined) {
            resize_perfdata_popup();
            clearTimeout(reload_timer);
            reload_timer = setTimeout(function() {
                reload_perfdata_popup();
            }, 300);
        }
    });
});


function graphexplorer_display_graph(host, service)
{
    PF_HOST = host;
    PF_SERVICE = service;

    whiteout();
    $('div.childpage').append('<div class="close-perfdata-popup btn-close"></div><div class="perfdata-popup"><div class="pf-graph-container"><div class="dashify-ge-button"><a class="dashifybutton tt-bind" id="dashify2" title="'+_('Add to Dashboard')+'"><i class="fa fa-14 fa-sign-in fa-rotate-270"></i></a><div id="settingscontainer"><input type="hidden" class="url" value=""></div></div><div id="graphcontainer" style="text-align: center; height: ' + PF_GRAPH_HEIGHT + 'px;"><div style="padding-top: ' + (PF_GRAPH_HEIGHT/2 - 33) + 'px;"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i> <span style="vertical-align: top; line-height: 33px; margin-left: 5px;">' + _('Loading') + '...</span></div></div><div id="graph-script-container"></div></div><div class="pf-graph-settings"><div class="input-group fl" style="width: 220px;"><label class="input-group-addon">'+_('Time Range')+'</label><select class="form-control" id="time"><option value="-4h">'+_('Last 4 Hours')+'</option><option value="-24h" selected>'+_('Last 24 Hours')+'</option><option value="-1w">'+_('Last 7 Days')+'</option><option value="-1m">'+_('Last 30 Days')+'</option><option value="-1y">'+_('Last 365 Days')+'</option></select></div><button class="btn btn-update btn-sm btn-default fl" style="margin-left: 10px;"><i class="fa fa-refresh"></i> '+_('Update')+'</button><button class="btn btn-close btn-sm btn-default fr"><i class="fa fa-times"></i> '+_('Close')+'</button><div class="clear"></div></div></div>');

    // Resize to proper sizing for window size (Default 1200x500 view)
    resize_perfdata_popup();

    // Get graph settings
    reload_perfdata_popup();

    $('.pf-graph-settings .btn-update').click(function() {
        $(this).html('<i class="fa fa-refresh fa-spin"></i> ' + _('Loading') + ' ...').prop('disabled', true);
        reload_perfdata_popup($('.pf-graph-settings #time').val());
    });

    // Close the graph display
    $('.btn-close').click(function () {
        close_perfdata_popup();
    });

    // Or close with escape key
    $(document).keyup(function(e) {
        if (e.keyCode == 27) {
            close_perfdata_popup();
        }
    });

    $('#dashify2').click(function () {
        $('#whiteout').css('z-index', 9010);

        // Start loading the boards
        get_ajax_data_innerHTML("getdashboardselectmenuhtml", "", true, '#addToDashboardBoardSelect');
        var graph_title = $('#graphcontainer').find('.highcharts-title tspan').html();

        var theurl = "";
        var content = '<div id="popup_header"><b>' + _('Add to Dashboard') + '</b></div><div id="popup_data"><p>' + _('Add this graph to a dashboard.') + '</p></div>';
        content += '<div><label for="addToDashboardTitleBox">' + _("Dashlet Title") + '</label></div>';
        content += '<input type="text" size="30" name="title" id="addtoDashboardTitleBox" value="'+graph_title+'" class="textfield form-control">';
        content += '<div style="margin-top: 10px;"><label for="addToDashboardBoardSelect">' + _('Select a Dashboard to Add To') + '</label></div>';
        content += '<select id="addToDashboardBoardSelect" class="form-control"></select>';
        content += '<div id="addToDashboardFormButtons" style="margin-top: 10px;"><button id="AddToDashButton" class="btn btn-sm btn-primary" onclick="add_perfdata_dashlet()">' + _("Add It") + '</button></div>';

        set_child_popup_content(content);
        display_child_popup("250px");
    });

    $('#dashify2').hover(
        function () {
            $(".pf-graph-container").addClass("hover");
        },
        function () {
            $(".pf-graph-container").removeClass("hover");
        }
    );
}


function add_perfdata_dashlet() {
    var url = $('#settingscontainer').find('input.url').val();
    var board = $('#addToDashboardBoardSelect').val();
    var title = $('#addtoDashboardTitleBox').val();

    // Validate before finishing
    if (title == '' || board == '') {
        alert(_('You must fill out the entire form.'));
        return false;
    }

    // Send dashlet info to Graph Explorer component dashify page
    $.post(base_url + 'includes/components/graphexplorer/dashifygraph.php', { url: url, dashletName: title, boardName: board, nsp: nsp_str }, function (data) {
        $('#whiteout').css('z-index', 9000);

        // If it was a success show created message
        if (data.success == 1) {
            content = "<div id='child_popup_header'><b>" + _('Dashlet Added') + "</b></div><div style='height: 100px;' id='child_popup_data'><p>" + _('Dashlet is now loaded on your dashboard.') + "</p></div>";
            set_child_popup_content(content);
            display_child_popup();
            $('#child_popup_layer').css('height', '80px');
            fade_child_popup('green', 2000);
        }
    }, 'json');
}


function close_perfdata_popup() {
    $('.close-perfdata-popup').remove();
    $('.perfdata-popup').remove();
    clear_whiteout();
}


function reload_perfdata_popup(start) {
    var url = base_url + 'includes/components/graphexplorer/visApi.php';
    var args = { type: 'perfdata',
                 host: PF_HOST,
                 service: PF_SERVICE,
                 div: 'graphcontainer',
                 height: PF_GRAPH_HEIGHT };

    if (start) {
        args['start'] = start;
    }

    // Get the actual graph to display
    $.get(url, args, function(script) {
        var url_parts = this.url.split('visApi');
        var url = 'visApi' + url_parts[1];
        $('#settingscontainer').find('input.url').val(url);
        $("#graph-script-container").html(script);
        resize_perfdata_popup();
        $('.pf-graph-settings .btn-update').html('<i class="fa fa-refresh"></i> '+_('Update')).prop('disabled', false);
    });
}


function resize_perfdata_popup() {
    if ($('.perfdata-popup').is(':visible')) {
        var ww = $(window).width();
        var wh = $(window).height();
        PF_GRAPH_HEIGHT = 500;
        if (ww < 1300) {
            ww -= 100;
            $('.perfdata-popup').css('width', ww)
                                .css('margin-left', '-' + ww/2 + 'px');
        }
        if (wh < 700) {
            PF_GRAPH_HEIGHT = wh - 200;
            $('.perfdata-popup').css('height', PF_GRAPH_HEIGHT)
                                .css('margin-top', '-' + PF_GRAPH_HEIGHT/2 + 'px');
            PF_GRAPH_HEIGHT -= 50;
        } else {
            $('.perfdata-popup').css('height', 'auto');
            $('.perfdata-popup').css('margin-top', '-' + $('.perfdata-popup').outerHeight()/2 + 'px');
        }
        $('#graphcontainer').css('height', PF_GRAPH_HEIGHT);
    }
}