#!/usr/bin/env python

# Note: this script assumes that you are already chdir'd to the correct location. 
import argparse
import re
import subprocess
import shlex
import time
import json
import os.path
import shutil

# TODO:
# - Options parsing:
# --- List directives that get overwritten in nagios.cfg
# --- Revisit the arguments which exist currently once the script has been finished, in case some don't make sense.
# - Main CFG:
# --- which directives do we actually care about??
# --- Ignore-main-cfg option is reasonable, overwrite maybe isn't.
# - Object Configuration:
# --- Default to whatever the import file thingy does.
# - Plugins
# --- Default to preferring plugins that exist on the system.
# --- Allowing override and ignore makes some amount of sense here.
# - Perfdata
# --- Won't necessarily be added for v1, since we'll need to translate between RRD schemas...
# --- ignore-perfdata will be implemented by default :)

# These are the directives in the UNMODIFIED section of the default Nagios XI config.
# Directives which we ignore anyways (currently just file paths) will be commented out rather than deleted.
nagios_cfg_directives = [
    'accept_passive_host_checks',
    'accept_passive_service_checks',
    'additional_freshness_latency',
    'auto_reschedule_checks',
    'auto_rescheduling_interval',
    'auto_rescheduling_window',
    'bare_update_check',
    'cached_host_check_horizon',
    'cached_service_check_horizon',
    'check_external_commands',
    'check_for_orphaned_hosts',
    'check_for_orphaned_services',
    'check_for_updates',
    'check_host_freshness',
#    'check_result_path',
    'check_result_reaper_frequency',
    'check_service_freshness',
#    'command_file',
    'daemon_dumps_core',
    'date_format',
    'debug_file',
    'debug_level',
    'debug_verbosity',
    'enable_event_handlers',
    'enable_flap_detection',
    'enable_notifications',
    'enable_predictive_host_dependency_checks',
    'enable_predictive_service_dependency_checks',
    'event_broker_options',
    'event_handler_timeout',
    'execute_host_checks',
    'execute_service_checks',
    'high_host_flap_threshold',
    'high_service_flap_threshold',
    'host_check_timeout',
    'host_freshness_check_interval',
    'host_inter_check_delay_method',
    'illegal_macro_output_chars',
    'illegal_object_name_chars',
    'interval_length',
    'lock_file',
#    'log_archive_path',
    'log_external_commands',
#    'log_file',
    'log_host_retries',
    'log_initial_states',
    'log_notifications',
    'log_passive_checks',
    'log_rotation_method',
    'log_service_retries',
    'low_host_flap_threshold',
    'low_service_flap_threshold',
    'max_check_result_file_age',
    'max_check_result_reaper_time',
    'max_concurrent_checks',
    'max_debug_file_size',
    'max_host_check_spread',
    'max_service_check_spread',
    'nagios_group',
    'nagios_user',
    'notification_timeout',
#    'object_cache_file',
    'obsess_over_hosts',
    'obsess_over_services',
    'ocsp_timeout',
    'passive_host_checks_are_soft',
    'perfdata_timeout',
#    'precached_object_file',
#    'resource_file',
    'retained_contact_host_attribute_mask',
    'retained_contact_service_attribute_mask',
    'retained_host_attribute_mask',
    'retained_process_host_attribute_mask',
    'retained_process_service_attribute_mask',
    'retained_service_attribute_mask',
    'retain_state_information',
    'retention_update_interval',
    'service_check_timeout',
    'service_freshness_check_interval',
    'service_inter_check_delay_method',
    'service_interleave_factor',
    'soft_state_dependencies',
#    'state_retention_file',
#    'status_file',
    'status_update_interval',
#    'temp_file',
#    'temp_path',
    'use_aggressive_host_checking',
    'use_regexp_matching',
    'use_retained_program_state',
    'use_retained_scheduling_info',
    'use_syslog',
    'use_true_regexp_matching'
]

