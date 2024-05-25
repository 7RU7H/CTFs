#!/bin/sh

#------------------------------------------------------------------
# © 2013 Belkin International, Inc. and/or its affiliates. All rights reserved.
#------------------------------------------------------------------

source /etc/init.d/ulog_functions.sh
source /etc/init.d/event_handler_functions.sh

#------------------------------------------------------------------
# name of this service
# This name MUST correspond to the registration of this service.
# Usually the registration occurs in /etc/registration.
# The registration code will refer to the path/name of this handler
# to be activated upon default events (and other events)
#------------------------------------------------------------------
SERVICE_NAME="openvpn"
NAMESPACE=$SERVICE_NAME

SIG="MCF1"
TIMESTAMP="`date`"
HSTNAME="`hostname`"

OVPN_CONF_DIR="/var/config/openvpn"
CONF_FILE="/tmp/etc/openvpn.conf"
TEMP_DIR="/tmp/openvpn"
OVPN_STATIC_DIR="/tmp/vpn"
#AUTH_SCRIPT="/etc/init.d/openvpn/vpn_usr_auth.sh"
AUTH_SCRIPT="/tmp/vpn/vpn_usr_auth.sh"
AUTH_FILE="/tmp/vpn/user_auth"
STATUS_LOG="/tmp/openvpn_status.log"
SERVER_FILES_TARBALL="/etc/init.d/openvpn/easy_rsa.tgz"


CLIENT_CONFIG="clientconfig.ovpn"

CLIENT_CONFIG_FILE="${OVPN_STATIC_DIR}/${CLIENT_CONFIG}"


#-----------------------------------------------------------------
# SYSCFG VALUES USED
#
# vpn_max_users -- applies to all VPN services
# openvpn::enabled
# openvpn::localip
# openvpn::ip_start_addr ( replace openvpn::serverip )
# openvpn::port
# openvpn::proto
# openvpn::mtu
# openvpn::user_#_name
# openvpn::user_#_pass
# openvpn::debug
# openvpn::client_config_file
#-----------------------------------------------------------------
#-----------------------------------------------------------------
# PPTPD DEFAULT VALUES
#
# dfc_enabled="0"
dfc_debug="0"
dfc_localip="192.168.1.1"
dfc_serverip="172.19.13.0"
dfc_vpn_is_gateway="0"
dfc_port="1194"
dfc_proto="udp"
dfc_mtu="6000"
dfc_user_name="hansolo"
dfc_user_pass="chewbacca"
dfc_max_users="25"
dfc_max_conns="5"
dfc_debug="2"
dfc_client_config="$CLIENT_CONFIG_FILE"
#-----------------------------------------------------------------

#-----------------------------------------------------------------
# create_conf_file
#
# This function creates the OpenVPN server config file
#-----------------------------------------------------------------

