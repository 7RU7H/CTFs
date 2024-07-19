<?php
//
// Nagios XI API Documentation
// Copyright (c) 2016-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

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

    do_page_start(array("page_title" => _('API - Common Solutions')), true);
?>

    <div class="container-fluid help">
        <div class="row">
            <div class="col-sm-8 col-md-9 col-lg-9">

                <h1><?php echo _('API - Common Solutions'); ?></h1>

                <p><?php echo _('This document explains some of the solutions that can be created by using the API. These examples are meant to help users build out their own scripts and software that use the Nagios XI API to integrate into.'); ?></p>

                <ul>
                    <li><a href="#update-service"><?php echo _('Update a Single Value for a Service'); ?></a></li>
                    <li><a href="#add-hosts-service"><?php echo _('Add Multiple Hosts to a Service'); ?></a></li>
                    <li><a href="#delete-multiples"><?php echo _('Removing Services from Multiple Hosts'); ?></a></li>
                    <li><a href="#scheduling-downtime"><?php echo _('Scheduling Downtime for Multiple Hosts and Services'); ?></a></li>
                </ul>

                <a name="update-service"></a>
                <div class="help-section common-solutions">

                    <h4><?php echo _('Update a Single Value for a Service'); ?></h4>

                    <p><?php echo _('With the <code>PUT config/service</code> API endpoint, you can edit multiple or just a single configuration option for a service. You can also do this with other objects using PUT requests.'); ?></p>
                    <p><?php echo _('For this example, we are going to use '); ?></p>

                </div>

                <a name="add-hosts-service"></a>
                <div class="help-section common-solutions">
                    
                    <h4><?php echo _('Add Multiple Hosts to a Service'); ?></h4>

                    <p><?php echo _('With the <code>PUT config/service</code> API endpoint, you are able to edit the value to replace all the values. This endpoing is only for completely overwriting parameters for a config. For instance, you are not able to just add a single item to the <code>host_name</code> parameter. In order to add a bunch of hosts, we need to send the full list like so:'); ?></p>

                    <pre>host_name=host1,host2,host5,host10</pre>

                    <p><?php echo _('You can then use the above with the full API call below:'); ?></p>

                    <pre><code>curl -XPUT "<?php echo get_base_url(); ?>api/v1/config/service/localhost/PING?apikey=<?php echo $apikey; ?>&amp;host_name=host1,host2,host5,host10"</code></pre>

                    <p><?php echo _('This will overwrite the <code>host_name</code> values, assigning the service object to the four specified hosts.'); ?></p>

                </div>

                <a name="delete-multiples"></a>
                <div class="help-section common-solutions">

                    <h4><?php echo _('Removing Services from Multiple Hosts'); ?></h4>

                    <p><?php echo _('If you have a set of hosts, such as <code>host1</code>, <code>host2</code>, <code>host3</code> and you would like to remove the services <code>PING</code> and <code>SSH</code> from all of them with one single API call, you can do that by sending multiple paramters via an array in the API call. Note that if a service does not exist on a host, it will not error out and will still continue to remove the service from the rest of the hosts.'); ?></p>

                    <p><?php echo _('To specify multiple hosts, you can pass the <code>host_name[]</code> parameter as an array in the URL. For the hosts:'); ?></p>

                    <pre>host_name[]=host1&amp;host_name[]=host2&amp;host_name[]=host3</pre>

                    <p><?php echo _('Then for the services pass the <code>service_description[]</code> variable:'); ?></p>

                    <pre>service_description[]=PING&amp;service_description[]=SSH</pre>

                    <p><?php echo _('Combine these into the full API call below:'); ?></p>

                    <pre><code>curl -XDELETE "<?php echo get_base_url(); ?>api/v1/config/service?apikey=<?php echo $apikey; ?>&amp;host_name[]=host1&amp;host_name[]=host2&amp;host_name[]=host3&service_description[]=PING&amp;service_description[]=SSH"</code></pre>

                </div>

                <a name="scheduling-downtime"></a>
                <div class="help-section common-solutions">

                    <h4><?php echo _('Scheduling Downtime for Multiple Hosts and Services'); ?></h4>

                    <p><?php echo _('If you need to schedule a downtime for multiple hosts, for example <code>host1</code>, <code>host2</code>, and <code>host3</code>, you can then use the following API call:'); ?></p>                    

                    <pre><code>curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/scheduleddowntime?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "comment=Test downtime creation&amp;start=<?php echo time() + (60*60); ?>&amp;end=<?php echo time() + (2*60*60); ?>&amp;hosts[]=host1&amp;hosts[]=host2&amp;hosts[]=host3"</code></pre>

                    <p><?php echo _('You can schedule a downtime for multiple services, for example <code>service1</code>, <code>service2</code>, and <code>service3</code> on the same host (<code>host1</code>) by running:'); ?></p>                    

                    <pre><code>curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/scheduleddowntime?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "comment=Test downtime creation&amp;start=<?php echo time() + (60*60); ?>&amp;end=<?php echo time() + (2*60*60); ?>&amp;services[host1][]=service1&amp;services[host1][]=service2&amp;services[host1][]=service3"</code></pre>

                    <p>or use the API call bellow to schedule a downtime for multiple services on different hosts:</p>

                    <pre><code>curl -XPOST "<?php echo get_base_url(); ?>api/v1/system/scheduleddowntime?apikey=<?php echo $apikey; ?>&amp;pretty=1" -d "comment=Test downtime creation&amp;start=<?php echo time() + (60*60); ?>&amp;end=<?php echo time() + (2*60*60); ?>&amp;services[host1][]=service1&amp;services[host2][]=service2&amp;services[host3][]=service3"</code></pre>

                </div>

            </div>
        </div>
    </div>

<?php
    do_page_end(true);
}