<?php
//
// XI Recurring Scheduled Downtime
// Copyright (c) 2010-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../componenthelper.inc.php');


// Initialization stuff
pre_init();
init_session();
grab_request_vars();

// Check prereqs and authentication
check_prereqs();
check_authentication();


route_request();


function route_request()
{
    licensed_feature_check();
    
    $mode = grab_request_var("mode", "");
    switch ($mode) {
        case "add":
            recurringdowntime_add_downtime();
            break;
        case "edit":
            recurringdowntime_add_downtime($edit = true);
            break;
        case "delete":
            recurringdowntime_delete_downtime();
            break;
        default:
            recurringdowntime_show_downtime();
            break;
    }
}


/**
 * Generate random-ish sid for new entries
 *
 * @return string
 */
function recurringdowntime_generate_sid()
{
    return md5(uniqid(microtime()));
}


function recurringdowntime_delete_downtime()
{
    global $request;

    // check session
    check_nagios_session_protector();

    if (!$request["sid"]) {
        header("Location:" . $_SERVER["HTTP_REFERER"]);
        exit;
    }

    $delete_tab = "";
    $cfg = recurringdowntime_get_cfg();
    $cfg_to_delete = $cfg[$request["sid"]];
    if (is_readonly_user(0)) {
        header("Location:" . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    if ($cfg_to_delete["schedule_type"] == "hostgroup") {
        $delete_tab = "#hostgroup-tab";
        if (!is_authorized_for_hostgroup(0, $cfg_to_delete["hostgroup_name"])) {
            header("Location:" . $_SERVER["HTTP_REFERER"] . $delete_tab);
            exit;
        }
    } elseif ($cfg_to_delete["schedule_type"] == "servicegroup") {
        $delete_tab = "#servicegroup-tab";
        if (!is_authorized_for_servicegroup(0, $cfg_to_delete["servicegroup_name"])) {
            header("Location:" . $_SERVER["HTTP_REFERER"] . $delete_tab);
            exit;
        }
    } elseif ($cfg_to_delete["schedule_type"] == "service") {
        $delete_tab = "#service-tab";
        if ($request["service_description"] == '*') {
            if (!is_authorized_for_host(0, $cfg_to_delete["host_name"])) {
                header("Location:" . $_SERVER["HTTP_REFERER"] . $delete_tab);
                exit;
            }
        } else {
            if (strstr($request["service_description"], "*")) {
                $search_str = "lk:" . str_replace("*", "%", $request["service_description"]);
            } else {
                $search_str = $request["service_description"];
            }
            if (!is_authorized_for_service(0, $cfg_to_delete["host_name"], $search_str)) {
                header("Location:" . $_SERVER["HTTP_REFERER"] . $delete_tab);
                exit;
            }
        }
    } elseif ($cfg_to_delete["schedule_type"] == "host") {
        $delete_tab = "#host-tab";
        if (!is_authorized_for_host(0, $cfg_to_delete["host_name"])) {
            header("Location:" . $_SERVER["HTTP_REFERER"] . $delete_tab);
            exit;
        }
    }
    unset($cfg[$request["sid"]]);
    recurringdowntime_write_cfg($cfg);
    header("Location:" . $_SERVER["HTTP_REFERER"] . $delete_tab);
    exit;
}


/**
 * @param bool $edit
 */
function recurringdowntime_add_downtime($edit = false) {
    global $request;

    // check session
    check_nagios_session_protector();

    if ($edit && !$request["sid"]) {
        $edit = false;
    }

    if ($edit) {
        $arr_cfg = recurringdowntime_get_cfg();
        $formvars = $arr_cfg[$request["sid"]];
        $days = array("mon", "tue", "wed", "thu", "fri", "sat", "sun");
        $selected_days = explode(",", $formvars["days_of_week"]);
        unset($formvars["days_of_week"]);
        for ($i = 0; $i < 7; $i++) {
            if (in_array($days[$i], $selected_days)) {
                $formvars["days_of_week"][$i] = "on";
            }
        }
        
        $months = array( "jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec" );
        $selected_months = explode(",", $formvars["months_of_year"]);
        unset($formvars["months_of_year"]);
        for ($i = 0; $i < 12; $i++) {
            if (in_array($months[$i], $selected_months)) {
                $formvars["months_of_year"][$i] = "on";
            }
        }

        if (count($formvars) == 0) {
            echo "<strong>" . _('The requested schedule id (sid) is not valid.') . "</strong>";
            exit;
        }
        if ($arr_cfg[$request["sid"]]["schedule_type"] == "hostgroup") {
            $form_mode = "hostgroup";
        } elseif ($arr_cfg[$request["sid"]]["schedule_type"] == "servicegroup") {
            $form_mode = "servicegroup";
        } elseif ($arr_cfg[$request["sid"]]["schedule_type"] == "service") {
            $form_mode = "service";
        } else {
            $form_mode = "host";
        }
    } else {
        $form_mode = $request["type"];

        $formvars["host_name"] = grab_request_var("host_name", grab_request_var("host", ""));
        $formvars["childoptions"] = grab_request_var("childoptions", grab_request_var("childoptions", ""));
        $formvars["service_description"] = grab_request_var("service_description", grab_request_var("service", ""));
        $formvars["hostgroup_name"] = isset($request["hostgroup_name"]) ? $request["hostgroup_name"] : "";
        $formvars["servicegroup_name"] = isset($request["servicegroup_name"]) ? $request["servicegroup_name"] : "";

        $formvars["duration"] = "60";
    }

    $errors = array();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // handle form
        if ($form_mode == "host") {
            if (is_readonly_user(0)) {
                $errors[] = _("Read only users cannot add schedule recurring downtime");
            }
            if (empty($request["host_name"]))
                $errors[] = _("Please enter a host name.");
            else if (!is_authorized_for_host(0, $request["host_name"])) {
                $errors[] = _("The host you specified is not valid for your user account.");
            }
        } else if ($form_mode == "service") {
            if (is_readonly_user(0)) {
                $errors[] = _("Read only users cannot add schedule recurring downtime");
            }
            if (empty($request["host_name"]))
                $errors[] = _("Please enter a host name.");
            else if (!is_authorized_for_host(0, $request["host_name"]) && empty($request["service_description"])) {
                $errors[] = _("The host you specified is not valid for your user account.");
            }
            if (empty($request["service_description"]))
                $errors[] = _("Please enter a service name.");
            else {
                if ($request["service_description"] == '*') {
                    if (!is_authorized_for_host(0, $request["host_name"])) {
                        $errors[] = _("The service you specified is not valid for your user account.");
                    }
                } else {
                    if (strstr($request["service_description"], "*")) {
                        $search_str = "lk:" . str_replace("*", "%", $request["service_description"]);
                    } else {
                        $search_str = $request["service_description"];
                    }

                    if (!is_authorized_for_service(0, $request["host_name"], $search_str)) {
                        $errors[] = _("The service you specified is not valid for your user account.");
                    }
                }
            }
        } else if ($form_mode == "servicegroup") {
            if (empty($request["servicegroup_name"]))
                $errors[] = _("Please enter a servicegroup name.");
            else if (!is_authorized_for_servicegroup(0, $request["servicegroup_name"])) {
                $errors[] = _("The servicegroup you specified is not valid for your user account.");
            }
        } else {
            if (empty($request["hostgroup_name"]))
                $errors[] = _("Please enter a hostgroup name.");
            else if (!is_authorized_for_hostgroup(0, $request["hostgroup_name"])) {
                $errors[] = _("The hostgroup you specified is not valid for your user account.");
            }
        }
        $required = array(
            "time" => _("Please enter the start time for this downtime event."),
            "duration" => _("Please enter the duration of this downtime event. Duration should be more than 5 mins.")
        );
        foreach ($required as $field => $errval) {
            if (empty($request[$field])) {
                $errors[] = $errval;
            }
        }

        if (!empty($request["time"])) {
            $exp = '/^(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})$/';
            if (!preg_match($exp, $request["time"])) {
                $errors[] = _("Please enter a valid time in 24-hour format, e.g. 21:00.");
            }
        }

        if (!empty($request["duration"])) {
            if (!is_numeric($request["duration"])) {
                $errors[] = _("Please enter a valid duration time in minutes, e.g. 60. Duration should be more than 5 mins.");
            }
        }

        if (!empty($request["days_of_month"])) {
            $days = explode(",", $request["days_of_month"]);
            $dayerror = false;
            foreach ($days as $day) {
                if (!is_numeric($day) || $day < 0 || $day > 31)
                    $dayerror = true;
            }
            if ($dayerror == true)
                $errors[] = _("Invalid days of the month.");
        }
        
        if (!count($errors) > 0) {
            $str_days_of_week = "";
            $str_months_of_year = "";
            $str_days_of_month = $request["days_of_month"];
            $i = 0;

            if (!empty($request['days_of_week'])) {
                foreach ($request["days_of_week"] as $k => $day) {
                    if ($i++ > 0) {
                        $str_days_of_week .= ",";
                    }
                    $str_days_of_week .= $day;
                }
            }

            if (!empty($request['months_of_year'])) {
                foreach ($request["months_of_year"] as $k => $month) {
                    if ($i++ > 0) {
                        $str_months_of_year .= ",";
                    }
                    $str_months_of_year .= $month;
                }
            }

            $new_cfg = array(
                "user" => $_SESSION["username"],
                "comment" => grab_request_var('comment', ''),
                "time" => $request["time"],
                "duration" => $request["duration"],
                "days_of_week" => $str_days_of_week,
                "days_of_month" => $str_days_of_month,
                "months_of_year" => $str_months_of_year,
            );

            if (isset($request["svcalso"])) {
                $new_cfg["svcalso"] = 1;
            }

            if ($edit) {
                $sid = $request["sid"];
            } else {
                $sid = recurringdowntime_generate_sid();
            }

            if ($form_mode == "host") {
                $new_cfg["schedule_type"] = "host";
                $new_cfg["host_name"] = $request["host_name"];
                $new_cfg["childoptions"] = $request["childoptions"];
            } elseif ($form_mode == "service") {
                $new_cfg["schedule_type"] = "service";
                $new_cfg["service_description"] = $request["service_description"];
                $new_cfg["host_name"] = $request["host_name"];
            } elseif ($form_mode == "servicegroup") {
                $new_cfg["schedule_type"] = "servicegroup";
                $new_cfg["servicegroup_name"] = $request["servicegroup_name"];
            } elseif ($form_mode == "hostgroup") {
                $new_cfg["schedule_type"] = "hostgroup";
                $new_cfg["hostgroup_name"] = $request["hostgroup_name"];
            }

            $cfg = recurringdowntime_get_cfg();
            $cfg[$sid] = $new_cfg;
            recurringdowntime_write_cfg($cfg);
            if ($request["return"]) {
                $go = $request["return"];
            } else {
                $go = 'recurringdowntime.php';
            }
            header("Location: $go");
            exit;
        } else {
            $formvars = $request;
        }
    }

    $svcalso = grab_array_var($formvars, 'svcalso', '');
    $comment = grab_array_var($formvars, 'comment', '');
    $time = grab_array_var($formvars, 'time', '');
    $months_of_year = grab_array_var($formvars, 'months_of_year', array());
    $days_of_week = grab_array_var($formvars, 'days_of_week', array());
    $days_of_month = grab_array_var($formvars, 'days_of_month', '');
    $childoptions = grab_array_var($formvars, 'childoptions', 0);

    do_page_start(array("page_title" => _("Add Recurring Downtime Schedule")), true);
?>

<h1><?php if ($edit) { echo _("Edit Recurring Downtime Schedule"); } else { echo _("Add Recurring Downtime Schedule"); } ?></h1>
<p>
    <strong><?php echo _("Note"); ?>:</strong> <?php echo _("A new downtime schedule will be added to the monitoring engine one hour before it is set to activate, according to the parameters specified below."); ?>
</p>

<?php if (count($errors) > 0) { ?>
    <div id="message">
        <ul class="errorMessage" style="padding: 10px 0 10px 30px;">
            <?php foreach ($errors as $k => $msg) { ?>
                <li><?php echo $msg; ?></li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<form action="<?php echo encode_form_val($_SERVER["REQUEST_URI"]); ?>" method="post">
<input type="hidden" name="return" value="<?php echo encode_form_val($request["return"]); ?>">
<?php echo get_nagios_session_protector(); ?>

<h5 class="ul"><?php echo _("Schedule Settings"); ?></h5>

<table class="table table-no-border table-auto-width">
<tbody>
<?php if ($form_mode == "host") { ?>
    <tr>
        <td class="vt">
            <label for="hostBox"><?php echo _("Host"); ?>:</label>
        </td>
        <td>
            <?php if ($edit || (isset($_GET["host_name"]) || isset($_GET["host"]))) { ?>
            <input disabled="disabled" id="hostBox" type="text" name="host_name" value="<?php echo encode_form_val($formvars["host_name"]); ?>" size="25" class="form-control">
            <input type="hidden" name="host_name" value="<?php echo encode_form_val($formvars["host_name"]); ?>">
            <?php } else { ?>
            <input id="hostBox" type="text" name="host_name" value="<?php echo encode_form_val($formvars["host_name"]); ?>" size="25" class="form-control">
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#hostBox").each(function () {
                            $(this).autocomplete({source: suggest_url + '?type=host', minLength: 1});

                            $(this).blur(function () {
                                var hostname = $("#hostBox").val();
                            });
                            $(this).change(function () {
                                var hostname = $("#hostBox").val();
                            });

                        });
                    });
                </script>
            <?php } ?>
            <div class="subtext"><?php echo _("The host associated with this schedule."); ?></div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label for="child_hosts"><?php echo _("Child Hosts"); ?>:</label></td>
        <td>
            <select id="childoptions" name="childoptions" class="form-control req">
                <option value="0"><?php echo _('Do nothing with child hosts'); ?></option>
                <option value="2"><?php echo _('Schedule downtime for all child hosts'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="svcalso"><?php echo _("Services"); ?>:</label>
        </td>
        <td class="checkbox">
            <label>
                <input id="svcalso" type="checkbox" name="svcalso" value="1" <?php echo is_checked($svcalso, 1); ?>>
                <?php echo _("Include all services on this host"); ?>
            </label>
        </td>
    </tr>
<?php } elseif ($form_mode == "service") { ?>
    <tr>
        <td class="vt">
            <label for="hostBox"><?php echo _("Host"); ?>:</label>
        </td>
        <td>
            <?php if ($edit || (isset($_GET["host_name"]) || isset($_GET["host"]))) { ?>
            <input disabled="disabled" id="hostBox" class="form-control" type="text" name="host_name" value="<?php echo encode_form_val($formvars["host_name"]); ?>" size="25">
            <input type="hidden" name="host_name" value="<?php echo encode_form_val($formvars["host_name"]); ?>">
            <?php } else { ?>
            <input id="hostBox" class="form-control" type="text" name="host_name" value="<?php echo encode_form_val($formvars["host_name"]); ?>" size="25">
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#hostBox").each(function () {
                            $(this).autocomplete({source: suggest_url + '?type=host', minLength: 1});

                            $(this).blur(function () {
                                var hostname = $("#hostBox").val();
                            });
                            $(this).change(function () {
                                var hostname = $("#hostBox").val();
                            });

                        });
                    });
                </script>
            <?php } ?>
            <div class="subtext"><?php echo _("The host associated with this schedule."); ?></div>
        </td>
    </tr>
    <tr>
        <td class="vt">
            <label for="serviceBox"><?php echo _("Service"); ?>:</label>    
        </td>
        <td>
            <input id="serviceBox" class="form-control" type="text" name="service_description" value="<?php echo encode_form_val($formvars["service_description"]); ?>" size="25">
            <div class="subtext"><?php echo _("Optional service associated with this schedule."); ?> <?php if (is_admin()) { ?><a class="tt-bind" title="<?php echo _('Examples'); ?>: HTTP*, *HTTP, *HTTP*"><?php echo _("A * wildcard can be used before and/or after the service name to specify multiple matches"); ?>.</a><?php } ?></div>
            <script type="text/javascript">
                $(document).ready(function () {
                    $("#hostBox").blur(function () {
                        var hostname = $(this).val();
                        $('#serviceBox').autocomplete({
                                source: suggest_url + '?type=services&host=' + hostname,
                                minLength: 1
                        });
                    });
                });
            </script>
        </td>
    </tr>
