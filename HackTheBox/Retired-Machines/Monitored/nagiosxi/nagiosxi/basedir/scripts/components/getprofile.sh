#!/bin/bash

# GRAB THE ID
folder=$1
if [ "$folder" == "" ]; then
    echo "You must enter a folder name/id to generate a profile."
    echo "Example: ./getprofile.sh <id>"
    exit 1
fi

# Clean the folder name
folder=$(echo "$folder" | sed -e 's/[^[:alnum:]|-]//g')

# Get OS & version
if which lsb_release &>/dev/null; then
    distro=`lsb_release -si`
    version=`lsb_release -sr`
elif [ -r /etc/redhat-release ]; then

    if rpm -q centos-release; then
        distro=CentOS
    elif rpm -q sl-release; then
        distro=Scientific
    elif [ -r /etc/oracle-release ]; then
        distro=OracleServer
    elif rpm -q cloudlinux-release; then
        distro=CloudLinux
    elif rpm -q fedora-release; then
        distro=Fedora
    elif rpm -q redhat-release || rpm -q redhat-release-server; then
        distro=RedHatEnterpriseServer
    fi >/dev/null

    version=`sed 's/.*release \([0-9.]\+\).*/\1/' /etc/redhat-release`
else
    # Release is not RedHat or CentOS, let's start by checking for SuSE
    # or we can just make the last-ditch effort to find out the OS by sourcing os-release if it exists
    if [ -r /etc/os-release ]; then
        source /etc/os-release
        if [ -n "$NAME" ]; then
            distro=$NAME
            version=$VERSION_ID
        fi
    fi
fi

ver="${version%%.*}"

# Make a clean folder (but save profile.html)
rm -rf "/usr/local/nagiosxi/var/components/profile/$folder/"
mkdir "/usr/local/nagiosxi/var/components/profile/$folder/"
mv -f "/usr/local/nagiosxi/tmp/profile-$folder.html" "/usr/local/nagiosxi/var/components/profile/$folder/profile.html"

# Create the folder setup
mkdir -p "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs"
mkdir -p "/usr/local/nagiosxi/var/components/profile/$folder/logs"
mkdir -p "/usr/local/nagiosxi/var/components/profile/$folder/versions"

echo "-------------------Fetching Information-------------------"
echo "Please wait......."

echo "Creating system information..."
echo "$distro" > "/usr/local/nagiosxi/var/components/profile/$folder/hostinfo.txt"
echo "$version" >> "/usr/local/nagiosxi/var/components/profile/$folder/hostinfo.txt"

echo "Creating nagios.txt..."
nagios_log_file=$(cat /usr/local/nagios/etc/nagios.cfg | sed -n -e 's/^log_file=//p' | sed 's/\r$//')
tail -n500 "$nagios_log_file" &> "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs/nagios.txt"

echo "Creating perfdata.txt..."
perfdata_log_file=$(cat /usr/local/nagios/etc/pnp/process_perfdata.cfg | sed -n -e 's/^LOG_FILE = //p')
tail -n500 "$perfdata_log_file" &> "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs/perfdata.txt"

echo "Creating npcd.txt..."
npcd_log_file=$(cat /usr/local/nagios/etc/pnp/npcd.cfg | sed -n -e 's/^log_file = //p')
tail -n500 "$npcd_log_file" &> "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs/npcd.txt"

echo "Creating cmdsubsys.txt..."
tail -n500 /usr/local/nagiosxi/var/cmdsubsys.log > "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs/cmdsubsys.txt"

echo "Creating event_handler.txt..."
tail -n500 /usr/local/nagiosxi/var/event_handler.log > "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs/event_handler.txt"

echo "Creating eventman.txt..."
tail -n500 /usr/local/nagiosxi/var/eventman.log > "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs/eventman.txt"

echo "Creating perfdataproc.txt..."
tail -n500 /usr/local/nagiosxi/var/perfdataproc.log > "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs/perfdataproc.txt"

echo "Creating sysstat.txt..."
tail -n500 /usr/local/nagiosxi/var/sysstat.log > "/usr/local/nagiosxi/var/components/profile/$folder/nagios-logs/sysstat.txt"

echo "Creating systemlog.txt..."
if [ -f /var/log/messages ]; then
    /usr/bin/tail -n1000 /var/log/messages > "/usr/local/nagiosxi/var/components/profile/$folder/logs/messages.txt"
elif [ -f /var/log/syslog ]; then
    /usr/bin/tail -n1000 /var/log/syslog > "/usr/local/nagiosxi/var/components/profile/$folder/logs/messages.txt"
fi

echo "Retrieving all snmp logs..."
if [ -f /var/log/snmptrapd.log ]; then
    /usr/bin/tail -n1000 /var/log/snmptrapd.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/snmptrapd.txt"
