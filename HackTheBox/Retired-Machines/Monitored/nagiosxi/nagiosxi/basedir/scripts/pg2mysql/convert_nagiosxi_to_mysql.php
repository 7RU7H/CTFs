#!/usr/bin/php
<?php

/* Change this variable if you've changed the mysql root password (this script will create a new database). */
$root_password = 'nagiosxi';


/*
This script heavily utilizes code from the 'Pg2MySQL' converter project
http://www.lightbox.org/pg2mysql.php

Copyright (C) 2005-2011 James Grant <james@lightbox.org>
            Lightbox Technologies Inc.

This script is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public
License as published by the Free Software Foundation, version 2.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; see the file COPYING.  If not, write to
the Free Software Foundation, Inc., 59 Temple Place - Suite 330,
Boston, MA 02111-1307, USA.
*/


require_once(dirname(__FILE__).'/../../html/config.inc.php');
include "pg2mysql.inc.php";

function shell_command_or_die($cmd)
{
    $output = array();
    $rc = -1;
    exec($cmd, $output, $rc);
    if ($rc != 0) {
        $step_name = explode($cmd, " ");
        echo "$cmd failed!\n";
        foreach ($output as $line) {
            echo "$line";
        }
        exit;
    }
}

function shell_command_can_fail($cmd)
{
    $output = array();
    $rc = -1;
    exec($cmd, $output, $rc);
    return array('output' => $output, 'rc' => $rc);
}

$nagiosxi_dbtype = $cfg['db_info']['nagiosxi']['dbtype'];
if ($nagiosxi_dbtype != "pgsql") {
    echo "Your config.inc.php says you're using '$nagiosxi_dbtype', not 'pgsql'. You must be using pgsql to migrate away from it.";
    exit;
}

echo <<<EOF
Before you begin, please keep in mind that this script is attempting an automated database migration. 
Please take a snapshot or backup of your server first, and consider opening a ticket with the Support Team.


EOF;

$username = $cfg['db_info']['nagiosxi']['user'];
$password = $cfg['db_info']['nagiosxi']['pwd'];
$database = $cfg['db_info']['nagiosxi']['db'];
$hostname = $cfg['db_info']['nagiosxi']['dbserver'];

$ccm_hostname = $cfg['db_info']['nagiosql']['dbserver'];
$ndoutils_hostname = $cfg['db_info']['ndoutils']['dbserver'];

$base_directory = dirname(__FILE__);
$input_path = $base_directory . "/nagiosxi.psql";
$output_path = $base_directory . "/nagiosxi.mysql";

echo "Checking for offloaded databases...\n";
$mysql_hostname = $hostname;
$offloaded_mysql = false;
if ($ccm_hostname == $ndoutils_hostname && $ndoutils_hostname != "localhost") {
    $offloaded_mysql = true;
    // $mysql_hostname = $ndoutils_hostname;
    // echo "Detected offloaded database - new database will be configured on $mysql_hostname\n";
}
$safe_mysql_hostname = escapeshellarg($mysql_hostname);

echo "Checking for mysql or mariadb...\n";
/* Are we on a YUM- or APT- based system? */
$package_manager = "apt";
$xisys_file = $cfg['root_dir'] . '/var/xi-sys.cfg';
if (!file_exists($xisys_file)) {
    echo "Could not find $xisys_file! Exiting.\n";
    exit;
}
$xisys = parse_ini_file($xisys_file);
if ($xisys === false) {
    echo "Could not open $xisys_file! Exiting.\n";
    exit;
}
if (strstr($xisys['dist'], 'el') !== false) {
    $package_manager = 'yum';
}

$cmd = "yum list installed | grep -e mysql -e mariadb";
if ($package_manager === "apt") {
    $cmd = "dpkg-query -f '\${binary:Package}\n' -W | grep -e mysql-server -e mariadb-server";
}
$result = shell_command_can_fail($cmd);
if ($result['rc'] != 0 && count($result['output']) <= 1) {
    // Note: both of the above commands will show multiple matching packages when an appropriate DB is found.
    echo "Could not find MySQL or MariaDB installed on your server. Exiting\n";
    exit;
}

/* Is the database turned on? */
echo "Verifying database is on...\n";
$cmd = 'service mysqld status || service mysql status || systemctl status mariadb.service';
$result = shell_command_can_fail($cmd);
if ($result['rc'] != 0) {
    echo "MySQL/MariaDB is not running. Please start your database server to continue.\n";
    exit;
}

echo "Verifying credentials...\n";
$cmd = "mysql -h $safe_mysql_hostname -u root -p$root_password -e 'SHOW DATABASES;' 2>/dev/null";
$result = shell_command_can_fail($cmd);
if ($result['rc'] != 0) {
    echo "Failed to authenticate as root. Please change \$root_password at the top of this script.\n";
    exit;
}

echo "Verifying privileges...\n";
$cmd = "mysql -Ns -f $safe_mysql_hostname -u root -p$root_password -e 'SELECT * FROM mysql.user WHERE User = \"root\" AND Create_user_priv = \"Y\" AND Grant_priv = \"Y\" AND Select_priv = \"Y\" AND Insert_priv = \"Y\" AND Delete_priv = \"Y\"'";
$result = shell_command_can_fail($cmd);
if ($result['rc'] != 0 || count($result['output']) < 1) {
    echo "Failed to determine whether the root mysql account has enough access to make database changes. Exiting.\n";
    exit;
}

