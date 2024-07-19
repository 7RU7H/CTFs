#!/bin/bash

# Install NXTI Server Plugin
# --------------------------
BASEDIR=$(dirname $(readlink -f $0))
cd $BASEDIR

. $BASEDIR/../../../../var/xi-sys.cfg

echo "### NAGIOS XI TRAP INTERFACE INSTALL SCRIPT ###"

# Check whether we have sufficient privileges
if [ $(id -u) -ne 0 ]; then
    echo "This script needs to be run as root/superuser." >&2
    exit 0
fi
if [ -f installed.nxti ]; then
    echo "NXTI already installed"
    exit 0
fi

echo "Getting and running snmptrap integration"

echo ""
echo "========================================"
echo "Nagios XI SNMP Trap Support Installation"
echo "========================================"
echo ""

echo ""
echo "Installing Prerequisites"
echo ""

if [ "$distro" == "Ubuntu" ] || [ "$distro" == "Debian" ]; then
	apt-get install libnet-snmp-perl libsys-syslog-perl snmptrapd -y
else 
	yum install snmptt net-snmp net-snmp-perl -y
fi

echo ""
echo "Download & installing supporting files"
echo ""

# Install supporting files
cp includes/snmptrap-bins/* /usr/local/bin/
chmod a+x /usr/local/bin/snmptraphandling.py /usr/local/bin/snmpttconvertmib /usr/local/bin/addmib
touch /etc/snmp/nagios-check-storage
chown "snmptt:$nagiosgroup" /etc/snmp/nagios-check-storage
chmod 0664 /etc/snmp/nagios-check-storage

if [ -f /etc/snmp/snmp.conf ]; then
	sed -i 's/mibs :/mibs +ALL/' /etc/snmp/snmp.conf
fi

# SNMPv2-PDU contains bad trap definitions, but they're defined in other MIB 
# files for all known distros. This would normally be handled by changing the 
# MIB loading order (in snmp.conf) but some customers may have customized this already.
if [ -f /var/lib/snmp/mibs/ietf/SNMPv2-PDU ]; then
	mkdir -p /var/lib/snmp/mibs-extra/ietf
	mv /var/lib/snmp/mibs/ietf/SNMPv2-PDU /var/lib/snmp/mibs-extra/ietf/SNMPv2-PDU
fi

echo ""
echo "Updating snmptt.ini"
echo ""

# Make backups so that existing installations can rollback if necessary.
cp /etc/snmp/snmptt.ini /etc/snmp/snmptt.ini.bak

# Update settings
sed -i 's/mode[ ]*=[ ]*standalone/mode = daemon/' /etc/snmp/snmptt.ini
sed -i 's/\(^dns_enable.*\)0[0-9]*\([ \t#].*\)*$/\11\2/' /etc/snmp/snmptt.ini
sed -i 's/\(^strip_domain.*\)0[0-9]*\([ \t#].*\)*$/\11\2/' /etc/snmp/snmptt.ini
sed -i 's/\(^net_snmp_perl_enable.*\)0[0-9]*\([ \t#].*\)*$/\11\2/' /etc/snmp/snmptt.ini
sed -i 's/\(^syslog_level[ \t]*=[ \t]*\)[a-zA-Z]*\(.*\)/\1info\2/' /etc/snmp/snmptt.ini
sed -i 's/\(^log_enable[ \t]*=[ \t]*\)[0-9]*\(.*\)/\11/' /etc/snmp/snmptt.ini
sed -i 's/\(^log_system_enable[ \t]*=[ \t]*\)[0-9]*\(.*\)/\11/' /etc/snmp/snmptt.ini
sed -i 's/\(^unknown_trap_log_enable[ \t]*=[ \t]*\)[0-9]*\(.*\)/\11/' /etc/snmp/snmptt.ini

# Make NXTI quieter
sed -i 's/\(^syslog_enable[ \t]*=[ \t]*\)[0-9]*\(.*\)/\10/' /etc/snmp/snmptt.ini
sed -i 's/\(^syslog_system_enable[ \t]*=[ \t]*\)[0-9]*\(.*\)/\10/' /etc/snmp/snmptt.ini

# Received traps don't go to syslog
if [ "$distro" == "Debian" ] || [ "$distro" == "Ubuntu" ]; then
	if [ -f /lib/systemd/system/snmptrapd.service ]; then
		sed -i 's/\(^ExecStart=.*\)\(-Lsd\)\(.*\)/\1-Ln\3/' /lib/systemd/system/snmptrapd.service
	elif [ -f /etc/default/snmptrapd ]; then
		sed -i "s/\(^TRAPDOPTS='.*\)\(-Lsd\)\(.*\)/\1-Ln\3/" /etc/default/snmptrapd
	elif [ -f /etc/default/snmpd ]; then
		sed -i "s/\(^TRAPDOPTS='.*\)\(-Lsd\)\(.*\)/\1-Ln\3/" /etc/default/snmpd
	else
		sed -i "s/\(^TRAPDOPTS='.*\)\(-Lsd\)\(.*\)/\1-Ln\3/" /etc/init.d/snmptrapd || true;
		sed -i "s/\(^TRAPDOPTS='.*\)\(-Lsd\)\(.*\)/\1-Ln\3/" /etc/init.d/snmpd || true;
	fi
elif [ "$dist" == "el7" ]; then
	sed -i 's/\(^Environment=OPTIONS=\)\(.*\)/\1"-Ln"/' /usr/lib/systemd/system/snmptrapd.service
else
	sed -i 's/^# \(OPTIONS=".*\)\(-Lsd\)\(.*\)/\1-Ln\3/' /etc/sysconfig/snmptrapd
fi

echo ""
echo "Creating snmptrapd.conf"
echo ""

cat << EOF > /etc/snmp/snmptrapd.conf
disableAuthorization yes
traphandle default /usr/sbin/snmptthandler
EOF

echo ""
echo "Adding the snmptt user to the nagios and nagcmd groups"
echo ""

# Add the snmptt user to the nagios and nagcmd groups:
eval "$usermodbin" -a -G $nagioscmdgroup snmptt
eval "$usermodbin" -a -G $nagiosgroup snmptt 

echo ""
echo "Adding firewall rules"
echo ""

# Add firewall rule
set +e
if [ `command -v firewall-cmd` ]; then
	firewall-cmd --zone=public --add-port=162/udp --permanent
    firewall-cmd --reload
else
	status=$(service iptables status)
	if [ $? = 0 ]; then
		if ! grep -q -- '-A INPUT -p udp -m state --state NEW -m udp --dport 162 -j ACCEPT' /etc/sysconfig/iptables; then
			# determine information for the rules
			chain=$(iptables -L | awk '/^Chain.*INPUT/ {print $2; exit(0)}')
			rulenum=$((`iptables -L $chain | wc -l` - 2))

			# test to make sure we aren't using less than the minimum 1
			if [ "$rulenum" -lt 1 ]; then rulenum=1; fi

			# Add to iptables
			iptables -I "$chain" "$rulenum" -m state --state NEW -m udp -p udp --dport 162 -j ACCEPT
			service iptables save
		fi
	fi
fi
set -e

# Set up the snmptt & snmptrapd daemon to start automatically on boot,
#    as well as starting it now

echo ""
echo "Set up the snmptt daemon to start automatically on boot"
echo "as well as starting it now"
echo ""
if [ "$dist" == "el7" ] || [ "$dist" == "el8" ] || [ "$dist" == "el9" ]; then
	systemctl enable snmptt
	systemctl start snmptt.service
elif [ "$distro" == "Debian" ] || [ "$distro" == "Ubuntu" ]; then
	update-rc.d snmptt defaults
	update-rc.d snmpd defaults
else 
	chkconfig --add snmptt
	chkconfig snmptt on
fi

echo ""
echo "Set up the snmptrapd daemon to start automatically on boot"
echo "as well as starting it now"
echo ""
if [ "$dist" == "el7" ] || [ "$dist" == "el8" ] || [ "$dist" == "el9" ]; then
	systemctl enable snmptrapd
	systemctl start snmptrapd
elif [ "$distro" == "Debian" ] || [ "$distro" == "Ubuntu" ]; then

	if [ -f /etc/default/snmptrapd ]; then
		sed -i 's/^TRAPDRUN=no/TRAPDRUN=yes/' /etc/default/snmptrapd
	else
		sed -i 's/^TRAPDRUN=no/TRAPDRUN=yes/' /etc/default/snmpd
	fi

	service snmpd restart

	if [ -f /etc/init.d/snmptrapd ]; then
		update-rc.d snmptrapd defaults
		update-rc.d snmptrapd start 20 3 4 5
		service snmptrapd restart
	fi

	if [ `command -v systemctl` ]; then
		systemctl enable snmptrapd
		systemctl restart snmptrapd
	fi

else
	chkconfig --add snmptrapd
	chkconfig snmptrapd on
	service snmptrapd restart
fi

echo ""
echo "========================================"
echo "SNMP Trap Support Installation Complete!"
echo "========================================"

echo "Checking for existing snmptt.conf file"

# START EXISTING SNMPTT.CONF CHECK
if [ -s /etc/snmp/snmptt.conf.nxti ]; then
    echo "Existing snmptt.conf.nxti file found"
else
    echo "No existing snmptt.conf.nxti file was found, or the file was empty"
    echo "Adding snmptt.conf.nxti to snmptt.ini"
    awk '/\/etc\/snmp\/snmptt.conf$/ {print;print "/etc/snmp/snmptt.conf.nxti";next}1' /etc/snmp/snmptt.ini >> /etc/snmp/snmptt.ini.temp
    mv -f /etc/snmp/snmptt.ini.temp /etc/snmp/snmptt.ini
    touch /etc/snmp/snmptt.conf.nxti
fi

# Some systems (Debian 10) have no default snmptt.conf file
if [ ! -f /etc/snmp/snmptt.conf ]; then
	cp /etc/snmp/snmptt.conf.nxti /etc/snmp/snmptt.conf
fi
# END EXISTING SNMPTT.CONF CHECK

echo "Changing permissions for snmptt.conf"
touch /etc/snmp/snmptt_nxti.bak
chmod 664 /etc/snmp/snmptt_nxti.bak
chmod 664 /etc/snmp/snmptt.conf.nxti
chgrp $nagiosuser /etc/snmp/snmptt.conf.nxti
chgrp $nagiosuser /etc/snmp/snmptt_nxti.bak

echo "Editing snmptt.ini"

df_line=`awk '/date_format/{ print NR; exit }' /etc/snmp/snmptt.ini`
df_line=$((df_line + 1))
sed -i "$df_line idate_format = %Y-%m-%d" /etc/snmp/snmptt.ini

echo "Creating a test file for NXTI Test Events to write to"

touch $proddir/var/NXTI_Write_Test
chmod 666 $proddir/var/NXTI_Write_Test

echo ""
echo "Modifying permissions"
echo ""

# Fix SNMP user on Ubuntu 22 systems
snmpttuser="snmptt"
if [ "$dist" == "ubuntu22" ]; then
	snmpttuser="Debian-snmp"
fi

# Modifying permissions:
$chownbin root:$nagiosgroup /etc/snmp/snmptt.conf /etc/snmp /usr/local/bin/addmib
$chownbin $nagiosuser:$nagiosgroup $proddir/var/NXTI_Write_Test /etc/snmp/snmptt.ini
$chownbin -R "$snmpttuser:snmptt" /var/spool/snmptt /var/log/snmptt
chmod 664 /etc/snmp/snmptt.ini
chmod 664 /etc/snmp/snmptt.conf
chmod -R g+w /etc/snmp
chmod g+x /usr/local/bin/addmib
chmod -R ug+w /var/spool/snmptt /var/log/snmptt
chmod g-w /var/log/snmptt

if [ -d /var/lib/snmp ]; then
	# This is sometimes a config directory that must be read to run the snmptrap command
	chmod g+w /var/lib/snmp -R
	usermod -a -G `stat -c "%G" /var/lib/snmp` $apacheuser
fi


# If we're being fancy, we can read snmptt.conf here
# and attempt to autoconfigure NXTI. 

echo "Restarting snmptt"
service snmptt restart
echo "Restarting snmptrapd"
if [ "$dist" == "el7" ] || [ "$dist" == "el8" ] || [ "$dist" == "el9" ]; then
	systemctl daemon-reload
	service snmptrapd restart
elif [ "$distro" == "Debian" ] || [ "$distro" == "Ubuntu" ]; then
	if [ $ver -ge 16 ]; then
		service snmptrapd restart
	else
		service snmpd restart
	fi
fi

touch $BASEDIR/installed.nxti
