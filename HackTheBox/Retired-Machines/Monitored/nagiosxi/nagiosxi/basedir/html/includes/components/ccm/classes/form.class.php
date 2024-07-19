<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: form.class.php
//  Desc: Handles the form generation when editing or viewing an object
//


class Form
{

    // Class properties 
    var $type;      // General form type: host, service, contact, timeperiods, commands
    var $template;  // Boolean: Is this for a template? 
    var $preload;   // Boolean: Do we need to load this form with data?
    var $exactType; // Establishes exact form type: example: host, hostgroup, hosttemplate, etc. 
    var $hostgroups_tploptions;
    var $ucType;    // Upper-case first letter 
    var $cmd;
    var $unique;


    // Private properties
    private $mainTypes = array('host','service','hosttemplate','servicetemplate');


    /**
     * Constructor:
     * Establishes necessary properties to make decisions for object
     *
     * @param $type
     * @param $cmd
     */
    function __construct($type, $cmd)
    {
        // Setup class variables upon instantiation 
        $this->type = $type;
        $this->exactType = $type;
        $this->cmd = $cmd; 
        $this->preload = ($cmd == 'modify') ? true : false; 
        $this->template = strstr($type,'template');
        if ($this->template == 'template') $this->exactType = $type;

        // Unique field entry for service/service template tables 
        $this->hostgroups_tploptions = ($this->type =='service') ? 'hostgroup_name_tploptions': 'hostgroups_tploptions';    
        $this->ucType = ucfirst($this->type);
        $this->unique = 100;
    }


    /**
     * Grabs the appropriate element data from the database. Used only for loaded forms.
     *
     * @param   string  $tbl        Table name
     * @param   int     $id         Table row ID
     * @return  array   $results    All SQL data in assoc array for object ID
     */
    function acquire_data($tbl, $id)
    {
        global $ccm;

        $table = "tbl_".$tbl;
        $query = "SELECT * FROM `$table` WHERE `id`=$id LIMIT 1";
        $results = $ccm->db->query($query);

        return $results;
    }

    
    /**
     * Prepares all acquired data for CCM form creation, assigns all data to $FIELDS array  
     */ 
    function prepare_data()
    {
        global $FIELDS;
        global $ccm;

        // Pass the table type and the table id, grab the data from the table, and then insert into the main $FIELDS array
        $tbl = $this->exactType;
        $id = intval(($this->preload) ? ccm_grab_request_var('id', 0) : 0);
        
        // Figure out the template field entry?
        $tbl_grp = $tbl.'group';
        if (!$this->template) {
            $tbl_tmpl = $tbl.'template';
        } else {
            $tbl_tmpl = $tbl;
        }

        $hostAddress = '127.0.0.1';

        // If the mode is to 'modify', grab the DB info and load the $FIELDS array
        if (!empty($id)) {
            $data = $this->acquire_data($tbl, $id);

            // Add assoc keys with data to $FIELDS array 
            foreach ($data as $d) {
                foreach ($d as $key => $value) {
                    $FIELDS[$key] = $value;
                }
            }

            // For the run check command to run properly we need a hostaddress even if we're a service
            // we handle that here
            if (!empty($FIELDS['address'])) {
                $hostAddress = $FIELDS['address'];
            } else {

                // if we're a service (and we have a config name), do a query to pull the host address
                if ($tbl == 'service' && !empty($FIELDS['config_name'])) {
                    $query = "SELECT address FROM tbl_host WHERE host_name = '{$FIELDS['config_name']}' LIMIT 1";
                    $results = $ccm->db->query($query);
                    if (!empty($results[0]['address']))
                        $hostAddress = $results[0]['address'];
                }
            }
        }

        // Verify that the user can access this
        if ($id > 0 && !is_admin() && get_user_meta(0, 'ccm_access', 0) == 2) {
            if ($this->type == 'host' || $this->type == 'service') {
                if (!ccm_has_access_for_object($id, $this->type, $data[0])) {
                    die(_('You do not have permission to manage this object.'));
                } else {
                    // Check ccm_general_permissions();
                }
            }
        }

        // Figure out if we're using host_name, config_name, template_name, etc 
        $opts = array('host', 'hostgroup', 'servicegroup', 'timeperiod', 'command', 'contact', 'contactgroup');       
        $name_field = (in_array($this->exactType, $opts)) ? $this->exactType.'_name' : 'template_name';
        $hidName = ($this->exactType == 'service' || preg_match('/(escalation|dependency)/', $this->exactType) && $this->preload == true) ? $FIELDS['config_name'] : $FIELDS[$name_field];

        // If service
        $FIELDS['hidServiceDescription'] = '';
        if ($this->exactType == 'service') {
            $FIELDS['hidServiceDescription'] = $FIELDS['service_description'];
        }

        // Hidden form fields 
        $FIELDS['hostAddress'] = $hostAddress;
        $FIELDS['hidName'] = $hidName;
        $FIELDS['mode'] = $this->cmd;
        $FIELDS['hidId'] = $id;
        $FIELDS['exactType'] = $this->exactType;
        $FIELDS['genericType'] = $this->type;
        $FIELDS['returnUrl'] = ccm_grab_request_var('returnUrl', '');

        // Verify URL is not a real url
        $tmp_url = urldecode($FIELDS['returnUrl']);
        $tmp = parse_url($tmp_url);
        if (!empty($tmp['scheme'])) {
            $FIELDS['returnUrl'] = '';
        }

        // Set return URL to something if it's empty
        if (empty($FIELDS['returnUrl'])) {
            $FIELDS['returnUrl'] = "index.php?cmd=view&type=" . $this->exactType;
        }

        $FIELDS['page'] = ccm_grab_request_var('page', 1);

        // Create base $FIELDS arrays 
        $this->init_field_arrays($tbl_tmpl);
        $this->init_help_items();

        // Special cases by object type
        switch ($this->exactType)
        {
            case 'host':
            case 'service':
            case 'hosttemplate':
            case 'servicetemplate':
                // Get check commands if needed
                $this->init_check_commands();
                break;

            case 'timeperiod':
                $this->init_timeperiod_vals();
                break;

            // Unique service list for these pages
            case 'serviceescalation':
                $this->init_unique_services();
                break;

            case 'servicedependency':
                $this->init_unique_services();
                $this->init_dependency_arrays();
                break;

            case 'hostdependency':
                $this->init_dependency_arrays();
                break;

            default:
                break;
        }

        // Preload all the relationships
        if ($this->preload) {
            $this->find_host_relationships(); // Host relationships
            $this->find_parent_relationships(); // Parent relationships
            $this->find_hostgroup_relationships(); // Hostgroups
            $this->find_servicegroup_relationships(); // Servicegroups
            $this->find_contact_relationships(); // Contacts
            $this->find_contactgroup_relationships(); // Contactgroups
            $this->find_template_relationships(); // Templates
            $this->find_variable_relationships(); // Variables
            $this->find_service_relationships(); // Service relationships
        }
    }


