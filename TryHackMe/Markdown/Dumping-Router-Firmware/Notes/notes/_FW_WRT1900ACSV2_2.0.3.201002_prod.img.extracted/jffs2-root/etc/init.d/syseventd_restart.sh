#!/bin/sh
source /etc/init.d/ulog_functions.sh
do_restart() {
   ulog system "Restarting sysevent subsystem"
   /sbin/syseventd
   sleep 2
   apply_system_defaults
   INIT_DIR=/etc/registration.d
   execute_dir $INIT_DIR
   ulog system "Restarting lan and wan"
   sysevent set lan-start
   sysevent set wan-start
}
do_restart