<?php } elseif ($form_mode == "servicegroup") { ?>
    <tr>
        <td class="vt">
            <label for="servicegroupBox"><?php echo _("Servicegroup"); ?>:</label>
        </td>
        <td>
            <?php if ($edit || isset($_GET["servicegroup_name"])) { ?>
            <input disabled="disabled" id="servicegroupBox" class="form-control" type="text" name="servicegroup_name" value="<?php echo encode_form_val($formvars["servicegroup_name"]); ?>" size="25">
            <input type="hidden" name="servicegroup_name" value="<?php echo encode_form_val($formvars["servicegroup_name"]); ?>">
            <?php } else { ?>
            <input id="servicegroupBox" class="form-control" type="text" name="servicegroup_name" value="<?php echo encode_form_val($formvars["servicegroup_name"]); ?>" size="25">
            <div class="subtext"><?php echo _("The servicegroup associated with this schedule"); ?>.</div>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#servicegroupBox").each(function () {
                            $(this).autocomplete({source: suggest_url + '?type=servicegroups', minLength: 1});
                        });
                    });
                </script>
            <?php } ?>
        </td>
    </tr>
<?php } else { ?>
    <tr>
        <td class="vt">
            <label for="hostgroupBox"><?php echo _("Hostgroup"); ?>:</label>
        </td>
        <td>
            <?php if ($edit || isset($_GET["hostgroup_name"])) { ?>
            <input disabled="disabled" id="hostgroupBox" class="form-control" type="text" name="hostgroup_name" value="<?php echo encode_form_val($formvars["hostgroup_name"]); ?>" size="25">
            <input type="hidden" name="hostgroup_name" value="<?php echo encode_form_val($formvars["hostgroup_name"]); ?>">
            <?php } else { ?>
            <input id="hostgroupBox" class="form-control" type="text" name="hostgroup_name" value="<?php echo encode_form_val($formvars["hostgroup_name"]); ?>" size="25">
            <div class="subtext"><?php echo _("The hostgroup associated with this schedule"); ?>.</div>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $("#hostgroupBox").each(function () {
                            $(this).autocomplete({source: suggest_url + '?type=hostgroups', minLength: 1});
                        });
                    });
                </script>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td>
            <label for="svcalso"><?php echo _("Services"); ?>:</label>
        </td>
        <td class="checkbox">
            <label>
                <input id="svcalso" class="checkfield" type="checkbox" name="svcalso"value="1" <?php echo is_checked($svcalso, 1); ?>>
                <?php echo _("Include all services in this hostgroup"); ?>
            </label>
        </td>
    </tr>