    /**
     * Initializes necessary arrays for $FIELDS array, which will populate the form
     */
    private function init_field_arrays($tbl_tmpl)
    {
        global $FIELDS;
        global $ccm;

        // XXX TODO: only select data that is actually needed for config, separate function  
        // Global field variables for select lists multi-dimensional of array('id' => #, 'object_name') 
        $FIELDS['selParentOpts'] = array();
        $FIELDS['selParentOpts'] = $ccm->db->get_tbl_opts('host');
        $FIELDs['selHostOpts'] = array(); 
        $FIELDS['selHostOpts'] = $ccm->db->get_tbl_opts('host');
        $FIELDS['selHostgroupOpts'] = array();
        $FIELDS['selHostgroupOpts'] = $ccm->db->get_tbl_opts('hostgroup');
        $FIELDS['selServicegroupOpts'] = array();
        $FIELDS['selServicegroupOpts'] = $ccm->db->get_tbl_opts('servicegroup');
        $FIELDS['selHostServiceOpts'] = array();
        $FIELDS['selTemplateOpts'] = array();
        $FIELDS['selTimeperiods'] = array();
        $FIELDS['selTimeperiods'] = $ccm->db->get_tbl_opts('timeperiod');
        $FIELDS['selContactOpts'] = array();
        $FIELDS['selContactOpts'] = $ccm->db->get_tbl_opts('contact');
        $FIELDS['selContactgroupOpts'] = array();
        $FIELDS['selContactgroupOpts'] = $ccm->db->get_tbl_opts('contactgroup');
        $FIELDS['selEventHandlers'] = array();
        $FIELDS['selEventHandlers'] = $ccm->db->get_command_opts(2);
        $FIELDS['freeVariables'] = array();

        // Arrays for preloaded forms, these arrays are used to determine what values should
        // be preselected on page load - AB and BA arrays are for showing two-way DB relationships 
        $FIELDS['pre_parents'] = array();
        $FIELDS['pre_hosts_AB_exc'] = array();
        $FIELDS['pre_hosts_AB'] = array();
        $FIELDS['pre_hosts_BA'] = array();
        $FIELDS['pre_hosts'] = array(); // Used for escalations / dependencies only 
        $FIELDS['pre_services'] = array(); // Used for escalations / dependencies only
        $FIELDS['pre_services_AB_exc'] = array(); // Used for escalations / dependencies only 
        $FIELDS['pre_hostgroups'] = array(); // Used for escalations / dependencies only 
        $FIELDS['pre_hostgroups_AB_exc'] = array();
        $FIELDS['pre_hostgroups_AB'] = array();
        $FIELDS['pre_hostgroups_BA'] = array();
        $FIELDS['pre_servicegroups'] = array(); // Used for escalations / dependencies only 
        $FIELDS['pre_servicegroups_AB'] = array();
        $FIELDS['pre_servicegroups_BA'] = array();
        $FIELDS['pre_templates'] = array();
        $FIELDS['pre_contacttemplates'] =& $FIELDS['pre_templates'];
        $FIELDS['pre_contacts_AB'] = array();
        $FIELDS['pre_contacts_BA'] = array();
        $FIELDS['pre_contactgroups_AB'] = array();
        $FIELDS['pre_contactgroups_BA'] = array();
        $FIELDS['selCommandOpts'] = array();

        // Servicegroup specific 
        $FIELDS['pre_hostservices_AB'] = array();
        $FIELDS['pre_hostservices_BA'] = array();

        // Contacts specific 
        $FIELDS['pre_hostcommands'] = array();
        $FIELDS['pre_servicecommands'] = array();

        // For servicegroups page only
        if ($this->exactType == 'servicegroup') {
            $FIELDS['selHostServiceOpts'] = $ccm->db->get_hostservice_opts();
        }

        // Host, service, hosttemplate, servicetemplate
        if (in_array($this->exactType, $this->mainTypes)) {
            $FIELDS['selCommandOpts'] = $ccm->db->get_command_opts(1);
            $FIELDS['selTemplateOpts'] = $ccm->db->get_tbl_opts($tbl_tmpl);
        }

        // Special stuff for contact/contacttemplate
        if ($this->exactType == 'contact' || $this->exactType == 'contacttemplate') {
            $FIELDS['selTemplateOpts'] = $ccm->db->get_tbl_opts('contacttemplate');
            $this->find_command_relationships(); 
            $FIELDS['selContacttemplateOpts'] =& $FIELDS['selTemplateOpts'];
        }

        // Use template options
        if (in_array($this->exactType, array('host', 'service', 'contact'))) {
            $nameTemplates = $ccm->db->query("SELECT id,name FROM tbl_".$this->exactType." WHERE name!='' AND name!='NULL';");
            foreach ($nameTemplates as $t) {
                $FIELDS['selTemplateOpts'][] = array('id' => $t['id'].'::2', 'template_name'=>$t['name']);
            }
        }

        // Add wildcards
        if (in_array($this->exactType, array('contact', 'contacttemplate', 'serviceescalation')))
            $FIELDS['selContactgroupOpts'][] = array('id' => '*', 'contactgroup_name' => '*');
        if (in_array($this->exactType, array('contactgroup', 'serviceescalation')))
            $FIELDS['selContactOpts'][] = array('id' => '*', 'contact_name' => '*');
        if (in_array($this->exactType, array('service', 'servicetemplate', 'serviceescalation', 'hostescalation')) ) 
            $FIELDS['selHostgroupOpts'][] = array('id' => '*', 'hostgroup_name' => '*');
        if (in_array($this->exactType, array('service', 'servicetemplate', 'hostgroup', 'serviceescalation', 'hostescalation')))
            $FIELDS['selHostOpts'][] = array('id' => '*', 'host_name' => '*');
    }


