<?php
/**
 *  Example template for custom login splash page
 *
 *  This file can be modified for use as a custom login splash page for Nagios XI
 *  Implement the use of this include by accessing the Admin->Manage Components->Custom Login Splash component,
 *  and specify this absolute directory location for the include file.
 */
?>

<style type="text/css">
a.product-select-tab { display: inline-block; font-family: 'robotoblack'; background-color: #FFF; padding: 2px 15px; font-weight: bold; font-size: 40px; margin-right: 10px; text-decoration: none; border: 2px solid #EEE; }
a.product-select-tab.xi { color: #4D89F9; }
a.product-select-tab.fusion { color: #444; }
a.product-select-tab.logserver { color: #23B55E; }
a.product-select-tab.na { color: #FFA121; }
a.product-select-tab:hover { border: 2px solid transparent; color: #FFF; }
a.product-select-tab.xi:hover { background-color: #4D89F9; }
a.product-select-tab.fusion:hover { background-color: #444; }
a.product-select-tab.logserver:hover { background-color: #23B55E; }
a.product-select-tab.na:hover { background-color: #FFA121; }
.product-tab { display: none; background-color: #F6F6F6; margin-top: 10px; padding: 15px; }
</style>

<script type="text/javascript">
$(document).ready(function() {

    // Hover change 'tab'
    $('a.product-select-tab').mouseenter(function() {
        var type = $(this).data('tab');
        $('.product-tab').hide();
        $('.product-tab.' + type).show();
    });

});
</script>

<div class="loginsplash"></div>

<h3><?php echo _("Nagios Products"); ?></h3>
<div style="margin: 20px 0 35px 0;">

    <a class="product-select-tab xi" data-tab="xi" target="_new" href="https://www.nagios.com/products/nagios-xi/">XI</a>
    <a class="product-select-tab fusion" data-tab="fusion" target="_new" href="https://www.nagios.com/products/nagios-fusion/">F</a>
    <a class="product-select-tab logserver" data-tab="logserver" target="_new" href="https://www.nagios.com/products/nagios-log-server/">LS</a>
    <a class="product-select-tab na" data-tab="na" target="_new" href="https://www.nagios.com/products/nagios-network-analyzer/">NA</a>

    <div class="product-tab xi" style="display: block;">
        <b><?php echo _("Nagios XI"); ?></b>
        <div style="margin-top: 10px;"><?php echo _('Provides monitoring of all mission-critical infrastructure components including applications, services, operating systems, network protocols, systems metrics, and network infrastructure. Hundreds of third-party addons provide for monitoring of virtually all in-house applications, services, and systems.'); ?></div>
    </div>

    <div class="product-tab fusion">
        <b><?php echo _("Nagios Fusion"); ?></b>
        <div style="margin-top: 10px;"><?php echo _('Provides IT operations staff and management with quick, at-a-glance visual indication of problems anywhere across your IT infrastructure. Integrates with both Nagios Core and Nagios XI monitoring servers to provide infrastructure-wide visibility.'); ?></div>
    </div>

    <div class="product-tab logserver"">
        <b><?php echo _("Nagios Log Server"); ?></b>
        <div style="margin-top: 10px;"><?php echo _('Nagios Log Server greatly simplifies the process of searching your log data. Set up alerts to notify you when potential threats arise, or simply query your log data to quickly audit any system. With Nagios Log Server, you get all of your log data in one location, with high availability and fail-over built right in. Quickly configure your servers to send all log data with easy source setup wizards and start monitoring your logs in minutes.'); ?></div>
    </div>

    <div class="product-tab na">
        <b><?php echo _("Nagios Network Analyzer"); ?></b>
        <div style="margin-top: 10px;"><?php echo _('Network Analyzer provides an in-depth look at all network traffic sources and potential security threats allowing system admins to quickly gather high-level information regarding the health of the network as well as highly granular data for complete and thorough network analysis.'); ?></div>
    </div>

</div>

<h3><?php echo _("Contact Us"); ?></h3>
<p>
    <?php echo _("Looking for more information? Have a technical or sales question?"); ?>
</p>
<table class="table table-condensed table-no-border" style="width: auto;">
    <tr>
        <td style="vertical-align: top; padding-right: 15px;">
            <div><b><?php echo _("Sales"); ?></b></div>
            <?php echo _("Phone"); ?>: (651) 204-9102
            <br><?php echo _("Email"); ?>: <a href="mailto:sales@nagios.com">sales@nagios.com</a>
        </td>
        <td style="vertical-align: top; padding-right: 15px;">
            <div><b><?php echo _("Web"); ?></b></div>
            <a href="https://www.nagios.com/" target="_blank" rel="noreferrer"><?php echo _("Nagios Website"); ?></a><br>
            <a href="https://exchange.nagios.com/" target="_blank" rel="noreferrer"><?php echo _("Nagios Exchange"); ?></a><br>
        </td>
        <td style="vertical-align: top;">
            <div><b><?php echo _("Support"); ?></b></div>
            <a href="https://support.nagios.com/forum/" target="_blank" rel="noreferrer"><?php echo _("Support Forum"); ?></a><br>
            <a href="https://support.nagios.com/kb/" target="_blank" rel="noreferrer"><?php echo _("Knowledgebase"); ?></a>
        </td>
    </tr>
</table>