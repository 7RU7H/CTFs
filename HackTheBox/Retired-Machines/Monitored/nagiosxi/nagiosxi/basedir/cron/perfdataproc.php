#!/bin/env php -q
<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');

$max_time = 55;
$sleep_time = 10;

$nsca_target_hosts = array();
$nrdp_target_hosts = array();
$enable_nsca = 0;
$enable_nrdp = 0;
$enable_outbound = 0;
$data_files = array();

init_dataprocessor();
process_data();

function init_dataprocessor()
{
    global $nsca_target_hosts;
    global $nrdp_target_hosts;
    global $enable_nsca;
    global $enable_nrdp;
    global $enable_outbound;
    global $outbound_filter_mode;
    global $outbound_host_filters;

    // Make database connections
    $dbok = db_connect_all();
    if (!$dbok) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }
    
    $thr = get_option("nsca_target_hosts");
    if (!empty($thr)) {
        $nsca_target_hosts = unserialize($thr);
    }
        
    $thr = get_option("nrdp_target_hosts");
    if (!empty($thr)) {
        $nrdp_target_hosts = unserialize($thr);
    }

    $enable_nsca = get_option("enable_nsca_output");
    $enable_nrdp = get_option("enable_nrdp_output");
    $enable_outbound = get_option("enable_outbound_data_transfer");
    $outbound_filter_mode = get_option("outbound_data_filter_mode");
    $outbound_host_filters = get_option("outbound_data_host_name_filters");

    if (!$enable_outbound) {
        echo "Outbound data DISABLED ".date('r')."\n";
    }
    
    return;
}

function process_data()
{
    global $max_time;
    global $sleep_time;
    global $enable_outbound; 
    global $cfg;

    $start_time = time();
    $n = 0;
    $t = 0;
    
    // Cfg variables for bulk filemove 
    $dest = grab_array_var($cfg, 'perfdata_spool', "/usr/local/nagios/var/spool/perfdata/");
    $xidpe = grab_array_var($cfg, 'xidpe_dir', "/usr/local/nagios/var/spool/xidpe/"); 

    while (1) {
    
        // Bail if if we've been here too long
        $now = time();
        if (($now - $start_time) > $max_time) {
            break;
        }
    
        // Only process files if outbound transfers are enabled
        if ($enable_outbound) {
            $n = 0;
            $n += process_data_files();
            $t += $n;
        } else {
            // Simple bulk file move so pnp picks up perfdata
            $cmd = 'find '.$xidpe.' -name \'*.*\' -exec mv {} '.$dest.' \;';
            exec($cmd);
        }
    
        // Sleep for a bit if we didn't do anything...
        if ($n == 0) {
            update_sysstat();
            sleep($sleep_time);
        }
    }
        
    update_sysstat();
    echo "\n";
    echo "DONE. Processed $t files.\n";
}

function process_data_files()
{
    global $cfg;
    global $data_files;
    
    $dir = grab_array_var($cfg, 'xidpe_dir', "/usr/local/nagios/var/spool/xidpe/");
    $n = 0;
    
    $cmd = "ls -tr1 $dir";
    exec($cmd, $lines);
    foreach ($lines as $file) {
        if (filetype($dir.$file) != "file") {
            continue;
        }
        process_data_file($dir, $file);
        $n++;
    }
    
    foreach ($data_files as $df) {
        $type = $df['type'];
        $timestamp = $df['timestamp'];
        $d = $df['directory'];
        $f = $df['file'];
        process_perfdata_file($type, $timestamp, $d, $f);
    }
    
    return $n;
}
    
function process_data_file($d, $f)
{
    global $cfg;
    global $data_files;
    global $data;
    
    $parts = explode(".", $f);
    $filetype = $parts[1];

    switch ($filetype) {
        // Performance data file
        case "perfdata":
            $timestamp = $parts[0];
            $type = $parts[2];
            $data_files[$f] = array(
                "type" => $type,
                "timestamp" => $timestamp,
                "directory" => $d,
                "file" => $f,
                "data" => parse_perfdata_file($d.$f)
            );
            break;
        default:
            break;
    }
        
    // Copy the file to the perfdata spool dir, so pnp can graph the data
    $dest = grab_array_var($cfg, 'perfdata_spool', "/usr/local/nagios/var/spool/perfdata/");
    $newf = $type."-perfdata.".$timestamp;
    echo "Copying perfdata file to ".$dest.$newf."\n";
    copy($d.$f, $dest.$newf);
    
    // Remove the file
    unlink($d.$f);
}

