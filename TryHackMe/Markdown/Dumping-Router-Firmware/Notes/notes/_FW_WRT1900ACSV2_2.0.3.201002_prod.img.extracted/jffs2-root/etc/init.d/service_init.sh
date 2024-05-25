#!/bin/sh
change_root_passwd()
{
   PASSWD=`syscfg get http_admin_password`
   if [ -z "$PASSWD" ] ; then
      return
   fi
   echo "root:$PASSWD" | chpasswd -e
}
change_admin_passwd()
{
   PASSWD=`syscfg get http_admin_password`
   if [ -z "$PASSWD" ] ; then
      return
   fi
   echo "admin:$PASSWD" | chpasswd -e
}
case "$1" in
   http_admin_password)
      change_root_passwd
      change_admin_passwd
      ;;
   *)
      echo "Usage: service_init.sh http_admin_password" > /dev/console
      exit 3
      ;;
esac
