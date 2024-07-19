<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: session.inc.php
//  Desc: Handles all the definitions and includes for the CCM. Also does the authentication
//        using the function in auth.inc.php.
//

// Main includes
require_once(INCDIR.'common_functions.inc.php'); 
require_once(INCDIR.'language.inc.php'); 
require_once(INCDIR.'auth.inc.php'); 
require_once(INCDIR.'hidden_overlay_functions.inc.php'); 

// CCM classes
require_once(CLASSDIR.'config.class.php'); 
require_once(CLASSDIR.'data.class.php');
require_once(CLASSDIR.'import.class.php');
require_once(CLASSDIR.'db.class.php');
require_once(CLASSDIR.'form.class.php');

// CCM main includes 
require_once(TPLDIR.'ccm_table.php');
require_once(INCDIR.'page_router.inc.php');

// Currently we only support single domain configs 
$_SESSION['domain'] = 1;

// Create connections
$ccm = new stdClass();
$ccm->db = new DB($cfg['db_info']['nagiosql']);
$ccm->import = new Import();
$ccm->config = new Config();
$ccm->data = new Data();

// Process $_POST an $_GET variables 
$escape_request_vars = true; 
ccm_grab_request_vars();

// NagiosQL authentication
$AUTH = check_auth();

// Global unique element ID used as a counter 
$unique = 100;

// Nagios XI language settings
ccm_init_language();
    
// Debug mode...
$debug = ccm_grab_request_var('debug', false);
if ($debug == 'enable') { $_SESSION['debug'] = true; }
if ($debug == 'verbose') { $_SESSION['debug'] = 'verbose'; }
if ($debug == 'disable') { unset($_SESSION['debug']); }
