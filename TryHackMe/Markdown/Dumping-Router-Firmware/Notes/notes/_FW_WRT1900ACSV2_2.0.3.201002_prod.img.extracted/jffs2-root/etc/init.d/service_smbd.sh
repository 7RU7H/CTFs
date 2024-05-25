#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/user_functions.sh
SMB_SERVER="/sbin/smbd"
NMB_SERVER="/sbin/nmbd"
SAMBA_CONF_FILE="/tmp/samba/smb.conf"
SAMBA_PASS_FILE="/tmp/samba/smbpasswd"
ANON_SMB_DIR="/tmp/anon_smb"
TMP_LOG="/tmp/service_smbd.log"
USE_DISK_PERSISTENCE="yes"
DEFAULT_SHARE_NAME_CONVENTION="partition"
if [ ! -d "$ANON_SMB_DIR" ] ; then
  mkdir -p "$ANON_SMB_DIR"
fi
MNT_DIR="/tmp/ftp"
if [ ! -d "$MNT_DIR" ] ; then
  mkdir -p "$MNT_DIR"
fi
echo "" > "$TMP_LOG"
get_group_perms() 
{
  gns=`syscfg show | grep group | grep _name=$1 | cut -d'_' -f1,2`
  gperms=`syscfg get ${gns}_perms`
  echo "$gperms"
}
prod_name=`syscfg get device model_base`
if [ ! "$prod_name" ] ; then
  prod_name=`syscfg get device modelNumber`
