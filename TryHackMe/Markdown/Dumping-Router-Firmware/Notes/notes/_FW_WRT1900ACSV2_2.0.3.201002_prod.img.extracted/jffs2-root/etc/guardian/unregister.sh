#!/bin/sh

IPT_RUN=/etc/init.d/ipt_run.sh

# $1(table)
# $2(rule)
delete_if_exists()
{
	iptables-save -t $1 2> /dev/null | grep -e "$2" |
	while read line; do
		$IPT_RUN iptables -t $1 `echo $line | sed s/"-A"/"-D"/`;
	done
}

#delete_if_exists "nat" "-A prerouting_fromlan_plugins -j prerouting_fromlan_plugin_pc"
#delete_if_exists "nat" "-A prerouting_fromguest_plugins -j prerouting_fromguest_plugin_pc"
#delete_if_exists "filter" "-A lan2wan_plugins -j lan2wan_plugin_pc"
#delete_if_exists "filter" "-A wan2lan_plugins -j wan2lan_plugin_pc"
#delete_if_exists "filter" "-A lan2self_plugins -j lan2self_plugin_pc"
#delete_if_exists "filter" "-A self2lan_plugins -j self2lan_plugin_pc"

# $1(table)
# $2(rule)
cleanup ()
{
	while [ $? == 0 ]; do
		$IPT_RUN iptables -t $1 $2 2> /dev/null;
	done;
	return 0;
}

cleanup "mangle" "-D PREROUTING -j prerouting_pc"
cleanup "nat" "-D prerouting_fromlan_plugins -j prerouting_fromlan_plugin_pc"
cleanup "nat" "-D prerouting_fromguest_plugins -j prerouting_fromguest_plugin_pc"
cleanup "filter" "-D lan2wan_plugins -j lan2wan_plugin_pc"
cleanup "filter" "-D wan2lan_plugins -j wan2lan_plugin_pc"
cleanup "filter" "-D lan2self_plugins -j lan2self_plugin_pc"
cleanup "filter" "-D self2lan_plugins -j self2lan_plugin_pc"

exit 0
