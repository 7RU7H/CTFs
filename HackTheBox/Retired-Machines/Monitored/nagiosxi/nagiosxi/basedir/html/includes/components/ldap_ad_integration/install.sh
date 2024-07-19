#!/bin/bash

BASEDIR=$(dirname $(readlink -f $0))

. $BASEDIR/../../../../var/xi-sys.cfg

# Install php-ldap if it's not already installed
# comes pre-installed in ubuntu and debian XI versions
if [ `command -v yum` ]; then
	yum install php-ldap -y
fi

if [ "$distro" == "Ubuntu" ] || [ "$distro" == "Debian" ]; then
	ldap_config="/etc/ldap/ldap.conf"
	ldap_dir="/etc/ldap"
else
	ldap_config="/etc/openldap/ldap.conf"
	ldap_dir="/etc/openldap"
fi

cacerts_dir="$ldap_dir/cacerts"
certs_dir="$ldap_dir/certs"

# Set the permissions of the openldap configuration files/folders
mkdir -p $cacerts_dir
mkdir -p $certs_dir
chown $apacheuser.$nagiosgroup $ldap_dir $certs_dir $cacerts_dir $ldap_config
chmod 664 $ldap_config
chmod 775 $ldap_dir $certs_dir $cacerts_dir

# Edit line in ldap config
sed -i 's/TLS_CACERTDIR/#TLS_CACERTDIR/g' $ldap_config
echo "TLS_CACERTDIR $ldap_dir/cacerts" >> $ldap_config

exit 0