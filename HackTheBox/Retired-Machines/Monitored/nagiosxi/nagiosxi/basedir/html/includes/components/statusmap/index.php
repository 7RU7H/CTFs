<?php
//
// Network Status Map Component
// Copyright (c) 2016 Nagios Enterprises, LLC. All rights reserved.
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
	show_status_map();
}

function show_status_map()
{
    do_page_start(array("page_title"=>_("Network Status Map")), true);
?>

    <script type="text/javascript">
    $(document).ready(function() {
        resize_statusmap();

        $(window).resize(function() {
            resize_statusmap();
        });
    });

    function resize_statusmap() {
        var w = $(window).width() - 40;
        var h = $(window).height() - 80;
        $('#map').css('width', w).css('height', h);
    }
    </script>

    <h1><?php echo _("Network Status Map"); ?></h1>

    <div class="statusmap"><iframe id="map" src="map.php" scrolling="no"></div>

<?php
    do_page_end(true);
}