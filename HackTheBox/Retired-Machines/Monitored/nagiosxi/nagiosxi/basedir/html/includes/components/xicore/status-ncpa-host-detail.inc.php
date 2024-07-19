<?php

function display_ncpa_host_status()
{
    $host_name = grab_request_var('host');

    $host = get_data_host_status(array('host_name' => $host_name, 'brevity' => 1));
    if (empty($host)) {
        exit("Host does not exist.");
    }
    $host = $host[0];
    $services = get_data_service_status(array('host_name' => $host_name, 'brevity' => 1));
    $host['services'] = $services;
    $host_obj = json_encode($host);

    do_page_start(array("page_title" => _("Host Status Detail")), true, true);
?>

<script type="text/javascript" src="https://<?php echo $host['address']; ?>:5693/static/js/smoothie.js"></script>
<link type="text/css" href="https://<?php echo $host['address']; ?>:5693/static/css/ncpa-graph.css" rel="stylesheet">

<div style="margin: 10px 0;">

<div id="ncpa-host-content">
    
    <div class="row" style="margin: 0 -10px;">
        <div class="col-sm-8">
            <div style="background-color: #F9F9F9; padding-right: 15px; margin-right: 20px;" class="fl">
                <img v-bind:src="icon_url" style="vertical-align: top; margin-right: 10px;">
                <span style="font-weight: bold; font-size: 20px; line-height: 40px;">{{ host.host_name }}</span>
            </div>
            <div style="background-color: #F9F9F9; padding: 5px 15px; margin-right: 20px; height: 40px;" class="fl">
                <div><b><?php echo _('Hostgroups'); ?></b></div>
                <div><a href="">linux-server</a>, <a href="">webhost</a>, <a href="">admin-servers</a></div>
            </div>
        </div>
        <div class="col-sm-4" style="text-align: right;">
            <a v-on:click="refresh" class="btn btn-default"><?php echo _('Refresh'); ?></a> 
            <a href="" class="btn btn-default"><?php echo _('Actions'); ?></a>
        </div>
    </div>

    <div class="row" style="margin: 20px -5px 0 -5px;">
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-12">
                    <div style="margin-bottom: 20px; background-color: #F0F0F0; padding: 5px 10px; font-weight: bold;">
                        <?php echo _('Host Status'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div style="background-color: #F9F9F9; padding: 20px; margin-bottom: 20px;">
                        {{ host.output }}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div v-on:click="display.server_data = !display.server_data" style="margin-bottom: 20px; background-color: #F0F0F0; padding: 5px 10px; font-weight: bold; cursor: pointer;">
                        <?php echo _('Live Server Data'); ?>
                        <span class="fr">
                            <i v-bind:class="display.server_data ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa fa-14"></i>
                        </span>
                    </div>
                </div>
                <div class="col-sm-4" v-if="display.server_data">
                    <div class="graph" style="margin-bottom: 20px; min-height: 220px; background-color: #F9F9F9;">
                        <div class="sk-spinner sk-spinner-fading-circle" style="top: 99px;">
                            <div class="sk-circle1 sk-circle"></div>
                            <div class="sk-circle2 sk-circle"></div>
                            <div class="sk-circle3 sk-circle"></div>
                            <div class="sk-circle4 sk-circle"></div>
                            <div class="sk-circle5 sk-circle"></div>
                            <div class="sk-circle6 sk-circle"></div>
                            <div class="sk-circle7 sk-circle"></div>
                            <div class="sk-circle8 sk-circle"></div>
                            <div class="sk-circle9 sk-circle"></div>
                            <div class="sk-circle10 sk-circle"></div>
                            <div class="sk-circle11 sk-circle"></div>
                            <div class="sk-circle12 sk-circle"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8" v-if="display.server_data">
                    <div class="top">
                        <table class="processes table table-striped table-hover table-bordered table-condensed">
                            <thead>
                                <tr id="titles"></tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div v-on:click="display.service_details = !display.service_details" style="margin-bottom: 20px; background-color: #F0F0F0; padding: 5px 10px; font-weight: bold; cursor: pointer;">
                        <?php echo _('Service Status Details'); ?>
                        <span class="fr">
                            <i v-bind:class="display.service_details ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa fa-14"></i>
                        </span>
                    </div>
                </div>
                <div class="col-sm-12" v-if="display.service_details">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 90px; text-align: center;">Status</th>
                                <th>Service Name</th>
                                <th>Output</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="service in host.services" v-bind:key="service.service_object_id">
                                <td style="text-align: center;" v-bind:class="get_status_output_class(service.current_state)">{{ get_status_output(service.current_state) }}</td>
                                <td><a v-bind:href="get_service_details_url(host.host_name, service.service_description)">{{ service.service_description }}</a></td>
                                <td>{{ service.output }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            Quick Actions?
        </div>
    </div>

</div>

</div>

<script type="text/javascript">
var obj = {
    host: <?php echo $host_obj; ?>,
    display: { 
        service_details: 1,
        server_data: 1
    }
};
var main = new Vue({
    el: '#ncpa-host-content',
    data: obj,
    computed: {
        icon_url: function() {
            return '<?php echo wizard_logo(); ?>'+this.host.icon_image;
        }
    },
    methods: {
        poll_status_data: function() {
            $.get('api.php', { cmd: 'gethost', host: this.host.host_name }, function(host) {
                main.host = host;
            }, 'json');
            setTimeout(this.poll_status_data, 5000);
        },
        poll_ncpa_connection: function() {



        },
        refresh: function() {
            console.log('refreshing...');
            this.poll_status_data();
        },
        get_status_output: function(current_state) {
            if (current_state == 0) {
                return "OK";
            } else if (current_state == 1) {
                return "WARNING";
            } else if (current_state == 2) {
                return "CRITICAL";
            } else if (current_state == 3) {
                return "UNKNOWN";
            } else {
                return "PENDING";
            }
        },
        get_service_details_url: function(host, service) {
            return "status.php?show=servicedetail&host=" + encodeURIComponent(host) + "&service=" + encodeURIComponent(service);
        },
        get_status_output_class: function(current_state) {
            return 'service' + this.get_status_output(current_state).toLowerCase();
        },
        load_graphs: function() {
            $('.graph').load('https://' + this.host.address + ':5693/graph/cpu/percent?token=mytoken');
        },
        load_top: function() {
            var ws = new WebSocket('wss://' + this.host.address + ':5693/ws/top?token=mytoken');
            ws.onmessage = function(d) {
                info = $.parseJSON(d.data);
                
                var load = info.load
                var vir = info.vir
                var swap = info.swap
                var pl = info.process
                current_pl = pl;
                
                //update_stats(load, vir, swap);
                
                if (plbody.children().length == 0) {
                    create_header(pl);
                }
                pl.sort(comparator);
                create_plbody(pl);
            }
        }
    },
    mounted: function() {
        setTimeout(this.poll_status_data, 5000);
        this.load_graphs();
        this.load_top();
    }
});

var display = '';
var critical = '';
var warning = '';
var highlight = '';
var plbody = $('table.processes tbody');
var plhead = $('table.processes thead tr#titles');
var order = ['pid', 'name', 'username', 'cpu_percent', 'mem_percent'];
var current_pl = [];

var cpu_compare = function(a, b) {
    if (a.cpu_percent[0] < b.cpu_percent[0]) {
        return 1
    }
    if (a.cpu_percent[0] > b.cpu_percent[0]) {
        return -1
    }
    return 0;
}

var mem_compare = function(a, b) {
    if (a.mem_percent[0] < b.mem_percent[0]) {
        return 1
    }
    if (a.mem_percent[0] > b.mem_percent[0]) {
        return -1
    }
    return 0;
}

var comparator = cpu_compare;

var create_plbody = function(pl) {
    var limit = 6;
    plbody.empty();
    $.each(pl, function(i) {
        if ((display > 0 && display <= i) || i >= limit) {
            return false;
        }
        var p = pl[i];
        var tr = $('<tr>', {id: p.pid});
        $.each(order, function(j) {
            var data = p[order[j]];
            if (data.constructor === Array) {
                data = data[0];
            }
            var td = $('<td>', { text: data });
            if (highlight != null && order[j] == 'name' && p[order[j]].indexOf(highlight) > -1) {
                tr.addClass('top-highlight');
            }
            if ((order[j] == 'cpu_percent' || order[j] == 'mem_percent') && critical > 0 && p[order[j]][0] > critical) {
                td.addClass('critical');
            }
            else if ((order[j] == 'cpu_percent' || order[j] == 'mem_percent') && warning > 0 && p[order[j]][0] > warning) {
                td.addClass('warning');
            }
            tr.append(td);    
        })
        plbody.append(tr);
    })
}

var create_header = function(pl) {
    plhead.empty();

    $.each(order, function(i) {
        var th = $('<th>', { id: order[i], text: order[i].replace(/_/g, ' ') });
        var s = $('<span>', { class: 'signia', html: ' <i class="fa fa-caret-down fa-l"></i>'});

        if (order[i] == 'cpu_percent') {
            th.append(s);
            th.css('width', '12%');
            th.click(function() {
                $('.signia').hide();
                s.show();
                comparator = cpu_compare;
                current_pl.sort(cpu_compare);
                create_plbody(current_pl);
            });
        }
        if (order[i] == 'mem_percent') {                    
            th.append(s);
            th.css('width', '12%');
            th.click(function() {
                $('.signia').hide();
                s.show();
                comparator = mem_compare;
                current_pl.sort(mem_compare);
                create_plbody(current_pl);
            });
            s.hide();
        }
        if (order[i] == 'pid') {
            th.css('width', '8%');
        }
        
        plhead.append(th);
    });
}
</script>

<?php
    do_page_end(true);
    exit();
}
