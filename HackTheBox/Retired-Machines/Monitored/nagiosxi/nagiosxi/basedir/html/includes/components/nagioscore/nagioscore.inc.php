<?php
//
// Nagios Core Component
// Copyright (c) 2008-2017 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

nagioscore_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function nagioscore_component_init()
{
    $name = "nagioscore";
    $args = array(
        COMPONENT_NAME => $name,
        COMPONENT_TITLE => _("Nagios Core Integration"),
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => _("Provides integration with Nagios Core."),
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );
    register_component($name, $args);
}


///////////////////////////////////////////////////////////////////////////////////////////
// URL FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

// gets url used to access core ui
/**
 * @return string
 */
function nagioscore_get_ui_url()
{
    $url = get_base_url() . "includes/components/nagioscore/ui/";
    return $url;
}


///////////////////////////////////////////////////////////////////////////////////////////
// IMAGE FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

/**
 * @param string $img
 *
 * @return string
 */
function nagioscore_image($img = "")
{
    $base_path = nagioscore_get_ui_url();
    return $base_path . "images/" . $img;
}

/**
 * @param string $img
 *
 * @return string
 */
function nagioscore_object_image($img = "")
{
    $base_path = nagioscore_get_ui_url();
    return $base_path . "images/logos/" . $img;
}
	