def get_options():

    parser = argparse.ArgumentParser()

    parser.add_argument('-j', '--json-file', default="nagiosbundle.json", 
        help="The path (relative or absolute) to the nagiosbundle json file. Defaults to nagiosbundle.json in the current working directory.")

    parser.add_argument('--ignore-main-cfg', default=False, action='store_const', const=True,
        help="Do not overwrite any part of nagios.cfg. Default behavior is to only overwrite certain directives (see --help-default-directives)")
    parser.add_argument('--overwrite-main-cfg', default=False, action='store_const', const=True,
        help="Completely overwrite nagios.cfg (not recommended).")

    parser.add_argument('--ignore-configuration', default=False, action='store_const', const=True,
        help="Do not overwrite any part of nagios object configuration. Default behavior is to merge existing object configuration, preferring data in the bundle.")
    parser.add_argument('--overwrite-configuration', default=False, action='store_const', const=True,
        help="Completely overwrite all nagios object configuration, removing configuration data that existed beforehand.")

    parser.add_argument('--ignore-plugins', default=False, action='store_const', const=True,
        help="Do not import any plugins. Default behavior is to import new plugins, but prefer any that already exist on the Nagios XI system.")
    parser.add_argument('--overwrite-plugins', default=False, action='store_const', const=True,
        help="Always prefer plugins which were included in the bundle (not recommended - some plugins may be compiled binaries).")

    #parser.add_argument('--ignore-perfdata', default=False, action='store_const', const=True,
    #    help="Do not overwrite any part of nagios.cfg. Default behavior is to only overwrite certain directives: (TODO: list directives here)")
    #parser.add_argument('--overwrite-perfdata', default=False, action='store_const', const=True,
    #    help="Completely overwrite nagios.cfg (not recommended).")

    parser.add_argument('--help-default-directives', default=False, action='store_const', const=True,
        help="Print the main_cfg directives for which the bundle's value is preferred by default.")

    args = parser.parse_args()
    if args.help_default_directives:
        print("The following directives will use the bundle's value by default: \n%s\n", "\n".join(nagios_cfg_directives))
    return args 

def initialize_nagiosbundle_json(json_path):
    nagiosbundle_json = None
    with open(json_path) as nagiosbundle_json_bytes:
        nagiosbundle_json = json.load(nagiosbundle_json_bytes)
    return nagiosbundle_json

def tar_abs_to_relative(path):
    if path[0] == '/':
        return path[1:]
    return path

def import_main_cfg(core_nagios_cfg_path, ignore_this_step, full_overwrite):
    print("Importing nagios.cfg")
    if ignore_this_step:
        return

    xi_nagios_cfg_path = '/usr/local/nagios/etc/nagios.cfg'
    core_nagios_cfg_path = tar_abs_to_relative(core_nagios_cfg_path)

    if full_overwrite:
        # This is almost never a reasonable thing to do
        xi_stat_result = os.stat(xi_nagios_cfg_path)
        shutil.copy(core_nagios_cfg_path, xi_nagios_cfg_path)
        os.chown(xi_nagios_cfg_path, xi_stat_result.st_uid, xi_stat_result.st_gid)
        return

    core_nagios_cfg_text = None
    with open(core_nagios_cfg_path) as core_nagios_cfg:
        core_nagios_cfg_text = core_nagios_cfg.read()

    xi_nagios_cfg_text = None
    with open(xi_nagios_cfg_path) as xi_nagios_cfg:
        xi_nagios_cfg_text = xi_nagios_cfg.read()

    base_regexp = '^%s=(.*)'
    for directive in nagios_cfg_directives:
        this_regexp = re.compile(base_regexp % directive)
        
        # Find value in Core file
        matches = re.search(this_regexp, core_nagios_cfg_text)
        if not matches:
            # Core's cfg does not contain this directive
            continue

        # Replace value in XI file
        replace_text = "%s=%s" % (directive, matches.group(1))
        print(replace_text)
        re.sub(this_regexp, replace_text, xi_nagios_cfg_text)

    with open(xi_nagios_cfg_path, 'w') as xi_nagios_cfg:
        xi_nagios_cfg.write(xi_nagios_cfg_text)