create_conf_file () {

# alway recreate the conf file for now.
#	if [ ! -f "$CLIENT_CONFIG_FILE" ] ; then
		create_client_www_download
#	fi
	if [ ! -d ${OVPN_CONF_DIR} ]  ; then
		mkdir -p "${OVPN_CONF_DIR}"
	fi
#	reate_client_server_keys
	
#	mkdir -p `dirname "$CONF_FILE"`
	LOCAL_IP="`syscfg get openvpn::hostname`"
	if [ ! "$LOCAL_IP" ] ; then
		LOCAL_IP="`sysevent get ipv4_wan_ipaddr`"
		if [ ! "$LOCAL_IP" ] ; then
			WAN_DEV="`syscfg get wan_physical_ifname`"
			LOCAL_IP="`ifconfig $WAN_DEV | grep "inet addr:" | cut -d':' -f2 | cut -d' ' -f1`"
			if [ ! "$LOCAL_IP" ] ; then
				LOCAL_IP="$dfc_localip"
			fi
		fi
		MY_IP=$LOCAL_IP
	else
		MY_IP="`sysevent get ipv4_wan_ipaddr`"
	fi
	
	if [ "`syscfg get ${NAMESPACE}::localip`" != "$LOCAL_IP" ] ; then
		echo "${NAMESPACE} detected a change of WAN ip - setting sysevent ${NAMESPACE}-WAN-IP-Change" >> /dev/console
		sysevent set ${NAMESPACE}-WAN-IP-Change $LOCAL_IP
	fi
	syscfg set ${NAMESPACE}::localip "$LOCAL_IP"
	
	DEBUG="`syscfg get ${NAMESPACE}::debug`"
	if [ ! "$DEBUG" ] ; then
		DEBUG="$dfc_debug"
		syscfg set ${NAMESPACE}::debug "$DEBUG"
	fi
	
	PORT="`syscfg get ${NAMESPACE}::port`"
	if [ ! "$PORT" ] ; then
		PORT="$dfc_port"
#		echo "$SERVICE_NAME using default port $PORT" >> /dev/console
		syscfg set ${NAMESPACE}::port "$PORT"
	fi
	PROTO="`syscfg get ${NAMESPACE}::proto`"
	if [ ! "$PROTO" ] ; then
		PROTO="$dfc_proto"
#		echo "$SERVICE_NAME using default port $PORT" >> /dev/console
		syscfg set ${NAMESPACE}::proto "$PROTO"
	fi
	MTU="`syscfg get ${NAMESPACE}::mtu`"
	if [ ! "$MTU" ] ; then
		PROTO="$dfc_mtu"
#		echo "$SERVICE_NAME using default port $PORT" >> /dev/console
		syscfg set ${NAMESPACE}::mtu "$mtu"
	fi
#	SRVRIP="`syscfg get ${NAMESPACE}::serverip`"
	SRVRIP="`syscfg get ${NAMESPACE}::ip_start_addr`"
	if [ ! "$SRVRIP" ] ; then
		SRVRIP="$dfc_serverip"
#		echo "$SERVICE_NAME using server ip $SRVRIP" >> /dev/console
		syscfg set ${NAMESPACE}::ip_start_addr "$SRVRIP"
	fi
	C_CFG_FILE="`syscfg get ${NAMESPACE}::client_config`"
	if [ ! "$C_CFG_FILE" ] ; then
		C_CFG_FILE="$dfc_client_config"
		syscfg set ${NAMESPACE}::client_config "$C_CFG_FILE"
	fi
    MAX_CONNS="`syscfg get vpn_max_connections`"
    if [ ! "$MAX_CONNS" ] ; then
        MAX_CONNS="$dfc_max_conns"
#       echo "$SERVICE_NAME using max connections $MAX_CONNS" >> /dev/console
        syscfg set vpn_max_connections "$MAX_CONNS"
    fi
	LAN_IP="`syscfg get lan_ipaddr`"
	LAN_NETMASK="`syscfg get lan_netmask`"
	PROTO="`syscfg get ${NAMESPACE}::proto`"
	MTU="`syscfg get ${NAMESPACE}::mtu`"
	PREFIX="`ipcalc -p $LAN_NETMASK | cut -d '=' -f2`"
	NETWORK="`ipcalc -n $LAN_IP/$PREFIX| cut -d '=' -f2`"
	
	if [ "$PROTO" == "tcp" ] ; then
		PROTO="tcp-server"
	fi
	IPPOOL="`echo $SRVRIP | cut -d'.' -f1-3`"
	LAN_NET="`echo $LAN_IP | cut -d'.' -f1-3`"
	
	# set up the range of IP addresses in the pool
	IPPOOL_START="1"
	IPPOOL_END="`expr $IPPOOL_START + $MAX_CONNS`"
	IPPOOL_START="`expr $IPPOOL_START + 1`"
	
	VPN_IS_GATEWAY="`syscfg get ${NAMESPACE}::vpn_is_gateway`"
	if [ ! "$VPN_IS_GATEWAY" ] ; then
		VPN_IS_GATEWAY="$dfc_vpn_is_gateway"
		syscfg set ${NAMESPACE}::vpn_is_gateway "$VPN_IS_GATEWAY"
	fi
	
#------------------------------------------------
	cat << EOM > $CONF_FILE
# Auto-generated configuration file from $HSTNAME
# $TIMESTAMP $SIG
local $MY_IP
# must open up this port on the firewall
port $PORT
proto $PROTO
dev tun
topology subnet
mode server
tls-server
tun-mtu $MTU
# the 'fragment 0' below *may* be used to improve performance on some platforms
# but is incompatible with some android openvpn applications
# fragment 0
mssfix 0
push "topology subnet"
ifconfig $IPPOOL.1 255.255.255.0
ifconfig-pool $IPPOOL.$IPPOOL_START $IPPOOL.$IPPOOL_END 255.255.255.0
#push "redirect-gateway def1"
push "route-gateway $IPPOOL.1"
#push "route-gateway $MY_IP"
push "route $LAN_NET.0 $LAN_NETMASK"
auth-user-pass-verify $AUTH_SCRIPT via-file
keepalive 10 120
max-clients $MAX_CONNS
user  nobody
group nobody
script-security 3
duplicate-cn
dh /etc/init.d/openvpn/dh1024.pem
ca $OVPN_CONF_DIR/ca.crt
cert $OVPN_CONF_DIR/server.crt
key $OVPN_CONF_DIR/server.key  # This file should be kept secret
ns-cert-type server
persist-key
persist-tun
status $STATUS_LOG
# use one of these to override logging to syslog
;log         openvpn.log
log-append  /var/log/messages
verb $DEBUG
client-cert-not-required
username-as-common-name
EOM
#------------------------------------------------

if [ "$VPN_IS_GATEWAY" == "1" ] ; then
	echo "$SERVICE_NAME will be used as default gateway" >> /dev/console
	sed -i 's/#push "r/push "r/g' $CONF_FILE
fi

	echo "$SERVICE_NAME conf file created" >> /dev/console



}  


