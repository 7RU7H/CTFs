#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/user_functions.sh
FTP_SERVER="/usr/local/sbin/vsftpd"
FTP_WELCOME_MSG="FTP Server"
FTP_CONF_FILE="/tmp/etc/.root/vsftpd.conf"
TMP_LOG="/tmp/service_vsftpd.log"
POSIX_PASS_FILE="/tmp/etc/.root/passwd"
POSIX_SHAD_FILE="/tmp/etc/.root/shadow"
POSIX_GROUP_FILE="/tmp/etc/.root/group"
MNT_DIR="/tmp/ftp"
if [ ! -d "$MNT_DIR" ] ; then
  mkdir -p "$MNT_DIR"
fi
MNT_DIR_ADMIN="$MNT_DIR/admin_mnt"
MNT_DIR_GUEST="$MNT_DIR/guest_mnt"
echo "" > "$TMP_LOG"
SERVICE_NAME="vsftpd"
get_first_media_drive() 
{
  drive=`ls /mnt/ | grep sd | sort | head -n 1`
  if [ "$drive" ] ; then
    echo "$drive"
  fi
}
get_all_media_drives() 
{
  drive=`ls /mnt/ | grep sd | sort`
  if [ "$drive" ] ; then
    echo "$drive"
  fi
}
is_shared_drive()
{
  SHARED_CNT=`syscfg get SharedFolderCount`
  
  [ -z "$SHARED_CNT" ] && return 1
  if [ $SHARED_CNT -gt 0 ] ; then
    for ct in `seq 1 $SHARED_CNT`
    do
      NAMESPACE="ftp_$ct"
      if [ "" != "$NAMESPACE" ] ; then
        DRIVE=`syscfg get $NAMESPACE drive`
        if [ "$DRIVE" = "$1" ] ; then
          return 0
        fi
      fi
    done
  fi
  
  return 1
}
mount_anon_folders () 
{
  ulog vsftpd "mount anon ftp folders"
  chmod 755 "$MNT_DIR/anonymous"
  
  ftp_fldr_cnt=`syscfg get FTPFolderCount`
  if [ "$ftp_fldr_cnt" -gt "0" ] ; then
    ulog vsftpd "mount anon ftp folders using shared folders info" 
    for i in `seq 1 $ftp_fldr_cnt`
    do
      fldr_name=`syscfg get ftp_${i} name`
      fldr_drive=`syscfg get ftp_${i} drive`
      fldr_path=`syscfg get ftp_${i} folder`
      if [ -d "/mnt/$fldr_drive/$fldr_path" ] ; then
        mkdir "$MNT_DIR/anonymous/$fldr_name"
        mount -o umask=002 "/mnt/$fldr_drive/$fldr_path" "$MNT_DIR/anonymous/$fldr_name" -o bind
        chmod 775 "$MNT_DIR/anonymous/$fldr_name"
      else
        echo "could not find path /mnt/$fldr_drive/$fldr_path" >> $TMP_LOG
      fi
    done
  fi
  ulog vsftpd "mount anon ftp folders unsing default USB drives"
  DRIVES=`ls /mnt/ | grep -r "sd[a-z][0-9.]"`
  i="1"
  for d in $DRIVES
  do
    if is_shared_drive $d ; then
      ulog vsftpd "mount_anon: shared_drive $d"
    else
      dlabel=`usblabel $d`
      if [ "$dlabel" == "" ] ; then
        dlabel="$d"
      else
        if [ -d "$MNT_DIR/anonymous/$dlabel" ] ; then
          labelcnt=`mount | grep "$MNT_DIR/anonymous/" | sed -r "s/^.* on (.*) type .*/\1/g" | sed 's/\\\\040/ /g' | grep "$dlabel" | wc -l`
          if [ $labelcnt -gt 0 ] && [ 20 -gt $labelcnt ] ; then
            anonext="($labelcnt)"
          else
            anonext=""
          fi 
          dlabel="$dlabel$anonext"
        else
          echo "could not find path $MNT_DIR/anonymous/$dlabel" >> $TMP_LOG
        fi
      fi
      ulog vsftpd "ftp anon drive label = $dlabel"
      if [ -d "/mnt/$d" ] ; then
        mkdir "$MNT_DIR/anonymous/$dlabel"
        mount -o umask=002 /mnt/$d "$MNT_DIR/anonymous/$dlabel" -o bind
        chmod 775 "$MNT_DIR/anonymous/$dlabel"
        i=`expr $i + 1`
      else
        echo "could not find path /mnt/$d" >> $TMP_LOG
      fi
    fi
  done
}
unmount_anon_folders () 
{
  ulog vsftpd "unmount anon ftp folders"
  
  if [ -d "$MNT_DIR/anonymous" ] ; then
    DRIVES=`ls $MNT_DIR/anonymous`
    mount | grep "$MNT_DIR/anonymous" | sed -r "s/^.* on (.*) type .*/\1/g" | sed "s|\040| |g" | while read file;
    do
      echo "vsftpd - unmounting $file" >> $TMP_LOG
    
      ulog vsftpd "unmounting $file"
    
      umount "$file"
      sync
      success=`mount | grep "$file "`
      if [ "$success" = "" ] ; then
        ulog vsftpd "unmounted $file, removing folder $file"
        rmdir "$file"
      fi
    done
    mount | grep "$MNT_DIR/anonymous" | sed -r "s/^.* on (.*) type .*/\1/g" | sed "s|\040| |g" | sed "s/ (deleted)//g" | while read file;
    do
      ulog vsftpd "unmount ftp anon shares: $file" 
      umount "$file"
      sync
      rmdir "$file"
    done
  fi
}
add_anon_ftp_user () 
{
  mkdir -p $MNT_DIR/anonymous
  echo "anonymous::505:505:file_guest:$MNT_DIR/anonymous:/bin/sh" >> $POSIX_PASS_FILE
  sed -i "s/file_guest:x:1004:/file_guest:x:1004:anonymous,/g" $POSIX_GROUP_FILE
  mount_anon_folders
}
del_anon_ftp_user () 
{
  sed -i "/anonymous:.*$/d" $POSIX_PASS_FILE
  sed -i "/anonymous:.*$/d" $POSIX_SHAD_FILE
  sed -i "s/anonymous,//g" $POSIX_GROUP_FILE
  unmount_anon_folders
  rmdir $MNT_DIR/anonymous
}
create_vsftpd_conf () 
{
  echo "root" > /tmp/.ftpusers
  echo "create_vsftp_conf:" >> $TMP_LOG
  FTP_PORT=`syscfg get ftp_port`
  if [ "$FTP_PORT" != "21" ] ; then
      FTP_PORT2="21,$FTP_PORT"
  fi
  echo "insmod /lib/modules/`uname -r`/nf_conntrack_ftp.ko ports=$FTP_PORT2" >> $TMP_LOG
  rmmod nf_nat_ftp
  rmmod nf_conntrack_ftp
  if [ "$FTP_PORT" != "21" ] ; then
    insmod /lib/modules/`uname -r`/nf_conntrack_ftp.ko ports="$FTP_PORT2"
  else
    insmod /lib/modules/`uname -r`/nf_conntrack_ftp.ko
  fi
  insmod /lib/modules/`uname -r`/nf_nat_ftp.ko
  FTP_ENCODING=`syscfg get ftp_encoding`
  FTP_ANON=`syscfg get ftp_anon_enabled`
  FTP_NAME=`syscfg get ftp_server_name`
  FTP_CHARSET=`syscfg get ftp_encoding`
  if [ ! "$FTP_NAME" ] ; then
    FTP_NAME=`syscfg get hostname`
  fi
  if [ ! -f "$FTP_CONF_FILE" ] ; then
    CfgDir=`dirname "$FTP_CONF_FILE"`
    if [ ! -d "$CfgDir" ] ; then
       mkdir -p "$CfgDir"
    fi
  fi
  echo "# this file was auto-generated and will be overwritten" > $FTP_CONF_FILE
  if [ $FTP_ANON -gt 0 ] ; then
    echo "anonymous_enable=NO" >> $FTP_CONF_FILE
    echo "anon_world_readable_only=YES" >> $FTP_CONF_FILE
    echo "anon_mkdir_write_enable=NO" >> $FTP_CONF_FILE 
    echo "anon_upload_enable=YES" >> $FTP_CONF_FILE  
    echo "anon_root=$MNT_DIR" >> $FTP_CONF_FILE
    echo "local_umask=0111" >> $FTP_CONF_FILE
    echo "anon_umask=002" >> $FTP_CONF_FILE
    echo "chroot_local_user=YES" >> $FTP_CONF_FILE 
    echo "local_enable=YES" >> $FTP_CONF_FILE 
    echo "local_umask=002" >> $FTP_CONF_FILE
    del_anon_ftp_user
    add_anon_ftp_user
  else
    del_anon_ftp_user
    echo "anonymous_enable=NO" >> $FTP_CONF_FILE
    echo "chroot_local_user=YES" >> $FTP_CONF_FILE 
    echo "local_enable=YES" >> $FTP_CONF_FILE 
    echo "local_umask=002" >> $FTP_CONF_FILE
  fi
  echo "secure_chroot_dir=$MNT_DIR" >> $FTP_CONF_FILE
  echo "listen_port=$FTP_PORT" >> $FTP_CONF_FILE
  echo "nopriv_user=file_admin" >> $FTP_CONF_FILE
  echo "dirmessage_enable=YES" >> $FTP_CONF_FILE 
  echo "syslog_enable=YES" >> $FTP_CONF_FILE 
  echo "ftp_username=file_admin" >> $FTP_CONF_FILE 
  echo "connect_from_port_20=NO" >> $FTP_CONF_FILE 
  echo "pam_service_name=vsftpd" >> $FTP_CONF_FILE 
  echo "ssl_enable=NO" >> $FTP_CONF_FILE  
  echo "userlist_enable=YES" >> $FTP_CONF_FILE 
  echo "userlist_file=/tmp/.ftpusers" >> $FTP_CONF_FILE 
  echo "write_enable=YES" >> $FTP_CONF_FILE
  echo "ftpd_banner=Welcome to $FTP_NAME" >> $FTP_CONF_FILE 
  echo "log_ftp_protocol=NO" >> $FTP_CONF_FILE 
  echo "idle_session_timeout=120" >> $FTP_CONF_FILE
  echo "data_connection_timeout=300" >> $FTP_CONF_FILE
  echo "accept_timeout=60" >> $FTP_CONF_FILE
  echo "connect_timeout=60" >> $FTP_CONF_FILE
  echo "max_clients=20" >> $FTP_CONF_FILE 
  echo "pasv_enable=YES" >> $FTP_CONF_FILE 
  echo "pasv_max_port=40500" >> $FTP_CONF_FILE 
  echo "pasv_min_port=40000" >> $FTP_CONF_FILE 
  if [ "$FTP_CHARSET" ] ; then
    if [ $FTP_CHARSET -eq 2 ] ; then
      FTP_CHARSET=3
    elif [ $FTP_CHARSET -eq 3 ] ; then
      FTP_CHARSET=4
    fi
    echo "ftp_characterset=$FTP_CHARSET"  >> $FTP_CONF_FILE
  fi
  echo "listen_ipv6=YES" >> $FTP_CONF_FILE
  ulog vsftpd "$FTP_CONF_FILE created"
}
create_group_mount_points () 
{
  echo "create_mount_points:" >> $TMP_LOG
  if [ ! -d "$MNT_DIR_ADMIN" ] ; then
    mkdir -p "$MNT_DIR_ADMIN"
    chmod 755 "$MNT_DIR_ADMIN"
  fi
  if [ ! -d "$MNT_DIR_GUEST" ] ; then
    mkdir -p "$MNT_DIR_GUEST"
    chmod 755 "$MNT_DIR_GUEST"
  fi
  GCOUNT=`syscfg get group_count`
  
  echo "creating mount points for $GCOUNT groups" >> $TMP_LOG
  for i in `seq 1 $GCOUNT`
  do
    GNAME=`syscfg get group_${i}_name`
    GPERM=`syscfg get group_${i}_perms`
    
    if [ "$GNAME" ] && [ "$GPERM" ] ; then
      if [ "$GPERM" == "file_admin" ] ; then
        if [ "$GNAME" != "admin" ] ; then
          if [ ! -d "$MNT_DIR/$GNAME" ] ; then
            echo "creating mount point $i - $MNT_DIR/$GNAME (admin group)" >> $TMP_LOG
            mkdir "$MNT_DIR/$GNAME"
            chmod 755 "$MNT_DIR/$GNAME"
          else
            echo "could not find path $MNT_DIR/$GNAME" >> $TMP_LOG
          fi
        fi
      elif [ "$GPERM" == "file_guest" ] ; then
        if [ "$GNAME" != "guest" ] ; then
          if [ ! -d "$MNT_DIR/$GNAME" ] ; then
            echo "creating mount point $i - $MNT_DIR/$GNAME (guest group)" >> $TMP_LOG
            mkdir "$MNT_DIR/$GNAME"
            chmod 755 "$MNT_DIR/$GNAME"
          else
            echo "could not find path $MNT_DIR/$GNAME" >> $TMP_LOG
          fi
        fi
      fi
    fi
  done
}
remove_group_mount_points () 
{
  echo "remove_mount_points:" >> $TMP_LOG
  GCOUNT=`syscfg get group_count`
  for i in `seq 1 $GCOUNT`
  do
    GNAME=`syscfg get group_${i}_name`
      if [ "$GNAME" != "admin" ] && [ "$GNAME" != "guest" ] ; then
      echo "removing mount point $i - $MNT_DIR/$GNAME" >> $TMP_LOG
      around=`mount | grep "$MNT_DIR/$GNAME"`
      if [ "$around" != "" ] ; then
        echo "disk not unmounted : $around"  >> $TMP_LOG
      else 
        echo "removing mount point $i - $MNT_DIR/$GNAME" >> $TMP_LOG
        if [ "$GNAME" != "admin" ] && [ "$GNAME" != "guest" ] ; then
          rmdir "$MNT_DIR/$GNAME"
        fi
      fi
    fi
  done
}
DEFAULT_SHARE_NAME_CONVENTION="device"
prod_name=`syscfg get device modelNumber`
mount_ftp_shares() 
{
  ulog vsftpd "mount ftp shares"
  echo "mount_ftp_shares:" >> $TMP_LOG
  COUNT=`syscfg get FTPFolderCount`
  [ -z "$COUNT" ] && COUNT=0
  if [ $COUNT -gt 0 ] ; then
    ulog vsftpd "mount ftp folders using shared folders info"
    echo "$COUNT ftp shares configured" >> $TMP_LOG
    for ct in `seq 1 $COUNT`
    do
      NAMESPACE="ftp_$ct"
      NAME=`syscfg get $NAMESPACE name`
      FOLDER=`syscfg get $NAMESPACE folder`
      DRIVE=`syscfg get $NAMESPACE drive`
      READONLY=`syscfg get $NAMESPACE readonly`
      GROUPS=`syscfg get $NAMESPACE groups | sed 's/,/ /g'`
      
      echo "" >> $TMP_LOG
      echo "FtpFolder_${ct} : $NAMESPACE" >> $TMP_LOG  
      echo "  name   : $NAME" >> $TMP_LOG  
      echo "  folder : $FOLDER" >> $TMP_LOG  
      echo "  drive  : $DRIVE" >> $TMP_LOG  
      echo "  ro     : $READONLY" >> $TMP_LOG
      echo "  groups : $GROUPS" >> $TMP_LOG
      
      for g in $GROUPS
      do
        if [ "$g" != "admin" ] && [ "$g" != "guest" ] ; then
          echo "checking for $MNT_DIR/$g" >> $TMP_LOG
          if [ -d "$MNT_DIR/$g" ] ; then
            if [ -d "/mnt/$DRIVE$FOLDER" ] ; then
              echo "creating $MNT_DIR/$g/$NAME" >> $TMP_LOG
              mkdir -p "$MNT_DIR/$g/$NAME"
              adjust_perms=$(get_group_perms "$g")
              echo "mounting $MNT_DIR/$g/$NAME has $adjust_perms permissions" >> $TMP_LOG
              echo "mounting /mnt/$DRIVE$FOLDER on $MNT_DIR/$g/$NAME" >> $TMP_LOG
              mount -o umask=002 "/mnt/$DRIVE$FOLDER" "$MNT_DIR/$g/$NAME" -o bind
              if [ "$adjust_perms" == "file_guest" ] ; then
                chmod 775 "$MNT_DIR/$g/$NAME"
              fi
            else
              echo "could not find path /mnt/$DRIVE$FOLDER" >> $TMP_LOG
            fi
          fi
        else
          if [ -d "/mnt/$DRIVE$FOLDER" ] ; then
            if [ -d "/mnt/$DRIVE$FOLDER" ] ; then
              if [ "$g" = "admin" ] ; then
                mkdir -p "$MNT_DIR_ADMIN/$NAME"
                echo "mounting /mnt/$DRIVE$FOLDER on $MNT_DIR_ADMIN/$NAME" >> $TMP_LOG
                mount -o umask=002 "/mnt/$DRIVE$FOLDER" "$MNT_DIR_ADMIN/$NAME" -o bind
              fi          
              if [ "$g" = "guest" ] ; then
                mkdir -p "$MNT_DIR_GUEST/$NAME"
                echo "mounting /mnt/$DRIVE$FOLDER on $MNT_DIR_GUEST/$NAME" >> $TMP_LOG
                mount -o umask=002 "/mnt/$DRIVE$FOLDER" "$MNT_DIR_GUEST/$NAME" -o bind
              fi
            else
              echo "could not find path /mnt/$DRIVE$FOLDER" >> $TMP_LOG
            fi
          fi
        fi
      done
    done
  fi
  ulog vsftpd "mount ftp folders using default USB drives"
  DRIVES=$(get_all_media_drives)
  dcount=1
  labelcnt=0
  for d in $DRIVES
  do
    if is_shared_drive $d ; then
      ulog vsftpd "mount_ftp: shared_drive $d"
    else 
      usb_label=`usblabel $d`
      if [ "$usb_label" ] ; then
        if [ -d "$MNT_DIR_ADMIN/$usb_label" ] ; then
          labelcnt=`mount | grep "$MNT_DIR_ADMIN/" | sed -r "s/^.* on (.*) type .*/\1/g" | sed 's/\\\\040/ /g' |grep "$usb_label" | wc -l`
          if [ $labelcnt -gt 0 ] && [ 20 -gt $labelcnt ] ; then
            anonext="($labelcnt)"
          else
            anonext=""
          fi
          if [ -d "/tmp/$d" ] ; then
            mkdir -p "$MNT_DIR_ADMIN/$usb_label$anonext"
            echo "mounting /tmp/$d on $MNT_DIR_ADMIN/$usb_label$anonext" >> $TMP_LOG
            mount -o umask=002 "/tmp/$d" "$MNT_DIR_ADMIN/$usb_label$anonext" -o bind
          else
            echo "could not find path /tmp/$d" >> $TMP_LOG
          fi
        else
          echo "could not find path $MNT_DIR_ADMIN/$usb_label" >> $TMP_LOG
          if [ -d "/tmp/$d" ] ; then
            mkdir -p "$MNT_DIR_ADMIN/$usb_label"
            echo "mounting /tmp/$d on $MNT_DIR_ADMIN/$usb_label" >> $TMP_LOG
            mount -o umask=002 "/tmp/$d" "$MNT_DIR_ADMIN/$usb_label" -o bind
          else
            echo "could not find path /tmp/$d" >> $TMP_LOG
          fi
        fi
      else
        if [ -d "/tmp/$d" ] ; then
        mkdir -p "$MNT_DIR_ADMIN/$d"
        echo "mounting /tmp/$d on $MNT_DIR_ADMIN/$d" >> $TMP_LOG
        mount -o umask=002 "/tmp/$d" "$MNT_DIR_ADMIN/$d" -o bind
        else
            echo "could not find path /tmp/$d" >> $TMP_LOG
        fi
      fi
    fi
    dcount=`expr $dcount + 1`
  done
}
umount_ftp_shares() 
{
  ulog vsftpd "unmount ftp shares"
  echo "umount_ftp_shares:" >> $TMP_LOG
  mount | grep "$MNT_DIR" | sed -r "s/^.* on (.*) type .*/\1/g" | sed "s|\040| |g" | while read file;
  do
    echo "vsftpd - unmounting $file" >> $TMP_LOG
  
    ulog vsftpd "unmounting $file"
  
    umount "$file"
    sync
    success=`mount | grep "$file "`
    if [ "$success" = "" ] ; then
      echo "vsftpd - unmounted $file, removing folder $file" >> $TMP_LOG
      ulog vsftpd "unmounted $file, removing folder $file"
      rmdir "$file"
    fi
    if [ -d "$file" ] ; then
      if [ -z "$(ls -A $file)" ] ; then
          ulog vsftpd "Found empty dir = $file and remove dir"
          rmdir "$file"
      fi
    fi
  done
  sync
  mount | grep "$MNT_DIR" | sed -r "s/^.* on (.*) type .*/\1/g" | sed "s|\040| |g" | sed "s/ (deleted)//g" | while read file;
  do
    ulog vsftpd "unmount ftp shares: $file" 
    umount "$file"
    sync
    rmdir "$file"
  done
}
service_init ()
{
  umount_ftp_shares
  unmount_anon_folders
  create_group_mount_points
  mount_ftp_shares
  create_vsftpd_conf
}
service_pre_halt ()
{
  umount_ftp_shares
  unmount_anon_folders
  remove_group_mount_points
  rmmod nf_nat_ftp
  rmmod nf_conntrack_ftp
  insmod /lib/modules/`uname -r`/nf_conntrack_ftp.ko
  insmod /lib/modules/`uname -r`/nf_nat_ftp.ko
}
service_start ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] ; then
      service_init
      FTP_ENA=`syscfg get ftp_enabled`
      if [ "$FTP_ENA" == "1" ] ; then
        sysevent set ${SERVICE_NAME}-errinfo 
        sysevent set ${SERVICE_NAME}-status starting
        echo "Starting ${SERVICE_NAME} ... "
        $FTP_SERVER &
        
        check_err $? "Couldnt handle start"
        sysevent set ${SERVICE_NAME}-status started
      fi
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
    
    killall -s TERM vsftpd
    service_pre_halt
    
    check_err $? "Couldnt handle stop"
    sysevent set ${SERVICE_NAME}-status stopped
  fi
}
case "$1" in
  ${SERVICE_NAME}-start)
    service_start
    restart_count=`sysevent get vsftpd_restart_count`
    if [ "$restart_count" == "" ] ; then
      restart_count=1
      sysevent set vsftpd_restart_count "$restart_count"
    else
      sysevent set vsftpd_restart_count `expr $restart_count + 1`
    fi
    restart_count=`sysevent get vsftpd_restart_count`
    echo " --- vsftpd restarted $restart_count times ---" >> /dev/console
    /etc/init.d/share_parser.sh
    ;;
  ${SERVICE_NAME}-stop)
    service_stop
    ;;
  ${SERVICE_NAME}-restart)
    service_stop
    service_start
    ;;
  *)
    echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
    exit 3
    ;;
esac