# For each directory:
# Recursively iterate through the directory, adding each individual .cfg file to the list of individual files
# For each individually-named file:
# Parse the top level of each entry (so just object type and record the full text)
# Sort objects into the order recommended by the import tool
# Place these into different files
def import_object_cfg(cfg_files, cfg_dirs, ignore_this_step, full_overwrite, is_nagiosxi):
    print("Importing object configuration")
    if ignore_this_step:
        return

    # These are static XI files that shouldn't be imported
    # if the bundle was from a Nagios XI system
    xi_static_files = ['xiobjects.cfg', 'xitemplates.cfg', 'xitest.cfg']

    # Make the directory
    os.mkdir('nagiosbundle-config')
    os.mkdir('nagiosbundle-config/services')

    for cfg_dir in cfg_dirs:
        cfg_dir = tar_abs_to_relative(cfg_dir)
        if os.path.isdir(cfg_dir):
            for filename in os.listdir(cfg_dir):
                if is_nagiosxi and filename in xi_static_files:
                    continue
                if os.path.isdir(filename):
                    cfg_dirs.append(cfg_dir + '/' + filename)
                else:
                    cfg_files.append(cfg_dir + '/' + filename)

    # Remove Nagios XI specific static config files
    


    # explanation:
    # (                                                              <-- capture full matched text
    #  \s*define\s*<object_type>\s*{                                 <-- literals (start of object definition)
    #                            (?:                                 <-- non-capturing group
    #                               [^}\n]*\n                        <-- line not containing end brace
    #                                        |                       <-- or
    #                                         [^}\n]*#[^\n]*\n       <-- line without end brace until comment begins, after which end braces are allowed
    #                                                         )*\s*} <-- repeat group until line with just end brace is reached
    base_regexp = '(\s*define\s*%s\s*{(?:[^}\n]*\n|[^}\n]*#[^\n]*\n)*\s*})'
    command_regexp = re.compile(base_regexp % "command")
    timeperiod_regexp = re.compile(base_regexp % "timeperiod")
    contact_regexp = re.compile(base_regexp % "contact")
    contactgroup_regexp = re.compile(base_regexp % "contactgroup")
    host_regexp = re.compile(base_regexp % "host")
    hostgroup_regexp = re.compile(base_regexp % "hostgroup")
    service_regexp = re.compile(base_regexp % "service")
    servicegroup_regexp = re.compile(base_regexp % "servicegroup")

    commands = []
    timeperiods = []
    contacts = []
    contactgroups = []
    hosts = []
    hostgroups = []
    services = []
    servicegroups = []

    for cfg_file in cfg_files:
        cfg_file = tar_abs_to_relative(cfg_file)
        print("Importing from %s..." % cfg_file)
        if cfg_file[-4:] != '.cfg':
            continue # not actually a nagios config file, wasn't being read by nagios daemon

        cfg_file_text = ''
        try:
            with open(cfg_file) as cfg_file_bytes:
                cfg_file_text = cfg_file_bytes.read()
        except IOError as error:
            print("Warning: could not open %s" % cfg_file)
            continue

        commands += command_regexp.findall(cfg_file_text)
        timeperiods += timeperiod_regexp.findall(cfg_file_text)
        contacts += contact_regexp.findall(cfg_file_text)
        contactgroups += contactgroup_regexp.findall(cfg_file_text)
        hosts += host_regexp.findall(cfg_file_text)
        hostgroups += hostgroup_regexp.findall(cfg_file_text)
        services += service_regexp.findall(cfg_file_text)
        servicegroups += servicegroup_regexp.findall(cfg_file_text)

    print("Total commands: %d" % len(commands))
    print("Total timeperiods: %d" % len(timeperiods))
    print("Total contacts: %d" % len(contacts))
    print("Total contactgroups: %d" % len(contactgroups))
    print("Total hosts: %d" % len(hosts))
    print("Total hostgroups: %d" % len(hostgroups))
    print("Total services: %d" % len(services))
    print("Total servicegroups: %d" % len(servicegroups))
    template_regexp = re.compile('register\s*0')

    # Sort captured objects into templates vs "real" objects, where applicable
    contact_templates = []
    contact_real = []
    for contact in contacts:
        if template_regexp.search(contact):
            contact_templates.append(contact)
        else:
            contact_real.append(contact)
    contacts = contact_real

    host_templates = []
    host_real = []
    for host in hosts:
        if template_regexp.search(host):
            host_templates.append(host)
        else:
            host_real.append(host)
    hosts = host_real

    service_templates = []
    service_real = []
    for service in services:
        if template_regexp.search(service):
            service_templates.append(service)
        else:
            service_real.append(service)
    services = service_real

    print("Total real contacts: %d" % len(contacts))
    print("Total tmpl contacts: %d" % len(contact_templates))
    print("Total real hosts: %d" % len(hosts))
    print("Total tmpl hosts: %d" % len(host_templates))
    print("Total real services: %d" % len(services))
    print("Total tmpl services: %d" % len(service_templates))
    commands = '\n'.join(commands)
    with open('nagiosbundle-config/commands.cfg', 'w') as commands_write:
        commands_write.write(commands)
    
    timeperiods = '\n'.join(timeperiods)
    with open('nagiosbundle-config/timeperiods.cfg', 'w') as timeperiods_write:
        timeperiods_write.write(timeperiods)
    
    contact_templates = '\n'.join(contact_templates)
    with open('nagiosbundle-config/contacttemplates.cfg', 'w') as contact_templates_write:
        contact_templates_write.write(contact_templates)
    
    contacts = '\n'.join(contacts)
    with open('nagiosbundle-config/contacts.cfg', 'w') as contacts_write:
        contacts_write.write(contacts)
    
    contactgroups = '\n'.join(contactgroups)
    with open('nagiosbundle-config/contactgroups.cfg', 'w') as contactgroups_write:
        contactgroups_write.write(contactgroups)
    
    host_templates = '\n'.join(host_templates)
    with open('nagiosbundle-config/hosttemplates.cfg', 'w') as host_templates_write:
        host_templates_write.write(host_templates)

    hosts = '\n'.join(hosts)
    with open('nagiosbundle-config/hosts.cfg', 'w') as hosts_write:
        hosts_write.write(hosts)
    
    hostgroups = '\n'.join(hostgroups)
    with open('nagiosbundle-config/hostgroups.cfg', 'w') as hostgroups_write:
        hostgroups_write.write(hostgroups)

    service_templates = '\n'.join(service_templates)
    with open('nagiosbundle-config/servicetemplates.cfg', 'w') as service_templates_write:
        service_templates_write.write(service_templates)

    # Special section for services to link services to the first host name provided if they have one
    # otherwise they will be added to a 'migrated-services' file instead.
    get_host_regex = re.compile('(?:host_name\s([^;^,^\n]*))')
    file_name = 'nagiosbundle-config/migrated-services'
    for service in services:
        found_host_name = get_host_regex.findall(service)
        if found_host_name:
            file_name = found_host_name[0].strip()

        with open('nagiosbundle-config/services/'+file_name+'.cfg', 'a') as service_file:
            service_file.write(service+'\n')

    servicegroups = '\n'.join(servicegroups)
    with open('nagiosbundle-config/servicegroups.cfg', 'w') as servicegroups_write:
        servicegroups_write.write(servicegroups)