create_client_www_download() {
#  echo "creating openvpn_client.tgz" >> /dev/console
#  rm -rf /tmp/openvpn_client
#  rm -rf /tmp/www/openvpn_client.tgz
  if [ ! -d ${OVPN_STATIC_DIR} ]  ; then
		mkdir -p "${OVPN_STATIC_DIR}"
	fi
  cp ${OVPN_CONF_DIR}/client.conf ${OVPN_STATIC_DIR}/client.conf
#   echo "USERNAME" > ${OVPN_STATIC_DIR}/openvpn_login.txt
#   echo "PASSWORD" >> ${OVPN_STATIC_DIR}/openvpn_login.txt
#  SERVER_IP="`syscfg get openvpn::localip`"
  
  SERVER_IP="`syscfg get openvpn::hostname`"
	if [ ! "$SERVER_IP" ] ; then
		SERVER_IP="`sysevent get ipv4_wan_ipaddr`"
		if [ ! "$SERVER_IP" ] ; then
			WAN_DEV="`syscfg get wan_physical_ifname`"
			SERVER_IP="`ifconfig $WAN_DEV | grep "inet addr:" | cut -d':' -f2 | cut -d' ' -f1`"
			if [ ! "$SERVER_IP" ] ; then
				SERVER_IP="$dfc_localip"
			fi
		fi
	fi
	MY_IP=$SERVER_IP
	
	# syscfg set ${NAMESPACE}::localip "$LOCAL_IP"
	
  SERVER_PORT="`syscfg get openvpn::port`"
  SERVER_PROTO="`syscfg get openvpn::proto`"
  SERVER_MTU="`syscfg get openvpn::mtu`"
  SERVER_REMOTE="$MY_IP $SERVER_PORT"
  sed -e "s/remote XXX.XXX.XXX.XXX YYYY/remote $SERVER_REMOTE/g" \
      -e "s/proto udp/proto $SERVER_PROTO/g" \
      -e "s/tun-mtu 6000/tun-mtu $SERVER_MTU/g" \
      -i ${OVPN_STATIC_DIR}/client.conf
	if [ ! -d "/tmp/www" ] ; then
		mkdir -p /tmp/www
	fi
	here=`pwd`
	echo "<ca>" >>  ${OVPN_STATIC_DIR}/client.conf
	cat ${OVPN_CONF_DIR}/ca.crt >> ${OVPN_STATIC_DIR}/client.conf
	echo "</ca>" >> ${OVPN_STATIC_DIR}/client.conf
	echo "<cert>" >>  ${OVPN_STATIC_DIR}/client.conf
	cat ${OVPN_CONF_DIR}/client.crt >> ${OVPN_STATIC_DIR}/client.conf
	echo "</cert>" >> ${OVPN_STATIC_DIR}/client.conf
	echo "<key>" >>  ${OVPN_STATIC_DIR}/client.conf
	cat ${OVPN_CONF_DIR}/client.key >> ${OVPN_STATIC_DIR}/client.conf
	echo "</key>" >> ${OVPN_STATIC_DIR}/client.conf
	echo "" >> ${OVPN_STATIC_DIR}/client.conf
	echo "# Auto-generated configuration file from $HSTNAME" >> ${OVPN_STATIC_DIR}/client.conf
	echo "# $TIMESTAMP $SIG" >> ${OVPN_STATIC_DIR}/client.conf
	cp ${OVPN_STATIC_DIR}/client.conf ${CLIENT_CONFIG_FILE}
	rm -rf ${OVPN_STATIC_DIR}/client.conf
#	rm -rf $TEMP_DIR/easy_rsa
}