    /** 
     * Initializes check_command values/arrays for $FIELDS array as necessary
     */ 
    function init_check_commands()
    {
        global $FIELDS; 
        
        // Check command, is there is a check command defined for this 
        if (isset($FIELDS['check_command']) && $FIELDS['check_command'] != NULL) {

            $exclamation_replacement = '%%%%%%%%%%%%%%%%%%%%%%';
            $cmd_vals = str_replace('\!', $exclamation_replacement, $FIELDS['check_command']);
            $cmd_vals = explode('!', $cmd_vals);
            foreach ($cmd_vals as $index => $cmd_val) {
                $cmd_vals[$index] = str_replace($exclamation_replacement, '\!', $cmd_val);
            }

            // First items in the check command string is the field ID 
            $FIELDS['sel_check_command'] = isset($cmd_vals[0]) ? $cmd_vals[0] : "";

            // Grab the actual command to print out in the form 
            foreach ($FIELDS['selCommandOpts'] as $opt) {
                if ($cmd_vals[0] == $opt['id']) {
                    $FIELDS['fullcommand'] = $opt['command_line'];  
                }
            }

            // Assign any command line arguments to their own text field 
            $FIELDS['tfArg1'] = isset($cmd_vals[1]) ? $cmd_vals[1] : '';
            $FIELDS['tfArg2'] = isset($cmd_vals[2]) ? $cmd_vals[2] : '';
            $FIELDS['tfArg3'] = isset($cmd_vals[3]) ? $cmd_vals[3] : '';
            $FIELDS['tfArg4'] = isset($cmd_vals[4]) ? $cmd_vals[4] : '';
            $FIELDS['tfArg5'] = isset($cmd_vals[5]) ? $cmd_vals[5] : '';
            $FIELDS['tfArg6'] = isset($cmd_vals[6]) ? $cmd_vals[6] : '';
            $FIELDS['tfArg7'] = isset($cmd_vals[7]) ? $cmd_vals[7] : '';
            $FIELDS['tfArg8'] = isset($cmd_vals[8]) ? $cmd_vals[8] : '';
            $FIELDS['tfArg9'] = isset($cmd_vals[9]) ? $cmd_vals[9] : null;
            $FIELDS['tfArg10'] = isset($cmd_vals[10]) ? $cmd_vals[10] : null;
            $FIELDS['tfArg11'] = isset($cmd_vals[11]) ? $cmd_vals[11] : null;
            $FIELDS['tfArg12'] = isset($cmd_vals[12]) ? $cmd_vals[12] : null;
            $FIELDS['tfArg13'] = isset($cmd_vals[13]) ? $cmd_vals[13] : null;
            $FIELDS['tfArg14'] = isset($cmd_vals[14]) ? $cmd_vals[14] : null;
            $FIELDS['tfArg15'] = isset($cmd_vals[15]) ? $cmd_vals[15] : null;
            $FIELDS['tfArg16'] = isset($cmd_vals[16]) ? $cmd_vals[16] : null;
            $FIELDS['tfArg17'] = isset($cmd_vals[17]) ? $cmd_vals[17] : null;
            $FIELDS['tfArg18'] = isset($cmd_vals[18]) ? $cmd_vals[18] : null;
            $FIELDS['tfArg19'] = isset($cmd_vals[19]) ? $cmd_vals[19] : null;
            $FIELDS['tfArg20'] = isset($cmd_vals[20]) ? $cmd_vals[20] : null;
            $FIELDS['tfArg21'] = isset($cmd_vals[21]) ? $cmd_vals[21] : null;
            $FIELDS['tfArg22'] = isset($cmd_vals[22]) ? $cmd_vals[22] : null;
            $FIELDS['tfArg23'] = isset($cmd_vals[23]) ? $cmd_vals[23] : null;
            $FIELDS['tfArg24'] = isset($cmd_vals[24]) ? $cmd_vals[24] : null;
            $FIELDS['tfArg25'] = isset($cmd_vals[25]) ? $cmd_vals[25] : null;
            $FIELDS['tfArg26'] = isset($cmd_vals[26]) ? $cmd_vals[26] : null;
            $FIELDS['tfArg27'] = isset($cmd_vals[27]) ? $cmd_vals[27] : null;
            $FIELDS['tfArg28'] = isset($cmd_vals[28]) ? $cmd_vals[28] : null;
            $FIELDS['tfArg29'] = isset($cmd_vals[29]) ? $cmd_vals[29] : null;
            $FIELDS['tfArg30'] = isset($cmd_vals[30]) ? $cmd_vals[30] : null;
            $FIELDS['tfArg31'] = isset($cmd_vals[31]) ? $cmd_vals[31] : null;
            $FIELDS['tfArg32'] = isset($cmd_vals[32]) ? $cmd_vals[32] : null;

            // Set variables to empty if not set
        } else {
            $FIELDS['check_command'] = ''; 
            $FIELDS['sel_check_command'] = '';
        }
    }


