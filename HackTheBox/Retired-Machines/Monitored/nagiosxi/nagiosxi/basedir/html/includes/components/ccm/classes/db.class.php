<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: db.class.php
//  Desc: Database handler for CCM... does some things instead of using the default
//        old NagiosQL database class.
//


class DB
{
    // Retain last query for debugging 
    var $last_query = '';
    var $affected_rows = 0;
    var $message = '';
    var $error = false;
    var $db;

    // Moved over from mysqldb class
    var $strDBError       = "";
    var $intLastId        = 0;
    var $intAffectedRows  = 0;
    
    /**
     * Establishes DB connection upon initialization 
     */ 
    function __construct($opts = array())
    {
        $this->connect($opts); 
    }
    
    /**
     * Close DB connection upon de-initialization 
     */ 
    function __deconstruct()
    {
        $this->db->close(); 
    }
     
    /**
     * Displays formatted error message
     *
     * @param string $query the query that was attempted 
     */
    function display_error($query)
    {
        print '<p class="error">Could complete the query because: <br />'.$this->db->error.'</p>';
        print '<p class="error">The query being run was: '.$query.'</p>';
    }

    /**
     * Get the current database error string
     *
     * @return  string  MySQL database error
     */
    function get_error()
    {
        return $this->db->error;
    }
    
    /**
     * Establishes DB connection based on configuration values passed
     *
     * @param   array   $opts   Config options array
     * @return  bool            True if connected, false if not
     */ 
    private function connect($opts = array())
    {
        // Check for a port and add it to the options
        if (!array_key_exists('port', $opts)) {
            if (strpos($opts['dbserver'], ':') !== false) {
                $x = explode(':', $opts['dbserver']);
                $opts['dbserver'] = $x[0];
                $opts['port'] = intval($x[1]);
            } else {
                $opts['port'] = 3306;
            }
        }

        $this->db = new MySQLi($opts['dbserver'], $opts['user'], $opts['pwd'], $opts['db'], $opts['port']);

        // Set the UTF-8 setting and return true that connection was successful
        if ($this->db) {
            $this->db->set_charset('utf8');
            $this->db->query("set names 'utf8'");
            return true;
        }

        return false;
    }

    /**
     * Executes an SQL query, returns results as an associative array OR returns NULL
     *
     * @param   string  $query      SQL query to be run
     * @param   bool    $return     True to return data back
     * @return  mixed               Associative array with SQL results, if $return == false return error string
     */ 
    function query($query, $return = true)
    {
        $result = $this->db->query($query);
        $this->last_query = $query;
        $this->affected_rows = $this->db->affected_rows;

        if ($result === false) {
            $this->error = $this->db->error;
            return false;
        } else if ($result === true) {
            return true;
        }

        if ($return) {
            $data = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            $result->free();
            return $data;
        }
    }
    
