<?php
//
// Nagios CCM Integration Component
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

nagioscorecfg_component_init();

function nagioscorecfg_component_init()
{
    $name = "nagioscorecfg";
    $args = array(
        COMPONENT_NAME => $name,
        COMPONENT_TITLE => "Nagios Core Config Integration",
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => "Provides Nagios Core apply config in the Nagios XI web UI.",
        COMPONENT_PROTECTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );
    register_component($name, $args);
}
