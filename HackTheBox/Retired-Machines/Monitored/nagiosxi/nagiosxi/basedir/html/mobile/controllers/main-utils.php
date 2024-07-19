<?php

////////////////////////////////////////////////////////////////////////////////////////
//
//                                      MAIN FUNCTIONS
//
// These functions are intended to be used across the entire mobile platform
////////////////////////////////////////////////////////////////////////////////////////



/*
 * SYNOPSIS: Initializes the page DOM, required PHP controllers and stylesheets
 * 
 * PARAMETERS:
 *
 *      string   $title         Specifies the current page title
 *      array    $includes      An array with two keys, whose values contain additional files to load
 *                              Uses the following format:
 *                              
 *                              $includes = array(
 *                                  'controllers' => array(),
 *                                  'styles'      => array(),
 *                              );
 * 
 * OUTPUT:
 *
 *      html                    Sends the prepared HTML to the client
 * 
 */
function mobile_page_start($title = '', $additional_styles = array(), $additional_controllers = array())
{
    if (empty($title)) {
        $title = _('Nagios XI Mobile');
    }
    $title = sprintf('%s &middot; %s', _('XI Mobile'), $title);
    
    load_controllers($additional_controllers);
    ?>

    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title><?php echo $title; ?></title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <?php load_styles($additional_styles); ?>
            <!-- This page head links function adds a LOT of weight, should be trimmed heavily for mobile -->
            <?php do_page_head_links(); ?>
        </head>
        <body>
        
  <?php
}


/*
 * SYNOPSIS: Closes the page DOM, and loads JavaScript files, including any additional specified
 * 
 * PARAMETERS:
 *
 *      array    $additional_scripts      An array containing the names of additional JavaScript files to load
 *                              
 * 
 * OUTPUT:
 *
 *      html                    Sends the prepared HTML to the client
 * 
 */
function mobile_page_end($additional_scripts = array())
{
    load_scripts($additional_scripts);


    if ( grab_array_var($_SESSION, 'toast', '') != '' ) {
        show_toast($_SESSION['toast']);
    } else {
        ?>
            <div id="toast-container" class="toast">
                
            </div>
        <?php
    }

    ?>

        </body>
    </html>

    <?php
}



function mobile_load_footer()
{
    ?>

        <div class="mobile-footer">
            <div class="footer-icon-container">
                <i class="fas fa-star"></i>
            </div>

            <div class="footer-icon-container">

            </div>

            <grid-container class="footer-icon-container">
                <grid-item><i class="fas fa-sign-out-alt"></i></grid-item>
                <grid-item><span><?php echo _('Sign Out'); ?></span></grid-item>
            </grid-container>
        </div>

    <?php
}

/* Pass an already-translated string to this rendering function */
function mobile_load_navbar($title = '')
{
    ?>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <button type="button" class="navbar-toggle collapsed" >
                    <span class="sr-only"><?php echo _('Toggle navigation'); ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>
                    <span class="navbar-brand"><?php echo $title; ?></span>
                </div>

                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="home.php"><?php echo _('Home'); ?></a></li>

                        <!-- Host Dropdown -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo _('Hosts'); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="hosts.php?view=up"><?php echo _('Up'); ?></a></li>
                                <li><a href="hosts.php?view=down"><?php echo _('Down'); ?></a></li>
                                <li><a href="hosts.php?view=unreachable"><?php echo _('Unreachable'); ?></a></li>
                                <li><a href="hosts.php?view=problems"><?php echo _('Problems'); ?></a></li>
                                <li><a href="hosts.php?view=unhandled"><?php echo _('Unhandled'); ?></a></li>
                            </ul>
                        </li>

                        <!-- Services Dropdown -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo _('Services'); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="services.php?view=ok"><?php echo _('OK'); ?></a></li>
                                <li><a href="services.php?view=warning"><?php echo _('Warning'); ?></a></li>
                                <li><a href="services.php?view=critical"><?php echo _('Critical'); ?></a></li>
                                <li><a href="services.php?view=unknown"><?php echo _('Unknown'); ?></a></li>
                                <li><a href="services.php?view=problems"><?php echo _('Problems'); ?></a></li>
                                <li><a href="services.php?view=unhandled"><?php echo _('Unhandled'); ?></a></li>
                            </ul>
                        </li>

                        <li><a href="<?php echo get_base_url(); ?>"><?php echo _('Return to Desktop'); ?></a></li>
                        <li><a href="login.php?mode=logout"><?php echo _('Logout'); ?></a></li>

                        </ul>
                    </li>
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </nav>
    <?php
}


/*
 * SYNOPSIS: Loads base controllers + any additional controllers if specified
 * 
 * PARAMETERS:
 *
 *      array   $additional_controllers      An array containing any controllers to load
 *                                           above and beyond the base set.
 * 
 * OUTPUT:
 *
 *      bool    true                         Returns true upon completion
 * 
 */