remove_client_www_download() {
#	echo "removing link for openvpn_client.tgz" >> /dev/console
	rm -rf /tmp/www/openvpn_client.tgz
}


add_ip_tables_rules () {
	WAN_IP="`sysevent get ipv4_wan_ipaddr`"
	WAN_IF="`syscfg get wan_physical_ifname`"
	PORT="`syscfg get ${NAMESPACE}::port`"
	/sbin/iptables -A FORWARD -p tcp -i $WAN_IF -d $WAN_IP --dport $PORT -j ACCEPT
	/sbin/iptables -t nat -A PREROUTING -p tcp -d $WAN_IP --dport $PORT -j DNAT --to-destination $WAN_IP:$PORT
	/sbin/iptables -A FORWARD -p udp -i $WAN_IF -d $WAN_IP --dport $PORT -j ACCEPT
	/sbin/iptables -t nat -A PREROUTING -p udp -d $WAN_IP --dport $PORT -j DNAT --to-destination $WAN_IP:$PORT
	# for ssh
	/sbin/iptables -A FORWARD -p tcp -i $WAN_IF -d $WAN_IP --dport 22 -j ACCEPT
	/sbin/iptables -t nat -A PREROUTING -p tcp -d $WAN_IP --dport 22 -j DNAT --to-destination $WAN_IP:22
	echo "iptables rules added for ${NAMESPACE}" >> /dev/console
}

del_ip_tables_rules () {
	WAN_IP="`sysevent get ipv4_wan_ipaddr`"
	WAN_IF="`syscfg get wan_physical_ifname`"
	PORT="`syscfg get ${NAMESPACE}::port`"
	/sbin/iptables -D FORWARD -p tcp -i $WAN_IF -d $WAN_IP --dport $PORT -j ACCEPT
	/sbin/iptables -t nat -D PREROUTING -p tcp -d $WAN_IP --dport $PORT -j DNAT --to-destination $WAN_IP:$PORT
	/sbin/iptables -D FORWARD -p udp -i $WAN_IF -d $WAN_IP --dport $PORT -j ACCEPT
	/sbin/iptables -t nat -D PREROUTING -p udp -d $WAN_IP --dport $PORT -j DNAT --to-destination $WAN_IP:$PORT
	# for ssh 
	/sbin/iptables -D FORWARD -p tcp -i $WAN_IF -d $WAN_IP --dport 22 -j ACCEPT
	/sbin/iptables -t nat -D PREROUTING -p tcp -d $WAN_IP --dport 22 -j DNAT --to-destination $WAN_IP:22
	echo "iptables rules removed for ${NAMESPACE}" >> /dev/console
}

