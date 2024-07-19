<?php
//
//  Nagios XI 5 API v1
//  Copyright (c) 2015-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once __DIR__ . '/../../includes/common.inc.php';
require_once __DIR__ . '/../includes/utils-api.inc.php';

if (!defined('BACKEND')) {
    define('BACKEND', true);
}

// Establish database connection
db_connect_all();

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = (!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : "Unknown";
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');

// Set content type based on output type requested
$output_type = isset($_GET['outputtype']) ? $_GET['outputtype'] : 'json';
$_GET['pretty'] = (($output_type == 'json') && isset($_GET['pretty'])) ? $_GET['pretty'] : 0;

$contentTypes = [
    'csv' => 'text/csv',
    'xml' => 'application/xml',
    'json' => 'application/json'
];

header('Content-Type: ' . $contentTypes[$output_type]);

// Process API request
try {
    if (empty($_REQUEST['request'])) {
        throw new Exception('No request was made');
    }
    $api = new API($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    $apiResponse = $api->process_api();
    echo $apiResponse;
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage())) . "\n";
}