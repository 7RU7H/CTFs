#!/bin/sh
source /etc/init.d/ulog_functions.sh
gen_passwd() {
   TMP_PASSWORD_FILE=/tmp/.tmp_htpasswd_$$
   USERNAME=$1
   if [ "" = "$USERNAME" ] ; then
      USERNAME=admin
   fi
   PASSWORD=$2
   if [ "" = "$PASSWORD" ] ; then
      PASSWORD=admin
   fi
   sysevent set fuadmin_pass "$PASSWORD"
   
   echo $PASSWORD | /usr/sbin/htpasswd -c $TMP_PASSWORD_FILE $USERNAME > /dev/null 2>&1
   sed -i 's/^.*://g' $TMP_PASSWORD_FILE
   cat $TMP_PASSWORD_FILE
   rm -f $TMP_PASSWORD_FILE
}
PASSWORD_FILE=/tmp/.htpasswd
gen_authfile() {
   USERNAME=$1
   PASSWORD=$2
   IS_ENCODED=$3
   if [ "" = "$USERNAME" ] || [ "" = "$PASSWORD" ] ; then
      return
   fi
   sysevent set fuadmin_pass "$PASSWORD"
   if [ "encoded" = "$IS_ENCODED" ] ; then
       if [ -f $PASSWORD_FILE ] ; then
	   if grep "$USERNAME" $PASSWORD_FILE > /dev/null 2>&1 ; then
	       sed 's/^\('${USERNAME}':\)\(.*\)$/\1'${PASSWORD}'/g' < $PASSWORD_FILE > .htpasswd.NEW && mv .htpasswd.NEW $PASSWORD_FILE 
	   else
	       echo "$USERNAME:$PASSWORD" >> $PASSWORD_FILE
	   fi
       else
	   echo "$USERNAME:$PASSWORD" >> $PASSWORD_FILE
       fi
   else
      if [ -f $PASSWORD_FILE ] ; then
          echo "$PASSWORD" | /usr/sbin/htpasswd $PASSWORD_FILE $USERNAME > /dev/null 2>&1
      else 
          echo "$PASSWORD" | /usr/sbin/htpasswd -c $PASSWORD_FILE $USERNAME > /dev/null 2>&1
      fi
   fi
   grep $USERNAME $PASSWORD_FILE | cut -f2 -d:
}