# For each file in the plugins directory, check if a file with the same name exists in XI
# if it doesn't exist, or if full_overwrite is set, copy the plugin from the bundle to libexec
# Note: libexec is hardcoded, since it's hardcoded in config.inc.php. We may want a more robust way to handle this
def import_plugins(plugin_directories, ignore_this_step, full_overwrite):
    print("Importing plugins")
    if ignore_this_step:
        return

    xi_plugin_dir = '/usr/local/nagios/libexec/'
    for plugin_dir in plugin_directories:
        plugin_dir = tar_abs_to_relative(plugin_dir)

        for filename in os.listdir(plugin_dir):
            print("Found %s" % filename)
            basename = os.path.basename(filename)
            if os.path.isdir(plugin_dir + '/' + filename):
                # either copy the whole directory or don't
                # this isn't 100% correct but it's only wrong when XI has a specific directory and Core had the same directory with different files
                if full_overwrite or not os.path.exists(xi_plugin_dir + basename):
                    # copytree may not be correct if the directory does exist (i.e. because full_overwrite is set)
                    shutil.copytree(plugin_dir + '/' + filename, xi_plugin_dir + basename)
            else:
                if full_overwrite or not os.path.exists(xi_plugin_dir + basename):
                    print("Actually writing %s" % filename)
                    shutil.copy(plugin_dir + '/' + filename, xi_plugin_dir)

#TODO: finish this
def import_resource_file(resource_file):

    # Find XI's main resource file
    main_cfg_text = ''
    with open('/usr/local/nagios/etc/nagios.cfg') as main_cfg:
        main_cfg_text = [line for line in main_cfg]

    parsed_resource_files = []
    for line in main_cfg_text:
        parsed_resource_files += re.findall(r'resource_file=(.*)\n', line)
    xi_resource_file = parsed_resource_files[0] if parsed_resource_files else '/usr/local/nagios/etc/resource.cfg'
    if xi_resource_file[0] != '/':
        xi_resource_file = "/usr/local/nagios/etc/" + xi_resource_file

    # Make a backup of the unmodified resource file
    shutil.copy(xi_resource_file, xi_resource_file + '.bak')

def main():
    options = get_options()
    nagiosbundle_json = initialize_nagiosbundle_json(options.json_file)
    import_main_cfg(nagiosbundle_json['main_cfg'], options.ignore_main_cfg, options.overwrite_main_cfg)
    #import_resource_file(nagiosbundle_json['resource_file'])
    import_object_cfg(nagiosbundle_json['cfg_file'], nagiosbundle_json['cfg_dir'], options.ignore_configuration, options.overwrite_configuration, nagiosbundle_json['is_nagiosxi'])
    import_plugins(nagiosbundle_json['plugin_dir'], options.ignore_plugins, options.overwrite_plugins)
    #import_perfdata(options.ignore_perfdata, options.overwrite_perfdata)

if __name__ == '__main__':
    main()