create_auth_file () {
	if [ ! -d `dirname "$AUTH_FILE"` ]  ; then
		mkdir -p `dirname "$AUTH_FILE"`
	fi
	echo "" > "$AUTH_FILE"
#	DEFAULT_USER="1"
	for i in `seq 1 $MAX_USERS`
	do
		USER="`syscfg get openvpn::user_${i}_name`"
		if [ "$USER" != "" ] ; then
			DEFAULT_USER="0"
			VPN_PASSWORD="`syscfg get openvpn::user_${i}_pass`"
			echo "$USER $VPN_PASSWORD" >> $AUTH_FILE
		fi
	done
	if [ "$DEFAULT_USER" == "1" ] ; then
		VPN_USER="$dfc_user_name"
#		echo "$SERVICE_NAME using default vpn user $VPN_USER"
		VPN_PASSWORD="$dfc_user_pass"
#		echo "$SERVICE_NAME using default vpn password $VPN_PASSWORD"
		echo "$VPN_USER $VPN_PASSWORD" >> $AUTH_FILE
		# echo "$VPN_PASSWORD" >> $AUTH_FILE
  fi
  sed -i '/^$/d' $AUTH_FILE
#  echo "$SERVICE_NAME auth file created" >> /dev/console
}

create_client_server_keys() {
    reset_val=`sysevent get factory_reset`
	if [ ! "`syscfg get create_openvpn_uniq_files`" ] && [ "true" = $reset_val  ]; then
		echo "creating server keys for OpenVPN" >> /dev/console
#---------------------------------------------------------
        cat << EOM > ${OVPN_CONF_DIR}/client.conf 
client
dev tun
proto udp
remote XXX.XXX.XXX.XXX YYYY
tun-mtu 6000
# fragment 0 can be used to improve performance in some instances but
# breaks compatibility with some Android apps
# fragment 0
mssfix 0
resolv-retry infinite
nobind
persist-key
persist-tun
ns-cert-type server
auth-user-pass
verb 3
EOM
#---------------------------------------------
		HERE="`pwd`"
		if [ ! -d "$TEEMP_DIR" ] ; then
			mkdir -p $TEMP_DIR
		fi
        export OPENSSL_CONF=/etc/certs/openssl.cnf
		tar -zxvf "$SERVER_FILES_TARBALL" -C "$TEMP_DIR"
		cd "$TEMP_DIR/easy_rsa/"
        sed -e "s/export KEY_CN=.*/export KEY_CN=Mamba/g" \
            -e "s/export KEY_NAME=.*/export KEY_NAME=BlackMamba/g" \
            -e "s/export KEY_OU=.*/export KEY_OU=Belkin/g" \
            -i vars
		source vars
		./clean-all
		HOME=. ./build-ca --batch
		HOME=. ./build-key-server --batch server
        export KEY_CN=client
        HOME=. ./build-key --batch client
		# do not generate DH file because it takes too long.
		#./build-dh
		cp keys/ca.crt ${OVPN_CONF_DIR}/ca.crt
		cp keys/server.crt ${OVPN_CONF_DIR}/server.crt
		cp keys/server.key ${OVPN_CONF_DIR}/server.key
        cp keys/client.crt ${OVPN_CONF_DIR}/client.crt
        cp keys/client.key ${OVPN_CONF_DIR}/client.key
		cp /etc/init.d/openvpn/dh1024.pem ${OVPN_CONF_DIR}/
		echo "creating server keys for OpenVPN - DONE" >> /dev/console
		cd $HERE
		rm -rf "$TEMP_DIR/easy_rsa"
	else
		echo "server keys for OpenVPN already exist" >> /dev/console
		#rm -rf "$TEMP_DIR/easy_rsa/"
	fi
	syscfg set create_openvpn_uniq_files 1
}


#----------------------------------------------------------------------------------------
#                     Default Event Handlers
#
# Each service has three default events that it should handle
#    ${SERVICE_NAME}-start
#    ${SERVICE_NAME}-stop
#    ${SERVICE_NAME}-restart
#
# For each case:
#   - Clear the service's errinfo
#   - Set the service's status 
#   - Do the work
#   - Check the error code (check_err will set service's status and service's errinfo upon error)
#   - If no error then set the service's status
#----------------------------------------------------------------------------------------

