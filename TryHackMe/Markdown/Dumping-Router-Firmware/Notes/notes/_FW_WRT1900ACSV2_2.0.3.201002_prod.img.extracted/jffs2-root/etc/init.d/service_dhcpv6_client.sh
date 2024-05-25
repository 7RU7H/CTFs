#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh
source /etc/init.d/service_dhcpv6_client/dhclient_handlers.sh
SERVICE_NAME="dhcpv6_client"
SELF="$0[$$]"
DHCPV6_BIN=dhclient
DHCPV6_BINARY="/sbin/${DHCPV6_BIN}"
DHCPV6_CONF_FILE=/etc/dhclient.conf
DHCPV6_PID_FILE=/var/run/dhcp6c.pid
SCRIPT_FILE=/tmp/dhclient_script.sh
LEASES_FILE=/tmp/dhclient_leases.db
PROGRESS_SCRIPT_FILE=/etc/cron/cron.everyminute/dhcpv6_check_progress.sh 
DEPREF6_SCRIPT_FILE="/tmp/depref6.sh"
if [ "1" = "`syscfg get ipv6_verbose_logging`" ] 
then
   LOG=/var/log/ipv6.log
else
   LOG=/dev/null
fi
DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE=1
prepare_DEPREF6_sanity_script() {
(
cat << 'End-of-Text'
#!/bin/sh
sleep 20
TEST=`sysevent get depref6_sanity`
if [ "1" = "$TEST" ] ; then
   echo "dhcpv6c DEPREF6 watchdog detected that dhclient is stuck in DEPREF6 state. Restarting dhcpv6c" > /dev/console
   echo "dhcpv6c DEPREF6 watchdog detected that dhclient is stuck in DEPREF6 state. Restarting dhcpv6c" >> /var/log/messages
   sysevent set dhcpv6_client-restart
else
   sysevent set depref6_sanity
fi
End-of-Text
)> $DEPREF6_SCRIPT_FILE
   chmod 777 $DEPREF6_SCRIPT_FILE
}
remove_dhcpv6_check_progress_script() {
   rm -f $PROGRESS_SCRIPT_FILE
}
prepare_dhcpv6_check_progress_script() {
   sysevent set dhcpv6_progress_script_num_times_called 0
(
cat << 'End-of-Text'
#!/bin/sh
PROGRESS_SCRIPT_FILE=/etc/cron/cron.everyminute/dhcpv6_check_progress.sh 
NUM_MINS_TO_WAIT=2
SYSCFG_dhcpv6c_enable=`syscfg get dhcpv6c_enable`
SYSEVENT_dhcpv6_client_current_config=`sysevent get dhcpv6_client_current_config`
if [ -n "$SYSCFG_dhcpv6c_enable" -a "$SYSCFG_dhcpv6c_enable" != "3" ] ; then
   rm -f $PROGRESS_SCRIPT_FILE
   exit 0
fi
if [ -z "$SYSEVENT_dhcpv6_client_current_config" -o "$SYSEVENT_dhcpv6_client_current_config" = "4" ] ; then
   rm -f $PROGRESS_SCRIPT_FILE
   sysevent set dhcpv6_progress_script_num_times_called 
   exit 0
fi
if [ "SYSEVENT_dhcpv6_client_current_config" != "$SYSCFG_dhcpv6c_enable" ] ; then
   NUM=`sysevent get dhcpv6_progress_script_num_times_called`
   if [ -z "$NUM" ] ; then
      NUM=0
   fi
   NUM=`expr $NUM + 1` 
   if [ "$NUM_MINS_TO_WAIT" -le "$NUM" ] ; then
      rm -f $PROGRESS_SCRIPT_FILE
      sysevent set dhcpv6_client-restart ACCEPT_INCOMPLETE_LEASE
      exit 0
   else
      sysevent set dhcpv6_progress_script_num_times_called $NUM
   fi
else
   rm -f $PROGRESS_SCRIPT_FILE
   exit 0
fi
End-of-Text
)> $PROGRESS_SCRIPT_FILE
   chmod 777 $PROGRESS_SCRIPT_FILE
}
make_dhcpv6c_duid() {
   if [ -z "$1" ] ; then
      ulog dhcpv6c status "$SELF called make_dhcpv6c_duid with no parameter. Ignoring."
      return
   fi
   DUID_TYPE="00:02:"
   MANUFACTURER_IANA_PRIVATE_ENTERPRISE_NUMBER="03:09:05:05:"
   BRIDGE_MAC=`ifconfig $SYSCFG_lan_ifname | grep HWaddr | awk '{print $5}'`
   if [ -n "$1" ] ; then
      syscfg set $1 ${DUID_TYPE}${MANUFACTURER_IANA_PRIVATE_ENTERPRISE_NUMBER}${BRIDGE_MAC}
      syscfg commit
   fi
}
restore_dhcpv6c_duid() {
   if [ ! -s /var/run/dhcpv6c_duid ] 
   then
        SYSCFG_dhcpv6c_duid=`syscfg get dhcpv6c_duid`
	if [ -z "$SYSCFG_dhcpv6c_duid" ] 
        then
           ulog dhcpv6c status "No dhcpv6 client duid exists. Creating one ..."
           make_dhcpv6c_duid dhcpv6c_duid
           SYSCFG_dhcpv6c_duid=`syscfg get dhcpv6c_duid`
         fi
         echo -n "$SYSCFG_dhcpv6c_duid" > /var/run/dhcp6c_duid
   fi
}
prepare_leases_file() {
   echo -n > $LEASES_FILE
}
prepare_dhcpv6_callback_file() {
prepare_DEPREF6_sanity_script
if [ -f "$SCRIPT_FILE" ] ; then
   ulog dhcpv6c status "script file ($SCRIPT_FILE) exist. Not recreating it."
   return
fi
(
cat << 'End-of-Text'
#!/bin/sh
source /etc/init.d/ulog_functions.sh
source /etc/init.d/ipv6_functions.sh
source /etc/init.d/resolver_functions.sh
source /etc/init.d/service_dhcpv6_client/dhclient_handlers.sh
source /etc/init.d/service_wan/wan_helper_functions
DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE=1
PROGRESS_SCRIPT_FILE=/etc/cron/cron.everyminute/dhcpv6_check_progress.sh 
DEPREF6_SCRIPT_FILE="/tmp/depref6.sh"
bailout()
{
   ulog dhcpv6c warning " Restarting DHCPv6 Protocol"
   sysevent set dhcpv6_client-restart
   exit 0
}
initiate_restart()
{
   sysevent set dhcpv6c_confused_state 1
   ulog dhcpv6c error "Event $reason on interface $interface has confusing information. Event contained:"
   ulog dhcpv6c info "  new_ip6_address: $new_ip6_address  old_ip6_address: $old_ip6_address"
   ulog dhcpv6c info "  new_ip6_prefixlen: $new_ip6_prefixlen  old_ip6_prefixlen: $old_ip6_prefixlen"
   ulog dhcpv6c info "  new_ip6_prefix: $new_ip6_prefix  old_ip6_prefix: $old_ip6_prefix"
   ulog dhcpv6c info "  new_dhcp6_name_servers: $new_dhcp6_name_servers  old_dhcp6_name_servers: $old_dhcp6_name_servers"
   ulog dhcpv6c info "  new_dhcp6_domain_search: $new_dhcp6_domain_search  old_dhcp6_domain_search: $old_dhcp6_domain_search"
   ulog dhcpv6c info "  new_dhcp6_sntp_servers: $new_dhcp6_sntp_servers  old_dhcp6_sntp_servers: $old_dhcp6_sntp_servers"
   ulog dhcpv6c info "  new_dhcp6_aftr: $new_dhcp6_aftr  old_dhcp6_aftr: $old_dhcp6_aftr"
   ulog dhcpv6c info "  new_max_life: $new_max_life  old_max_life: $old_max_life"
   ulog dhcpv6c info "  new_preferred_life: $new_preferred_life  old_preferred_life: $old_preferred_life"
   ulog dhcpv6c info "  new_dhcp6_server_id: $new_dhcp6_server_id  old_dhcp6_server_id: $old_dhcp6_server_id"
   ulog dhcpv6c info "  new_dhcp6_client_id: $new_dhcp6_client_id  old_dhcp6_client_id: $old_dhcp6_client_id"
   ulog dhcpv6c info "  new_iaid: $new_iaid  old_iaid:$old_iaid"
   ulog dhcpv6c info "  new_starts: $new_starts  old_starts: $old_starts"
   ulog dhcpv6c info "  new_life_starts: $new_life_starts  old_life_starts: $old_life_starts"
   panic
   bailout
}
sysevent set depref6_sanity
case "$reason" in
   PREINIT6)
      ulog dhcpv6c status "dhclient in PREINIT6 state"
      sysevent set dhcpv6c_confused_state  
      sysevent set dhcpv6c_bound 0
      release_ia_na
      release_ia_pd
      release_sntp_info
      release_dns_info
      release_aftr
      exit 0
      ;;
   BOUND6)
      ulog dhcpv6c status "dhclient in BOUND6 state"
      if [ -n "`sysevent get dhcpv6c_confused_state`" ] ; then
         ulog dhcpv6c status "dhclient is confused. Ignoring state"
         exit 0
      fi
      if [ -n "$old_ip6_prefix" -o "$old_dhcp6_aftr" ] ; then
         ulog dhcpv6c warning "prefix: $old_ip6_prefix  aftr: $old_dhcp6_aftr"
         initiate_restart
      fi
      if [ -n "$DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE" ] ; then
         rm -f $PROGRESS_SCRIPT_FILE
      fi
      if [ -n "$new_ip6_address" ] ; then
         new_ia_na
         RC="$?"
         if [ "3" = "$RC" ] ; then
            ulog dhcpv6c status "Offered address failed DAD."
            exit $RC
         fi
         if [ "0" != "$RC" ] ; then
            exit $RC
         fi
      fi
      if [ -n "$new_ip6_prefix" ] ; then
         new_ia_pd
         sysevent set ipv6_firewall-restart
      fi
      if [ -n "$new_dhcp6_name_servers" -o -n "$new_dhcp6_domain_search" ] ; then
         new_dns_info
      fi
      if [ -n "$new_dhcp6_sntp_servers" ]  ; then
         new_sntp_info
      fi
      if [ -n "$new_dhcp6_aftr" ] ; then
         new_aftr
      fi
      sysevent set ipv6_connection_state "ipv6 connection provisioned"
      sysevent set dhcpv6c_bound 1
      exit 0
      ;;
   RENEW6)
      ulog dhcpv6c status "dhclient in RENEW6 state"
      if [ -n "`sysevent get dhcpv6c_confused_state`" ] ; then
         ulog dhcpv6c status "dhclient is confused. Ignoring state"
         exit 0
      fi
      if [ -n "$new_ip6_prefix" -a -n "$old_ip6_prefix" -a "$old_ip6_prefix" = "$new_ip6_prefix" ] ; then
         SYSEVENT_ipv6_delegated_prefix=`sysevent get ipv6_delegated_prefix`
         if [ "$new_ip6_prefix"  != "$SYSEVENT_ipv6_delegated_prefix" ] ; then
            ulog dhcpv6c warning "renew: $old_ip6_prefix current: $SYSEVENT_ipv6_delegated_prefix"
            initiate_restart
         fi
      fi
      if [ -n "$new_ip6_prefix" -a -z "$old_ip6_prefix" -a  -z "`sysevent get ipv6_ia_pd_received`" ] ; then
         ulog dhcpv6c warning "renew: new prefix "$new_ip6_prefix" but no old_ip6_prefix"
         initiate_restart
      fi
      if [ -n "$DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE" ] ; then
         SYSCFG_dhcpv6c_enable=`syscfg get dhcpv6c_enable`
         if [ -z "$SYSCFG_dhcpv6c_enable" ] ; then
            SYSCFG_dhcpv6c_enable=3
         fi
         if [ "$SYSCFG_dhcpv6c_enable" = "3" ] ; then
            SYSEVENT_dhcpv6_client_current_config=`sysevent get dhcpv6_client_current_config`
            if [ -n "$SYSEVENT_dhcpv6_client_current_config" -a "$SYSEVENT_dhcpv6_client_current_config" = "2" ] ; then
               ulog dhcpv6c status "Restart dhcpv6_client to attempt to get IA_NA and IA_PD"
               sysevent set dhcpv6_client_current_config
               sysevent set dhcpv6_client-restart ACCEPT_INCOMPLETE_LEASE
               exit 0
            fi
         fi
      fi
      if [ -n "$new_ip6_address" -a -n "$old_ip6_address" -a "$new_ip6_address" != "$old_ip6_address" ] ; then
         release_ia_na
         new_ia_na
         if [ "0" != "$?" ] ; then
            exit $?
         fi
      else
         if [ -z "$new_ip6_prefixlen" ] ; then
            new_ip6_prefixlen=64
         fi
         if [ -n "$new_ip6_address" -a "${new_ip6_address}/${new_ip6_prefixlen}" != "`sysevent get ${interface}_dhcpv6_ia_na`" ] ; then
            release_ia_na
            new_ia_na
            if [ "0" != "$?" ] ; then
              exit $?
           fi
         fi
      fi
      if [ -n "$new_ip6_prefix" -a -n "$old_ip6_prefix" -a "$new_ip6_prefix" = "$old_ip6_prefix" -a -n "$new_max_life" -a "0" -lt "$new_max_life" ] ; then
         renew_ia_pd
      fi
      if [ -n "$new_ip6_prefix" -a -n "$old_ip6_prefix" -a "$new_ip6_prefix" != "$old_ip6_prefix" ] ; then
         deprecate_ia_pd
         new_ia_pd
         sysevent set ipv6_firewall-restart
      elif [  -n "$new_ip6_prefix" -a -n "$new_max_life" -a "$new_max_life" != 0 -a "$new_ip6_prefix" != "`sysevent get ipv6_delegated_prefix`" ] ; then
         deprecate_ia_pd
         new_ia_pd
         sysevent set ipv6_firewall-restart
      fi
      if [ -n "$new_dhcp6_sntp_servers" -a -n "$old_dhcp6_sntp_servers" -a "$new_dhcp6_sntp_servers" != "$old_dhcp6_sntp_servers" ] ; then
         release_sntp_info
         new_sntp_info
      fi
      
      if [ -n "$new_dhcp6_domain_search" -a -n "$old_dhcp6_domain_search" -a "$new_dhcp6_domain_search" != "$old_dhcp6_domain_search" ] ; then
         REDO_DNS_INFO=1
      elif [ -n "$new_dhcp6_name_servers" -a  -n "$old_dhcp6_name_servers" -a "$new_dhcp6_name_servers" != "$old_dhcp6_name_servers" ] ; then
         REDO_DNS_INFO=1
      elif [ -n "$new_dhcp6_domain_search" -o -n "$new_dhcp6_domain_search" ] ; then
         renew_dns_info
         REDO_DNS_INFO=0
      fi
      if [ -n "$REDO_DNS_INFO" -a "1" = "$REDO_DNS_INFO" ] ; then
         release_dns_info
         new_dns_info
      fi
      sysevent set ipv6_ia_pd_received 1
      exit 0
      ;;
   REBIND6)
      ulog dhcpv6c status "dhclient in REBIND6 state"
      if [ -n "`sysevent get dhcpv6c_confused_state`" ] ; then
         ulog dhcpv6c status "dhclient is confused. Ignoring state"
         exit 0
      fi
      if [ -n "$DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE" ] ; then
         SYSCFG_dhcpv6c_enable=`syscfg get dhcpv6c_enable`
         if [ -z "$SYSCFG_dhcpv6c_enable" ] ; then
            SYSCFG_dhcpv6c_enable=3
         fi
         if [ "$SYSCFG_dhcpv6c_enable" = "3" ] ; then
            SYSEVENT_dhcpv6_client_current_config=`sysevent get dhcpv6_client_current_config`
            if [ -n "$SYSEVENT_dhcpv6_client_current_config" -a "$SYSEVENT_dhcpv6_client_current_config" = "2" ] ; then
               ulog dhcpv6c status "Restart dhcpv6_client to attempt to get IA_NA and IA_PD"
               sysevent set dhcpv6_client_current_config
               sysevent set dhcpv6_client-restart ACCEPT_INCOMPLETE_LEASE
               exit 0
            fi
         fi
      fi
      if [ -n "$new_ip6_address" -a -n "$old_ip6_address" -a "$new_ip6_address" != "$old_ip6_address" ] ; then
         release_ia_na
         new_ia_na
         if [ "0" != "$?" ] ; then
            exit $?
         fi
      else
         if [ -z "$new_ip6_prefixlen" ] ; then
            new_ip6_prefixlen=64
         fi
         if [ -n "$new_ip6_address" -a "${new_ip6_address}/${new_ip6_prefixlen}" != "`sysevent get ${interface}_dhcpv6_ia_na`" ] ; then
            release_ia_na
            new_ia_na
            if [ "0" != "$?" ] ; then
              exit $?
           fi
         fi
      fi
      if [ -n "$new_ip6_prefix" -a -n "$old_ip6_prefix" -a "$new_ip6_prefix" = "$old_ip6_prefix" -a -n "$new_max_life" -a "0" -lt "$new_max_life" ] ; then
         renew_ia_pd
      fi
      if [ -n "$new_ip6_prefix" -a -n "$old_ip6_prefix" -a "$new_ip6_prefix" != "$old_ip6_prefix" ] ; then
         deprecate_ia_pd
         new_ia_pd
         sysevent set ipv6_firewall-restart
      elif [  -n "$new_ip6_prefix" -a -n "$new_max_life" -a "$new_max_life" != 0 -a "$new_ip6_prefix" != "`sysevent get ipv6_delegated_prefix`" ] ; then
         deprecate_ia_pd
         new_ia_pd
         sysevent set ipv6_firewall-restart
      fi
      if [ -n "$new_dhcp6_sntp_servers" -a -n "$old_dhcp6_sntp_servers" -a "$new_dhcp6_sntp_servers" != "$old_dhcp6_sntp_servers" ] ; then
         release_sntp_info
         new_sntp_info
      fi
      
      if [ -n "$new_dhcp6_domain_search" -a -n "$old_dhcp6_domain_search" -a "$new_dhcp6_domain_search" != "$old_dhcp6_domain_search" ] ; then
         REDO_DNS_INFO=1
      elif [ -n "$new_dhcp6_name_servers" -a  -n "$old_dhcp6_name_servers" -a "$new_dhcp6_name_servers" != "$old_dhcp6_name_servers" ] ; then
         REDO_DNS_INFO=1
      elif [ -n "$new_dhcp6_domain_search" -o -n "$new_dhcp6_domain_search" ] ; then
         renew_dns_info
         REDO_DNS_INFO=0
      fi
      if [ -n "$REDO_DNS_INFO" -a "1" = "$REDO_DNS_INFO" ] ; then
         release_dns_info
         new_dns_info
      fi
      if [ -n "$new_ip6_prefix" -a -n "$old_ip6_prefix" ] ; then
         sysevent set radvd-reload
         ulog dhcpv6c status "dhclient in REBIND6 state, eventing radvd-reload"
      fi
      exit 0
      ;;
   RELEASE6)
      ulog dhcpv6c status "dhclient in RELEASE6 state"
      if [ -n "`sysevent get dhcpv6c_confused_state`" ] ; then
         ulog dhcpv6c status "dhclient is confused. Ignoring state"
         exit 0
      fi
      release_ia_na
      release_ia_pd
      sysevent set ipv6_firewall-restart
      if [ -n "$old_dhcp6_name_servers" -o -n "$old_dhcp6_domain_search" ] ; then
         release_dns_info
      fi
      release_sntp_info
      release_aftr
      if [ -n "$DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE" ] ; then
         sysevent set dhcpv6_client_current_config
      fi
      sysevent set ipv6_connection_state "ipv6 dhcpv6 manually released"
      exit 0
      ;;
   DEPREF6)
      ulog dhcpv6c status "dhclient in DEPREF6 state"
      if [ -n "`sysevent get dhcpv6c_confused_state`" ] ; then
         ulog dhcpv6c status "dhclient is confused. Ignoring state"
         exit 0
      fi
      sysevent set depref6_sanity 1
      exec $DEPREF6_SCRIPT_FILE &
      ulog dhcpv6c status "dhclient in DEPREF6 state after starting $DEPREF6_SCRIPT_FILE"
      exit 0
      ;;
   EXPIRE6)
      ulog dhcpv6c status "dhclient in EXPIRE6 state"
      if [ -n "`sysevent get dhcpv6c_confused_state`" ] ; then
         ulog dhcpv6c status "dhclient is confused. Ignoring state"
         exit 0
      fi
      ulog dhcpv6c status "Expire has new_ip6_prefix $new_ip6_prefix , old_ip6_prefix $old_ip6_prefix"
      ulog dhcpv6c status "old_max_life $old_max_life  old_ip6_address $old_ip6_address"
      ulog dhcpv6c status "sysevent `sysevent get ipv6_delegated_prefix`"
      FW_RESTART_NEEDED=0
      if [ -n "$old_ip6_prefix" ] ; then
         if [ "$old_ip6_prefix" = "`sysevent get ipv6_delegated_prefix`" ] ; then
