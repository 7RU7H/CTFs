<?php
//
// Basic LDAP class to mimic adLDAP functionality for easier usage of the LDAP/AD component
// Copyright (c) 2014-2022 Nagios Enterprises, LLC. All rights reserved.
//

class basicLDAP {

    const LDAP_FOLDER = 'OU';
    const LDAP_CONTAINER = 'CN';

    public $type = "ldap";

    protected $host = "";
    protected $port = "389";
    protected $security = "";

    // Connection objects
    protected $ldapConnection;
    protected $ldapBind;
    protected $baseDn;
    
    function __construct($host, $port, $baseDn="", $security="")
    {
        if (!empty($host)) { $this->host = $host; }
        if (!empty($port)) { $this->port = $port; }
        if (!empty($security)) { $this->security = $security; }
        if (!empty($baseDn)) { $this->baseDn = $baseDn; }

        return $this->connect();
    }

    // Connects to the LDAP server
    protected function connect()
    {
        if ($this->security == "ssl") {
            $this->ldapConnection = ldap_connect("ldaps://" . $this->host . ":" . $this->port);
        } else {
            $this->ldapConnection = ldap_connect($this->host, $this->port);
        }

        // Start TLS if we are using it (close connection if we can't use TLS)
        if ($this->security == "tls") {
            $v = ldap_start_tls($this->ldapConnection);
            if (!$v) {
                $this->close();
                return false;
            }
        }

        ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);

        return true;
    }

    // Authenticates witih the LDAP server
    public function authenticate($dn, $password)
    {
        // Prevent null binding
        if ($dn === NULL || $password === NULL) { return false; } 
        if (empty($dn) || empty($password)) { return false; }

        if (strpos(strtolower($dn), strtolower($this->baseDn)) === false) {
            $dn = 'CN='.$dn.','.$this->baseDn;
        }

        // Bind as the user
        $ret = false;
        $this->ldapBind = @ldap_bind($this->ldapConnection, $dn, $password);
        if ($this->ldapBind){ 
            $ret = true; 
        }

        // Find the base DN if it is given
        if ($ret) {
            $new_base_dn = $this->findBaseDn();
            if (!empty($new_base_dn)) {
                $this->baseDn = $new_base_dn;
            }
        }

        return $ret;
    }

    // Closes the LDAP connection
    public function close() {
        if ($this->ldapConnection) {
            @ldap_close($this->ldapConnection);
        }
    }

    public function findBaseDn() 
    {
        $namingContext = $this->getRootDse(array('namingcontexts'));
        $namingContexts = $namingContext[0]['namingcontexts'];

        // Get the first context, then check if we have dn= in the context
        // as a quick basic validation of the context legitimacy
        $context = $namingContexts[0];
        for ($i = 0; $i < count($namingContexts); $i++) {
            if (strpos($namingContexts[$i], 'dc=') !== false) {
                $context = $namingContexts[$i];
                break;
            }
        }

        return $context;
    }
    
    public function getRootDse($attributes = array("*", "+")) {
        if (!$this->ldapBind){ return (false); }
        
        $sr = @ldap_read($this->ldapConnection, '', 'objectClass=*', $attributes);
        $entries = @ldap_get_entries($this->ldapConnection, $sr);
        return $entries;
    }

    public function getLdapConnection() {
        return $this->ldapConnection;
    }

    public function getLdapBind() {
        return $this->ldapBind;
    }

    public function getLastErrno() {
        return 0;
    }
    
    // DIRECTORY STRUCTURE

    public function folder_listing($folderName = NULL, $dnType = basicLDAP::LDAP_FOLDER, $search = "")
    {
        if (!$this->ldapBind) { return false; }
        $filter = '(&(objectClass=*)';

        // Add search feature to the filter
        if (!empty($search)) {
            $filter .= '(name='.str_replace(array('(', ')'), array('\\28', '\\29'), $search).')';
        }

        // If the folder name is null then we will search the root level of AD
        // This requires us to not have an OU= part, just the base_dn
        $searchOu = $this->baseDn;
        if (is_array($folderName)) {
            $ou = $dnType . '="' . implode('",' . $dnType . '="', $folderName) . '"';
            $ou = str_replace(array('(', ')'), array('\\28', '\\29'), $ou);
            $filter .= '(!(distinguishedname=' . $ou . ',' . $this->baseDn . ')))';
            $searchOu = $ou . ',' . $this->baseDn;
        } else {
            $bdn = str_replace(array('(', ')'), array('\\28', '\\29'), $this->baseDn);
            $filter .= '(!(distinguishedname=' . $bdn . ')))';
        }

        $sr = ldap_list($this->ldapConnection, $searchOu, $filter);
        $entries = ldap_get_entries($this->ldapConnection, $sr);

        if (is_array($entries)) {
            return $entries;
        }
        return false;
    }

    public function user_info($dn)
    {
        $sr = ldap_search($this->ldapConnection, $dn, '(objectclass=*)');
        $entries = ldap_get_entries($this->ldapConnection, $sr);

        if (is_array($entries)) {
            return $entries;
        } 
        return false;
    }
}