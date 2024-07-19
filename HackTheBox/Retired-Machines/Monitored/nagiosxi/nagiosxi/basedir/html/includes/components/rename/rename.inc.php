<?php
//
// Renaming Tool Component
// Copyright (c) 2010-2019 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$rename_component_name = "rename";
rename_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function rename_component_init()
{
    global $rename_component_name;

    // Boolean to check for latest version
    $versionok = rename_component_checkversion();

    // Component description
    $desc = _("This component allows administrators to manage renaming of hosts and services in bulk.");

    if (!$versionok) {
        $desc = "<b>" . _("Error: This component requires Nagios XI 2012R1.0 or later with Enterprise Features enabled.") . "</b>";
    }

    // All components require a few arguments to be initialized correctly.  
    $args = array(
        COMPONENT_NAME => $rename_component_name,
        COMPONENT_VERSION => '1.7.0',
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => $desc,
        COMPONENT_TITLE => _("Bulk Renaming Tool"),
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    // Register this component with XI 
    register_component($rename_component_name, $args);

    // Only add this menu if the user is an admin / register the addmenu function
    if ($versionok) {
        register_callback(CALLBACK_MENUS_INITIALIZED, 'rename_component_addmenu');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


function rename_component_checkversion()
{
    if (!function_exists('get_product_release') || get_product_release() < 5500) {
        return false;
    }
    return true;
}


function rename_component_addmenu($arg = null)
{
    global $rename_component_name;
    global $menus;

    // Retrieve the URL for this component
    $urlbase = get_component_url_base($rename_component_name);

    // Add to the new ccm if it is installed 
    add_menu_item(MENU_CCM, array(
        "type" => "link",
        "title" => _("Bulk Renaming Tool"),
        "id" => "menu-ccm-rename",
        "order" => 802.75,
        "opts" => array(
            "href" => $urlbase . "/rename.php",
            "icon" => "fa fa-tags"
        )
    ));
}


/**
 * Creates a directory with new host name, moves perfdata files there
 * This function creates a new directory with apache.apache ownership, so the reconfigure_nagios.sh
 * script has to update permissions to nagios.nagios to PNP can write to it
 *
 * @param mixed  $host_array host information returned from form
 * @param string $msg        REFERENCE variable to output string
 * @return int $errors error count from function
 */
function rename_host_perfdata($host_array, &$msg)
{
    global $cfg;
    $errors = 0;

    // Directories 
    $perfdir = grab_array_var($cfg, 'perfdata_dir', '/usr/local/nagios/share/perfdata');
    $hostdir = pnp_convert_object_name($host_array['old_name']); //cleaned variable
    $destdir = $perfdir . '/' . pnp_convert_object_name($host_array['host_name']); //cleaned variable
    $xmlfile = $destdir . '/' . '_HOST_.xml';

    // Does this host have performance data??
    if (!file_exists($perfdir . '/' . $hostdir)) {
        return $errors;
    }

    // If names are the same, don't update file locations
    if (pnp_convert_object_name($host_array['old_name']) == pnp_convert_object_name($host_array['host_name'])) {
        return $errors;
    }

    // Make new directory
    $cmd = "mkdir -p " . $destdir;
    $output = system($cmd, $code);
    if ($code > 0) {
        $errors++;
        $msg .= _("Unable to create directory") . ": $destdir $output<br />";
        return $errors;
    }
    $cmd2 = "chmod 775 " . $destdir;
    $output = system($cmd2, $code);
    if ($code > 0) {
        $errors++;
        $msg .= _("Unable to change permissions on directory") . ": $destdir $output<br />";
        return $errors;
    }

    // Update xml files
    $files = scandir($perfdir . '/' . $hostdir);
    foreach ($files as $file) {
        if (!strpos($file, '.xml')) continue;

        // Get file contents
        $f = file_get_contents($perfdir . '/' . $hostdir . '/' . $file);
        if (!$f) {
            $errors++;
            $msg .= _("Unable to update performance data XML file:") . " $file<br />";
        }

        // Create new files
        $newf = str_replace($host_array['old_name'], $host_array['host_name'], $f);
        if (!file_put_contents($destdir . '/' . $file, $newf)) {
            $errors++;
            $msg .= _("Unable to update performance data XML file") . ": $file<br />";
        } else {
            // file_put_contents succeeded, update perms
            chmod($destdir . '/' . $file, 0644);
        }
    }

    // Remove old files
    $cmd = "mv -f " . $perfdir . '/' . $hostdir . '/*.rrd ' . $destdir;
    $output = system($cmd, $code);
    if ($code > 0 && $output != false) {
        $errors++;
        if ($output != false) {
            $msg .= $output . "<br />";
        }
    }
    
    $cmd = "rm -rf " . $perfdir . '/' . $hostdir;
    $output = system($cmd, $code);
    if ($code > 0 && $output != false) {
        $errors++;
        if ($output != false) {
            $msg .= $output . "<br />";
        }
    }

    return $errors;
}


/**
 * Update filenames and XML entries for service performance data
 * This function creates a new xml / rrd file with apache.apache ownership, so the reconfigure_nagios.sh
 * script has to update permissions to nagios.nagios to PNP can write to it
 *
 * @param mixed  $array service information returned from form
 * @param string $msg   REFERENCE variable to output string
 * @return int $errors error count from function
 */
function rename_service_perfdata($array, &$msg)
{
    global $cfg;
    $errors = 0;

    // Directories 
    $perfdir = grab_array_var($cfg, 'perfdata_dir', '/usr/local/nagios/share/perfdata');

    // Will need to fetch related host names from ndoutils function
    $hosts =& $array['hosts'];

    // Each iteration handles a host:service combination
    foreach ($hosts as $host) {
        $destdir = $perfdir . '/' . pnp_convert_object_name($host) . '/';
        $servicefile = $destdir . pnp_convert_object_name($array['service_description']);
        $oldfile = $destdir . pnp_convert_object_name($array['old_name']);

        if ($servicefile == $oldfile) {
            continue;
        }

        $newrrd = $servicefile . '.rrd';
        $newxml = $servicefile . '.xml';
        $oldxml = $oldfile . '.xml';
        $oldrrd = $oldfile . '.rrd';

        // Does this service have performance data??
        if (!file_exists($oldxml) && !file_exists($oldrrd)) {
            return $errors;
        }

        // Update xml files
        $f = file_get_contents($oldxml);
        if (!$f) {
            $errors++;
            $msg .= "Unable to update performance data XML file: $newxml<br />";
            return $errors;
        }

        // Create new files
        $newf = str_replace($array['old_name'], $array['service_description'], $f);
        if (!file_put_contents($newxml, $newf)) {
            $errors++;
            $msg .= "Unable to update service description in performance data XML file: $oldxml<br />";
        } else {
            // file_put_contents succeeded, so set ownership to nagios.nagios
            chown($newxml, 'nagios:nagios');
        }

        // Move (rename) files and delete old files
        $cmd1 = 'rm -f ' . $oldxml;
        $output = system($cmd1, $code);
        if ($code > 0) {
            $errors++;
            $msg .= "Unable to run: $cmd1. $output<br />";
        }
        $cmd2 = 'mv -f ' . $oldfile . '.rrd ' . $newrrd;
        $output = system($cmd2, $code);
        if ($code > 0) {
            $errors++;
            $msg .= "Unable to run: $cmd2. $output <br />";
        }
        $cmd3 = 'chmod 644 ' . $newxml;
        $output = system($cmd3, $code);
        if ($code > 0) {
            $errors++;
            $msg .= "Unable to run: $cmd3. $output <br />";
        }
    }

    return $errors;
}


/**
 * Runs multiple SQL queries and gets all related hosts for a particular service.
 * This function does NOT handle service->hostgroup->hostgroup->host relationships, but should do everything else
 *
 * @param   int     $id             Service ID from nagiosql tbl_service
 * @param   string  $hoststring     REFERENCE variable to hoststring that will be used in SQL IN() search
 * @return  mixed   $hosts          Array of all related hosts
 */
function get_service_to_host_relationships($id, &$hoststring)
{
    $hoststring = '';

    // Handle service->host relationships
    $query = "SELECT a.id,a.host_name FROM tbl_lnkServiceToHost b JOIN  tbl_host a ON a.id=b.idSlave WHERE b.idMaster={$id}";
    $rs = exec_sql_query(DB_NAGIOSQL, $query, true);

    foreach ($rs as $row) {
        $hoststring .= "'" . $row['host_name'] . "',";
        $hosts[] = $row['host_name'];
    }

    // Check for service->hostgroup relationships and check relationships for hostgroup->host
    $query = "SELECT b.host_name FROM tbl_lnkHostgroupToHost a
            JOIN tbl_host b ON a.idSlave=b.id WHERE idMaster 
            IN (SELECT idSlave FROM tbl_lnkServiceToHostgroup WHERE idMaster={$id})";
    $rs = exec_sql_query(DB_NAGIOSQL, $query, true);
    if ($rs && $rs->recordCount() > 0) {
        foreach ($rs as $row) {
            $hoststring .= "'" . $row['host_name'] . "',";
            $hosts[] = $row['host_name'];
        }
    }

    // Check relationships host->hostgroup
    $query = "SELECT b.host_name FROM tbl_lnkHostToHostgroup a
            JOIN tbl_host b ON a.idMaster=b.id WHERE idSlave 
            IN (SELECT idSlave FROM tbl_lnkServiceToHostgroup WHERE idMaster={$id})";

    $rs = exec_sql_query(DB_NAGIOSQL, $query, true);
    if ($rs && $rs->recordCount() > 0) {
        foreach ($rs as $row) {
            $hoststring .= "'" . $row['host_name'] . "',";
            $hosts[] = $row['host_name'];
        }
    }

    // Remove last comma
    $hoststring = substr($hoststring, 0, (strlen($hoststring) - 1));

    return $hosts;
}
