<?php
require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs 
grab_request_vars();
check_prereqs();
check_authentication(false);
?>
<!DOCTYPE html>
<html ng-app="mapApp">
    <head>
        <meta charset="utf-8"/>
        <title>Nagios Map</title>
        <link type="text/css" rel="stylesheet" href="../../css/bootstrap.3.min.css"/>
        <link type='text/css' rel='stylesheet' href='css/common.css'/>
        <link type='text/css' rel='stylesheet' href='css/map.css'/>
        <link type='text/css' rel='stylesheet' href='css/map-directive.css'/>
        <?php if (get_theme() == 'xi5dark') { ?><link type="text/css" rel="stylesheet" href="../../css/themes/modern-dark.css"/><?php } ?>
        <script type="text/javascript" src="../../js/d3/d3.v3.min.js"></script>
        <script type="text/javascript" src="angularjs/angular-1.8.2/angular.min.js"></script>
        <script type="text/javascript" src="angularjs/ui-bootstrap-tpls-0.12.0.min.js"></script>
        <script type="text/javascript" src="angularjs/ui-utils-0.2.1/ui-utils.js"></script>
        <script type="text/javascript" src="js/spin.min.js"></script>
        <script type="text/javascript" src="js/map.js"></script>
        <script type="text/javascript" src="js/map-directive.js"></script>
        <script type="text/javascript" src="js/map-form.js"></script>
        <script type="text/javascript" src="js/nagios-decorations.js"></script>
        <script type="text/javascript" src="js/nagios-time.js"></script>
        <script type="text/javascript">
        var lang = {
            'None': '<?php echo _("None"); ?>',
            'This is a root host': '<?php echo _("This is a root host"); ?>',
            'Unknown': '<?php echo _("Unknown"); ?>'
        };

        // Translation helper function
        function _(str) {
            var trans = lang[str];
            if (trans) { return trans; }
            return str;
        }
        </script>
        <style type="text/css">
        #resize-handle { display: none; }
        </style>
    </head>
    <body ng-controller="mapCtrl">
        <div id="image-cache" style="display: none;"></div>
        <div id="header-container">
            <div info-box cgiurl="{{params.cgiurl}}"
                    decoration-title="{{infoBoxTitle()}}"
                    update-interval="10"
                    last-update="lastUpdate"
                    initial-state="collapsed"
                    collapsable="true"
                    include-partial="map-links.html">
            </div>
        </div>
        <div id="map-container" ng-hide="formDisplayed"
                nagios-map
                cgiurl="{{params.cgiurl}}"
                layout="{{params.layout}}"
                dimensions="{{params.dimensions}}"
                ulx="{{params.ulx}}"
                uly="{{params.uly}}"
                lrx="{{params.lrx}}"
                lry="{{params.lry}}"
                root="params.root"
                maxzoom="params.maxzoom"
                nolinks="{{params.nolinks}}"
                notext="{{params.notext}}"
                nopopups="{{params.nopopups}}"
                noresize="{{params.noresize}}"
                noicons="{{params.noicons}}"
                iconurl="{{params.iconurl}}"
                reload="{{reload}}"
                update-interval="10"
                last-update="lastUpdate"
                map-width="svgWidth"
                map-height="svgHeight"
                build="canBuildMap()">
            </div>

            <div id="menubutton" ng-style="menuButtonStyle()"
                    ng-hide="params.nomenu">
                <button type="button" class="btn"
                        ng-click="displayForm()">
                    <img src="images/menu.png"/>
                </button>
            </div>
        </div>
    </body>
</html>