    /**
     * Finds and creates host relationship arrays for $FIELDS array (as necessary) 
     */ 
    function find_host_relationships()
    {
        global $FIELDS;
        global $ccm;

        // Everything that is not a dependency
        if (!strpos($this->exactType, 'dependency')) {

            // Does this object have host relationships (service specific)
            if ((isset($FIELDS['host_name']) && $FIELDS['host_name'] > 0) || (isset($FIELDS['members']) && $FIELDS['members'] == 1)) {
                $matches = array('Hostescalation', 'Hostgroup', 'Hosttemplate', 'Host', 'Serviceescalation', 'Servicetemplate', 'Service');
                if (in_array($this->ucType, $matches)) {
                    $hosts1 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToHost', 'master');
                    foreach ($hosts1 as $h) {
                        $exclude = 0;
                        if (array_key_exists('exclude', $h)) {
                            if ($h['exclude']) {
                                $FIELDS['pre_hosts_AB_exc'][] = $h['idSlave'];
                            }
                        }
                        $FIELDS['pre_hosts_AB'][] = $h['idSlave'];
                    }
                }
            }

            // Find other DB relationships
            $matches = array('Contact', 'Contactgroup', 'Host', 'Hostgroup', 'Hosttemplate', 'Variabledefinition');
            if (in_array($this->ucType, $matches)) {
                $hosts2 = $ccm->db->find_links($FIELDS['id'], 'HostTo'.$this->ucType, 'slave');
                foreach ($hosts2 as $h) {
                    $FIELDS['pre_hosts_BA'][] = $h['idMaster'];
                }
            }

        } else {

            if ((isset($FIELDS['host_name']) && $FIELDS['host_name'] == 1)) {
                $hosts = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToHost_H', 'master');
                foreach ($hosts as $h) {
                    $FIELDS['pre_hosts'][] = $h['idSlave'];
                }
            }

        }

        // Wildcard selection
        if ((isset($FIELDS['host_name']) && $FIELDS['host_name'] == 2) ||
            (isset($FIELDS['members']) && $FIELDS['members'] == 2 && 
            !strpos($this->exactType, 'dependency'))) {
            $FIELDS['pre_hosts'][] = '*'; 
            $FIELDS['pre_hosts_AB'][] = '*';
        }
    }


