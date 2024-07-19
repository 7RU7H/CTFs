<?php
//
// Nagios XI API Documentation
// Copyright (c) 2017-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/html-helpers.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);

if (!is_admin()) {
    die(_('Not authorized to view this page.'));
}

route_request();

function route_request()
{
    $page = grab_request_var("page", "");

    switch ($page) {
        default:
            show_main_api_page();
            break;
    }
}

function show_main_api_page()
{
    $apikey = get_apikey();

    do_page_start(array("page_title" => _('Callback Reference')), true);
?>

    <script src="../includes/js/api-help.js"></script>
    <!-- Keep the help section part of the page -->
    <script>
    $(document).ready(function() {
        // replace all instances of CALLBACK_ with strong text inside of cb-reference classes
        $('.cb-reference p').html(function(index, html) {
            return html.replace(/CALLBACK_\w+/g, '<strong>$&</strong>');  
        });

        // perform some basic highlighting on the code samples
        $('pre.code').html(function(index, html) {
            return html.replace(/\/\/.*/g, '<span style="color: #00b200;">$&</span>');
        });
    });
    </script>

    <div class="container-fluid help">
        <div class="row">
            <div class="col-sm-8 col-md-9 col-lg-9">
    
                <h1><?php echo _('Callback Reference'); ?></h1>
                <p><?php echo _('This document aims to be a comprehensive guide to developing additional functionality for Nagios XI using built-in Callbacks.'); ?></p>

                <div class="help-section cb-reference">
                    <a name="how-to"></a>
                    <h4><?php echo _("How to hook into a Nagios XI Callback"); ?></h4>
                    <p><?php echo _("So you think you're ready to add Callback functionality to your custom component or configuration wizard? Great! Here's how you do it.."); ?></p>
                    <h6><?php echo _("Choosing a Callback"); ?></h6>
                    <p><?php echo _("The first step is to decide which Callback you want to use. Do you want to add custom page headers, or do you want to create custom menus to show up in the navigation pane? The possibilities are <s>endless</s> almost endless. You can use the guide on the right hand side of this reference page to look through the list of available Callbacks. Click on the one that looks like the one you want and read the specific documentation regarding that particular Callback. For the purposes of this example, we're going to link our Nagios XI instance to a custom javascript file using the <code>CALLBACK_PAGE_HEAD</code> Callback."); ?></p>
                    <h6><?php echo _("Let's start coding!"); ?></h6>
                    <p><?php echo _("Development of Nagios XI components is outside of the scope of this document, so we'll be making the assumption that you have at least a basic working knowledge of creating and testing custom components. We're going to create a component entirely from scratch, so we need to come up with a name. We'll use nagiosxicallbacks. Create a directory in <strong>/usr/local/nagiosxi/html/includes/components/</strong> called <strong>nagiosxicallbacks</strong>."); ?></p>
                    <pre>mkdir /usr/local/nagiosxi/html/includes/components/nagiosxicallbacks</pre>
                    <p><?php echo _("Now we need to populate the directory with two files: <strong>nagiosxicallbacks.js</strong> and <strong>nagiosxicallbacks.inc.php</strong>"); ?></p>
                    <h6>nagiosxicallbacks.js</h6>
                    <pre class="code">// <?php echo _("make a popup so we know its working"); ?>

$(document).ready(function () {
    alert('Nagios XI Callbacks Demonstration!');
});</pre>
                    <h6>nagiosxicallbacks.inc.php</h6>
                    <pre class="code">&lt;?php

// <?php echo _("include the xi component helper"); ?>

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// <?php echo _("define/call our component initialize function"); ?>

nagiosxicallbacks_component_init();
function nagiosxicallbacks_component_init()
{

    // <?php echo _("information to pass to xi about our component"); ?>

    $args = array(
        COMPONENT_NAME =>           "nagiosxicallbacks",
        COMPONENT_VERSION =>        "1.0",
        COMPONENT_AUTHOR =>         "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION =>    "Demonstrate Nagios XI Callbacks inside of Custom Components",
        COMPONENT_TITLE =>          "Nagios XI Callbacks Demonstration"
        );

    // <?php echo _("register with xi"); ?>

    if (register_component("nagiosxicallbacks", $args)) {

        // <?php echo _("register a function to be called when CALLBACK_PAGE_HEAD is executed"); ?>

        <strong style='color: #ab0000; font-weight: bold;'>register_callback(CALLBACK_PAGE_HEAD, 'nagiosxicallbacks_cb_page_head');</strong>

    }
}

// <?php echo _("include javascript links in page heads in xi"); ?>

function nagiosxicallbacks_cb_page_head($callback_type, $callback_args) {

    // <?php echo _("make sure we are in the right callback"); ?>

    if ($callback_type == CALLBACK_PAGE_HEAD) {

        // <?php echo _("we only need to include our javascript if this is a child page"); ?>

        if ($callback_args["child"] == true) {

            echo "&lt;script type='text/javascript' src='" .  get_component_url_base("nagiosxicallbacks") . "/nagiosxicallbacks.js'&gt;&lt;/script&gt;";
        }
    }
}

?&gt;</pre>
                    <h6><?php echo _("What happened?"); ?></h6>
                    <p><?php echo _("If you followed the directions up to this point, you may have noticed a javascript alert window popping up on every page refresh. If you didn't get a popup, clear your browser's cache and try again."); ?></p>
                    <p><?php echo _("Got the popup now? Ok, good - lets go over the code. First, we defined our javascript file responsible for displaying the popup on page load. Then we defined a few functions in PHP responsible for making sure that the javascript file was included in the &lt;head&gt; of our Nagios XI pages."); ?></p>
                    <p><?php echo _("We defined a pretty basic component, and made sure to call the component initialize function. Inside of the initialize function, we declared our component with our <code>\$args</code> and calling register_component. We defined a function (<code>nagiosxicallbacks_cb_page_head</code>) to echo our script link."); ?></p>
                    <p><?php echo _("In order to make sure that our Callback function is actually called during the right time, we had to use <strong>register_callback</strong>. We supplied <code>CALLBACK_PAGE_HEAD</code> as the Callback argument and a string containing our function name as the function argument."); ?></p>
                    <p><?php echo _("When <code>CALLBACK_PAGE_HEAD</code> is executed in the Nagios XI stack, it automatically passes two variables to our function: <code>\$callback_type</code> and <code>\$callback_args</code>. The <code>\$callback_type</code> variable contains a value that corresponds directly to the Callback that is being executed - in this case it would be <code>CALLBACK_PAGE_HEAD</code>. The <code>\$callback_args</code> variable can contain different data for each Callback type; check each Callback's specific reference for passed values. In this particular case, the only argument thats passed is <code>\$callback_args[\"child\"]</code> - a boolean value representing whether this is a child page or not. As you can see, our code checks for both: the right <code>\$callback_type</code>, and to make sure we're only including the javascript when <code>\$callback_args[\"child\"]</code> is true (this means that its the page displayed in the main content frame)."); ?></p>
                </div>

                <div class="help-section cb-reference">
                    <a name="register-callback"></a>
                    <h4>register_callback($callback_type, $callback_function)</h4>
                    <p><?php echo _("The function used to register a callback_function with a specific Callback."); ?></p>
                    <h6>$callback_type</h6>
                    <p><em>Type: constant</em></p>
                    <p><?php echo _("One of the CALLBACK constants listed under Defined Constants in the reference guide."); ?></p>
                    <h6>$callback_function</h6>
                    <p><em>Type: string</em></p>
                    <p><?php echo _("Name of the function you want to call when this Callback is executed in the Nagios XI stack."); ?></p>
                </div>
                <div class="help-section cb-reference">
                    <a name="callback-function"></a>
                    <h4>callback_function($callback_type, &amp;$callback_arguments)</h4>
                    <p><?php echo _("The function (you define) to execute when a Callback is executed. The Nagios XI stack will pass the listed arguments to the function."); ?></p>
                    <h6>$callback_type</h6>
                    <p><em>Type: constant</em></p>
                    <p><?php echo _("The CALLBACK constant that is currently being executed."); ?></p>
                    <h6>&amp;$callback_arguments</h6>
                    <p><em>Type: array, by reference</em></p>
                    <p><?php echo _("Contains miscellaneous data used for manipulation in your customizations. <strong><em>If you change values in this array, they will be changed in the Nagios XI stack</em></strong>. Some of the Callbacks look for or require additional elements or changed values in this array, and the execution or output of the Nagios XI stack can be altered based on the value of the elements in this p."); ?></array>
                </div>
<?php

$callback_table = new help_table("Will be reset before each use");
$callback_data = array(
    array(
        "title" =>                  "CALLBACK_AUTHENTICATION_PASSED",
        "description" =>            _("Executed after a user's authentication is checked."),
        "notes" =>                  _("This happens whenever the authentication is checked AND passed - NOT when a user is authenticated. e.g.: Before Nagios XI decides whether or not you have adequate access to view a certain page (usually only when you're logged in). This happens very frequently."),
        "callback_args" =>          "",
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_BODY_END",
        "description" =>            sprintf(_("Executed directly before the %s tag is printed."), encode_form_val("</body>")),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_BODY_START",
        "description" =>            sprintf(_("Executed directly after the %s tag is printed."), encode_form_val("</body>")),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_CONFIG_SPLASH_SCREEN",
        "description" =>            _("Executed directly after the navigation links are printed on the Configure Options page."),
        "notes" =>                  _("Only occurs on the Configure Options page. This allows you to create custom navigation endpoints for your configuration landing page."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_CONTENT_END",
        "description" =>            _("Executed directly before the footer is printed."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_CONTENT_START",
        "description" =>            sprintf(_("Executed directly after %s is printed."), encode_form_val('<div id="mainframe">')),
        "notes" =>                  _("Only occurs if this is not a child page.") . " (\$callback_arguments array['child'] == false)",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_CUSTOM_TABLE_DATA",
        "description" =>            sprintf(_("Executed after each of the %s tags are written in the host and service status tables, but before the %s tag"), encode_form_val("<td>"), encode_form_val("</td>")),
        "notes" =>                  sprintf(_("Used when generating host/service status tables. Allows you to insert data to the end of each table row in the status table, BEFORE the %s tag is written."), encode_form_val("</tr>")),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("objecttype", "Constant: OBJECTTYPE_SERVICE or OBJECTTYPE_HOST", _("The type of table cell you're dealing with."))
                                        ->tr("host_name", "string", sprintf(_("If %s then this is the host name of the object that this cell refers to, if %s then this is the host name of the service_description of the object that this cell refers to."), "OBJECTTYPE_HOST", "OBJECTTYPE_SERVICE"))
                                        ->tr("service_description", "string", sprintf(_("If %s then this will be the service description of the service that this cell refers to."), "OBJECTTYPE_SERVICE"))
                                        ->tr("object_id", "integer", _("The ID of the object that this cell refers to."))
                                        ->tr("object_data", "SimpleXML", _("The XML of the object that this cell refers to."))
                                        ->tr("allow_html", "boolean", _("Value is set to inform whether or not HTML is allowed in the status table."))
                                        ->tr("table_data", "array", sprintf(_("The array to append elements to, they will be inserted at the end of this cell's row, before the %s."), encode_form_val("</tr>")))
                                        ->return_text(),
        "conditional_checks" =>     sprintf(_("If allow_html is true, then only %s and %s tags are allowed (you'll need those to create a table cell)."), encode_form_val("<td>"), encode_form_val("</td>")),
        "xi_output_changed" =>      "\$callback_arguments['table_data'] = array()",
        ),

    array(
        "title" =>                  "CALLBACK_CUSTOM_TABLE_HEADERS",
        "description" =>            sprintf(_("Executed after each of the %s tags are written in the host and service status tables, but before the %s tag"), encode_form_val("<th>"), encode_form_val("</tr>")),
        "notes" =>                  sprintf(_("Used when generating host/service status tables. Allows you to insert data to the end of the header table row in the status table, BEFORE the %s tag is written."), encode_form_val("</tr>")),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("objecttype", "Constant: OBJECTTYPE_SERVICE or OBJECTTYPE_HOST", _("The type of table you're dealing with"))
                                        ->tr("allow_html", "boolean", _("Value is set to inform whether or not HTML is allowed in the status table."))
                                        ->tr("table_headers", "array", _("The array to append elements to, they will be appended to the table header row."))
                                        ->return_text(),
        "conditional_checks" =>     sprintf(_("If allow_html is true, then only %s and %s tags are allowed (you'll need those to create a table header)."), encode_form_val("<th>"), encode_form_val("</th>")),
        "xi_output_changed" =>      "\$callback_arguments['table_headers'] = array()",
        ),

    array(
        "title" =>                  "CALLBACK_CUSTOM_TABLE_ICONS",
        "description" =>            sprintf(_("Executed right before the %s tag is printed."), encode_form_val("<div class='extraicons'>")),
        "notes" =>                  sprintf(_("Used when generating host/service status tables. Allows you to insert data into the icon section of the table cells, inside of %s."), encode_form_val("<div class='extraicons'></div>")),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("objecttype", "Constant: OBJECTTYPE_SERVICE or OBJECTTYPE_HOST", _("The type of table cell you're dealing with"))
                                        ->tr("host_name", "string", sprintf(_("If %s then this is the host name of the object that this cell refers to, if %s then this is the host name of the service_description of the object that this cell refers to."), "OBJECTTYPE_HOST", "OBJECTTYPE_SERVICE"))
                                        ->tr("service_description", "string", sprintf(_("If %s then this will be the service description of the service that this cell refers to."), "OBJECTTYPE_SERVICE"))
                                        ->tr("object_id", "integer", _("The ID of the object that this cell refers to."))
                                        ->tr("object_data", "SimpleXML", _("The XML of the object that this cell refers to."))
                                        ->tr("allow_html", "boolean", _("Value is set to inform whether or not HTML is allowed in the status table."))
                                        ->tr("icons", "array", sprintf(_("The array to append elements to, they will be inserted into this cell's %s section."), encode_form_val("<div class='extraicons'></div>")))
                                        ->return_text(),
        "conditional_checks" =>     sprintf(_("If allow_html is true, then only %s tags are allowed (you'll need that to display your icon)."), encode_form_val("<img>")),
        "xi_output_changed" =>      "\$callback_arguments['table_icons'] = array()",
        ),

    array(
        "title" =>                  "CALLBACK_EVENT_ADDED",
        "description" =>            _("Executed after an event is added to the XI database."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("event_id", "integer", _("The ID returned from the insert into the nagiosxi database."))
                                        ->tr("event_source", "integer", sprintf(_("The source of the event. You can find the defined types in %s in the %s section."), "constants.inc.php", "// EVENT SOURCES"))
                                        ->tr("event_type", "integer", sprintf(_("The type of event. You can find the defined types in %s in the %s section"), "constants.inc.php", "// EVENT TYPES"))
                                        ->tr("event_time", "timestamp", _("The UNIX timestamp of the time at which the event occured."))
                                        ->tr("event_meta", "string", _("Any associated data with this event. This string is in JSON format."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_EVENT_PROCESSED",
        "description" =>            sprintf(_("Executed after an event is processed by XI %s."), "(cron/eventman.php)"),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("event_id", "integer", _("The ID returned from the insert into the nagiosxi database."))
                                        ->tr("event_source", "integer", sprintf(_("The source of the event. You can find the defined types in %s in the %s section."), "constants.inc.php", "// EVENT SOURCES"))
                                        ->tr("event_type", "integer", sprintf(_("The type of event. You can find the defined types in %s in the %s section"), "constants.inc.php", "// EVENT TYPES"))
                                        ->tr("event_time", "timestamp", _("The UNIX timestamp of the time at which the event occured."))
                                        ->tr("event_meta", "string", _("Any associated data with this event. This string is in JSON format."))
                                        ->tr("logging_enabled", "boolean", _("Whether logging was enabled or disabled during this events processing."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
    
    array(
        "title" =>                  "CALLBACK_FOOTER_END",
        "description" =>            sprintf(_("Executed after the footer %s is printed, but before the page %s is printed."), encode_form_val("</div>"), encode_form_val("</div>")),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_FOOTER_START",
        "description" =>            _("Executed before the footer is printed."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_HEADER_END",
        "description" =>            sprintf(_("Executed before the header %s is printed."), encode_form_val("</div>")),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_HEADER_START",
        "description" =>            _("Executed directly after the header is printed."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_HOME_PAGE_OPTIONS",
        "description" =>            _("Used to override default home page options."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("destination", "string", _("Used to determine how to handle the other elements of the array."))
                                        ->tr("page_title", "string", _("The string to overwrite the page title with."))
                                        ->tr("page_url", "string", sprintf(_("The string used to redirect if %s."), encode_form_val("redirect_url == true")))
                                        ->tr("redirect_url", "boolean", _("Used to determine if we're going to redirect the homepage to another URL."))
                                        ->return_text(),
        "conditional_checks" =>     sprintf(_("If destination is equal to '%s', and %s is not blank, and %s is true, then the page will be redirected to %s's value"), "custom", "page_url", "redirect_url", "page_url") . "<br />" . 
                                    sprintf(_("If destination is equal to '%s', then the title of the page will be set to %s's value"), "homedashboard", "page_title"),
        "xi_output_changed" =>      "The title of the home page will be affected based on the conditional_checks",
        ),

    array(
        "title" =>                  "CALLBACK_HOST_DETAIL_ACTION_LINK",
        "description" =>            _("Used to add an action to the Quick Action section on the Host Status Detail page."),
        "notes" =>                  sprintf(_("Although not necessary, in order to maintain consistency, the preferred format for the actions array elements is: %s"), encode_form_val("<li><div class='commandimage'><img></div><div class='commandtext'><a href=\$actionlink>\$actiontext</a></div></li>")),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("hostname", "string", _("The hostname of the current host."))
                                        ->tr("host_id", "string", _("The ID of the current host."))
                                        ->tr("hoststatus_xml", "SimpleXML", _("The status XML of the current host."))
                                        ->tr("actions", "array", _("The array to append action elements to."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("The Quick Actions list on Host Status Detail pages will be affected by the elements appended to the %s array"), encode_form_val("\$cbargs['actions']")),
        ),

    array(
        "title" =>                  "CALLBACK_HOST_TABS_INIT",
        "description" =>            _("Append custom tabs to host status pages."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("host", "string", _("The current host."))
                                        ->tr("tabs", "array", sprintf(_("The array to append tab information to.%sEach tab to be appended is an array in the format of %s"), "<br>", encode_form_val("array('id' => string, 'title' => string, 'content' => string, 'icon' => string)")))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("New tabs will appear on host status pages based on the %s array"), encode_form_val("\$cbargs['tabs']")),
        ),
      
    array(
        "title" =>                  "CALLBACK_MENUS_DEFINED",
        "description" =>            _("Executed directly after Nagios XI default top-level menus are defined."),
        "notes" =>                  _("This can be used to add top-level menus to Nagios XI."),
        "callback_args" =>          sprintf(_("This callback actually returns the menu array as %s. The array is in the format of %s"), 
            encode_form_val("\$cbargs"), 
            "<pre>" . 
            print_r(array(
            'menu_name' => array(
                'menuitems' => array(
                    'numeric_identifier' => array(
                        'type' => "string 'html'/'menusection'/'linkspacer'/'link'", 
                        'title' => "string", 
                        'id' => "string", 
                        'order' => "numeric", 
                        'opts' => array(
                            'html' => "string (if type == 'html')", 
                            'id' => "string (if type == 'menusection')", 
                            'icon' => "string [i class] (if type == 'link')",
                            'href' => "string (if type == 'link')",
                            'target' => "string (if type == 'link')"
                            ),
                        ),
                    ),
                ),
            ), true) .
            "</pre>"),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("The Nagios XI menus will be altered depending on your removal or addition of menu items from the %s"), encode_form_val("\$callback_args.")),
        ),
        
    array(
        "title" =>                  "CALLBACK_MENUS_INITIALIZED_FINAL",
        "description" =>            _("Executed directly after all additional menu items have been initialized (ones created via callbacks)."),
        "notes" =>                  _("This can be used to sort menu items, etc."),
        "callback_args" =>          sprintf(_("These will be the same as the array listed in %s"), "CALLBACK_MENUS_DEFINED"),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("The Nagios XI menus will be altered depending on your removal or addition of menu items from the %s"), encode_form_val("\$callback_args.")),
        ),
        
    array(
        "title" =>                  "CALLBACK_MENUS_INITIALIZED",
        "description" =>            _("Executed directly after Nagios XI initializes default menu items."),
        "notes" =>                  _("This can be used to initialize your own menu items."),
        "callback_args" =>          sprintf(_("These will be the same as the array listed in %s"), "CALLBACK_MENUS_DEFINED"),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("The Nagios XI menus will be altered depending on your removal or addition of menu items from the %s"), encode_form_val("\$callback_args.")),
        ),

    array(
        "title" =>                  "CALLBACK_NOTIFICATION_EMAIL",
        "description" =>            _("Executed directly after Nagios XI processes the notification templates and gathers all information necessary to send an email notification, directly before sending that email."),
        "notes" =>                  _("This can be used to alter your email notifications."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("referer", "string", _("The referer of the message, used in the PHPmailer log"))
                                        ->tr("from", "string", sprintf(_("Who the message is sent as (%sFrom:%s header)"), '<code>', '</code>'))
                                        ->tr("to", "string", sprintf(_("The message recipient (%sTo:%s header)"), '<code>', '</code>'))
                                        ->tr("subject", "string", sprintf(_("The message's subject (%sSubject:%s header)"), '<code>', '</code>'))
                                        ->tr("message", "string", _("The message body after the macro processing has occured."))
                                        ->tr("meta", "array", _("The event meta data that is passed to the notification handler"))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("All of the elements of the array are used in processing the message. %sChanging any of the %s\$callback_arguments%s array will alter your email notification!%s"), '<strong>', '<code>', '</code>', '</strong>'),
        ),

    array(
        "title" =>                  "CALLBACK_NOTIFICATION_EMAIL_SENT",
        "description" =>            _("Executed directly after Nagios XI sends a notificaton email."),
        "notes" =>                  _("This can be used to clean up any processing or storage requirements necessary prior to sending an email otification."),
        "callback_args" =>          sprintf(_("These will be the same as the array listed in %s"), "CALLBACK_NOTIFICATION_EMAIL"),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_NOTIFICATION_SMS",
        "description" =>            _("Executed directly after Nagios XI processes the notification templates and gathers all information necessary to send an SMS notification, directly before sending that SMS."),
        "notes" =>                  _("This can be used to alter your SMS notifications."),
        "callback_args" =>          sprintf(_("These will be the same as the array listed in %s"), "CALLBACK_NOTIFICATION_EMAIL"),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("All of the elements of the array are used in processing the message. %sChanging any of the %s\$callback_arguments%s array will alter your SMS notification!%s"), '<strong>', '<code>', '</code>', '</strong>'),
        ),

    array(
        "title" =>                  "CALLBACK_NOTIFICATION_SMS_SENT",
        "description" =>            _("Executed directly after Nagios XI sends an SMS notification."),
        "notes" =>                  _("This can be used to clean up any processing or storage requirements necessary prior to sending an SMS notification."),
        "callback_args" =>          sprintf(_("These will be the same as the array listed in %s"), "CALLBACK_NOTIFICATION_SMS"),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_NOTIFICATION_TEMPLATE_EMAIL",
        "description" =>            _("Executed directly before Nagios XI processes the notification templates for an email notification."),
        "notes" =>                  _("This can be used to alter your email notification templates, which in turn can alter your final email notifications."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("subject", "string", sprintf(_("The message's subject (%sSubject:%s header), containing the pre-processed template macros."), '<code>', '</code>'))
                                        ->tr("message", "string", sprintf(_("Who the message is sent as (%sFrom:%s header), containing the pre-processed template macros."), '<code>', '</code>'))
                                        ->tr("meta", "array", sprintf(_("An array containing meta information regarding the notification. Can contain data regarding the host/contact/service/etc."), '<code>', '</code>'))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("All of the elements of the array are used in processing the message. %sChanging any of the %s\$callback_arguments%s array will alter your email notification!%s"), '<strong>', '<code>', '</code>', '</strong>'),
        ),

    array(
        "title" =>                  "CALLBACK_NOTIFICATION_TEMPLATE_SMS",
        "description" =>            _("Executed directly before Nagios XI processes the notification templates for an SMS notification."),
        "notes" =>                  _("This can be used to alter your SMS notification templates, which in turn can alter your final SMS notifications."),
        "callback_args" =>          sprintf(_("These will be the same as the array listed in %s"), "CALLBACK_NOTIFICATION_TEMPLATE_EMAIL"),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("All of the elements of the array are used in processing the message. %sChanging any of the %s\$callback_arguments%s array will alter your SMS notification!%s"), '<strong>', '<code>', '</code>', '</strong>'),
        ),
        
    array(
        "title" =>                  "CALLBACK_PAGE_HEAD",
        "description" =>            sprintf(_("Executed directly before the %s tag is printed."), encode_form_val("</head>")),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("child", "boolean", _("False if main window, true if main content (frame)."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_PREREQS_PASSED",
        "description" =>            sprintf(_("Executed during the %s function, if all prerequisites have been satisified (user password change, license agreement, one-time install executed)."), "check_prereqs()"),
        "notes" =>                  "",
        "callback_args" =>          "",
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_PROCESS_AUTH_INFO",
        "description" =>            _("Executed before default Nagios authentication methods."),
        "notes" =>                  _("Can be used to authenticate a user against external credentials."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("credentials", "array", sprintf(_("The array containing the user submitted information, in the form of %s"), "<code>array('username' => string, 'password' => string)</code>"))
                                        ->tr("login_ok", "integer", _("If set to 1, then user is authenticated."))
                                        ->tr("info_messages", "array", _("Array to append information messages to."))
                                        ->tr("debug_messages", "array", _("Array to append debug messages to."))
                                        ->return_text(),
        "conditional_checks" =>     _("Nagios XI will authenticate a user based on the value of ") . "<code>" . encode_form_val("\$cbargs['login_ok']") . "</code>",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_REPORTS_ACTION_LINK",
        "description" =>            sprintf(_("Executed directly after %s%s%s is printed on report pages."), "<code>", encode_form_val("<div class='reportexportlinks'>"), "</code>"),
        "notes" =>                  _("Use this to add action links (upper right hand corner) to report pages."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("title", "string", _("The title of the report."))
                                        ->tr("url", "string", _("The url of the report."))
                                        ->tr("meta", "array", _("An array containing any associated meta data."))
                                        ->tr("actions", "array", _("Array to append link html to."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("An action link will appear on the reports page depending on elements appended to the %s array."), encode_form_val("\$cbargs['actions']")),
        ),

    array(
        "title" =>                  "CALLBACK_SERVICE_DETAIL_ACTION_LINK",
        "description" =>            _("Used to add an action to the Quick Action section on the Service Status Detail page."),
        "notes" =>                  sprintf(_("Although not necessary, in order to maintain consistency, the preferred format for the actions array elements is: %s"), encode_form_val("<li><div class='commandimage'><img></div><div class='commandtext'><a href=\$actionlink>\$actiontext</a></div></li>")),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("hostname", "string", _("The hostname of the current service's host."))
                                        ->tr("servicename", "string", _("The service description of the current service."))
                                        ->tr("service_id", "string", _("The ID of the current service."))
                                        ->tr("servicestatus_xml", "SimpleXML", _("The status XML of the current service."))
                                        ->tr("actions", "array", _("The array to append action elements to."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("The Quick Actions list on Service Status Detail pages will be affected by the elements appended to the %s array"), encode_form_val("\$cbargs['actions']")),
        ),
 
    array(
        "title" =>                  "CALLBACK_SERVICE_TABS_INIT",
        "description" =>            _("Append custom tabs to service status pages."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("host", "string", _("The current host."))
                                        ->tr("service", "string", _("The current service."))
                                        ->tr("tabs", "array", sprintf(_("The array to append tab information to.%sEach tab to be appended is an array in the format of %s"), "<br>", encode_form_val("array('id' => string, 'title' => string, 'content' => string, 'icon' => string)")))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("New tabs will appear on service status pages based on the %s array"), encode_form_val("\$cbargs['tabs']")),
        ),
        
    array(
        "title" =>                  "CALLBACK_SESSION_STARTED",
        "description" =>            sprintf(_("Executed directly after PHP's %s is called."), "session_start()"),
        "notes" =>                  "",
        "callback_args" =>          "",
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_SUBSYS_APPLYCONFIG",
        "description" =>            _("Executed directly before the Command Subsystem performs an Apply Configuration or Reconfigure."),
        "notes" =>                  "",
        "callback_args" =>          "",
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_SUBSYS_CLEANER",
        "description" =>            _("Executed directly after automatic NagiosQL backups and NOM checkpoints are cleaned up."),
        "notes" =>                  "",
        "callback_args" =>          "",
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_SUBSYS_DBMAINT",
        "description" =>            _("Executed directly after the database maintenance is performed."),
        "notes" =>                  "",
        "callback_args" =>          "",
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_SUBSYS_GENERIC",
        "description" =>            _("Executed directly after any command is ran in the Command Subsystem."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("command", "string", _("The command that was executed by the Command Subsystem."))
                                        ->tr("command_data", "string", _("Any relevant data associated with the command."))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_TWO_FACTOR_AUTH",
        "description" =>            _("This callback is executed directly after a user has been successfully authenticated via the built in authentication methods or from a successful callback using") . " <code>CALLBACK_PROCESS_AUTH_INFO</code>",
        "notes" =>                  sprintf(_("If your two factor authentication requires redirection, take a look at the %s callback."), "<code>CALLBACK_TWO_FACTOR_POST</code>"),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("credentials", "array", sprintf(_("The array containing the user submitted information, in the form of %s"), "<code>array('username' => string, 'password' => string)</code>"))
                                        ->tr("2fa_ok", "integer", _("If set to 1, then user is two factor authenticated."))
                                        ->tr("2fa_disabled", "integer", _("If set to 1, then this will stop the two factor auth from beng used for any user."))
                                        ->tr("info_messages", "array", _("Array to append information messages to."))
                                        ->tr("debug_messages", "array", _("Array to append debug messages to."))
                                        ->return_text(),
        "conditional_checks" =>     _("Nagios XI will accept two factor authentication for a user based on the value of ") . "<code>" . encode_form_val("\$cbargs['2fa_ok']") . "</code>",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_TWO_FACTOR_POST",
        "description" =>            _("Executed after the session is initialized on login, but before any authentication or page display occurs."),
        "notes" =>                  _("Use this callback if your two factor authentication requires some kind of page postback."),
        "callback_args" =>          "",
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_USER_EDIT_HTML_GENERAL_SETTINGS",
        "description" =>            _("Executed directly after the last row is printed (but before the end table tag) to the screen when building the General Settings table on the Edit User page."),
        "notes" =>                  _("Define a function that prints html to hook into this portion of the Edit User page."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("user_id", "integer", "The ID of the user being edited (0 if user is being added).")
                                        ->tr("add", "boolean", "Whether this user is being added (true) or an existing user (false).")
                                        ->tr("session_user_id", "integer", "The ID of the user who is currently editing.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_USER_EDIT_HTML_PREFERENCES",
        "description" =>            _("Executed directly after the last row is printed (but before the end table tag) to the screen when building the Preferences table on the Edit User page."),
        "notes" =>                  _("Define a function that prints html to hook into this portion of the Edit User page."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("user_id", "integer", "The ID of the user being edited (0 if user is being added).")
                                        ->tr("add", "boolean", "Whether this user is being added (true) or an existing user (false).")
                                        ->tr("session_user_id", "integer", "The ID of the user who is currently editing.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_USER_EDIT_HTML_AUTH_SETTINGS",
        "description" =>            _("Executed directly after the last row is printed (but before the end table tag) to the screen when building the Authentication Settings table on the Edit User page."),
        "notes" =>                  _("Define a function that prints html to hook into this portion of the Edit User page."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("user_id", "integer", "The ID of the user being edited (0 if user is being added).")
                                        ->tr("add", "boolean", "Whether this user is being added (true) or an existing user (false).")
                                        ->tr("session_user_id", "integer", "The ID of the user who is currently editing.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_USER_EDIT_HTML_API_SETTINGS",
        "description" =>            _("Executed directly after the last row is printed (but before the end table tag) to the screen when building the API Settings table on the Edit User page."),
        "notes" =>                  _("Define a function that prints html to hook into this portion of the Edit User page."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("user_id", "integer", "The ID of the user being edited (0 if user is being added).")
                                        ->tr("add", "boolean", "Whether this user is being added (true) or an existing user (false).")
                                        ->tr("session_user_id", "integer", "The ID of the user who is currently editing.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_USER_EDIT_HTML_GENERIC",
        "description" =>            _("Executed directly before the div containing the form buttons (Edit User, Cancel)."),
        "notes" =>                  _("Define a function that prints html to hook into this portion of the Edit User page."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("user_id", "integer", "The ID of the user being edited (0 if user is being added).")
                                        ->tr("add", "boolean", "Whether this user is being added (true) or an existing user (false).")
                                        ->tr("session_user_id", "integer", "The ID of the user who is currently editing.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_USER_EDIT_PROCESS",
        "description" =>            _("Executed after the Edit User form has been submitted, after all of the main logic (add/edit) has been executed."),
        "notes" =>                  _("Define a function that looks for any form inputs you designed with the") . " <code>USER_EDIT_HTML_*</code> "
                                    . _("callbacks."),
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("user_id", "integer", "The ID of the user being edited (0 if user is being added).")
                                        ->tr("add", "boolean", "Whether this user is being added (true) or an existing user (false).")
                                        ->tr("session_user_id", "integer", "The ID of the user who is currently editing.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),

    array(
        "title" =>                  "CALLBACK_USER_CREATED",
        "description" =>            _("Executed directly after a user was successfully created."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("username", "string", "The username that was created.")
                                        ->tr("user_id", "integer", "The ID assigned to the created user.")
                                        ->tr("password", "string", "The password used to create the account.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_USER_DELETED",
        "description" =>            _("Executed directly after a user was successfully deleted."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("username", "string", "The username of the account that was deleted.")
                                        ->tr("user_id", "integer", "The ID assigned to the deleted user.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
        
    array(
        "title" =>                  "CALLBACK_USER_NOTIFICATION_MESSAGES_TABS_INIT",
        "description" =>            _("Append custom tabs to the Account/Notification Messages page."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("tabs", "array", sprintf(_("The array to append tab information to.%sEach tab to be appended is an array in the format of %s"), "<br>", encode_form_val("array('id' => string, 'title' => string, 'content' => string, 'icon' => string)")))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("New tabs will appear on the Account/Notification Messages page based on the %s array"), encode_form_val("\$cbargs['tabs']")),
        ),
        
    array(
        "title" =>                  "CALLBACK_USER_NOTIFICATION_METHODS_TABS_INIT",
        "description" =>            _("Append custom tabs to the Account/Notification Methods page."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("tabs", "array", sprintf(_("The array to append tab information to.%sEach tab to be appended is an array in the format of %s"), "<br>", encode_form_val("array('id' => string, 'title' => string, 'content' => string, 'icon' => string)")))
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      sprintf(_("New tabs will appear on the Account/Notification Methods page based on the %s array"), encode_form_val("\$cbargs['tabs']")),
        ),
        
    array(
        "title" =>                  "CALLBACK_USER_PASSWORD_CHANGED",
        "description" =>            _("Executed directly after a user was successfully deleted."),
        "notes" =>                  "",
        "callback_args" =>          $callback_table
                                        ->reset(_("Array key"), _("Value type"), _("Value description"))
                                        ->tr("username", "string", "The username of the account.")
                                        ->tr("user_id", "integer", "The ID assigned to the user account.")
                                        ->tr("password", "string", "The updated password.")
                                        ->return_text(),
        "conditional_checks" =>     "",
        "xi_output_changed" =>      "",
        ),
    );


$help_section = "";
$reference_section = "";
$count = 0;
foreach($callback_data as $callback) {

    if (empty($callback['title']))
        continue;

    // create the div for displaying data
    $help_section .= "
                <div class=\"help-section cb-reference\">
                    <a name=\"" . strtolower($callback['title']) . "\"></a>
                    <h4>" . $callback['title'] . "</h4>";

    if (!empty($callback['description']))
        $help_section .= "
                    <h6>" . $callback['description'] . "</h6>";

    $help_section .= "<hr width=\"60%\" />";

    if (!empty($callback['notes']))
        $help_section .= "
                    <p>" . _("Notes") . ": " . $callback['notes'] . "</p>";

    if (!empty($callback['callback_args']))
        $help_section .= "
                    <h6>\$callback_arguments " . _("array") . "</h6>
                    <p>" . $callback['callback_args'] . "</p>";

    if (!empty($callback['conditional_checks']))
        $help_section .= "
                    <h6>" . _("Conditional checks on") . " \$callback_arguments</h6>
                    <p>" . $callback['conditional_checks'] . "</p>";

    if (!empty($callback['xi_output_changed']))
        $help_section .= "
                    <h6>\$callback_arguments " . _("used in Nagios XI output") . "</h6>
                    <p>" . $callback['xi_output_changed'] . "</p>";

    $help_section .= "                    
                </div>
                ";

    // append to the reference navigation
    $reference_section .= "<li><a href=\"#" . strtolower($callback['title']) . "\">" . $callback['title'] . "</a></li>";
}

echo $help_section;
?>
            </div>

             <div class="col-sm-4 col-md-3 col-lg-3 nav-box">
                <div class="well help-right-nav">
                    <h5><?php echo _('Callback Reference'); ?></h5>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _("How to"); ?></p>
                    <ul>
                        <li><a href="#how-to"><?php echo _("Hook into a Nagios XI Callback"); ?></a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _("Internal functions"); ?></p>
                    <ul>
                        <li><a href="#register-callback">register_callback</a></li>
                        <li><a href="#callback-function">callback_function</a></li>
                    </ul>
                    <p style="margin: 10px 0; padding: 0;"><?php echo _("Available Callbacks"); ?></p>
                    <ul>
                        <?php echo $reference_section; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<?php
    do_page_end(true);
}