<?php } ?>
<tr>
    <td class="vt">
        <label for="commentBox"><?php echo _("Comment"); ?>:</label>
    </td>
    <td>
        <input id="commentBox" class="form-control" type="text" name="comment" value="<?php echo encode_form_val($comment); ?>" size="60">
        <div class="subtext"><?php echo _("An optional comment associated with this schedule."); ?></div>
    </td>
</tr>
<tr>
    <td class="vt">
        <label for="timeBox"><?php echo _("Start Time"); ?>:</label>
    </td>
    <td>
        <input id="timeBox" class="form-control" type="text" name="time" value="<?php echo encode_form_val($time); ?>" size="6">
        <div class="subtext"><?php echo _("Time of day the downtime should start in 24-hr format"); ?> (e.g. 13:30).</div>
    </td>
</tr>
<tr>
    <td class="vt">
        <label for="durationBox"><?php echo _("Duration"); ?>:</label>
    </td>
    <td>
        <div class="input-group">
            <input id="durationBox" class="form-control" type="text" name="duration" value="<?php echo encode_form_val($formvars["duration"]); ?>" size="6">
            <div class="input-group-addon"><?php echo _('Minutes'); ?></div>
        </div>
        <div class="subtext"><?php echo _("Duration of the scheduled downtime in minutes. Duration should be more than 5 mins or downtime will not be scheduled."); ?></div>
    </td>