fi
SERVICE_NAME="smbd"
get_first_media_drive() 
{
	if [ -d "/mnt" ] ; then
		drive=`ls /mnt/ | grep sd | sort | head -n 1`
		if [ "$drive" ] ; then
			echo "$drive"
		fi
  fi
}
get_all_media_drives() 
{
	if [ -d "/mnt" ] ; then
		drive=`ls /mnt/ | grep sd | sort`
		if [ "$drive" ] ; then
			echo "$drive"
		fi
  fi
}
load_shares_from_disk() 
{
  CHECK_DRIVES=$(get_all_media_drives)
  for D in $CHECK_DRIVES
  do
    if [ -f "/mnt/$D/.smb_share.nfo" ] ; then
      ulog smbd "found SMB share config on drive $D"
      CFG=`cat "/mnt/$D/.smb_share.nfo" | sed "s/+DRIVE+/$D/g"`
      echo "$CFG"
      /etc/init.d/share_parser.sh
    else 
      ulog smbd "no SMB share config found on drive $D"
    fi
  done
}
is_shared_drive()
{
  SHARED_CNT=`syscfg get SharedFolderCount`
  
  [ -z "$SHARED_CNT" ] && return 1
  if [ $SHARED_CNT -gt 0 ] ; then
    for ct in `seq 1 $SHARED_CNT`
    do
      NAMESPACE="smb_$ct"
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
mount_samba_shares()
{
  ulog smbd "mount samba shares"
  echo "mount_samba_shares:" >> $TMP_LOG
  COUNT=`syscfg get SharedFolderCount`
  ANON_ACCESS=`syscfg get SharedFolderAnonEna`
  [ -z "$COUNT" ] && COUNT=0
  if [ $COUNT -gt 0 ] ; then
    ulog smbd "mount samba folders using shared folders info"
    echo "$COUNT samba shares configured" >> $TMP_LOG
    for ct in `seq 1 $COUNT`
    do
      NAMESPACE="smb_$ct"
      if [ "" != "$NAMESPACE" ] ; then
        NAME=`syscfg get $NAMESPACE name`
        FOLDER=`syscfg get $NAMESPACE folder`
        DRIVE=`syscfg get $NAMESPACE drive`
        READONLY=`syscfg get $NAMESPACE readonly`
        GROUPS=`syscfg get $NAMESPACE groups | sed 's/,/ /g'`
                
        echo "" >> $TMP_LOG
        echo "SharedFolder_${ct}: $NAMESPACE" >> $TMP_LOG
        echo " name   : $NAME" >> $TMP_LOG
        echo " folder : .$FOLDER." >> $TMP_LOG
        echo " drive  : $DRIVE" >> $TMP_LOG
        echo " ro     : $READONLY" >> $TMP_LOG
        echo " groups : $GROUPS" >> $TMP_LOG
        echo "attempting to create share $NAME -> /mnt/$DRIVE$FOLDER" >> $TMP_LOG
        share_loc="/mnt/$DRIVE$FOLDER"
        if [ -d "$share_loc" ] ; then
          echo "/mnt/$DRIVE$FOLDER - Exists ( $share_loc )" >> $TMP_LOG
          if [ $ANON_ACCESS -gt 0 ] ; then
            mkdir -p "$ANON_SMB_DIR/$NAME"
            mount -o umask=000 "/mnt/$DRIVE$FOLDER" "$ANON_SMB_DIR/$NAME" -o bind
            chmod 777 "$ANON_SMB_DIR/$NAME"
          fi
        fi
      fi
    done
  fi
  ulog smbd "mount samba folders using default USB drives"
  DRIVES=$(get_all_media_drives)
  dcount=1
  labelcnt=0
  for d in $DRIVES
  do
    if is_shared_drive $d ; then
      ulog smbd "mount: shared_drive $d"
    else
      usb_label=`usblabel $d`
      if [ "$usb_label" != "" ] ; then
        if [ -d "$ANON_SMB_DIR/$usb_label" ] ; then
          labelcnt=`mount | grep "$ANON_SMB_DIR/"  | sed -r "s/^.* on (.*) type .*/\1/g" | sed 's/\\\\040/ /g' | grep "$usb_label" | wc -l`
          if [ $labelcnt -gt 0 ] && [ 20 -gt $labelcnt ] ; then
            anonext="($labelcnt)"
          else
            anonext=""
          fi
          mkdir -p "$ANON_SMB_DIR/$usb_label$anonext"
          echo "mounting /tmp/$d on $ANON_SMB_DIR/$usb_label$anonext" >> $TMP_LOG
          mount -o umask=002 "/tmp/$d" "$ANON_SMB_DIR/$usb_label$anonext" -o bind
        else
          mkdir -p "$ANON_SMB_DIR/$usb_label"
          echo "mounting /tmp/$d on $ANON_SMB_DIR/$usb_label" >> $TMP_LOG
          mount -o umask=002 "/tmp/$d" "$ANON_SMB_DIR/$usb_label" -o bind
        fi
      else
        mkdir -p "$ANON_SMB_DIR/$d"
        echo "mounting /tmp/$d on $ANON_SMB_DIR/--  $d" >> $TMP_LOG
        mount -o umask=002 "/tmp/$d" "$ANON_SMB_DIR/$d" -o bind
      fi
      dcount=`expr $dcount + 1`
    fi    
  done
}
umount_samba_shares()
{
  ulog smbd "umount samba folders"
  
  if [ -d "$ANON_SMB_DIR" ] ; then
    mount | grep "$ANON_SMB_DIR/" | sed -r "s/^.* on (.*) type .*/\1/g" | sed "s|\040| |g" | while read file;
    do
      echo "smb - unmounting $file" >> $TMP_LOG
    
      ulog smbd "unmounting $file"
    
      umount "$file"
      sync
      success=`mount | grep "$file "`
      if [ "$success" == "" ] ; then
        ulog smbd "unmounted $file, removing folder $file"
        rmdir "$file"
      fi
    done
    mount | grep "$ANON_SMB_DIR" | sed -r "s/^.* on (.*) type .*/\1/g" | sed "s|\040| |g" | sed "s/ (deleted)//g" | while read file;
    do
      ulog smbd "unmount samba shares: $file" 
      umount "$file"
      sync
      rmdir "$file"
    done
  else
    mkdir -p "$ANON_SMB_DIR"
  fi
}
create_common_samba_conf()
{
  WORKGROUP=`syscfg get SharedFolderWorkgroup`
  ANON_ACCESS=`syscfg get SharedFolderAnonEna`
  DEVICE_NAME=`hostname`
  if [ ! -f "$SAMBA_CONF_FILE" ] ; then
    CfgDir=`dirname "$SAMBA_CONF_FILE"`
    if [ ! -d "$CfgDir" ] ; then
       mkdir -p "$CfgDir"
    fi
  fi
  echo "# this file was auto-generated and will be overwritten" > $SAMBA_CONF_FILE
  if [ "$WORKGROUP" ] ; then 
    echo "[global]" >> $SAMBA_CONF_FILE
    echo "  netbios name = $DEVICE_NAME" >> $SAMBA_CONF_FILE
    echo "  workgroup = $WORKGROUP" >> $SAMBA_CONF_FILE
    if [ $ANON_ACCESS -gt 0 ] ; then
      echo "  security = share" >> $SAMBA_CONF_FILE
      echo "  guest account = admin" >> $SAMBA_CONF_FILE
    else 
      echo "  security = user" >> $SAMBA_CONF_FILE
      echo "  guest account = guest" >> $SAMBA_CONF_FILE
    fi
    echo "  encrypt passwords = yes" >> $SAMBA_CONF_FILE
    echo "  wins server = " >> $SAMBA_CONF_FILE
    echo "  wins support = no" >> $SAMBA_CONF_FILE
    echo "  preferred master = auto" >> $SAMBA_CONF_FILE
    echo "  domain master = auto" >> $SAMBA_CONF_FILE
    echo "  local master = yes" >> $SAMBA_CONF_FILE
    echo "  domain logons = no" >> $SAMBA_CONF_FILE
    echo "  os level = 65" >> $SAMBA_CONF_FILE
    echo "  passdb backend = smbpasswd:$SAMBA_PASS_FILE" >> $SAMBA_CONF_FILE
    echo "  disable spoolss = yes" >> $SAMBA_CONF_FILE
    echo "  null passwords = yes" >> $SAMBA_CONF_FILE
    echo "  wide links = no" >> $SAMBA_CONF_FILE
    echo "  strict allocate = no" >> $SAMBA_CONF_FILE
    echo "  use sendfile = yes" >> $SAMBA_CONF_FILE
    echo "  oplocks = yes" >> $SAMBA_CONF_FILE
    echo "  level2 oplocks = yes" >> $SAMBA_CONF_FILE
  fi
}
create_shared_samba_conf()
{
  ulog smbd "create samba config using shared folder info"
  SHARE_CNT=`syscfg get SharedFolderCount`
  USB_DISK_COUNT=`ls /dev/ | grep -r "sd[a-z][0-9.]" | uniq | wc -l`
  
  if [ $USB_DISK_COUNT -gt 0 ] ; then
    [ -z "$SHARE_CNT" ] && SHARE_CNT=0
    if [ $SHARE_CNT -gt 0 ] ; then
      echo "share count = $SHARE_CNT" >> $TMP_LOG  
      for ct in `seq 1 $SHARE_CNT`
      do
        echo "share ${ct}." >> $TMP_LOG
        NAMESPACE="smb_$ct"
        if [ "" != "$NAMESPACE" ] ; then
          NAME=`syscfg get $NAMESPACE name`
          FOLDER=`syscfg get $NAMESPACE folder`
          DRIVE=`syscfg get $NAMESPACE drive`
          READONLY=`syscfg get $NAMESPACE readonly`
          GROUPS=`syscfg get $NAMESPACE groups | sed 's/,/ /g'`
          
          echo "" >> $TMP_LOG
          echo "SharedFolder_${ct}: $NAMESPACE" >> $TMP_LOG
          echo " name   : $NAME" >> $TMP_LOG
          echo " folder : .$FOLDER." >> $TMP_LOG
          echo " drive  : $DRIVE" >> $TMP_LOG
          echo " ro     : $READONLY" >> $TMP_LOG
          echo " groups : $GROUPS" >> $TMP_LOG
          share_loc="/mnt/$DRIVE$FOLDER"
          if [ -d "$share_loc" ] ; then
            echo "" >> $SAMBA_CONF_FILE
            if [ "$NAME" ] ; then
              echo "[$NAME]" >> $SAMBA_CONF_FILE
              echo "  path = /mnt/$DRIVE$FOLDER" >> $SAMBA_CONF_FILE
              echo "  inherit permissions = yes" >> $SAMBA_CONF_FILE
              echo "  create mask = 0777" >> $SAMBA_CONF_FILE
              echo "  directory mask = 0777" >> $SAMBA_CONF_FILE
              echo "  writable = yes" >> $SAMBA_CONF_FILE
              echo "  browseable = yes" >> $SAMBA_CONF_FILE
            fi
            
            if [ $ANON_ACCESS -gt 0 ] ; then
              echo "  public = yes" >> $SAMBA_CONF_FILE
              echo "  guest ok = yes" >> $SAMBA_CONF_FILE
            else
              echo "  guest ok = no" >> $SAMBA_CONF_FILE
              echo -n "  read list = ">> $SAMBA_CONF_FILE
              for g in $GROUPS
              do
                users=$(get_group_users "$g")
                ngperms=$(get_group_perms "$g")
                if [ "$ngperms" == "file_guest" ] ; then
                  if [ "$users" ] ; then
                    for u in $users
                    do
                      if [ "$u" != "root" ] ; then
                        echo -n "$u, " >> $SAMBA_CONF_FILE
                      fi
                    done
                  fi
                fi
              done
              echo "" >> $SAMBA_CONF_FILE
              echo -n "  write list = " >> $SAMBA_CONF_FILE
              for g in $GROUPS
              do
                users=$(get_group_users "$g")
                ngperms=$(get_group_perms "$g")
                if [ "$ngperms" == "file_admin" ] ; then
                  if [ "$users" ] ; then
                    for u in $users
                    do
                      if [ "$u" != "root" ] ; then
                        echo -n "$u, " >> $SAMBA_CONF_FILE
                      fi
                    done
                  fi
                fi
              done
              echo "" >> $SAMBA_CONF_FILE
              echo -n "  valid users = " >> $SAMBA_CONF_FILE
              for g in $GROUPS
              do
                users=$(get_group_users "$g")
                if [ "$users" ] ; then
                  for u in $users
                  do
                    if [ "$u" != "root" ] ; then
                      echo -n "$u, " >> $SAMBA_CONF_FILE
                    fi
                  done
                fi
              done
              echo "" >> $SAMBA_CONF_FILE
              echo -n "  admin users = " >> $SAMBA_CONF_FILE
              for g in $GROUPS
              do
                users=$(get_group_users "$g")
                ngperms=$(get_group_perms "$g")
                if [ "$ngperms" == "file_admin" ] ; then
                  if [ "$users" ] ; then
                    for u in $users
                    do
                      if [ "$u" != "root" ] ; then
                        echo -n "$u, " >> $SAMBA_CONF_FILE
                      fi
                    done
                  fi
                fi
              done
              echo "" >> $SAMBA_CONF_FILE
            fi
          fi
        fi
      done
    fi
  fi
}
create_default_samba_conf()
{
  DRIVES=$(get_all_media_drives)
  scntr=1
  labelcnt=0
  ulog smbd "create samba config using default USB drives"
  
  for d in $DRIVES
  do
    if is_shared_drive $d ; then
      ulog smbd "config: shared_drive : $d"
    else
      echo "" >> $SAMBA_CONF_FILE
      if [ "$DEFAULT_SHARE_NAME_CONVENTION" = "partition" ] ; then
        lbl=`usblabel "$d"`
        if [ "$lbl" ] ; then
          if [ -d "$ANON_SMB_DIR/$lbl" ] ; then
            newlabel=`mount | grep "$ANON_SMB_DIR/" | grep "$d " | sed 's/\\\\040/ /g' | awk -F " type" '{print $1}' | awk -F "on " '{print $2}' | awk -F "/" '{print $4}'`
            echo "[$newlabel]" >> $SAMBA_CONF_FILE
          else
            echo "[$lbl]" >> $SAMBA_CONF_FILE
          fi
        else
          echo "[$d]" >> $SAMBA_CONF_FILE
        fi
      elif [ "$DEFAULT_SHARE_NAME_CONVENTION" == "share" ] ; then
        echo "[share$scntr]" >> $SAMBA_CONF_FILE
      else
        echo "[$prod_name$scntr]" >> $SAMBA_CONF_FILE 
      fi
      
      echo "  path = /mnt/$d" >> $SAMBA_CONF_FILE
      echo "  create mask = 0777" >> $SAMBA_CONF_FILE
      echo "  directory mask = 0777" >> $SAMBA_CONF_FILE
      echo "  public = yes" >> $SAMBA_CONF_FILE
      echo "  writable = yes" >> $SAMBA_CONF_FILE
      echo "  browseable = yes" >> $SAMBA_CONF_FILE
      echo "  inherit acls = yes" >> $SAMBA_CONF_FILE
      if [ $ANON_ACCESS -gt 0 ] ; then
        echo "  guest ok = yes" >> $SAMBA_CONF_FILE
      else
        echo "  guest ok = no" >> $SAMBA_CONF_FILE
      fi
      scntr=`expr $scntr + 1`
    fi
  done
}
create_samba_conf () 
{
  create_common_samba_conf
  create_default_samba_conf
  create_shared_samba_conf
  ulog smbd "$SAMBA_CONF_FILE created"
}
create_samba_passwd_file () 
{
  userlist=$(get_current_user_list)
  if [ "$userlist" ] ; then
    echo -n "" > $SAMBA_PASS_FILE
    for un in $userlist
    do
      pword=$(get_user_password "$un")
      if [ "$pword" ] ; then
      (echo "$pword"; echo "$pword") | /bin/smbpasswd -as "$un" > /dev/null
      fi
    done
  fi
}
service_init ()
{
  umount_samba_shares
  mount_samba_shares
  sync_file_users
  create_samba_conf
  create_samba_passwd_file
}
service_pre_halt()
{
  umount_samba_shares
  rm -f $SAMBA_CONF_FILE
}
BRIDGE_MODE="0"
check_bridge_mode ()
{
  BRIDGE_MODE=`syscfg get bridge_mode`
  if [ "$BRIDGE_MODE" ] && [ "$BRIDGE_MODE" != "0" ] ; then
    echo "starting $NMB_SERVER for bridge mode"
    service_init
    killall nmbd &> /dev/null
    $NMB_SERVER -D
  fi
}
start_nmbd ()
{
  service_init
  killall nmbd &> /dev/null
  $NMB_SERVER -D
}
service_start ()
{
  wait_till_end_state ${SERVICE_NAME}
  service_init
  STATUS=`sysevent get ${SERVICE_NAME}-status`
  if [ "started" != "$STATUS" ] ; then
    sysevent set ${SERVICE_NAME}-errinfo 
    sysevent set ${SERVICE_NAME}-status starting
    
    echo "Starting ${SERVICE_NAME} ... "
    $SMB_SERVER &
    if [ "$BRIDGE_MODE" == "0" ] ; then
      $NMB_SERVER &
    fi
    check_err $? "Couldnt handle start"
    sysevent set ${SERVICE_NAME}-status started
  else
    check_bridge_mode
    start_nmbd
  fi
  sysevent set ${SERVICE_NAME}-isready yes
}
service_stop ()
{
  wait_till_end_state ${SERVICE_NAME}
  STATUS=`sysevent get ${SERVICE_NAME}-status`
  if [ "stopped" != "$STATUS" ] ; then
    sysevent set ${SERVICE_NAME}-errinfo 
    sysevent set ${SERVICE_NAME}-status stopping
    
    echo "Stoppping ${SERVICE_NAME} ..."
    killall -9 smbd &> /dev/null
    killall -9 nmbd &> /dev/null
    sleep 1
    
    service_pre_halt
    
    check_err $? "Couldnt handle stop"
    sysevent set ${SERVICE_NAME}-status stopped
  else
    check_bridge_mode
    start_nmbd
  fi
  sysevent set ${SERVICE_NAME}-isready no
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
  bridge-status)
    check_bridge_mode
    ;;
  lan-status)
      LAN_STATUS=`sysevent get lan-status`
      if [ "$LAN_STATUS" == "started" ]; then
          STATUS=`sysevent get ${SERVICE_NAME}-status`
          if [ "$STATUS" == "started" ]; then
              service_stop
              service_start
          fi
      fi
  ;;
  *)
    echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
    exit 3
    ;;
esac
