<?php

// Define some constants to make the CCM think it's in XI
define('BASEDIR', dirname(__FILE__).'/');
define('CFG_ONLY', 1);
global $cfg, $ccm;

// Include CCM codebase
require_once(dirname(__FILE__) . '/../../../config.inc.php');
require_once(dirname(__FILE__) . '/includes/constants.inc.php');
include_once(dirname(__FILE__) . '/includes/common_functions.inc.php');
include_once(dirname(__FILE__) . '/includes/delete_object.inc.php');
include_once(dirname(__FILE__) . '/includes/applyconfig.inc.php');
include_once(dirname(__FILE__) . '/classes/db.class.php');
include_once(dirname(__FILE__) . '/classes/config.class.php');
include_once(dirname(__FILE__) . '/classes/data.class.php');
include_once(dirname(__FILE__) . '/classes/import.class.php');

// Create the CCM object
$ccm = new stdClass();
$ccm->db = new DB($cfg['db_info']['nagiosql']);
$ccm->import = new Import();
$ccm->config = new Config();
$ccm->data = new Data();