function load_controllers($additional_controllers = array())
{
    $controller_dir = get_base_dir() . '/mobile/controllers';
    // Define base controllers here
    $base_controllers = array(
        "$controller_dir/main-utils.php",
        "$controller_dir/authentication.php"
    );

    // Include base controllers
    foreach ($base_controllers as $controller) {
        require_once($controller);
    }

    // Include additional controllers if specified
    if ( ! empty($additional_controllers) ) {
        foreach ($additional_controllers as $controller) {
            require_once($controller);
        }
    }

    return true;
}


/*
 * SYNOPSIS: Loads base stylesheets + any additional stylesheets if specified
 * 
 * PARAMETERS:
 *
 *      array   $additional_styles           An array containing any stylesheets to load
 *                                           above and beyond the base set.
 * 
 * OUTPUT:
 *
 *      bool    true                         Returns true upon completion
 * 
 */
function load_styles($additional_styles = array())
{
    $css_dir = get_base_url() . 'mobile/static/css';
    $base_styles = array(
        "$css_dir/mobile.css",
        "$css_dir/normalize.css",
        "$css_dir/bootstrap.min.css"
    );

    // Load base styles
    foreach ($base_styles as $stylesheet) {
        echo '<link rel="stylesheet" type="text/css" media="screen" href="' . $stylesheet . '" />';
    }

    // Load additional styles if specified
    if ( ! empty($additional_styles) ) {
        foreach ($additional_styles as $stylesheet) {
            $stylesheet_url = "$css_dir/$stylesheet";

            echo '<link rel="stylesheet" type="text/css" media="screen" href="' . $stylesheet_url . '" />';
        }
    }
    
    return true;
}


/*
 * SYNOPSIS: Loads base JavaScript scripts + any additional scripts if specified
 * 
 * PARAMETERS:
 *
 *      array   $additional_scripts          An array containing any scripts to load
 *                                           above and beyond the base set.
 * 
 * OUTPUT:
 *
 *      bool    true                         Returns true upon completion
 * 
 */
function load_scripts($additional_scripts = array())
{
    $scripts_dir = get_base_url() . 'mobile/static/js';
    // Define base scripts here
    $base_scripts = array(
        "$scripts_dir/all.min.js",
        "$scripts_dir/main.js"
    );

    // Load base scripts
    foreach ($base_scripts as $script) {
        echo '<script src="' . $script . '"></script>';
    }

    // Load additional scripts if specified
    if ( ! empty($additional_scripts) ) {
        foreach ($additional_scripts as $script) {
            echo '<script src="' . $script . '"></script>';
        }
    }
}

function mobile_redirect_to_login($logout = false, $msg = '')
{
    if ($logout) {
        deinit_session();
        init_session();
    }

    // Add message for redirect
    if (!empty($msg)) { 
        $_SESSION["flash_msg"] = $msg;
    }

    $theurl = get_base_url() . "mobile/views/login.php";
    header("Location: " . $theurl);
    exit();
}

function load_image($image_name = '')
{
    $image_dir = get_base_url() . '/mobile/static/img';

    $image_path = $image_dir . '/' . $image_name;

    return $image_path;
}

function get_host_overview_data()
{
    $backendargs = array();
    $backendargs["cmd"] = "gethoststatus";
    $backendargs["limitrecords"] = false;
    $backendargs["totals"] = 1;

    // Host ID limiters
    if (!empty($host_ids)) {
        $backendargs["host_id"] = "in:" . implode(',', $host_ids);
    }

    // Get total hosts
    $xml = get_xml_host_status($backendargs);
    $total_records = 0;
    if ($xml) {
        $total_records = intval($xml->recordcount);
    }

    // Get host totals (up/pending checked later)
    $state_totals = array();
    for ($x = 1; $x <= 2; $x++) {
        $backendargs["current_state"] = $x;
        $xml = get_xml_host_status($backendargs);
        $state_totals[$x] = 0;
        if ($xml) {
            $state_totals[$x] = intval($xml->recordcount);
        }
    }

    // Get up (non-pending)
    $backendargs["current_state"] = 0;
    $backendargs["has_been_checked"] = 1;
    $xml = get_xml_host_status($backendargs);
    $state_totals[0] = 0;
    if ($xml) {
        $state_totals[0] = intval($xml->recordcount);
    }

    // Get pending
    $backendargs["current_state"] = 0;
    $backendargs["has_been_checked"] = 0;
    $xml = get_xml_host_status($backendargs);
    $state_totals[3] = 0;
    if ($xml) {
        $state_totals[3] = intval($xml->recordcount);
    }

    // Total problems

    // Unhandled problems
    $backendargs["current_state"] = "in:1,2";
    unset($backendargs["has_been_checked"]);
    $backendargs["problem_acknowledged"] = 0;
    $backendargs["scheduled_downtime_depth"] = 0;
    $xml = get_xml_host_status($backendargs);
    $unhandled_problems = 0;
    if ($xml) {
        $unhandled_problems = intval($xml->recordcount);
    }

    // Compile Host Data
    $host_overview_data = array(
        'up' => $state_totals[0],
        'down' => $state_totals[1],
        'unreachable' => $state_totals[2],
        'pending' => $state_totals[3],
        'problems' => $state_totals[1] + $state_totals[2],
        'unhandled_problems' => $unhandled_problems
    );

    return $host_overview_data;
}