</tr>
<tr>
    <td class="vt">
        <label for="weekdaysBox"><?php echo _("Valid Months"); ?>:</label>
    </td>
    <td>
        <table class="table table-condensed table-bordered table-auto-width">
            <tr>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[0])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[0]" value="jan"> <?php echo _("Jan"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[1])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[1]" value="feb"> <?php echo _("Feb"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[2])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[2]" value="mar"> <?php echo _("Mar"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[3])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[3]" value="apr"> <?php echo _("Apr"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[4])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[4]" value="may"> <?php echo _("May"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[5])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[5]" value="jun"> <?php echo _("Jun"); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[6])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[6]" value="jul"> <?php echo _("Jul"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[7])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[7]" value="aug"> <?php echo _("Aug"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[8])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[8]" value="sep"> <?php echo _("Sep"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[9])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[9]" value="oct"> <?php echo _("Oct"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[10])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[10]" value="nov"> <?php echo _("Nov"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($months_of_year[11])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="months_of_year[11]" value="dec"> <?php echo _("Dec"); ?>
                    </label>
                </td>
            </tr>
        </table>
        <div class="subtext"><?php echo _("Months this schedule is valid. Defaults to every month if none selected"); ?>.</div>
    </td>
</tr>
<tr>
    <td class="vt">
        <label for="weekdaysBox"><?php echo _("Valid Weekdays"); ?>:</label>
    </td>
    <td>
        <table class="table table-condensed table-bordered table-auto-width">
            <tr>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($days_of_week[0])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="days_of_week[0]" value="mon"> <?php echo _("Mon"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($days_of_week[1])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="days_of_week[1]" value="tue"> <?php echo _("Tue"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($days_of_week[2])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="days_of_week[2]" value="wed"> <?php echo _("Wed"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($days_of_week[3])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="days_of_week[3]" value="thu"> <?php echo _("Thu"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($days_of_week[4])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="days_of_week[4]" value="fri"> <?php echo _("Fri"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($days_of_week[5])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="days_of_week[5]" value="sat"> <?php echo _("Sat"); ?>
                    </label>
                </td>
                <td class="checkbox" style="width: 64px;">
                    <label>
                        <input <?php if (!empty($days_of_week[6])) { echo "checked"; } ?> type="checkbox" class="checkfield" name="days_of_week[6]" value="sun"> <?php echo _("Sun"); ?>
                    </label>
                </td>
            </tr>
        </table>
        <div class="subtext"><?php echo _("Days of the week this schedule is valid. Defaults to every weekday if none selected"); ?>.</div>
    </td>
</tr>
<tr>
    <td class="vt">
        <label for="monthdaysBox"><?php echo _("Valid Days of Month"); ?>:</label>
    </td>
    <td>
        <input id="monthdaysBox" class="form-control" type="text" name="days_of_month" value="<?php echo encode_form_val($days_of_month); ?>" size="15">
        <div class="subtext"><?php echo _("Comma-separated list of days of month this schedule is valid. Defaults to every day if empty. If you specify weekdays <em>and</em> days of the month, then <em>both</em> must match for the downtime to be scheduled."); ?></div>
    </td>
</tr>
</table>

<button type="submit" name="submit" class="btn btn-sm btn-primary"><?php echo _("Submit Schedule"); ?></button>
<button type="button" name="cancel" class="btn btn-sm btn-default" onClick="javascript:document.location.href='<?php echo $request["return"]; ?>'"><?php echo _("Cancel"); ?></button>

<?php
    do_page_end(true);
}
//
// end function recurringdowntime_add_downtime()
//

//
// begin function show_downtime()
//
function recurringdowntime_show_downtime()
{
    global $request;

    do_page_start(array("page_title" => _("Recurring Downtime")), true);

    if (isset($request["host"])) {
        $host_tbl_header = _("Recurring Downtime for Host: ") . $request["host"];
        $service_tbl_header = _("Recurring Downtime for Host: ") . $request["host"];
        if (is_authorized_for_host(0, $request["host"])) {
            $host_data = recurringdowntime_get_host_cfg($request["host"]);
            $service_data = recurringdowntime_get_service_cfg($request["host"]);
        }
    } elseif (isset($request["hostgroup"])) {
        $hostgroup_tbl_header = _("Recurring Downtime for Hostgroup: ") . $request["hostgroup"];
        if (is_authorized_for_hostgroup(0, $request["hostgroup"])) {
            $hostgroup_data = recurringdowntime_get_hostgroup_cfg($request["hostgroup"]);
        }
    } elseif (isset($request["servicegroup"])) {
        $servicegroup_tbl_header = _("Recurring Downtime for Servicegroup: ") . $request["servicegroup"];
        if (is_authorized_for_servicegroup(0, $request["servicegroup"])) {
            $servicegroup_data = recurringdowntime_get_servicegroup_cfg($request["servicegroup"]);
        }
    }

    if (!isset($request["host"]) && !isset($request["hostgroup"]) && !isset($request["servicegroup"])) {
        /*
        $host_tbl_header = "Recurring Downtime for All Hosts";
        $hostgroup_tbl_header = "Recurring Downtime for All Hostgroups";
        $servicegroup_tbl_header = "Recurring Downtime for All Servicegroups";
        */
        $host_tbl_header = _("Host Schedules");
        $service_tbl_header = _("Service Schedules");
        $hostgroup_tbl_header = _("Hostgroup Schedules");
        $servicegroup_tbl_header = _("Servicegroup Schedules");
        $host_data = recurringdowntime_get_host_cfg($host = false);
        $service_data = recurringdowntime_get_service_cfg($host = false);
        $hostgroup_data = recurringdowntime_get_hostgroup_cfg($hostgroup = false);
        $servicegroup_data = recurringdowntime_get_servicegroup_cfg($servicegroup = false);
        $showall = true;
    }

    ?>
    <h1><?php echo _("Recurring Downtime"); ?></h1>

    <?php echo _("Scheduled downtime definitions that are designed to repeat (recur) at set intervals are shown below.  The next schedule for each host/service are added to the monitoring engine when the cron runs at the top of the hour."); ?>

    <?php
    if (!isset($request["host"]) && !isset($request["hostgroup"]) && !isset($request["servicegroup"])) {
        ?>
        <p>
    <?php } ?>

    <script type="text/javascript">
        function do_delete_sid(sid) {
            input = confirm('<?php echo _("Are you sure you wish to delete this downtime schedule?"); ?>');
            if (input == true) {
                window.location.href = 'recurringdowntime.php?mode=delete&sid=' + sid + '&nsp=<?php echo get_nagios_session_protector_id();?>';
            }
        }
    </script>

    <?php
    if ($showall) {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#tabs").tabs().show();
            });
        </script>

        <div id="tabs" class="hide">
            <ul>
                <li><a href="#host-tab"><?php echo _("Hosts"); ?></a></li>
                <li><a href="#service-tab"><?php echo _("Services"); ?></a></li>
                <li><a href="#hostgroup-tab"><?php echo _("Hostgroups"); ?></a></li>
                <li><a href="#servicegroup-tab"><?php echo _("Servicegroups"); ?></a></li>
            </ul>
    <?php
    }
    ?>

    <?php if (!empty($_GET["host"]) || $showall) {

    $host = grab_request_var('host', '');

    // hosts tab
    if ($showall) {
        echo "<div id='host-tab'>";
    }
    ?>

    <h4 <?php if ($host) { echo 'style="margin-top: 20px;"'; } ?>><?php echo $host_tbl_header; ?></h4>

    <?php
    if (!is_readonly_user(0)) {
        if ($host) {
            ?>
            <div style="margin: 0 0 10px 0;">
                <a href="recurringdowntime.php?mode=add&type=host&host_name=<?php echo $host; ?>&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23host-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add schedule for this host"); ?></a>
            </div>
        <?php
        } else {
            ?>
            <div style="margin: 0 0 10px 0;">
                <a href="recurringdowntime.php?mode=add&type=host&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23host-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add Schedule"); ?></a>
            </div>
        <?php
        }
    }
    ?>

    <table class="tablesorter table table-condensed table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th><?php echo _("Host"); ?></th>
                <th><?php echo _("Services"); ?></th>
                <th><?php echo _("Comment"); ?></th>
                <th><?php echo _("Start Time"); ?></th>
                <th><?php echo _("Duration"); ?></th>
                <th><?php echo _("Months"); ?></th>
                <th><?php echo _("Weekdays"); ?></th>
                <th><?php echo _("Days in Month"); ?></th>
                <th><?php echo _("Handle Child Hosts"); ?></th>
                <th class="center" style="width: 80px;"><?php echo _("Actions"); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($host_data) {
            $i = 0;

            $host_names = array();
            foreach ($host_data as $k => $data) {
                $host_names[$k] = $data['host_name'];
            }
            array_multisort($host_names, SORT_ASC, $host_data);

            foreach ($host_data as $sid => $schedule) {
                if (empty($schedule['childoptions'])) {
                    $schedule['childoptions'] = 0;
                }
                ?>
                <tr>
                    <td><?php echo $schedule["host_name"]; ?></td>
                    <td><?php echo (isset($schedule["svcalso"]) && $schedule["svcalso"] == 1) ? _("Yes") : _("No"); ?></td>
                    <td><?php echo encode_form_val($schedule["comment"]); ?></td>
                    <td><?php echo $schedule["time"]; ?></td>
                    <td><?php echo $schedule["duration"]; ?></td>
                    <td><?php if ($schedule["months_of_year"]) {
                            echo $schedule["months_of_year"];
                        } else {
                            echo _("All");
                        } ?></td>
                    <td><?php if ($schedule["days_of_week"]) {
                            echo $schedule["days_of_week"];
                        } else {
                            echo _("All");
                        } ?></td>
                    <td><?php if ($schedule["days_of_month"]) {
                            echo $schedule["days_of_month"];
                        } else {
                            echo _("All");
                        } ?></td>
                    <td><?php if ($schedule["childoptions"] == 0) {
                            echo _("No");
                        } elseif ($schedule["childoptions"] == 1) {
                            echo _("Yes, triggered");
                        } elseif ($schedule["childoptions"] == 2) {
                            echo _("Yes, non-triggered");
                        } ?></td>
                    <td class="center">
                        <?php if (!is_readonly_user(0)) { ?>
                            <a href="<?php echo 'recurringdowntime.php' . "?mode=edit&sid=" . $sid . "&return=" . urlencode($_SERVER["REQUEST_URI"]); ?>%23host-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>"><img src="<?php echo theme_image('pencil.png'); ?>" class="tt-bind" title="<?php echo _('Edit schedule'); ?>" alt="<?php echo _('Edit'); ?>"></a>&nbsp;
                            <a onClick="javascript:return do_delete_sid('<?php echo $sid; ?>');" href="javascript:void(0);"><img src="<?php echo theme_image('cross.png'); ?>" class="tt-bind" title="<?php echo _('Delete schedule'); ?>" alt="<?php echo _('Delete'); ?>"></a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } // end foreach ?>

        <?php } else { ?>
            <tr>
                <td colspan="10">
                    <div style="padding:4px">
                        <em><?php echo _("There are currently no host recurring downtime events defined."); ?></em>
                    </div>
                </td>
            </tr>
        <?php } // end if host_data ?>
    </table>

    <?php if ($showall) echo "</div>"; // end host tab?>

<?php } // end if host or showall

if (!empty($_GET["service"]) || $showall) {

    $host = grab_request_var('host', '');
    $service = grab_request_var('service', '');

    if ($showall) {
        echo "<div id='service-tab'>";
    }
    ?>

    <h4><?php echo $service_tbl_header; ?></h4>

    <?php
    if (!is_readonly_user(0)) {
        if ($host) {
            ?>
            <div style="clear: left; margin: 0 0 10px 0;">
                <a href="recurringdowntime.php?mode=add&type=service&host_name=<?php echo $host; ?>&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23service-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add schedule for this host"); ?></a>
            </div>
        <?php
        } else {
            ?>
            <div style="clear: left; margin: 0 0 10px 0;">
                <a href="recurringdowntime.php?mode=add&type=service&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23service-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add Schedule"); ?></a>
            </div>
        <?php
        }
    } // end is_readonly_user
    ?>
    <table class="tablesorter table table-condensed table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th><?php echo _("Host"); ?></th>
                <th><?php echo _("Service"); ?></th>
                <th><?php echo _("Comment"); ?></th>
                <th><?php echo _("Start Time"); ?></th>
                <th><?php echo _("Duration"); ?></th>
                <th><?php echo _("Weekdays"); ?></th>
                <th><?php echo _("Days in Month"); ?></th>
                <th><?php echo _("Actions"); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($service_data) {
            $i = 0;

            $host_names = array();
            $service_names = array();
            foreach ($service_data as $k => $data) {
                $host_names[$k] = $data['host_name'];
                $service_names[$k] = $data['service_description'];
            }

            array_multisort($host_names, SORT_ASC, $service_names, SORT_ASC, $service_data);

            foreach ($service_data as $sid => $schedule) {
                ?>
                <tr>
                    <td><?php echo $schedule["host_name"]; ?></td>
                    <td><?php echo $schedule["service_description"]; ?></td>
                    <td><?php echo encode_form_val($schedule["comment"]); ?></td>
                    <td><?php echo $schedule["time"]; ?></td>
                    <td><?php echo $schedule["duration"]; ?></td>
                    <td><?php if ($schedule["days_of_week"]) {
                            echo $schedule["days_of_week"];
                        } else {
                            echo "All";
                        } ?></td>
                    <td><?php if ($schedule["days_of_month"]) {
                            echo $schedule["days_of_month"];
                        } else {
                            echo "All";
                        } ?></td>
                    <td style="text-align:center;width:60px">
                    <?php if (!is_readonly_user(0)) { ?>
                        <a href="<?php echo 'recurringdowntime.php'. "?mode=edit&sid=" . $sid . "&return=" . urlencode($_SERVER["REQUEST_URI"]); ?>%23service-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>"><img src="<?php echo theme_image('pencil.png'); ?>" class="tt-bind" title="<?php echo _('Edit schedule'); ?>" alt="<?php echo _('Edit'); ?>"></a>
                        <a onClick="javascript:return do_delete_sid('<?php echo $sid; ?>');" href="javascript:void(0);" title="<?php echo _('Delete Schedule'); ?>"><img src="<?php echo theme_image('cross.png'); ?>" class="tt-bind" title="<?php echo _('Delete schedule'); ?>" alt="<?php echo _('Delete'); ?>"></a>
                    <?php }?>
                    </td>
                </tr>
            <?php } // end foreach ?>

        <?php } else { ?>
            <tr>
                <td colspan="9">
                    <div style="padding:4px">
                        <em><?php echo _("There are currently no host/service recurring downtime events defined."); ?></em>
                    </div>
                </td>
            </tr>
        <?php } // end if service_data ?>
    </table>

    <?php if ($showall) echo "</div>"; // end service tab?>

<?php } // end if service or showall

