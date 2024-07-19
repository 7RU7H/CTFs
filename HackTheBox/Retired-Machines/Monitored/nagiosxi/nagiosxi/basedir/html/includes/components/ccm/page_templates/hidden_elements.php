<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: hidden_elements.php
//  Desc: Creates the hidden overlay based on the type of object/element we are using. All
//        hidden overlay functions are defined in includes/hidden_overlay_functions.inc.php.
//        Builds a hidden overlay div and populates values based on parameters given.
//

echo '</div>';

switch ($this->exactType)
{
    case 'host': 
    case 'hosttemplate':
        echo build_hidden_overlay('parent', 'host_name', false, true); // Parents
        echo build_hidden_overlay('hostgroup', 'hostgroup_name', true, true); // Host Groups 
        echo build_hidden_overlay('template', 'template_name', false, false); // Templates
        echo build_hidden_overlay('contactgroup', 'contactgroup_name', false, true); // Contact Groups
        echo build_hidden_overlay('contact', 'contact_name', true, true); // Contacts
        echo build_command_output_box(); // Command Test
        echo build_variable_box(); // Free Variables
        break;
    
    case 'service':
    case 'servicetemplate':
        echo build_hidden_overlay('host', 'host_name', true, true); // Hosts
        echo build_hidden_overlay('hostgroup', 'hostgroup_name', true, true); // Host Groups
        echo build_hidden_overlay('servicegroup', 'servicegroup_name', true, true); // Service Groups
        echo build_hidden_overlay('template', 'template_name', false, false); // Templates
        echo build_hidden_overlay('contactgroup', 'contactgroup_name', false, true); // Contact Groups
        echo build_hidden_overlay('contact', 'contact_name', true, true); // Contacts
        echo build_command_output_box(); // Command Test
        echo build_variable_box(); // Free Variables
        break;

    case 'hostgroup':
        echo build_hidden_overlay('host', 'host_name', true, false); // Hosts
        echo build_hidden_overlay('hostgroup', 'hostgroup_name', true, false); // Host Groups
        break; 
    
    case 'servicegroup':
        echo build_hidden_overlay('servicegroup', 'servicegroup_name', true, false); // Service Groups
        echo build_hidden_overlay('hostservice', 'servicegroup_name', false, false); // Services
        break; 
    
    case 'contactgroup':
        echo build_hidden_overlay('contactgroup', 'contactgroup_name', false, false); // Contact Groups
        echo build_hidden_overlay('contact', 'contact_name', true, false); // Contacts   
        break;  
    
    case 'contact':
    case 'contacttemplate':
        echo build_hidden_overlay('contactgroup', 'contactgroup_name', true, true); // Contact Groups
        echo build_hidden_overlay('servicecommand', 'command_name', false, true, 'selEventHandlers'); // Service Commands
        echo build_hidden_overlay('hostcommand', 'command_name', false, true, 'selEventHandlers'); // Host Commands
        echo build_hidden_overlay('contacttemplate', 'template_name', false, false); // Contact Templates
        echo build_variable_box(); // Free Variables
    break; 
    
    case 'timeperiod':
        echo build_hidden_overlay('exclude', 'timeperiod_name', false, false); // Excluded Time Periods
        break; 
    
    case 'serviceescalation':
    case 'hostescalation':
    case 'servicedependency':
    case 'hostdependency': 
        echo build_hidden_overlay('host', 'host_name'); // Hosts
        echo build_hidden_overlay('hostgroup', 'hostgroup_name'); // Host Groups
        if (strpos($this->exactType, 'escalation')) {
            echo build_hidden_overlay('contact', 'contact_name', true); // Contacts
            echo build_hidden_overlay('contactgroup', 'contactgroup_name'); // Contact Groups
        }
        if ($this->exactType == 'serviceescalation') {
            echo build_hidden_overlay('service', 'service_description', false, false, '', $this->exactType); // Services
            echo build_hidden_overlay('servicegroup', 'servicegroup_name', false, false, '', $this->exactType);
        }
        if ($this->exactType == 'hostdependency' || $this->exactType == 'servicedependency') {
            echo build_hidden_overlay('hostdependency', 'host_name', false, false, 'selHostDepOpts'); // Hosts (Dependant)
            echo build_hidden_overlay('hostgroupdependency', 'hostgroup_name', false, false, 'selHostgroupDepOpts'); // Host Groups (Dependant)
        }
        if ($this->exactType == 'servicedependency') {
            echo build_hidden_overlay('service', 'service_description', false, false, '', $this->exactType);
            echo build_hidden_overlay('servicegroup', 'servicegroup_name', false, false, '', $this->exactType);
            echo build_hidden_overlay('servicedependency', 'service_description', false, false, 'selServiceDepOpts', $this->exactType); // Services (Dependant)
            echo build_hidden_overlay('servicegroupdependency', 'servicegroup_name', false, false, 'selServicegroupDepOpts', $this->exactType);
        }
        break;
    
    default:
        break;
}

// Documentation overlay creation
?>
<div id='documentation' class='overlay'></div>