    /**
     * Finds and creates parent relationship arrays for $FIELDS array (as necessary)
     */ 
    function find_parent_relationships()
    {
        global $FIELDS;
        global $ccm;

        // Does this object have parents?
        if (isset($FIELDS['parents']) && $FIELDS['parents'] == 1) {
            $parents = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToHost', 'master');
            foreach ($parents as $p) {
                $FIELDS['pre_parents'][] = $p['idSlave'];
            }
        }
    }


    /**
     * Finds and creates hostgroup relationship arrays for $FIELDS array (as necessary)
     */
    function find_hostgroup_relationships()
    {
        global $FIELDS;
        global $ccm;
        
        if (!strpos($this->exactType, 'dependency')) {

            // Does this object have hostgroup relationships?
            if (isset($FIELDS['hostgroups']) && $FIELDS['hostgroups'] == 1 || isset($FIELDS['hostgroup_name'])) {

                // Grab hostgroup memberships
                $h_groups1 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToHostgroup', 'master');
                foreach ($h_groups1 as $h) {
                    $exclude = 0;
                    if (array_key_exists('exclude', $h)) {
                        if ($h['exclude']) {
                            $FIELDS['pre_hostgroups_AB_exc'][] = $h['idSlave'];
                        }
                    }
                    $FIELDS['pre_hostgroups_AB'][] = $h['idSlave'];
                }

            }

            // Find indirect hostgroup relationships 
            if ($this->exactType == 'host' || $this->exactType == 'hostgroup') {
                $h_groups2 = $ccm->db->find_links($FIELDS['id'], 'HostgroupTo'.$this->ucType, 'slave');
                foreach ($h_groups2 as $h) {
                    $FIELDS['pre_hostgroups_BA'][] = $h['idMaster'];
                }
            }

        } else {

            $FIELDS['pre_hostgroups'] = array(); 

            // Grab active relationships
            if ((isset($FIELDS['hostgroup_name']) && $FIELDS['hostgroup_name'] == 1)) {
                $hosts = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToHostgroup_H', 'master');
                foreach ($hosts as $h) {
                    $FIELDS['pre_hostgroups'][] = $h['idSlave'];
                }
            }

        }

        // Wildcard selection
        if ((isset($FIELDS['hostgroup_name']) && $FIELDS['hostgroup_name'] == 2) ||
            (isset($FIELDS['hostgroups']) && $FIELDS['hostgroups'] == 2)) {
            $FIELDS['pre_hostgroups'][] = '*';
            $FIELDS['pre_hostgroups_AB'][] = '*';
        }
    }


    /**
     * Finds and creates servicegroup relationship arrays for $FIELDS array (as necessary)
     */ 
    function find_servicegroup_relationships()
    {
        global $FIELDS;
        global $ccm;

        if (!strpos($this->exactType, 'dependency')) {

            // Does this object have servicegroup relationships? 
            if (isset($FIELDS['servicegroups']) && $FIELDS['servicegroups'] == 1 || isset($FIELDS['servicegroup_name'])) {
                $s_groups1 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToServicegroup','master');
                foreach ($s_groups1 as $s) {
                    $FIELDS['pre_servicegroups_AB'][] = $s['idSlave'];
                }
            }

            // Servicegroup to service relationships
            if ($this->exactType == 'servicegroup') {
                $q = "SELECT sg.servicegroup_name, hosts.id as hostid,service.id as serviceid
                        FROM tbl_lnkServiceToServicegroup as sglinks
                        INNER JOIN tbl_service as service on sglinks.idMaster=service.id
                        INNER JOIN tbl_lnkServiceToHost on tbl_lnkServiceToHost.idMaster=service.id
                        INNER JOIN tbl_host as hosts on tbl_lnkServiceToHost.idSlave=hosts.id
                        INNER JOIN tbl_servicegroup as sg on sg.id=sglinks.idSlave
                        WHERE sg.id='".$FIELDS['id']."'";

                $s_groups2 = $ccm->db->query($q);
                foreach ($s_groups2 as $s) {
                    $FIELDS['pre_hostservices_BA'][] = $s['hostid'].'::0::'.$s['serviceid'];
                }
            }
    
        } else {

            $FIELDS['pre_servicegroups'] = array(); 

            // Grab active relationships
            if ((isset($FIELDS['servicegroup_name']) && $FIELDS['servicegroup_name'] == 1)) {
                $hosts = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToServicegroup_S', 'master');
                foreach ($hosts as $h) {
                    $FIELDS['pre_servicegroups'][] = $h['idSlave'];
                }
            }

        }

    }


