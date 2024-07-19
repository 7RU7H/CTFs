<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../config.inc.php');
include_once(dirname(__FILE__) . '/auth.inc.php');
include_once(dirname(__FILE__) . '/utils.inc.php');


/**
 * @param $dbh
 */
function handle_db_connect_error($dbh)
{
    ?>
    DB Connect Error [<?php echo $dbh; ?>]: <?php echo get_sql_error($dbh); ?>
<?php
}


/**
 * @param $dbh
 * @param $sql
 */
function handle_sql_error($dbh, $sql)
{
    ?>
    <p><pre>SQL Error [<?php echo $dbh; ?>] : <?php echo get_sql_error($dbh); ?></pre></p>
<?php
}


function handle_install_needed()
{
    header("Location: install.php");
    exit;
}


function handle_upgrade_needed()
{
    header("Location: install.php");
    exit;
}
