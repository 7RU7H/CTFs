<?php
//
// New API utils and classes for Nagios XI 5
// Copyright (c) 2015-2020 Nagios Enterprises, LLC. All rights reserved.
//

//
// ACTUAL API SECTION
//

include_once(dirname(__FILE__) . '/utils.inc.php');

class API extends NagiosAPI
{
    protected $user;

    public function __construct($request, $origin)
    {
        parent::__construct($request);

        // List of nodes that do not need API key
        $no_api_key = array('authenticate', 'license');

        // Do not need an API key to use the authenticate method
        $nodes = explode('/', $request, 2);
        if (in_array($nodes[0], $no_api_key)) {
            return;
        }

        $apikey = new APIKey();

        // If we used a fusekey, we need to swap that into apikey, and check for specified user data [name/id]
        $userinfo = '';
        if (array_key_exists('fusekey', $this->request)) {
            $this->request['apikey'] = $this->request['fusekey'];
            if (array_key_exists('user', $this->request)) {
                $userinfo = $this->request['user'];
            }
        }
        
        if (!array_key_exists('apikey', $this->request) && !array_key_exists('fusekey', $this->request)) {
            throw new Exception(_('No API Key provided'));
        } else if (!$apikey->verify_key($this->request, $this->method, $origin, $reason, $userinfo)) {
            throw new Exception($reason);
        }

        // Add GET parameters to the args list
        $args = $this->args;
        foreach ($this->request as $key => $val) {
            if ($key != 'apikey' && $key != 'request' && $key != 'pretty') {
                $args[$key] = $val;
            }
        }

        // endpoint/method/args were all set in parent's constructor.
        // $_SESSION['username'] is set during verify_key
        send_to_audit_log(sprintf(_('API endpoint \'%1$s/%2$s\' accessed via %3$s with arguments %4$s'), $this->endpoint, $this->verb, $this->method, json_encode($args)), AUDITLOGTYPE_SECURITY, AUDITLOGSOURCE_API, $_SESSION['username']);
    }

    protected function convert_to_output_type($data)
    {
        $type = grab_array_var($this->request, 'outputtype', 'json');
        if ($type == 'xml') {
            return $data;
        }
        $data = simplexml_load_string($data);
        return json_encode($data);
    }

    /**
     * Used for the api/v1/authenticate token generation
     */
    protected function authenticate($verb, $args)
    {
        switch ($this->method)
        {
            case 'POST':
                return $this->authenticate_user();

            default:
                return array('error' => _('You can only use POST with authenticate.'));
        }
    }

    /**
     * Used as an endpoint for the V2 license API to poke XI and make it do "things"
     */
    protected function license($verb, $args)
    {
        if (is_v2_license()) {
            switch ($this->method)
            {
                case 'POST':
                    switch ($verb) {

                        case 'checkin':
                            
                            // Rate limit the checkin function to once every 5 seconds
                            $lcc = get_option('last_api_v2_license_checkin', 0);
                            if ($lcc > time() - 5) {
                                return array('error' => _('Slow down. You must wait before requesting another check-in.'));
                            }

                            // Do the actual checkin
                            $ret = pull_v2_license_info();
                            if ($ret) {
                                set_option('last_api_v2_license_checkin', time());
                                return array('success' => _('Successfully ran v2 license check-in.'));
                            } else {
                                return array('error' => _('Could not get updated v2 license information from v2 API.'));
                            }
                    }
                    break;
            }
        }
        return array('error' => _('Unknown API endpoint.'));
    }

    /**
    * Format an array so CSV can be downloaded and displayed correctly. 
    * Will do an array walk, aquiring keys into one file stream and values into another file stream
    * If there is a members array, it will do the same thing, but will add the values from the previous array walk
    * to the same line. This allows for a much nicer looking table for the *members api calls.
    * Uses fputcsv because it automatically handles escaping and the ends of rows.
    *
    * @param array $associative_array The array to be formatted
    *
    * @return string The formatted array to be downloaded as a CSV
    */
    public static function csv_format($associative_array) {
        $keys = array();
        $head = array();
        $row = array();

        $headers = fopen('php://memory', 'rw+'); 
        $body = fopen('php://memory', 'rw+'); 

        foreach ($associative_array as $array) {
            array_walk($array, function ($value, $key) use (&$keys, &$head) {
                if (!is_array($value)) {
                    $keys[] = $key;
                    $head[] = $value;
                }
            });
            if (key_exists("members", $array)) {
                foreach ($array["members"] as $members) {
                    foreach ($members as $member) {
                        array_walk($member, function ($value, $key) use (&$keys, &$row) {
                            $keys[] = $key;
                            $row[] = $value;
                        });
                        $mergedRow = array_merge($head, $row);
                        fputcsv($body, $mergedRow);
                        $row = array();
                    }
                }
            } else {
                fputcsv($body, $head);
            }
            $head = array();
        }
        $keys = array_unique($keys);
        fputcsv($headers, $keys);

        rewind($headers);
        rewind($body);

        $output = stream_get_contents($headers);
        $output .= stream_get_contents($body);

        fclose($headers);
        fclose($body);

        return $output;
    }