    /**
     * Fnds and creates servicegroup relationship arrays for $FIELDS array (as necessary) 
     */ 
    function find_service_relationships()
    {
        global $FIELDS; 
        global $ccm;

        if (!strpos($this->exactType, 'dependency')) {

            // Does this object have servicegroup relationships?
            if (isset($FIELDS['members']) && $FIELDS['members'] == 1 && $this->exactType == 'servicegroup') {
                $services = $ccm->db->find_service_links($FIELDS['id']);
                foreach ($services as $s) {
                    $FIELDS['pre_hostservices_AB'][] = $s;
                }
            }

            $matches = array('Contact', 'Contactgroup', 'Host', 'Hostgroup', 'Servicegroup', 'Servicetemplate', 'Variabledefinition');
            if (in_array($this->ucType, $matches)) {
                $s_groups2 = $ccm->db->find_links($FIELDS['id'], 'ServiceTo'.$this->ucType, 'slave');
                foreach ($s_groups2 as $s) {
                    $FIELDS['pre_hostservices_BA'][] = $s['idMaster'];
                }
            }

        } else {

            $FIELDS['pre_services'] = array(); 
            if ((isset($FIELDS['service_description']) && $FIELDS['service_description'] == 1)) {
                //grab active host relationships 
                $service = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToService_S', 'master');
                foreach ($service as $s) {
                    $FIELDS['pre_services'][] = $s['idSlave'];
                }             
            }

        }
        
        // Wildcard selection
        if ((isset($FIELDS['service_description']) && $FIELDS['service_description'] == 2) || isset($FIELDS['members']) && $FIELDS['members'] == 2) {
            $FIELDS['pre_services'][] = '*';
            $FIELDS['pre_hostservices_AB'][] = '*';
        }
    }


    /**
     * Finds and creates template relationship arrays for $FIELDS array (as necessary)
     */ 
    function find_template_relationships()
    {
        global $FIELDS; 
        global $ccm;

        if (isset($FIELDS['use_template']) && $FIELDS['use_template'] == 1)
        {
            // Grab all templates    
            $tblTemplate = ($this->exactType == 'hosttemplate' || 
                            $this->exactType == 'servicetemplate' || 
                            $this->exactType == 'contacttemplate') ? $this->ucType : $this->ucType.'template';

            $dep1 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'To'.$tblTemplate, 'master', 1);
            $dep2 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'To'.$tblTemplate, 'master', 2);

            // Sort the results
            foreach ($dep1 as $key => $row) {
                $sort[$key]  = $row['idSort'];
            }
            array_multisort($sort, SORT_ASC, $dep1);

