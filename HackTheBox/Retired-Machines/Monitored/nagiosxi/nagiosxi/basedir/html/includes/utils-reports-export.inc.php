<?php
//
// Report Exporting Utilities
// Copyright (c) 2015-2020 Nagios Enterprises, LLC. All rights reserved.
//


define('EXPORT_PDF', 'pdf');
define('EXPORT_JPG', 'jpg');
define('EXPORT_PORTRAIT', 0);
define('EXPORT_LANDSCAPE', 1);


/**
 * Export a report as a PDF file or JPG image
 *
 * @param   string      $reportname         The name of the report file (i.e. 'availability')
 * @param   constant    $type               Msut be EXPORT_PDF (default) or EXPORT_JPG
 * @param   constant    $orientation        Can be either EXPORT_PORTRAIT (default) or EXPORT_LANDSCAPE
 * @param   string      $report_location    Used to override the folder in which the $reportname .php resides
 * @param   string      $filename           The name of the file that runs the report (i.e. index.php or availability.php)
 */
function export_report($reportname, $type = EXPORT_PDF, $orientation = EXPORT_PORTRAIT, $report_location = null, $filename = null)
{
    global $cfg;
    $username = $_SESSION['username'];
    $language = $_SESSION['language'];

    // Do specifics for each type of report
    switch ($type)
    {
        case EXPORT_PDF:
            $bin = 'wkhtmltopdf';
            $content_type = 'application/pdf';
            $opts = ' --lowquality --no-outline --footer-spacing 3 --margin-bottom 15mm --footer-font-size 9 --footer-right "Page [page] of [toPage]" --footer-left "' . get_datetime_string(time(), DT_SHORT_DATE_TIME, DF_AUTO, "null") . '"';
            if ($orientation == EXPORT_LANDSCAPE) {
                $opts .= ' -O landscape';
            }
            $ext = '';
            break;
        case EXPORT_JPG:
            $bin = 'wkhtmltoimage';
            $content_type = 'application/jpg';
            $opts = '';
            $ext = 'jpg';
            break;
        default:
            die(_('ERROR: Could not export report as ') . $type . '. ' . _('This type is not defined.'));
            break;
    }

    // Grab the current URL parts
    $query = array();
    $url_parts = parse_url($_SERVER['REQUEST_URI']);
    $url_parts = explode('&', $url_parts['query']);
    foreach ($url_parts as $p) {
        $part = explode('=', $p);
        if (isset($part[1]) && $part[0] != 'mode') {
            $query[$part[0]] = urldecode($part[1]);
        }
    }

    // Add in some required components to the query
    $query['token'] = user_generate_auth_token(get_user_id($username));
    $query['locale'] = $language;
    $query['records'] = 100000;
    $query['mode'] = 'getreport';
    $query['hideoptions'] = 1;
    $query['export'] = 1;
    $query['old_browser_compat'] = 1;

    // Some report specific changes
    $delay = get_option('report_export_delay', 400);
    if ($reportname == 'auditlog') {
        $report_location = 'admin';
    } else if ($reportname == 'capacityplanning') {
        $report_location = 'includes/components/capacityplanning';
        $delay = get_option('report_export_cp_delay', 1000);
    } else if ($reportname == 'execsummary') {
        $query['records'] = 10;
    }

    // Make sure a report change exists
    if ($report_location === null) {
        $report_location = 'reports';
    }

    // Make filename reportname if it doesn't exist
    if ($filename === null) {
        $filename = $reportname;
    }

    // Wait for javascript to finish
    $addopts = ' --no-stop-slow-scripts';
    $old_browser_compat_jquery1 = get_option('old_browser_compat_jquery1', 1);
    if (!$old_browser_compat_jquery1) {
        $addopts .= ' --javascript-delay '.$delay;
    }

    // Start creating the internal report url we will be calling and make a tempfile name
    $url = get_localhost_url() . $report_location . '/' . urlencode($filename) . '.php?' . http_build_query($query);

    $tempfile = get_tmp_dir() . "/exportreport-" . $username . "-" . uniqid();
    if (!empty($ext)) { $tempfile .= '.'.$ext; }
    $log = $cfg['root_dir'].'/var/wkhtmltox.log';
    $cmd = '/usr/bin/' . $bin . " {$addopts} {$opts} ".escapeshellarg($url)." ".escapeshellarg($tempfile);
    if (file_exists($log) && is_writable($log)) {
        file_put_contents($log, "{$cmd}\n");
        $cmd .= " &>> $log";
    }
    @exec($cmd);
    
    if (!file_exists($tempfile)) {
        $color = "#000";
        if (get_theme() == 'xi5dark') { $color = "#EEE"; }
        echo '<div style="margin: 7% auto; color: '.$color.'; max-width: 80%; text-align: center; font-family: verdana, arial; font-size: 1rem; word-wrap: break-word;">';
        echo '<div><strong>' . _('Failed to create ') . '<span style="text-transform: uppercase;">' . $type . '</span></strong></div>';
        echo '<div style="margin: 10px 0 30px 0;">' . _('Verify that your Nagios XI server can connect to the URL') . ':</div>';
        echo '<div style="font-size: 0.7rem;">' . $url . '</div>';
        echo '</div>';
        die();
    } else {
        header('Content-type: ' . $content_type);
        header('Content-Disposition: inline; filename="' . time() . '-' . $reportname . '.' . $type . '"');
        readfile($tempfile);
        unlink($tempfile);
    }
}