function get_service_overview_data($host_id = '')
{
    $backendargs = array();
    $backendargs["cmd"] = "getservicestatus";
    $backendargs["limitrecords"] = false;
    $backendargs["totals"] = 1;
    $backendargs["combinedhost"] = true;

    // Host ID limiters
    if (!empty($host_id)) {
        $backendargs["host_id"] = "in:$host_id";
    }

    // Service ID limiters
    if (!empty($service_ids)) {
        $backendargs["service_id"] = "in:" . implode(',', $service_ids);
    }

    // Get total services
    $xml = get_xml_service_status($backendargs);
    $total_records = 0;
    if ($xml) {
        $total_records = intval($xml->recordcount);
    }

    // Get state totals (ok/pending checked later)
    $state_totals = array();
    for ($x = 1; $x <= 3; $x++) {
        $backendargs["current_state"] = $x;
        $xml = get_xml_service_status($backendargs);
        $state_totals[$x] = 0;
        if ($xml) {
            $state_totals[$x] = intval($xml->recordcount);
        }
    }

    // Get ok (non-pending)
    $backendargs["current_state"] = 0;
    $backendargs["has_been_checked"] = 1;
    $xml = get_xml_service_status($backendargs);
    $state_totals[0] = 0;
    if ($xml) {
        $state_totals[0] = intval($xml->recordcount);
    }

    // Get pending
    $backendargs["current_state"] = 0;
    $backendargs["has_been_checked"] = 0;
    $xml = get_xml_service_status($backendargs);
    $state_totals[4] = 0;
    if ($xml) {
        $state_totals[4] = intval($xml->recordcount);
    }

    // Total problems
    $total_problems = $state_totals[1] + $state_totals[2] + $state_totals[3];

    // Unhandled problems
    $backendargs["current_state"] = "in:1,2,3";
    unset($backendargs["has_been_checked"]);
    $backendargs["problem_acknowledged"] = 0;
    $backendargs["scheduled_downtime_depth"] = 0;
    $xml = get_xml_service_status($backendargs);
    $unhandled_problems = 0;
    if ($xml) {
        $unhandled_problems = intval($xml->recordcount);
    }

    // Compile Data for return
    $service_overview_data = array(
        'ok' => $state_totals[0],
        'warning' => $state_totals[1],
        'critical' => $state_totals[2],
        'unknown' => $state_totals[3],
        'problems' => $total_problems,
        'unhandled_problems' => $unhandled_problems
    );

    return $service_overview_data;
}

function get_host_status_badge($current_state)
{
    $current_state = intval($current_state);

    switch ($current_state) {
        case '0':
            $badge_color = 'green';
            $badge_text = _('Up');
            break;
        case '1':
            $badge_color = 'red';
            $badge_text = _('Down');
            break;
        case '2':
            $badge_color =  'orange';
            $badge_text = _('Unreachable');
            break;
    }

    $badge_html = "<span class=\"badge $badge_color\">$badge_text</span>";

    echo $badge_html;
}


function get_service_status_badge($current_state)
{
    $current_state = intval($current_state);

    switch ($current_state) {
        case '0':
            $badge_color = 'green';
            $badge_text = _('OK');
            break;
        case '1':
            $badge_color = 'yellow';
            $badge_text = _('Warning');
            break;
        case '2':
            $badge_color =  'red';
            $badge_text = _('Critical');
            break;
        case '3':
            $badge_color =  'orange';
            $badge_text = _('Unknown');
            break;
    }

    $badge_html = "<span class=\"badge $badge_color\">$badge_text</span>";

    echo $badge_html;
}


function seconds_to_time($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format(_('%a days, %h hours, %i minutes and %s seconds'));
}

function show_zero_record_card()
{
    ?>
        <div class="card xsmall zero-record-card">
            <h1><?php echo _('No Records Found'); ?></h1>
        </div>
    <?php
}

