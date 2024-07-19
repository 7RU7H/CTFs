#!/usr/bin/php -q
<?php
//
// Nagios Core Contact Notofication Handler (Uses Nagios XI Mail Settings)
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//
// Example Commands:
//
// define command {
//     command_name    notify-host-by-xi-email
//     command_line    /usr/bin/php /usr/local/nagiosxi/scripts/contact_notification_handler.php --message="***** Nagios Monitor XI Alert *****\n\nNotification Type: $NOTIFICATIONTYPE$\nHost: $HOSTNAME$\nState: $HOSTSTATE$\nAddress: $HOSTADDRESS$\nInfo: $HOSTOUTPUT$\n\nDate/Time: $LONGDATETIME$\n" --subject="** $NOTIFICATIONTYPE$ Host Alert: $HOSTNAME$ is $HOSTSTATE$ **" --contactemail="$CONTACTEMAIL$"
// }
//
// define command {
//     command_name    notify-service-by-xi-email
//     command_line    /usr/bin/php /usr/local/nagiosxi/scripts/contact_notification_handler.php --message="***** Nagios Monitor XI Alert *****\n\nNotification Type: $NOTIFICATIONTYPE$\n\nService: $SERVICEDESC$\nHost: $HOSTALIAS$\nAddress: $HOSTADDRESS$\nState: $SERVICESTATE$\n\nDate/Time: $LONGDATETIME$\n\nAdditional Info:\n\n$SERVICEOUTPUT$" --subject="** $NOTIFICATIONTYPE$ Service Alert: $HOSTALIAS$/$SERVICEDESC$ is $SERVICESTATE$ **" --contactemail="$CONTACTEMAIL$"
// }

define("SUBSYSTEM",1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils-email.inc.php');

handle_notification();

function handle_notification()
{
    global $argv;

    $debug = false;
    $args = parse_argv($argv);

    // Make database connections
    $dbok = db_connect_all();
    if (!$dbok) {
        echo "Error: Could not connect to databases!\n";
        exit();
    }

    // Submit the event
    $event_meta = array();
    foreach ($args as $var => $val) {
        $event_meta[$var] = $val;
    }

    // Email notifications
    if (isset($args['contactemail']) && isset($args['message']) && isset($args['subject'])) {

        if ($debug == true) {
            echo _("An email notification will be sent")."...\n\n";
        }

        // Use this for debug output in PHPmailer log
        $debugmsg = "";

        // Set where email is coming from for PHPmailer log
        $send_mail_referer = "scripts/contact_notification_handler.php > Notification Handler Script";

        $opts = array(
            "to" => $args['contactemail'],
            "subject" => $args['subject']
        );
        $opts["message"] = str_replace('\n', "\n", $args['message']);

        if ($debug == true) {
            echo "Email Notification Data:\n\n";
            print_r($opts);
            echo "\n\n";
        }

        send_email($opts, $debugmsg, $send_mail_referer);
    } else {
        echo "Requires the following: --contactemail --subject --message\n";
    }
}
