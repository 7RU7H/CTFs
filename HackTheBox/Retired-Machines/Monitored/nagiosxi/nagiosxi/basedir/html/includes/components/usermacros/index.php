<?php
//
// User Macros Component
// Copyright (c) 2016-2019 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/../../common.inc.php');
require_once(dirname(__FILE__) . '/../../utils-ccm.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);


// Only admins and people with CCM access can access this component
if (!ccm_has_access_for('usermacros')) {
    echo _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    exit();
}


route_request();


function route_request()
{
    global $request;
    $mode = grab_request_var("mode");

    switch ($mode) {

        case "restart":
            $x = submit_command(COMMAND_NAGIOSCORE_RESTART);
            if ($x) {
                flash_message(_('Restart command has been sent to Nagios Core.'), FLASH_MSG_SUCCESS);
            } else {
                flash_message(_('An error occured while sending restart command.'), FLASH_MSG_ERROR);
            }
            header('Location: index.php');
            break;

        case "update":
            $macro = grab_request_var("macro");
            $new_value = grab_request_var("new_value");
            $output = run_update($macro, $new_value);
            echo $output;
            break;

        case "overwrite":
            $content = grab_request_var("content");
            send_to_audit_log(_('Updated user macros file in user macros component'), AUDITLOGTYPE_CHANGE);
            $output = full_overwrite_from_index($content);
            echo $output;
            break;

        case "query":
            $raw = grab_request_var("raw", 1);
            $reset_query_time = grab_request_var("reset_query_time", 0);
            $output = get_resource_data($raw, $reset_query_time);
            echo $output;
            break;

        case "control":
            run_control_modal();
            break;

        case "modal_content":
            $content = control_modal_content();
            return $content;
            break;

        case "get_system_macros_file":
            $content = get_system_macros_file();
            echo $content;
            break;

        case "system_macros_detected":
            $content = get_system_macros_detected();
            print json_encode($content);
            break;

        case "write_system_macros_detected":
            $content = grab_request_var("content");
            send_to_audit_log(_('Updated system macros in user macros component'), AUDITLOGTYPE_CHANGE);
            $output = set_system_macros_detected($content);
            echo $output;
            break;

        default:
            display_usermacros();
            break;

    }
}


function display_usermacros()
{
    $tab = grab_request_var("tab", 1);
    $usermacro_disable = get_option("usermacro_disable", 0);
    $usermacro_redacted = get_option("usermacro_redacted", 1);
    $usermacro_user_redacted = get_option("usermacro_user_redacted", 0);

    if ($usermacro_disable) {
        $disabled = "<i class='fa fa-square' style='font-size: 14px; color: #999; margin-right: 5px;'></i>";
    } else {
        $disabled = "<i class='fa fa-check-square' style='font-size: 14px; color: #4D89F9; margin-right: 5px;'></i>";
    }

    if (!$usermacro_redacted) {
        $redacted = "<i class='fa fa-square' style='font-size: 14px; color: #999; margin-right: 5px;'></i>";
    } else {
        $redacted = "<i class='fa fa-check-square' style='font-size: 14px; color: #4D89F9; margin-right: 5px;'></i>";
    }

    // Makes sure user has appropriate license level
    licensed_feature_check();

    // Start the HTML page
    do_page_start(array("page_title" => "User Macros"), true);
?>

<script type="text/javascript">
    var disabled = <?php echo intval($usermacro_disable); ?>;
    var usermacro_redacted = <?php echo intval($usermacro_redacted); ?>;
    var base_url = "<?php echo get_base_url(); ?>";

    var macro_data = "";
    var chosen_detected_list = [];

    $(document).ready(function() {
        $('#tabs').tabs().show();

        // Get data
        index_get_resource_data();
        get_system_macros_file();

        // Detect System Macro selection (dblclick + ?)
        $("#system-macro-body").on("dblclick", function() {
            var add_html = "";
            var value = "";
            value = $("#system-macro-body option:selected").val();

            $("#system-macro-body option:contains('" + value + "')").attr("disabled", "disabled");
            $("#system-macro-body option:contains('" + value + "')").css("backgroundColor", "#eee");

            var chosen = $("#system-macro-body option:contains('" + value + "')").length;
            var chosen_check = chosen_detected_check(value);

            if (chosen > 0 && chosen_check) {
                add_html = '<option>' + value + '</option>';

                $("#system-detect-content").append(add_html);
                chosen_detected_list.push(value);
            }
        });

        // Detect System Macro selection (dblclick + ?)
        $("#system-detect-content").on("dblclick", function() {
            var add_html = "";
            var value = "";
            value = $("#system-detect-content option:selected").val();

            $("#system-detect-content option:selected").remove();
            $("#system-macro-body option:contains('" + value + "')").removeAttr("disabled");
            $("#system-macro-body option:contains('" + value + "')").css("backgroundColor", "#fff");

            // Remove from chosen_detected_list
            for (var i = chosen_detected_list.length - 1; i >= 0 ; i--) {
                if (chosen_detected_list[i] === value) {
                    chosen_detected_list.splice(i, 1);
                }
            }
        });

        // Detect Filter
        $('#filterMacros').on('input', function() {
            var input = this.value.toLowerCase();

            $('#system-macro-body > optgroup > option').addClass("hidden").filter(function() {
                return this.value.toLowerCase().indexOf( input ) > -1;
            }).removeClass("hidden");

            if ($(this).val().length > 0) {
                $(this).parent().find('.clear-filter').show();
            } else {
                $(this).parent().find('.clear-filter').hide();
            }
        });

        $(".clear-filter").on("click", function() {
            $("#filterMacros").val('');
            $('.clear-filter').hide();
            $('#system-macro-body > optgroup > option').removeClass("hidden");
        });

        // Add multiple button
        $("#add-multiple-detected").click( function() {
            var element = "";
            var element = $("#system-macro-body option:selected");
            var selected = element.length;
            var value = element.val();
            var chosen_check = chosen_detected_check(value);

            if (selected == 1) {
                $("#system-macro-body option:contains('" + value + "')").attr("disabled", "disabled");
                $("#system-macro-body option:contains('" + value + "')").css("backgroundColor", "#eee");

                var chosen = $("#system-macro-body option:contains('" + value + "')").length;

                if (chosen > 0 && chosen_check) {
                    add_html = '<option>' + value + '</option>';

                    $("#system-detect-content").append(add_html);
                    chosen_detected_list.push(value);
                }
            } else if (selected > 1) {
                $.each(element, function() {
                    value = $(this).val();

                    $("#system-macro-body option:contains('" + value + "')").attr("disabled", "disabled");
                    $("#system-macro-body option:contains('" + value + "')").css("backgroundColor", "#eee");

                    var chosen = $("#system-macro-body option:contains('" + value + "')").length;

                    if (chosen > 0 && chosen_check) {
                        add_html = '<option>' + value + '</option>';

                        $("#system-detect-content").append(add_html);
                        chosen_detected_list.push(value);
                    }
                });
            }
        });

        // Remove multiple button
        $("#remove-multiple-detected").click( function() {
            var element = "";
            var element = $("#system-detect-content option:selected");
            var selected = element.length;
            var value = element.val();

            if (selected == 1) {
                $("#system-detect-content option:selected").remove();
                $("#system-macro-body option:contains('" + value + "')").removeAttr("disabled");
                $("#system-macro-body option:contains('" + value + "')").css("backgroundColor", "#fff");

                // Remove from chosen_detected_list
                for (var i = chosen_detected_list.length - 1; i >= 0 ; i--) {
                    if (chosen_detected_list[i] === value) {
                        chosen_detected_list.splice(i, 1);
                    }
                }
            } else if (selected > 1) {
                $.each(element, function() {
                    value = $(this).val();

                    $("#system-detect-content option:selected").remove();
                    $("#system-macro-body option:contains('" + value + "')").removeAttr("disabled");
                    $("#system-macro-body option:contains('" + value + "')").css("backgroundColor", "#fff");

                    // Remove from chosen_detected_list
                    for (var i = chosen_detected_list.length - 1; i >= 0 ; i--) {
                        if (chosen_detected_list[i] === value) {
                            chosen_detected_list.splice(i, 1);
                        }
                    }
                });
            }
        });
    });

    // Detect Macro Submit Manually
    $("div.childpage").on("click", "#macro-update-submit", function() {
        var content = $("#macro_file").val();
        var url = base_url + "includes/components/usermacros/?mode=overwrite";
        url = url + "&content=" + encodeURIComponent(content);

        if (!disabled && !usermacro_redacted) {
            $("#macro-throbber").css("display", "block");
            var remove_id = $("#tiggered-button-id").val();

            // First get modal content and append to page
            $.post(url, function(data) {
                data = $.parseJSON(data);

                if (data.return_code == 0) { // Success
                    $("#macro-update-message").css("background-color", "#5CB85C");
                    $("#macro-update-message").html(data.response);

                    index_get_resource_data();

                    $("#macro-throbber").css("display", "none");
                } else {
                    // Failure
                    $("#macro-update-message").css("background-color", "#CC7070");
                    $("#macro-update-message").html(data.response);

                    $("#macro-throbber").css("display", "none");
                }
            });
        }
    });

    // Detect System Macro Submit
    $("div.childpage").on("click", "#system-update-submit", function() {
        var content = "";
        var url = base_url + "includes/components/usermacros/?mode=write_system_macros_detected";

        $("#system-detect-content option").each(function() {
            content += $(this).val() + "\n";
        });

        url = url + "&content=" + encodeURIComponent(content);

        if (!disabled) {
            $("#macro-throbber-system").css("display", "block");

            // First get modal content and append to page
            $.post(url, function(data) {
                data = $.parseJSON(data);

                if (data.return_code == 0) { // Success
                    $("#system-update-message").css("background-color", "#5CB85C");
                    $("#system-update-message").html(data.response);

                    $("#macro-throbber-system").css("display", "none");
                    // Reload the tab to update view
                    $('#my_tabs').tabs('load', 2);
                } else { // Failure
                    $("#system-update-message").css("background-color", "#CC7070");
                    $("#system-update-message").html(data.response);

                    $("#macro-throbber-system").css("display", "none");
                }
            });
        }
    });

    function index_get_resource_data() {
        $("#resource_table_status").css("display", "none");
        $("#table-macro-throbber").css("display", "block");

        var url = base_url + "includes/components/usermacros/?mode=query&raw=1";
        var html = "";

        if (!disabled) {
            // Get resource file contents and write to textarea
            $.get(url, function (data) {
                data = $.parseJSON(data);
                macro_data = data;

                if (macro_data !== "") { // Success
                    $.each(macro_data, function(key, value) {
                        html += value + "\n";
                    });

                    $("#macro_file").html(html);

                    $("#table-macro-throbber").css("display", "none");
                    $("#resource_table_status").html("<img src='<?php echo theme_image('ok_small.png') ?>' class='tt-bind' data-original-title='File Verified'>");
                    $("#resource_table_status").css("display", "block");
                } else { // Failure
                    $("#resource_table_status").html("<img src='<?php echo theme_image('error_small.png') ?>' class='tt-bind' data-original-title='File not found. Check permissions!'>");
                    $("#resource_table_status").css("display", "block");
                    $("#table-macro-throbber").css("display", "none");
                }
            });
        } else {
            var html = "The component has been disabled.  Go to the Manage Components page to turn it on.";

            $("#table-macro-throbber").css("display", "none");
            $("#resource_table_status").html("<img src='<?php echo theme_image('error_small.png') ?>' class='tt-bind' data-original-title='The component has been disabled'>");
            $("#resource_table_status").css("display", "block");
            $("#macro_file").html(html);
        }
    }

    // Display current selected active system macros
    function get_system_macros_detected() {
        $("#macro-throbber-system").css("display", "block");

        var url = base_url + "includes/components/usermacros/?mode=system_macros_detected";
        var add_html = "";
        var smacro_data = "";

        // Get resource file contents and write to textarea
        $.get(url, function (data) {
            if (data !== "") {
                smacro_data = data;

                $.each(smacro_data, function(key, value) {

                    if (value !== "") {
                        $("#system-macro-body option:contains('" + value + "')").attr("disabled", "disabled");
                        $("#system-macro-body option:contains('" + value + "')").css("backgroundColor", "#eee");

                        var chosen = $("#system-macro-body option:contains('" + value + "')").length;

                        if (chosen > 0) {
                            add_html = '<option>' + value + '</option>';

                            $("#system-detect-content").append(add_html);
                        }
                    }
                });

                $("#macro-throbber-system").css("display", "none");
            } else {
                $("#macro-throbber-system").css("display", "none");
            }
        }, 'json');
    }

    // Display available System Macros in select field
    function get_system_macros_file() {
        var html = "";

        if (!disabled) {
            $("#resource_table_status").css("display", "none");
            $("#table-macro-throbber").css("display", "block");

            var url = base_url + "includes/components/usermacros/?mode=get_system_macros_file";

            // Get resource file contents and write to textarea
            $.get(url, function (data) {
                data = $.parseJSON(data);
                smacro_data = data;

                if (smacro_data !== "") { // Success
                    $.each(smacro_data, function(key, value) {

                        html += "<optgroup label=" + key + ">";
                        $.each(value, function(k, v) {
                            html += "<option value=" + v + " style=''>" + v + "</option>";
                        });
                        html += "</optgroup>";
                    });

                    $("#system-macro-body").html(html);
                    get_system_macros_detected();
                } else { // Failure
                    html = "<option>Error retrieving system macro text file.  Contact Support.</option>";
                    $("#system-macro-body").html(html);
                }
            });
        } else {
            $("#resource_table_status").css("display", "block");
            $("#resource_table_status").html("<img src='<?php echo theme_image('error_small.png') ?>' rel='tooltip' title='The component has been disabled'>");

            html = "<option>The component has been disabled.</option>";
            $("#system-macro-body").html(html);
        }
    }

    // Check if we already added System macro
    function chosen_detected_check(value) {
        var not_chosen = $.inArray(value, chosen_detected_list);

        if (not_chosen > -1) {
            return 0;
        }

        return 1;
    }
</script>

<h1><?php echo _('User Macros Component'); ?></h1>
<?php
if (is_admin()) { ?>
<p><?php echo _("Click here to configure the User Macro component") ?><a href='<?php echo get_base_url(); ?>admin/?xiwindow=components.php?config=usermacros' target="_top"><i class='fa fa-cog fa-14 tt-bind' style='margin-left: 7px; color: #000;' title='<?php echo _("Configure"); ?>'></i></a></p>
<?php 
} ?>

<div style="margin-bottom: 12px; max-width: 700px;">
    <table>
        <tr>
            <td>
                <?php echo _("<b>Component Settings: </b>") ?>
            </td>
            <td class="checkbox">
                <label style="cursor: default;">
                    <?php echo $disabled . _("Component Enabled") ?>
                </label>
            </td>
            <td class="checkbox">
                <label style="cursor: default;">
                    <?php echo $redacted . _("Redact Displayed Values") ?>
                </label>
            </td>
            <?php
            if (!$usermacro_redacted && $usermacro_user_redacted) {
            ?>
            <td class="checkbox">
                <label style="cursor: default;">
                    <?php echo "<i class='fa fa-check-square' style='font-size: 14px; color: #4D89F9; margin-right: 5px;'></i>" . _("Redact Values for Non-Admins") ?>
                </label>
            </td>
            <?php
            }
            ?>
        </tr>
    </table>
</div>

<input type="hidden" name="tab" class="form-control" value="<?php echo htmlentities($tab); ?>" id="macrotabname">

<div class="hide ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <li><a href="#tab-user"><?php echo _("User Macros") ?></a></li>
        <li><a href="#tab-system"><?php echo _("System Macros") ?></a></li>
    </ul>

    <div id="tab-user" style="padding: 10px 0;">
        <p style="max-width: 750px;"><?php echo _("You can view and edit the assigned User Macros for your system here. If you make changes to the resource.cfg file from anywhere you must to restart Nagios Core for the new macros to be utilized."); ?></p>
        <p style="max-width: 750px;"><?php echo _("<b>Note:</b> If you edit this file it will overwrite the original content so you will not be able to undo any changes. If Redact Displayed Values is enabled you will not be able to edit the file."); ?></p>

        <div style="clear: both;">
            <table style="table table-condensed" id="resource_table">
                <tr>
                    <div style="height: 40px; width: 750px; border-color: #ccc; border-style: solid solid none solid; border-width: 1px;">
                        <div style="width: 700px; float: left; border-right: 1px solid #ccc; line-height: 20px; height: 100%; margin: 0; padding: 9px;"><b>/usr/local/nagios/etc/resource.cfg</b></div>
                        <div style="float: right; padding: 11px 16px; margin: 0;"><div id="table-macro-throbber" style="display: none;"><img src="<?php echo theme_image("throbber.gif") ?>"></div><div id="resource_table_status"></div></div></div>
                    </div>
                </tr>
                <tr>
                    <textarea id="macro_file" spellcheck="false" class="form-control" <?php if ($usermacro_redacted) { echo 'readonly'; } ?> style="height: 400px; width: 750px;"></textarea>
                </tr>
                <?php if (is_admin() && !$usermacro_redacted) { ?>
                <tr>
                    <div class="btn-group" style="display: block; max-width: 750px; margin-top: 5px;">
                        <button id="macro-update-submit" class="btn btn-sm btn-primary" style="margin: 0;"><?php echo _("Update") ?></button><div id="macro-update-message" style="display: inline-block; max-width: 537px; height: 29px; margin-left: 7px; padding: 5px 10px; background-color: #fff;"><div id="macro-throbber" style="display: none;"><img src="<?php echo theme_image("throbber.gif") ?>"></div></div>
                        <div class="fr">
                            <form action="index.php" style="margin: 0;">
                                <button type="submit" id="restart" name="mode" value="restart" class="btn btn-sm btn-default tt-bind" title="<?php echo _('Restart to utilize new macros'); ?>"><i class="fa fa-refresh l"></i><?php echo _(" Restart Nagios Core") ?></button>
                            </form>
                        </div>
                    </div>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <div id="tab-system" style="padding: 10px 0;">
        <p><?php echo _("Here you can add and remove System Macros that will be displayed as a selection option when a macro is detected.") ?></p>
        <p><?php echo _("For general information about usage and which macros are supported check the Nagios Core documentation ") ?><a href="https://assets.nagios.com/downloads/nagioscore/docs/nagioscore/4/en/macrolist.html" target="_blank" rel="noreferrer"><?php echo _("here")?></a>. <?php echo _("Double click to add and remove or select multiple and click the add/remove buttons.")?></p><br>
        <a href="https://assets.nagios.com/downloads/nagioscore/docs/nagioscore/4/en/macrolist.html" target="_top"></a>
        <div class="row-fluid">
            <div class="col-sm-7 col-md-6 col-lg-6">
                <div style="display: inline-block; position: relative; vertical-align: top; width: 85%;" class="filter">
                    <span style="display: none; position: absolute; top: 7px; bottom: 0; right: 5px; color: #BBB; cursor: pointer;" class="clear-filter" title="" data-original-title="Clear"><i class="fa fa-times fa-14"></i></span>
                    <input type="text" id="filterMacros" class="form-control fc-fl" style="width: 100%; border-bottom: 0;" placeholder="Filter...">
                    <select class="form-control fc-m lists" multiple="multiple" id="system-macro-body" style="width: 100%; height: 500px;"></select>
                </div>
                <div style="display: inline-block; vertical-align: top; margin: 225px 0 0 20px;">
                    <button id="add-multiple-detected" class="btn btn-sm btn-primary" style="margin: 0;"><i class="fa fa-chevron-right"></i></button><br>
                    <button id="remove-multiple-detected" class="btn btn-sm btn-primary" style="margin-top: 8px;"><i class="fa fa-chevron-left"></i></button>
                </div>
            </div>
            <div class="col-sm-5 col-md-6 col-lg-6">
                <div>
                    <span class="sys-detect-span" style="padding-top: 5px;"><b><?php echo _("Active Detected System Macros")?></b></span>
                    <select class="form-control fc-m lists" multiple="multiple" id="system-detect-content" style="width: 100%; height: 500px; margin-top: 15px;"></select>
                </div>
            </div>
            <div class="btn-group" style="margin-top: 7px;">
                <button id="system-update-submit" class="btn btn-sm btn-primary"><?php echo _("Update") ?></button><div id="system-update-message" style="display: inline-block; width: 600px; height: 29px; margin-left: 7px; padding: 5px 10px; background-color: transparent;"><div id="macro-throbber-system" style="display: none;"><img src="<?php echo theme_image("throbber.gif") ?>"></div></div>
            </div>
        </div>

    </div>
</div>

<?php
    // closes the HTML page
    do_page_end(true);
}


/**
 * Get resource.cfg file data first param is for 
 *
 * @param   string  $raw                    Raw data or object
 * @param   string  $request_query_time     Updates the time last read
 * @return  string                          JSON data
 */
function get_resource_data($raw, $reset_query_time)
{
    $return = nagiosccm_get_resource_cfg($raw, $reset_query_time);
    return json_encode($return);
}


/**
 * Write New Macro to File
 */
function run_update($macro, $new_value)
{
    $write_time = time();
    $output = nagiosccm_add_new_macro($macro, $new_value, $write_time);
    return $output;
}


/**
 * Add User Macro Modal
 */
function control_modal_content()
{
    // Get the raw resource.cfg contents
    $usermacro_disable = get_option("usermacro_disable", 0);
    $macro_data = nagiosccm_get_resource_cfg(true);
    $macro_content = "";

    if (!$macro_data) {
        $status = "<img src=" . theme_image("error_small.png") . " class='tt-bind' data-original-title='File not found.  Check permission!'>";
    } else {
        $status = "<img src=" . theme_image("ok_small.png") . " class='tt-bind' data-original-title='File Verified'>";

        foreach ($macro_data as $key => $value) {
            $macro_content .= $value . "\n";
        }
    }

    if ($usermacro_disable == 1) {
        $content = $macro_data;
    } else {
        $content = '
        <div id="macro-control-modal">
            <input id="tiggered-button-id" type="hidden" value="0">
            <div style="clear: both;">
                <h4 id="comment-title" style="padding: 0; margin: 0 0 20px 0;">' . _("Create New User Macro") . '</h4>
                <p>You must restart Nagios Core after creating a new macro for the value to be translated.</p>
                <div style="height: 40px; width: 550px; background-color: #fff; border-color: #ccc; border-style: solid solid none solid; border-width: 1px;">
                    <div style="width: 500px; float: left; border-right: 1px solid #ccc; height: 100%; margin: 0; padding: 9px;"><b>/usr/local/nagios/etc/resource.cfg</b></div>
                    <div style="float: right; padding: 9px 15px; margin: 0;">' . $status . '</div>
                </div>
                <textarea id="macro_file" onfocus="this.blur()" spellcheck="false" class="form-control" style="resize: none; height: 300px; width: 550px; margin-bottom: 0; background-color: #fff;" disabled>' . $macro_content . '</textarea><div id="macro-action-message" style="width: 100%; font-color: #fff; background-color: inherit; vertical-align: middle; height: 25px; margin-bottom: 15px; padding: 5px;"><div id="macro-throbber" style="display: none;"><img src="' . theme_image("throbber.gif") . '"></div></div>
                <div class="btn-group">
                    <label style="float: left; padding: 6px;">' . _("New Macro") . '</label>
                    <button id="new_macro_id" class="btn btn-default" style="font-size: 11px;" disabled></button>
                    <input id="new_macro" class="form-control btn btn-default" style="background-color: #fff; border-color: none; text-decoration: none;">
                    <button id="macro-update-submit" class="btn btn-sm btn-primary" style="margin-left: 10px;">' . _("Save Macro") . '</button>
                    <button id="macro-update-close" class="btn btn-sm btn-default" style="margin-left: 4px;">' . _("Close") . '</button>
                </div>
            </div>
        </div>';
    }

    echo $content;
}


function get_system_macros_file()
{
    $lines = file('includes/nagios_macros.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $systemmacros = array();
    $key = false;

    foreach($lines as $line) {
        if (preg_match("/^[^\$]/", $line)) {
            $key = $line;
        }

        if ($key != $line)
            $systemmacros[$key][] = $line;
    }

    return json_encode($systemmacros);
}


// System Macros
function set_system_macros_detected($content)
{
    if (!empty($content)) {
        $return["response"] = _("Successfuly updated the Detected System Macro options table in the Nagios XI database.");
        $return["return_code"] = 0;
    } else {
        $return["response"] = _("All System Macros have been removed from detection.");
        $return["return_code"] = 0;
    }

    $system_macros = array();
    $content = trim($content);
    if (!empty($content)) {
        $system_macros = explode("\n", $content);
    }

    set_option("usermacro_system_macros_detected", base64_encode(serialize($system_macros)));

    return json_encode($return);
}


// System Macros
function get_system_macros_detected()
{
    $system_macros = get_option("usermacro_system_macros_detected", array());

    if (!empty($system_macros)) {
        $system_macros = unserialize(base64_decode($system_macros));
    } else {
        $defaults = array('$HOSTNAME$',
                          '$HOSTDISPLAYNAME$',
                          '$HOSTALIAS$',
                          '$SERVICEDESC$',
                          '$SERVICEDISPLAYNAME$',
                          '$SERVICESTATE$');
        set_option("usermacro_system_macros_detected", base64_encode(serialize($defaults)));
    }

    return $system_macros;
}


// Update function for index page
function full_overwrite_from_index($content)
{
    // Write to file
    $write = file_put_contents('/usr/local/nagios/etc/resource.cfg', $content);

    // Check for success
    if ($write) {
        $return["response"] = _("Successfuly updated the resource.cfg file.");
        $return["return_code"] = 0;
    } else {
        $return["response"] = _("Writing to /usr/local/nagios/etc/resource.cfg failed.  Verify file permissions.");
        $return["return_code"] = 1;
    }

    return json_encode($return);
}