function process_perfdata_file($type, $timestamp, $d, $f)
{
    global $cfg;
    global $nsca_target_hosts;
    global $nrdp_target_hosts;
    global $enable_nsca;
    global $enable_nrdp;
    global $enable_outbound;
    global $data_files;

    $use_nsca = false;
    $use_nrdp = false;
    
    echo "\n";
    echo "Processing perfdata file '".$d.$f."'\n";
    
    if ($enable_outbound == false) {
        echo "Outbound data DISABLED - Data will not be send via NSCA or NRDP.\n";
        return;
    }

    // Parse data file
    $data = $data_files[$f]['data'];
    unset($data_files[$f]);
    
    if ($enable_nsca == 1 && count($nsca_target_hosts) > 0) {
        $use_nsca = true;
    }
    
    // Pass it to nsca
    if ($use_nsca == true) {
    
        echo "Sending passive check data to NSCA server(s)...\n";
        
        $total_checks = 0;
    
        // Create the file
        $tmpfname = tempnam("/tmp", "NSCAOUT");
        $fh = fopen($tmpfname, "w");
        foreach ($data as $did => $darr) {
        
            $l = "";
            $datatype = grab_array_var($darr, "DATATYPE");

            if ($datatype == "SERVICEPERFDATA"){
            
                $output = grab_array_var($darr, "SERVICEOUTPUT", "SERVICEOUTPUT macro not found in perfdata - no output available.");
                $output .= '|'.grab_array_var($darr, "SERVICEPERFDATA", "");
                $hostname = grab_array_var($darr, "HOSTNAME", "[NOHOSTNAME]");
                $servicename = grab_array_var($darr, "SERVICEDESC", "[NOSERVICEDESCRIPTION]");
                $stateid = grab_array_var($darr, "STATEID", -2);

                $l = $hostname."\t".$servicename."\t".$stateid."\t".$output."\n".chr(0x17);

                // Check filter
                $fres = filter_outbound_data($hostname, $servicename);
            } else {
                $output = grab_array_var($darr,"HOSTOUTPUT","HOSTOUTPUT macro not found in perfdata - no output available.");
                $output .= '|'.grab_array_var($darr, "HOSTPERFDATA","");
                $hostname = grab_array_var($darr, "HOSTNAME", "[NOHOSTNAME]");
                $stateid = grab_array_var($darr, "STATEID", -2);

                $l = $hostname."\t".$stateid."\t".$output."\n".chr(0x17);

                // Vheck filter
                $fres = filter_outbound_data($hostname, "");
            }

            // Did the host/service name pass the filters?
            if ($fres == true) {
                if (!empty($l)) {
                    fputs($fh, $l);
                    $total_checks++;
                }
            }
        }
        fclose($fh);
        
        // Send to NSCA
        if ($total_checks > 0) {
            foreach ($nsca_target_hosts as $tharr) {
                $address = trim(grab_array_var($tharr, "address"));
                if (empty($address)) {
                    continue;
                }

                $cfg = "/usr/local/nagios/etc/send_nsca-".$address.".cfg";
                
                echo "  Sending to NSCA target host: $address\n";
                $cmdline="/bin/cat ".escapeshellarg($tmpfname)." | /usr/local/nagios/libexec/send_nsca -H ".escapeshellarg($address)." -to 10 -c ".escapeshellarg($cfg);
                echo "    CMDLINE: $cmdline\n";
                system($cmdline);
            }
        } else {
            echo "No checks to send via NSCA\n";
        }
        
        unlink($tmpfname);
    }
    
    if ($enable_nrdp == 1 && count($nrdp_target_hosts) > 0) {
        $use_nrdp = true;
    }
    
    // Pass it to NRDP
    if ($use_nrdp == true) {
    
        echo "Sending passive check data to NRDP server(s)...\n\n";
        
        $total_checks = 0;
    
        // Create the file
        $tmpfname = tempnam("/tmp", "NRDPOUT");
        $fh = fopen($tmpfname, "w");
        foreach ($data as $did => $darr) {
        
            $l = "";
            $datatype = grab_array_var($darr, "DATATYPE");
            
            if ($datatype == "SERVICEPERFDATA") {
                $output = str_replace("&", "%26", encode_form_val(grab_array_var($darr, "SERVICEOUTPUT")));
                $longserviceoutput = str_replace("&", "%26", encode_form_val(grab_array_var($darr, "LONGSERVICEOUTPUT", "")));
                if(!empty($longserviceoutput))
                    $output .= '\n' . $longserviceoutput;
                $output .= '|'.grab_array_var($darr, "SERVICEPERFDATA", "");
                $hostname = grab_array_var($darr, "HOSTNAME");
                $servicename = grab_array_var($darr, "SERVICEDESC");
                $stateid = grab_array_var($darr, "STATEID");

                $l = restrict_size($hostname, "hostname")."\t".restrict_size($servicename, "servicename")."\t".$stateid."\t".restrict_size($output, "output")."\n";

                // Check filter
                $fres = filter_outbound_data($hostname, $servicename);
            } else {
                $output = str_replace("&", "%26", encode_form_val(grab_array_var($darr, "HOSTOUTPUT")));
                $longhostoutput = str_replace("&", "%26", encode_form_val(grab_array_var($darr, "LONGHOSTOUTPUT", "")));
                if(!empty($longhostoutput))
                    $output .= '\n' . $longhostoutput;
                $output .= '|'.grab_array_var($darr, "HOSTPERFDATA", "");
                $hostname = grab_array_var($darr, "HOSTNAME");
                $stateid = grab_array_var($darr, "STATEID");

                $l = restrict_size($hostname, "hostname")."\t".$stateid."\t".restrict_size($output, "output")."\n";

                // Check filter
                $fres = filter_outbound_data($hostname, "");
            }

            // Did the host/service name pass the filters?
            if ($fres == true) {
                if (!empty($l)) {
                    fputs($fh, $l);
                    $total_checks++;
                }
            }
        }
        fclose($fh);
        
        // Send to NRDP
        if ($total_checks > 0) {
            foreach ($nrdp_target_hosts as $h) {
                $address = trim(grab_array_var($h, "address"));
                if (empty($address)) {
                    continue;
                }

                $method = trim(grab_array_var($h, "method", "http"));
                $token = grab_array_var($h, "token");

                $spool_dir = "/tmp/$address";
                if (!is_dir($spool_dir)) {
                    mkdir($spool_dir, 0775);
                }

                if (file_exists($spool_dir.'/spool.xml')) {
                    
                    // Try sending the spooled checks to the NRDP server
                    echo "  Trying to send spooled checks to NRDP target host: $address\n";
                    $cmdline = "/usr/local/nrdp/clients/send_nrdp.sh -u ".escapeshellarg($method."://".$address."/nrdp/")." -t ".escapeshellarg($token)." -f ".escapeshellarg($spool_dir."/spool.xml");
                    echo "    CMDLINE: $cmdline\n";

                    $proc = proc_open($cmdline, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
                    if (is_resource($proc)) {
                        $stdout = stream_get_contents($pipes[1]);
                        fclose($pipes[1]);
                        $stderr = stream_get_contents($pipes[2]);
                        fclose($pipes[2]);
                        $ret = proc_close($proc);
                    }
                    echo "    STDOUT: ".$stdout;
                    if (!empty($stderr)) { echo "    STDERR: ".$stderr."\n"; }
                    echo "    RETURN CODE: ".$ret."\n\n";

                    if ($ret == 0) {
                        unlink($spool_dir.'/spool.xml');
                    }
                }

                /*
                // If there are checks that are waiting to be sent... consolidate them in the spool
                $spooled_files = array();
                if ($h = opendir($spool_dir)) {
                    while (false !== ($file = readdir($h))) {
                        if ($file != "." && $file != ".." && $file != "spool") {
                            $spooled_files[] = $file;
                        }
                    }
                    closedir($h);
                }

                sort($spooled_files, SORT_NUMERIC);
                foreach ($spooled_files as $file) {
                    $c = file_get_contents($spool_dir.'/'.$file);
                    file_put_contents($spool_dir.'/spool', $c, FILE_APPEND);
                    unlink($spool_dir.'/'.$file);
                }
                
                // Load the spool file and try to send the spooled checks
                if (file_exists($spool_dir.'/spool')) {

                    // Try sending the spooled checks to the NRDP server
                    echo "  Trying to send spooled checks to NRDP target host: $address\n";
                    $cmdline = "cat $spool_dir/spool | /usr/local/nrdp/clients/send_nrdp.sh -u ".$method."://".$address."/nrdp/ -t ".$token;
                    echo "    CMDLINE: $cmdline\n";
                    
                    $proc = proc_open($cmdline, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
                    if (is_resource($proc)) {
                        $stdout = stream_get_contents($pipes[1]);
                        fclose($pipes[1]);
                        $stderr = stream_get_contents($pipes[2]);
                        fclose($pipes[2]);
                        $ret = proc_close($proc);
                    }
                    echo "    STDOUT: ".$stdout;
                    if (!empty($stderr)) { echo "    STDERR: ".$stderr."\n"; }
                    echo "    RETURN CODE: ".$ret."\n\n";

                    if ($ret == 0) {
                        unlink($spool_dir.'/spool');
                    }
                }
                */

                echo "  Sending to NRDP target host: $address\n";
                $cmdline = "cat ".escapeshellarg($tmpfname)." | /usr/local/nrdp/clients/send_nrdp.sh -u ".escapeshellarg($method."://".$address."/nrdp/")." -t ".escapeshellarg($token);
                echo "    CMDLINE: $cmdline\n";
                
                $proc = proc_open($cmdline, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
                if (is_resource($proc)) {
                    $stdout = stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    $stderr = stream_get_contents($pipes[2]);
                    fclose($pipes[2]);
                    $ret = proc_close($proc);
                }

                // We now have the following data...
                // $stdout - standard out (if successful)
                // $stderr - standard error (used to verify if there was an error sending NRPE data)
                // $ret - return code
                echo "    STDOUT: ".$stdout;
                if (!empty($stderr)) { echo "    STDERR: ".$stderr."\n"; }
                echo "    RETURN CODE: ".$ret."\n\n";

                if ($ret == 1) {
                    // NRDP wasn't available on the server so let's save the data for later
                    echo "ERROR SENDING OVER NRDP - Spooling checks for passing later.\n\n";

                    // Load XML file from spool
                    if (file_exists($spool_dir.'/spool.xml')) {
                        $xml = @simplexml_load_file($spool_dir.'/spool.xml');
                        if (!$xml) {
                            $xml = @simplexml_load_string("<?xml version='1.0'?><checkresults></checkresults>");
                        }
                    } else {
                        $xml = @simplexml_load_string("<?xml version='1.0'?><checkresults></checkresults>");
                    }
                    
                    $raw = file_get_contents($tmpfname);
                    $lines = explode("\n", $raw);
                    foreach ($lines as $line) {
                        if (empty($line)) {
                            continue;
                        }
                        $items = explode("\t", $line);
                        if (count($items) == 4) {
                            $chkresult = $xml->addChild('checkresult');
                            $chkresult->addAttribute('type', 'service');
                            $chkresult->addAttribute('checktype', '1');
                            $chkresult->addChild('servicename', $items[1]);
                            $chkresult->addChild('hostname', $items[0]);
                            $chkresult->addChild('state', $items[2]);
                            $chkresult->addChild('output', encode_form_val($items[3]));
                            $chkresult->addChild('time', $timestamp);
                        } else {
                            $chkresult = $xml->addChild('checkresult');
                            $chkresult->addAttribute('type', 'host');
                            $chkresult->addAttribute('checktype', '1');
                            $chkresult->addChild('hostname', $items[0]);
                            $chkresult->addChild('state', $items[1]);
                            $chkresult->addChild('output', encode_form_val($items[2]));
                            $chkresult->addChild('time', $timestamp);
                        }
                    }

                    // Save XML file
                    $xml->asXML($spool_dir.'/spool.xml');
                }
            }
        }

        unlink($tmpfname);
    } else {
        //echo "NRDP disabled or not configured - skipping.\n";
    }
}
    
function filter_outbound_data($hn, $sn)
{
    global $outbound_filter_mode;
    global $outbound_host_filters;
    
    /////////////////////////////////////////////////////
    // HOST FILTERS
    /////////////////////////////////////////////////////
    if (empty($outbound_host_filters)) {
        // Always let data through if filters aren't defined
        return true;
    }
    $filters = explode("\r\n", $outbound_host_filters);
    foreach ($filters as $filterraw) {
        $filter = trim($filterraw);
        if (empty($filter)) {
            continue;
        }

        $res = preg_match($filter, $hn);
        if ($res) {
            if ($outbound_filter_mode == "exclude") {
                return false;
            } else {
                return true;
            }
        } else {
            // We might match a future filter
            continue;
        }
    }

    // We reached the end - is this good or bad?
    if ($outbound_filter_mode == "exclude") {
        return true;
    } else {
        return false;
    }
}

function parse_perfdata_file($f)
{
    $discard_soft_states = false;
    $d = array();
    $contents = file_get_contents($f);
    $lines = explode("\n", $contents);
    
    foreach ($lines as $line) {
        $elements = explode("\t", $line);
        $e = array();
        foreach ($elements as $element) {
            $parts = explode("::", $element);
            $var = $parts[0];
            if (empty($var)) {
                continue;
            }
            unset($parts[0]);
            $val = implode("::", $parts);
            $e[$var] = $val;
        }
    
        // Do some checks
        $datatype = grab_array_var($e, "DATATYPE");
        if ($datatype == "SERVICEPERFDATA") {

            $state = grab_array_var($e, "SERVICESTATE");
            $e["STATEID"] = state_str_to_id($state);

            $statetype = grab_array_var($e, "SERVICESTATETYPE");
            if ($discard_soft_states == true && $statetype == "SOFT") {
                continue;
            }
        } else if ($datatype == "HOSTPERFDATA") {

            $state = grab_array_var($e, "HOSTSTATE");
            $e["STATEID"] = state_str_to_id($state);

            $statetype = grab_array_var($e, "HOSTSTATETYPE");
            if ($discard_soft_states == true && $statetype == "SOFT") {
                continue;
            }
        } else {
            continue;
        }
            
        if (count($e) > 0) {
            $d[] = $e;
        }
    }
        
    return $d;
}
    
function state_str_to_id($str)
{
    $sid = -1;
    switch ($str) {
        case "UP":
            $sid = 0;
            break;
        case "DOWN":
            $sid = 1;
            break;
        case "UNREACHABLE":
            $sid = 2;
            break;
        case "OK":
            $sid = 0;
            break;
        case "WARNING":
            $sid = 1;
            break;
        case "UNKNOWN":
            $sid = 3;
            break;
        case "CRITICAL":
            $sid = 2;
            break;
        default:
            echo "NO MATCH FOR STATE '$str'\n";
            break;
    }
    return $sid;
}

function restrict_size($str, $type)
{    
    switch ($type) {
        case "hostname":
            if (strlen($str) > 63) {
                echo "$type truncated\n";
            }
            return substr($str, 0, 63);
        
        case "servicename":
            if (strlen($str) > 127) {
                echo "$type truncated\n";
            }
            return substr($str, 0, 127);
                
        case "output":
            if (strlen($str) > 4095) {
                echo "$type truncated\n";
                $output = explode("|", $str);
                $str = $output[0];
            }
            return substr($str, 0, 4095);
    }
}
    
function update_sysstat()
{
    // Record our run in sysstat table
    $arr = array(
        "last_check" => time()
    );
    $sdata = serialize($arr);
    update_systat_value("perfdataprocessor", $sdata);
}