    protected function get_data($name, $function, $xfunction, $count = false)
    {
        $type = grab_array_var($this->request, 'outputtype', 'json');
        $totals = grab_array_var($this->request, 'totals', 0);

        if ($type == "xml") { 
            $data = $xfunction($this->request);
        } else {
            $data = array("recordcount" => 0);
            if ($count) {
                // This tells the function to return an array with a count of the original data before
                // doing the manipulation to add it into the "members" sections, this is for -members calls
                $objs = $function($this->request, false, true);
                $data[$name] = $objs[0];
                $data['recordcount'] = $objs[1];
            } else {
                $objs = $function($this->request);
                $data[$name] = $objs;
                $data['recordcount'] = count($data[$name]);
            }
            if ($totals) {
                $data = array('recordcount' => intval($data[$name][0]['total']));
            }
            if ($type == "csv") {
                $data = $this->csv_format($data[$name]);
            }
        }
        return $data;
    }

    protected function objects($verb, $args)
    {
        // Include the objects utility functions
        require_once('utils-objects.inc.php');

        switch ($this->method)
        {
            case 'GET':
                switch ($verb)
                {
                    case 'hoststatus':
                        return $this->get_data('hoststatus', 'get_data_host_status', 'get_host_status_xml_output');

                    case 'servicestatus':
                        return $this->get_data('servicestatus', 'get_data_service_status', 'get_service_status_xml_output');

                    case 'logentries':
                        return $this->get_data('logentry', 'get_data_logentries', 'get_logentries_xml_output');

                    case 'statehistory':
                        return $this->get_data('stateentry', 'get_data_statehistory', 'get_statehistory_xml_output');

                    case 'comment':
                        return $this->get_data('comment', 'get_data_comment', 'get_comments_xml_output');

                    case 'downtime':
                        return $this->get_data('scheduleddowntime', 'get_scheduled_downtime', 'get_scheduled_downtime_xml_output');

                    case 'contact':
                        return $this->get_data('contact', 'get_data_contact', 'get_contact_objects_xml_output');

                    case 'host':
                        return $this->get_data('host', 'get_data_host', 'get_host_objects_xml_output');

                    case 'service':
                        return $this->get_data('service', 'get_data_service', 'get_service_objects_xml_output');

                    case 'hostgroup':
                        return $this->get_data('hostgroup', 'get_data_hostgroup', 'get_hostgroup_objects_xml_output');

                    case 'servicegroup':
                        return $this->get_data('servicegroup', 'get_data_servicegroup', 'get_servicegroup_objects_xml_output');

                    case 'contactgroup':
                        return $this->get_data('contactgroup', 'get_data_contactgroup', 'get_contactgroup_objects_xml_output');

                    case 'hostgroupmembers':
                        return $this->get_data('hostgroup', 'get_data_hostgroup_members', 'get_hostgroup_member_objects_xml_output', true);

                    case 'servicegroupmembers':
                        return $this->get_data('servicegroup', 'get_data_servicegroup_members', 'get_servicegroup_member_objects_xml_output', true);

                    case 'contactgroupmembers':
                        return $this->get_data('contactgroup', 'get_data_contactgroup_members', 'get_contactgroup_member_objects_xml_output', true);

                    case 'timeperiod':
                        return $this->get_data('timeperiod', 'get_data_timeperiod', 'get_timeperiods_xml_output');

                    case 'rrdexport':
                        $data = api_objects_get_rrd_json_output($this->request);
                        return $data;

                    case 'cpexport':
                        require_once('../../includes/components/capacityplanning/cp-common.inc.php');
                        $data = get_cp_array_data();
                        return $data;

                    case 'hostavailability':
                        $d = array();
                        $this->request['hostonly'] = true;
                        $data = get_parsed_nagioscore_csv_availability_xml_output($this->request, true);
                        $d['hostavailability'] = $data[0];
                        return $d;

                    case 'serviceavailability':
                        $d = array();
                        $this->request['serviceonly'] = true;
                        $data = get_parsed_nagioscore_csv_availability_xml_output($this->request, true);
                        $d['serviceavailability'] = $data[1];
                        return $d;

                    case 'sla':
                        // Verify enterprise license
                        if (!enterprise_features_enabled()) {
                            return array("error" => _('Enterprise features are not enabled. Please check your license settings.'));
                        }
                        $data = get_sla_data();
                        return $data;

                    case 'bpi':
                        $type = grab_array_var($this->request, 'outputtype', 'json');
                        $data = api_objects_get_bpi_data($type);
                        return $data;

                    case 'unconfigured':
                        return api_objects_unconfigured();

                    default:
                        $type = grab_array_var($this->request, 'outputtype');
                        $error = array('error' => _('Unknown API endpoint.'));
                        if ($type == 'csv') {
                            return $this->csv_format([$error]);
                        }
                        return $error;
                }

            case 'POST':
                return array('error' => _('Unknown API endpoint.'));

            case 'PUT':
                return array('error' => _('Unknown API endpoint.'));

            case 'DELETE':
                return array('error' => _('Unknown API endpoint.'));
        }
    }

