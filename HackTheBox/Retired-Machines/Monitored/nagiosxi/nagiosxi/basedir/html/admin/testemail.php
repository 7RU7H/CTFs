<?php
//
// Test email script - passed here by the test email link in mail settings.
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

////TODO: Make this a modal window instead of a page we go to (BB)

require_once(dirname(__FILE__) . '/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

route_request();

function route_request()
{
    global $request;

    if (isset($request['update'])) {
        do_test();
    } else {
        show_test();
    }
}


function show_test($error = false, $msg = "")
{
    $email = get_user_attr(0, "email");

    do_page_start(array("page_title" => _('Test Email Settings')), true);
?>

    <h1><?php echo _('Test Email Settings'); ?></h1>

    <?php display_message($error, false, $msg); ?>

    <p><?php echo sprintf(_('Use this to send a test email to your current logged in user address to verify you can recieve alerts from %s.'), get_product_name()); ?></p>

    <form method="post" action="">
        <input type="hidden" name="update" value="1">
        <p><?php echo _("An email will be sent to"); ?>: <b><?php echo $email; ?></b></p>
        <p><a href="<?php echo get_base_url() . "account/?xiwindow=main.php"; ?>" target="_top"><b><?php echo _("Change your email address"); ?></b></a></p>
        <div style="margin-top: 20px;">
            <a href="mailsettings.php" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i> <?php echo _('Back'); ?></a>
            <button type="submit" class="btn btn-sm btn-primary" name="sendbutton"><i class="fa fa-paper-plane"></i> <?php echo _('Send Test Email'); ?></button>
        </div>
    </form>

    <?php

    do_page_end(true);
    exit();
}


/**
 * Do the actual email test and send an email
 */
function do_test()
{
    // Don't allow sending test emails in demo mode
    if (in_demo_mode() == true) {
        show_test(true, _("Changes are disabled while in demo mode."));
    }

    $email = grab_request_var("email", "");
    $test_email = true;
    $output = array();
    $debugmsg = "";

    // Get the user's email address
    if (empty($email)) {
        $email = get_user_attr(0, "email");
    }

    // Send a test email notification
    if ($test_email == true) {
        // Get the email subject and message
        $subject = sprintf(_("%s Email Test"), get_product_name());
        $message = sprintf(_("This is a test email from %s"), get_product_name());

        // Set where email is coming from for PHPmailer log
        $send_mail_referer = "admin/testemail.php";

        $opts = array(
            "to" => $email,
            "subject" => $subject,
        );
        $opts["message"] = $message;
        
        /*
         * send_email() - Sends the email using utils-email.inc.php
         * 
         * @param array $opts               - array of options for sending email
         * @param &string $debugmsg         - debug message - passed by reference and updated with debug message
         * @param string $send_mail_referer - page that is sending the email
         * 
         * @return bool - true if email was sent successfully, false if not
         */
        try {
            $result = send_email($opts, $debugmsg, $send_mail_referer);
            $opts["debug"] = true;

            $output[] = _("A test email was sent to ") . "<b>" . $email . "</b>";
            $output[] = "----";
            $output[] = _("Mailer said") . ": <b>" . $debugmsg . "</b>";

            // Check for errors
            if ($result == false) {
                $output[] = _("An error occurred sending a test email!");
                show_test(true, $output);
            }
        } catch (Exception $e) {
            $output[] = '<p><strong>'._("A critical error has occurred sending a test email!").'</strong></p>';
            $output[] = '<p><strong>Error: </strong>' . $e->getMessage() . '</p>';
            $output[] = '
                <button class="btn btn-sm btn-default collapsible">Details</button> <br><br>
                <div class="collapsible-content">
                    <p><strong>File:</strong> ' . $e->getFile() . '</p>
                    <p><strong>Line:</strong> ' . $e->getLine() . '</p>
                    <p><strong>Code:</strong></p>
                    <pre>' . $e->getTraceAsString() . '</pre>' .
                    (($e->getResponseBody()) ? '<pre>' . print_r($e->getResponseBody(), true) . '</pre>' : '') .
                    '<style>
                        .collapsible-content {
                            display: none;
                            overflow: hidden;
                        }

                        .collapsible.active + .collapsible-content {
                            display: block;
                        }
                    </style>
                    <script>
                        $(document).ready(function() {
                            $(".collapsible").on("click", function() {
                                var content = $(this).next().next().next();
                                content.toggle();
                            });
                        });
                    </script>
                </div>';

            show_test(true, $output);
        }
    }

    send_to_audit_log(_('Sent a test email'), AUDITLOGTYPE_INFO);

    show_test(false, $output);
    return $output;
}

//custom error handler
function error_to_exception($errno, $errstr, $errfile, $errline){
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('error_to_exception');