function show_toast($message)
{
    if ( ! isset($_SESSION['toast']) ) {
        return false;
    }

    if ( isset($_SESSION['toast_type']) ) {
        $type = $_SESSION['toast_type'];
    }

    $message = $_SESSION['toast'];
    $message = encode_form_val($message);

    ?>
    
    <div class="toast">
        <p><?php echo "$message"; ?></p>
    </div>

    <script type="text/javascript">
        show_toast();
    </script>

    <?php

    // Clear the message
    unset($_SESSION['toast']);
    unset($_SESSION['toast_type']);
}

function set_toast($message = '', $type = '')
{
    // Allow for AJAX calls
    if ( empty($message) ) {
        $message = grab_request_var('message', '');
    }

    if ( empty($type) ) {
        $type = grab_request_var('toast_type', '');
    }

    if ( $message != '' ) {
        unset($_SESSION['toast']);
        $_SESSION['toast'] = encode_form_val($message);
    }

    if ( $message != '' ) {
        unset($_SESSION['toast_type']);
        $_SESSION['toast_type'] = encode_form_val($type);
    }
    return true;
}

/**
 * Get AJAX JS and HTML code for running a Nagios Core command
 *
 * @param   array   $cmdarr     Command data
 * @return  string              JS/HTML
 */
function mobile_get_nagioscore_command_ajax_code($cmdarr, $callback = '')
{
    $args = array();
    if (!empty($cmdarr["multi_cmd"]))
        foreach ($cmdarr["multi_cmd"] as $k => $command_args)
            foreach ($command_args["command_args"] as $var => $val)
                $args['multi_cmd'][$k][$var] = $val;
            
    if (!empty($cmdarr["command_args"])) {
        foreach ($cmdarr["command_args"] as $var => $val) {
            $args[$var] = $val;
        }
    }

    $cmddata = json_encode($args);

    if ($callback != '') {
        $clickcmd = "onClick='mobile_submit_command(this, " . COMMAND_NAGIOSCORE_SUBMITCOMMAND . "," . $cmddata . ",\"" . $callback . "\")'";
    } else {
        $clickcmd = "onClick='mobile_submit_command(this, " . COMMAND_NAGIOSCORE_SUBMITCOMMAND . "," . $cmddata . ")'";
    }

    return $clickcmd;
}

function get_notification_ajax($mode = 'disable', $host_name = '', $service_name = '', $callback = '')
{
    $auth_command = is_authorized_for_service_command(0, $host_name, $service_name);
    if ($auth_command) {

        $cmd = array(
            "command" => COMMAND_NAGIOSCORE_SUBMITCOMMAND,
        );

        if ( $service_name != '' ) {
            $cmd["command_args"] = array(
                "host_name" => $host_name,
                "service_name" => $service_name,
            );

            if ( $mode == 'disable') {
                $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DISABLE_SVC_NOTIFICATIONS;
            } else {
                $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_ENABLE_SVC_NOTIFICATIONS;
            }
        } else {
            $cmd["command_args"] = array(
                "host_name" => $host_name,
            );

            if ( $mode == 'disable') {
                $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_DISABLE_HOST_NOTIFICATIONS;
            } else {
                $cmd["command_args"]["cmd"] = NAGIOSCORE_CMD_ENABLE_HOST_NOTIFICATIONS;
            }
        }
        
        

        if ($callback != '') {
            $command = mobile_get_nagioscore_command_ajax_code($cmd, $callback);
        } else {
            $command = mobile_get_nagioscore_command_ajax_code($cmd);
        }

        return $command;
    } else {
        // TODO: This needs to be fleshed out
        return false;
    }
}

function get_acknowledge_ajax($host_name = '', $service_name = '', $callback = '')
{
    $auth_command = is_authorized_for_service_command(0, $host_name, $service_name);
    if ($auth_command) {

        $cmd = array(
            "command" => COMMAND_NAGIOSCORE_SUBMITCOMMAND,
        );

        if ( $service_name != '' ) {
            $cmd["command_args"] = array(
                "host_name"    => $host_name,
                "service_name" => $service_name,
                "cmd"          => NAGIOSCORE_CMD_ACKNOWLEDGE_SVC_PROBLEM
            );
        } else {
            $cmd["command_args"] = array(
                "host_name" => $host_name,
                "cmd"          => NAGIOSCORE_CMD_ACKNOWLEDGE_HOST_PROBLEM
            );
        }
        
        

        if ($callback != '') {
            $command = mobile_get_nagioscore_command_ajax_code($cmd, $callback);
        } else {
            $command = mobile_get_nagioscore_command_ajax_code($cmd);
        }

        return $command;
    } else {
        // TODO: This needs to be fleshed out
        return false;
    }
}

function check_mobile_authentication()
{
    if (is_authenticated() == false) {
        mobile_redirect_to_login();
    }
}