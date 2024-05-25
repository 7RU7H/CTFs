#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/user_functions.sh
source /etc/init.d/usb_functions.sh
SERVICE_NAME="file_sharing"
SELF_NAME="`basename $0`"
EVENT_NAME=$1
pre_init () 
{
  S_NAME=`syscfg get smb_server_name`
  if [ "$S_NAME" == "" ] || [ "$S_NAME" == "(none)" ]; then
    if [ `hostname` != "(none)" ] ; then
    syscfg set smb_server_name `hostname`
    fi
  fi
  S_NAME=`syscfg get ftp_server_name`
  if [ "$S_NAME" == "" ] || [ "$S_NAME" == "(none)" ]; then
    if [ `hostname` != "(none)" ] ; then
    syscfg set ftp_server_name `hostname`
    fi
  fi
  S_NAME=`syscfg get MediaServer::name`
  if [ "$S_NAME" == "" ] || [ "$S_NAME" == "(none)" ]; then
    if [ `hostname` != "(none)" ] ; then
    syscfg set MediaServer::name `hostname`
    fi
  fi
}
service_init ()
{
  if [ ! -d "/tmp/mnt" ] ; then
    mkdir -p /tmp/mnt
  fi
  create_user_group_entries
  sync_file_users
}
check_mounted_usb_drives()
{
  mount | grep -q "sd[a-z]"
  if [ "0" = "$?" ] ; then
    return
  fi
  DEVS=`ls /dev/ | grep "sd[a-z][0-9]"`
  
  for d in $DEVS 
  do
    if Hotplug_IsDeviceStorage $d ; then
      Hotplug_GetId $d
      ulog usb autodetect "$PID check_mounted_usb_drives: $d on $DEVICE_PORT"
      `$STORAGE_DEVICE_SCRIPT add $d $DEVICE_PORT`
      sleep 1
    fi
  done
}
service_start ()
{
   FIRST_START=`sysevent get file-sharing-first-start`
   BRIDGE_MODE=`syscfg get bridge_mode`
   if [ "$FIRST_START" != "" ] || [ "$BRIDGE_MODE" == "1" ] ; then
     pre_init
     USB_COUNT=`sysevent get no_usb_drives`
     if [ ! "$USB_COUNT" ] ; then
        USB_COUNT=0
     fi
     if [ $USB_COUNT -lt 1 ] ; then
       SMB_ENA=`syscfg get smb_enabled`
       FTP_ENA=0
       MED_ENA=0
       SERVICES=0
     else
       service_init
       echo "$USB_COUNT usb storage drives detected..."
       if [ $USB_COUNT -gt 0 ] ; then
        check_mounted_usb_drives
       fi
       
       SMB_ENA=`syscfg get smb_enabled`
       FTP_ENA=`syscfg get ftp_enabled`
       MED_ENA=`syscfg get MediaServer::mediaServerEnable`
       FTP_ENA="1"
     fi
    
     if [ "$SMB_ENA" == "1" ] ; then
        /etc/init.d/service_smbd.sh smbd-start
     fi
     if [ "$FTP_ENA" == "1"  ] ; then
        /etc/init.d/service_vsftpd.sh vsftpd-start
     fi
     if [ "$MED_ENA" == "1"  ] && [ -f "/etc/init.d/service_mediaserver.sh" ] ; then
        if [ "$EVENT_NAME" = "dns-restart" ] ; then
            /etc/init.d/service_mediaserver.sh dns-restart
        else
            /etc/init.d/service_mediaserver.sh mediaserver-restart
        fi
     fi
     sysevent set file_sharing-status started
     sysevent set file-sharing-first-start "OK"
  else
    /etc/init.d/share_parser.sh
    sleep 40
    sysevent set file-sharing-first-start "OK"
    sysevent set file_sharing-restart
  fi
}
service_stop ()
{
  if [ -f "/etc/init.d/service_mediaserver.sh" ] ; then
    if [ "$EVENT_NAME" != "dns-restart" ] ; then
        /etc/init.d/service_mediaserver.sh mediaserver-stop
    fi
  fi
  /etc/init.d/service_vsftpd.sh vsftpd-stop
  /etc/init.d/service_smbd.sh smbd-stop
  sysevent set file_sharing-status stopped
  /etc/init.d/share_parser.sh
}
umount_unused_devices()
{
  ulog sharing "umount unused devices"
  mount | grep "sd[a-z].*" | sed -r "s/.* on (.*) type .*/\1/g" | sed "s|.040(deleted)||g" | while read file;
  do
    ulog sharing "umount unused devices: unmounting $file"
    dev=`echo $file | awk -F '/' '{print $NF}'`
    tmp_dev="/tmp/$dev"
    ulog sharing "umount unused devices: file=$file, tmp_dev=$tmp_dev"
    if [ "$file" != "$tmp_dev" ] ; then
      if [ -d $tmp_dev ] ; then
        ulog sharing "umount unused devices: Do not umount $file"
      else
        ulog sharing "umount unused devices: umount $file"
        umount "$file"
        rc=$?
        sync
        if [ "$rc" != "0" ] ; then 
          ulog sharing "umount unused devices: ERROR unmount $file"
          umount "$file"
        fi
        ulog sharing "umount unused devices: rmdir $file"
        rmdir "$file"
        if [ -d "$file" ] ; then
          if [ -z "$(ls -A $file)" ] ; then
            ulog sharing "umount unused devices: Found empty dir = $file and remove dir"
            rmdir "$file"
          fi
        fi        
      fi
    fi
  done
}
SHOULD_I_START=`sysevent get lan-status`
if [ "$SHOULD_I_START" == "started" ] ;  then
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
  mount_usb_drives)
    USTATE=`sysevent get no_usb_drives`                                     
    if [ $USTATE -gt 0 ]; then
      CURSTAT=`sysevent get file_sharing-status`
      if [ "$CURSTAT" != "starting" ] ; then
        service_stop
        service_start
      else
        echo "waiting for startup complete..."
      fi
    else
      echo "stopping file sharing due to USB complete removal"
      service_stop
      sysevent set smbd-start
    fi
    ;;
  remove_usb_drives)
    echo "receives remove_usb_drives event" > /dev/console
    CURSTAT=`sysevent get file_sharing-status`
    if [ "$CURSTAT" != "starting" ] ; then
      service_stop
      USTATE=`sysevent get no_usb_drives`
      if [ $USTATE -gt 0 ] ; then
        service_start
      else
        umount_unused_devices
      fi
    else
      echo "waiting for startup complete..." > /dev/console
    fi
    ;;
  fuadmin_pass)
    service_stop
    service_start
    ;;
  hostname)
    ;;
  dns-restart)
    HN=`syscfg get hostname`
    syscfg set ftp_server_name "$HN"
    syscfg set smb_server_name "$HN"
    syscfg set MediaServer::name "$HN"
    syscfg commit
    service_stop
    service_start
    ;;
  fwup_state)
    fwup_updating $3 && service_stop
    ;;
  *)
    echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
    exit 3
    ;;
esac
fi
