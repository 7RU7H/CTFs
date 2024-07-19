#!/bin/env php -q
<?php
//
// Command Subsystem Cron
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

define("SUBSYSTEM", 1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/common.inc.php');

$max_time = 59;
$logging = true;

init_cmdsubsys();
do_cmdsubsys_jobs();

function init_cmdsubsys()
{
    // Make database connections
    $dbok = db_connect_all();
    if ($dbok == false) {
        echo "ERROR CONNECTING TO DATABASES!\n";
        exit();
    }

    return;
}

function do_cmdsubsys_jobs()
{
    global $max_time;
    global $logging;

    // Enable logging?  
    $logging = is_null(get_option('enable_subsystem_logging')) ? true : get_option("enable_subsystem_logging");
    
    $start_time = time();
    $t = 0;

    while(1) {
        $n = 0;

        // Bail if if we're been here too long
        $now = time();
        if (($now - $start_time) > $max_time) {
            break;
        }

        $n += process_commands();
        $t += $n;
        
        // Sleep for 1 second if we didn't do anything...
        if ($n == 0) {
            update_sysstat();
            if ($logging) {
                echo ".";
            }
            usleep(1000000);
        }
    }

    update_sysstat();
    echo "\n";
    echo "PROCESSED $t COMMANDS\n";

    // Handle misc background jobs (update checks, etc)
    do_uloop_jobs();
}   
    
function update_sysstat()
{
    // Record our run in sysstat table
    $arr = array(
        "last_check" => time()
    );
    $sdata = serialize($arr);
    update_systat_value("cmdsubsys", $sdata);
}

function process_commands()
{
    global $db_tables;
    global $cfg;

    // Get the next queued command
    $sql = "SELECT * FROM ".$db_tables[DB_NAGIOSXI]["commands"]." WHERE status_code='0' AND event_time<=NOW() ORDER BY submission_time ASC";
    $args = array(
        "sql" => $sql,
        "useropts" => array(
            "records" => 1
        )
    );
    $sql = limit_sql_query_records($args, DB_NAGIOSXI);
    if (($rs = exec_sql_query(DB_NAGIOSXI, $sql, true, false))) {
        if (!$rs->EOF) {
            process_command_record($rs);
            return 1;
        }
    }
    return 0;
}
    
function process_command_record($rs)
{
    global $db_tables;
    global $cfg;
    global $logging;
    
    if ($logging) {
        echo "PROCESSING COMMAND ID ".$rs->fields["command_id"]."...\n";
    }
    
    $command_id = $rs->fields["command_id"];
    $command = intval($rs->fields["command"]);
    $command_data = $rs->fields["command_data"];
    $userid = $rs->fields["submitter_id"];
    $username = get_user_attr($userid, "username");
    
    // If the command is htaccess hide the password in the db -JO
    $clear_command_data = "";
    if ($command == COMMAND_NAGIOSXI_SET_HTACCESS) {
        $clear_command_data = ", command_data=''";
    }
    
    // Immediately update the command as being processed
    $sql = "UPDATE ".$db_tables[DB_NAGIOSXI]["commands"]." SET status_code='".escape_sql_param(COMMAND_STATUS_PROCESSING, DB_NAGIOSXI)."', processing_time=NOW()" . $clear_command_data . " WHERE command_id='".escape_sql_param($command_id, DB_NAGIOSXI)."'";
    exec_sql_query(DB_NAGIOSXI, $sql);

    // Process the command
    $result_code = process_command($command, $command_data, $result, $username);

    // Mark the command as being completed
    $sql = "UPDATE ".$db_tables[DB_NAGIOSXI]["commands"]." SET status_code='".escape_sql_param(COMMAND_STATUS_COMPLETED,DB_NAGIOSXI)."', result_code='".escape_sql_param($result_code, DB_NAGIOSXI)."', result='".escape_sql_param($result, DB_NAGIOSXI)."', processing_time=NOW() WHERE command_id='".escape_sql_param($command_id, DB_NAGIOSXI)."'";
    exec_sql_query(DB_NAGIOSXI, $sql);
}
    

function process_command($command, $command_data, &$output, $username)
{
    global $cfg;
    global $logging;
    
    // Don't reveal password data for certain commands
    if ($logging && ($command != 1100 && $command != 2881)) {
        echo "PROCESS COMMAND: CMD=$command, DATA=$command_data\n";
    }
    
    $output = "";
    $return_code = 0;
    
    // Get the base dir for scripts
    $base_dir = $cfg['root_dir'];
    $script_dir = $cfg['script_dir'];
    
    // Default to no command data
    $cmdline = "";
    $script_name = "";
    $script_data = "";
    
    // Post-command function call
    $post_func = "";
    $post_func_args = array();
    
    switch ($command) {
    
        case COMMAND_NAGIOSCORE_SUBMITCOMMAND:
            echo "COMMAND DATA: $command_data\n";
        
            // Command data is serialized so decode it...
            $cmdarr = json_decode($command_data, true);
            
            if ($logging) {
                echo "CMDARR:\n";
                print_r($cmdarr);
            }

              if(array_key_exists("multi_cmd", $cmdarr)){
                $err = 0;
                foreach($cmdarr['multi_cmd'] as $cmdarray){
                    $cmdarray_multi = (array) $cmdarray;
                    if ($logging) {
                        echo "CMDARR:\n";
                        print_r($cmdarray_multi);
                    }
                    if (array_key_exists("cmd", $cmdarray_multi)) {
                        $corecmdid = strval($cmdarray_multi["cmd"]);
                    } else {
                        return COMMAND_RESULT_ERROR;
                    }
                    
                    $nagioscorecmd = core_get_raw_command($corecmdid, $cmdarray_multi);
                    send_to_audit_log("Submitted a command to Nagios Core: ".$nagioscorecmd, AUDITLOGTYPE_INFO, '', $username);
                    echo "CORE CMD: $nagioscorecmd\n";
                
                    // SECURITY CONSIDERATION:
                    // We write directly to the Nagios command file to avoid shell interpretation of meta characters
                    if ($logging) {
                        echo "SUBMITTING A NAGIOSCORE COMMAND...\n";
                    }
                    if (($result = core_submit_command($nagioscorecmd, $output)) == false)
                        $err++;

                }
                if ($err > 0) {
                    return COMMAND_RESULT_ERROR;
                } else {
                    return COMMAND_RESULT_OK;
                }
              } else{
               if (array_key_exists("cmd", $cmdarr)) {
                    $corecmdid = strval($cmdarr["cmd"]);
                } else {
                    return COMMAND_RESULT_ERROR;
                }
                
                $nagioscorecmd = core_get_raw_command($corecmdid, $cmdarr);
                send_to_audit_log("Submitted a command to Nagios Core: ".$nagioscorecmd, AUDITLOGTYPE_INFO, '', $username);
                echo "CORE CMD: $nagioscorecmd\n";
            
                // SECURITY CONSIDERATION:
                // We write directly to the Nagios command file to avoid shell interpretation of meta characters
                if ($logging) {
                    echo "SUBMITTING A NAGIOSCORE COMMAND...\n";
                }
                if (($result = core_submit_command($nagioscorecmd, $output)) == false) {
                    return COMMAND_RESULT_ERROR;
                } else {
                    return COMMAND_RESULT_OK;
                }
              }
            break;

        case COMMAND_NAGIOSCORE_APPLYCONFIG:
            $script_name = "reconfigure_nagios.sh";
            echo "APPLYING NAGIOSCORE CONFIG...\n";

            // Do callback functions
            $args = array(); 
            do_callbacks(CALLBACK_SUBSYS_APPLYCONFIG, $args);
            break;
            
        case COMMAND_NAGIOSCORE_RECONFIGURE:
            $script_name = "reconfigure_nagios.sh";
            echo "RECONFIGURING NAGIOSCORE ...\n";
            send_to_audit_log("Reconfigured Nagios Core", AUDITLOGTYPE_INFO, '', $username);

            // Do callback functions
            $args = array(); 
            do_callbacks(CALLBACK_SUBSYS_APPLYCONFIG, $args);
            break;
            
        // CCM BACKEND COMMANDS
        case COMMAND_NAGIOSCORE_IMPORTONLY:
            $script_name = "ccm_import.php";
            echo "IMPORTING CONFIGURATION FILES ...\n";
            send_to_audit_log("Applied a new configuration to CCM without Applying configuration", AUDITLOGTYPE_INFO, '', $username);
            break;
        case COMMAND_NAGIOSQL_DELETECONTACT:
            $script_name = "ccm_delete_object.php";
            $script_data = "-t contact -i ".escapeshellarg($command_data);
            echo "DELETING CONTACT ...\n";
            send_to_audit_log("Deleted contact '".$command_data."'", AUDITLOGTYPE_DELETE, '', $username);
            break;
        case COMMAND_NAGIOSQL_DELETETIMEPERIOD:
            $script_name = "ccm_delete_object.php";
            $script_data = "-t timeperiod -i ".escapeshellarg($command_data);
            echo "DELETING TIMEPERIOD ...\n";
            send_to_audit_log("Deleted timeperiod '".$command_data."'", AUDITLOGTYPE_DELETE, '', $username);
            break;
        case COMMAND_NAGIOSQL_DELETESERVICE:
            $script_name = "ccm_delete_object.php";
            $script_data = "-t service -i ".escapeshellarg($command_data);
            echo "DELETING SERVICE ...\n";
            send_to_audit_log("Deleted service '".$command_data."'", AUDITLOGTYPE_DELETE, '', $username);
            break;
        case COMMAND_NAGIOSQL_DELETEHOST:
            $script_name = "ccm_delete_object.php";
            $script_data = "-t host -i ".escapeshellarg($command_data);
            echo "DELETING HOST ...\n";
            send_to_audit_log("Deleted host '".$command_data."'", AUDITLOGTYPE_DELETE, '', $username);
            break;
            
            
        // DAEMON COMMANDS
        // NAGIOS CORE
        case COMMAND_NAGIOSCORE_GETSTATUS:
            $cmdline = "sudo ".$script_dir."/manage_services.sh status nagios";
            break;
        case COMMAND_NAGIOSCORE_START:
            $cmdline = "sudo ".$script_dir."/manage_services.sh start nagios";
            send_to_audit_log("cmdsubsys: User started Nagios Core", AUDITLOGTYPE_INFO, '', $username);
            break;
        case COMMAND_NAGIOSCORE_STOP:
            $cmdline = "sudo ".$script_dir."/manage_services.sh stop nagios";
            send_to_audit_log("cmdsubsys: User stopped Nagios Core", AUDITLOGTYPE_INFO, '', $username);
            break;
        case COMMAND_NAGIOSCORE_RESTART:
            $cmdline = "sudo ".$script_dir."/manage_services.sh restart nagios";
            send_to_audit_log("cmdsubsys: User restarted Nagios Core", AUDITLOGTYPE_INFO, '', $username);
            break;
        case COMMAND_NAGIOSCORE_RELOAD:
            $cmdline = "sudo ".$script_dir."/manage_services.sh reload nagios";
            send_to_audit_log("cmdsubsys: User reloaded Nagios Core configuration", AUDITLOGTYPE_INFO, '', $username);
            break;
        case COMMAND_NAGIOSCORE_CHECKCONFIG:
            $cmdline = "sudo ".$script_dir."/manage_services.sh checkconfig nagios";
            break;

        // NPCD
        case COMMAND_NPCD_GETSTATUS:
            $cmdline = "sudo ".$script_dir."/manage_services.sh status npcd";
            break;
        case COMMAND_NPCD_START:
            $cmdline = "sudo ".$script_dir."/manage_services.sh start npcd";
            send_to_audit_log("cmdsubsys: User started NPCD", AUDITLOGTYPE_INFO, '', $username);
            break;
        case COMMAND_NPCD_STOP:
            $cmdline = "sudo ".$script_dir."/manage_services.sh stop npcd";
            send_to_audit_log("cmdsubsys: User stopped NPCD", AUDITLOGTYPE_INFO, '', $username);
            break;
        case COMMAND_NPCD_RESTART:
            $cmdline = "sudo ".$script_dir."/manage_services.sh restart npcd";
            send_to_audit_log("cmdsubsys: User restarted NPCD", AUDITLOGTYPE_INFO, '', $username);
            break;
        case COMMAND_NPCD_RELOAD:
            $cmdline = "sudo ".$script_dir."/manage_services.sh reload npcd";
            send_to_audit_log("cmdsubsys: User reloaded NPCD configuration", AUDITLOGTYPE_INFO, '', $username);
            break;
            
        // Apache
        case COMMAND_RESTART_HTTP:
            $cmdline = "sudo ".$script_dir."/manage_services.sh restart httpd";
            send_to_audit_log("cmdsubsys: User restarted HTTPD", AUDITLOGTYPE_INFO, '', $username);
            break;

        // PHP-FPM
        case COMMAND_RESTART_PHP_FPM:
            $cmdline = "sudo ".$script_dir."/manage_services.sh restart php-fpm";
            send_to_audit_log("cmdsubsys: User restarted PHP-FPM", AUDITLOGTYPE_INFO, '', $username);
            break;

		case COMMAND_NAGIOSXI_SET_HTACCESS:
			$cmdarr = unserialize($command_data);	
			$cmdline = $cfg['htpasswd_path']." -b -s ".$cfg['htaccess_file']." ".escapeshellarg($cmdarr["username"])." ".escapeshellarg($cmdarr["password"]);
			break;

        case COMMAND_NAGIOSXI_DEL_HTACCESS:
            $cmdarr = unserialize($command_data);   
            $cmdline = $cfg['htpasswd_path']." -D ".$cfg['htaccess_file']." ".escapeshellarg($cmdarr["username"]);
            break;

        case COMMAND_DELETE_CONFIGWIZARD:
            $dir = cmdsubsys_clean_str($command_data);

            if (empty($dir)) {
                return COMMAND_RESULT_ERROR;
            }
            
            $cmdline = escapeshellcmd("rm -rf ".get_base_dir()."/includes/configwizards/".$dir);
            break;

        case COMMAND_INSTALL_CONFIGWIZARD:
            if ($logging) {
                echo "INSTALLING CONFIGWIZARD...\n";
                echo "RAW COMMAND DATA: $command_data\n";
            }

            $file = cmdsubsys_clean_str($command_data);
            
            if ($logging) {
                echo "CONFIGWIZARD FILE: '".$file."'\n";
            }

            if (empty($file)) {
                echo "FILE ERROR!\n";
                return COMMAND_RESULT_ERROR;
            }
            
            // Create a new temp directory for holding the unzipped wizard
            $tmpname = random_string(5);
            if ($logging) {
                echo "TMPNAME: $tmpname\n";
            }
            $tmpdir = get_tmp_dir()."/".$tmpname;
            system("rm -rf ".$tmpdir);
            mkdir($tmpdir);
            
            // Unzip wizard to temp directory
            $cmdline = "cd ".$tmpdir." && ".escapeshellcmd("unzip -o ".get_tmp_dir()."/configwizard-".$file);
            system($cmdline);
            
            // Determine wizard directory/file name
            $cdir = system("ls -1 ".$tmpdir."/");
            $cname = $cdir;
            if (preg_match("/(.*)(-[0-9a-f]{40})(.*)/", $cname, $hash_matches) == 1) {
                $cname = $hash_matches[1] . $hash_matches[3];
            }
            if (preg_match("/(.*)(-master|-development)(.*)/", $cname, $branch_matches) == 1) {
                $cname = $branch_matches[1] . $branch_matches[3];
            }
            
            // Make sure this is a config wizard
            $cmdline = "grep register_configwizard ".escapeshellarg($tmpdir."/".$cdir."/".$cname.".inc.php")." | wc -l";
            if ($logging) {
                echo "CMD=$cmdline";
            }

            $out = system($cmdline, $rc);
            
            if ($logging) {
                echo "OUT=$out";
            }

            // Delete temp directory if it's not a config wizard
            if ($out == "0") {
                system("rm -rf ".$tmpdir);
                $output = "Uploaded zip file is not a config wizard.";
                echo $output."\n";
                return COMMAND_RESULT_ERROR;
            }
            
            if ($logging) { 
                echo "Wizard looks ok...";
            }
            
            // Null-op
            $cmdline = "/bin/true";
            
            // Make new wizard directory (might exist already)
            @mkdir(get_base_dir()."/includes/configwizards/".$cname);
            
            // Move wizard to production directory and delete temp directory
            $cmdline = ". ".get_root_dir()."/var/xi-sys.cfg && chmod -R 755 ".$tmpdir." && chown -R \$nagiosuser:\$nagiosgroup ".$tmpdir." && cp -rf ".$tmpdir."/".$cdir."/* ".get_base_dir()."/includes/configwizards/".$cname." && rm -rf ".$tmpdir;

            $post_func = "install_configwizard";
            $post_func_args = array(
                "wizard_name" => $cname,
                "wizard_dir" => get_base_dir()."/includes/configwizards/".$cname
            );
            break;

        case COMMAND_UPGRADE_CONFIGWIZARD:
            if (!empty($command_data)) {
                $command_data = unserialize($command_data);
            }

            if ($logging) {
                echo "AUTO UPGRADING CONFIG WIZARDS";
                echo "CMD DATA: ";
                if (!empty($command_data)) {
                    print_r($command_data);
                }
            }
            
            $cmdline = "/bin/true";
            
            $proxy = false;
            if (have_value(get_option('use_proxy'))) {
                $proxy = true;
            }

            $options = array(
                'return_info' => true,
                'method' => 'get',
                'timeout' => 60,
                'debug' => true
            );

            // If command data is empty, we need to upgrade ALL the config wizards
            // Grab a list of all the available config wizards and get a list of what needs to be upgraded
            if (empty($command_data)) {
                $needs_upgrade = get_all_configwizards_needing_upgrade();
                if (!empty($needs_upgrade)) {

                    foreach ($needs_upgrade as $w) {
                        
                        $url = $w['download'];
                        $name = $w['wizard_dir'];
                        
                        // Start installation process
                        $tmpdir = get_tmp_dir().'/' . $name;
                        
                        // Fetch the url
                        $result = load_url($url, $options, $proxy);
                        if (empty($result["body"])){
                            $cmdline = "/bin/false";
                            break 2;
                        }
                        // Download the file to temp directory
                        file_put_contents(get_tmp_dir() ."/".$name.".zip", $result["body"]);
                        $cmd = 'cd ' . get_tmp_dir() . '; mv ' . $name . '.zip configwizard-' . $name .'.zip;';
                        system($cmd);

                        // Unzip the file and 
                        $cmd = 'cd ' . get_tmp_dir() . ' && unzip -o configwizard-' . $name . '.zip;';
                        system($cmd);

                        $cmd = ". ".get_root_dir()."/var/xi-sys.cfg && chmod -R 755 ".$tmpdir." && chown -R \$nagiosuser:\$nagiosgroup ".$tmpdir." && cp -rf ".$tmpdir."/".$cdir." ".get_base_dir()."/includes/configwizards/ && rm -rf ".$tmpdir;
                        system($cmd);

                        $args = array("wizard_name" => $name, "wizard_dir" => get_base_dir().'/includes/configwizards/'.$name, "allow_restart" => false);
                        install_configwizard($args);

                    }

                    // Restart core once all wizards are updated
                    reconfigure_nagioscore();
                }
            } else {

                // Grab the command data
                $name = $command_data['name'];
                $url = $command_data['url'];

                // Start installation process
                $tmpdir = escapeshellcmd(get_tmp_dir().'/' . $name);

                // Fetch the url
                $result = load_url($url, $options, $proxy);
                
                if (empty($result["body"])){
                    $cmdline = "/bin/false";
                    break;
                }
                // Download the file to temp directory
                file_put_contents(get_tmp_dir() ."/".$name.".zip", $result["body"]);
                $cmd = 'cd ' . get_tmp_dir() . '; '.escapeshellcmd('mv ' . $name . '.zip configwizard-' . $name .'.zip').';';
                system($cmd);

                // Unzip the file and 
                $cmd = 'cd ' . get_tmp_dir() . ' && '.escapeshellcmd('unzip -o configwizard-' . $name . '.zip').';';
                system($cmd);

                $cmd = ". ".get_root_dir()."/var/xi-sys.cfg && chmod -R 755 ".$tmpdir." && chown -R \$nagiosuser:\$nagiosgroup ".$tmpdir." && cp -rf ".$tmpdir."/".$cdir." ".get_base_dir()."/includes/configwizards/ && rm -rf ".$tmpdir;
                system($cmd);

                $args = array("wizard_name" => $name, "wizard_dir" => get_base_dir().'/includes/configwizards/'.$name);
                install_configwizard($args);
            }

            break;

        case COMMAND_PACKAGE_CONFIGWIZARD:
            $dir = cmdsubsys_clean_str($command_data);
            
            if (empty($dir)) {
                return COMMAND_RESULT_ERROR;
            }

            // Check if the file exists
            $folder = get_base_dir()."/includes/configwizards/".$dir;
            if (!file_exists($folder)) {
                return COMMAND_RESULT_ERROR;
            }

            $cmdline = "cd ".get_base_dir()."/includes/configwizards && ".escapeshellcmd("zip -r ".get_tmp_dir()."/configwizard-".$dir.".zip ".$dir);
            break;

        case COMMAND_DELETE_DASHLET:
            $dir = cmdsubsys_clean_str($command_data);
            
            if (empty($dir)) {
                return COMMAND_RESULT_ERROR;
            }

            $cmdline = escapeshellcmd("rm -rf ".get_base_dir()."/includes/dashlets/".$dir);
            break;

        case COMMAND_INSTALL_DASHLET:
            $file = cmdsubsys_clean_str($command_data);
            
            if (empty($file)) {
                return COMMAND_RESULT_ERROR;
            }

            // Create a new temp directory for holding the unzipped dashlet
            $tmpname = random_string(5);
            if ($logging) {
                echo "TMPNAME: $tmpname\n";
            }
            $tmpdir = get_tmp_dir()."/".$tmpname;
            system("rm -rf ".$tmpdir);
            mkdir($tmpdir);
            
            // Unzip dashlet to temp directory
            $cmdline = "cd ".$tmpdir." && ".escapeshellcmd("unzip -o ".get_tmp_dir()."/dashlet-".$file);
            system($cmdline);
            
            // Determine dashlet directory/file name
            $cdir = system("ls -1 ".$tmpdir."/");
            $cname = $cdir;
            if (preg_match("/(.*)(-[0-9a-f]{40})(.*)/", $cname, $hash_matches) == 1) {
                $cname = $hash_matches[1] . $hash_matches[3];
            }
            if (preg_match("/(.*)(-master|-development)(.*)/", $cname, $branch_matches) == 1) {
                $cname = $branch_matches[1] . $branch_matches[3];
            }
            
            // Make sure this is a dashlet
            $isdashlet = true;

            // Check for register_dashlet...
            $cmdline = "grep register_dashlet ".escapeshellarg($tmpdir."/".$cdir."/".$cname.".inc.php")." | wc -l";
            if ($logging) {
                echo "CMD=$cmdline";
            }
            
            $out = system($cmdline, $rc);
            
            if ($logging) {
                echo "OUT=$out";
            }       
            
            // Verify it was a dashlet...
            if ($out == "0") {
                $isdashlet = false;
            }
            
            // Check to make sure its not a component...
            $cmdline = "grep register_component ".escapeshellarg($tmpdir."/".$cdir."/".$cname.".inc.php")." | wc -l";
            if ($logging) {
                echo "CMD=$cmdline";
            }
            
            $out = system($cmdline, $rc);
            
            if ($logging) {
                echo "OUT=$out";
            }
            
            // Verify that it is not a component
            if ($out != "0") {
                $isdashlet = false;
            }

            // Delete template if it isn't a dashlet
            if ($isdashlet == false) {
                system("rm -rf ".$tmpdir);
                $output = "Uploaded zip file is not a dashlet.";
                echo $output."\n";
                return COMMAND_RESULT_ERROR;
            }
            
            if ($logging) {
                echo "Dashlet looks ok...";
            }
            
            // Make new dashlet directory (might exist already)
            @mkdir(get_base_dir()."/includes/dashlets/".$cname);
            
            // Move dashlet to production directory and delete temp directory
            $cmdline = ". ".get_root_dir()."/var/xi-sys.cfg && chmod -R 755 ".$tmpdir." && chown -R \$nagiosuser:\$nagiosgroup ".$tmpdir." && cp -rf ".$tmpdir."/".$cdir."/* ".get_base_dir()."/includes/dashlets/".$cname." && rm -rf ".$tmpdir;
            break;

        case COMMAND_PACKAGE_DASHLET:
            $dir = cmdsubsys_clean_str($command_data);

            if (empty($dir)) {
                return COMMAND_RESULT_ERROR;
            }

            // Check if the file exists
            $folder = get_base_dir()."/includes/dashlets/".$dir;
            if (!file_exists($folder)) {
                return COMMAND_RESULT_ERROR;
            }
            
            $cmdline = "cd ".get_base_dir()."/includes/dashlets && ".escapeshellcmd("zip -r ".get_tmp_dir()."/dashlet-".$dir.".zip ".$dir);
            break;

        case COMMAND_DELETE_COMPONENT:
            $dir = cmdsubsys_clean_str($command_data);
            
            if (empty($dir)) {
                return COMMAND_RESULT_ERROR;
            }
            
            $cmdline = escapeshellcmd("rm -rf ".get_base_dir()."/includes/components/".$dir);
            break;

        case COMMAND_INSTALL_COMPONENT:
            $file = cmdsubsys_clean_str($command_data);

            if (empty($file)) {
                return COMMAND_RESULT_ERROR;
            }

            // Create a new temp directory for holding the unzipped component
            $tmpname = random_string(5);
            if ($logging) {
                echo "TMPNAME: $tmpname\n";
            }
            $tmpdir = get_tmp_dir()."/".$tmpname;
            system("rm -rf ".$tmpdir);
            mkdir($tmpdir);
            
            // Unzip component to temp directory
            $cmdline = "cd ".$tmpdir." && ".escapeshellcmd("unzip -o ".get_tmp_dir()."/component-".$file);
            system($cmdline);
            
            // Determine component directory/file name
            $cdir = system("ls -1 ".$tmpdir."/");
            $cname = $cdir;
            if (preg_match("/(.*)(-[0-9a-f]{40})(.*)/", $cname, $hash_matches) == 1) {
                $cname = $hash_matches[1] . $hash_matches[3];
            }
            if (preg_match("/(.*)(-master|-development)(.*)/", $cname, $branch_matches) == 1) {
                $cname = $branch_matches[1] . $branch_matches[3];
            }

            // Make an exception to the grep command for the following components and deny the others from being uploaded
            $except = array('capacityplanning', 'bulkmodifications', 'autodiscovery', 'ldap_ad_integration', 'deploynotification', 'nagiosbpi', 'scheduledbackups', 'scheduledreporting');
            $deny = array('profile');
            if (!in_array($cname, $except)) {
                if (!in_array($cname, $deny)) {

                    // Make sure this is a component
                    $cmdline = "grep register_component ".escapeshellarg($tmpdir."/".$cdir."/".$cname.".inc.php")." | wc -l";

                    if ($logging) {
                        echo "CMD=$cmdline";
                    }

                    $out = system($cmdline, $rc);
                    
                    if ($logging) {
                        echo "OUT=$out";
                    }

                    // Delete temp directory if it's not a component
                    if ($out == "0") {
                        system("rm -rf ".$tmpdir);
                        $output = "Uploaded zip file is not a component.";
                        echo $output."\n";
                        return COMMAND_RESULT_ERROR;
                    }

                } else {
                    system("rm -rf ".$tmpdir);
                    $output = "Uploaded component cannot be updated through the UI.";
                    echo $output."\n";
                    return COMMAND_RESULT_ERROR;
                }
            }

            if ($logging) {
                echo "Component looks ok...";
            }
            
            // Null-op
            $cmdline = "/bin/true";
            
            // Make new component directory (might exist already)
            @mkdir(get_base_dir()."/includes/components/".$cname);
            
            // Move component to production directory and delete temp directory
            // and added permissions fix to make sure all new components are executable
            $cmdline = ". ".get_root_dir()."/var/xi-sys.cfg && chmod -R 755 ".$tmpdir." && chown -R \$nagiosuser:\$nagiosgroup ".$tmpdir." && cp -rf ".$tmpdir."/".$cdir."/* ".get_base_dir()."/includes/components/".$cname." && rm -rf ".$tmpdir;

            $component_name = $cname;
            $post_func = "install_component";
            $post_func_args = array(
                "component_name" => $component_name,
                "component_dir" => get_base_dir()."/includes/components/".$component_name,
            );
            break;

        case COMMAND_UPGRADE_COMPONENT:

            if (!empty($command_data)) {
                $command_data = unserialize($command_data);
            }

            if ($logging) {
                echo "AUTO UPGRADING COMPONENTS";
                echo "CMD DATA: ";
                if (!empty($command_data)) {
                    print_r($command_data);
                }
            }

            $cmdline = "/bin/true";

            $proxy = false;
            if (have_value(get_option('use_proxy'))) {
                $proxy = true;
            }

            $options = array(
                'return_info' => true,
                'method' => 'get',
                'timeout' => 60,
                'debug' => true
            );
            
            // If command data is empty, we need to upgrade ALL the config wizards
            // Grab a list of all the available config wizards and get a list of what needs to be upgraded
            if (count($command_data) > 1) {
                
                foreach ($command_data as $c) {

                    $name = $c['name'];
                    $url = $c['url'];

                    // Create a new temp directory for holding the unzipped component
                    $tmpname = random_string(5);
                    if ($logging) {
                        echo "TMPNAME: $tmpname\n";
                    }
                    $tmpdir = get_tmp_dir()."/".$tmpname;
                    system("rm -rf ".$tmpdir);
                    mkdir($tmpdir);
                    
                    // Fetch the url
                    $result = load_url($url, $options, $proxy);
                    if (empty($result["body"])) {
                        $cmdline = "/bin/false";
                        break 2;
                    }
                    
                    file_put_contents($tmpdir."/".$name.".zip", $result["body"]);
                    // Download and unzip component
                    $cmd = "cd ".$tmpdir."; unzip -o  ".$name.".zip;";
                    system($cmd);

                    @mkdir(get_base_dir()."/includes/components/".$name);

                    // Move component to production directory and delete temp directory
                    // and added permissions fix to make sure all new components are executable
                    $cmd = ". ".get_root_dir()."/var/xi-sys.cfg && chmod -R 755 ".$tmpdir."/".$name." && chown -R \$nagiosuser:\$nagiosgroup ".$tmpdir."/".$name." && cp -rf ".$tmpdir."/".$name." ".get_base_dir()."/includes/components/ && rm -rf ".$tmpdir;
                    system($cmd);

                    install_component(array("component_name" => $name, "component_dir" => get_base_dir()."/includes/components/".$name));
                }

            } else {

                // Only upgrade a single component
                $name = $command_data[0]['name'];
                $url = $command_data[0]['url'];

                // Create a new temp directory for holding the unzipped component
                $tmpname = random_string(5);
                if ($logging) {
                    echo "TMPNAME: $tmpname\n";
                }
                $tmpdir = get_tmp_dir()."/".$tmpname;
                system("rm -rf ".$tmpdir);
                mkdir($tmpdir);

                // Fetch the url
                $result = load_url($url, $options, $proxy);
                if (empty($result["body"])){
                    $cmdline = "/bin/false";
                    break;
                }
                file_put_contents($tmpdir."/".$name.".zip", $result["body"]);
                // Download and unzip component
                $cmdline = "cd ".$tmpdir."; ".escapeshellcmd("unzip -o ".$name.".zip").";";
                system($cmdline);

                @mkdir(get_base_dir()."/includes/components/".$name);

                // Move component to production directory and delete temp directory
                // and added permissions fix to make sure all new components are executable
                $cmdline = ". ".get_root_dir()."/var/xi-sys.cfg && chmod -R 755 ".escapeshellarg($tmpdir."/".$name)." && chown -R \$nagiosuser:\$nagiosgroup ".escapeshellarg($tmpdir."/".$name)." && cp -rf ".escapeshellarg($tmpdir."/".$name)." ".get_base_dir()."/includes/components/ && rm -rf ".$tmpdir;

                $post_func = "install_component";
                $post_func_args = array(
                    "component_name" => $name,
                    "component_dir" => get_base_dir()."/includes/components/".$name
                );
            }

            break;

        case COMMAND_PACKAGE_COMPONENT:
            $dir = cmdsubsys_clean_str($command_data);

            if (empty($dir)) {
                return COMMAND_RESULT_ERROR;
            }

            // Check if the file exists
            $folder = get_base_dir()."/includes/components/".$dir;
            if (!file_exists($folder)) {
                return COMMAND_RESULT_ERROR;
            }
            
            $cmdline = "cd ".get_base_dir()."/includes/components && ".escapeshellcmd("zip -r ".get_tmp_dir()."/component-".$dir.".zip ".$dir);
            break;

        case COMMAND_DELETE_CONFIGSNAPSHOT:
            $ts = cmdsubsys_clean_str($command_data);
            
            if (empty($ts)) {
                return COMMAND_RESULT_ERROR;
            }

            $cmdline = escapeshellcmd("rm -rf ".$cfg['nom_checkpoints_dir']."errors/".$ts.".tar.gz");
            break;

        case COMMAND_RESTORE_CONFIGSNAPSHOT:
            $cmdline = get_root_dir()."/scripts/nom_restore_nagioscore_checkpoint_specific.sh ".escapeshellcmd($command_data);
            break;

        case COMMAND_RESTORE_NAGIOSQL_SNAPSHOT:
            $cmdline = get_root_dir()."/scripts/ccm_snapshot.sh ".escapeshellcmd($command_data);
            break;

        case COMMAND_ARCHIVE_SNAPSHOT:
            $ts = $command_data;
            $archive_dir = $cfg['nom_checkpoints_dir']."/archives";
            $ql_archive_dir = $cfg['nom_checkpoints_dir']."/../nagiosxi/archives";
            
            if (!is_dir($archive_dir)) {
                mkdir($archive_dir);
            }
            
            if (!is_dir($ql_archive_dir)) {
                mkdir($ql_archive_dir);
            }
                
            $cmdline = escapeshellcmd("cp ".$cfg['nom_checkpoints_dir']."/$ts.tar.gz ".$cfg['nom_checkpoints_dir']."/$ts.txt $archive_dir").";";
            $cmdline .= escapeshellcmd("cp ".$cfg['nom_checkpoints_dir']."/../nagiosxi/".$ts."_nagiosql.sql.gz $ql_archive_dir").";";
            $cmdline .= escapeshellcmd("cp -r ".$cfg['nom_checkpoints_dir']."/".$ts." $archive_dir");
            break;

        case COMMAND_DELETE_ARCHIVE_SNAPSHOT:
            $ts = cmdsubsys_clean_str($command_data);
            $archive_dir = $cfg['nom_checkpoints_dir']."/archives";
            $ql_archive_dir = $cfg['nom_checkpoints_dir']."/../nagiosxi/archives";

            if (empty($ts)) {
                return COMMAND_RESULT_ERROR;
            }

            $cmdline = "rm -rf ".$archive_dir."/".$ts.".tar.gz";
            $cmdline .= " ".$archive_dir."/".$ts.".txt";
            $cmdline .= " ".$archive_dir."/".$ts;
            $cmdline .= " ".$ql_archive_dir."/".$ts."_nagiosql.sql.gz";
            $cmdline = escapeshellcmd($cmdline);
            break;

        case COMMAND_RENAME_ARCHIVE_SNAPSHOT:
            $command_data = unserialize($command_data);
            $old_name = escapeshellarg($command_data[0]);
            $new_name = escapeshellarg($command_data[1]);
            $archive_dir = $cfg['nom_checkpoints_dir']."/archives";
            $ql_archive_dir = $cfg['nom_checkpoints_dir']."/../nagiosxi/archives";

            if (empty($old_name) || empty($new_name)) {
                return COMMAND_RESULT_ERROR;
            }

            $cmdline = "mv ".$archive_dir."/".$old_name.".tar.gz ".$archive_dir."/".$new_name.".tar.gz;";
            $cmdline .= "mv ".$archive_dir."/".$old_name.".txt ".$archive_dir."/".$new_name.".txt;";
            $cmdline .= "mv ".$archive_dir."/".$old_name." ".$archive_dir."/".$new_name.";";
            $cmdline .= "mv ".$ql_archive_dir."/".$old_name."_nagiosql.sql.gz ".$ql_archive_dir."/".$new_name."_nagiosql.sql.gz;";
            break;

        case COMMAND_CREATE_SYSTEM_BACKUP:
            $data = unserialize($command_data);
            
            // If there is a name set
            if (!empty($data[0])) {
                $name = " -n " . escapeshellarg($data[0]);
            } else {
                $name = "";
            }

            // If there is a directory set
            if (!empty($data[1])) {
                $d = rtrim($data[1], "/");
                $dir = " -d " . escapeshellarg($d);
            } else {
                $dir = "";
            }
            
            $cmdline = 'sudo ' . get_root_dir()."/scripts/backup_xi.sh" . $name . $dir . " 2>&1 | tee " . get_root_dir().'/var/components/scheduledbackups.log';
            break;

        case COMMAND_DELETE_SYSTEM_BACKUP:
            $data = unserialize($command_data);

            // If there is a name set
            if (!empty($data[0])) {
                $name = escapeshellarg($data[0]);
            } else {
                $name = "";
            }

            // If there is a directory set
            if (!empty($data[1])) {
                $d = rtrim($data[1], "/");
                $dir = escapeshellarg($d);
            } else {
                $dir = "/store/backups/nagiosxi";
            }

            if (empty($name)) {
                return COMMAND_RESULT_ERROR;
            }

            $cmdline = "rm -rf " . $dir . "/" . $name . ".tar.gz";
            break;

        case COMMAND_RENAME_SYSTEM_BACKUP:
            $data = unserialize($command_data);
            $old_name = escapeshellarg($data[0]);
            $new_name = escapeshellarg($data[1]);

            if (empty($old_name) || empty($new_name)) {
                return COMMAND_RESULT_ERROR;
            }

            $cmdline = "mv /store/backups/nagiosxi/" . $old_name . ".tar.gz /store/backups/nagiosxi/" . $new_name . ".tar.gz";
            break;
        
        case COMMAND_UPDATE_XI_TO_LATEST:
            $data = unserialize($command_data);
            $file = $data[0];
            
            $tmpdir = get_tmp_dir();
            
            $proxy = false;
            if (have_value(get_option('use_proxy'))) {
                $proxy = true;
            }

            $options = array(
                'return_info' => true,
                'method' => 'get',
                'timeout' => 300,
                'debug' => true
            );
            
            // Fetch the url
            $result = load_url($file, $options, $proxy);
            if (empty($result["body"])) {
                return COMMAND_RESULT_ERROR;
            }
            
            if (file_exists($tmpdir."/xi-latest.tar.gz")) {
                unlink($tmpdir."/xi-latest.tar.gz");
            }

            file_put_contents($tmpdir."/xi-latest.tar.gz", $result["body"]);
            file_put_contents($tmpdir."/upgrade.log", "STARTING XI UPGRADE\n");
                    
            $cmdline = "sudo ".get_root_dir()."/scripts/upgrade_to_latest.sh";
            break;
        
        case COMMAND_CHANGE_TIMEZONE:
            $timezone = $command_data;
            $cmdline = "sudo ".get_root_dir()."/scripts/change_timezone.sh -z ".escapeshellarg($timezone);
            break;

        case COMMAND_RUN_CHECK_CMD:
            if (!empty($command_data)) {
                // Fix escaping for quoted sections
                $escaped_cmd = escapeshellcmd($command_data);

                // Build array of quoted parts, and the same escaped
                preg_match_all('/\'[^\']+\'/', $command_data, $matches);
                $matches = current($matches);
                $quoted = array();
                foreach ($matches as $match) {
                    $quoted[escapeshellcmd($match)] = $match;
                }

                // Replace sections that were single quoted with original content
                foreach ($quoted as $search => $replace) {
                    $escaped_cmd = str_replace($search, $replace, $escaped_cmd);
                }

                $cmdline = $escaped_cmd;
            } else {
                return COMMAND_RESULT_ERROR;
            }
            break;

        case COMMAND_RESTART_SNMPTT:
            $cmdline = "sudo ".$script_dir."/manage_services.sh restart snmptt";
            send_to_audit_log("cmdsubsys: User restarted snmptt", AUDITLOGTYPE_INFO, '', $username);
            break;

        case COMMAND_STOP_SHELLINABOX:
            // Stop shell in a box
            $cmdline = "sudo " . $script_dir . "/manage_services.sh stop shellinaboxd";
            send_to_audit_log("cmdsubsys: User stopped shell in a box", AUDITLOGTYPE_INFO, '', $username);
            break;

        case COMMAND_START_SHELLINABOX:
            // Start shell in a box
            $cmdline = "sudo " . $script_dir . "/manage_services.sh start shellinaboxd";
            send_to_audit_log("cmdsubsys: User started shell in a box", AUDITLOGTYPE_INFO, '', $username);
            break;

        case COMMAND_DISABLE_SHELLINABOX:
            // Disable shell in a box
            $cmdline = "sudo " . $script_dir . "/manage_services.sh disable shellinaboxd";
            send_to_audit_log("cmdsubsys: User disabled shell in a box", AUDITLOGTYPE_INFO, '', $username);
            break;

        case COMMAND_ENABLE_SHELLINABOX:
            // Enable shell in a box
            $cmdline = "sudo " . $script_dir . "/manage_services.sh enable shellinaboxd";
            send_to_audit_log("cmdsubsys: User enabled shell in a box", AUDITLOGTYPE_INFO, '', $username);
            break;

        case COMMAND_SEND_TO_LOGSERVER:
            $cmdarr = unserialize($command_data);
            $hostname = escapeshellarg($cmdarr['hostname']);
            $port = escapeshellarg($cmdarr['port']);
            $tag = escapeshellarg($cmdarr['tag']);
            $file = escapeshellarg($cmdarr['file']);

            $cmdline = "sudo /usr/bin/php $script_dir/send_to_nls.php $hostname $port $tag $file";
            break;

        case COMMAND_STOP_SENDING_TO_LOGSERVER:
            $cmdarr = unserialize($command_data);
            $tag = escapeshellarg($cmdarr['tag']);
            $cmdline = "sudo /usr/bin/php $script_dir/send_to_nls.php $tag";
            break;

        # --------------------------
        # APACHE CONFIG COMMANDS
        # --------------------------

        case COMMAND_ENABLE_HTTP_REDIRECT:
            $cmdline = "sudo ".$script_dir."/manage_ssl_config.sh --redirect-http";
            break;

        case COMMAND_DISABLE_HTTP_REDIRECT:
            $cmdline = "sudo ".$script_dir."/manage_ssl_config.sh --disable-redirect-http";
            break;

        case COMMAND_UPDATE_SSL_CONFIG:
            if (empty($command_data)) {
                return COMMAND_RESULT_ERROR;
            }
            $data = unserialize($command_data);
            $key = $data[0];
            $crt = $data[1];
            $cmdline = "sudo ".$script_dir."/manage_ssl_config.sh -k " . escapeshellarg($key) . " -c " . escapeshellarg($crt);
            break;

        # --------------------------
        # DEPLOYMENT COMMANDS
        # --------------------------

        case COMMAND_START_DEPLOY:
            $job_id = intval($command_data);
            if (!empty($job_id)) {
                $cmdline = "php ".$script_dir."/deploy_run_job.php -j " . escapeshellarg($job_id);
            } else {
                echo "NO VALID DEPLOY JOB ID IN DEPLOY JOB COMMAND\n";
                return COMMAND_RESULT_ERROR;
            }
            break;

        case COMMAND_DELETE_DEPLOY:
            $job_id = intval($command_data);
            if (!empty($job_id)) {

                // Include utils
                require_once(dirname(__FILE__).'/../html/config/deployment/includes/utils-deployment.inc.php');

                deploy_delete_job($job_id);

                $cmdline = "/bin/true";

            } else {
                echo "NO VALID DEPLOY JOB ID IN DEPLOY JOB COMMAND\n";
                return COMMAND_RESULT_ERROR;
            }
            break;

        # --------------------------
        # BPI COMMANDS
        # --------------------------

        case COMMAND_BPI_SYNC:
            $extra = "";
            if ($command_data != "remove") {
                $extra = "--no-delete";
            }
            $cmdline = "php " . get_component_dir_base('nagiosbpi') . "/api_tool.php --cmd=syncall $extra";
            break;

        case COMMAND_BPI_REMOVE_MISSING:
            $cmdline = "php " . get_component_dir_base('nagiosbpi') . "/api_tool.php --cmd=removemissing";
            break;

        # --------------------------
        # MIGRATE COMMANDS
        # --------------------------

        case COMMAND_START_MIGRATE:
            $data = json_decode($command_data, true);
            if (isset($data['address']) && isset($data['username']) && isset($data['password'])) {
                $cmdline = $cmdline = "sudo /usr/bin/php ".$script_dir."/migrate/migrate.php -a ".escapeshellarg($data['address'])." -u ".escapeshellarg($data['username'])." -p ".escapeshellarg($data['password'])." -e";
                if (isset($data['overwrite']) && $data['overwrite'] == 1) { $cmdline .= ' -o'; }
                if (isset($data['clear']) && $data['clear'] == 1) { $cmdline .= ' -c'; }
                if (isset($data['nagios_cfg']) && !empty($data['nagios_cfg'])) { $cmdline .= ' -C '.escapeshellarg($data['nagios_cfg']); }
            } else {
                echo "DID NOT PASS VALID DATA FOR MIGRATE SCRIPT\n";
                return COMMAND_RESULT_ERROR;
            }
            break;

        # --------------------------
        # CCM COMMANDS
        # --------------------------

        case COMMAND_CCM_UPDATE_PERMS:
            ccm_update_permissions();
            $cmdline = "/bin/true";
            break;

        default:
            echo "INVALID COMMAND ($command)!\n";
            return COMMAND_RESULT_ERROR;
            break;
    }
    
    // We're running a script, so generate the command line to execute
    if ($script_name != "") {
        if ($script_data != "") {
            $cmdline = sprintf("cd %s && ./%s %s", $script_dir, $script_name, $script_data);
        } else {
            $cmdline = sprintf("cd %s && ./%s", $script_dir, $script_name);
        }
    }

    // Run the system command (and don't reveal credentials)
    if ($command == COMMAND_NAGIOSXI_SET_HTACCESS) {
        echo "Setting new htaccess credentials\n";
    } else {
        echo "CMDLINE=$cmdline\n";
    }

    $return_code = 127;
    $output = "";
    if ($cmdline != "") {
        
        // we need multi line output for the ccm run check
        if ($command == COMMAND_RUN_CHECK_CMD) {
            exec($cmdline, $output, $return_code);
            $output = implode("\n", $output);
        } else {
            $output = system($cmdline, $return_code);
        }
    }
    
    echo "OUTPUT=$output\n";
    echo "RETURNCODE=$return_code\n";
    
    // Run the post function call
    if ($return_code == 0 && $post_func != "" && function_exists($post_func)) {
        echo "RUNNING POST FUNCTION CALL: $post_func\n";
        $return_code = $post_func($post_func_args);
        echo "POST FUNCTION CALL RETURNCODE=$return_code\n";
    }

    // Do callbacks
    $args = array(
        'command' => $command,
        'command_data' => $command_data,
        'return_code' => $return_code
    );
    do_callbacks(CALLBACK_SUBSYS_GENERIC, $args);

    if ($return_code != 0) {
        return $return_code;
    }
    return COMMAND_RESULT_OK;
}

function cmdsubsys_clean_str($x)
{
    $x = str_replace("..", "", $x);
    $x = str_replace("/", "", $x);
    $x = str_replace("\\", "", $x);
    return $x;
}