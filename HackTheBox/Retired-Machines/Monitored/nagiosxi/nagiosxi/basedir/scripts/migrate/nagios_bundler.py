#!/usr/bin/env python

#TODO/known issues
# - We set tar with --ignore-failed-read since some supplied paths may not work.
# ---Instead, we should stat() paths ourselves, and only supply them if they exist.
# ---If tar fails after, then we have a failure condition (?)
# ---Should we also fail if any paths are missing? Perfdata dir in particular has a default path, when perfdata doesn't get processed on a bare core install
# ---This should also let us clean up the generated coremigration.json, which includes the missing paths if tar fails
# - coremigration.json should just give us the relative path that we get from extracting the tar, not the absolute path that we feed in

import argparse
import re
import subprocess
import shlex
import time
import json
import os.path
import sys


def get_options():

    parser = argparse.ArgumentParser()

    parser.add_argument('-f', '--main-config-file', default="/usr/local/nagios/etc/nagios.cfg", 
        help="The <config_file> directive passed to the nagios daemon. Defaults to /usr/local/nagios/etc/nagios.cfg")
    parser.add_argument('-p', '--perfdata-dir', default="/usr/local/nagios/share/perfdata", 
        help="The location of your pnp4nagios performance data directory. Defaults to /usr/local/nagios/share/perfdata")
    parser.add_argument('-c', '--extra-cfg-file', default=[], action='append', help="If there are any individual nagios configuration files, " + 
        "other than those specified by the cfg_file directive in the argument to -f, list them with this option.")
    parser.add_argument('-d', '--extra-cfg-dir', default=[], action='append', help="If there are any directories containing nagios configuration files, " +
        "other than those specified by the cfg_dir directive in the argument to -f, list them with this option.")
    parser.add_argument('-P', '--plugin-dir', default=[], action='append', help="If there are any plugin directories other than $USER1$ in resource.cfg, " +
        "list them with this option")
    parser.add_argument('-r', '--resource-file', default='', help='Overrides the resource_file parsed from nagios.cfg')

    parser.add_argument('--override-configuration', default=False, action='store_const', const=True,
        help="Only use -c and -d to list configuration directories, do not parse the file given in -f.")
    parser.add_argument('--override-plugins', default=False, action='store_const', const=True,
        help="Only use -p to list plugin directories, do not parse the file given in -f or its resource file.")

    args = parser.parse_args()

    possible_cfg_files = [args.main_config_file, '/usr/local/nagios/etc/nagios.cfg', '/etc/nagios/nagios.cfg', '/etc/nagios3/nagios.cfg', '/etc/nagios4/nagios.cfg']
    for path in possible_cfg_files:
        if os.path.exists(path):
            args.main_config_file = path
            break
    else: # loop did not break
        print("Error: file does not exist at %s nor at other default nagios.cfg paths" % args.main_config_file)
        sys.exit(1)

    main_cfg_text = ''
    with open(args.main_config_file) as main_cfg:
        main_cfg_text = [line for line in main_cfg]

    nagios_base_path = os.path.dirname(args.main_config_file)

    parsed_cfg_dirs = []
    parsed_cfg_files = []
    if not args.override_configuration:
        for line in main_cfg_text:
            parsed_cfg_dirs += re.findall(r'^cfg_dir=(.*)\n', line)
            parsed_cfg_files += re.findall(r'^cfg_file=(.*)\n', line)

    parsed_plugin_directories = []
    if not args.override_plugins:
        
        if not args.resource_file:
            parsed_resource_files = []
            for line in main_cfg_text:
                parsed_resource_files += re.findall(r'resource_file=(.*)\n', line)
            args.resource_file = parsed_resource_files[0] if parsed_resource_files else '/usr/local/nagios/etc/resource.cfg'

        with open(args.resource_file) as resource_cfg:
            for line in resource_cfg:
                parsed_plugin_directories += re.findall(r'\$USER1\$=(.*)\n', line)

    # For relative file paths parsed from nagios config, prepend the nagios.cfg base path
    # relative paths given thru the command line can be left alone, since we're just passing these to tar
    for i, filename in enumerate(parsed_cfg_files):
        if filename[0] != '/':
            parsed_cfg_files[i] = nagios_base_path + '/' + filename
    for i, filename in enumerate(parsed_cfg_dirs):
        if filename[0] != '/':
            parsed_cfg_dirs[i] = nagios_base_path + '/' + filename
    for i, filename in enumerate(parsed_plugin_directories):
        if filename[0] != '/':
            parsed_plugin_directories[i] = nagios_base_path + '/' + filename


    args.extra_cfg_file += parsed_cfg_files
    args.extra_cfg_dir += parsed_cfg_dirs
    args.plugin_dir += parsed_plugin_directories

    return args 

def create_archive(options):

    # Check if this is an XI system
    is_nagiosxi = False
    if os.path.isfile('/usr/local/nagiosxi/var/xiversion'):
        is_nagiosxi = True

    migration_info = { 'cfg_file': options.extra_cfg_file, 'cfg_dir': options.extra_cfg_dir, 'plugin_dir': options.plugin_dir,
                       'perfdata_dir': options.perfdata_dir, 'main_cfg': options.main_config_file, 'resource_file': options.resource_file,
                       'is_nagiosxi': is_nagiosxi }

    file_list = "%s %s %s %s %s %s" % (' '.join(options.extra_cfg_file), ' '.join(options.extra_cfg_dir), ' '.join(options.plugin_dir), options.perfdata_dir, options.resource_file, options.main_config_file)

    # Bundle up files
    timestamp = time.time()
    process = subprocess.Popen(shlex.split('tar --ignore-failed-read -cf nagiosbundle-%d.tar %s' % (timestamp, file_list)))
    process.wait()

    with open('nagiosbundle.json', 'w') as dumpfile:
        json.dump(migration_info, dumpfile)

    # Add information json file
    process = subprocess.Popen(shlex.split('tar -rf nagiosbundle-%d.tar nagiosbundle.json' % timestamp))
    process.wait()

    process = subprocess.Popen(shlex.split('gzip nagiosbundle-%d.tar' % timestamp))
    process.wait()

    # Cleanup
    os.remove('nagiosbundle.json');

def main():
    options = get_options()
    create_archive(options)

if __name__ == '__main__':
    main()