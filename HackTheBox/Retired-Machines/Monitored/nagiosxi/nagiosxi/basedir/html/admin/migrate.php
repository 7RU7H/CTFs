<?php
//
// Migration from Core to XI
// Copyright (c) 2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();


// Only admins can access this page
if (!is_admin()) {
    echo _("You are not authorized to access this feature. Contact your system administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    $cmd = grab_request_var('cmd', '');
    switch ($cmd) {

        case 'getstatus':
            header("Content-Type: application/json");
            echo json_encode(migrate_get_status());
            break;

        case 'cleanup':
            migrate_cleanup();
            header('Location: migrate.php');
            exit();
            break;

        case 'migrate':
            submit_migrate_job();
            break;

        default:
            show_step1();
            break;

    }
}


function show_step1()
{
    global $request;
    $title = _('Migrate Server');

    $address = grab_request_var('address', '');
    $username = grab_request_var('username', 'root');
    $password = grab_request_var('password', '');
    $overwrite = intval(grab_request_var('overwrite', 1));
    $clear = intval(grab_request_var('clear', 0));
    $nagios_cfg = grab_request_var('nagios_cfg', '');

    // Check for running migration
    $status = array();
    $migration_running = intval(get_option('migration_running', 0));
    if ($migration_running) {
        $status = migrate_get_status();
    }

    do_page_start(array("page_title" => $title), true, true);
?>

<style type="text/css">
[v-cloak] {
  display: none !important;
}
.items { padding: 10px; border: 1px solid #FFF; }
.item { padding: 6px 12px; font-weight: bold; border-bottom: 1px dotted #DDD; }
.item:last-child { border-bottom: none; }
.item:nth-child(even) { background-color: #FFF; }
.item:nth-child(odd) { background-color: #F9F9F9; }
.item > img { margin-right: 10px; }
</style>

<h1><?php echo $title; ?></h1>

<div id="migrate" v-cloak>

    <p v-show="!migration_running"><?php echo _('Use this migration tool to import your existing Nagios Core configuration into this Nagios XI server. The tool will try to reach out to the Nagios Core server, collect the configuration data, and configure Nagios XI.'); ?><br><?php echo _('Root or sudo access to the Nagios Core server is required. Ensure that the Nagios XI system can connect to the Nagios Core server via SSH with a privledged user.'); ?></p>

    <form v-show="!migration_running" method="post" class="form-horizontal form-modern">
        <input type="hidden" name="cmd" value="migrate">

        <div style="margin: 20px 0 30px 0;">

            <div class="form-group">
                <label class="col-sm-6 col-lg-2 control-label"><?php echo _('Server Address'); ?></label>
                <div class="col-sm-6 col-lg-10">
                    <input name="address" class="form-control" style="width: 260px;" value="<?php echo encode_form_val($address); ?>" />
                    <div class="subtext"><?php echo _('IP Address or Hostname of the Nagios Core server to migrate to this system.'); ?></div>
                </div>
            </div>

            <h5 class="ul" style="margin-bottom: 10px;"><?php echo _('Credentials'); ?></h5>

            <div class="form-group">
                <label class="col-sm-6 col-lg-2 control-label"><?php echo _('Username'); ?></label>
                <div class="col-sm-6 col-lg-10">
                    <input type="text" name="username" class="username form-control" value="<?php echo encode_form_val($username); ?>">
                    <div class="subtext"><?php echo _('If not using root user, the user should have access to become root using sudo.'); ?></div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-6 col-lg-2 control-label"><?php echo _('Password'); ?></label>
                <div class="col-sm-6 col-lg-10">
                    <input type="password" name="password" value="<?php echo encode_form_val($password); ?>" class="form-control">
                    <button type="button" style="vertical-align: top;" class="btn btn-sm btn-default tt-bind btn-show-password" title="<?php echo _("Show password"); ?>"><i class="fa fa-eye"></i></button>
                </div>
            </div>

            <div class="advanced-button" style="margin: 30px 0 20px 0;">
                <a v-on:click="toggle_advanced"><span v-if="advanced"><?php echo _('Hide Advanced Settings'); ?></span><span v-else><?php echo _('Show Advanced Settings'); ?></span></a>
                <i v-if="!advanced" class="fa fa-chevron-up"></i><i v-else class="fa fa-chevron-down"></i></i>
            </div>

            <div v-show="advanced">

                <h5 class="ul" style="margin-bottom: 10px;"><?php echo _('Advanced Settings'); ?></h5>

                <div class="form-group">
                    <label class="col-sm-6 col-lg-2 control-label"><?php echo _('Overwrite Configs'); ?></label>
                    <div class="col-sm-6 col-lg-10 checkbox">
                        <label>
                            <input type="checkbox" name="overwrite" value="1" <?php echo is_checked($overwrite, 1); ?> />
                            <?php echo _('Overwrite duplicate configuration objects with the migrated version'); ?>
                        </label>
                        <!--<br>
                        <label>
                            <input type="checkbox" name="clear" value="1" <?php echo is_checked($clear, 1); ?> />
                            <?php echo _('Clear current configuration on this system'); ?>
                        </label>-->
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 col-lg-2 control-label"><?php echo _('Nagios Config File'); ?></label>
                    <div class="col-sm-6 col-lg-10">
                        <input name="nagios_cfg" class="form-control" style="width: 300px;" value="<?php echo encode_form_val($nagios_cfg); ?>" />
                        <div class="subtext">
                            <?php echo _('Full path and name of your nagios.cfg file if it is in a non-standard location (example: /opt/nagios/etc/nagios.cfg)'); ?><br>
                            <?php echo _('This nagios.cfg file will be added to the standard list of nagios.cfg locations that the migrate script will check when looking for your config file.'); ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div>
            <button type="submit" class="btn btn-sm btn-primary"><?php echo _('Migrate'); ?> <i class="fa fa-chevron-right"></i></button>
        </div>
    </form>

    <div v-show="migration_running">
        <div class="row">
            <div class="col-lg-6">
                <p><?php echo _('Migration in progress... please wait, this can take time depending on the size of the system being migrated.'); ?></p>
                <div class="well" style="margin-top: 10px; padding: 0;">
                    <div class="items">
                        <div class="item">
                            <img src="<?php echo theme_image('throbber.gif'); ?>" v-if="status.transfer == 0 && status.error == 0">
                            <img src="<?php echo theme_image('success_small.png'); ?>" v-if="status.transfer == 1">
                            <img src="<?php echo theme_image('cross.png'); ?>" v-if="status.transfer == 0 && status.error == 1">
                            <?php echo _('Bundled and transferred Nagios Core data'); ?>
                        </div>
                        <div class="item">
                            <img src="<?php echo theme_image('throbber.gif'); ?>" v-if="status.prep == 0 && status.error == 0">
                            <img src="<?php echo theme_image('success_small.png'); ?>" v-if="status.prep == 1">
                            <img src="<?php echo theme_image('cross.png'); ?>" v-if="status.prep == 0 && status.error == 1">
                            <?php echo _('Prepared Nagios Core data for import'); ?>
                        </div>
                        <div class="item">
                            <img src="<?php echo theme_image('throbber.gif'); ?>" v-if="status.import == 0 && status.error == 0">
                            <img src="<?php echo theme_image('success_small.png'); ?>" v-if="status.import == 1">
                            <img src="<?php echo theme_image('cross.png'); ?>" v-if="status.import == 0 && status.error == 1">
                            <?php echo _('Imported Nagios Core data into Nagios XI'); ?>
                        </div>
                        <div class="item">
                            <img src="<?php echo theme_image('throbber.gif'); ?>" v-if="status.apply == 0 && status.error == 0">
                            <img src="<?php echo theme_image('success_small.png'); ?>" v-if="status.apply == 1">
                            <img src="<?php echo theme_image('cross.png'); ?>" v-if="status.apply == 0 && status.error == 1">
                            <?php echo _('Applied new configuration in Nagios XI'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-show="migration_running && status.complete == 1">
        <?php echo _('Migration completed successfully!'); ?>
    </div>

    <div v-show="status.complete == 1">
        <p><?php echo sprintf(_("Now that your migration is complete, check for %s to ensure all plugins are running properly on the new system."), '<a href="../includes/components/xicore/status.php?&show=services&hoststatustypes=0&servicestatustypes=16">'._('any critical host/services').'</a>'); ?></p>
    </div>

    <div v-show="status.error == 1">
        <div class="row">
            <div class="col-lg-6">
                <p class="alert alert-danger" v-html="status.cmd_output"></p>
                <a href="migrate.php?cmd=cleanup" class="btn btn-sm btn-default"><?php echo _('Return'); ?></a>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
var data = {
    migration_running: <?php echo $migration_running; ?>,
    status: <?php echo json_encode($status); ?>,
    advanced: 0
};
var main = new Vue({
    el: '#migrate',
    data: data,
    methods: {
        poll_status: function() {
            $.get('migrate.php', { cmd: 'getstatus' }, function(status) {
                main.status = status;
            }, 'json');
            if (main.status.error == 0 && main.status.complete != 1) {
                setTimeout(this.poll_status, 2000);
            }
        },
        toggle_advanced: function() {
            main.advanced = !main.advanced;
        }
    },
    mounted: function() {
        if (data.migration_running && data.status.error == 0) {
            setTimeout(this.poll_status, 2000);
        }
    }
});
</script>

<?php 
    do_page_end(true);
}

function submit_migrate_job()
{
    // Verify another migration isn't running
    $migration_running = get_option('migration_running', 0);
    if ($migration_running) {
        flash_message(_('You cannot start a migration when a migration is currently running.'), FLASH_MSG_ERROR);
        show_step1();
        return;
    }

    // Grab data
    $address = grab_request_var('address', '');
    $username = grab_request_var('username', '');
    $password = grab_request_var('password', '');
    $overwrite = intval(grab_request_var('overwrite', 0));
    $clear = intval(grab_request_var('clear', 0));
    $nagios_cfg = grab_request_var('nagios_cfg', '');

    // Do a quick verification check
    if (empty($address) || empty($username) || empty($password)) {
        flash_message(_('You must fill out the form completely.'), FLASH_MSG_ERROR);
        show_step1();
        return;
    }

    // Start the migration job
    $data = array(
        'address' => $address,
        'username' => $username,
        'password' => encrypt_data($password),
        'overwrite' =>  $overwrite,
        'clear' => $clear,
        'nagios_cfg' => $nagios_cfg
    );
    $data = json_encode($data);
    $cmd_id = submit_command(COMMAND_START_MIGRATE, $data);
    if ($cmd_id < 1) {
        flash_message(_('Error submitting migration job.'), FLASH_MSG_ERROR);
        show_step1();
        return;
    }

    // Save info for tracking
    set_option('migration_running', 1);
    set_option('migration_cmd_id', $cmd_id);
    set_option('migrated', 1);

    // Clear current state
    set_option('migration_error', 0);
    set_option('migration_status_transfer', 0);
    set_option('migration_status_prep', 0);
    set_option('migration_status_import', 0);
    set_option('migration_status_apply', 0);
    set_option('migration_complete', 0);

    // Redirect back to main page (we track it there)
    header('Location: migrate.php');
    exit();
}

function migrate_get_status()
{
    $dir = get_root_dir() . '/scripts/migrate';
    $ansible_output = '';
    $error_output = '';

    // Get overall command progress
    $cmd_id = get_option('migration_cmd_id', 0);
    $cmd = get_command_status_xml_output(array('command_id' => $cmd_id), true, false);
    $cmd_output = '';

    if (!empty($cmd)) {
        $cmd = $cmd[0];
        $cmd_output = $cmd['result'];

        // If we failed and command finished executing, result code for 1 means
        // that the script died while doing an ansible playbook call so let's get the log output
        if ($cmd['status_code'] == 2 && $cmd['result_code'] == 1) {

            // Check for errors.txt file data first or get the output.json data
            $job_name = get_option('migration_job_name', '');
            if (!empty($job_name)) {
                $error_output = file_get_contents($dir.'/jobs/'.$job_name.'/errors.txt');
                if (empty($error_output)) {
                    
                    // Get the ansible output
                    $raw = file_get_contents($dir.'/jobs/'.$job_name.'/output.json');
                    $start = strpos($raw, '{');
                    $raw = substr($raw, $start);
                    $data = json_decode($raw, true);

                    // Check the tasks for a failed task
                    foreach ($data['plays'][0]['tasks'] as $task) {
                        $action = current($task['hosts']);
                        if ($action['failed']) {
                            if (!empty($action['msg']) && $action['msg'] == "MODULE FAILURE") {
                                if (!empty($action['module_stdout'])) {
                                    $ansible_output = $action['module_stdout'];
                                }
                                if (!empty($action['module_stderr'])) {
                                    $ansible_output .= '<br />'.$action['module_stderr'];
                                }
                            } else {
                                $ansible_output = $action['msg'];
                            }
                        }
                    }
                }
            }

        }
    }

    // Get specific progress report
    $status = array(
        'cmd_id' => $cmd_id,
        'cmd_output' => '<b>'.$cmd_output.'</b>',
        'error_output' => $error_output,
        'transfer' => get_option('migration_status_transfer', 0),
        'prep' => get_option('migration_status_prep', 0),
        'import' => get_option('migration_status_import', 0),
        'apply' => get_option('migration_status_apply', 0),
        'complete' => get_option('migration_complete', 0),
        'error' => get_option('migration_error', 0)
    );

    // Add ansible output for common problems
    if (!empty($ansible_output))  {
        if (strpos($ansible_output, '/usr/bin/python: No such file or directory') !== false) {
            $ansible_output = sprintf(_('The remote system does not have the python binary located at %s or a symlink to the proper location does not exist. If this is a CentOS/RHEL 8 system, you may need to run %s before running the migration.'), '/usr/bin/python', '<code>ln -s /usr/bin/python3 /usr/bin/python</code>');
        }
        $status['cmd_output'] .= '<br /><br /><b>'._('The script exited with an error on remote system:').'</b><br />'.$ansible_output;
    }

    // Add specific scenario data into error output for the user so it's easier to understand
    if ($status['import'] == 1 && $status['apply'] == 0 && $status['error'] == 1) {
        $status['cmd_output'] .= '<br /><br />'.sprintf(_('Check the %s to see the imported config. You can edit the configuration and re-apply or you can revert to the snapshot form before the migration in the %s page.'), '<a href="../includes/components/ccm/xi-index.php">'._('Core Config Manager').'</a>', '<a href="coreconfigsnapshots.php">'._('Configuration Snapshots').'</a>');
    }

    return $status;
}

function migrate_cleanup()
{
    set_option('migration_running', 0);
    set_option('migration_cmd_id', 0);
    set_option('migration_job_name', '');
    set_option('migration_error', 0);
    set_option('migration_status_transfer', 0);
    set_option('migration_status_prep', 0);
    set_option('migration_status_import', 0);
    set_option('migration_status_apply', 0);
    set_option('migration_complete', 0);
}