    protected function config($verb, $args)
    {
        if (is_admin() == false) { return array('error' => _('Authenticiation failed.')); }

        // Include the config utility functions
        require_once('utils-config.inc.php');

        switch ($this->method)
        {
            case 'GET':

                // Include the config-get utility functions
                require_once('utils-config-get.inc.php');

                switch ($verb)
                {

                    case 'host':
                        return api_config_view_cfg_host($this->request);

                    case 'service':
                        return api_config_view_cfg_service($this->request);

                    case 'hostgroup':
                        return api_config_view_cfg_hostgroup($this->request);

                    case 'servicegroup':
                        return api_config_view_cfg_servicegroup($this->request);

                    case 'command':
                        return api_config_view_cfg_command($this->request);

                    case 'contact':
                        return api_config_view_cfg_contact($this->request);

                    case 'contactgroup':
                        return api_config_view_cfg_contactgroup($this->request);

                    case 'timeperiod':
                        return api_config_view_cfg_timeperiod($this->request);

                    default:
                        return array('error' => _('Unknown API endpoint.'));

                }

            case 'POST':
                switch ($verb)
                {
                    case 'import':
                        return api_config_raw_cfg_import($this->request);

                    case 'host':
                        return api_config_create_cfg_host($this->request);

                    case 'service':
                        return api_config_create_cfg_service($this->request);

                    case 'hostgroup':
                        return api_config_create_cfg_hostgroup($this->request);

                    case 'servicegroup':
                        return api_config_create_cfg_servicegroup($this->request);

                    case 'command':
                        return api_config_create_cfg_command($this->request);

                    case 'contact':
                        return api_config_create_cfg_contact($this->request);

                    case 'contactgroup':
                        return api_config_create_cfg_contactgroup($this->request);

                    case 'timeperiod':
                        return api_config_create_cfg_timeperiod($this->request);

                    default:
                        return array('error' => _('Unknown API endpoint.'));
                }

            case 'PUT':
                switch ($verb)
                {
                    case 'host':
                        return api_config_edit_cfg_host($this->request, $args);

                    case 'service':
                        return api_config_edit_cfg_service($this->request, $args);

                    case 'hostgroup':
                        return api_config_edit_cfg_hostgroup($this->request, $args);

                    case 'servicegroup':
                        return api_config_edit_cfg_servicegroup($this->request, $args);

                    case 'command':
                        return api_config_edit_cfg_command($this->request, $args);

                    case 'contact':
                        return api_config_edit_cfg_contact($this->request, $args);

                    case 'contactgroup':
                        return api_config_edit_cfg_contactgroup($this->request, $args);

                    case 'timeperiod':
                        return api_config_edit_cfg_timeperiod($this->request, $args);

                    default:
                        return array('error' => _('Unknown API endpoint.'));
                }

            case 'DELETE':
                switch ($verb)
                {
                    case 'host':
                        return api_config_remove_cfg_host($this->request, $args);

                    case 'service':
                        return api_config_remove_cfg_service($this->request, $args);

                    case 'hostgroup':
                        return api_config_remove_cfg_hostgroup($this->request, $args);

                    case 'servicegroup':
                        return api_config_remove_cfg_servicegroup($this->request, $args);

                    case 'command':
                        return api_config_remove_cfg_command($this->request, $args);

                    case 'contact':
                        return api_config_remove_cfg_contact($this->request, $args);

                    case 'contactgroup':
                        return api_config_remove_cfg_contactgroup($this->request, $args);

                    case 'timeperiod':
                        return api_config_remove_cfg_timeperiod($this->request, $args);

                    default:
                        return array('error' => _('Unknown API endpoint.'));
                }
        }
    }