#-------------------------------------------------------------------------------------------
#  function   : service_init
#  - optional procedure to retrieve syscfg configuration data using utctx_cmd
#    this is a protected way of accessing syscfg
#-------------------------------------------------------------------------------------------
service_init ()
{
    if [ ! -d ${OVPN_CONF_DIR} ]  ; then
		mkdir -p "${OVPN_CONF_DIR}"
	fi
	create_client_server_keys

#	echo "$SERVICE_NAME service_init"
	if [ ! -d "/dev/net" ] ; then
#		echo "creating /dev/net/tun" >> /dev/console
		mkdir /dev/net
		mknod /dev/net/tun c 10 200
		chmod 600 /dev/net/tun
	fi
	if [ ! -d "/tmp/vpn" ] ; then
		mkdir -p /tmp/vpn
	fi
	if [ "`syscfg get ${NAMESPACE}::ip_start_addr`" == "" ] ; then
		syscfg set ${NAMESPACE}::ip_start_addr $dfc_serverip
	fi
	if [ "`syscfg get ${NAMESPACE}::port`" == "" ] ; then
		syscfg set ${NAMESPACE}::port $dfc_port
	fi
	if [ "`syscfg get ${NAMESPACE}::proto`" == "" ] ; then
		syscfg set ${NAMESPACE}::proto $dfc_proto
	fi
	if [ "`syscfg get ${NAMESPACE}::mtu`" == "" ] ; then
		syscfg set ${NAMESPACE}::mtu $dfc_mtu
	fi
	if [ "`syscfg get ${NAMESPACE}::localip`" == "" ] ; then
		syscfg set ${NAMESPACE}::localip "`sysevent get ipv4_wan_ipaddr`"
	fi
	if [ "`syscfg get ${NAMESPACE}::client_config`" == "" ] ; then
		C_CFG_FILE="$dfc_client_config"
		syscfg set ${NAMESPACE}::client_config "$C_CFG_FILE"
	fi
	create_client_www_download
	create_auth_file
	if [ "`syscfg get ${NAMESPACE}::enabled`" == "1" ] ; then
		echo "$SERVICE_NAME running $1"
	else
#		echo "$SERVICE_NAME disabled in syscfg"
		exit 1
	fi
}

