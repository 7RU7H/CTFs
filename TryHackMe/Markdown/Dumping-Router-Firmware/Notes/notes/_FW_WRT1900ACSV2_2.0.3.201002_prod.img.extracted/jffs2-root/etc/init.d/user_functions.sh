#!/bin/sh
POSIX_PASS_FILE="/tmp/etc/.root/passwd"
POSIX_SHAD_FILE="/tmp/etc/.root/shadow"
POSIX_GROUP_FILE="/tmp/etc/.root/group"
SAMBA_PASS_FILE="/tmp/samba/smbpasswd"
USER_PASS_FILE=`syscfg get user_auth_file`
if [ ! -f "$USER_PASS_FILE" ] ; then
    PASS=`syscfg get http_admin_password_initial`
    if [ -z "$PASS" ] ; then
        PASS=admin
    fi
    echo "1000:$PASS" > $USER_PASS_FILE
    echo "1001:guest" >> $USER_PASS_FILE
fi
MNT_DIR="/tmp/ftp"
UTMP_LOG="/tmp/user_config.log"
echo "" > $UTMP_LOG
FILE_ADMIN_GROUP_NAME="file_admin"
DEFAULT_GROUP=$FILE_ADMIN_GROUP_NAME
USER_BLACKLIST="root nobody sshd quagga firewall"
MIN_USER_ID=1010
get_pw_file () {
  echo `syscfg get user_auth_file`
}
get_pw_file_enc () {
  echo `syscfg get user_auth_enc`
}
get_user_count () {
  echo `syscfg get user_count`
}
get_file_guest_group_id () {
  gid=`cat $POSIX_GROUP_FILE | grep "file_guest:" | cut -d':' -f3`
  echo "$gid"
}
get_file_admin_group_id () {
  gid=`cat $POSIX_GROUP_FILE | grep "file_admin:" | cut -d':' -f3`
  echo "$gid"
}
get_group_users () {
  user_count=`syscfg get user_count`
  userlist=""
  for i in `seq 1 $user_count`
  do
    ugroup=`syscfg get user_${i}_group`
    if [ "$ugroup" == "$1" ] ; then
      uname=`syscfg get user_${i}_username`
      userlist="$uname $userlist"
    fi
  done
  echo "$userlist"
}
get_file_admin_usernames () {
  user_count=`syscfg get user_count`
  userlist=""
  for i in `seq 1 $user_count`
  do
    uname=`syscfg get user_${i}_username`
    ugroup=$(get_user_group "$uname")
    uperms=$(get_group_perms "$ugroup")
    if [ "$uperms" == "file_admin" ] ; then
      userlist="$uname,$userlist"
    fi
  done
  echo "$userlist"
}
get_file_guest_usernames () {
  user_count=`syscfg get user_count`
  userlist=""
  for i in `seq 1 $user_count`
  do
    uname=`syscfg get user_${i}_username`
    ugroup=$(get_user_group "$uname")
    uperms=$(get_group_perms "$ugroup")
    
    if [ "$uperms" == "file_guest" ] ; then
      userlist="$uname,$userlist"
    fi
  done
  echo "$userlist"
}
get_group_perms () {
  perms=""
  cnt=`syscfg get group_count`
  for i in `seq 1 $cnt`
  do
    name=`syscfg get group_${i}_name`
    if [ "$name" == "$1" ] ; then
      perms=`syscfg get group_${i}_perms`
    fi
  done
  echo "$perms"
}
do_group_entries () {
  admins=$(get_file_admin_usernames)
  guests=$(get_file_guest_usernames)
  adminid=$(get_file_admin_group_id)
  guestid=$(get_file_guest_group_id)
    
  sed -i "s/^file_admin:.*/file_admin:x:$adminid:$admins/g" $POSIX_GROUP_FILE
  sed -i "s/^root:.*/root:x:0:root,$admins/g" $POSIX_GROUP_FILE
  sed -i "s/^file_guest:.*/file_guest:x:$guestid:$guests/g" $POSIX_GROUP_FILE
}
USER_PASSFILE=$(get_pw_file)
USER_PASSENC=$(get_pw_file_enc)
USER_COUNT=$(get_user_count)
get_current_user_list () {
  userlist=""
  for ct in `seq 1 $USER_COUNT`
  do
    user=`syscfg get user_${ct}_username`
    userlist="$user $userlist"
  done
  echo "$userlist"
}
get_user_password () {
  uid=$(get_user_id "$1")
  echo `cat $USER_PASSFILE | grep "^$uid:" | cut -d':' -f2`
}
does_user_exist () {
  CurrentUsers=$(get_current_user_list)
  for cu in $CurrentUsers
  do
    if [ "$cu" == "$1" ] ; then
      echo "$cu"
    fi
  done
}
get_user_tag () {
  echo `syscfg show | grep "$1" | cut -d'_' -f1,2`
}
get_user_index () {
  echo `syscfg show | grep user_ | grep _username | grep "=$1$" | sort -n | head -n 1 | cut -d'_' -f2`
}
get_user_id () {
  idx=$(get_user_index "$1")
  if [ "$idx" ] ; then
    uid=`syscfg get user_${idx}_id`
    echo "$uid"
  fi
}
get_user_perms () {
  idx=$(get_user_index "$1")
  if [ "$idx" ] ; then
    perms=`syscfg get user_${idx}_perms`
    echo "$perms"
  fi
}
get_user_group () {
  idx=$(get_user_index "$1")
  if [ "$idx" ] ; then
    echo `syscfg get user_${idx}_group`
  fi
}
get_user_enabled () {
  idx=$(get_user_index "$1")
  if [ "$idx" ] ; then
    echo `syscfg get user_${idx}_enabled`
  fi
}
get_next_posix_id () {
  lastuid=`tail -n 1 $POSIX_PASS_FILE | cut -d':' -f3`
  lastgid=`tail -n 1 $POSIX_GROUP_FILE | cut -d':' -f3`
  if [ $lastgid -gt $lastuid ] ; then
    nid=`expr $lastgid + 1`
  else
    nid=`expr $lastuid + 1`
  fi
  if [ $nid -lt $MIN_USER_ID ] ; then
    nid=`expr $MIN_USER_ID + 1`
  fi
  echo "$nid"
}
nxtid=$(get_next_posix_id)
check_user_blacklist() {
  for Uname in $USER_BLACKLIST
  do
    if [ "$1" == "$Uname" ] ; then
      echo "user $1 blacklisted"  >> $UTMP_LOG
    fi
  done
}
get_group_name () {
  nm=`cat $POSIX_GROUP_FILE | grep ":$1:" | cut -d':' -f1`
  echo "$nm"
}
create_user_group_entries () {
  exists=`cat $POSIX_PASS_FILE | grep "^file_admin:"`
  if [ "$exists" == "" ] ; then
   adduser -h /mnt/ftp -H -D -u 999 file_admin
  fi
  exists=`cat $POSIX_GROUP_FILE | grep "^file_guest:"`
  if [ "$exists" == "" ] ; then
   addgroup file_guest
  fi
}
remove_user_passwords() {
  if [ "$1" ] ; then
    bl=$(check_user_blacklist "$1")
    if [ ! "$bl" ] ; then
      sed -i "/^$1:.*/d" $POSIX_PASS_FILE
      sed -i "/^$1:.*/d" $POSIX_SHAD_FILE
	if [ -f "$SAMBA_PASS_FILE" ]; then
	      sed -i "/^$1:.*/d" $SAMBA_PASS_FILE
	fi
    fi
  fi
}
add_new_user () {
    cnt=$(get_user_count)
    cnt=`expr $cnt + 1`
    syscfg set user_${cnt}_username "$1"
    syscfg set user_${cnt}_id "$3"
    syscfg set user_${cnt}_perms "$4"
    syscfg set user_count "$cnt"
    posix_pass=`cryptpw -a md5 "$2"`
    
    if [ "$4" == "file_admin" ] ; then
      echo "$1:x:$3:$3:File User,,,:$MNT_DIR/admin_mnt:/bin/sh" >> $POSIX_PASS_FILE
    else
      echo "$1:x:$3:$3:File User,,,:$MNT_DIR/guest_mnt:/bin/sh" >> $POSIX_PASS_FILE
    fi
    echo "$1:$posix_pass:15070:0:99999:7:::" >> $POSIX_SHAD_FILE
    echo "$3:$2" >> $USER_PASSFILE    
    syscfg commit
}
sync_user () {
   MODEL_NAME=`syscfg get device::model_base`
   if [ -z "$MODEL_NAME" ] ; then
        MODEL_NAME=`syscfg get device::modelNumber`
	MODEL_NAME=${MODEL_NAME%-*}
   fi
    HW_REVISION=`syscfg get device::hw_revision`
    remove_user_passwords "$1"
    posix_pass=`cryptpw -a md5 "$2"`
    ugroup=$(get_user_group "$1")
      if [ "$ugroup" == "admin" ] ; then
        echo "$1:x:$3:0:File User,,,:$MNT_DIR/admin_mnt:/bin/sh" >> $POSIX_PASS_FILE
      elif [ "$ugroup" == "guest" ] ; then
        echo "$1:x:$3:$3:File User,,,:$MNT_DIR/guest_mnt:/bin/sh" >> $POSIX_PASS_FILE
      else
        if [ "$4" == "file_admin" ] ; then
          echo "$1:x:$3:0:File User,,,:$MNT_DIR/$ugroup:/bin/sh" >> $POSIX_PASS_FILE
        else
          echo "$1:x:$3:$3:File User,,,:$MNT_DIR/$ugroup:/bin/sh" >> $POSIX_PASS_FILE
        fi
      fi
    
    echo "$1:$posix_pass:15070:0:99999:7:::" >> $POSIX_SHAD_FILE
    
    UID=$3
    PASS=$2
    sed -i "/^$UID:.*/d" $USER_PASSFILE
    echo "$3:$2" >> $USER_PASSFILE
}
del_user () {
  cnt=$(get_user_count)
  idx=$(get_user_index "$1")
  
  if [ "$idx" == "" ] ; then
    echo "no user $1 found"  >> $UTMP_LOG
    exit
  fi
  
  remove_user_passwords "$1"
  uid=`syscfg get user_${idx}_id`
  if [ "$cnt" == "$idx" ] ; then
    syscfg unset user_${cnt}_username
    syscfg unset user_${cnt}_id
    syscfg unset user_${cnt}_perms
  else
    replace_username=`syscfg get user_"$cnt"_username`
    replace_userid=`syscfg get user_"$cnt"_id`
    replace_userperms=`syscfg get user_"$cnt"_perms`
    syscfg set user_${idx}_username "$replace_username"
    syscfg set user_${idx}_id "$replace_userid"
    syscfg set user_${idx}_perms "$replace_userperms"
    syscfg unset user_${cnt}_username
    syscfg unset user_${cnt}_id
    syscfg unset user_${cnt}_perms
    cnt=`expr $cnt - 1`
  fi
}
filter_valid_users() {
  USER_COUNT=`syscfg get user_count`
  ID_LIST=""
  for i in `seq 1 $USER_COUNT`
  do
    NEW_ID=`syscfg get user_${i}_id`
    ID_LIST="$ID_LIST$NEW_ID"
    if [ "$i" != "$USER_COUNT" ] ; then
      ID_LIST="$ID_LIST|"
    fi
  done
  AUTH_FILE=`syscfg get user_auth_file`
  grep -E "$ID_LIST" $AUTH_FILE > /tmp/.tmppwl
  mv -f /tmp/.tmppwl $AUTH_FILE
  INVALID_USERS=`grep -vE "$ID_LIST" /etc/passwd | grep "/tmp/ftp" | cut -d':' -f1`
  for i in $INVALID_USERS
  do
    sed -i "/^$i:.*$/d" /tmp/etc/.root/passwd
    sed -i "/^$i:.*$/d" /tmp/etc/.root/shadow
  done
  
}
sync_file_users () {
  echo "sync_file_users:" >> $UTMP_LOG
  NUM_USERS=`syscfg get user_count`
  if [ "" = "$NUM_USERS" ] || [ "0" = "$NUM_USERS" ] ; then
      echo "error no users found!"  >> $UTMP_LOG
      exit 0;
  fi
  for ct in `seq 1 $NUM_USERS`
  do
    u=`syscfg get user_${ct}_username`
    un="$u"
    pt_pass=$(get_user_password "$u")
    uid=$(get_user_id "$u")
    ugroup=$(get_user_group "$u")
    CONFIGD_GROUPS=`syscfg show | grep group_ | grep _name | cut -d'=' -f2`
    g_deleted=0
    for cg in $CONFIGD_GROUPS
    do
      if [ "$cg" == "$ugroup" ] ; then
        g_deleted=1
      fi
    done
    if [ $g_deleted -eq 0 ] ; then
      syscfg set user_${ct}_group guest
      ugroup="guest"
    fi
    uperms=$(get_group_perms "$ugroup")
    uenabled=$(get_user_enabled "$u")
    bl=$(check_user_blacklist "$u")
    
    echo "" >> $UTMP_LOG
    echo "user_${ct}" >> $UTMP_LOG
    echo "username : $u" >> $UTMP_LOG
    echo "syscfg'd : $un" >> $UTMP_LOG
    echo "pt_pass  : $pt_pass" >> $UTMP_LOG
    echo "uid      : $uid" >> $UTMP_LOG
    echo "perms    : $uperms" >> $UTMP_LOG
    echo "group    : $ugroup" >> $UTMP_LOG
    echo "enabled  : $uenabled" >> $UTMP_LOG  
    
    if [ "$uenabled" == "1" ] ; then
      if [ ! "$bl" ] ; then
        if [ "$un" == "" ] ; then
          echo "user $u not found in syscfg" >> $UTMP_LOG
        else   
          if [ "$u" ] && [ "$pt_pass" ] && [ "$uid" ] && [ "$uperms" ] ; then
            echo "sync_user $u $pt_pass $uid $uperms" >> $UTMP_LOG
            sync_user $u $pt_pass $uid $uperms
          else
            echo "error syncing user $u"  >> $UTMP_LOG
          fi
        fi
      else
        echo "skipping user $u ( black listed )" >> $UTMP_LOG
      fi
    else
      echo "user $u disabled in syscfg" >> $UTMP_LOG
      remove_user_passwords "$u"
    fi
  done
  do_group_entries
  echo "all users synced"  >> $UTMP_LOG
  filter_valid_users
}
