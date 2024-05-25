DHCP_LEASEFILE=/etc/dnsmasq.leases
STATICIP_HOSTS=/tmp/staticIP_hosts
DETECTED_HOSTS=/tmp/detected_hosts
addr_get_ipv4_from_mac()
{
    local mac
    local match
    mac=$1
    ADDR_IPV4=""
    if [ -f $DHCP_LEASEFILE ]
    then
        match=`grep -i $mac $DHCP_LEASEFILE`
        if [ "$?" -eq 0 ]
        then
            ADDR_IPV4=`echo $match | awk '{print $3}'`
            return
        fi
    fi
    if [ -f $STATICIP_HOSTS ]
    then
        match=`grep -i $mac $STATICIP_HOSTS`
        if [ "$?" -eq 0 ]
        then
            ADDR_IPV4=`echo $match | awk '{print $2}'`
            return
        fi
    fi
    if [ -f $DETECTED_HOSTS ]
    then
        match=`grep -i $mac $DETECTED_HOSTS`
        if [ "$?" -eq 0 ]
        then
            ADDR_IPV4=`echo $match | awk '{print $3}'`
            return
        fi
    fi
}
addr_get_mac_from_ipv4()
{
    local ipv4 
    local match
    ipv4=$1
    ADDR_MAC=""
    if [ -f $DHCP_LEASEFILE ]
    then
        match=`grep $ipv4 $DHCP_LEASEFILE`
        if [ "$?" -eq 0 ]
        then
            ADDR_MAC=`echo $match | awk '{print $2}'`
            return
        fi
    fi
    if [ -f $STATICIP_HOSTS ]
    then
        match=`grep $ipv4 $STATICIP_HOSTS`
        if [ "$?" -eq 0 ]
        then
            ADDR_MAC=`echo $match | awk '{print $1}'`
            return
        fi
    fi
    if [ -f $DETECTED_HOSTS ]
    then
        match=`grep $ipv4 $DETECTED_HOSTS`
        if [ "$?" -eq 0 ]
        then
            ADDR_MAC=`echo $match | awk '{print $2}'`
            return
        fi
    fi
}