fi
if [ -f /var/log/snmptt/snmptt.log ]; then
    /usr/bin/tail -n1000 /var/log/snmptt/snmptt.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/snmptt.txt"
fi
if [ -f /var/log/snmptt/snmpttsystem.log ]; then
    /usr/bin/tail -n1000 /var/log/snmptt/snmpttsystem.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/snmpttsystem.txt"
fi
if [ -f /var/log/snmpttunknown.log ]; then
    /usr/bin/tail -n1000 /var/log/snmpttunknown.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/snmpttunknown.log.txt"
fi

echo "Creating apacheerrors.txt..."
if [ -d /var/log/httpd ]; then
    for a in $(ls /var/log/httpd)
        do
            /usr/bin/tail -n1000 /var/log/httpd/$a > "/usr/local/nagiosxi/var/components/profile/$folder/logs/$a.txt"
        done

elif [ -d /var/log/apache2 ]; then
    for a in $(ls /var/log/apache2)
        do
            /usr/bin/tail -n1000 /var/log/apache2/$a > "/usr/local/nagiosxi/var/components/profile/$folder/logs/$a.txt"
        done
fi

echo "Creating mysqllog.txt..."

# Determine if MySQL or MariaDB is localhost
db_host=$(
    php -r '
        define("CFG_ONLY", 1);
        require_once($argv[1]);
        print(@$cfg["db_info"]["ndoutils"]["dbserver"] . "\n");
    ' \
        '/usr/local/nagiosxi/html/config.inc.php' 2>/dev/null |
    tail -1
)
echo "The database host is $db_host" > "/usr/local/nagiosxi/var/components/profile/$folder/logs/database_host.txt"
if [ "$db_host" == "localhost" ]; then

    if [ -f /var/log/mysqld.log ]; then
        /usr/bin/tail -n500 /var/log/mysqld.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/database_log.txt"
    elif [ -f /var/log/mariadb/mariadb.log ]; then
        /usr/bin/tail -n500 /var/log/mariadb/mariadb.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/database_log.txt"
    elif [ -f /var/log/mysql/mysql.log ]; then
        /usr/bin/tail -n500 /var/log/mysql/mysql.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/database_log.txt"       
    fi

    # Check if we are running with postgresql
    $(grep -q pgsql /usr/local/nagiosxi/html/config.inc.php)

    if [ $? -eq 0 ]; then

        echo "Getting xi_users..."
        echo 'select * from xi_users;' | psql nagiosxi nagiosxi > "/usr/local/nagiosxi/var/components/profile/$folder/xi_users.txt"

        echo "Getting xi_usermeta..."
        echo 'select * from xi_usermeta;' | psql nagiosxi nagiosxi > "/usr/local/nagiosxi/var/components/profile/$folder/xi_usermeta.txt"

        echo "Getting xi_options(mail)..."
        echo 'select * from xi_options;' | psql nagiosxi nagiosxi | grep mail > "/usr/local/nagiosxi/var/components/profile/$folder/xi_options_mail.txt"

        echo "Getting xi_otions(smtp)..."
        echo 'select * from xi_options;' | psql nagiosxi nagiosxi | grep smtp > "/usr/local/nagiosxi/var/components/profile/$folder/xi_options_smtp.txt"

    else

        echo "Getting xi_users..."
        echo 'select * from xi_users;' | mysql -u root -pnagiosxi nagiosxi -t > "/usr/local/nagiosxi/var/components/profile/$folder/xi_users.txt"

        echo "Getting xi_usermeta..."
        echo 'select * from xi_usermeta;' | mysql -u root -pnagiosxi nagiosxi -t > "/usr/local/nagiosxi/var/components/profile/$folder/xi_usermeta.txt"

        echo "Getting xi_options(mail)..."
        echo 'select * from xi_options;' | mysql -t -u root -pnagiosxi nagiosxi | grep mail > "/usr/local/nagiosxi/var/components/profile/$folder/xi_options_mail.txt"

        echo "Getting xi_otions(smtp)..."
        echo 'select * from xi_options;' | mysql -t -u root -pnagiosxi nagiosxi | grep smtp > "/usr/local/nagiosxi/var/components/profile/$folder/xi_options_smtp.txt"

    fi

    if which mysqladmin >/dev/null 2>&1; then
        errlog=$(mysqladmin -u root -pnagiosxi variables | grep log_error)
        if [ $? -eq 0 ] && [ -f "$errlog" ]; then
            /usr/bin/tail -n500 "$errlog" > "/usr/local/nagiosxi/var/components/profile/$folder/logs/database_errors.txt"
        fi
    fi

    # Do manual check also, just in case we didn't get a log
    if [ -f /var/log/mysql.err ]; then
        /usr/bin/tail -n500 /var/log/mysql.err > "/usr/local/nagiosxi/var/components/profile/$folder/logs/database_errors.txt"
    elif [ -f /var/log/mysql/error.log ]; then
        /usr/bin/tail -n500 /var/log/mysql/error.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/database_errors.txt"
    elif [ -f /var/log/mariadb/error.log ]; then
        /usr/bin/tail -n500 /var/log/mariadb/error.log > "/usr/local/nagiosxi/var/components/profile/$folder/logs/database_errors.txt"
    fi
