<?php
//
// NXTI
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

$nxti_component_name = "nxti";

nxti_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function nxti_component_init()
{
    global $nxti_component_name;
    $versionok = true;

    // Component description
    $desc = _("Manage SNMP Trap definitions from the web interface. Access at Admin -> Monitoring Config -> SNMP Trap Interface.");
    if (!file_exists(dirname(__FILE__) . "/installed.nxti")) { 
        // install.sh should run on install and upgrade, so something is already likely wrong.
        
        $desc .= "<br><b>"._("Important") . ": " . _("Installation is not complete. Run the following as root to install:")."</b><br/><pre>
cd " . dirname(__FILE__) . "
chmod +x install.sh
./install.sh
</pre><br/>" .
        sprintf(_('If this message persists, please contact us at %s.'), "https://support.nagios.com/forum/");
    }

    if (!$versionok) {
        $desc = "<b>"._("Error").": "._("This component requires Nagios XI 2009R1.2B or later.")."</b>";
    }

    $args = array(
        COMPONENT_NAME => $nxti_component_name,
        COMPONENT_VERSION => '1.0.4',
        COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION => $desc,
        COMPONENT_TITLE => _("SNMP Trap Interface (NXTI)"),
        COMPONENT_PROTECTED => true,
        COMPONENT_ENCRYPTED => true,
        COMPONENT_TYPE => COMPONENT_TYPE_CORE
    );

    // Register this component with XI
    register_component($nxti_component_name, $args);

    // Register the addmenu function
    if ($versionok) {

        register_callback(CALLBACK_BODY_START, 'nxti_component_addwidget');
        register_callback(CALLBACK_MENUS_INITIALIZED, 'nxti_component_addmenu');
    }
}


///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////


function nxti_component_addwidget( $cbtype, &$cbargs )
{
    global $nxti_component_name;

    /* nonzero if the URL includes 'nxti/index.php' (main NXTI path) */
    $page_is_nxti_index = strpos($_SERVER['REQUEST_URI'], $nxti_component_name . '/index.php');

    if ($cbargs['child'] == 1 && $page_is_nxti_index) {

        $hide = '';
        if (array_key_exists('msg', $_SESSION)) {
            $hide = 'style="display: none;"';
        }

        /* helpsystem_icon includes styles for right-float positioning in the child page */
        echo '<div id="NXTIWidgetGoesHere" class="helpsystem_icon"></div>';
    }
}

function nxti_component_addmenu($arg = null)
{
    global $nxti_component_name;
    $urlbase = get_component_url_base($nxti_component_name);
    
    //Figure out where I'm going on the menu
    $mi = find_menu_item(MENU_ADMIN, "menu-admin-missingobjects", "id");
    if ($mi == null) {
        return;
    }

    $order = grab_array_var($mi, "order", "");
    if ($order == "") {
        return;
    }

    $neworder = $order + 0.1;

    // Add this to the main home menu
    add_menu_item(MENU_ADMIN, array(
        "type" => "link",
        "title" => "SNMP Trap Interface",
        "id" => "menu-admin-nxti",
        "order" => $neworder,
        "opts" => array(
            "href" => $urlbase . "/index.php",
            "icon" => "fa-th-large"
        )
    ));
}
