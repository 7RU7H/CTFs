#!/bin/sh 
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/ipv6_functions.sh
SERVICE_NAME="dhcpv6_server"
if [ "1" = "`syscfg get ipv6_verbose_logging`" ] 
then
   LOG=/var/log/ipv6.log
else
   LOG=/dev/null
fi
SELF="$0[$$]"
EVENT=$1
if [ ! -z "$EVENT" ]
then
        VALUE=" (=`sysevent get $EVENT`)"
fi
ulog dhcpv6s status "$$: got EVENT=$EVENT$VALUE"
make_duid() {
   DUID_TYPE="00:02:"
   MANUFACTURER_IANA_PRIVATE_ENTERPRISE_NUMBER="03:09:05:05:"
   BRIDGE_MAC_NAME=`syscfg get lan_ifname`
   BRIDGE_MAC=`ifconfig $BRIDGE_MAC_NAME | grep HWaddr | awk '{print $5}'`
   if [ -n "$1" ] ; then
      syscfg set $1 ${DUID_TYPE}${MANUFACTURER_IANA_PRIVATE_ENTERPRISE_NUMBER}${BRIDGE_MAC}
      syscfg commit
   fi
}
prepare_dhcpv6s_config() {
   CONFIG_EMPTY=yes
   LOCAL_DHCPV6_CONF_FILE=/tmp/dhcp6s.conf$$
   rm -f $LOCAL_DHCPV6_CONF_FILE
   echo "# Automatically generated on `date` by $SELF for $EVENT" > $LOCAL_DHCPV6_CONF_FILE
   eval `get_current_lan_ipv6address`
   if [ "1" = "$SYSCFG_dhcp_server_propagate_wan_nameserver" ] ; then
      OTHER_DNS_NAMESERVERS=`sysevent get ipv6_nameserver`
      if [ -n "$CURRENT_LAN_IPV6ADDRESS" -o -n "$OTHER_DNS_NAMESERVERS" ] ; then
         echo "option domain-name-servers $CURRENT_LAN_IPV6ADDRESS $OTHER_DNS_NAMESERVERS ;" >> $LOCAL_DHCPV6_CONF_FILE
         CONFIG_EMPTY=no
      fi
   else
      if [ -n "$CURRENT_LAN_IPV6ADDRESS" ] ; then
         echo "option domain-name-servers $CURRENT_LAN_IPV6ADDRESS  ;" >> $LOCAL_DHCPV6_CONF_FILE
         CONFIG_EMPTY=no
      fi
   fi
   if [ ! -z "`sysevent get ipv6_domain`" -o ! -z "`sysevent get dhcp_domain`" ]
   then
      DOMAIN_NAMES=`sysevent get ipv6_domain`
      if [ -n "$DOMAIN_NAMES" ] ; then
         for x in $DOMAIN_NAMES ; do
            echo "option domain-name \"$x\" ;" >> $LOCAL_DHCPV6_CONF_FILE
         done
         CONFIG_EMPTY=no
      fi
      DOMAIN_NAMES=`sysevent get dhcp_domain`
      if [ -n "$DOMAIN_NAMES" ] ; then
         for x in $DOMAIN_NAMES ; do
            echo "option domain-name \"$x\" ;" >> $LOCAL_DHCPV6_CONF_FILE
         done
         CONFIG_EMPTY=no
      fi
   fi
   if [ ! -z "`sysevent get ipv6_ntp_server`" ]
   then
      echo "option ntp-servers `sysevent get ipv6_ntp_server` ;" >> $LOCAL_DHCPV6_CONF_FILE
      CONFIG_EMPTY=no
   fi
   cat $LOCAL_DHCPV6_CONF_FILE > $DHCPV6_CONF_FILE
   rm -f $LOCAL_DHCPV6_CONF_FILE
}
restore_dhcpv6s_duid() {
   if [ ! -s /var/run/dhcp6s_duid ] 
   then
	if [ -z "$SYSCFG_dhcpv6s_duid" ] 
        then
           make_duid dhcpv6s_duid 
           eval `utctx_cmd get dhcpv6s_duid`
        fi
        echo -n "$SYSCFG_dhcpv6s_duid" > /var/run/dhcp6s_duid
        echo "$SELF: dhcpv6 server DUID restored as $SYSCFG_dhcpv6s_duid" >> $LOG
   fi
}
service_init ()
{
   eval `utctx_cmd get dhcpv6s_duid lan_ifname dhcpv6s_enable dhcp_server_propagate_wan_nameserver`
   LAN_INTERFACE_NAME=$SYSCFG_lan_ifname
   LAN_STATE=`sysevent get lan-status`
   DHCPV6_BINARY=/sbin/dhcp6s
   DHCPV6_CONF_FILE=/etc/dhcp6s.conf
   DHCPV6_PID_FILE=/var/run/dhcp6s.pid
}
service_start ()
{
   if [ -z "$SYSCFG_dhcpv6s_enable" ] || [ "$SYSCFG_dhcpv6s_enable" = "0" ]
   then
	echo "$SELF: DHCPv6 server cannot start because it is disabled" >> $LOG
      	sysevent set ${SERVICE_NAME}-errinfo "Cannot start: disabled"
	return
   fi
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] 
   then
      if [ "$LAN_STATE" != "started" ]
      then
	echo "$SELF: DHCPv6 server cannot start LAN=$LAN_STATE" >> $LOG
      	sysevent set ${SERVICE_NAME}-errinfo "Cannot start LAN=$LAN_STATE"
      	sysevent set ${SERVICE_NAME}-status stopped
	return
      fi
      sysevent set ${SERVICE_NAME}-errinfo 
      sysevent set ${SERVICE_NAME}-status starting
      echo "$SELF: Starting dhcpv6 server on LAN ($LAN_INTERFACE_NAME) event=$EVENT" >> $LOG
      restore_dhcpv6s_duid
      prepare_dhcpv6s_config
      if [ "$CONFIG_EMPTY" = "no" ]
      then
	      $DHCPV6_BINARY -P $DHCPV6_PID_FILE $LAN_INTERFACE_NAME >> $LOG 2>&1
	      check_err $? "Couldnt handle start"
	      sysevent set ${SERVICE_NAME}-status started
      else
	      sysevent set ${SERVICE_NAME}-errinfo "Nothing to announce with DHCPv6"
	      sysevent set ${SERVICE_NAME}-status stopped
      fi
   fi
}
service_stop ()
{
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" != "$STATUS" ] 
   then
      sysevent set ${SERVICE_NAME}-errinfo 
      sysevent set ${SERVICE_NAME}-status stopping
      echo "$SELF: Stopping DHCPv6 Server (LAN state=$LAN_STATE, event=$EVENT)" >> $LOG
      killall dhcp6s > /dev/null 2>&1
      sysevent set ${SERVICE_NAME}-status stopped
   fi
}
service_init 
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
   lan-status|ipv6_nameserver|ipv6_domain|ipv6_ntp_server|br0_ipv6_prefix|br1_ipv6_prefix|br0_ula_prefix|dhcp_domain)
      service_stop
      service_start
      ;;
     
   *)
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      echo "Received $1" > /dev/console
      exit 3
      ;;
esac