if (!empty($_GET["hostgroup"]) || $showall) {

    $hostgroup = grab_request_var('hostgroup', '');

    if ($showall) {
        echo "<div id='hostgroup-tab'>";
    }
    ?>

    <h4><?php echo $hostgroup_tbl_header; ?></h4>

    <?php
    if (!is_readonly_user(0)) {
        if ($hostgroup) {
            ?>
            <div style="clear: left; margin: 0 0 10px 0;">
                <a href="recurringdowntime.php?mode=add&type=hostgroup&hostgroup_name=<?php echo $hostgroup; ?>&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23hostgroup-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add schedule for this hostgroup"); ?></a>
            </div>
        <?php
        } else {
            ?>
            <div style="clear: left; margin: 0 0 10px 0;">
                <a href="recurringdowntime.php?mode=add&type=hostgroup&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23hostgroup-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add Schedule"); ?></a>
            </div>
        <?php
        }
    } // end is_readonly_user
    ?>
    <table class="tablesorter table table-condensed table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th><?php echo _("Hostgroup"); ?></th>
            <th><?php echo _("Services"); ?></th>
            <th><?php echo _("Comment"); ?></th>
            <th><?php echo _("Start Time"); ?></th>
            <th><?php echo _("Duration"); ?></th>
            <th><?php echo _("Weekdays"); ?></th>
            <th><?php echo _("Days in Month"); ?></th>
            <th><?php echo _("Actions"); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php

        if ($hostgroup_data) {
            $i = 0;

            $hostgroup_names = array();
            foreach ($hostgroup_data as $k => $data) {
                $hostgroup_names[$k] = $data['hostgroup_name'];
            }

            array_multisort($hostgroup_names, SORT_ASC, $hostgroup_data);

            foreach ($hostgroup_data as $sid => $schedule) {
                ?>
                <tr>
                    <td><?php echo $schedule["hostgroup_name"]; ?></td>
                    <td><?php echo (isset($schedule["svcalso"]) && $schedule["svcalso"] == 1) ? "Yes" : "No"; ?></td>
                    <td><?php echo encode_form_val($schedule["comment"]); ?></td>
                    <td><?php echo $schedule["time"]; ?></td>
                    <td><?php echo $schedule["duration"]; ?></td>
                    <td><?php if ($schedule["days_of_week"]) {
                            echo $schedule["days_of_week"];
                        } else {
                            echo "All";
                        } ?></td>
                    <td><?php if ($schedule["days_of_month"]) {
                            echo $schedule["days_of_month"];
                        } else {
                            echo "All";
                        } ?></td>
                    <td style="text-align:center;width:60px">
                        <?php if (!is_readonly_user(0)) { ?>
                        <a href="<?php echo 'recurringdowntime.php' . "?mode=edit&sid=" . $sid . "&return=" . urlencode($_SERVER["REQUEST_URI"]); ?>%23hostgroup-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" title="Edit Schedule"><img src="<?php echo theme_image('pencil.png'); ?>" class="tt-bind" title="<?php echo _('Edit schedule'); ?>" alt="<?php echo _('Edit'); ?>"></a>
                        <a onClick="javascript:return do_delete_sid('<?php echo $sid; ?>');" href="javascript:void(0);" title="<?php echo _('Delete Schedule'); ?>"><img src="<?php echo theme_image('cross.png'); ?>" class="tt-bind" title="<?php echo _('Delete schedule'); ?>" alt="<?php echo _('Delete'); ?>"></a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } // end foreach ?>
        <?php } else { ?>
            <tr>
                <td colspan="8">
                    <div style="padding:4px">
                        <em><?php echo _("There are currently no hostgroup recurring downtime events defined."); ?></em>
                    </div>
                </td>
            </tr>
        <?php } // end if hostrgroup_data ?>
        </tbody>
    </table>

    <?php if ($showall) echo "</div>"; // end hostgroup tab?>

<?php } // end if hostgroup or showall

