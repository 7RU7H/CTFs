#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
SERVICE_NAME="mediaserver"
WORKDIR1="/usr/local/MediaServer"
WORKDIR2="`dirname $0`"
PIDFILE="/tmp/config/mediaserver.pid"
APPDATA="/tmp/share/.system"
SELF_NAME="`basename $0`"
CFGDIR="/tmp/config"
CFGFILE="$CFGDIR/mediaserver.ini"
if [ "`cat /etc/product`" = "wraith" ]; then
DAEMON=twonkystarter
else
DAEMON=twonkymedia
fi
TWONKYSRV="/usr/local/MediaServer/${DAEMON}"
LOG_FILE="/tmp/service_mediaserver.log"
echo "" > $LOG_FILE
service_init ()
{
  TMS_ENABLED=`syscfg get media_server_enabled`
  TMS_NAME=`syscfg get media_server_name`
  if [ "$TMS_NAME" == "" ] ; then
    TMS_NAME=`hostname`
  fi
  TMS_PORT=`syscfg get media_server_port`
  TMS_SCANTIME=`syscfg get media_server_scan_time`
  TMS_SHDEFAULT=`syscfg get media_server_default_share`
  TMS_WEBENA="2"
  TMS_CONTENTDIR=`syscfg get media_server_contentdir`
  TMS_V=`syscfg get media_server_v`
  if [ X"$TMS_V" == X"" ]; then 
	TMS_V=0
  fi
  TMS_VLEVEL=`syscfg get media_server_vlevel`
  if [ X"$TMS_VLEVEL" == X"" ]; then 
	TMS_VLEVEL=0
  fi
  TMS_MAXITEMS=`syscfg get media_server_maxitems`
  if [ X"$TMS_MAXITEMS" == X"" ]; then 
	TMS_MAXITEMS=80000
  fi
  
  if [ -e "/tmp/TwonkyMediaServer-log.txt" ] ; then
      rm -f /tmp/TwonkyMediaServer-log.txt
  fi
  ln -sf /dev/null /tmp/TwonkyMediaServer-log.txt
  
  if [ ! -d "$CFGDIR" ]; then
    mkdir -p "$CFGDIR"
  fi
}
get_first_media_drive () {
  drive=`ls /mnt/ | grep sd | sort | head -n 1`
  if [ "$drive" ] ; then
    echo "$drive"
  fi
}
get_all_media_drives () {
  drive=`ls /mnt/ | grep sd | sort`
  if [ "$drive" ] ; then
    echo "$drive"
  fi
}
config_cgroup()
{
    if [ ! -f /cgroup/tasks ]; then
        return
    fi
    if [ ! -d /cgroup/twonky ]; then
        mkdir -p /cgroup/twonky
        echo 3 > /proc/sys/vm/drop_caches
        echo 1 > /proc/sys/vm/compact_memory
        local mem
        mem=`grep -e '^MemTotal' /proc/meminfo | awk '{print $2}'`
        mem=`expr $mem / 4`
        local free
        free=`grep -e '^MemFree' /proc/meminfo | awk '{print $2}'`
        free=`expr $free - 10240`
        if [ "$mem" -gt "$free" ]; then
            mem=$free
        fi
        mem=`expr $mem \* 1024`
        echo "$mem" > /cgroup/twonky/memory.limit_in_bytes
        echo "1" > /cgroup/twonky/memory.oom_control
    fi
    echo 0 > /cgroup/twonky/tasks
}
service_start ()
{
   if [ $TMS_ENABLED -gt 0 ]; then
      config_cgroup
      wait_till_end_state ${SERVICE_NAME}
      
      DEFDRIVE=$(get_first_media_drive)
      
      BASEDIR="/mnt"
      DBDIR="/mnt/$DEFDRIVE"
      APPDATA="/mnt/$DEFDRIVE/.tmp"
      TMS_SHDEFAULT="/mnt/$DEFDRIVE"
     
      SHARECNT=`syscfg get MedFolderCount`
      if [ $SHARECNT -lt 1 ] ; then
        
        drive_count=`sysevent get no_usb_drives`
        if [ "$drive_count" == "" ] || [ "$drive_count" == "0" ] ; then
            echo "no storage drives attached"
            exit
        fi
        
        OTHERDRIVES=$(get_all_media_drives)
        contentdir=""
        for hdd in $OTHERDRIVES
        do
       	if [ "$contentdir" == "" ] ; then
            contentdir="+A|/mnt/$hdd"
	else
            contentdir="+A|/mnt/$hdd,$contentdir"
	fi
        done
        if [ "$contentdir" == "" ] ; then
          contentdir="+A|/"
        fi
      else
        contentdir=""
        for ct in `seq 1 $SHARECNT`
        do
          NAMESPACE="med_${ct}"
          NAME=`syscfg get $NAMESPACE name`
          FOLDER=`syscfg get $NAMESPACE folder`
          DRIVE=`syscfg get $NAMESPACE drive`
          READONLY=`syscfg get $NAMESPACE readonly`
          if [ $ct -eq 1 ] ; then
            contentdir="+A|/mnt/$DRIVE$FOLDER"
          else
            contentdir="+A|/mnt/$DRIVE$FOLDER,$contentdir"
          fi
          echo "" >> $LOG_FILE
          echo "media share $ct" >> $LOG_FILE
          echo " Drive: $DRIVE" >> $LOG_FILE
          echo "FOLDER: $FOLDER" >> $LOG_FILE
        done
      fi
     
     STATUS=`sysevent get ${SERVICE_NAME}-status`
     if [ "started" != "$STATUS" ] ; then
        sysevent set ${SERVICE_NAME}-errinfo 
        sysevent set ${SERVICE_NAME}-status starting
        
        echo "Starting ${SERVICE_NAME}"
        cp /etc/mediaserver.ini $CFGFILE
        sed -i "s%-BASEDIR-%%g" $CFGFILE
        sed -i "s%=/mnt/%=%g" $CFGFILE
        sed -i "s%-CONTENTDIR-%$contentdir%g" $CFGFILE
        sed -i "s%-ENABLEWEB-%$TMS_WEBENA%g" $CFGFILE
        sed -i "s%-SERVERNAME-%$TMS_NAME%g" $CFGFILE
        sed -i "s%-SCANTIME-%$TMS_SCANTIME%g" $CFGFILE
  ulimit -v 60000
	ln -sf $WORKDIR1/mediawatcher.sh /etc/cron/cron.daily/                                      
        
	bridge_mode=`syscfg get bridge_mode`					
	if [ "$bridge_mode" = 0 ]; then						
		serv_loc_addr=`syscfg get lan_ipaddr`				
	else									
		serv_loc_addr=`sysevent get ipv4_wan_ipaddr`			
	fi						
        export TZ=`sysevent get TZ`       
        "$TWONKYSRV" -D -contentbase "/" -contentdir "$contentdir" -cachedir "$DBDIR/cache" -dbdir "$DBDIR/.tmp" -appdata "${APPDATA}"   \
        -inifile $CFGFILE -httpport $TMS_PORT -ignoredir ".tmp" -enableweb $TMS_WEBENA -rmautoshare 0 -friendlyname "$TMS_NAME" \
        -scantime $TMS_SCANTIME -uploadenabled 0 -rmautoshare 0 -rmhomedrive "/mnt" -maxitems $TMS_MAXITEMS -ip "$serv_loc_addr" \
	-v $TMS_V -vlevel $TMS_VLEVEL \
	-followlinks 0 \
	-disablefrontends 8 \
        -cachemaxsize 2048 -stack_size 320000 -logfile "/mnt/$DEFDRIVE/.tmp/twonky.log"
        check_err $? "Couldnt handle start"
        sysevent set ${SERVICE_NAME}-status started
        if [ -f "/mnt/$DEFDRIVE/.tmp/db.info" ] ; then
          LAST_SCAN_TIME=`cat /mnt/$DEFDRIVE/.tmp/db.info | head -n 1 | sed 's/t://g'`
        else
          LAST_SCAN_TIME="Not Available"
        fi
        syscfg set last_scan_time_file "/mnt/$DEFDRIVE/.tmp/db.info"
        syscfg set last_scan_time "$LAST_SCAN_TIME"
     fi
   else
      echo "${SERVICE_NAME} disabled in configuration"
      echo "please use:"
      echo "syscfg set media_server_enabled 1"
      echo "to enable the server"
   fi
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" != "$STATUS" ] ; then
      sysevent set ${SERVICE_NAME}-errinfo 
      sysevent set ${SERVICE_NAME}-status stopping
      echo "Stopping ${SERVICE_NAME} ... "
      killall -s TERM $DAEMON
      check_err $? "Couldnt handle stop"
      syscfg set last_scan_time "Not Available"
      sysevent set ${SERVICE_NAME}-status stopped
      rm -rf /twonkymedia
   fi
}
service_init 
if [ -x "$WORKDIR1" ]; then
WORKDIR="$WORKDIR1"
else
WORKDIR="$WORKDIR2"
fi
cd $WORKDIR
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
   ${SERVICE_NAME}-rescan)
      echo "${SERVICE_NAME} rescan contentdir"
      wget http://`syscfg get lan_ipaddr`:9999/rpc/rescan -O /dev/null
      ;;
   *)
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
