<?php
// Require common utilities and functions required to run Nagios XI
require_once(dirname(__FILE__) . '/../includes/common.inc.php');
require_once(dirname(__FILE__) . '/../config.inc.php');
require_once(dirname(__FILE__) . '/../includes/components/nxti/includes/utils-traps.inc.php');
require_once(dirname(__FILE__) . '/../includes/utils-banner_message.inc.php');

// Initialization stuff
pre_init();
init_session(); // Connect to databases, handle cookies/session, set page headers

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication();

// Only admins can access this page
if (!is_admin()) {
    echo _("You are not authorized to access this feature. Contact your Nagios XI administrator for more information, or to obtain access to this feature.");
    exit();
}

route_request();

function route_request() {
    global $request;
    $mode = grab_request_var('mode');

    switch($mode) {
        case 'set_session':
            set_session();
            break;
        case 'message_table':
            show_banner_message_table();
            break;
        case 'banner_message_record':
            show_banner_message_record();
            break;
        default:
            display_banner_message();
            break;
    }
}

/**
 * Sets session variable for banner_message.
 *
 * This function is used to set the session variable for the banner_message. This is used to
 * store the search criteria for the banner_message table. This function is called by the
 * route_request() function.
 *
 */
function set_session() {
    $var = grab_request_var('var', '');
    $val = grab_request_var('val', '');

    $valid_vbls = array(
        'search-msg'
    );

    // Sets session variable if it's valid for search functionality.
    if (in_array($var, $valid_vbls)) {
        $_SESSION['banner_message'][$var] = $val;
    }
}

/**
 * Creates the page size selector
 *
 * Stores the html for the page size selector. This is used in build_banner_message_table() in
 * multiple places so it is made into a function to reduce code duplication.
 *
 */
function render_page_size_selector() {
    ?>
    <select class="form-control condensed num-records">
        <option value="5"  ><?php echo sprintf(_('%d Per Page'), 5);   ?></option>
        <option value="10" ><?php echo sprintf(_('%d Per Page'), 10);  ?></option>
        <option value="25" ><?php echo sprintf(_('%d Per Page'), 25);  ?></option>
        <option value="50" ><?php echo sprintf(_('%d Per Page'), 50);  ?></option>
        <option value="100"><?php echo sprintf(_('%d Per Page'), 100); ?></option>
        <option value="500"><?php echo sprintf(_('%d Per Page'), 500); ?></option>
    </select>
    <?php
}

/**
 * Creates the html for the banner_message table
 *
 * This builds the html for the banner_message table which is later on displayed to the browser
 * in build_banner_message_table(). This function uses retrieve_banner_message_data() to get the data with
 * the specific criteria that the user has selected.
 *
 */
