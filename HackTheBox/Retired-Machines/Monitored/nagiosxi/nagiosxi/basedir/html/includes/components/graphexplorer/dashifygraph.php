<?php
ob_start();

require_once(dirname(__FILE__) . '/../../common.inc.php');
include_once(dirname(__FILE__) . '/dashlet.inc.php');
require_once(dirname(__FILE__) . '/visFunctions.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);
check_nagios_session_protector();

// Process variables and submit to dashboard 
$url = grab_request_var('url');
$title = grab_request_var('dashletName');
$board = grab_request_var('boardName');

// Generate a div ID
$div_id = "visContainer" . uniqid();

$data = explode("&", $url);
foreach ($data as &$d) {
    if (strpos($d, "div=") !== false) {
        $d = "div=" . $div_id;
    }
}
$url = implode("&", $data);

$dashleturl = '/includes/components/graphexplorer/' . $url;
$dargs = array('url' => $dashleturl, 'divId' => $div_id);

add_dashlet_to_dashboard(0, $board, 'graphexplorer', $title, null, $dargs);

/* Old way...
$returnto = urlencode($url); 
$newurl=get_base_url().'/includes/components/graphexplorer/?r='.$returnto;  
header("Location: $newurl"); 
*/

// New way ... just prints json
print json_encode(array('success' => 1));

ob_end_flush(); 