if (!empty($_GET["servicegroup"]) || $showall) {

    $servicegroup = grab_request_var('servicegroup', '');

    if ($showall) {
        echo "<div id='servicegroup-tab'>";
    }
        ?>
        <h4><?php echo $servicegroup_tbl_header; ?></h4>
        <?php
        if (!is_readonly_user(0)) {
            if ($servicegroup) {
                ?>
                <div style="clear: left; margin: 0 0 10px 0;">
                    <a href="recurringdowntime.php?mode=add&type=servicegroup&servicegroup_name=<?php echo $servicegroup; ?>&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23servicegroup-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add schedule for this servicegroup"); ?></a>
                </div>
            <?php } else { ?>
                <div style="clear: left; margin: 0 0 10px 0;">
                    <a href="recurringdowntime.php?mode=add&type=servicegroup&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23servicegroup-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus l"></i> <?php echo _("Add Schedule"); ?>
                    </a>
                </div>
            <?php
            }
        } // end is_readonly_user
        ?>
        <table class="tablesorter table table-condensed table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <th><?php echo _("Servicegroup"); ?></th>
                <th><?php echo _("Comment"); ?></th>
                <th><?php echo _("Start Time"); ?></th>
                <th><?php echo _("Duration"); ?></th>
                <th><?php echo _("Weekdays"); ?></th>
                <th><?php echo _("Days in Month"); ?></th>
                <th><?php echo _("Actions"); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php

            if ($servicegroup_data) {
                $i = 0;

                $servicegroup_names = array();
                foreach ($servicegroup_data as $k => $data) {
                    $servicegroup_names[$k] = $data['servicegroup_name'];
                }

                array_multisort($servicegroup_names, SORT_ASC, $servicegroup_data);

                foreach ($servicegroup_data as $sid => $schedule) {
                    ?>
                    <tr>
                        <td><?php echo $schedule["servicegroup_name"]; ?></td>
                        <td><?php echo $schedule["comment"]; ?></td>
                        <td><?php echo $schedule["time"]; ?></td>
                        <td><?php echo $schedule["duration"]; ?></td>
                        <td><?php if ($schedule["days_of_week"]) {
                                echo $schedule["days_of_week"];
                            } else {
                                echo "All";
                            } ?></td>
                        <td><?php if ($schedule["days_of_month"]) {
                                echo $schedule["days_of_month"];
                            } else {
                                echo "All";
                            } ?></td>
                        <td style="text-align:center;width:60px">
                            <?php if (!is_readonly_user(0)) { ?>
                            <a href="recurringdowntime.php?mode=edit&sid=<?php echo $sid; ?>&return=<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>%23servicegroup-tab&nsp=<?php echo get_nagios_session_protector_id(); ?>" title="Edit Schedule"><img src="<?php echo theme_image('pencil.png'); ?>" class="tt-bind" title="<?php echo _('Edit schedule'); ?>" alt="<?php echo _('Edit'); ?>"></a>
                            <a onClick="javascript:return do_delete_sid('<?php echo $sid; ?>');" href="javascript:void(0);" title="<?php echo _('Delete Schedule'); ?>"><img src="<?php echo theme_image('cross.png'); ?>" class="tt-bind" title="<?php echo _('Delete schedule'); ?>" alt="<?php echo _('Delete'); ?>"></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } // end foreach ?>

            <?php } else { ?>
                <tr>
                    <td colspan="7">
                        <div style="padding:4px">
                            <em><?php echo _("There are currently no servicegroup recurring downtime events defined."); ?></em>
                        </div>
                    </td>
                </tr>
            <?php } // end if servicegroup_data ?>


            </tbody>
        </table>

        <?php if ($showall) echo "</div>"; // end servicegroup tab?>

    <?php } // end if servicegroup or showall ?>

    <?php
    if ($showall) { // end of tabs container
        ?>
        </div>
    <?php
    }
    do_page_end(true);
}