fi

echo "Creating a sanatized copy of config.inc.php..."
cp /usr/local/nagiosxi/html/config.inc.php "/usr/local/nagiosxi/var/components/profile/$folder/config.inc.php"
sed -i '/pwd/d' "/usr/local/nagiosxi/var/components/profile/$folder/config.inc.php"
sed -i '/password/d' "/usr/local/nagiosxi/var/components/profile/$folder/config.inc.php"

echo "Creating memorybyprocess.txt..."
ps aux --sort -rss > "/usr/local/nagiosxi/var/components/profile/$folder/memorybyprocess.txt"

echo "Creating filesystem.txt..."
df -h > "/usr/local/nagiosxi/var/components/profile/$folder/filesystem.txt"
echo "" >> "/usr/local/nagiosxi/var/components/profile/$folder/filesystem.txt"
df -i >> "/usr/local/nagiosxi/var/components/profile/$folder/filesystem.txt"

echo "Dumping PS - AEF to psaef.txt..."
ps -aef > "/usr/local/nagiosxi/var/components/profile/$folder/psaef.txt"

echo "Creating top log..."
top -b -n 1 > "/usr/local/nagiosxi/var/components/profile/$folder/top.txt"

echo "Creating sar log..."
sar 1 5 > "/usr/local/nagiosxi/var/components/profile/$folder/sar.txt"

FILE=$(ls /usr/local/nagiosxi/nom/checkpoints/nagioscore/ | sort -n -t _ -k 2 | grep .gz | tail -1) 
cp "/usr/local/nagiosxi/nom/checkpoints/nagioscore/$FILE" "/usr/local/nagiosxi/var/components/profile/$folder/"

echo "Copying objects.cache..."
objects_cache_file=$(cat /usr/local/nagios/etc/nagios.cfg | sed -n -e 's/^object_cache_file=//p' | tr -d '\r')
cp "$objects_cache_file" "/usr/local/nagiosxi/var/components/profile/$folder/"

echo "Copying MRTG Configs..."
tar -pczf "/usr/local/nagiosxi/var/components/profile/$folder/mrtg.tar.gz" /etc/mrtg/

echo "Counting Performance Data Files..."

spool_perfdata_location=$(cat /usr/local/nagios/etc/pnp/npcd.cfg | sed -n -e 's/^perfdata_spool_dir = //p')
echo "Total files in $spool_perfdata_location" > "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"
ls -al "$spool_perfdata_location" | wc -l >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"
echo "" >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"

spool_xidpe_location=$(cat /usr/local/nagios/etc/commands.cfg | sed -n -e 's/\$TIMET\$.perfdata.host//p' | sed -n -e 's/\s*command_line\s*\/bin\/mv\s//p' | sed -n -e 's/.*\s//p')
echo "Total files in $spool_xidpe_location" >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"
ls -al "$spool_xidpe_location" | wc -l >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"
echo "" >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"

echo "Counting MRTG Files..."
echo "Total files in /etc/mrtg/conf.d/" >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"
ls -al /etc/mrtg/conf.d/ | wc -l >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"
echo "" >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"

echo "Total files in /var/lib/mrtg/" >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"
ls -al /var/lib/mrtg/ | wc -l >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"
echo "" >> "/usr/local/nagiosxi/var/components/profile/$folder/file_counts.txt"

echo "Getting Network Information..."
ip addr > "/usr/local/nagiosxi/var/components/profile/$folder/ip_addr.txt"

echo "Getting CPU info..."
cat /proc/cpuinfo > "/usr/local/nagiosxi/var/components/profile/$folder/cpuinfo.txt"

echo "Getting memory info..."
free -m > "/usr/local/nagiosxi/var/components/profile/$folder/meminfo.txt"

echo "Getting ipcs Information..."
ipcs > "/usr/local/nagiosxi/var/components/profile/$folder/ipcs.txt"

echo "Getting SSH terminal / shellinabox yum info..."
if [ `command -v yum` ]; then
    yum info shellinabox > "/usr/local/nagiosxi/var/components/profile/$folder/versions/shellinabox.txt"
else
    apt-cache show shellinabox > "/usr/local/nagiosxi/var/components/profile/$folder/versions/shellinabox.txt"
fi

echo "Getting Nagios Core version..."
/usr/local/nagios/bin/nagios --version > "/usr/local/nagiosxi/var/components/profile/$folder/versions/nagios.txt"

