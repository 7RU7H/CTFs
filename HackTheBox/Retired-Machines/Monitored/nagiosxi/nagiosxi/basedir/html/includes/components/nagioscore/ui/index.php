<?php
//
// Copyright (c) 2008-2017 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../nagioscore.inc.php');

// Initialization stuff
pre_init();
init_session();
grab_request_vars();

// Check prereqs and auth
check_prereqs();
check_authentication();

route_request();

function route_request()
{

    draw_page();
}

function draw_page()
{

    do_page_start(array("page_id" => "subcomponent-nagioscore-page"));
    ?>
    <div id="leftnav">
        <?php draw_menu(); ?>
    </div>

    <div id="maincontent">
        <iframe src="<?php echo get_window_frame_url(nagioscore_get_ui_url() . "tac.php"); ?>" width="100%"
                frameborder="0" id="maincontentframe" name="maincontentframe">
            [<?php echo _("Your user agent does not support frames or is currently configured not to display frames. "); ?>
            ]
        </iframe>
    </div>

    <?php
    do_page_end();
}

function draw_menu()
{
    $m = get_menu_items();
    draw_menu_items($m);
    ?>

<?php
}


/**
 * @return array
 */
function get_menu_items()
{

    $includes_path = get_base_url() . "/includes/";
    $components_path = $includes_path . "components/";

    $nagioscoreui_path = nagioscore_get_ui_url();


    $mi = array();

    $mi[] = array(
        "type" => "html",
        "title" => "Nagios Core",
        "opts" => array(
            "html" => "<img src='" . $components_path . "xicore/images/subcomponents/nagioscore.png' title='Nagios Core'>",
        )
    );
    $mi[] = array(
        "type" => "linkspacer"
    );
    $mi[] = array(
        "type" => "linkspacer"
    );

    // Quick View
    $mi[] = array(
        "type" => "menusection",
        "title" => _("Quick View"),
        "opts" => array(
            "id" => "quickview",
            "expanded" => true,
            "url" => $nagioscoreui_path . "tac.php"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Tactical Overview"),
        "opts" => array(
            "href" => $nagioscoreui_path . "tac.php"
        )
    );
    $mi[] = array(
        "type" => "linkspacer"
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Open Problems"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?host=all&type=detail&servicestatustypes=61&serviceprops=10&hostprops=10"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Service Problems"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?host=all&servicestatustypes=28"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Host Problems"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?hostgroup=all&style=hostdetail&hoststatustypes=12"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Network Outages"),
        "opts" => array(
            "href" => $nagioscoreui_path . "outages.php"
        )
    );
    $mi[] = array(
        "type" => "menusectionend",
        "title" => "",
        "opts" => ""
    );

    // Detail
    $mi[] = array(
        "type" => "menusection",
        "title" => _("Details"),
        "opts" => array(
            "id" => "statusdetails",
            "expanded" => false,
            "url" => $nagioscoreui_path . "status.php?host=all"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Service Detail"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?host=all"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Host Detail"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?hostgroup=all&style=hostdetail"
        )
    );
    $mi[] = array(
        "type" => "linkspacer"
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Hostgroup Overview"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?hostgroup=all&style=overview"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Hostgroup Summary"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?hostgroup=all&style=summary"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Hostgroup Grid"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?hostgroup=all&style=grid"
        )
    );
    $mi[] = array(
        "type" => "linkspacer"
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Servicegroup Overview"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?servicegroup=all&style=overview"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Servicegroup Summary"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?servicegroup=all&style=summary"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Servicegroup Grid"),
        "opts" => array(
            "href" => $nagioscoreui_path . "status.php?servicegroup=all&style=grid"
        )
    );
    $mi[] = array(
        "type" => "menusectionend",
        "title" => "",
        "opts" => ""
    );

    // Maps
    $mi[] = array(
        "type" => "menusection",
        "title" => _("Maps"),
        "opts" => array(
            "id" => "maps",
            "expanded" => false,
            "url" => $nagioscoreui_path . "statusmap.php"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Status Map"),
        "opts" => array(
            "href" => $nagioscoreui_path . "statusmap.php"
        )
    );
    $mi[] = array(
        "type" => "menusectionend",
        "title" => "",
        "opts" => ""
    );

    // Incident Management
    $mi[] = array(
        "type" => "menusection",
        "title" => _("Incident Management"),
        "opts" => array(
            "id" => "incidentmanagement",
            "expanded" => false,
            "url" => $nagioscoreui_path . "extinfo.php?type=3"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Comments"),
        "opts" => array(
            "href" => $nagioscoreui_path . "extinfo.php?type=3"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Scheduled Downtime"),
        "opts" => array(
            "href" => $nagioscoreui_path . "extinfo.php?type=6"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Notifications"),
        "opts" => array(
            "href" => $nagioscoreui_path . "notifications.php?contact=all"
        )
    );
    $mi[] = array(
        "type" => "menusectionend",
        "title" => "",
        "opts" => ""
    );

    // Reports
    $mi[] = array(
        "type" => "menusection",
        "title" => _("Reports"),
        "opts" => array(
            "id" => "reports",
            "expanded" => false
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Trends"),
        "opts" => array(
            "href" => $nagioscoreui_path . "trends.php"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Availability"),
        "opts" => array(
            "href" => $nagioscoreui_path . "avail.php"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Alert History"),
        "opts" => array(
            "href" => $nagioscoreui_path . "history.php?host=all"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Alert Summary"),
        "opts" => array(
            "href" => $nagioscoreui_path . "summary.php"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Alert Histogram"),
        "opts" => array(
            "href" => $nagioscoreui_path . "histogram.php"
        )
    );
    $mi[] = array(
        "type" => "menusectionend",
        "title" => "",
        "opts" => ""
    );

    // Process Info
    $mi[] = array(
        "type" => "menusection",
        "title" => _("Nagios Core Process"),
        "opts" => array(
            "id" => "system",
            "expanded" => false,
            "url" => $nagioscoreui_path . "extinfo.php?type=0"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Core Process"),
        "opts" => array(
            "href" => $nagioscoreui_path . "extinfo.php?type=0"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Core Performance"),
        "opts" => array(
            "href" => $nagioscoreui_path . "extinfo.php?type=4"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Core Queue"),
        "opts" => array(
            "href" => $nagioscoreui_path . "extinfo.php?type=7"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Core Event Log"),
        "opts" => array(
            "href" => $nagioscoreui_path . "showlog.php"
        )
    );
    $mi[] = array(
        "type" => "menusectionend",
        "title" => "",
        "opts" => ""
    );

    // Config
    $mi[] = array(
        "type" => "menusection",
        "title" => _("Configuration"),
        "opts" => array(
            "id" => "config",
            "expanded" => false,
            "url" => $nagioscoreui_path . "config.php"
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("Manage Config"),
        "opts" => array(
            "href" => get_base_url() . "?page=subcomponent-nagioscorecfg",
            "target" => "_self",
        )
    );
    $mi[] = array(
        "type" => "link",
        "title" => _("View Config"),
        "opts" => array(
            "href" => $nagioscoreui_path . "config.php"
        )
    );
    $mi[] = array(
        "type" => "menusectionend",
        "title" => "",
        "opts" => ""
    );


    return $mi;
}


?>