ulog dhcpv6c status "EXPIRE calling release_ia_pd"
            release_ia_pd
            FW_RESTART_NEEDED=1
         fi
      fi
      if [ -n "$old_ip6_address" ] ; then
         release_ia_na
      fi
      if [ -n "$old_dhcp6_name_servers" -o -n "$old_dhcp6_domain_search" ] ; then
         release_dns_info
      fi
      if [ -n "$old_dhcp6_sntp_servers" ] ; then
         release_sntp_info
      fi
               
      if [ "1" = "$FW_RESTART_NEEDED" ] ; then
         sysevent set ipv6_firewall-restart
      fi
      exit 0
      ;;
   state_done)
      ulog dhcpv6c status "dhclient in state_done"
      sysevent set ipv6_ia_pd_received
      ;;
   *)
      ulog dhcpv6c status "Rceived unhandled state ($reason)"
      ;;
esac
    
End-of-Text
)> $SCRIPT_FILE
   chmod 777 $SCRIPT_FILE
}
prepare_dhcpv6_client_config_file() {
   WAN_IF_NAME=$1
   LOCAL_DHCPV6_CONF_FILE=/tmp/dhcp6c.conf$$
   rm -f $LOCAL_DHCPV6_CONF_FILE
   echo -n > $LOCAL_DHCPV6_CONF_FILE
   echo "timeout 60;" >> $LOCAL_DHCPV6_CONF_FILE
   echo "retry 60;" >> $LOCAL_DHCPV6_CONF_FILE
   echo "select-timeout 5;" >> $LOCAL_DHCPV6_CONF_FILE
   echo "option dhcp6.aftr code 64 = string;" >> $LOCAL_DHCPV6_CONF_FILE
   echo "interface \"$WAN_IF_NAME\" {" >> $LOCAL_DHCPV6_CONF_FILE
   echo "   send dhcp-client-identifier $SYSCFG_dhcpv6c_duid;" >> $LOCAL_DHCPV6_CONF_FILE
   echo "   send dhcp6.reconf-accept;" >> $LOCAL_DHCPV6_CONF_FILE
   echo "   request dhcp6.name-servers,dhcp6.domain-search,dhcp6.sntp-servers,dhcp6.aftr;" >> $LOCAL_DHCPV6_CONF_FILE
   echo "}" >> $LOCAL_DHCPV6_CONF_FILE  
   cat $LOCAL_DHCPV6_CONF_FILE > $DHCPV6_CONF_FILE
   rm -f $LOCAL_DHCPV6_CONF_FILE
}
force_expire_deprecated_address ()
{
   ulog dhcpv6c status "in force_expire_deprecated_address $1 , $2"
   is_deprecated_delegated_address $1
   if [ "$?" = "1" ] ; then
      remove_deprecated_delegated_address $1
      sysevent set ipv6_firewall-restart
   else
      ulog dhcpv6c status "$2 is not currently on $1. Nothing to expire"
   fi
}
service_init ()
{
   eval `utctx_cmd get dhcpv6c_enable dhcpv6c_duid lan_ifname guest_lan_ifname`
   if [ -z "$SYSCFG_dhcpv6c_enable" ] ; then
      SYSCFG_dhcpv6c_enable=3
   fi
   SYSEVENT_current_wan_ipv6_ifname=`sysevent get current_wan_ipv6_ifname`
   SYSEVENT_wan_ppp_ifname=`sysevent get wan_ppp_ifname`
   if [ "up" = "`sysevent get ppp_status`" ] ; then
      WAN_INTERFACE_NAME=$SYSEVENT_wan_ppp_ifname
   else
      WAN_INTERFACE_NAME=$SYSEVENT_current_wan_ipv6_ifname
   fi
}
service_start ()
{
   if [ "$SYSCFG_dhcpv6c_enable" = "0" ] 
   then
      ulog dhcpv6c status "DHCPv6 client is disabled. Ignoring service start request"
      return
   fi
   if [ "$SYSCFG_dhcpv6c_enable" = "1" -o "$SYSCFG_dhcpv6c_enable" = "3" ] ; then
      NEED_PD=1
   else
      NEED_PD=0
   fi
   if [ "$SYSCFG_dhcpv6c_enable" = "2" -o "$SYSCFG_dhcpv6c_enable" = "3" ] ; then
      NEED_NA=1
   else
      NEED_NA=0
   fi
   if [ -n "$DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE" ] ; then
      if [ "$SYSCFG_dhcpv6c_enable" = "3" ] ; then
         SYSEVENT_dhcpv6_client_current_config=`sysevent get dhcpv6_client_current_config`
         if [ -z "$SYSEVENT_dhcpv6_client_current_config" ] ; then
            sysevent set dhcpv6_client_current_config 3
         elif [ "$SYSEVENT_dhcpv6_client_current_config" = "3" ] ; then
            sysevent set dhcpv6_client_current_config 1
            NEED_PD=1
            NEED_NA=0
            ulog dhcpv6c status "DHCPv6 client cannot get NA and PD. Trying PD only"
         elif [ "$SYSEVENT_dhcpv6_client_current_config" = "1" ] ; then
            sysevent set dhcpv6_client_current_config 2
            NEED_PD=0
            NEED_NA=1
            ulog dhcpv6c status "DHCPv6 client cannot get NA and PD. Trying NA only"
         else
            sysevent set dhcpv6_client_current_config 4
            NEED_PD=1
            NEED_NA=1
         fi
      fi
   fi
   if [ "$NEED_PD" = "1" ] ; then
      PD_FLAG=" -P "
   else
      PD_FLAG=""
   fi
   if [ "$NEED_NA" = "1" ] ; then
      NA_FLAG=" -N "
   else
      NA_FLAG=""
   fi
   if [ "$NEED_NA" = "0" -a "$NEED_PD" = "0" ] ; then
      ulog dhcpv6c status "DHCPv6 client is not needed for IA_NA nor IA_PD. Ignoring startup request of DHCPv6 client"
   fi
   wait_till_end_state ${SERVICE_NAME}
   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "started" != "$STATUS" ] 
   then
      sysevent set ${SERVICE_NAME}-errinfo 
      sysevent set ${SERVICE_NAME}-status starting
      restore_dhcpv6c_duid
      sysevent set ${SYSCFG_lan_ifname}_ipv6_deprecated_but_valid_delegated_address
      sysevent set ${SYSCFG_guest_lan_ifname}_ipv6_deprecated_but_valid_delegated_address
     
      prepare_dhcpv6_client_config_file $WAN_INTERFACE_NAME
      prepare_leases_file
      prepare_dhcpv6_callback_file
      ulog dhcpv6c status "Calling $DHCPV6_BINARY -6 $PD_FLAG $NA_FLAG -pf $DHCPV6_PID_FILE -cf $DHCPV6_CONF_FILE -lf $LEASES_FILE -sf $SCRIPT_FILE $WAN_INTERFACE_NAME &"
      $DHCPV6_BINARY -6 $PD_FLAG $NA_FLAG -pf $DHCPV6_PID_FILE -cf $DHCPV6_CONF_FILE -lf $LEASES_FILE -sf $SCRIPT_FILE $WAN_INTERFACE_NAME & >> $LOG 2>&1
      if [ -n "$DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE" ] ; then
         prepare_dhcpv6_check_progress_script
      fi
      check_err_exit $? "$DHCPV6_BINARY result"
      sysevent set ${SERVICE_NAME}-status started
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
      HAS_NA=`sysevent get ${WAN_INTERFACE_NAME}_dhcpv6_ia_na`
      HAS_PD=`sysevent get ipv6_delegated_prefix`
      if [ -f $LEASES_FILE ] && [ -n "$HAS_NA" -o -n "$HAS_PD" ] ; then
         if [ -n "$HAS_PD" ] ; then
	        PD_FLAG=" -P "
	     else
	        PD_FLAG=""
	     fi
	     if [ -n "$HAS_NA" ] ; then
	        NA_FLAG=" -N "
	     else
	        NA_FLAG=""
	     fi
         $DHCPV6_BINARY -6 -r $PD_FLAG $NA_FLAG -lf $LEASES_FILE -cf $DHCPV6_CONF_FILE $WAN_INTERFACE_NAME & >> $LOG 2>&1
      fi
      if [ -n "$DHCPV6_CLIENT_ACCEPT_INCOMPLETE_LEASE" ] ; then
         remove_dhcpv6_check_progress_script
      fi
      sleep 2
      killall dhclient
      echo -n > $DHCPV6_CONF_FILE
      rm -f $LEASES_FILE
      sysevent set wan_dhcpv6_lease
      
      sysevent set ${SERVICE_NAME}-status stopped
      sysevent set dhcpv6c_bound 0
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
      if [ -z "$2" -o "$2" != "ACCEPT_INCOMPLETE_LEASE" ] ; then
         sysevent set dhcpv6_client_current_config
         SYSEVENT_dhcpv6_client_current_config=
      fi
      service_start
      ;;
   dhcpv6_client_expire_deprecated_address)
      if [ -n "$2" ] ; then
         INT=`echo $2 | cut -d' ' -f 1`
         ADDR=`echo $2 | cut -d' ' -f 2`
         if [ -n "$INT" -a -n "$ADDR" ] ; then
            force_expire_deprecated_address $INT $ADDR
            ulog dhcpv6c status "Expired deprecated address $ADDR on $INT"
         fi
      fi
      ;;
   *)
      echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
      exit 3
      ;;
esac