function show_banner_message_table() {
    $page = intval(grab_request_var("page", ""));
    $perpage = intval(grab_request_var("perpage", ""));
    $orderby = grab_request_var("orderby", "");
    $orderdir = grab_request_var("orderdir", "");
    $search = grab_request_var("search", "");

    $banner_message_data = retrieve_banner_message_data($page, $perpage, $orderby, $orderdir, $search);
    if (!$banner_message_data || empty($banner_message_data)) {
        echo "<tr><td colspan='7'>" . _("No messages found! To view messages either change search criteria or add a message.") . "</td></tr>";
    }
    foreach ($banner_message_data as $key => $value):?>
        <tr>
            <td><input type="checkbox" class="banner_message_checkbox" value="<?php echo encode_form_val($value['msg_id']);?>"></td>
            <td id="def_banner_message_id<?php echo encode_form_val($value['msg_id']);?>"><?php echo encode_form_val($value["msg_id"]);?></td>
            <td id="def_banner_message_message<?php echo encode_form_val($value['msg_id']);?>"><?php echo encode_form_val($value["message"]);?></td>
            <td id="def_banner_message_date<?php echo encode_form_val($value['msg_id']);?>"><?php echo encode_form_val($value["time_created"]);?></td>
            <td id="def_banner_message_user_details<?php echo encode_form_val($value['msg_id']);?>" class="clickable user_details_btn_container" align="center" data-id="<?php echo encode_form_val($value['msg_id']);?>">
                <a id="user_details_btn_<?php echo encode_form_val($value['msg_id']);?>" class="user_details_btn">
                    <div class="user_details_btn_text"> <?php echo _("View");?> </div>
                </a>
            </td>
            <td align="center">
                <?php if ($value['message_active'] == 1): ?>
                    <a class="banner_message_toggle_display_button" data-enabled="<?php echo encode_form_val($value['message_active']); ?>" data-id="<?php echo encode_form_val($value['msg_id']); ?>"><?php echo _("Yes"); ?></a>
                <?php else: ?>
                    <a class="banner_message_toggle_display_button" data-enabled="<?php echo encode_form_val($value['message_active']); ?>" data-id="<?php echo encode_form_val($value['msg_id']); ?>" style="color: red; font-weight: bold;"><?php echo _("No"); ?></a>
                <?php endif; ?>
            </td>
            <td align="center">
                <a class="msg_edit_button" style="text-decoration: none;" data-id="<?php echo encode_form_val($value['msg_id']); ?>">
                    <img class='tableItemButton tt-bind' src="<?php echo theme_image("pencil.png"); ?>" border='0' alt='Edit' title=<?php echo _('Edit'); ?>>
                </a>
                <a class="msg_copy_button" data-id="<?php echo encode_form_val($value['msg_id']); ?>" style="text-decoration: none;">
                    <img class='tableItemButton tt-bind' src="<?php echo theme_image("statusdetailmulti.png"); ?>" border='0' alt='Copy' title=<?php echo _('Copy'); ?>>
                </a>
                <a class="msg_delete_button" style="text-decoration: none;" data-id="<?php echo encode_form_val($value['msg_id']);?>">
                    <img class='tableItemButton tt-bind' src="<?php echo theme_image("cross.png"); ?>" border='0' alt='Delete' title=<?php echo _('Delete'); ?>>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    <script>

        /**
         * Gets user array for edit/copy page.
         *
         * This function returns an array of all the users and removes those that are in the specific
         * users arr. We concat an array of the users with the specified users together and then use the
         *  filter function to remove any users that have duplicate entries. We do this to get an accurate
         * list of users for the users select box when editing or copying a modal. This way we are keeping
         * the visuals consistant with the create msg modal and are only displaying users once, either in
         * the users box or in the selected users box.
         *
         * @param array arr     An array that you want to concat with users array and remove any 
         *                      matching values.
         * 
         */
        function populate_users_edit_banner_message(arr) {
            $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                action: 'retrieve_users' 
            }, function(data){
                let users_arr = [];
                let user_data = data;
                if (user_data && user_data.length > 0) {
                    for (let i = 0; i < user_data.length; i++) {
                        let username = user_data[i].username;
                        users_arr.push(username);
                    }
                    
                    users_arr_altered = users_arr.concat(arr);
                    //creates final_users_arr by filtering out any of the users that were in both arr and users_arr
                    final_users_arr = users_arr_altered.filter(function(val) {
                        return arr.indexOf(val) == -1;
                    });
                    for (let i = 0; i < final_users_arr.length; i++ ) {
                        let username = final_users_arr[i];
                        let select_row = "<option>" + username + "</option>";
                        $("#select_specific_users_banner_message").append(select_row);
                    }
                }
            }, "json")
            .fail(function(xhr, error_string, error_thrown) {
                flash_message("<?php echo _('An error occured: ')?>" + error_string, 'error');
            });
        }

        function populate_modal_with_data(data) {
            let start_date = data[0].start_date;
            let end_date = data[0].end_date;
            if (start_date == '0001-01-01') {
                start_date = null;
            }
            if (end_date == '0001-01-01') {
                end_date = null;
            }
            $('#banner_message').val(data[0].message);
            $('#banner_message_start_date').val(start_date);
            $('#banner_message_end_date').val(end_date);
            if (data[0].acknowledgeable == 1) {
                $('#acknowledgeable_banner_message').prop('checked', true);
            } else {
                $('#acknowledgeable_banner_message').prop('checked', false);
            }

            if (data[0].message_active == 1) {
                $('#enable_banner_message').prop('checked', true);
            } else {
                $('#enable_banner_message').prop('checked', false);
            }

            if (data[0].specify_users == 1) {
                $('#specify_users').prop('checked', true);
                $('#select_specific_users_banner_message').prop('disabled', false);
                $('#selected_users').prop('disabled', false);
                $('#select_specific_users_banner_message').removeClass('element_disabled');
                $('#selected_users').removeClass('element_disabled');
                $('.modal_users').removeClass('element_disabled_text');
                $('.modal_selected_users').removeClass('element_disabled_text');
            } else {
                $('#specify_users').prop('checked', false);
            }

            if (data[0].schedule_message == 1) {
                $('#schedule_banner_message').prop('checked', true);
                $('#banner_message_start_date').prop('disabled', false);
                $('#banner_message_end_date').prop('disabled', false);
                $('#banner_message_start_date').removeClass('element_disabled');
                $('#banner_message_end_date').removeClass('element_disabled');
                $('.modal_start_date_label').removeClass('element_disabled_text');
                $('.modal_end_date_label').removeClass('element_disabled_text');
                $('#message_time_frame').removeClass('element_disabled_text');
            } else {
                $('#schedule_banner_message').prop('checked', false);
            }
            $('#banner_message_banner_color').val(data[0].banner_color);

            if (data && data.length > 0) {
                for (let i = 0; i < data.length; i++) {
                    let username = data[i].username;
                    let user_id = data[i].user_id;
                    let select_row = `<option value=${user_id}>${username}</option>`;
                    if (data[i].specified == 0) {
                        $("#select_specific_users_banner_message").append(select_row);
                    } else {
                        $("#selected_users").append(select_row);
                    }
                }
            }
        }

        $(document).ready(function() {
            $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                action: 'retrieve_banner_message'
            }, function(data) {
                if (data && data.length > 0) {
                    let checked = (data[0].feature_active == 1) ? true : false;
                    $('#enable_banner_message_toggle').attr("checked", checked);
                }
            }, "json")
            .fail(function(xhr, error_string, error_thrown) {
                flash_message("<?php echo _('An error occured: ')?>" + error_string, 'error');
            });

            $('.user_details_btn_container').click(function(event) {
                let data_id = $(this).attr('data-id');
                get_user_details_by_id(data_id);
                event.stopPropagation();
            })

            $('#enable_banner_message_toggle').unbind().click(function() {
                let toggle = $('#enable_banner_message_toggle').is(":checked");
                let feature_active = (toggle === true) ? 1 : 0;
                if ($('.banner_message_checkbox').length == 0) {
                    flash_message("<?php echo _('Enabling/disabling only affects this feature when there has been a message set.'); ?>", 'info', true);
                } else {
                    $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                        action: 'update_feature_display',
                        setting_value: feature_active
                    }, function() {}, "json")
                    .done(function(response) {
                        let toggle = $('#enable_banner_message_toggle').is(":checked");
                        if (response.msg_type == 'error') {
                            flash_message(response.message, response.msg_type, true);
                        } else {
                            if (toggle == false) {
                                flash_message("<?php echo _('Banner message feature has been successsfully disabled.'); ?>", response.msg_type, true);
                            } else if (toggle == true) {
                                flash_message("<?php echo _('Banner message feature has been successsfully enabled.'); ?>", response.msg_type, true);
                            }
                        }
                    })
                    .fail(function(xhr, error_string, error_thrown) {
                        flash_message("<?php echo _('Failed to update the message display setting: ')?>" + error_string, 'error');
                    });
                }
            })

            $('.banner_message_toggle_display_button').click(function () {
                var toggle_display_mode = 0;
                if ($(this).attr('data-enabled') == 0) {
                    toggle_display_mode = 1;
                }

                var data_id = $(this).attr('data-id');
                $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                    action: 'update_display_banner_message',
                    id: data_id,
                    display: toggle_display_mode
                }, function() {}, "json")
                .done(function(response) {
                    update_banner_message_table();
                    flash_message(response.message, response.msg_type, true);
                })
                .fail(function(xhr, error_string, error_thrown) {
                    flash_message("<?php echo _('Failed to update the message display settings: ')?>" + error_string, 'error');
                });
            });

            $('.msg_edit_button').click(function() {
                //stops modal from opening and closing due to clicking outside of modal
                event.stopPropagation();
                let data_id = $(this).attr('data-id');
                $("#banner_message_modal_content").html(modal_content);
                $("#banner_message_modal").fadeIn(200);
                blackout();
                $('#banner_message_modal_submit_btn').html('<?php echo _("Update Message"); ?>')
                $("#banner_message_form").attr("onsubmit", "update_banner_message_settings(event)");
                $("#banner_message_form").attr("msg_id", data_id);
                $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                    action: 'retrieve_specific_banner_message',
                    msg_id: data_id
                }, function(data) {
                    populate_modal_with_data(data);
                }, "json")
            });

            $('.msg_copy_button').click(function(event) {
                event.stopPropagation();
                let data_id = $(this).attr('data-id');
                $("#banner_message_modal_content").html(modal_content);
                $("#banner_message_modal").fadeIn(200);
                blackout();
                $('#banner_message_modal_submit_btn').html('<?php echo _("Copy Message"); ?>')
                $("#banner_message_form").attr("msg_id", data_id);

                $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                    action: 'retrieve_specific_banner_message',
                    msg_id: data_id
                }, function(data) {
                    populate_modal_with_data(data);
                }, "json")
            });

            $('.msg_delete_button').click(function() {
                var msg_delete_ok = confirm('<?php echo _("Are you sure you want to delete this message?") ?>');
                if (msg_delete_ok) {
                    var data_id = $(this).attr('data-id');
                    $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                        action: 'delete_banner_message',
                        id: data_id
                    }, function() {}, "json")
                    .done(function(response) {
                        update_banner_message_table();
                        flash_message(response.message, response.msg_type, true);
                    })
                    .fail(function(xhr, error_string, error_thrown) {
                        flash_message("<?php echo _('An error occured: ')?>" + error_string, 'error');
                    });
                }
            });

            var defs_hidden_form_string = "";
            $(".banner_message_checkbox").each(function () {
                if ($("#banner_message_hidden_form" + $(this).val()).length ) {
                    // The ID exists
                    if ($("#banner_message_hidden_form" + $(this).val()).val() > 0) {
                        // make sure the box is checked!
                        $(this).prop('checked', true);
                    }
                }
                else {
                    defs_hidden_form_string += "<input type='hidden' id ='banner_message_hidden_form" + $(this).val() +"' name=id[] value='-1'>";
                }
            });

            $("#banner_message_hidden_form").append(defs_hidden_form_string);
        });
    </script>
<?php
}

/**
 * Gets the count of the banner_message data.
 *
 * Gets count of the banner_message data to be used in pagination. This is used in update_banner_message_table()
 *
 */
function show_banner_message_record() {
    $search = grab_request_var("search", "");
    $record_count = get_count_banner_message_data($search);
    echo json_encode(array("recordcount" => $record_count));
}

/**
 * Builds the HTML for the banner_message page
 *
 * Default option in the route request that builds the inital html for the banner_message page.
 * Houses all of the other functions or has them ran on the building of the page.
 *
 */