$answer = strval(readline("Are you ready to start? [y/N]\n"));
if (empty($answer) || ($answer[0] != 'y' && $answer[0] != 'Y')) {
    echo "Exiting.\n";
    exit;
}
echo "Continuing...\n";

echo "Dumping to nagiosxi.psql...\n";
$cmd = "pg_dump --no-acl --no-owner --format p --data-only -U ".escapeshellarg($username)." " . escapeshellarg($database) . " >$input_path";
shell_command_or_die($cmd);
$cmd2 = "sed -i 's/COPY public\./COPY /g' $input_path";
shell_command_or_die($cmd2);
echo "Done!\n";


echo "Creating $output_path...\n";
$config['engine']="InnoDB";
pg2mysql_large($input_path, $output_path);
echo "Done!\n";


echo "Checking for existing Nagios XI MySQL configuration...\n";
$cmd = "mysql -h $safe_mysql_hostname -u root -p$root_password -e 'SHOW DATABASES;' 2>/dev/null | grep $database";
$result = shell_command_can_fail($cmd);
if ($result['rc'] == 0 && strlen($result['output'][0]) > 1) {
    $cmd = "mysql -h $safe_mysql_hostname -u root -p$root_password -e 'DROP DATABASE $database'";
    shell_command_or_die($cmd);
}

$cmd = "mysql -h $safe_mysql_hostname -u root -p$root_password mysql -e 'SELECT * FROM user WHERE User = \"$username\"' 2>/dev/null";
$result = shell_command_can_fail($cmd);
if ($result['rc'] == 0 && count($result['output']) >= 2) {
    $cmd = "mysql -h $safe_mysql_hostname -u root -p$root_password -e 'DROP USER \"$username\"@\"localhost\"' 2>/dev/null";
    shell_command_or_die($cmd);
}

echo "Creating Nagios XI database...\n";
$create_db_path = dirname(__FILE__)."/nagiosxi_create_db.sql";
$cmd = "mysql -h $safe_mysql_hostname -uroot -p$root_password < $create_db_path";
shell_command_or_die($cmd);
echo "Done!\n";

echo "Importing PostgreSQL data to MySQL...\n";
$cmd = "mysql -h $safe_mysql_hostname -uroot -p$root_password --force $database < $output_path";
shell_command_or_die($cmd);
echo "Done!\n";

echo "Creating '$username' user in MySQL...\n";
$cmd = "mysql -h $safe_mysql_hostname -u root -p$root_password -e \"CREATE USER '$username'@'localhost' IDENTIFIED BY '$password';\"";
shell_command_or_die($cmd);
$cmd = "mysql -h $safe_mysql_hostname -u root -p$root_password -e \"GRANT ALL ON $database.* TO '$username'@'localhost'; FLUSH PRIVILEGES;\"";
shell_command_or_die($cmd);
echo "Done!\n";

echo "Modifying config.inc.php to use mysql...\n";
$cmd = 'cp -p /usr/local/nagiosxi/html/config.inc.php /usr/local/nagiosxi/html/config.inc.php.backup';
shell_command_or_die($cmd);
$cmd = "sed -i 's/pgsql/mysql/' /usr/local/nagiosxi/html/config.inc.php";
shell_command_or_die($cmd);
// if ($offloaded_mysql) {
//     // Note: we're assuming 'nagiosxi' is always the first database configured in config.inc.php
//     $cmd = 'sed -i \'s/"dbserver".*/"dbserver" => "'.$mysql_hostname.'",/\' /usr/local/nagiosxi/html/config.inc.php';
//     shell_command_or_die($cmd);
// }

// We don't care if this one succeeds.
$cmd = "mv /usr/local/nagiosxi/html/includes/components/nagiosim ~/nagiosim.backup &>/dev/null";
exec($cmd);

echo "Done!\n";


echo "Disabling PostgreSQL, restarting MySQL...";
$cmd = 'service mysqld restart || service mysql restart || systemctl restart mariadb.service';
shell_command_or_die($cmd);
$cmd = "service postgresql stop || systemctl stop postgresql.service";
shell_command_or_die($cmd);
$cmd = "chkconfig postgresql off || systemctl disable postgresql.service";
shell_command_or_die($cmd);
echo "Done!\n";


echo <<<XHTML

========
FINISHED
========

All of our migration commands succeeded without any issues. 
PostgreSQL is disabled, and MySQL/MariaDB is enabled.
Please try to log into your Nagios XI interface and confirm that it's working.
If you succeed, please reboot this server and verify that the web interface is working after.
If the web interface is working, please run mysql_tune.sh to load our preferred MySQL/MariaDB configuration.

If you run into issues, you will need to run the following commands:

    mv /usr/local/nagiosxi/html/config.inc.php.backup /usr/local/nagiosxi/html/config.inc.php
    chkconfig postgresql on || systemctl enable postgresql
    service postgresql start || systemctl start postgresql

If you did roll back, you'll also need to get into contact with the Nagios XI Support team. 
In your ticket, please include a zip of this directory:

$base_directory

XHTML;


if ($offloaded_mysql) {
    echo <<<EOF
=========
IMPORTANT
=========

We've detected that you're using an offloaded/remote MySQL setup for NDO and the CCM.
Currently, the migrated Postgres->MySQL database is set up *locally*.
Please consult with the Support Team if you'd like to offload this database as well.
EOF;
}