echo "Getting NPCD version..."
/usr/local/nagios/bin/npcd --version > "/usr/local/nagiosxi/var/components/profile/$folder/versions/npcd.txt"

echo "Getting NRPE version..."
/usr/local/nagios/bin/nrpe --version > "/usr/local/nagiosxi/var/components/profile/$folder/versions/nrpe.txt"

echo "Getting NSCA version..."
/usr/local/nagios/bin/nsca --version > "/usr/local/nagiosxi/var/components/profile/$folder/versions/nsca.txt"

echo "Getting NagVis version..."
grep -i const_version /usr/local/nagvis/share/server/core/defines/global.php > "/usr/local/nagiosxi/var/components/profile/$folder/versions/nagvis.txt"

echo "Getting WKTMLTOPDF version..."
/usr/bin/wkhtmltopdf --version > "/usr/local/nagiosxi/var/components/profile/$folder/versions/wkhtmltopdf.txt"

echo "Getting Nagios-Plugins version..."
su -s /bin/bash nagios -c "/usr/local/nagios/libexec/check_ping --version" > "/usr/local/nagiosxi/var/components/profile/$folder/versions/nagios-plugins.txt"

echo "Getting BPI configs..."
mkdir -p "/usr/local/nagiosxi/var/components/profile/$folder/bpi/"
cp /usr/local/nagiosxi/etc/components/bpi.conf* "/usr/local/nagiosxi/var/components/profile/$folder/bpi/"

echo "Getting Firewall information..."

if which iptables >/dev/null 2>&1; then
    echo "iptables -S" > "/usr/local/nagiosxi/var/components/profile/$folder/iptables.txt"
    echo "-----------" >> "/usr/local/nagiosxi/var/components/profile/$folder/iptables.txt"
    iptables -S >> "/usr/local/nagiosxi/var/components/profile/$folder/iptables.txt" 2>&1
fi


if which firewall-cmd >/dev/null 2>&1; then
    echo "firewall-cmd --list-all-zones" > "/usr/local/nagiosxi/var/components/profile/$folder/firewalld.txt"
    echo "-----------" >> "/usr/local/nagiosxi/var/components/profile/$folder/firewalld.txt"
    firewall-cmd --list-all-zones >> "/usr/local/nagiosxi/var/components/profile/$folder/firewalld.txt" 2>&1
fi


if which ufw >/dev/null 2>&1; then
    echo "ufw status" > "/usr/local/nagiosxi/var/components/profile/$folder/ufw.txt"
    echo "-----------" >> "/usr/local/nagiosxi/var/components/profile/$folder/ufw.txt"
    ufw status >> "/usr/local/nagiosxi/var/components/profile/$folder/ufw.txt" 2>&1
fi

echo "Getting maillog..."
tail -100 /var/log/maillog > "/usr/local/nagiosxi/var/components/profile/$folder/maillog"

echo "Getting phpmailer.log..."
if [ -f /usr/local/nagiosxi/tmp/phpmailer.log ]; then
    tail -100 /usr/local/nagiosxi/tmp/phpmailer.log > "/usr/local/nagiosxi/var/components/profile/$folder/phpmailer.log"
fi

echo "Getting nom data..."
error_txt=$(ls -t /usr/local/nagiosxi/nom/checkpoints/nagioscore/errors/*.txt | head -n 1)
error_tar_gz=$(ls -t /usr/local/nagiosxi/nom/checkpoints/nagioscore/errors/*.tar.gz | head -n 1)
sql_gz=$(ls -t /usr/local/nagiosxi/nom/checkpoints/nagiosxi/*.sql.gz | head -n 1)

mkdir -p "/usr/local/nagiosxi/var/components/profile/$folder/nom/"
mkdir -p "/usr/local/nagiosxi/var/components/profile/$folder/nom/checkpoints/nagioscore/"
mkdir -p "/usr/local/nagiosxi/var/components/profile/$folder/nom/checkpoints/nagiosxi/"
mkdir -p "/usr/local/nagiosxi/var/components/profile/$folder/nom/checkpoints/nagioscore/errors/"

cp -rf "$error_txt" "/usr/local/nagiosxi/var/components/profile/$folder/nom/checkpoints/nagioscore/errors/"
cp -rf "$error_tar_gz" "/usr/local/nagiosxi/var/components/profile/$folder/nom/checkpoints/nagioscore/errors/"
cp -rf "$sql_gz" "/usr/local/nagiosxi/var/components/profile/$folder/nom/checkpoints/nagiosxi/"

echo "Zipping logs directory..."

## temporarily change to that directory, zip, then leave
(
    ts=$(date +%s)
    cd /usr/local/nagiosxi/var/components/profile
    mv "$folder" "profile-$ts"
    zip -r profile.zip "profile-$ts"
    rm -rf "profile-$ts"
    mv -f profile.zip ../
)

echo "Backup and Zip complete!"