#-------------------------------------------------------------------------------------------
#  function   : service_start
#  - Set service-status to starting
#  - Add code to read normalized configuration data from syscfg and/or sysevent 
#  - Create configuration files for the service
#  - Start any service processes 
#  - Set service-status to started
#
#  check_err will check for a non zero return code of the last called function
#  set the service-status to error, and set the service-errinfo, and then exit
#-------------------------------------------------------------------------------------------
service_start ()
{

	# Fix for: LSWF-2597, do not start OpenVPN in bridge mode
	bridge_mode="`syscfg get bridge_mode`"
	if [ "$bridge_mode" == "0" ]  ; then
		# wait_till_end_state will wait a reasonable amount of time waiting for ${SERVICE_NAME}
		# to finish transitional states (stopping | starting)
		wait_till_end_state ${SERVICE_NAME}
		UPTIME="`uptime | cut -d ' '  -f 4 | cut -d ':' -f1`"
	# 	if [ "$UPTIME" -gt "2" ] ; then
			sysevent set ${SERVICE_NAME}-delay_start done
	# 	fi
	# 	if [ "`sysevent get ${SERVICE_NAME}-delay_start`" == "" ] ; then
	# 		echo "delay start of OpenVPN until settled" >> /dev/console
	# 		sysevent set ${SERVICE_NAME}-delay_start waiting
	# 		sleep 15
	# 		sysevent set ${SERVICE_NAME}-delay_start done
	# 	fi
	# 	if [ "`sysevent get ${SERVICE_NAME}-delay_start`" == "waiting" ] ; then
	# 		# don't double start
	# 		exit
	# 	fi
		if [ "`sysevent get ${SERVICE_NAME}-delay_start`" == "done" ] ; then
			MAX_USERS="`syscfg get vpn_max_users`"
			if [ ! "$MAX_USERS" ] ; then
				MAX_USERS="$dfc_max_users"
				syscfg set vpn_max_users "$MAX_USERS"
			fi
			service_init
			STATUS=`sysevent get ${SERVICE_NAME}-status`
			if [ "started" != "$STATUS" ] ; then
				echo "starting ${SERVICE_NAME}" >> /dev/console
				sysevent set ${SERVICE_NAME}-errinfo 
				sysevent set ${SERVICE_NAME}-status starting
				create_conf_file
				# create_auth_file  # done in service_init
				if [ ! -f "$CLIENT_CONFIG_FILE" ] ; then
					create_client_www_download
				fi
				cp /etc/init.d/openvpn/vpn_usr_auth.sh $AUTH_SCRIPT
				# chown nobody:nobody $AUTH_SCRIPT
				chmod +x $AUTH_SCRIPT
				sysevent set firewall-restart
				# add_ip_tables_rules
				/sbin/openvpn $CONF_FILE &
				check_err $? "Couldnt handle start"
				sysevent set ${SERVICE_NAME}-status started
							ulog ${SERVICE_NAME} status "now started"
			fi
			if [ -f "/usr/local/sbin/OpenVPNconf.cgi" ] ; then
				if [ ! -f "/tmp/www/OpenVPNconf.cgi" ] ; then
					echo "creating dev link for OpenVPN web configuration..."
					mkdir -p /tmp/www
					cp /usr/local/sbin/OpenVPNconf.cgi /tmp/www/OpenVPNconf.cgi
				fi
			fi
		fi
	else
		echo "OpenVPN does not run in bride mode" >> /dev/console
	fi
}

#-------------------------------------------------------------------------------------------
#  function   : service_stop
#  - Set service-status to stopping
#  - Stop any service processes 
#  - Delete configuration files for the service
#  - Set service-status to stopped
#
#  check_err will check for a non zero return code of the last called function
#  set the service-status to error, and set the service-errinfo, and then exit
#-------------------------------------------------------------------------------------------
service_stop ()
{
   # wait_till_end_state will wait a reasonable amount of time waiting for ${SERVICE_NAME}
   # to finish transitional states (stopping | starting)
   wait_till_end_state ${SERVICE_NAME}

   STATUS=`sysevent get ${SERVICE_NAME}-status`
   if [ "stopped" != "$STATUS" ] ; then
			echo "stopping ${SERVICE_NAME}" >> /dev/console
      sysevent set ${SERVICE_NAME}-errinfo 
      sysevent set ${SERVICE_NAME}-status stopping
      remove_client_www_download
      rm -rf $AUTH_SCRIPT
      # del_ip_tables_rules
      killall -9 openvpn
      check_err $? "Couldnt handle stop"
      sysevent set ${SERVICE_NAME}-status stopped
      ulog ${SERVICE_NAME} status "now stopped"
      if [ "`syscfg get openvpn::enabled`" != "1" ] ; then
				sysevent set firewall-restart
      fi
   fi
}

#-------------------------------------------------------------------------------------------
# Entry
# The first parameter $1 is the name of the event that caused this handler to be activated
# The second parameter $2 is the value of the event or "NULL" if there is no value
# The other parameters are given if parameters passing was defined in the sysevent async call
#-------------------------------------------------------------------------------------------

# service_init 

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
   #----------------------------------------------------------------------------------
   # Add other event entry points here
   #----------------------------------------------------------------------------------
    wan-started)
        service_stop
        service_start
        ;;
    lan-started)
        if [ "`sysevent get wan-status`" == "started" ] ; then
            service_stop
            service_start
        fi
        ;;
	*)
		echo "error : $1 unknown" > /dev/console 
		echo "Usage: $SERVICE_NAME [ ${SERVICE_NAME}-start | ${SERVICE_NAME}-stop | ${SERVICE_NAME}-restart]" > /dev/console
		exit 3
		;;
esac
