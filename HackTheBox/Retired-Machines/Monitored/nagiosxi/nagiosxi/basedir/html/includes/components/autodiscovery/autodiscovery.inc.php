<?php
// 
// Auto-Discovery Component
// Copyright (c) 2010-2018 Nagios Enterprises, LLC. All rights reserved. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$autodiscovery_component_name = "autodiscovery";
autodiscovery_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function autodiscovery_component_init()
{
    global $autodiscovery_component_name;

    $versionok = autodiscovery_component_checkversion();

    $desc = "";
    if (!$versionok) {
        $desc = "<br><b>" . _("Error: This component requires Nagios XI 5.2.1 or later.") . "</b>";
    }

    $installok = autodiscovery_component_checkinstall($installed, $prereqs, $missing_components);
    if (!$installok) {
        $desc .= "<br><b>" . _("Error: Required setup has not been completed.") . " <a href='" . get_base_url() . "/includes/components/autodiscovery/'>" . _("Learn more") . "</a>.</b>";
    }

    $args = array(
        COMPONENT_NAME => $autodiscovery_component_name,
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => _("Provides device and service auto-discovery. ") . $desc,
        COMPONENT_TITLE => _("Auto-Discovery"),
        COMPONENT_VERSION => '2.2.6',
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    register_component($autodiscovery_component_name, $args);

    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'autodiscovery_component_addmenu');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// JOB FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


function autodiscovery_component_getjobs()
{
    $jobs = array();
    $jobs_s = get_option("autodiscovery_jobs");
    if ($jobs_s == "" || $jobs_s == null) {
        autodiscovery_component_savejobs($jobs);
    } else {
        $jobs = unserialize($jobs_s);
    }
    return $jobs;
}


function autodiscovery_component_savejobs($jobs)
{
    set_option("autodiscovery_jobs", serialize($jobs));
}


function autodiscovery_component_addjob($jobid, $job)
{
    $jobid = preg_replace("/[^a-zA-Z0-9]/", "", $jobid);
    $jobs = autodiscovery_component_getjobs();
    $jobs[$jobid] = $job;
    autodiscovery_component_savejobs($jobs);
}


function autodiscovery_component_get_jobid($jobid)
{
    $jobid = preg_replace("/[^a-zA-Z0-9]/", "", $jobid);
    $jobs = autodiscovery_component_getjobs();
    if (array_key_exists($jobid, $jobs)) {
        return $jobs[$jobid];
    }
    return null;
}


function autodiscovery_component_delete_jobid($jobid)
{
    $jobid = preg_replace("/[^a-zA-Z0-9]/", "", $jobid);
    $jobs = autodiscovery_component_getjobs();
    if (array_key_exists($jobid, $jobs)) {
        unset($jobs[$jobid]);
    }
    autodiscovery_component_savejobs($jobs);
    autodiscovery_component_delete_cron($jobid);
}


function autodiscovery_component_update_cron($id)
{
    $id = preg_replace("/[^a-zA-Z0-9]/", "", $id);
    $croncmd = autodiscovery_component_get_cron_cmdline($id);
    $crontimes = autodiscovery_component_get_cron_times($id);

    $cronline = sprintf("%s\t%s > /dev/null 2>&1\n", $crontimes, $croncmd);
    $tmpfile = get_tmp_dir() . "/scheduledreport." . $id;
    file_put_contents($tmpfile, $cronline);

    $cmd = "crontab -l | grep -v " . escapeshellarg($croncmd) . " | cat - " . escapeshellarg($tmpfile) . " | crontab - ; rm -f " . escapeshellarg($tmpfile);
    exec($cmd);
}


function autodiscovery_component_delete_cron($id)
{
    $id = preg_replace("/[^a-zA-Z0-9]/", "", $id);
    $croncmd = get_base_dir() . "/includes/components/autodiscovery/jobs/" . $id;
    $cmd = "crontab -l | grep -v " . escapeshellarg($croncmd) . " | crontab -";
    exec($cmd);
}


function autodiscovery_component_get_cron_cmdline($id)
{
    $id = preg_replace("/[^a-zA-Z0-9]/", "", $id);
    $cmdline = autodiscovery_component_get_cmdline($id);
    $cmd = $cmdline;
    return $cmd;
}


function autodiscovery_component_prep_job_files($jobid)
{
    $jobid = preg_replace("/[^a-zA-Z0-9]/", "", $jobid);
    $base_dir = get_component_dir_base("autodiscovery");
    $jobs_dir = $base_dir . "/jobs/";

    $watch_file = $jobs_dir . $jobid . ".watch";
    $out_file = $jobs_dir . $jobid . ".out";
    $xml_file = $jobs_dir . $jobid . ".xml";

    // Make sure permissions are correct on existing file (if we're rerunning the job)
    chmod($watch_file, 0660);
    chmod($out_file, 0660);
    chmod($xml_file, 0660);

    // Delete existing xml file (if we're rerunning the job)
    unlink($xml_file);
}


function autodiscovery_component_get_cmdline($jobid)
{
    $jobid = preg_replace("/[^a-zA-Z0-9]/", "", $jobid);
    $base_dir = get_component_dir_base("autodiscovery");
    $script_dir =  get_root_dir() . "/scripts/components/";
    $jobs_dir = $base_dir . "/jobs/";

    $watch_file = $jobs_dir . $jobid . ".watch";
    $out_file = $jobs_dir . $jobid . ".out";
    $xml_file = $jobs_dir . $jobid . ".xml";

    $jarr = autodiscovery_component_get_jobid($jobid);
    if (empty($jarr)) {
        return false;
    }

    $address = grab_array_var($jarr, "address", "127.0.0.1");
    $exclude_address = grab_array_var($jarr, "exclude_address");
    $os_detection = grab_array_var($jarr, "os_detection", "off");
    $topology_detection = grab_array_var($jarr, "topology_detection", "off");
    $system_dns = grab_array_var($jarr, "system_dns", "off");
    $scandelay = grab_array_var($jarr, "scandelay", "");
    $custom_ports = grab_array_var($jarr, "custom_ports", "");

    $osd = "";
    if ($os_detection == "on") {
        $osd = "--detectos=1";
    }
    $topod = "";
    if ($topology_detection == "on") {
        $topod = "--detecttopo=1";
    }
    $sdns = "";
    if ($system_dns == "on") {
        $sdns = "--system-dns=1";
    }
    $scan_delay = "";
    if (!empty($scandelay)) {
        $scan_delay = "--scandelay=".intval($scandelay);
    }

    if (!empty($custom_ports)) {
        $custom_ports = "--customports=".escapeshellarg($custom_ports);
    }
    
    $cmd = "rm -f " . escapeshellarg($xml_file) . "; touch " . escapeshellarg($watch_file) . "; sudo /usr/bin/php " . $script_dir . "autodiscover_new.php --addresses=" . escapeshellarg($address) . " --exclude=" . escapeshellarg($exclude_address) . " --output=" . escapeshellarg($jobid . ".xml") . " --watch=" . escapeshellarg($watch_file) . " --onlynew=0 --debug=1 " . $osd . " " . $topod . " " . $scan_delay . " " . $sdns . " " . $custom_ports . " > " . escapeshellarg($out_file) . " 2>&1 & echo $!";

    return $cmd;
}


function autodiscovery_component_get_cron_times($jobid)
{
    $times = "";

    $sj = autodiscovery_component_get_jobid($jobid);
    if ($sj == null)
        return $times;

    $frequency = grab_array_var($sj, "frequency", "");

    $sched = grab_array_var($sj, "schedule", array());
    $hour = grab_array_var($sched, "hour", 0);
    $minute = grab_array_var($sched, "minute", 0);
    $ampm = grab_array_var($sched, "ampm", "AM");
    $dayofweek = grab_array_var($sched, "dayofweek", 0);
    $dayofmonth = grab_array_var($sched, "dayofmonth", 1);

    $h = intval($hour);
    $m = intval($minute);
    if ($ampm == "PM" && $h != 12)
        $h += 12;
    if (($ampm == "AM" && $h == 12))
        $h = 0;
    if ($frequency == "Monthly")
        $dom = $dayofmonth;
    else
        $dom = "*";
    if ($frequency == "Weekly")
        $dow = $dayofweek;
    else
        $dow = "*";

    $times = sprintf("%d %d %s * %s", $m, $h, $dom, $dow);

    echo "CRON TIMES: $times<BR>";
    //exit();

    return $times;
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

function autodiscovery_component_checkversion()
{
    if (!function_exists('get_product_release')) {
        return false;
    }
    if (get_product_release() < 512) {
        return false;
    }
    return true;
}

function autodiscovery_component_checkinstall(&$installed, &$prereqs, &$missing_components)
{
    global $autodiscovery_component_name;

    $installed = true;
    $prereqs = true;
    $missing_components = "";

    $base_dir = get_component_dir_base($autodiscovery_component_name);

    // make sure pre-reqs are installed
    if (!file_exists("/usr/sbin/fping") && !file_exists('/usr/bin/fping')) {
        $missing_components .= "<li><b>fping</b> (/usr/sbin/fping or /usr/bin/fping)</li>";
        $prereqs = false;
    }
    if (!file_exists("/bin/traceroute") && !file_exists('/usr/bin/traceroute')) {
        $missing_components .= "<li><b>traceroute</b> (/bin/traceroute or /usr/bin/traceroute)</li>";
        $prereqs = false;
    }
    if (!file_exists("/usr/bin/nmap")) {
        $missing_components .= "<li><b>nmap</b> (/usr/bin/nmap)</li>";
        $prereqs = false;
    }

    if ($installed == false || $prereqs == false)
        return false;
    return true;
}

function autodiscovery_component_addmenu($arg = null)
{
    global $autodiscovery_component_name;

    $mi = find_menu_item(MENU_CONFIGURE, "menu-configure-monitoringwizard", "id");
    if ($mi == null)
        return;

    $order = grab_array_var($mi, "order", "");
    if ($order == "")
        return;

    $neworder = $order + 0.1;

    add_menu_item(MENU_CONFIGURE, array(
        "type" => "link",
        "title" => _("Auto-Discovery"),
        "id" => "menu-configure-autodiscovery",
        "order" => $neworder,
        "opts" => array(
            "href" => get_base_url() . 'includes/components/autodiscovery/',
            "icon" => "fa-eye"
        )
    ));

}

function autodiscovery_component_parse_job_data($jobid = "", &$new_hosts = 0, &$total_hosts = 0)
{
    $services = array();

    $jobid = preg_replace("/[^a-zA-Z0-9]/", "", $jobid);
    $base_dir = get_component_dir_base("autodiscovery");
    $output_file = $base_dir . "/jobs/" . $jobid . ".xml";

    $total_hosts = 0;
    $new_hosts = 0;
    $xml = @simplexml_load_file($output_file);
    if ($xml) {

        foreach ($xml->device as $d) {

            $status = strval($d->status);
            $address = strval($d->address);
            $fqdns = strval($d->fqdns);
            $macvendor = strval($d->macvendor);

            $total_hosts++;
            if ($status == "new")
                $new_hosts++;

            $services[$address] = array(
                "address" => $address,
                "fqdns" => $fqdns,
                "type" => "Unknown",
                "os" => "",
                "status" => $status,
                "ports" => array(),
                "macvendor" => $macvendor
            );

            // get ports
            foreach ($d->ports->port as $p) {

                $protocol = strval($p->protocol);
                $port = strval($p->port);
                $state = strval($p->state);

                if ($state != "open")
                    continue;

                $services[$address]["ports"][] = array(
                    "protocol" => $protocol,
                    "port" => $port,
                    "service" => getservbyport($port, strtolower($protocol)),
                );
            }

            // Get operating system (first one in list)
            $services[$address]["os"] = strval($d->operatingsystems->osinfo->osname);
            $services[$address]["osaccuracy"] = intval($d->operatingsystems->osinfo->osaccuracy);

            // Get device type
            $services[$address]["type"] = get_autodiscovery_type($services[$address]["os"]);

        }
    }

    return $services;
}

function get_autodiscovery_type($osname)
{
    $type = _('Unknown');

    do {

        if (strpos($osname, 'Windows XP') !== false || strpos($osname, 'Windows Vista') !== false || strpos($osname, 'Windows 7') !== false || strpos($osname, 'Windows 8') !== false) {
            $type = _('Windows Workstation');
            break;
        }

        if (strpos($osname, 'Windows Server') !== false || strpos($osname, 'Windows') !== false) {
            $type = _('Windows Server');
            break;
        }

        if (strpos($osname, 'Linux') !== false) {
            $type = _('Linux Server');
            break;
        }

        if (strpos($osname, 'Solaris') !== false || strpos($osname, 'AIX') !== false || strpos($osname, 'HP-UX') !== false) {
            $type = _('UNIX Server');
            break;
        }

        if (strpos($osname, 'Apple Mac OS X') !== false) {
            $type = _('Apple Workstation');
            break;
        }

        if (strpos($osname, 'Apple Mac OS X Server') !== false) {
            $type = _('Apple Server');
            break;
        }

        if (strpos($osname, 'OpenBSD') !== false) {
            $type = _('Linux Server');
            break;
        }

        if (stripos($osname, 'phone') !== false) {
            $type = _('IP Phone');
            break;
        }

        if (stripos($osname, 'switch') !== false) {
            $type = _('Switch');
            break;
        }

        if (stripos($osname, 'router') !== false) {
            $type = _('Router');
            break;
        }

    } while (false);

    return $type;
}