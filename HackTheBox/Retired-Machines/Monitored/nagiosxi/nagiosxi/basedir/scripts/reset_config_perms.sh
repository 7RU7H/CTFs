#!/bin/bash
#
# Reset Configuration Permissions
# Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
#

BASEDIR=$(dirname $(readlink -f $0))

# Import Nagios XI and xi-sys.cfg config vars
. $BASEDIR/../etc/xi-sys.cfg
eval $(php $BASEDIR/import_xiconfig.php)

echo ""
echo "--- reset_config_perms.sh ------------"

# Make sure config perms are proper
/bin/chown root.$nagiosgroup /usr/local/nagiosxi/etc/xi-sys.cfg
/bin/chmod 550 /usr/local/nagiosxi/etc/xi-sys.cfg

# Reset all scripts perms before doing individual ones
echo "> Setting script permissions"
/bin/chown $nagiosuser.$nagiosgroup /usr/local/nagiosxi/scripts/*
/bin/chmod 755 /usr/local/nagiosxi/scripts/*
/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/components

# Make sure apache user and nagios group can write to configuration file
# location and to that we can run the ccm_<type> scripts
echo "> Setting CCM script permissions"
/bin/chown $nagiosuser.$nagiosgroup /usr/local/nagiosxi/scripts/ccm_*
/bin/chmod 550 /usr/local/nagiosxi/scripts/ccm_*

echo "> Setting special script permissions"
/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/reset_config_perms.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/reset_config_perms.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/upgrade_to_latest.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/upgrade_to_latest.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/change_timezone.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/change_timezone.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/manage_services.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/manage_services.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/manage_ssl_config.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/manage_ssl_config.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/backup_xi.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/backup_xi.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/repair_databases.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/repair_databases.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/pg2mysql/convert_nagiosxi_to_mysql.php
/bin/chmod 550 /usr/local/nagiosxi/scripts/pg2mysql/convert_nagiosxi_to_mysql.php

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/repairmysql.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/repairmysql.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/import_xiconfig.php
/bin/chmod 550 /usr/local/nagiosxi/scripts/import_xiconfig.php

echo "> Setting special component script permissions"
/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/components/autodiscover_new.php
/bin/chmod 550 /usr/local/nagiosxi/scripts/components/autodiscover_new.php

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/components/getprofile.sh
/bin/chmod 550 /usr/local/nagiosxi/scripts/components/getprofile.sh

/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/send_to_nls.php
/bin/chmod 550 /usr/local/nagiosxi/scripts/send_to_nls.php

echo "> Setting migrate permissions"
mkdir -p /usr/local/nagiosxi/scripts/migrate/jobs
/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/migrate
/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/migrate/migrate.php
/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/migrate/nagios_bundler.py
/bin/chown root.$nagiosgroup /usr/local/nagiosxi/scripts/migrate/nagios_unbundler.py
/bin/chmod 550 /usr/local/nagiosxi/scripts/migrate/migrate.php
/bin/chmod 550 /usr/local/nagiosxi/scripts/migrate/nagios_bundler.py
/bin/chmod 550 /usr/local/nagiosxi/scripts/migrate/nagios_unbundler.py
/bin/chown $nagiosuser:$nagiosgroup /usr/local/nagiosxi/scripts/migrate/jobs

echo "> Setting configuration file/directory permissions"
/bin/chown -R $apacheuser:$nagiosgroup /usr/local/nagios/etc/
/bin/chmod -R 775 /usr/local/nagios/etc/
/bin/chmod ug-s /usr/local/nagios/etc/
find /usr/local/nagios/etc -type f -exec /bin/chmod 664 -- {} +
find /usr/local/nagios/etc -type d -exec /bin/chmod ug-s -- {} +

# Set perfdata directory permissions (RRDs get 664)
echo "> Setting perfdata directory and RRD permissions"
/bin/chmod -R 775 /usr/local/nagios/share/perfdata/
find /usr/local/nagios/share/perfdata/ -type f -exec /bin/chmod 664 -- {} +
/bin/chown -R $nagiosuser.$nagiosgroup /usr/local/nagios/share/perfdata

echo "> Setting libexec directory permissions"
/bin/chmod 775 /usr/local/nagios/libexec

# Set Nagios XI config.inc.php file permissions
echo "> Setting Nagios XI config permissions"
/bin/chown root:$nagiosgroup /usr/local/nagiosxi/html/config.inc.php
/bin/chmod 644 /usr/local/nagiosxi/html/config.inc.php

# Set NOM checkpoint users
echo "> Setting NOM checkpoint user:group permissions"
/bin/chown $nagiosuser:$nagiosgroup /usr/local/nagiosxi/nom/checkpoints/nagiosxi

# Make sure the corelog can be written to
if [ -f /usr/local/nagiosxi/var/corelog.newobjects ]; then
    echo "> + Setting Nagios Core corelog.newobjects user:group permissions"
    /bin/chown $nagiosuser.$nagiosgroup /usr/local/nagiosxi/var/corelog.newobjects
fi

# Make sure ccm config file is writeable by apache
if [ -f /usr/local/nagiosxi/etc/components/ccm_config.inc.php ]; then
    echo "> + Setting CCM configuration file user:group permissions"
    /bin/chown $apacheuser.$nagiosgroup /usr/local/nagiosxi/etc/components/ccm_config.inc.php
    /bin/chmod 664 /usr/local/nagiosxi/etc/components/ccm_config.inc.php
fi

# Make sure recurring downtime is writeable
if [ -f /usr/local/nagios/etc/recurringdowntime.cfg ]; then
	echo "> + Setting Recurring Downtime file user:group permissions"
	/bin/chown $apacheuser.$nagiosgroup /usr/local/nagios/etc/recurringdowntime.cfg
	/bin/chmod 664 /usr/local/nagios/etc/recurringdowntime.cfg
fi

# Make sure BPI configs are writeable by apache
echo "> + Setting BPI configuration file user:group permissions"
if [ -f /usr/local/nagiosxi/etc/components/bpi.conf ]; then
	/bin/chown $apacheuser.$nagiosgroup /usr/local/nagiosxi/etc/components/bpi*
	/bin/chmod 664 /usr/local/nagiosxi/etc/components/bpi.conf
	/bin/chmod 775 /usr/local/nagiosxi/etc/components/bpi
fi

echo "--------------------------------------"
