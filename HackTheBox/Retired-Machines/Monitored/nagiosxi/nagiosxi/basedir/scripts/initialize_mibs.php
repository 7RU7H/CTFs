<?php 

// Takes all of the MIB files currently installed in XI, puts them into our tracking table.

require_once(dirname(__FILE__) . '/../html/includes/utils-mibs.inc.php');
require_once(dirname(__FILE__) . '/../html/includes/common.inc.php');

$db_connection = db_connect_all();
if ($db_connection == false) {
	echo "\nCOULD NOT CONNECT TO DATABASE!\n";
	exit;
}

$mibs = get_fs_mibs();

$sql = "INSERT INTO " .$db_tables[DB_NAGIOSXI]['mibs']. " (mib_name, mib_uploaded, mib_type) VALUES ";
foreach ($mibs as $mib) {
    $mib_entries[] = "('" . escape_sql_param($mib['mibname'], DB_NAGIOSXI) . "', NOW(), 'upload')";
}
$sql .= implode(', ', $mib_entries);

$rows = exec_sql_query(DB_NAGIOSXI, $sql);

?>