            foreach ($dep1 as $h) {
                $FIELDS['pre_templates'][] = $h['idSlave'];
            }
            foreach ($dep2 as $h) {
                $FIELDS['pre_templates'][] = $h['idSlave'].'::2';
            }
        }
    }


    /**
     * Finds and creates contact relationship arrays for $FIELDS array (as necessary)
     */ 
    function find_contact_relationships()
    {
        global $FIELDS; 
        global $ccm;

        // Does this object have contacts?  
        if ((isset($FIELDS['contacts']) && $FIELDS['contacts'] == 1) || ($this->exactType == 'contactgroup' && $FIELDS['members'] == 1)) {
            $dep1 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToContact','master');          
            foreach ($dep1 as $h) {
                $FIELDS['pre_contacts_AB'][] = $h['idSlave'];
            }
        }

        // Indirect DB relationships
        $matches = array('CommandHost', 'CommandService', 'Contactgroup', 'Contacttemplate', 'Variabledefinition');
        if (in_array($this->ucType, $matches)) {
            $dep2 = $ccm->db->find_links($FIELDS['id'], 'ContactTo'.$this->ucType,'slave');
            if ($dep2 !== false) {
                foreach ($dep2 as $h) $FIELDS['pre_contacts_BA'][] = $h['idMaster'];
            }
        }

        // Wildcard selection
        if ((isset($FIELDS['contacts']) && $FIELDS['contacts'] == 2) || ($this->exactType == 'contactgroup' && $FIELDS['members'] == 2)) {
            $FIELDS['pre_contacts'][] ='*';
            $FIELDS['pre_contacts_AB'][] ='*';
        }

    }


    /**
     * Finds and creates contactgroup relationship arrays for $FIELDS array (as necessary)
     */ 
    function find_contactgroup_relationships()
    {
        global $FIELDS; 
        global $ccm;

        // Get contactgroups
        $matches = array('Contactgroup', 'Contacttemplate', 'Contact', 'Hostescalation', 'Hosttemplate', 'Host', 'Serviceescalation', 'Servicetemplate', 'Service');
        if (in_array($this->ucType, $matches)) {
            $dep1 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToContactgroup','master');
            foreach ($dep1 as $h) {
                $FIELDS['pre_contactgroups_AB'][] = $h['idSlave'];
            }
        }

        // Get two way dependencies 
        if ($this->exactType == 'contact' || $this->exactType == 'contactgroup') {
            $dep2 = $ccm->db->find_links($FIELDS['id'], 'ContactgroupTo'.$this->ucType, 'slave');
            foreach ($dep2 as $h) {
                $FIELDS['pre_contactgroups_BA'][] = $h['idMaster'];
            }
        }

        // Wildcard selection
        if ((isset($FIELDS['contacts_groups']) && $FIELDS['contact_groups'] == 2) ||
            (isset($FIELDS['contactgroups_members']) && $FIELDS['contactgroup_members'] == 2) ||
            isset($FIELDS['contactgroups']) && $FIELDS['contactgroups'] == 2) {
            $FIELDS['pre_contactgroups'][] ='*';
            $FIELDS['pre_contactgroups_AB'][] ='*';
        }   
    }


    /**
     * finds and creates contact to host/service command relationship arrays for $FIELDS array (as necessary)
     */ 
    function find_command_relationships()
    {
        global $FIELDS; 
        global $ccm;

        // Does this object have host/service command relationships?  
        if (isset($FIELDS['host_notification_commands']) && $FIELDS['host_notification_commands'] == 1) {
            $dep1 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToCommandHost', 'master');
            foreach ($dep1 as $h) {
                $FIELDS['pre_hostcommands'][] = $h['idSlave'];
            }
        }   
        if (isset($FIELDS['service_notification_commands']) && $FIELDS['service_notification_commands'] == 1) {
            $dep1 = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToCommandService', 'master');
            foreach ($dep1 as $h) {
                $FIELDS['pre_servicecommands'][] = $h['idSlave'];
            }
        }
    }


    /**
     * Finds and creates variable relationship arrays for $FIELDS array (as necessary)
     */ 
    function find_variable_relationships()
    {
        global $FIELDS;
        global $ccm;

        // If free variables in use? 
        if (isset($FIELDS['use_variables']) && $FIELDS['use_variables'] == 1) {
            $varDefs = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToVariabledefinition', 'master');
            if ($varDefs > 0) {
                $results = array();
                foreach ($varDefs as $v) {
                    $results = $ccm->db->search_query('tbl_variabledefinition', 'id', $v['idSlave']);
                    $array = array( 'name' => $results[0]['name'], 'value' => $results[0]['value']);
                    $FIELDS['freeVariables'][] = $array;
                }
            }
        }
    }


    /**
     * Initializes form fields and values for timeperiods page  
     */ 
    function init_timeperiod_vals()
    {
        global $FIELDS;
        global $ccm;

        $query = "SELECT id,timeperiod_name FROM tbl_timeperiod;";
        $FIELDS['selExcludeOpts'] = $ccm->db->query($query);
        $FIELDS['pre_excludes'] = array();
        $FIELDS['timedefinitions'] = array();
        $FIELDS['timeranges'] = array();

        // Fetch timeperiod list
        if ($FIELDS['mode'] == 'modify' && $FIELDS['exclude'] == 1) {
            $links = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToTimeperiod', 'master');
            foreach ($links as $link) {
                $FIELDS['pre_excludes'][] = $link['idSlave'];
            }
        }

        // Fetch preload values 
        if ($FIELDS['mode'] == 'modify') {
            $query = "SELECT `definition`,`range` FROM tbl_timedefinition where tipId='".$FIELDS['id']."';";
            $results = $ccm->db->query($query);
            foreach ($results as $r) {
                $FIELDS['timedefinitions'][] = $r['definition'];
                $FIELDS['timeranges'][] = $r['range'];
            }
        }
    }


    /**
    * Build unique services list and pre-select list
    */
    private function init_unique_services() 
    {
        global $FIELDS;
        global $ccm;

        $query = "SELECT DISTINCT `service_description`, `service`.`id`, `host`.`host_name` FROM `tbl_service` AS `service` 
                  LEFT JOIN `tbl_lnkServiceToHost` AS `link` ON `service`.`id` = `link`.`idMaster`
                  LEFT JOIN `tbl_host` AS `host` ON `host`.`id` = `link`.`idSlave`
                  WHERE `service`.`active` = '1' ORDER BY `host_name`, `service_description` ASC";
        $FIELDS['selServiceOpts'] = $ccm->db->query($query);

        // Grab service relationships
        if ((isset($FIELDS['service_description']) && $FIELDS['service_description'] >= 1)) {
            if ($this->exactType == 'serviceescalation' || $this->exactType == 'servicegroup') {
                $services = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToService', 'master');
                foreach ($services as $s) {
                    if (array_key_exists('exclude', $s) && $s['exclude'] == 1) {
                        $FIELDS['pre_services_AB_exc'][] = $s['idSlave'];
                    }
                    $FIELDS['pre_services'][] = $s['idSlave'];
                }
            }
        }
        
        // Handle wildcard selections
        if (in_array($this->exactType, array('serviceescalation', 'servicedependency'))) {
            $FIELDS['selServiceOpts'][] = array('id' => '*', 'service_description' => '*');
        }
    }


    /**
    * Create necessary arrays for host, service, hostgroup dependencies 
    */ 
    private function init_dependency_arrays()
    {
        global $FIELDS;
        global $ccm;

        $FIELDS['selHostDepOpts'] = &$FIELDS['selHostOpts'];
        $FIELDS['selHostgroupDepOpts'] = &$FIELDS['selHostgroupOpts'];
        $FIELDS['selServiceDepOpts'] = &$FIELDS['selServiceOpts'];
        $FIELDS['selServicegroupDepOpts'] = &$FIELDS['selServicegroupOpts'];
        $FIELDS['pre_hostdependencys'] = array();
        $FIELDS['pre_hostgroupdependencys'] = array();
        $FIELDS['pre_servicedependencys'] = array();
        $FIELDS['pre_servicegroupdependencys'] = array();
        $FIELDS['pre_hosts_AB'] = &$FIELDS['pre_hosts'];
        $FIELDS['pre_hostgroups_AB'] = &$FIELDS['pre_hostgroups'];
        $FIELDS['pre_services_AB'] = &$FIELDS['pre_services'];
        $FIELDS['pre_servicegroups_AB'] = &$FIELDS['pre_servicegroups'];

        // Host and hostgroups arrays are established in the the methods: find_host_relationships and find_hostgroup_relationships 

        // Dependent host
        if ((isset($FIELDS['dependent_host_name']) && $FIELDS['dependent_host_name'] == 1)) {
            $objects = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToHost_DH', 'master');
            foreach ($objects as $s) {
                $FIELDS['pre_hostdependencys'][] = $s['idSlave'];
            }
        }

        // Dependent hostgroup 
        if ((isset($FIELDS['dependent_hostgroup_name']) && $FIELDS['dependent_hostgroup_name'] == 1)) {
            $objects = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToHostgroup_DH', 'master');
            foreach ($objects as $s) {
                $FIELDS['pre_hostgroupdependencys'][] = $s['idSlave'];
            }                 
        }

        // Dependent services
        if ((isset($FIELDS['dependent_service_description']) && $FIELDS['dependent_service_description'] == 1)) {
            $objects = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToService_DS', 'master');
            foreach ($objects as $s) {
                $FIELDS['pre_servicedependencys'][] = $s['idSlave'];
            }
        }

        // Dependent service groups
        if ((isset($FIELDS['dependent_servicegroup_name']) && $FIELDS['dependent_servicegroup_name'] == 1)) {
            $objects = $ccm->db->find_links($FIELDS['id'], $this->ucType.'ToServicegroup_DS', 'master');
            foreach ($objects as $s) {
                $FIELDS['pre_servicegroupdependencys'][] = $s['idSlave'];
            }
        }

        // Wildcard selection

        if ((isset($FIELDS['dependent_service_description']) && $FIELDS['dependent_service_description'] == 2)) {
            $FIELDS['pre_servicedependencys'][] = '*';
        }

    }


    /**
    *   creates documentation listings on any appropriate forms 
    */
    private function init_help_items()
    {
        global $FIELDS;
        global $ccm;
        $type = $this->exactType;

        // Handle exceptions for templates
        if ($type == 'hosttemplate') $type = 'host';
        if ($type == 'servicetemplate') $type = 'service';
        if ($type == 'contacttemplate') $type = 'contact';
        $FIELDS['infotype'] = $type;
        
        $query = "SELECT `key2` FROM tbl_info WHERE `key1`='{$type}' ORDER BY `key2` ASC"; 
        $FIELDS['info'] = $ccm->db->query($query);
    }


    /**
     * Calls and include appropriate form templates based on object type. Prints direct html output to screen.    
     * 
     * @global  mixed   $FIELDS     Main object relationship array
     */ 
    function build_form()
    {
        global $FIELDS;
        global $cfg;

        // DEBUG 
        $debug = ccm_grab_array_var($_SESSION,'debug',false);
        if ($debug && $this->preload) {
            ccm_array_dump($FIELDS);
        }

        include(TPLDIR.'form_header.php');

        // Load form parts based on object type from template files 

        switch ($this->exactType)
        {
            case 'host':
            case 'service':
            case 'hosttemplate':
            case 'servicetemplate':
                // If loaded, process array so that it can be passed into the form
                include(TPLDIR.'common_settings.php');
                include(TPLDIR.'check_settings.php');
                include(TPLDIR.'alert_settings.php');
                include(TPLDIR.'misc_settings.php');
                include(TPLDIR.'hidden_elements.php');
            break;

            case 'hostgroup':
            case 'servicegroup':
            case 'contactgroup':
                include(TPLDIR.'group_template.php');
                include(TPLDIR.'hidden_elements.php');
            break;

            case 'timeperiod':
                include(TPLDIR.'timeperiod_template.php');
                include(TPLDIR.'hidden_elements.php'); 
            break;

            case 'contact':
            case 'contacttemplate':
                include(TPLDIR.'contact_template.php');
                include(TPLDIR.'misc_settings.php');
                include(TPLDIR.'hidden_elements.php');
            break;

            case 'command':
                include(TPLDIR.'command_template.php');
            break;

            case 'hostescalation':
            case 'hostdependency':
            case 'serviceescalation':
            case 'servicedependency':
                include(TPLDIR.'escalation_dependency_template.php');
                include(TPLDIR.'hidden_elements.php');
            break;

            case 'user':
                require_once(INCDIR.'admin_views.inc.php'); 
                manage_user_html(); 
            break;

            default:
                echo "no template defined yet!";
                echo $this->exactType;
            break;
        }

        include(TPLDIR.'form_footer.php');
    }

}