    /**
     * Generic search $tbl WHERE $field = $keywork function 
     * 
     * @param $tbl
     * @param $field
     * @param $keyword
     * @return array
     */
    function search_query($tbl, $field, $keyword)
    {
        $query = "SELECT * FROM `$tbl` WHERE `$field`=$keyword;";
        $result = $this->db->query($query);
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Generic insert wrapper function
     *
     * @param $query
     */
    function insert_query($query)
    {
        // Execute the query
        if ($this->db->query($query) !== false) {
            print '<p>The DB entry has been added!</p>';
            print '<p><a href="index.php">Return To Main Page</a></p>';
        } else {
            $this->display_error($query);
        }
    }

    /**
     * Grabs id and name field from a selected table. Use for select lists.
     *
     * @param $type Nagios object type (host, service, etc)
     * @return array
     */
    function get_tbl_opts($type)
    {
        // Retrieve list of hostnames and id's from DB
        global $FIELDS; 
        $table = "tbl_".$type;
        $extra = "";

        // If contacts, get alias too
        if ($type == 'contact') {
            $extra = ",alias";
        }

        // Change name directive for templates 
        if ($type == 'hosttemplate' || $type == 'servicetemplate' || $type == 'contacttemplate') {
            $type = 'template';
        }
        $query = "SELECT id,active,".$type."_name".$extra." FROM `$table`";

        // Add WHERE clause so objects can't have a relationship to themselves 
        if (isset($FIELDS['exactType']) && $FIELDS['exactType'] == $type) {
            $query .= "WHERE {$type}_name!='{$FIELDS['hidName']}'";
        }

        /*
        if ($type == 'host' || $type == 'service') {
            if (get_user_meta(0, 'ccm_access') == 2 && !is_authorized_for_all_objects() && !is_admin()) {
                $type_id = OBJECTTYPE_HOST;
                if ($type == 'service') {
                    $type_id = OBJECTTYPE_SERVICE;
                }
                
                if (strpos($query, 'WHERE') !== false) {
                    $query .= ' AND';
                } else {
                    $query .= ' WHERE';
                }

                $query .= " id IN (SELECT object_id FROM tbl_permission WHERE type = ".$type_id." AND user_id = ".$_SESSION['user_id'].")";
            }
        }
        */

        $query .= " ORDER BY {$type}_name ASC";

        $results = $this->query($query);
        return $results;
    }

    /**
     * Grabs all fields from commands table. Used for select lists.
     *
     * @param int $type Command type (check command, misc)
     * @return mixed
     */
    function get_command_opts($type=1)
    {
        $query = "SELECT * FROM `tbl_command` WHERE `command_type`=$type ORDER BY `command_name`";
        $results = $this->query($query) or die($this->display_error($query));
        return $results;
    }
    
    /**
     * Checks for table relationships, both master to slave, and slave to master 
     *
     * @param int $id Object id, primary key 
     * @param string $tbl lnkObjectToObject DB table to check 
     * @param bool $opt Used for special calls to get hosts/services/contacts with "use as template" fields
     * @param bool $master = boolean, master to slave, or slave to master?  
     * @return array $results assoc array of SQL results | empty array 
     */
    function find_links($id, $tbl, $master, $opt=false)
    {   
        $key = (($master == 'master') ? 'idMaster' : 'idSlave');
        $table = 'tbl_lnk'.$tbl;
        if ($opt == 2) {
            $query = "SELECT * FROM `$table` WHERE `$key`=$id AND idTable=2;"; // Named templates 
        } else if ($opt == 1) {
            $query = "SELECT * FROM `$table` WHERE `$key`=$id AND idTable=1;"; // Default template definition 
        } else {
            $query = "SELECT * FROM `$table` WHERE `$key`=$id;";
        }
        $results = $this->query($query);
        if ($results !== false) {
            return $results;
        }
        return array();
    }
    
    /**
     * Link finder for servicegroup to service relationships 
     *
     * @param int $id the object ID to find relationships for 
     * @return string $strings  a string in the following format (hostid::hostgroupID::serviceid) 
     */ 
    function find_service_links($id)
    {
        $table = 'tbl_lnkServicegroupToService';
        $query = "SELECT * FROM `$table` WHERE `idMaster`=$id;";
        $results = $this->query($query);
        if (count($results) == 0) {
            return array();
        } else {
            $strings = array();
            foreach ($results as $r) {
                $strings[] = $r['idSlaveH'].'::'.$r['idSlaveHG'].'::'.$r['idSlaveS'];
            }
            return $strings;
        }
    }

    /**
     * Retrieves array of H:host_name : service_description 
     *
     * @global object $ccm
     * @return array returns a list of services formatted H:host_name : service_description
     */ 
    function get_hostservice_opts()
    {
        global $ccm;
        $hostServiceList = array();

        // Add Services that are linked to Hosts
        $query = "SELECT a.idSlave as host_id,b.host_name, a.idMaster as service_id,c.service_description, c.active as active FROM tbl_lnkServiceToHost a
            JOIN tbl_host b ON a.idSlave=b.id JOIN tbl_service c ON a.idMaster=c.id ORDER BY b.host_name,c.service_description";
        $links = $ccm->db->query($query);
        foreach ($links as $lnk) {
            $key = $lnk['host_id'].'::0::'.$lnk['service_id'];
            $hostServiceList[$key] = array('name' => 'H: '.$lnk['host_name'].' : '.$lnk['service_description'], 'active' => $lnk['active']);
        }

        // Add Services that are linked to Hostgroups
        $query = "SELECT a.idSlave AS hostgroup_id, b.hostgroup_name AS hostgroup_name, a.idMaster AS service_id, c.service_description, c.active AS active
                  FROM tbl_lnkServiceToHostgroup AS a
                  JOIN tbl_hostgroup b ON a.idSlave = b.id
                  JOIN tbl_service c ON a.idMaster = c.id
                  ORDER BY b.hostgroup_name, c.service_description";
        $links = $ccm->db->query($query);
        foreach ($links as $lnk) {
            $key = "0::".$lnk['hostgroup_id']."::".$lnk['service_id'];
            $hostServiceList[$key] = array('name' => 'HG: '.$lnk['hostgroup_name'].' : '.$lnk['service_description'], 'active' => $lnk['active']);
        }

        return $hostServiceList;
    }

    /**
     * Takes in an SQL query and retuns the count as an integer
     *
     * @param $query
     * @return int
     */
    function count_results($query)
    {
        $r = $this->query($query);

        if (isset($r[0]['count(*)'])) {
            return $r[0]['count(*)'];
        }
        if (isset($r[0]['COUNT(*)'])) {
            return $r[0]['COUNT(*)'];
        }
        return 0;
    }

    /**
     * Dimple data deletion function for SINGLE deletions. (UNRELIABLE?)
     *
     * @param $table
     * @param $field
     * @param $id
     * @return string
     */
    function delete_entry($table, $field, $id)
    {
        $query = "DELETE FROM tbl_{$table} WHERE `{$field}`='$id';";
        $this->query($query);

        if ($this->affected_rows == 0) {
            $message = "Item $id failed to delete. <br />".$this->db->error;
        } else { 
            $message = "Item $id deleted successfully!<br />";
        }

        return $message;
    }


    /**
     * Escapes a string for the current DB connection
     *
     * @param   string  $str    String to escape
     * @return  string          Escaped string
     */
    function escape_string($str)
    {
        return $this->db->real_escape_string($str);
    }


    function get_last_id()
    {
        return $this->db->insert_id;
    }


    // ===================================================
    // Converted functions from mysqldb class
    // ===================================================


    /**
     * Sends an SQL statement to the database and stores the first returned
     *
     * @param   string  $strSQL     A SQL statement
     * @return  mixed               Value of field or false if failure
     */
    function getFieldData($strSQL)
    {
        $resQuery = $this->db->query($strSQL);

        // Error handling 
        if ($resQuery && ($resQuery->num_rows != 0) && ($this->db->error == "")) {
          $row = $resQuery->fetch_row();
          return $row[0];
        } else if ($this->db->error != "") {
            $this->strDBError = $this->db->error;
            $this->error = true;
            return false;
        }
        return "";
    }

        /**
     * Gets a single record and returns it as an associate array
     *
     * @param   string  $strSQL         An SQL statement
     * @param   array   $arrDataset     Associative array of dataset selected by SQL query (reference)
     * @return  bool                    True if success or false if failure
     */
    function getSingleDataset($strSQL, &$arrDataset)
    {
        $arrDataset = array();
        $resQuery = $this->db->query($strSQL);

        // Error handling
        if ($resQuery && $resQuery->num_rows != 0 && $this->db->error == "") {
            $arrDataset = $resQuery->fetch_assoc();
            return true;
        } else if ($this->db->error != "") {
            $this->strDBError = $this->db->error;
            $this->error = true;
            return false;
        }
        return false;
    }

    /**
     * Get an array of records and store them numerically
     *
     * @param   string  $strSQL         An SQL statement
     * @param   array   $arrDataset     Associative array of dataset selected by SQL query (reference)
     * @param   int     $intDataCount   The number of records to get (reference)
     *
     * @return bool
     */
    function getDataArray($strSQL, &$arrDataset, &$intDataCount)
    {
        $arrDataset = array();
        $intDataCount = 0;
        $resQuery = $this->db->query($strSQL);
    
        // Get the data and return it or handle the errors
        if ($resQuery && ($resQuery->num_rows != 0) && ($this->db->error == "")) {
            $intDataCount = $resQuery->num_rows;
            $i = 0;
            while ($arrDataTemp = $resQuery->fetch_assoc()) {
                foreach ($arrDataTemp AS $key => $value) {
                    $arrDataset[$i][$key] = $value;
                }
                $i++;
            }
            return true;
        } else if ($this->db->error != "") {
            $this->strDBError = $this->db->error;
            $this->error = true;
            return false;
        }
        return true;
    }

    /**
     * Inserts data into the database
     * Also sets:
     *      $this->intLastId - ID of the generated data set
     *      $this->intAffectedRows - Number of records affected
     *
     * @param   string  $strSQL     An SQL statement
     * @return  bool                True on success or false on failure
     */
    function insertData($strSQL)
    {
        $resQuery = $this->db->query($strSQL);
        
        // Error handling
        if ($this->db->error == "") {
              $this->intLastId = $this->db->insert_id;
              $this->intAffectedRows = $this->db->affected_rows;
              return true;
        } else {
            $this->strDBError = $this->db->error;
            $this->error = true;
            return false;
        }
    }

    /**
     * Count the number of data rows from a query
     * (On fialure it also sets "error" value in the object)
     *
     * @param   string  $strSQL     An SQL statement
     * @return  int                 Number of rows on success or 0 on failure
     */
    function countRows($strSQL)
    {
        $resQuery = $this->db->query($strSQL);

        // Error handling
        if ($resQuery && ($this->db->error == "")) {
            return $resQuery->num_rows;
        } else {
            $this->strDBError = $this->db->error;
            $this->error = true;
            return 0;
        }
    }

}