    protected function system($verb, $args)
    {
        if (is_admin() == false) { return array('error' => _('Authenticiation failed.')); }

        // Include the system utility functions
        require_once('utils-system.inc.php');

        switch ($this->method)
        {
            case 'GET':
                switch ($verb)
                {
                    case 'user':
                        return api_system_get_users(@$args[0]);

                    case 'status':
                        return api_system_get_status(@$args[0]);

                    case 'statusdetail':
                        $data = get_sysstat_data_xml_output($this->request);
                        return $this->convert_to_output_type($data);

                    case 'applyconfig':
                        return api_system_apply_config();

                    case 'importconfig':
                        return api_system_import_config();

                    case 'info':
                        return api_system_get_info();

                    case 'authserver':
                        return api_system_get_auth_servers();

                    case 'command':
                        return api_system_get_commands($this->request, @$args[0]);

                    default:
                        return array('error' => _('Unknown API endpoint.'));
                }

            case 'POST':
                switch ($verb)
                {
                    case 'user':
                        return api_system_add_user($this->request);

                    case 'applyconfig':
                        return api_system_apply_config();

                    case 'importconfig':
                        return api_system_import_config();

                    case 'addfusehost':
                        return api_system_add_fuse_host($this->request);

                    case 'authserver':
                        return api_system_add_auth_server($this->request);

                    case 'scheduleddowntime':
                        return api_system_add_scheduled_downtime();

                    case 'massimmediatecheck':
                        return api_system_mass_immediate_check();

                    case 'corecommand':
                        return api_system_core_command();

                    default:
                        return array('error' => _('Unknown API endpoint.'));
                }

            case 'PUT':
                return array('info' => _('This section has not yet been implemented.'));

            case 'DELETE':
                switch ($verb)
                {
                    case 'user':
                        return api_system_delete_user($args[0]);

                    case 'removefusehost':
                        return api_system_remove_fuse_host($args[0]);

                    case 'authserver':
                        return api_system_remove_auth_server($args[0]);

                    case 'scheduleddowntime':
                        return api_system_remove_scheduled_downtime($args[0]);

                    default:
                        return array('error' => _('Unknown API endpoint.'));
                }
        }
    }

    /**
     * Individual user info/account settings, status, etc
     * TODO: Implement actions for users
     */
    protected function user($verb, $args)
    {
        // Include the user utility functions
        require_once('utils-user.inc.php');

        switch ($this->method)
        {
            case 'GET':
                return array('info' => _('This section has not yet been implemented.'));

            case 'POST':
                return array('info' => _('This section has not yet been implemented.'));

            case 'PUT':
                return array('info' => _('This section has not yet been implemented.'));

            case 'DELETE':
                return array('info' => _('This section has not yet been implemented.'));
        }
    }

    /**
     * Authenticate the user and return an auth token that can be
     * used to log into the web interface.
     */
    protected function authenticate_user()
    {
        $args = $this->request;

        $username = strtolower(grab_array_var($args, "username", ""));
        $password = grab_array_var($args, "password", "");
        $valid_min = floatval(grab_array_var($args, "valid_min", 5));

        // Verify that everything we need is here
        if (empty($username) || empty($password)) {
            return array("error" => _("Must be valid username and password."));
        }

        // Verify user credentials (local, AD, LDAP, etc)
        $msg = array();
        if (check_login_credentials($username, $password, $msg)) {
            $user_id = get_user_id($username);
            $valid_until = time() + ($valid_min * 60);
            $auth_token = user_generate_auth_token($user_id, $valid_until);
        } else {
            $message = "";
            if (!empty($msg)) {
                $message = $msg[0]; // Only display first message
            }
            $data = array('username' => $username, 'message' => $message, 'error' => 1);
            return $data;
        }

        // Generate auth token and return it
        $data = array('username' => $username,
                      'user_id' => $user_id,
                      'auth_token' => $auth_token,
                      'valid_min' => $valid_min,
                      'valid_until' => date('r', $valid_until));

        return $data;
    }

}
