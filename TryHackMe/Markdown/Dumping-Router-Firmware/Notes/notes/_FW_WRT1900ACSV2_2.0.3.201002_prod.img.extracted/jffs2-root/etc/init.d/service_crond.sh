#!/bin/sh
source /etc/init.d/ulog_functions.sh
SERVICE_NAME="crond"
SELF_NAME="`basename $0`"
ulog wlan status "${SERVICE_NAME}, sysevent received: $1" > /dev/console
service_start () 
{
   ulog "${SERVICE_NAME}, service_start()"
   DEV_TYPE=`syscfg get ldal_wl_lego_device_type`
   WIFI_STA="false"
   if [ ! -z "$DEV_TYPE" ] && [ "LegoExtender" = "$DEV_TYPE" ]; then
	  WIFI_STA="true"
   fi
   killall crond > /dev/null 2>&1
   
   CRONTAB_DIR="/var/spool/cron/crontabs/"
   CRONTAB_FILE=$CRONTAB_DIR"root"
   if [ ! -e $CRONTAB_FILE ] ; then
        if [ -n "`syscfg get device::mac_addr`" ] ; then
            OUR_MAC=`syscfg get device::mac_addr`
        else
            INT=`sysevent get current_wan_ifname`
            if [ -z "$INT" ] ; then
                INT=`syscfg get wan_physical_ifname`
            fi
            OUR_MAC=`ip link show dev $INT | grep link | awk '{print $2}'`
        fi
      MAC1=$(echo $OUR_MAC | awk 'BEGIN{FS=":"} {print $6}')
      MAC1=$(printf %d 0x$MAC1)   # convert MAC byte to decimal
      MAC2=$(echo $OUR_MAC | awk 'BEGIN{FS=":"} {print $5}')
      MAC2=$(printf %d 0x$MAC2)   # convert MAC byte to decimal
      RANDOM=`expr $MAC1 \* $MAC2`
      mkdir -p /etc/cron/cron.everyminute
	  if [ "true" = "$WIFI_STA" ]; then
      	mkdir -p /etc/cron/cron.every2minute
	  fi
      mkdir -p /etc/cron/cron.every5minute
      mkdir -p /etc/cron/cron.every10minute
      mkdir -p /etc/cron/cron.every30minute
      mkdir -p /etc/cron/cron.hourly
      mkdir -p /etc/cron/cron.daily
      mkdir -p /etc/cron/cron.weekly
      mkdir -p /etc/cron/cron.monthly
      mkdir -p $CRONTAB_DIR
   
      echo "* * * * *  /sbin/execute_dir /etc/cron/cron.everyminute" > $CRONTAB_FILE
	  if [ "true" = "$WIFI_STA" ]; then
      	echo "1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,55,57,59 * * * *  /sbin/execute_dir /etc/cron/cron.every2minute" >> $CRONTAB_FILE
	  fi
      echo "1,6,11,16,21,26,31,36,41,46,51,56 * * * *  /sbin/execute_dir /etc/cron/cron.every5minute" >> $CRONTAB_FILE
      echo "2,12,22,32,42,52 * * * *  /sbin/execute_dir /etc/cron/cron.every10minute" >> $CRONTAB_FILE
      echo "3,33 * * * *  /sbin/execute_dir /etc/cron/cron.every30minute" >> $CRONTAB_FILE
      num1=$RANDOM
      rand1=`expr $num1 % 60`
      echo "$rand1 * * * * /sbin/execute_dir /etc/cron/cron.hourly" >> $CRONTAB_FILE
      num1=$RANDOM
      num2=$RANDOM
      rand1=`expr $num1 % 60`
      rand2=`expr $num2 % 24`
      echo "$rand1 $rand2 * * * /sbin/execute_dir /etc/cron/cron.daily" >> $CRONTAB_FILE
      num1=$RANDOM
      num2=$RANDOM
      num3=$RANDOM
      rand1=`expr $num1 % 60`
      rand2=`expr $num2 % 24`
      rand3=`expr $num3 % 7`
      echo "$rand1 $rand2 * * $rand3 /sbin/execute_dir /etc/cron/cron.weekly" >> $CRONTAB_FILE
      num1=$RANDOM
      num2=$RANDOM
      num3=$RANDOM
      rand1=`expr $num1 % 60`
      rand2=`expr $num2 % 24`
      rand3=`expr $num3 % 28`
      echo "$rand1 $rand2 $rand3 * * /sbin/execute_dir /etc/cron/cron.monthly" >> $CRONTAB_FILE
      echo "#! /bin/sh" > /etc/cron/cron.daily/ddns_daily.sh
      echo "sysevent set wan_last_ipaddr " >> /etc/cron/cron.daily/ddns_daily.sh
      echo "sysevent set ddns-start " >> /etc/cron/cron.daily/ddns_daily.sh
      chmod 700 /etc/cron/cron.daily/ddns_daily.sh
      echo "#! /bin/sh" > /etc/cron/cron.hourly/ntp_hourly.sh
      echo "sysevent set ntpclient-check" >> /etc/cron/cron.hourly/ntp_hourly.sh
      chmod 700 /etc/cron/cron.hourly/ntp_hourly.sh
      echo "#! /bin/sh" > /etc/cron/cron.everyminute/arp_mon_everyminute.sh
      echo "/etc/init.d/arp_mon.sh" >> /etc/cron/cron.everyminute/arp_mon_everyminute.sh
      chmod 700 /etc/cron/cron.everyminute/arp_mon_everyminute.sh
      echo "#! /bin/sh" > /etc/cron/cron.everyminute/pmon_everyminute.sh
      echo "/etc/init.d/pmon.sh" >> /etc/cron/cron.everyminute/pmon_everyminute.sh
      chmod 700 /etc/cron/cron.everyminute/pmon_everyminute.sh
      echo "#! /bin/sh" > /etc/cron/cron.everyminute/sysevent_tick.sh
      echo "sysevent set cron_every_minute" >> /etc/cron/cron.everyminute/sysevent_tick.sh
      chmod 700 /etc/cron/cron.everyminute/sysevent_tick.sh
	  if [ "true" = "$WIFI_STA" ]; then
      	echo "#! /bin/sh" > /etc/cron/cron.everyminute/wifi_worker.sh
      	echo "/etc/init.d/service_wifi/ldal_station_check_wifi.sh" >> /etc/cron/cron.everyminute/wifi_worker.sh
      	chmod 700 /etc/cron/cron.everyminute/wifi_worker.sh
	  fi
      echo "*/3 * * * * /etc/init.d/service_wifi/service_wifi_monitor.sh" >> $CRONTAB_FILE
      WIFI_CRON_DIR="/tmp/cron"
      FREQUENCY=`syscfg get wifi_scheduler::frequency`
      echo "#!/bin/sh" > "$WIFI_CRON_DIR"/wifi_scheduler_cron.sh
      echo "if [ \"\`syscfg get wifi_scheduler::enabled\`\" = \"1\" ] && [ ! -z \"\`syscfg get wifi_scheduler::if_enabled\`\" ]; then" >> "$WIFI_CRON_DIR"/wifi_scheduler_cron.sh
      echo "sysevent set wifi_scheduler_changed 1" >> "$WIFI_CRON_DIR"/wifi_scheduler_cron.sh
      echo "fi" >> "$WIFI_CRON_DIR"/wifi_scheduler_cron.sh
      chmod 700 "$WIFI_CRON_DIR"/wifi_scheduler_cron.sh
      if [ ! -z "$FREQUENCY" ]; then	 
	      echo "*/$FREQUENCY * * * * $WIFI_CRON_DIR/wifi_scheduler_cron.sh" >> $CRONTAB_FILE
      fi
      ln -sf /usr/bin/cronExpireSessions /etc/cron/cron.everyminute
   fi
   crond -l 9 -c $CRONTAB_DIR
   sysevent set ${SERVICE_NAME}-status "started"
}
service_stop () 
{
   ulog "${SERVICE_NAME}, service_stop()"
   killall crond > /dev/null 2>&1
   sysevent set ${SERVICE_NAME}-status "stopped"
}
case "$1" in
  ${SERVICE_NAME}-start)
      service_start
      ;;
  ${SERVICE_NAME}-stop)
      service_stop
      ;;
  ${SERVICE_NAME}-restart)
      service_stop
      service_start
      ;;
   ntpclient-status)
      STATUS=`sysevent get ntpclient-status`
      if [ "started" = "$STATUS" ] ; then 
		CRONTAB_DIR="/var/spool/cron/crontabs/"
        ulog ${SERVICE_NAME} status "restarting ${SERVICE_NAME} service" 
        killall crond > /dev/null 2>&1
        crond -l 9 -c $CRONTAB_DIR
      fi
      ;;
  *)
      echo "Usage: $SELF_NAME [${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" >&2
      exit 3
      ;;
esac