function display_banner_message() {

    $msg_search = '';
    if (isset($_SESSION['banner_message']['search-msg'])) {
        $msg_search = $_SESSION['banner_message']['search-msg'];
    }

    // Start the page
    do_page_start(array("page_title" => _('Admin Announcement Banner')), true);
    ?>
    <script>
        let modal_content = "";
        function populate_users_banner_message() {
            $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                action: 'retrieve_users' 
            }, function(data){
                let user_data = data;
                if (user_data && user_data.length > 0) {
                    for (let i = 0; i < user_data.length; i++) {
                        let username = user_data[i].username;
                        let user_id = user_data[i].user_id;
                        let select_row = `<option value=${user_id}>${username}</option>`;
                        $("#select_specific_users_banner_message").append(select_row);
                    }
                }
            }, "json")
            .fail(function(xhr, error_string, error_thrown) {
                flash_message("<?php echo _('An error occured: ')?>" + error_string, 'error');
            });
        }


        /**
         * Callback function to retrieve user data
         *
         * A callback function to retrieve user data for the user details table.
         * The reason it is a callback instead of a normal function is because
         * we want to use the user data along side with the message data.
         *
         * @param array callback  The user data.
         *
         */
        function retrieveUsers(callback) {
            $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                action: 'retrieve_users'
            }, function(data){
                let user_data = data;
                callback(user_data);
            }, "json")
            .fail(function(xhr, error_string, error_thrown) {
                flash_message("<?php echo _('An error occured: ')?>" + error_string, 'error');
            });
        }

        /**
         * Builds user details table for a specific message.
         *
         * Takes in the specific msg id and builds the user details table for that message.
         * Uses retrieveUsers() to get the user names for the table as a call back so that 
         * we can use the user data along side with the message data. Loops through the data
         * and to give the correct values for each user in the table.
         *
         * @param int $message_id   The id of the msg in the database.
         *
         */
        function get_user_details_by_id(message_id) {
            //create html for user details that will be added to the modal
            let user_name = '<?php echo addslashes(_("Username")); ?>';
            let user_details = '<?php echo addslashes(_("User Details"));?>';
            let selected_users = '<?php echo addslashes(_("Selected Users"));?>';
            let acknowledged = '<?php echo addslashes(_("Acknowledged"));?>';
            let needs_acknowledgement = '<?php echo addslashes(_("Needs Acknowledgement"));?>';
            let close = '<?php echo addslashes(_('Close')); ?>';
            let acknowledged_title = '<?php echo addslashes(_('Users that have acknowledged this message.'))?>';
            let selected_users_title = '<?php echo addslashes(_('Users that are shown this message.')); ?>';

            let user_details_modal = `<div class="user_details_modal_wrapper">
                                        <h5 class="center_user_details" id="user_details_caption">${user_details}</h5>
                                            <div class="user_details_table">
                                                <table class="table table-condensed table-striped table-bordered" style="margin-bottom: 0px;">
                                                   
                                                    <tr>
                                                        <th class="center_user_details">${user_name}</th>
                                                        <th class="center_user_details">${selected_users}<i class='fa fa-question-circle pop tt-bind' style="margin-left: 5px;" title='${selected_users_title}'></i></th>
                                                        <th class="center_user_details">${acknowledged}<i class='fa fa-question-circle pop tt-bind' style="margin-left: 5px;" title='${acknowledged_title}'></i></th>
                                                        <th class="center_user_details">${needs_acknowledgement}</th>
                                                    </tr>
                                                    <tbody id="user_details_table_${message_id}" class="user_details_table_body" style="margin-bottom: 5px;"></tbody>
                                                </table>
                                            </div>
                                        <button id="banner_message_close_modal_btn" class="btn-sm btn-default btn submitbutton user_details_modal_btn">${close}</button>
                                    </div>`;


            $("#banner_message_modal_content").html(user_details_modal);
            $("#banner_message_modal").fadeIn(200);
            blackout();
            retrieveUsers(function(user_names) {
                $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", { action: 'retrieve_specific_banner_message', msg_id: message_id }, function(data){
                    let banner_message_data = JSON.parse(data);
                    let msg_id = banner_message_data[0].msg_id;
                    let display_specific_banner_message = "<tr><th>" + msg_id + "</th><th colspan = 3>" + banner_message_data[0].message + "</th><tr>";

                    //empty user details table on click to prevent duplicate data
                    $(`#user_details_table_${msg_id}`).empty();
                    if (banner_message_data && banner_message_data.length > 0) {
                        for (let i = 0; i < user_names.length; i++) {
                            let users = user_names[i].username;
                            let displayed_to_td = "<td class='user_details_checkmarks' style='color: #4D89F9;'></td>";
                            let acknowledged_td = "<td class='user_details_checkmarks' style='color: #4D89F9;'></td>";
                            let needs_acknowledgement_td = "<td class='user_details_checkmarks' style='color: #4D89F9;'></td>";

                            //sets checkmark if the banner_message was displayed to this user
                            if (banner_message_data[i].specify_users == 0 || banner_message_data[i].specify_users == 1 && banner_message_data[i].specified == 1 ) {
                                displayed_to_td = "<td class='user_details_checkmarks' style='color: #4D89F9;'>&#10003;</td>";
                            }

                            //sets checkmark if the banner_message was acknowledged by this user
                            if ( banner_message_data[i].acknowledged == 1) {
                                acknowledged_td = "<td class='user_details_checkmarks' style='color: #4D89F9;'>&#10003;</td>";
                            }

                            //sets checkmark if the banner_message needs to be acknowledged by this user
                            if ( banner_message_data[0].specify_users == 1) {
                                if (banner_message_data[0].acknowledgeable == 1 && banner_message_data[i].acknowledged == 0 && banner_message_data[i].specified == 1) {
                                    needs_acknowledgement_td = "<td class='user_details_checkmarks' style='color: #4D89F9;'>&#10003;</td>";
                                }
                            } else if ( banner_message_data[0].specify_users == 0) {
                                if (banner_message_data[0].acknowledgeable == 1 && banner_message_data[i].acknowledged == 0) {
                                    needs_acknowledgement_td = "<td class='user_details_checkmarks' style='color: #4D89F9;'>&#10003;</td>";
                                }
                            }

                            //create row for expanded table and append it to table.
                            let user_row = "<tr><td class='center_user_details'>" + user_names[i].username + "</td>" + displayed_to_td + acknowledged_td + needs_acknowledgement_td + "</tr>"

                            $(`#user_details_table_${msg_id}`).append(user_row);
                        }
                        $('#specific_banner_message').html(display_specific_banner_message);
                    } 
                })
                .fail(function(xhr, error_string, error_thrown) {
                    flash_message("<?php echo _('An error occured: ')?>" + error_string, 'error');
                });
            });
        }

        $(document).ready(function(){

            // create modal content for adding/ editing a message.
            let _message_title = '<?php echo addslashes(_("Admin Announcement Banner")); ?>';
            let _message = '<?php echo addslashes(_("Message")); ?>';
            let _enable_message = '<?php echo addslashes(_("Enable message")); ?>';
            let _schedule_message = '<?php echo addslashes(_("Schedule message")); ?>';
            let _time_frame = '<?php echo addslashes(_("Time frame")); ?>';
            let _start = '<?php echo addslashes(_("Start")); ?>';
            let _end = '<?php echo addslashes(_("End")); ?>';
            let _acknowledgeable_message = '<?php echo addslashes(_("Acknowledgeable message")); ?>';
            let _set_specific_descriptor = '<?php echo addslashes(_("Set message only for specific users")); ?>';
            let _users = '<?php echo addslashes(_("Users")); ?>';
            let _selected_users = '<?php echo addslashes(_("Selected Users")); ?>';
            let _double_click_descriptor = '<?= addslashes(_("Double click on users to select/unselect users.")); ?>';
            let _banner_type = '<?php echo addslashes(_("Set banner type")); ?>';
            let _info = '<?php echo addslashes(_("Information")); ?>';
            let _success = '<?php echo addslashes(_("Success")); ?>';
            let _warning = '<?php echo addslashes(_("Warning")); ?>';
            let _critical = '<?php echo addslashes(_("Critical")); ?>';
            let _create_message = '<?php echo addslashes(_("Create Message")); ?>';
            let _cancel = '<?php echo addslashes(_("Cancel")); ?>';
            let _dismiss = '<?php echo addslashes(_("Dismiss")); ?>';
            let _add_selected = '<?php echo addslashes(_("Add selected")); ?>';
            let _remove_selected = '<?php echo addslashes(_("Remove selected")); ?>';

            modal_content = `<h5 id="banner_message_modal_header" class="modal_ul">${_message_title}</h5>
                            <span class="tt-bind" id="banner_message_top_close_modal_btn" title="${_dismiss}" data-placement="left"><i class="fa fa-times" style="font-size: 2em;"></i></span>
                            <form id="banner_message_form" onsubmit ="submit_banner_message(event)">
                                <table class="table table-condensed table-no-border table-auto-width">
                                    <tr>
                                        <td class="vt"><label for="banner_message">${_message}:</label></td>
                                        <td><textarea  rows="4" cols="40" type="text" class="form-control" id="banner_message" name="banner_message"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td class="error_msg_accomplice"></td>
                                        <td id="error_msg_banner_message" class="banner_message_modal_error_msg"></td>
                                    </tr>
                                    <tr>
                                        <td class="vt banner_message_table">
                                            <label for="banner_message">${_banner_type}:</label>
                                        </td>
                                        <td>
                                            <select id="banner_message_banner_color" class="form-control">
                                                <option value="banner_message_banner_info">${_info}</option>
                                                <option value="banner_message_banner_success">${_success}</option>
                                                <option value="banner_message_banner_warning">${_warning}</option>
                                                <option value="banner_message_banner_critical">${_critical}</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="banner_message_table">
                                            <label>${_enable_message}:</label>
                                        </td>
                                        <td class="banner_message_table">
                                            <input type="checkbox" id="enable_banner_message" name="enable_banner_message" checked="checked" class="banner_message_radio_btn">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="vt banner_message_table"><label>${_schedule_message}:</label></td>
                                        <td class="banner_message_table">
                                            <input type="checkbox" id="schedule_banner_message" name="schedule_banner_message" class="banner_message_radio_btn">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="vt banner_message_table">
                                            <label id="message_time_frame" class="element_disabled_text">${_time_frame}:</label>
                                        </td>
                                        <td class="banner_message_table">
                                            <label class="modal_start_date_label element_disabled_text">${_start}:</label>
                                            <input type="date" id="banner_message_start_date" class="banner_message_start_date element_disabled" disabled>
                                            <label class="modal_end_date_label element_disabled_text">${_end}:</label>
                                            <input type="date" id="banner_message_end_date" class="banner_message_end_date element_disabled" disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="error_time_accomplice"></td>
                                        <td id="error_msg_schedule_time" class="banner_message_modal_error_msg"></td>
                                    </tr>
                                    <tr>
                                        <td class="banner_message_table">
                                            <label>${_acknowledgeable_message}:</label>
                                        </td>
                                        <td class="banner_message_table">
                                            <input type="checkbox" id="acknowledgeable_banner_message" name="acknowledgeable_banner_message" checked="checked" class="banner_message_radio_btn">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="vt banner_message_table">
                                            <label>${_set_specific_descriptor}:</label>
                                        </td>
                                        <td class="banner_message_table">
                                            <input type="checkbox" id="specify_users" name="specify_users" class="banner_message_radio_btn">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="modal_users element_disabled_text">${_users}</th>
                                        <th class="modal_selected_users element_disabled_text">${_selected_users}</th>
                                    </tr>
                                    <tr>
                                        <td style="width: 290px;">
                                            <select id="select_specific_users_banner_message" class="element_disabled" multiple disabled></select>
                                            <div id="select_box_btn_wrapper">
                                            <div style="margin-top: 69px;">
                                            <button id="banner_message_modal_users_btn" class="btn btn-xs btn-primary" title="${_add_selected}"><i class="fa fa-chevron-right" style="margin-left: 5px;"></i></button>
                                            <button id="banner_message_modal_selected_users_btn" class="btn btn-xs btn-primary" title="${_remove_selected}"><i class="fa fa-chevron-left" style="margin-right: 5px;"></i></button>
                                            </div>
                                            </div>
                                            <div class="subtext">${_double_click_descriptor}</div>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <select id="selected_users" name="selected_users[]" class="element_disabled" multiple disabled></select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="banner_message_table" id="banner_message_modal_btn_container">
                                            <button type="submit" id="banner_message_modal_submit_btn" style="display: inline-block;" class="submitbutton btn btn-sm btn-primary banner_message_msg_btn">${_create_message}</button>
                                            <button id="banner_message_close_modal_btn" class="btn-sm btn-default btn submitbutton">${_cancel}</button>
                                        </td>
                                    </tr>
                                </table>
                            </form>`


            $('#banner_message_modal_btn').click(function(event){
                event.preventDefault();
                //stops modal from opening and closing due to clicking outside of modal
                event.stopPropagation();
                $("#banner_message_modal_content").html(modal_content);
                $("#banner_message_modal").fadeIn(200);
                blackout();
                populate_users_banner_message();
            })

            // esc key close modals
            $(document).keyup(function(event) {
                if (event.keyCode === 27 && $('#banner_message_modal').css('display') === 'block') {
                    $('#banner_message_modal').fadeOut(200);
                    clear_blackout();
                }

                if (event.keyCode === 27 && $('#confirm-rtd').css('display') === 'block') {
                    $('#confirm-rtd').fadeOut(200);
                    clear_blackout();
                }
            });

            // click outside of modals closes modals
            $(document).on('click', function(event){
                if ($(event.target).closest('#banner_message_modal').length === 0 && $('#banner_message_modal').css('display') === 'block') {
                    $('#banner_message_modal').fadeOut(200);
                    clear_blackout();
                }

                if ($(event.target).closest('#confirm-rtd').length === 0 && $('#confirm-rtd').css('display') === 'block') {
                    $('#confirm-rtd').fadeOut(200);
                    clear_blackout();
                }
            })

            // Using event delegation since the button is added dynamically and doesn't exsist in the dom at the time the event handler is set up.
            $('#banner_message_modal').on('click', '#banner_message_close_modal_btn', function(event) {
                event.preventDefault();
                $("#banner_message_modal").fadeOut(200);
                clear_blackout();
            });

            $('#banner_message_modal').on('click', '#banner_message_top_close_modal_btn', function(event) {
                $("#banner_message_modal").fadeOut(200);
                clear_blackout();
            });

            // Move options from #select_specific_users_banner_message to #selected_users
            $('#banner_message_modal').on('dblclick', '#select_specific_users_banner_message option:selected', function() {
                $('#select_specific_users_banner_message option:selected').appendTo('#selected_users');
                deselectAllOptions('#selected_users');
            });

            // Move options from #selected_users back to #select_specific_users_banner_message
            $('#banner_message_modal').on('dblclick', '#selected_users option:selected', function() {
                $('#selected_users option:selected').appendTo('#select_specific_users_banner_message');
                deselectAllOptions('#select_specific_users_banner_message');
            });

            // Buttons to move between select boxes
            $('#banner_message_modal').on('click', '#banner_message_modal_users_btn', function() {
                event.preventDefault();
                $('#select_specific_users_banner_message option:selected').appendTo('#selected_users');
                deselectAllOptions('#selected_users');
            });

            $('#banner_message_modal').on('click', '#banner_message_modal_selected_users_btn', function() {
                event.preventDefault();
                $('#selected_users option:selected').appendTo('#select_specific_users_banner_message');
                deselectAllOptions('#select_specific_users_banner_message');
            });

            // error handling for if certain fields are left empty when they shouldn't be
            $('#banner_message_modal').on('click', '.banner_message_msg_btn' , function(event) {
                let inputValue = $("#banner_message").val().trim(); 
                let schedule_banner_message = $('#schedule_banner_message').is(":checked");
                let start_date = $('#banner_message_start_date').val();
                let end_date = $('#banner_message_end_date').val();
                let error_msg_banner_message = '<?php echo addslashes(_("Message is empty. Please enter a message.")); ?>';
                let error_msg_schedule = '<?php echo addslashes(_("Can not schedule a message without a specified time frame.")); ?>';
                if (inputValue === '') {
                    event.preventDefault();
                    $('.error_msg_accomplice').show();
                    $('#error_msg_banner_message').text(error_msg_banner_message).show();
                    $('#banner_message_modal').animate({scrollTop: 0}, 'slow');
                    $('#banner_message').css('border-color', 'red');
                } else {
                    $('.error_msg_accomplice').hide();
                    $('#error_msg_banner_message').hide();
                    $('#banner_message').css('border-color', 'inherit');
                }

                if (schedule_banner_message && start_date == '' && end_date == ''){
                    event.preventDefault();
                    $('.error_time_accomplice').show();
                    $('#error_msg_schedule_time').text(error_msg_schedule).show();
                    $('#banner_message_modal').animate({scrollTop: 0}, 'slow');
                    $('#banner_message_start_date').css('border-color', 'red');
                    $('#banner_message_end_date').css('border-color', 'red');
                } else if (schedule_banner_message && (start_date != '' || end_date != '')) {
                    $('.error_time_accomplice').hide();
                    $('#error_msg_schedule_time').hide();
                    $('#banner_message_start_date').css('border-color', 'inherit');
                    $('#banner_message_end_date').css('border-color', 'inherit');
                } else if (!schedule_banner_message) {
                    $('.error_time_accomplice').hide();
                    $('#error_msg_schedule_time').hide();
                    $('#banner_message_start_date').css('border-color', 'inherit');
                    $('#banner_message_end_date').css('border-color', 'inherit');
                }
            });

            // alters timeframe data on modal to disable or enable based on checkbox value
            $('#banner_message_modal').on('click', '#schedule_banner_message', function(){
                let schedule_banner_message = $('#schedule_banner_message').is(":checked");
                if (schedule_banner_message) {
                    $('#banner_message_start_date').prop('disabled', false);
                    $('#banner_message_end_date').prop('disabled', false);
                    $('#banner_message_start_date').removeClass('element_disabled');
                    $('#banner_message_end_date').removeClass('element_disabled');
                    $('.modal_start_date_label').removeClass('element_disabled_text');
                    $('.modal_end_date_label').removeClass('element_disabled_text')
                    $('#message_time_frame').removeClass('element_disabled_text')
                } else {
                    $('#banner_message_start_date').prop('disabled', true);
                    $('#banner_message_end_date').prop('disabled', true);
                    $('#banner_message_start_date').addClass('element_disabled');
                    $('#banner_message_end_date').addClass('element_disabled');
                    $('.modal_start_date_label').addClass('element_disabled_text');
                    $('.modal_end_date_label').addClass('element_disabled_text')
                    $('#message_time_frame').addClass('element_disabled_text')
                }
            })

            $('#banner_message_modal').on('click', '#specify_users', function(){
                let specify_users = $('#specify_users').is(":checked");
                if (specify_users) {
                    $('#select_specific_users_banner_message').prop('disabled', false);
                    $('#selected_users').prop('disabled', false);
                    $('#select_specific_users_banner_message').removeClass('element_disabled');
                    $('#selected_users').removeClass('element_disabled');
                    $('.modal_users').removeClass('element_disabled_text');
                    $('.modal_selected_users').removeClass('element_disabled_text');
                } else {
                    $('#select_specific_users_banner_message').prop('disabled', true);
                    $('#selected_users').prop('disabled', true);
                    $('#select_specific_users_banner_message').addClass('element_disabled');
                    $('#selected_users').addClass('element_disabled');
                    $('.modal_users').addClass('element_disabled_text');
                    $('.modal_selected_users').addClass('element_disabled_text');
                    deselectAllOptions('#selected_users');
                    deselectAllOptions('#select_specific_users_banner_message');
                }
            })

        });

        // used to reselect select options for selected users incase they were unselected before submiting the data.
        function reselectAllOptions(selector) {
            $(selector).find('option').prop('selected', true);
        }

        function deselectAllOptions(selector) {
            $(selector).find('option').prop('selected', false);
        }

        /**
         * Submits the banner_message.
         *
         * Grabs data from the banner_message form and sends it to the banner_message-ajaxhelper page
         * to be processed. The banner_message-ajaxhelper page will then insert the banner_message
         * into the database.
         *
         */
        function submit_banner_message(event){
            event.preventDefault();
            reselectAllOptions('#selected_users');
            let inputValue = $("#banner_message").val();
            let created_by = '<?php echo $_SESSION["user_id"];?>';

            // throws error if we dont declare empty array and speific users is empty.
            let specific_users_arr = [];
            specific_users_arr = $("#selected_users").val();
            let specific_users_arr_int = specific_users_arr.map(Number);
            let startdate = $('#banner_message_start_date').val();
            let enddate = $('#banner_message_end_date').val();
            let banner_color = $('#banner_message_banner_color').val();
            let active_feature = $('#enable_banner_message_toggle').is(":checked");
            let enable_banner_message = $('#enable_banner_message').is(":checked");
            let schedule_banner_message = $('#schedule_banner_message').is(":checked");
            let acknowledge_banner_message = $('#acknowledgeable_banner_message').is(":checked");
            let specify_users = $('#specify_users').is(":checked");

            let enable_banner_message_value = enable_banner_message ? 1 : 0;
            let active_feature_value = active_feature ? 1 : 0;
            let schedule_banner_message_value = schedule_banner_message ? 1 : 0;
            let acknowledge_banner_message_value = acknowledge_banner_message ? 1 : 0;
            let specify_users_value = specify_users ? 1 : 0;
            startdate = startdate ? startdate : '0001-01-01';
            enddate = enddate ? enddate : '0001-01-01';

            $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                action: 'submit_banner_message',
                banner_message: inputValue,
                banner_message_creator: created_by,
                individual_banner_message: specify_users_value,
                users: specific_users_arr_int,
                acknowledgeable_banner_message: acknowledge_banner_message_value,
                banner_message_banner_color: banner_color,
                set_banner_message: enable_banner_message_value,
                schedule_banner_message: schedule_banner_message_value,
                start: startdate,
                end: enddate,
                feature_active: active_feature_value
            }, function() {}, "json")

            .done(function(response) {
                flash_message(response.message, response.msg_type, true);
                $('#banner_message_modal').fadeOut(200);
                clear_blackout();
                update_banner_message_table();
            })
            .fail(function(xhr, error_string, error_thrown) {
                flash_message("<?php echo _('Failed to send message to database. ')?>" + error_string, 'error');
                $('#banner_message_modal').fadeOut(200);
                clear_blackout();
            });
        };

        /**
         * Updates the banner_message settings.
         *
         * Grabs data from the banner_message settings form and sends it to the banner_message-ajaxhelper
         * page to be processed. The banner_message-ajaxhelper page will then update the banner_message
         * settings in the database.
         *
         */
        function update_banner_message_settings(event){
            event.preventDefault();
            reselectAllOptions('#selected_users');
            let id = $("#banner_message_form").attr("msg_id");
            let inputValue = $("#banner_message").val();
            let created_by = '<?php echo $_SESSION["user_id"];?>';

            // throws error if we dont declare empty array and speific users is empty.
            let specific_users_arr = [];
            specific_users_arr = $("#selected_users").val();
            let specific_users_arr_int = specific_users_arr.map(Number);
            let banner_color = $('#banner_message_banner_color').val();
            let startdate = $('#banner_message_start_date').val();
            let enddate = $('#banner_message_end_date').val();
            let active_feature = $('#enable_banner_message_toggle').is(":checked");
            let enable_banner_message = $('#enable_banner_message').is(":checked");
            let schedule_banner_message = $('#schedule_banner_message').is(":checked");
            let acknowledge_banner_message = $('#acknowledgeable_banner_message').is(":checked");
            let specify_users = $('#specify_users').is(":checked");

            let enable_banner_message_value = enable_banner_message ? 1 : 0;
            let schedule_banner_message_value = schedule_banner_message ? 1 : 0;
            let acknowledge_banner_message_value = acknowledge_banner_message ? 1 : 0;
            let specify_users_value = specify_users ? 1 : 0;
            startdate = startdate ? startdate : '0001-01-01';
            enddate = enddate ? enddate : '0001-01-01';

            $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                action: 'update_banner_message_settings',
                id: id,
                banner_message: inputValue,
                banner_message_creator: created_by,
                individual_banner_message: specify_users_value,
                users: specific_users_arr_int,
                acknowledgeable_banner_message: acknowledge_banner_message_value,
                banner_message_banner_color: banner_color,
                set_banner_message: enable_banner_message_value,
                schedule_banner_message: schedule_banner_message_value,
                start: startdate,
                end: enddate
            }, function() {}, "json")

            .done(function(response) {
                flash_message(response.message, response.msg_type, true);
                $('#banner_message_modal').fadeOut(200);
                clear_blackout();
                update_banner_message_table();
            })
            .fail(function(xhr, error_string, error_thrown) {
                flash_message("<?php echo _('Failed to send message to database. ')?>" + error_string, 'error');
                $('#banner_message_modal').fadeOut(200);
                clear_blackout();
            });
        };

    </script>
    <!-- Print text to the screen -->
    <!-- _() is a translation function, used to localize text by matching the passed in
    string to a translation dictionary. -->
    <h1><?php echo _('Admin Announcement Banner'); ?></h1>
    <!-- Checks db type and is postgres is used, tells the user to migrate to mysql if they want to use this feature. -->
    <?php
        global $cfg;
        $dbtype = '';
        if (array_key_exists("dbtype", $cfg['db_info'][DB_NAGIOSXI])) {
            $dbtype = $cfg['db_info'][DB_NAGIOSXI]['dbtype'];
        }

        if ($dbtype == 'pgsql') {
            echo '<p>' . _('This feature is not supported on Postgres-based systems. Please migrate to MySQL to use this feature. '). '<a href="https://support.nagios.com/kb/article/converting-postgresql-to-mysql-for-nagios-xi-560.html">' . _('Click here for instructions to migrate to MySQL.') . '</a></p>';
            do_page_end();
            die();
        }
    ?>
    <h5 class="ul"><?php echo _('Message Feature Settings'); ?></h5>
    <table class="table table-condensed table-no-border table-auto-width">
        <tr><td>
            <label><?php echo _("Enable/Disable banner message feature"); ?>:</label>
    </td><td>
    <label class="banner_message_switch">
        <input type="checkbox" checked="checked" id="enable_banner_message_toggle">
        <span class="banner_message_slider"></span>
    </label>
    </td></tr>
    </table>
    <div id="banner_message_modal">
        <div id="banner_message_modal_content">
        </div>
    </div>
    <div style="display: flex; flex-wrap: wrap;">
        <div style="display: inline-block; flex-grow: 1;">

            <div id="banner_message_wrapper">
                <div><!--BEGIN Record count, search bar, pagination -->
                    <div class="fl"> 
                        <div class="fl" id="ajax_banner_message_paging" style="vertical-align: bottom; height: 29px; line-height: 29px;"></div>
                        <div class="clear"></div>
                        <div style="margin: 3px 0 10px 0;">
                            <input type="text" class="form-control condensed tt-bind search-box" title="<?php echo _('Matches Msg Id, Message, or Date Created'); ?>" style="height: 26px;" value="<?php echo encode_form_val($msg_search);?>">
                            <button class="btn btn-xs btn-default tt-bind search-button negative-margin"><i class="fa fa-search fa-12"></i></button>
                        </div>
                    </div>
                    <div class="fr">
                        <div class="ajax-pagination" style="margin: 3px 0 10px 0;">
                        <br/>
                        <br/>
                            <button class="btn btn-xs btn-default first-page" title="<?php echo _('First Page'); ?>"><i class="fa fa-fast-backward"></i></button>
                            <button class="btn btn-xs btn-default previous-page" title="<?php echo _('Previous Page'); ?>"><i class="fa fa-chevron-left l"></i></button>
                            <span style="margin: 0 10px;"><span class="pagenum ajax_banner_message_page_total"><?php echo str_replace(array("%1", "%2"), array("0", "0"), _('Page %1 of %2')); ?></span></span>
                            <button class="btn btn-xs btn-default next-page" title="<?php echo _('Next Page'); ?>"><i class="fa fa-chevron-right r"></i></button>
                            <button class="btn btn-xs btn-default last-page" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-forward"></i></button>

                            <?php render_page_size_selector(); ?>

                            <input type="text" class="form-control condensed jump-to">
                            <button class="btn btn-xs btn-default tt-bind jump" title="<?php echo _('Jump to Page'); ?>"><i class="fa fa-chevron-circle-right fa-12"></i></button>
                        </div>
                    </div>
                </div>
                <!-- END Record count, search bar, pagination -->
                <!-- BEGIN banner_message definition table -->
                <table id="banner_message_records_table" class="table table-condensed table-striped table-bordered">

                    <thead>
                        <tr>
                            <th style="width: 24px;"><input type="checkbox" class="tt-bind" id="banner_message_select_all" onclick="banner_message_check_boxes()" name="deletebox[]" title="<?php echo _('Toggle all'); ?>"></th>
                            <th class="orderByThis" rowname="banner_message_data_msg_id" style="width: 45px;"><?php echo _("Id"); ?>&nbsp;<span class="sort-indicator"><i class="fa fa-chevron-down fa-12"></i></span></th>
                            <th class="orderByThis" rowname="banner_message_data_message"><?php echo _("Message"); ?>&nbsp;<span class="sort-indicator"></span></th>
                            <th class="orderByThis" style="width: 10%;" rowname="banner_message_data_date_created"><?php echo _("Date Created"); ?>&nbsp;<span class="sort-indicator"></span></th>
                            <th style="width: 100px;"><?php echo _("User Details"); ?></th>
                            <th class="orderByThis" style="width: 65px;"><?php echo _("Active");?>&nbsp;<span class="sort-indicator"></span></th>
                            <th style="width: 75px;"><?php echo _("Actions"); ?></th>
                        </tr>
                    </thead>

                    <tbody id="ajax_banner_message_table"></tbody>

                </table>
                <!-- END banner_message definition table -->

                <div>
                    <div class="fr"> <!-- BEGIN pagination -->
                        <div class="ajax-pagination" style="margin: 3px 0 0 0;">
                            <button class="btn btn-xs btn-default first-page" title="<?php echo _('First Page'); ?>"><i class="fa fa-fast-backward"></i></button>
                            <button class="btn btn-xs btn-default previous-page" title="<?php echo _('Previous Page'); ?>"><i class="fa fa-chevron-left l"></i></button>
                            <span style="margin: 0 10px;"><span class="pagenum ajax_banner_message_page_total"><?php echo str_replace(array("%1", "%2"), array("0", "0"), _('Page %1 of %2')); ?></span></span>
                            <button class="btn btn-xs btn-default next-page" title="<?php echo _('Next Page'); ?>"><i class="fa fa-chevron-right r"></i></button>
                            <button class="btn btn-xs btn-default last-page" title="<?php echo _('Last Page'); ?>"><i class="fa fa-fast-forward"></i></button>

                            <?php render_page_size_selector(); ?>

                            <input type="text" class="form-control condensed jump-to">
                            <button class="btn btn-xs btn-default tt-bind jump" title="<?php echo _('Jump to Page'); ?>"><i class="fa fa-chevron-circle-right fa-12"></i></button>
                        </div>
                    </div> <!-- END pagination -->
                    <div class='fl'> <!-- BEGIN Multibutton Form -->
                    <form action="index.php?">
                        <div id="banner_message_hidden_form">
                        </div>
                        <div class="form-inline">
                            <button class="btn btn-sm btn-primary" id="banner_message_modal_btn"><?php echo _('Add Message'); ?></button>

                            <div class="input-group">
                                <label class="input-group-addon"><?php echo _("With selected"); ?></label>
                                <select name="mode" id="show_banner_message_multi_action" style="width: 75px" class="form-control">
                                    <option value="deletemany"><?php echo _("Delete"); ?></option>
                                    <option value="enablemany"><?php echo _("Enable"); ?></option>
                                    <option value="disablemany"><?php echo _("Disable"); ?></option>
                                </select>
                            </div><!--
                         --><button id="show_banner_message_multi" class="btn btn-sm btn-default nxtiSquashedButtonsRight" type="button"><?= _("Go") ?></button>
                            <input class="btn btn-sm btn-default nxtiSquashedButtonsRight" style="display: none;" type="submit">
                        </div>
                    </form>
                </div> <!-- END Multibutton Form -->
            <!-- BEGIN Pagination Javascript -->
            <script>
                // Common to both scripts. Puts previous search in $_SESSION.
                function set_search_session(which, term) {
                    $.post('banner_message.php', {
                            mode: 'set_session',
                            var: 'search-' + which,
                            val: term
                    })
                    .fail(function(xhr, error_string, error_thrown) {
                        flash_message("<?php echo _('An error occured: ')?>" + error_string, 'error');
                    });
                }

                var def_current_page = 1;
                var def_max_page = 1;
                var def_per_page = 5;
                var msg_search_terms = "";
                var def_order_by = "msg_id"; // column of the DB
                var def_order_direction = "DESC"; // or asc

                // clicks search btn on enter key which search box is in focus
                $("#banner_message_wrapper .search-box").on('keypress', function (e) {
                    if (e.keyCode === 13) {
                        $(this).parent().children(".search-button").click();
                    }
                });

                $("#banner_message_wrapper .ajax-pagination .jump-to").on('keypress', function (e) {
                    if (e.keyCode === 13) {
                        $(this).parent().children(".jump").click();
                    }
                });

                $("#banner_message_wrapper .ajax-pagination .first-page").on('click', function () {
                    def_current_page = 1;
                    update_banner_message_table();
                });

                $("#banner_message_wrapper .ajax-pagination .previous-page").on('click', function () {
                    if (def_current_page != 1) {
                        def_current_page -= 1;
                    }
                    update_banner_message_table();
                });

                $("#banner_message_wrapper .ajax-pagination .next-page").on('click', function () {
                    if (def_current_page < def_max_page) {
                        def_current_page +=1;
                    }
                    update_banner_message_table();
                });

                $("#banner_message_wrapper .ajax-pagination .last-page").on('click', function () {
                    def_current_page = def_max_page;
                    update_banner_message_table();
                });

                $("#banner_message_wrapper .ajax-pagination .jump").on('click', function () {
                    def_current_page = $(this).parent().children(".jump-to").val();
                    update_banner_message_table();
                });

                $("#banner_message_wrapper .search-button").on('click', function () {
                    msg_search_terms = $(this).parent().children(".search-box").val();
                    set_search_session('def', msg_search_terms);
                    update_banner_message_table();
                });

                $("#banner_message_wrapper .ajax-pagination .num-records").on('change', function () {
                    def_per_page = parseInt($(this).val());
                    update_banner_message_table();
                    new_value = $(this).val();

                    // Grab the other num-records on this page and inject our new value
                    $(this).parent().parent().parent().parent().children("div").children("div .fr").children("div .ajax-pagination").children(".num-records").each(function( index ) {
                        $(this).val(new_value);
                    });
                });

                $("#banner_message_records_table .orderByThis").on('click', function () {
                    var indicator_html = "";
                    if (def_order_by == $(this).attr('rowname')) {
                        def_order_direction = (def_order_direction == "ASC"? "DESC" : "ASC");
                    }
                    else {
                        def_order_by = $(this).attr('rowname');
                        def_order_direction = "ASC";
                    }
                    indicator_html = "<i class='fa fa-chevron-" + (def_order_direction == "ASC" ? "up" : "down" ) + " fa-12'></i>";
                    $(".sort-indicator").html("");
                    $(this).children(".sort-indicator").html(indicator_html);
                    update_banner_message_table();
                });

                update_banner_message_table();
                var report_sym = 0;

                /**
                 * Builds table body for the banner_message Definitions table.
                 *
                 * This function builds the table body for the banner_message Definitions table and is used
                 * in various other functions to update the data for the tables. It is called in
                 * the function update_banner_message_table(). Calls show_banner_message_table() for the data.
                 *
                 */
                function build_banner_message_table() {
                    report_sym = 1;

                    var formvalues = '&mode=message_table';
                    formvalues += '&page=' + def_current_page;
                    formvalues += '&perpage=' + def_per_page;
                    formvalues += '&search=' + msg_search_terms;
                    formvalues += '&orderby=' + def_order_by;
                    formvalues += '&orderdir=' + def_order_direction;
                    var url = 'banner_message.php?' + formvalues;

                    current_page = 1;

                    $.get(url, {}, function(data) {
                        report_sym = 0;
                        hide_throbber();
                        $('#ajax_banner_message_table').html(data);
                        $('#banner_message_select_all').attr('checked', false);
                    });
                }

                /**
                 * Updates the banner_message Definitions table.
                 *
                 * This function updates the banner_message definitions table by calling the function
                 * and rebuilds the table with the specified data that the user requests. It
                 * is called on initial page load and when the user changes the page number,
                 * number of records per page, or search terms. Calls show_banner_message_record() for
                 * the new data with the specified parameters.
                 *
                 */
                function update_banner_message_table() {
                    var formvalues = '&mode=banner_message_record';
                    formvalues += '&search=' + msg_search_terms;
                    var url = 'banner_message.php?'+formvalues;

                    $.get(url, {}, function(data) {
                        var record_display_string = "<?php echo _('Showing records %1-%2 of %3'); ?>";
                        var page_display_string = "<?php echo _('Page %1 of %2'); ?>";
                        report_sym = 0;
                        hide_throbber();

                        // Calculate the maximum number of pages
                        // Update the records count
                        var def_max_record = data['recordcount']; //Something to do with the data param
                        def_max_page = Math.ceil(def_max_record / def_per_page);
                        def_current_page = Math.max(1, Math.min(def_current_page, def_max_page));
                        var def_current_record = Math.min(def_max_record, (def_current_page-1) * def_per_page + 1);
                        var def_max_shown_record = Math.min(def_max_record, def_current_record + def_per_page - 1);

                        build_banner_message_table();

                        def_current_page = Math.min(def_current_page, def_max_page);
                        record_display_string = record_display_string.replace("%1", def_current_record).replace("%2", def_max_shown_record).replace("%3", def_max_record);
                        page_display_string = page_display_string.replace("%1", def_current_page).replace("%2", def_max_page);
                        $('#ajax_banner_message_paging').html(record_display_string);
                        $('.ajax_banner_message_page_total').html(page_display_string);
                    }, 'json');
                }
            </script>
            <!-- END Pagination Javascript -->

            <!-- START Confirmation Modal Javascript -->
            <script>
                var banner_message_multi_button_array = [];
                var num_banner_message_checkbox_checked = 0;
                var hidden_table_string = "<tr><th><?php echo _('Message'); ?>&nbsp;<?php echo _('Id'); ?></th><th><?php echo _("Message"); ?></th><th><?php echo _('Category'); ?></th><th><?php echo _("Severity"); ?></th></tr>";

                $(document).on('change','input[type=checkbox][class~="banner_message_checkbox"]',function(){
                       if ($(this).is(':checked')){
                            // Add to hidden form (for multibutton)
                            $("#banner_message_hidden_form" + this.value).val(this.value);
                            // Update the count displayed in the modal.
                            num_banner_message_checkbox_checked += 1;

                             // Add to the array.
                             banner_message_multi_button_array[this.value] = [$('#def_banner_message_id' + this.value).html(), $('#def_banner_message_message'+ this.value).html(), $('#def_banner_message_date'+ this.value).html()];
                         } else {
                            $("#banner_message_hidden_form" + this.value).val(-1);
                            num_banner_message_checkbox_checked -= 1;

                            delete banner_message_multi_button_array[this.value];
                        }
                 });

                /**
                 * Checks values of check boxes.
                 *
                 * Checks the values of the checkboxes and reports the amount of boxes checked. If they are checked
                 * it creates an array of data to be used in the bulk modification tool.
                 * 
                 */
                function banner_message_check_boxes() {
                    if ($("#banner_message_select_all").prop("checked")) { // true => we are checking the other boxes
                        $(".banner_message_checkbox").each(function() {
                            if ($(this).prop("checked") == false) {
                                num_banner_message_checkbox_checked += 1;
                            }
                        });
                        $( ".banner_message_checkbox" ).prop("checked", true);
                        $(".banner_message_checkbox").each(function() {
                            $("#banner_message_hidden_form" + this.value).val(this.value);
                            banner_message_multi_button_array[this.value] = [$('#def_banner_message_id' + this.value).html(),
                                                                $('#def_banner_message_message'+ this.value).html(),
                                                                $('#def_banner_message_date'+ this.value).html()];
                        });
                    } else { // false => we are unchecking them.
                        $(".banner_message_checkbox").each(function() {
                            if ($(this).prop("checked") == true) {
                                num_banner_message_checkbox_checked -= 1;
                            }
                            $("#banner_message_hidden_form" + this.value).val(-1);
                            delete banner_message_multi_button_array[this.value];

                        });
                        $( ".banner_message_checkbox" ).prop("checked", false);
                    }
                }

                $(document).ready(function() {
                    $('#show_banner_message_multi').click(function(event) {
                        if ($('#show_banner_message_multi_action').val() == 'deletemany') {
                            event.stopPropagation();
                            $("#confirm-rtd").fadeIn(200);
                            $('#confirm-rtd-type').html('Delete');
                        }
                        else if ($('#show_banner_message_multi_action').val() == 'enablemany') {
                            event.stopPropagation();
                            $("#confirm-rtd").fadeIn(200);
                            $('#confirm-rtd-type').html('Enable');
                        }
                        else if ($('#show_banner_message_multi_action').val() == 'disablemany') {
                            event.stopPropagation();
                            $("#confirm-rtd").fadeIn(200);
                            $('#confirm-rtd-type').html('Disable');
                        }

                        blackout();

                        $('#confirm-rtd-count').html(num_banner_message_checkbox_checked);
                        // Show remove confirmation
                        var rd_width = $('#confirm-rtd').outerWidth();
                        var width = $(window).width();
                        if (width > 1000) {
                            rd_width = width * 0.65;
                        } else {
                            rd_width = width - 200;
                        }
                        if (rd_width < 400) {
                            rd_width = width;
                        }
                        hidden_table_string = "<tr><th style='border-left: none;'><?php echo _('Message'); ?>&nbsp;<?php echo _('Id'); ?></th><th><?php echo _('Message'); ?></th><th style='border-right: none;'><?php echo _('Date'); ?>&nbsp;<?php echo _('Created'); ?></th></tr>";
                        banner_message_multi_button_array.forEach(function (item, index) {
                            hidden_table_string = hidden_table_string + "<tr><td>" + item[0] +"</td><td>" + item[1] +"</td><td>" + item[2] +"</td></tr>";
                        })
                        $("#rtd-table").html(hidden_table_string);

                        $('#confirm-rtd .list-box').data('max-width', rd_width-62);
                        $('#confirm-rtd').css('max-width', rd_width).center().css({position: "fixed", top: "35%" }).show();


                    });
                    $("#show_banner_message_multi_continue").on('click', function () {
                        // For now just hide the modal.
                        clear_blackout();
                        $('#confirm-rtd').fadeOut(200);

                        let url_route = $('#show_banner_message_multi_action').find(':selected').val();
                        id = [];

                        $('#banner_message_hidden_form').children().each(function (a, b) {
                            id.push($(this).val());
                            $(this).val(-1);
                        });

                        // Reset Confirmation modal counter and table.
                        banner_message_multi_button_array = [];
                        num_banner_message_checkbox_checked = 0;

                        $.post("<?php get_base_url();?> /nagiosxi/admin/banner_message-ajaxhelper.php", {
                            action: url_route,
                            id: id
                        }, function() {}, "json")
                        .done(function(response) {
                            flash_message(response.message, response.msg_type, true);
                            update_banner_message_table();
                            $('#banner_message_select_all').prop("checked", false);
                        })
                        .fail(function(xhr, error_string, error_thrown) {
                            flash_message("<?php echo _('Operation failed to alter messages. ')?>" + error_string, 'error');
                        });
                    });

                    $("#show_banner_message_multi_cancel").on('click', function () {
                        // For now just hide the modal.
                        $('#confirm-rtd').fadeOut(200);
                        clear_blackout();
                    });

                    $("#show-rtds").on('click', function () {
                        // Construct a string that represents the table via our global array.
                        if ($("#rtd-table-hideunhide").hasClass("hide")) {
                            $("#rtd-table-hideunhide").removeClass("hide");
                        }
                        else {
                            $("#rtd-table-hideunhide").addClass("hide");
                        }
                        $('#confirm-rtd').center().css({position: "fixed", top: "35%" });
                    });
                });
            </script>
            <!-- END Confirmation Modal Javascript -->

            <!-- BEGIN Multiple Banner Message Modification Modal -->

            <div id="confirm-rtd" class="xi-modal-banner_message hide">
                <h2> <?php echo _('Confirm: ') . "<span id ='confirm-rtd-type'></span>" . _(' Mesages'); ?></h2>
                <p><?php echo _('You have selected to modify multiple messages.'); ?></p>
                <div class="confirm-rtd-table">
                    <table class="table table-striped table-condensed table-bordered table-no-margin">
                        <thead>
                            <tr>
                                <th style="border-bottom-width: 1px;">
                                    <div class="fl"><span id="confirm-rtd-count">0</span> <?php echo _('messages selected'); ?></div>
                                    <div class="fr"><a id="show-rtds"><?php echo _('Show selected'); ?></a></div>
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <div class="list-box hide rtd-content-table" id="rtd-table-hideunhide" data-max-width="">
                        <table class="table table-striped table-hover table-bordered table-condensed table-no-margin" style="border: 0;">
                            <tbody class="list" id="rtd-table">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div style="padding-top: 20px;">
                    <button type="button" id="show_banner_message_multi_continue" data-loading-text="<?php echo _('Removing...'); ?>" class="btn-confirm btn btn-sm btn-primary"><?php echo _('Continue'); ?></button>
                    <button type="button" id="show_banner_message_multi_cancel" class="btn-cancel btn btn-sm btn-default"><?php echo _('Cancel'); ?></button>
                </div>
            </div>

            <!-- END Multiple Banner Message Modification Modal -->
                </div>
            </div>
        </div>
    </div>
    <!-- End the page -->
    <?php do_page_end(true); 
}
