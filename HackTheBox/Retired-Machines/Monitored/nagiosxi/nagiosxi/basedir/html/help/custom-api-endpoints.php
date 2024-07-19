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

    do_page_start(array("page_title" => _('Custom API Endpoints')), true);
?>
    <script>
    $(document).ready(function() {
        // perform some basic highlighting on the code samples
        $('pre.code').html(function(index, html) {
            return html.replace(/\/\/.*/g, '<span style="color: #00b200;">$&</span>');
        });
    });
    </script>

<div class="container-fluid" style="padding: 0 5px;">
    <div class="row">
        <div class="col-sm-12 col-lg-9">

            <h1><?php echo _('Custom API Endpoints'); ?></h1>
            <p><?php echo _('This document is for developers who want to add API endpoints to their Nagios XI system. This document shows how, using a custom component.'); ?></p>

            <div class="help-section">
                    <a name="custom_endpoints"></a>
                    <h4><?php echo _('Custom Endpoints'); ?></h4>
                    <p><?php echo _("We're going to cover how to create custom endpoints for use in your Nagios XI instance. These will automatically become available to any user that has access to the API once they're registered."); ?></p>
                    <h6><?php echo _('Creating the necessary code'); ?></h6>
                    <p><?php echo _("Development of Nagios XI components is outside of the scope of this document, so we'll be making the assumption that you have at least a basic working knowledge of creating and testing custom components. We'll use nagiosxicustomendpoints as a working name for our example component. Create a directory in"); ?><strong>/usr/local/nagiosxi/html/includes/components/</strong> <?php echo _('called'); ?> <strong>nagiosxicustomendpoints</strong>.</p>
                    <pre>mkdir /usr/local/nagiosxi/html/includes/components/nagiosxicustomendpoints</pre>
                    <p><?php echo _('Now we need to populate the directory with only one file'); ?>: <strong>nagiosxicustomendpoints.inc.php</strong></p>
                    <h6>nagiosxicustomendpoints.inc.php</h6>
                    <pre class="code">&lt;?php

// <?php echo _('include the xi component helper'); ?>

require_once(dirname(__FILE__) . '/../componenthelper.inc.php');

// <?php echo _('define/call our component initialize function'); ?>

nagiosxicustomendpoints_component_init();
function nagiosxicustomendpoints_component_init()
{

    // <?php echo _('information to pass to xi about our component'); ?>

    $args = array(
        COMPONENT_NAME =>           "nagiosxicustomendpoints",
        COMPONENT_VERSION =>        "1.0",
        COMPONENT_AUTHOR =>         "Nagios Enterprises, LLC",
        COMPONENT_DESCRIPTION =>    "Demonstrate Nagios XI Custom API Endpoints",
        COMPONENT_TITLE =>          "Nagios XI Custom API Endpoints Example"
        );

    // <?php echo _('register with xi'); ?>

    register_component("nagiosxicustomendpoints", $args);

    // <?php echo _('register our custom api handler'); ?>

    <strong style='color: #ab0000; font-weight: bold;'>register_custom_api_callback('awesome', 'example', 'nagiosxicustomendpoints_awesome_example');</strong>

}

// <?php echo _('the function to be called when we reach our custom endpoint via api'); ?>

function nagiosxicustomendpoints_awesome_example($endpoint, $verb, $args) {
    
    $argslist = '';
    foreach ($args as $key =&gt; $arg) {
        $argslist .= "&lt;arg&gt;&lt;num&gt;{$key}&lt;/num&gt;&lt;data&gt;{$arg}&lt;/data&gt;&lt;/arg&gt;";
    }

    $xml = "    &lt;xml&gt;
                    &lt;endpoint&gt;{$endpoint}&lt;/endpoint&gt;
                    &lt;verb&gt;{$verb}&lt;/verb&gt;
                    &lt;argslist&gt;{$argslist}&lt;/argslist&gt;
                &lt;/xml&gt;";

    $data = simplexml_load_string($xml);
    return json_encode($data);
}

?&gt;</pre>
                    <p><?php echo _("Once you've created this file, you can use the Example cURL Request located below to check out your new Custom API Endpoint."); ?></p>
                    <div>
                        <h6><?php echo _('Example cURL Request'); ?>:</h6>
                        <pre class="curl-request">curl -XGET "<?php echo get_base_url(); ?>api/v1/awesome/example/data1/data2?apikey=<?php echo $apikey; ?>&amp;pretty=1"</pre> <a href="<?php echo get_base_url(); ?>api/v1/awesome/example/data1/data2?apikey=<?php echo $apikey; ?>&amp;pretty=1" target="_blank" rel="noreferrer" class="api-popout tt-bind" title="<?php echo _('Open URL in browser window'); ?>"><i class="fa fa-share icon-large"></i></a>
                        <div class="clear"></div>
                    </div>
                    <h6><?php echo _('Response JSON'); ?>:</h6>
                    <pre>{
    "xml": {
        "endpoint": "awesome",
        "verb": "example",
        "argslist": {
            "arg": [
                {
                    "num": "0",
                    "data": "data1"
                }, 
                {
                    "num": "1",
                    "data": "data2"
                }, 
            ]
        }
    }
}</pre>
                    <h6><?php echo _('Explanation and Notes'); ?></h6>
                    <p><?php echo _('The function register_custom_api_callback takes 3 arguments. They are, in order: the endpoint to specify, the verb to specify, and the function to call when this endpoint has been reached.'); ?></p>
                    <p><?php echo _('The callback function (nagiosxicustomendpoints_awesome_example in our example) should be defined with 3 arguments as well. They are, in order: the endpoint that was called, the verb that was called, and the array of args appended after the endpoint.'); ?></p>
                    <p><?php echo _('Keep in mind the following regarding custom endpoints:'); ?>
                        <ul>
                            <li><?php echo _('You can override existing endpoints and verbs'); ?>.</li>
                            <li><?php echo _('There is no automatic conversion to JSON when using Custom API Endpoints. Whatever is returned from your callback function will be printed as the API Response.'); ?></li>
                            <li><?php echo _('You can pull additional request variables via the Nagios XI functions like grab_request_var() or the standard PHP globals like $_GET and $_POST.'); ?></li>
                        </ul>
                    </p>
                </div>

        </div>
    </div>
</div>

<?php
    do_page_end(true);
}
