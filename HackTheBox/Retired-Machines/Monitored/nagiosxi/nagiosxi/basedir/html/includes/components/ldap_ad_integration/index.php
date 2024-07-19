<?php
//
// LDAP / Active Directory Integration
// Copyright (c) 2014-2022 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__).'/../../common.inc.php');
require_once(dirname(__FILE__).'/../componenthelper.inc.php');

require_once(dirname(__FILE__).'/adLDAP/src/adLDAP.php');
include_once(dirname(__FILE__).'/ldap_ad_integration.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables, check prereqs and authentication
grab_request_vars();
check_prereqs();
check_authentication(false);

// Only admins can access this page
if (is_admin() == false) {
    echo _("You do not have access to this section.");
    exit();
}

route_request();

function route_request()
{
    global $request;

    $cmd = grab_request_var('cmd', 'default');
    $users = grab_request_var('users', '');
    $action = grab_request_var('action', '');
    $update = grab_request_var('update', '');
    $back = grab_request_var("back", '');

    switch($cmd)
    {        
        case "landing_page":
            do_page_start(array("page_title" => _("LDAP / Active Directory Users")), true);
            $_SESSION['import_ldap_ad_username'] = grab_request_var("username", "");
            $_SESSION['import_ldap_ad_password'] = grab_request_var("password", "");
            
            $server_id = grab_request_var("server_id", "");

            if (empty($server_id)) {
                echo '<h1>'._("LDAP / Active Directory Import Users").'</h1>';
                collect_credentials();
                exit();
            }

            // Verify server type
            $servers_raw = get_option("ldap_ad_integration_component_servers");
            if (empty($servers_raw)) { $servers = array(); } else {
                $servers = unserialize(base64_decode($servers_raw));
            }

            $_SESSION['import_ldap_ad_server_id'] = $server_id;
            foreach ($servers as $server) {
                if ($server['id'] == $server_id) {
                    $_SESSION['import_ldap_ad_server'] = $server;
                }
            }

            landing_page();
            break;

        case "cancel":
            do_page_start(array("page_title" => _("LDAP / Active Directory Users")), true);
            landing_page();
            break;

        case "return_items":
            return_items();
            break;

        case "navigate":
            $select = grab_request_var('ad_object', "");
            $direction = grab_request_var("direction", "");
            $obj_array = parse_object($select);
            $folder = grab_array_var($obj_array, "1", array());
            $_SESSION["current_object_type"] = grab_array_var($obj_array, "0", "con");
            navigate($direction,$folder);
            break;

        case "import":
            $objs = grab_request_var("objs", "");
            $users = explode('|', $objs);
            show_user_options($users, false);
            break;

        case "finish":
            $users = grab_request_var("users", array());
            $preferences = grab_request_var("preferences", array());
            $security = grab_request_var("security", array());
            do_page_start(array("page_title" => _("LDAP / Active Directory Users")), true);
            create_nagios_users($users, $preferences, $security);
            break;

        case "get_location":
            get_location();
            break;

        case "parse_object":
            parse_object();
            break;

        case "grab_user_list":
            grab_user_list();
            break;

        case "display_select_list":
            display_select_list();
            break;

        case "display_nav_window":
            $target_path = grab_request_var("target_path", "");
            $type = grab_request_var("object_type", "");
            $new_list = grab_request_var("new_list", "0");

            $folder = json_decode($target_path);

            list($errno, $array_to_enum) = grab_ad_folders($folder, $type);
            display_nav_window($array_to_enum, $new_list);
            break;
        
        case "display_users":
            $target_path = grab_request_var("target_path", "");
            $type = grab_request_var("object_type", "container");
            $selected = grab_request_var("selected", "");
            $search = grab_request_var('search', '');

            $folder = json_decode($target_path);
            $selected = json_decode($selected);

            list($errno, $array_to_enum) = grab_ad_folders($folder, $type, $search);

            // Print error for page limit exceeded (normall 1000 users)
            if ($errno == 4) {
                echo '<div class="alert alert-info" style="margin: 0 0 20px 20px;">'.sprintf(_("The page limit (%d users) was exceeded. The PHP version on this system may not support paging."), count($array_to_enum)-1).'<br>'.sprintf(_("You can search a name and use wildcard %s. Searching for all users with first letter: %s"), "<code>*</code>", "<code>a*</code>, <code>b*</code>, ...").'</div>';
            }

            display_users($array_to_enum, $folder, $type, $selected, $search);
            break;

        default:
            do_page_start(array("page_title" => _("LDAP / Active Directory Import Users")), true);
            echo '<h1>'._("LDAP / Active Directory Import Users").'</h1>';
            collect_credentials();
    }
}

/**
 * Display the current Active Directory settings when there is an error so user can see what might
 * be wrong and change it.
 */
function error_page()
{
    $servers_raw = get_option("ldap_ad_integration_component_servers");
    if (empty($servers_raw)) { $servers = array(); } else {
        $servers = unserialize(base64_decode($servers_raw));
    }
}

/**
 * The main page that we go to when we open the Active Directory Users page
 */
function landing_page()
{
    global $ad_error;

    echo '<h1>'._("LDAP / Active Directory Import Users").'</h1>';
    
    $adldap = create_obj();
    
    if ($adldap == false) {
        $msg = '<strong>'._("Unable to authenticate:").'</strong> '.$ad_error;
        display_message(true, false, $msg);
    }

    if ($adldap == false) {
        collect_credentials();
        error_page();
        exit();
    }
    ?>

    <style type="text/css">
    .table-icon { vertical-align: bottom; }
    input[type="checkbox"].ad-checkbox { vertical-align: middle; margin-right: 5px; }
    .ad-list { list-style-type: none; margin: 0; padding: 0; }
    .folder-list { background-color: #F9F9F9; height: 100%; overflow-x: auto; }
    .user-list { margin: 20px; height: calc(100% - 80px); position: relative; overflow-y: auto; }
    .user-list li { }
    .user-list li label { cursor: pointer; }
    .sub-list li span { padding-left: 20px; }
    .ad-folder { padding: 5px 10px; display: block; margin: 0 0 1px 0; }
    .ad-folder:hover { cursor: pointer; background-color: #E9E9E9; }
    .ad-folder.active { background-color: #E9E9E9; }
    .import-button { margin-top: 20px; }
    #selected-users { margin-bottom: 15px; }
    #selected-users .num-users, #selected-users .users { font-weight: bold; }
    .user-dn { padding-left: 40px; font-family: 'Consolas', "Courier New", Courier, monospace; margin-bottom: 6px; }
    .user-toggle-show-dn { font-size: 11px; vertical-align: middle; cursor: pointer; margin-left: 2px; }
    .ad-ldap-container { width: 32%; min-width: 200px; }
    .btn > label, .btn > label > input { cursor: pointer; }
    </style>
    
    <p><?php echo _("Select the users you would like to give access to Nagios XI via LDAP/AD authentication. You will be able to set user-specific permissions on the next page."); ?></p>
    <h4><?php echo _("Select Users to Import from LDAP/AD"); ?></h4>

    <div id="selected-users">
        <span class="num-users">0</span> <?php echo _("users selected for import"); ?><span class="users"></span>
    </div>

    <div style="display: table; min-height: 100px; height: calc(100vh - 215px); border: 1px solid #CCC; width: 55%;">
        <div id="root" class="ad-ldap-container"></div>
        <div id="view" style="min-width: 400px; display: table-cell; vertical-align: top; ">
            <ul class="ad-list user-list">
                <li>&nbsp;</li>
            </ul>
        </div>
    </div>

    <div class="import-button">
        <form action="?cmd=import" method="post" style="margin: 0;">
            <input type="hidden" value="" name="objs" id="objs">
            <button class="btn btn-sm btn-primary" type="submit" id="select-users"><?php echo _("Add Selected Users"); ?></button>
        </form>
    </div>
    
    <script language="javascript" type="text/javascript">
    // Store the selected users for multiple requests
    var SELECTED_USERS = [];
    var SELECTED_USERNAMES = [];

    $(document).ready(function() {

        // Get the default root folders
        ad_generate_root();

        // When clicking on a folder we actually show the users/folders
        $('#root').on('click', '.ad-folder', function(e) {
            if (!$(this).parents('ul').hasClass('sub-list') && !$(this).hasClass('active') && $(this).parent().has('ul').length == 0) {
                $('.sub-list').remove();
            }

            $('.ad-folder').removeClass('active');
            $(this).addClass('active');

            var path = $(this).data('path');
            var type = $(this).data('type');
            grab_ad_obj('view', type, path, this);
        });

        // Show all users selected
        $('body').on('click', '.show-all-selected', function() {

            var content = '<div style="max-width: 400px; max-height: 500px; overflow: auto;">'+SELECTED_USERNAMES.join('<br>')+'</div>';
            set_child_popup_content(content);
            whiteout();
            display_child_popup();

        });

        // Select a user
        $('#view').on('change', '.ad-checkbox', function(e) {
            if ($(this).is(":checked")) {
                if (SELECTED_USERS.indexOf($(this).val()) == -1) {
                    SELECTED_USERS.push($(this).val());
                }
                if (SELECTED_USERNAMES.indexOf($(this).data('username')) == -1) {
                    SELECTED_USERNAMES.push($(this).data('username'));
                }
            } else {
                // Remove user from the list if we are un-checking it
                var i = SELECTED_USERS.indexOf($(this).val());
                if (i > -1) { SELECTED_USERS.splice(i, 1); }
                var i = SELECTED_USERNAMES.indexOf($(this).data('username'));
                if (i > -1) { SELECTED_USERNAMES.splice(i, 1); }
            }

            // Update user count at bottom of page
            var num = SELECTED_USERNAMES.length;
            $('#selected-users .num-users').html(num);
            var html = "";
            if (num > 10) {
                html = ": "+SELECTED_USERNAMES.slice(0, 10).join(', ');
                html += ', ... <a class="show-all-selected"><?php echo _('Show all selected users'); ?></a>';
            } else if (num > 0) {
                html = ": "+SELECTED_USERNAMES.join(', ');
            }
            $('#selected-users .users').html(html);

            $('#objs').val(SELECTED_USERS.join('|'));
        });

        $("#view").on('click', '.user-toggle-show-dn', function(e) {
            var userdn = $(this).parents('li').find('.user-dn');
            if (userdn.css("display") == "none") {
                userdn.show();
                $(this).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            } else {
                userdn.hide();
                $(this).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
        });

        $("#view").on('click', '.toggle-users', function(e) {
            var text = $(this).parents('label').find('span');
            if ($(this).is(':checked')) {
                $('.ad-checkbox:not(:disabled)').prop('checked', true).trigger('change');
                text.html('<?php echo _("Select None"); ?>');
            } else {
                $('.ad-checkbox:not(:disabled)').prop('checked', false).trigger('change');
                text.html('<?php echo _("Select All"); ?>');
            }
        });

        $('#select-users').click(function() {
            if ($('#objs').val() == '') {
                alert("<?php echo _('You must select at least one users to import.'); ?>");
                return false;
            }
        });

        $('body').on('keypress', '#ad-search', function(e) {
            if (e.keyCode == 13) {
                do_ad_search();
            }
        });

        $('body').on('click', '#ad-search-btn', function(e) {
            do_ad_search();
        });

        $('body').on('click', '#ad-search-clear-btn', function(e) {
            $('#ad-search').val('');
            do_ad_search();
        });

    });

    function do_ad_search()
    {
        var path = $('.ad-folder.active').data('path');
        var json_path = JSON.stringify(path);
        var type = $('.ad-folder.active').data('type');
        grab_ad_users('#view', type, json_path);
    }
            
    function grab_ad_obj(target_form, type, path, folder)
    {
        already_loaded = $(folder).parent().has('ul').length;
        var json_path = JSON.stringify(path);
        var target_form = "#" + target_form;

        if (already_loaded == 0) {
            $.ajax({
                type: "POST",
                url: "index.php",
                data: { cmd: "display_nav_window", object_type: type, target_path: json_path },
                success: function(response) {
                    if (response != "") {
                        // Check the level of the folder and add padding if necessary
                        var sub = $(folder).parents('ul').length;
                        var x = sub * 20;
                        $(folder).parent().append(response);
                        $(folder).parent().find('ul').find('span.ad-folder').css('padding-left', x+'px');
                    }
                    grab_ad_users(target_form, type, json_path);
                },
                error: function(response) { console.log("Error: Unable to connect to LDAP server."); }
            });
        } else {
            grab_ad_users(target_form, type, json_path);
        }
    }

    function ad_generate_root() {
        var target = "#root";
        var type = "container";
        $.ajax({
            type: "POST",
            url: "index.php",
            data: { cmd: "display_nav_window", object_type: type, target_path: "", new_list: "1" },
            success: function(response) {
                $(target).html(response);
                var path = '/';
                var type = 'organizationalUnit';
                grab_ad_obj('view', type, path, this);
            },
            error: function(response) { console.log("Error: Unable to connect to LDAP server."); }
        });
    }

    function grab_ad_users(target_form, type, json_path) {
        users_loading();
        $.ajax({
            type: "POST",
            url: "index.php",
            data: { cmd: "display_users", object_type: type, target_path: json_path, search: $('#ad-search').val(), selected: JSON.stringify(SELECTED_USERS) },
            success: function(response) {
                $(target_form).html(response);
                clear_users_loading();
            },
            error: function(response) { console.log("Error: Unable to connect to LDAP server."); }
        });
    }
    
    function toggle_boxes(element) {
        $(element.parentNode).children().attr("checked", "true");
        toggle_user_add();
    }

    function users_loading() {
        var view = $('#view');
        var pos = view.position();
        $('#whiteout').css({'width': $('#view').outerWidth()+'px',
                            'height': $('#view').outerHeight()+'px',
                            'position': 'absolute',
                            'top': pos.top,
                            'left': pos.left
                            }).show();
        show_throbber();
        var th = $('#throbber');
        th.css({
            "position": "absolute",
            "top": (((view.height() - th.outerHeight()) / 2) + pos.top + "px"),
            "left": (((view.width() - th.outerWidth()) / 2) + pos.left + "px")
        });
    }

    function clear_users_loading() {
        $('#whiteout').hide();
        hide_throbber();
    }
    </script>
<?php
}

function collect_credentials()
{
    // Grab the posted cerdentials to show again if there was an error
    $username = grab_request_var('username', '');
    $password = grab_request_var('password', '');
    $server_id = grab_request_var('server_id', 0);

    // Get top level container
    $servers_raw = get_option("ldap_ad_integration_component_servers");
    if ($servers_raw == "") { $servers = array(); } else {
        $servers = unserialize(base64_decode($servers_raw));
    }

    $output = '
    <p>'._("Log into your LDAP / Active Directory administrator or privileged account to be able to import users.").'</p>
    <form method="post" style="margin: 30px 0;">
        <div style="margin-bottom: 10px;">
            <input type="text" size="25" placeholder="Username" value="'.encode_form_val($username).'" name="username" id="ad_username" class="textfield form-control">
        </div>
        <div style="margin-bottom: 10px;">
            <input type="password" placeholder="Password" size="25" value="'.encode_form_val($password).'" name="password" id="ad_password" class="textfield form-control">
        </div>
        <div style="margin-bottom: 10px;">
            <select name="server_id" class="form-control">';

            foreach ($servers as $server) {
                if ($server['conn_method'] == "ad") {
                    $display_servers = $server['ad_domain_controllers'];
                } else {
                    $display_servers = $server['ldap_host'];
                }

                // Check if server was selected
                $selected = '';
                if ($server_id == $server['id']) { $selected = ' selected'; }
                
                $output .= '<option value="'.$server['id'].'"'.$selected.'>'.ldap_ad_display_type($server['conn_method']).' - '. encode_form_val($display_servers).'</option>';
            }

    $output .= '</select>
        </div>
        <input type="hidden" name="cmd" value="landing_page">
        <button type="submit" class="btn btn-sm btn-primary" name="next">'._("Next").' <i class="fa fa-chevron-right r"></i></button>
    </form>
    <a href="'.get_component_url_base("ldap_ad_integration").'/manage.php">'._("Manage Authentication Servers").'</a>';

    echo $output;
}

function is_computer($ad_array) {
    $comp = grab_array_var($ad_array["objectclass"], "4", "");
    if ($comp == "computer") {
        return true;
    }
    return false;
}

function is_user($ad_obj) {
    if ($_SESSION['import_ldap_ad_server']['conn_method'] == "ad") {
        $comp = grab_array_var($ad_obj["objectclass"], "4", "");
        $person = grab_array_var($ad_obj["objectclass"], "1", "");
        if (empty($comp) && $person == "person") {
            return true;
        }
    } else if ($_SESSION['import_ldap_ad_server']['conn_method'] == "ldap") {
        $person = strtolower(grab_array_var($ad_obj["objectclass"], "0", ""));
        $types = array('inetorgperson', 'account', 'person', 'organizationalperson', 'shadowaccount', 'posixaccount');
        if (in_array($person, $types)) {
            return true;
        }
    }
    return false;
}

function parse_object($select) {
    $prefix_length = 3;
    $prefix = substr($select, 0, $prefix_length);
    $postfix = substr($select, 3);
    return array ($prefix, $postfix);
}

function grab_ad_folders($folder="", $type="container", $search="")
{
    $ldap_obj = create_obj();

    if ($type == "organizationalUnit") {
        if ($ldap_obj->type == "ad") {
            $list_array = $ldap_obj->folder()->listing($folder, adLDAP::ADLDAP_FOLDER, false, null, $search);
        } else {
            $list_array = $ldap_obj->folder_listing($folder, basicLDAP::LDAP_FOLDER, $search);
        }
        $errno = $ldap_obj->getLastErrno();
        return array($errno, check_validity($list_array));
    } else if ($type == "container" || $type == "nsContainer") {
        if ($ldap_obj->type == "ad") {
            $list_array = $ldap_obj->folder()->listing($folder, adLDAP::ADLDAP_CONTAINER, false, null, $search);
        } else {
            $list_array = $ldap_obj->folder_listing($folder, basicLDAP::LDAP_CONTAINER, $search);
        }
        $errno = $ldap_obj->getLastErrno();
        return array($errno, check_validity($list_array));
    } else if ($type == "group") {
        $folder = grab_array_var($folder, "0");
        $ad_array = $ldap_obj->group()->members($folder, null, $search);
        $errno = $ldap_obj->getLastErrno();
        return array($errno, $ad_array);
    }

    return array(0, false);
}

function check_validity($ad_array) {
    $count = grab_array_var($ad_array, "count", 0);
    if (!($count == 0)) {
        return $ad_array;
    } else {
        return false;
    }
}

function grab_type($obj) {
    $item = grab_array_var($obj, "objectclass", "");
    if (!empty($item)) {
        $type = grab_array_var($item, "1", "");
        if (empty($type)) {
            $type = grab_array_var($item, "0", "");
        }
        return $type;
    }
}

function grab_user_name($type, $obj) {
    if ($type == "person") {
        $item = grab_array_var($obj, "samaccountname");
        if (!empty($item)) {
            return grab_array_var($item, "0", "");
        }
    } else if ($type == "inetOrgPerson") {
        $item = grab_array_var($obj, "uid");
        if (!empty($item)) {
            return grab_array_var($item, "0", "");
        }
    }
}

function grab_dn($obj) {
    $item = grab_array_var($obj, "dn");
    $item = str_replace(array('\,', '\2C'), '&#44;', $item);
    if (!($item == "")) {
        $dn = explode(",", $item);
        $value = explode("=", grab_array_var($dn, "0"));
        return grab_array_var($value, "1");
    }
}

function grab_full_dn($obj) {
    return grab_array_var($obj, "dn", grab_dn($obj));
}

function grab_path($obj) {
    $item = grab_array_var($obj, "dn", "");
    $item = str_replace(array('\,', '\2C'), '&#44;', $item);
    $path = array();
    
    if (!($item == "")) {
        $fully_qualified = explode(",", $item);
        foreach ($fully_qualified as $branch) {
            $value = explode("=", $branch);
            $id = grab_array_var($value, "0");
            if (strtoupper($id) == "OU" || strtoupper($id) == "CN") {
                $ou_location = grab_array_var($value, "1");
                if (!($ou_location == "")){
                    array_push($path, $ou_location);
                }
            }
        }
    }
    return $path;
}


function strip_dc($val_arr) {
    $return_arr = array();
    foreach ($val_arr as $val) {
        $pair = explode("=", $val);
        $type = grab_array_var($pair, "0", "");
        if (!(strtoupper($type) == "DC" || $type == "")) {
            $key = grab_array_var($pair, "1", "");
            array_push($return_arr, $key);
        }
    }
    return $return_arr;
}

function grab_root($obj) {
    $dn = grab_array_var($obj, "dn", "");
    $dn = str_replace(array('\,', '\2C'), '&#44;', $dn);
    $location = "";
    if (!($dn == "")) {
        $val = explode(",", $dn);
        $v = array_shift($val);
        $ad_obj = strip_dc($val);
        $container_name = grab_array_var($ad_obj, "0", "");
        return $container_name;
    }
    return $container_name;
}

function grab_sam($obj) {
    $sam = grab_array_var($obj, "samaccountname", "");
    $sam_account_name = "Unknown";
    if (!empty($sam)) {
        $sam_account_name = grab_array_var($sam, "0", "Unknown");
    }
    return $sam_account_name;
}

function display_nav_window($array_to_enum, $new_list=0)
{
    // Hide some folders that shouldn't be shown because they are very VERY unlikely to have users in them
    // unless someone likes putting their users in strange places...
    $dont_show = array("System", "Program Data", "ForeignSecurityPrincipals", "Managed Service Accounts");

    if ($new_list) {
        echo '<ul class="ad-list folder-list">';
    } else {
        echo '<ul class="ad-list sub-list">';
    }

    if (!($array_to_enum == false)) {
        foreach ($array_to_enum as $obj) {
            if (is_array($obj)) {
                $path = json_encode(grab_path($obj));
                $dn = grab_dn($obj);
                $type = grab_type($obj);
                $stype = strtolower($type);

                # Types of navigational structures (all lowercase)
                $containers = array('organizationalunit', 'container', 'nscontainer', 'group');

                if (in_array($stype, $containers)) {
                    
                    // Skip if the object is something we don't need to display
                    if (in_array($dn, $dont_show)) {
                        continue;
                    }

                    if ($stype == "group") { $image = "group.png"; }
                    if ($stype == "container" || $type == "nsContainer") { $image = "folder.png"; }
                    if ($stype == "organizationalunit") { $image = "folder_page.png"; }
                    ?>
                    <li>
                        <span class="ad-folder" data-path='<?php echo $path; ?>' data-type="<?php echo $type; ?>">
                            <img class="table-icon" src="<?php echo theme_image($image); ?>">
                            <?php echo $dn; ?>
                        </span>
                    </li>
                    <?php
                }
            }
        }
    }
    echo '</ul>';
}

function return_image($obj)
{
    if (is_user($obj)) {
        $image = "user.png";
    }
    if (is_computer($obj)) {
        $image = "monitor.png";
    }
    return $image;
}

function display_users($array_to_enum, $location, $type, $selected, $search="")
{
    // List of usernames not to show...
    $dont_show = array("krbtgt");

    $location = grab_array_var($location, "0");
    $ldapad_obj = create_obj();
    $person_exists = false;
    $all_checked = true;

    $strtype = _(strtolower($type));
    if ($strtype == "organizationalunit") {
        $strtype = _("folder");
    }

    $list_html .= '';
    if (!empty($array_to_enum)) {
        $list_html .= '<ul class="ad-list user-list">';
        if ($type == "group" || $type == "Group") {
            foreach ($array_to_enum as $username)
            {
                $person_exists = true;

                if ($ldapad_obj->type == "ad") {

                    $userinfo = $ldapad_obj->user()->info($username, array("displayname"));
                    $displayname = $userinfo[0]["displayname"][0];
                    $dn = $userinfo[0]['dn'];
                    $obj = $username;

                } else if ($ldapad_obj->type == "ldap") {
                    // Add LDAP groups someday...
                }

                if (in_array($obj, $selected)) {
                    $checked = "checked";
                } else {
                    $checked = '';
                    $all_checked = false;
                }

                $list_html .= '
                <li>
                    <label style="font-weight: normal; line-height: 20px;">
                        <input type="checkbox" class="ad-checkbox" data-username="'.$username.'" value="'.$obj.'" '.$checked.'>
                        <img class="table-icon" src="'.theme_image("user.png").'" border="0" alt="'._("Add New User").'" title="'._("Add New User").'" style="">
                        '.$displayname.' ('.$username.')
                    </label>
                    <i class="fa fa-plus-square-o user-toggle-show-dn" title="'._("Show full DN (destinguished name)").'"></i>
                    <div class="user-dn hide">DN: '.$dn.'</div>
                </li>';
            }
        } else {
            foreach ($array_to_enum as $obj)
            {

                if (is_array($obj)) {

                    $type = grab_type($obj);
                    $stype = strtolower($type);

                    # List of types of users/person units (all lowercase)
                    $units = array('person', 'account', 'inetorgperson', 'organizationalperson', 'shadowaccount', 'posixaccount');

                    if (in_array($stype, $units)) {
                        $username = grab_user_name($type, $obj);
                        $dn = grab_full_dn($obj);

                        if (in_array($username, $dont_show)) {
                            continue;
                        }

                        if ($ldapad_obj->type == "ad") {
                            $o = $username;
                        } else if ($ldapad_obj->type == "ldap") {
                            $o = $dn;
                        }

                        $image = return_image($obj);
                        $person_exists = true;

                        $display_name = '';
                        if (!empty($username)) {
                            $display_name = '('.$username.')';
                        }

                        $display_image = '';
                        if (!empty($image)) {
                            $display_image = '<img class="table-icon" src="'.theme_image($image).'" border="0" alt="'._("User").'" title="'._("User").'" style="">';
                        }

                        $opts = '';
                        if (is_computer($obj)) {
                            $opts .= ' disabled';
                        }
                        if (in_array($o, $selected)) {
                            $opts .= ' checked';
                        } else {
                            $all_checked = false;
                        }

                        $list_html .= '
                        <li>
                            <label style="font-weight: normal; line-height: 20px;">
                                <input type="checkbox" class="ad-checkbox" style="margin: 0 5px 0 0; vertical-align: middle;" data-username="'.$username.'" value="'.$o.'"'.$opts.'>
                                '.$display_image.'
                                '.grab_dn($obj).' '.$display_name.'
                            </label>
                            <i class="fa fa-plus-square-o user-toggle-show-dn" title="'._("Show full DN (destinguished name)").'"></i>
                            <div class="user-dn hide">DN: '.$dn.'</div>
                        </li>';
                    }
                }
            }
        }
        $list_html .= '<ul>';
    }

    ?>

    <div style="margin: 20px 0 0 20px" class="form-inline">
        <div class="fl">
            <input type="text" id="ad-search" style="width: 240px;" class="form-control" value="<?php echo encode_form_val($search); ?>" placeholder="<?php echo sprintf(_("Search this %s ..."), $strtype); ?>">
            <button id="ad-search-btn" class="btn btn-sm btn-default" ><i class="fa fa-search"></i></button>
            <?php if (!empty($search)) { ?>
            <button id="ad-search-clear-btn" class="btn btn-sm btn-default"><i class="fa fa-times"></i></button>
            <?php } ?>
        </div>
        <div class="fr">
            <?php if ($person_exists) { ?>
            <div class="btn btn-sm btn-default" style="margin-right: 20px;">
                <label style="font-weight: normal;">
                    <input type="checkbox" class="toggle-users" style="margin: 0 5px 0 0; vertical-align: middle;" <?php if ($all_checked) { echo "checked"; } ?>>
                    <span><?php if ($all_checked) { echo _("Select None"); } else { echo _("Select All"); } ?></span>
                </label>
            </div>
            <?php } ?>
        </div>
        <div class="clear"></div>
    </div>

    <?php
    // If there are no users or objects, display error message
    if (!$person_exists) {
        $extra = '';
        if (!empty($search)) {
            $extra = ' '._("You may use * wildcard in searches.");
        }
        echo '<ul class="ad-list user-list">';
        echo '<li>'._("No users or computers found.").$extra.'</li>';
        echo "</ul>";
    } else {
        echo $list_html;
    }
}

                    
function submit_forms() {
    ?>
    <script language="javascript" type="text/javascript">
    function count(){
        var users_to_add = new Array();
        $( "input:checked" ).each(function () {
            users_to_add.push( $(this).val() );
            })
        var rapture = JSON.stringify(users_to_add);
        window.location.replace("index.php?cmd=user_add&user_additions=" + rapture);
        };
    </script>
    <?php
}


function return_item_type($item) {
    $objectclass = grab_array_var($item, "objectclass");
    $type = $objectclass[1];
    return $type;
}

function return_dn($item) {
    $dn = grab_array_var($item, "distinguishedname");
    $count = grab_array_var($dn, "count");

    if ($count == 1) {
        $this_dn = $dn[0];
        $record_arr = explode(',', $this_dn);
        $cn = $record_arr[0];
        list($name, $val) = split('=', $cn);
    }
    return $val;
}

function create_profile_array($objs)
{
    $ldapad_obj = create_obj();
    $store = array();
    
    foreach ($objs as $obj) {
        if ($ldapad_obj->type == "ad") {
            $userinfo = $ldapad_obj->user()->info($obj, array("mail", "displayname"));
            $username = $obj;
            $email = grab_email($userinfo);
            $display_name = grab_display($userinfo);
            $dn = $userinfo[0]['dn'];
        } else if ($ldapad_obj->type == "ldap") {
            $userinfo = $ldapad_obj->user_info($obj);
            $username = $userinfo[0]['uid'][0];
            $email = grab_email($userinfo);
            $display_name = $userinfo[0]['cn'][0];
            $dn = $userinfo[0]['dn'];
        }

        $u = array(
            "username" => $username,
            "email" => $email,
            "displayname" => $display_name,
            "dn" => $dn
        );
        array_push($store, $u);
    }
    
    return $store;
}

function grab_email($the_user) {
    $arr = grab_array_var($the_user, "0", "");
    $mail = grab_array_var($arr, "mail", "");
    $email = grab_array_var($mail, "0", "");
    return $email;
}

function grab_display($the_user) {
    $display = grab_array_var($the_user[0]["displayname"], "0", "unknown");
    return $display;
}

function show_user_options($users, $revise)
{
    
    if ($revise == false) {
        $users = create_profile_array($users);
    }
    
    // By default we add a new user
    $add = true;
    $user_id = 0;

    // Get languages
    $languages = get_languages();
    $authlevels = get_authlevels();
    $number_formats = get_number_formats();
    $date_formats = get_date_formats();

    // Defaults
    $date_format = DF_ISO8601;
    $number_format = NF_2;
    $email = "";
    $username = "";
    $name = "";
    $level = "user";
    $language = get_option("default_language");
    $theme = get_option("default_theme");
    $create_contact = 1;
    $authorized_for_all_objects = 0;
    $authorized_to_configure_objects = 0;
    $authorized_for_all_object_commands = 0;
    $authorized_for_monitoring_system = 0;
    $advanced_user = 0;
    $readonly_user = 0;
    $api_enabled = 0;
    $ccm_access = 0;
    $ccm_access_list = array();

    do_page_start(array("page_title" => _("LDAP / Active Directory Import Users")), true);
    echo '<h1>'._("LDAP / Active Directory Import Users").'</h1>';
    ?>

    <p><?php echo _('Set the preferences and security settings for all users that will be imported. You can also edit multiple user\'s preferences/security settings at once by checking the users you want to edit and selecting the action from the dropdown.'); ?></p>

    <script type="text/javascript">

    $(document).ready(function() {

        function generate_popup(eid, etype) {
            show_throbber();
               
            if (etype == "preferences") {
                edit_title = "<?php echo encode_form_val(_('Preferences')); ?>";
                specific_content = '<div id="popup_data" style="width: 500px;"><p>'+$('.preferences-form').clone().html()+'</p>';
            } else if (etype == "security") {
                edit_title = "<?php echo encode_form_val(_('Security Settings')); ?>";
                specific_content = '<div id="popup_data" data-eid="'+eid+'" style="width: 500px;"><p>'+$('.security-form').clone().html()+'</p>';
            }

            var content = '<div id="popup_header"><b>'+edit_title+'</b></div>';
            content += specific_content;
            content += '<input type="hidden" value="'+eid+'" id="eid"><input type="hidden" value="'+etype+'" id="etype"><button type="button" class="e-save btn btn-sm btn-primary">' + "<?php echo encode_form_val(_('Save')); ?>" + '</button> <button type="button" class="e-cancel btn btn-sm btn-default">' + "<?php echo encode_form_val(_('Cancel')); ?>" + '</button></div>';

            hide_throbber();
            set_child_popup_content(content);

            if (etype == "security") {
                $('#popup_data #ccm_access').change(function() {
                    if ($(this).val() == 2) {
                        $('#popup_data .ccm-limited-access-settings').show();
                    } else {
                        $('#popup_data .ccm-limited-access-settings').hide();
                    }
                    $("#child_popup_layer").center();
                });
            }
        }

        // Edit user(s) preferences
        $('.edit').click(function () {
            var etype = $(this).data('type');
            var eid = $(this).parents('tr').attr('id');

            generate_popup(eid, etype);

            // Set the popup content
            if ($(this).parents('td').find('i').hasClass('fa-check-circle')) {
                if (etype == "preferences") {

                    var create_contact = true;
                    if ($('#'+eid+' .create_contact').val() == 0) { create_contact = false; }

                    $('#popup_data .create_contact').attr('checked', create_contact);
                    $('#popup_data .language').val($('#'+eid+' .language').val());
                    $('#popup_data .theme').val($('#'+eid+' .theme').val());
                    $('#popup_data .number_format').val($('#'+eid+' .number_format').val());
                    $('#popup_data .date_format').val($('#'+eid+' .date_format').val());

                } else if (etype == "security") {

                    $('#popup_data .auth_level').val($('#'+eid+' .auth_level').val()).trigger('change');
                    $('#popup_data .ccm_access').val($('#'+eid+' .ccm_access').val()).trigger('change');

                    // Load all the ccm access values (if limited)
                    $('#popup_data .ccm_access_list').attr('checked', false);
                    $('#'+eid+' .ccm_access_list').each(function(k, v) {
                        if ($(v).is(":checked")) {
                            $('#popup_data .ccm_access_list.'+$(v).val()).attr('checked', true);
                        }
                    });

                    update_popup_security_checkboxes(eid);

                }
            }

            display_child_popup();
            whiteout();
        });

        $('body').on('click', '.toggle-all', function() {
            $('#popup_data .ccm_access_list').attr('checked', true);
        });

        $('body').on('click', '.toggle-none', function() {
            $('#popup_data .ccm_access_list').attr('checked', false);
        });

        $('body').on('change', '.auth_level', function() {
            if ($(this).val() == "255") {
                $('#popup_data .user-setting').hide();
                $('#popup_data .ccm-limited-access-settings').hide();
                $('#popup_data input').each(function(k, v) {
                    $(v).attr('disabled', true);
                });
                $('#popup_data input[name="api_enabled"]').attr('disabled', false);
            } else {
                $('#popup_data .user-setting').show();
                $('#popup_data input').each(function(k, v) {
                    $(v).attr('disabled', false);
                });
                // Set all the values...
                update_popup_security_checkboxes($('#popup_data').data('eid'));
            }
            $("#child_popup_layer").center();
        });

        $('body').on('click', '.e-cancel', function() {
            close_child_popup();
            clear_whiteout();
            $('.edit-action').val('');
        });

        $('body').on('click', '.e-save', function() {
            var eid = $('#eid').val();
            var etype = $('#etype').val();

            // Update the preferences icon
            if (eid == -1) {
                $('.user-select:checked').each(function(k, v) {
                    var x = $(v).parents('tr').attr('id');
                    $('#'+x+' .'+etype+'-icon').removeClass('fa-circle-o').addClass('fa-check-circle');
                });
            } else {
                $('#'+eid+' .'+etype+'-icon').removeClass('fa-circle-o').addClass('fa-check-circle');
            }

            // Update items in the input fields
            if (etype == "preferences") {
                var create_contact = 0;
                if ($('#popup_data input.create_contact').is(":checked")) { create_contact = 1; }

                if (eid == -1) {
                    $('.user-select:checked').each(function(k, v) {
                        var x = $(v).parents('tr').attr('id');
                        $('#'+x+' .create_contact').val(create_contact);
                        $('#'+x+' .language').val($('#popup_data select.language').val());
                        $('#'+x+' .theme').val($('#popup_data select.theme').val());
                        $('#'+x+' .date_format').val($('#popup_data select.date_format').val());
                        $('#'+x+' .number_format').val($('#popup_data select.number_format').val());
                    });
                } else {
                    $('#'+eid+' .create_contact').val(create_contact);
                    $('#'+eid+' .language').val($('#popup_data select.language').val());
                    $('#'+eid+' .theme').val($('#popup_data select.theme').val());
                    $('#'+eid+' .date_format').val($('#popup_data select.date_format').val());
                    $('#'+eid+' .number_format').val($('#popup_data select.number_format').val());
                }
            } else if (etype == "security") {
                var can_see = 0;
                var can_reconfigure = 0;
                var can_control = 0;
                var can_control_engine = 0;
                var advanced_user = 0;
                var read_only = 0;
                var api_enabled = 0;

                if ($('#popup_data input.can_see').is(":checked")) { can_see = 1; }
                if ($('#popup_data input.can_reconfigure').is(":checked")) { can_reconfigure = 1; }
                if ($('#popup_data input.can_control').is(":checked")) { can_control = 1; }
                if ($('#popup_data input.can_control_engine').is(":checked")) { can_control_engine = 1; }
                if ($('#popup_data input.advanced_user').is(":checked")) { advanced_user = 1; }
                if ($('#popup_data input.read_only').is(":checked")) { read_only = 1; }
                if ($('#popup_data input.api_enabled').is(":checked")) { api_enabled = 1; }

                 if (eid == -1) {
                    $('.user-select:checked').each(function(k, v) {
                        var x = $(v).parents('tr').attr('id');
                        $('#'+x+' .auth_level').val($('#popup_data select.auth_level').val());
                        $('#'+x+' .can_see').val(can_see);
                        $('#'+x+' .can_reconfigure').val(can_reconfigure);
                        $('#'+x+' .can_control').val(can_control);
                        $('#'+x+' .can_control_engine').val(can_control_engine);
                        $('#'+x+' .advanced_user').val(advanced_user);
                        $('#'+x+' .read_only').val(read_only);
                        $('#'+x+' .api_enabled').val(api_enabled);
                        $('#'+x+' .ccm_access').val($('#popup_data select.ccm_access').val());
                        $('#popup_data input[name="ccm_access_list[]"]').each(function(k, v) {
                        var name = $(v).val();
                        if ($(v).is(":checked")) {
                            $('#'+x+' .ccm_access_list.'+name).attr('checked', true);
                        } else {
                            $('#'+x+' .ccm_access_list.'+name).attr('checked', false);
                        }
                    });
                    });
                } else {
                    $('#'+eid+' .auth_level').val($('#popup_data select.auth_level').val());
                    $('#'+eid+' .can_see').val(can_see);
                    $('#'+eid+' .can_reconfigure').val(can_reconfigure);
                    $('#'+eid+' .can_control').val(can_control);
                    $('#'+eid+' .can_control_engine').val(can_control_engine);
                    $('#'+eid+' .advanced_user').val(advanced_user);
                    $('#'+eid+' .read_only').val(read_only);
                    $('#'+eid+' .api_enabled').val(api_enabled);
                    $('#'+eid+' .ccm_access').val($('#popup_data select.ccm_access').val());
                    $('#popup_data input[name="ccm_access_list[]"]').each(function(k, v) {
                        var name = $(v).val();
                        if ($(v).is(":checked")) {
                            $('#'+eid+' .ccm_access_list.'+name).attr('checked', true);
                        } else {
                            $('#'+eid+' .ccm_access_list.'+name).attr('checked', false);
                        }
                    });
                }
            }

            close_child_popup();
            clear_whiteout();
            verify_users();
            if (eid == -1) {
                $('.edit-action').val('');
            }
        });

        function update_popup_security_checkboxes(eid)
        {
            var can_see = false;
            var can_reconfigure = false;
            var can_control = false;
            var can_control_engine = false;
            var advanced_user = false;
            var read_only = false;
            var api_enabled = false;

            if ($('#'+eid+' .can_see').val() == 1) { can_see = true; }
            if ($('#'+eid+' .can_reconfigure').val() == 1) { can_reconfigure = true; }
            if ($('#'+eid+' .can_control').val() == 1) { can_control = true; }
            if ($('#'+eid+' .can_control_engine').val() == 1) { can_control_engine = true; }
            if ($('#'+eid+' .advanced_user').val() == 1) { advanced_user = true; }
            if ($('#'+eid+' .read_only').val() == 1) { read_only = true; }
            if ($('#'+eid+' .api_enabled').val() == 1) { api_enabled = true; }

            $('#popup_data .can_see').attr('checked', can_see);
            $('#popup_data .can_reconfigure').attr('checked', can_reconfigure);
            $('#popup_data .can_control').attr('checked', can_control);
            $('#popup_data .can_control_engine').attr('checked', can_control_engine);
            $('#popup_data .advanced_user').attr('checked', advanced_user);
            $('#popup_data .read_only').attr('checked', read_only);
            $('#popup_data .api_enabled').attr('checked', api_enabled);
        }

        // Verifys that every user has their preferences/security settings set
        function verify_users()
        {
            var valid = true;
            $('.import-users tbody tr').each(function(i, row) {
                // Loop through each row and make sure everything we need is there...
                if (!$(row).find('.preferences-icon').hasClass('fa-check-circle') || !$(row).find('.security-icon').hasClass('fa-check-circle')) {
                    valid = false;
                    return;
                }
            });
            if (valid) {
                $('.import').attr('disabled', false);
                $('#import-message').hide();
            }
        }

        $('.user-select').click(function() {
            var disable_edit = true;
            $('.user-select').each(function(k, o) {
                if ($(o).is(':checked')) {
                    disable_edit = false;
                    return;
                }
            });

            $('.edit-action').prop('disabled', disable_edit).val('');
        });

        // Stop enter submitting form
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        // Check/uncheck all checkboxes
        $('#selectall').click(function() {
            if ($(this).is(":checked")) {
                $('input.user-select').prop('checked', true);
            } else {
                $('input.user-select').prop('checked', false);
                $('.edit-action').prop('disabled', true).val('');
            }
        });

        // Edit multiple user's settings at once
        $('.edit-action').change(function() {
            var etype = $(this).val();

            generate_popup(-1, etype);

            display_child_popup();
            whiteout();
            $("#child_popup_layer").center();
        });

        // Verify import before we send it out
        $('.import').click(function() {

            $('.import').prop('disabled', true).html('<i class="fa fa-spinner fa-pulse"></i> <?php echo encode_form_valq(_("Verifying")); ?>...');

            // Verify user's info...
            $.post("<?php echo get_component_url_base('ldap_ad_integration'); ?>/ajax.php", { cmd: 'getxiusers' }, function(users) {

                // Check usernames, names, and emails
                var errortext = '';
                var errors = false;
                var uerror = false;
                $('.import-users tbody tr').each(function(i, row) {
                    var username = $(row).find('.username').val();
                    if (username == "") {
                        $(row).find('.username').addClass('form-error');
                        errors = true;
                    } else if ($.inArray(username, users) >= 0) {
                        // Username is already in use
                        $(row).find('.username').addClass('form-error');
                        if (!uerror) {
                            errortext += '<strong><?php echo encode_form_valq(_("Username(s) already exist")); ?>.</strong> <?php echo encode_form_valq(_("Usernames must be unique")); ?>. ';
                        }
                        errors = true;
                        uerror = true;
                    }
                    if ($(row).find('.displayname').val() == "") {
                        $(row).find('.displayname').addClass('form-error');
                        errors = true;
                    }
                    if ($(row).find('.email').val() == "") {
                        $(row).find('.email').addClass('form-error');
                        errors = true;
                    }
                });

                if (!errors) {
                    $('form').submit();
                } else {
                    errortext += '<?php echo encode_form_valq(_("Must enter valid username, display name, and email for each user")); ?>. ';
                    $('.errors').html('<div class="alert alert-danger" style="margin-top: -20px;" role="alert">'+errortext+'</div>');
                    $('.import').prop('disabled', false).html('<?php echo encode_form_valq(_("Import")); ?> <i class="fa fa-chevron-right r"></i>');
                }

            }, 'json');

        });

        $('body').on('blur', '.form-error', function() {
            if ($(this).val() != "") {
                $(this).removeClass('form-error');
            }
        });

    });
    </script>

    <div id="import-message" class="message" style="max-width: 800px;">
        <ul class="actionMessage">
            <li><?php echo _('In order to finish importing you'); ?> <em><b><?php echo _('must select the preferences and security settings for all users'); ?></b></em>. <?php echo _('For quicker creation, select users with checkboxes and use the dropdown to set the preferences and security settings for multiple users at once.'); ?></li>
        </ul>
    </div>

    <form action="index.php?cmd=finish" method="post" style="margin-top: 25px;">

        <input type="hidden" name="cmd" value="finish">

        <?php echo get_nagios_session_protector(); ?>

        <div class="errors"></div>

        <table class="table table-striped table-bordered import-users" style="width: auto; margin-bottom: 0.5em;">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectall" class="checkbox user-select tt-bind" title="<?php echo _('Toggle checkboxes'); ?>"></th>
                    <th><?php echo _('Username'); ?></th>
                    <th><?php echo _('Display Name'); ?></th>
                    <th><?php echo _('Email'); ?></th>
                    <th style="text-align: center;"><?php echo _('Preferences'); ?></th>
                    <th style="text-align: center;"><?php echo _('Security Settings'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($users as $i => $user) {
                    if (!empty($user)) {
                    ?>
                    <tr id="user-<?php echo $i; ?>">
                        <td style="text-align: center;"><input type="checkbox" class="checkbox user-select"></td>
                        <td>
                            <input class="username form-control" name="users[<?php echo $i; ?>][username]" type="text" value="<?php echo $user['username']; ?>">
                            <input type="hidden" name="users[<?php echo $i; ?>][ldap_ad_username]" value="<?php echo $user['username']; ?>">
                            <input type="hidden" name="users[<?php echo $i; ?>][ldap_ad_dn]" value="<?php echo $user['dn']; ?>">
                        </td>
                        <td><input class="displayname form-control" name="users[<?php echo $i; ?>][displayname]" type="text" value="<?php echo $user['displayname']; ?>"></td>
                        <td><input class="email form-control" name="users[<?php echo $i; ?>][email]" type="text" value="<?php echo $user['email']; ?>" style="width: 200px;"></td>
                        <td style="line-height: 26px; text-align: center; width: 120px;">
                            <i class="fa fa-circle-o preferences-icon" style="margin-right: 4px; font-size: 12px;"></i>
                            <a class="edit" data-type="preferences"><?php echo _('Edit'); ?></a>
                            <input type="hidden" class="create_contact" name="preferences[<?php echo $i; ?>][create_contact]" value="">
                            <input type="hidden" class="language" name="preferences[<?php echo $i; ?>][language]" value="">
                            <input type="hidden" class="theme" name="preferences[<?php echo $i; ?>][theme]" value="">
                            <input type="hidden" class="date_format" name="preferences[<?php echo $i; ?>][date_format]" value="">
                            <input type="hidden" class="number_format" name="preferences[<?php echo $i; ?>][number_format]" value="">
                        </td>
                        <td style="line-height: 26px; text-align: center; width: 140px;">
                            <i class="fa fa-circle-o security-icon" style="margin-right: 4px; font-size: 12px;"></i> 
                            <a class="edit" data-type="security"><?php echo _('Edit'); ?></a>
                            <input type="hidden" class="auth_level" name="security[<?php echo $i; ?>][auth_level]" value="">
                            <input type="hidden" class="can_see" name="security[<?php echo $i; ?>][can_see]" value="">
                            <input type="hidden" class="can_reconfigure" name="security[<?php echo $i; ?>][can_reconfigure]" value="">
                            <input type="hidden" class="can_control" name="security[<?php echo $i; ?>][can_control]" value="">
                            <input type="hidden" class="can_control_engine" name="security[<?php echo $i; ?>][can_control_engine]" value="">
                            <input type="hidden" class="advanced_user" name="security[<?php echo $i; ?>][advanced_user]" value="">
                            <input type="hidden" class="read_only" name="security[<?php echo $i; ?>][read_only]" value="">
                            <input type="hidden" class="api_enabled" name="security[<?php echo $i; ?>][api_enabled]" value="">
                            <input type="hidden" class="ccm_access" name="security[<?php echo $i; ?>][ccm_access]" value="">
                            <div class="hide">
                                <input type="checkbox" class="ccm_access_list contact" name="security[<?php echo $i; ?>][ccm_access_list][]" value="contact">
                                <input type="checkbox" class="ccm_access_list contactgroup" name="security[<?php echo $i; ?>][ccm_access_list][]" value="contactgroup">
                                <input type="checkbox" class="ccm_access_list timeperiod" name="security[<?php echo $i; ?>][ccm_access_list][]" value="timeperiod">
                                <input type="checkbox" class="ccm_access_list hostescalation" name="security[<?php echo $i; ?>][ccm_access_list][]" value="hostescalation">
                                <input type="checkbox" class="ccm_access_list serviceescalation" name="security[<?php echo $i; ?>][ccm_access_list][]" value="serviceescalation">
                                <input type="checkbox" class="ccm_access_list hosttemplate" name="security[<?php echo $i; ?>][ccm_access_list][]" value="hosttemplate">
                                <input type="checkbox" class="ccm_access_list servicetemplate" name="security[<?php echo $i; ?>][ccm_access_list][]" value="servicetemplate">
                                <input type="checkbox" class="ccm_access_list contacttemplate" name="security[<?php echo $i; ?>][ccm_access_list][]" value="contacttemplate">
                                <input type="checkbox" class="ccm_access_list command" name="security[<?php echo $i; ?>][ccm_access_list][]" value="command">
                                <input type="checkbox" class="ccm_access_list hostdependency" name="security[<?php echo $i; ?>][ccm_access_list][]" value="hostdependency">
                                <input type="checkbox" class="ccm_access_list servicedependency" name="security[<?php echo $i; ?>][ccm_access_list][]" value="servicedependency">
                                <input type="checkbox" class="ccm_access_list staticconfig" name="security[<?php echo $i; ?>][ccm_access_list][]" value="staticconfig">
                                <input type="checkbox" class="ccm_access_list usermacros" name="security[<?php echo $i; ?>][ccm_access_list][]" value="usermacros">
                                <input type="checkbox" class="ccm_access_list import" name="security[<?php echo $i; ?>][ccm_access_list][]" value="import">
                                <input type="checkbox" class="ccm_access_list configmanagement" name="security[<?php echo $i; ?>][ccm_access_list][]" value="configmanagement">
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <select class="edit-action form-control" disabled>
            <option value="" disabled selected><?php echo _('Edit multiple'); ?> ...</option>
            <option value="preferences"><?php echo _('Preferences'); ?></option>
            <option value="security"><?php echo _('Security Settings'); ?></option>
        </select>

    <div class="preferences-form hide">
        <table class="table table-condensed table-no-border">
            <tbody>
                <tr>
                    <td><label for="acb"><?php echo _('Create as Monitoring Contact'); ?>:</label></td>
                    <td>
                        <input type="checkbox" class="checkbox create_contact" id="acb" name="create_contact" <?php echo is_checked($create_contact, 1); ?>>
                    </td>
                </tr>
                <tr>
                    <td><label><?php echo _('Language'); ?>:</label></td>
                    <td>
                        <select name="defaultLanguage" class="language languageList dropdown form-control">
                            <?php foreach ($languages as $lang => $title) { ?>
                            <option value="<?php echo $lang; ?>" <?php echo is_selected($language, $lang); ?>><?php echo get_language_nicename($title)."</option>"; ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?php echo _("XI User Interface Theme"); ?>:</label></td>
                    <td>
                        <select name="theme" class="theme form-control">
                            <option value=""<?php if ($theme == '') { echo " selected"; } ?>><?php echo _("Default"); ?></option>
                            <option value="xi5"<?php if ($theme == 'xi5') { echo " selected"; } ?>><?php echo _("Modern"); ?></option>
                            <option value="xi5dark"<?php if ($theme == 'xi5dark') { echo " selected"; } ?>><?php echo _("Modern Dark"); ?></option>
                            <option value="xi2014"<?php if ($theme == 'xi2014') { echo " selected"; } ?>><?php echo _("2014"); ?></option>
                            <option value="classic"<?php if ($theme == 'classic') { echo " selected"; } ?>><?php echo _("Classic"); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?php echo _('Date Format'); ?>:</label></td>
                    <td>
                        <select name="defaultDateFormat" class="date_format dateformatList dropdown form-control">
                            <?php foreach ($date_formats as $id => $txt) { ?>
                            <option value="<?php echo $id; ?>" <?php echo is_selected($id, $date_format); ?>><?php echo $txt; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label><?php echo _('Number Format'); ?>:</label></td>
                    <td>
                        <select name="defaultNumberFormat" class="number_format numberformatList dropdown form-control">
                            <?php foreach ($number_formats as $id => $txt) { ?>
                            <option value="<?php echo $id; ?>" <?php echo is_selected($id, $number_format); ?>><?php echo $txt; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="security-form hide">
        <table class="table table-condensed table-no-border">
            <tbody>
                <tr>
                    <td>
                        <label for="al"><?php echo _("Authorization Level");?>:</label>
                        <i tabindex="1" class="fa fa-question-circle pop" data-content="<b><?php echo _('User'); ?></b> - <?php echo _('Can only see hosts and services they are a contact of by default. View only, no configuration access by defualt.'); ?><br><br><b><?php echo _('Admin'); ?></b> - <?php echo _('Access to all objects by default and can control, access, and configure the entire Nagios XI system and has access to the admin section.'); ?>"></i>
                    </td>
                    <td>
                        <select name="level" id="al" class="auth_level authLevelList dropdown form-control">
                            <?php foreach ($authlevels as $al => $at) { ?>
                            <option value="<?php echo $al; ?>" <?php echo is_selected($level, $al); ?>><?php echo $at."</option>"; ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr class="user-setting">
                    <td>
                        <label for="aao"><?php echo _('Can see all hosts and services'); ?>:</label>
                        <i tabindex="2" class="fa fa-question-circle pop" data-content="<?php echo _('Allows a user to view all host and services that are applied in the system.'); ?><br><br><i><?php echo _('Note: User does not need to be a contact of an object to see it with this enabled.'); ?></i>"></i>
                    </td>
                    <td>
                        <input type="checkbox" value="1" class="checkbox can_see" id="aao" name="authorized_for_all_objects" <?php echo is_checked($authorized_for_all_objects, 1); ?>>
                    </td>
                </tr>
                <tr class="user-setting">
                    <td>
                        <label for="aaoc"><?php echo _('Can control hosts and services'); ?>:</label>
                        <i tabindex="3" class="fa fa-question-circle pop" data-content="<?php echo _('Allows the user do the following on hosts and services they have access to'); ?>: <ul style='margin: 0; padding: 10px 0 5px 20px;'><li><?php echo _('Acknowledge problems'); ?></li><li><?php echo _('Schedule downtime'); ?></li><li><?php echo _('Toggle notifications'); ?></li><li><?php echo _('Force checks on all objects'); ?></li></ul>"></i>
                    </td>
                    <td>
                        <input type="checkbox" value="1" class="checkbox can_control" id="aaco" name="authorized_for_all_object_commands" <?php echo is_checked($authorized_for_all_object_commands, 1); ?>>
                    </td>
                </tr>
                <tr class="user-setting">
                    <td>
                        <label for="aco"><?php echo _('Can configure hosts and services'); ?>:</label>
                        <i tabindex="4" class="fa fa-question-circle pop" data-content="<?php echo _('Allows a user to be able to access the following for hosts and services:'); ?><ul style='margin: 0; padding: 10px 0 5px 20px;'><li><?php echo _('Run Configuration Wizards'); ?></li><li><?php echo _('Delete from detail page'); ?></li><li><?php echo _('Re-configure from detail page'); ?></li</ul>"></i>
                    </td>
                    <td>
                        <input type="checkbox" value="1" class="checkbox can_reconfigure" id="atco" name="authorized_to_configure_objects" <?php echo is_checked($authorized_to_configure_objects, 1); ?>>
                    </td>
                </tr>
                <tr class="user-setting">
                    <td>
                        <label for="au"><?php echo _('Can access advanced features'); ?>:</label>
                        <i tabindex="5" class="fa fa-question-circle pop" data-content="<?php echo _('Allows the editing of check command in the re-configure host/service page.<br><br>Shows advanced tab and commands in host/service detail pages.<br><br>Allows setting host parents during wizards and re-configure on host detail page.'); ?>"></i>
                    </td>
                    <td>
                        <input type="checkbox" value="1" class="checkbox advanced_user" id="au" name="advanced_user" <?php echo is_checked($advanced_user, 1); ?>>
                    </td>
                </tr>
                <tr class="user-setting">
                    <td>
                        <label for="ams"><?php echo _('Can access monitoring engine'); ?>:</label>
                        <i tabindex="6" class="fa fa-question-circle pop" data-content="<?php echo _('Allows access to the Monitoring Process section in the main page.'); ?><br><br><?php echo _('Allows managing Nagios Core process such as starting, stopping, restarting, and changing process settings.'); ?><br><br><?php echo _('Allows access to the Event Log.'); ?>"></i>
                    </td>
                    <td>
                        <input type="checkbox" value="1" class="checkbox can_control_engine" id="ams" name="authorized_for_monitoring_system" <?php echo is_checked($authorized_for_monitoring_system, 1); ?>>
                    </td>
                </tr>

                <tr><td></td><td></td></tr>

                <tr class="user-setting">
                    <td>
                        <label for="rou"><?php echo _('Read-only access'); ?>:</label>
                        <i tabindex="7" class="fa fa-question-circle pop" data-content="<?php echo _('Restrict the user to have read-only access.'); ?><br><br><i><?php echo _('Note: This overwrites the following'); ?>:<ul style='margin: 0; padding: 10px 0 5px 20px;'><li><?php echo _('Can control hosts and services'); ?></li><li><?php echo _('Can configure hosts and services'); ?></li><li><?php echo _('Can access advanced features'); ?></li></ul></i>"></i>
                    </td>
                    <td>
                        <input type="checkbox" class="checkbox read_only" id="rou" name="readonly_user" <?php echo is_checked($readonly_user, 1); ?>>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="api_enabled"><?php echo _("API access"); ?>:</label>
                        <i tabindex="8" class="fa fa-question-circle pop" data-content="<?php echo _('Allow access to use the API and integrated help documentation.'); ?><br><br><i><?php echo _('Note: Users can only access the objects API endpoint.'); ?></i>"></i>
                    </td>
                    <td>
                        <input type="checkbox" class="checkbox api_enabled" id="api_enabled" name="api_enabled" <?php echo is_checked($api_enabled, 1); ?>>
                    </td>
                </tr>

                <tr class="user-setting"><td></td><td></td></tr>
                    <tr class="user-setting">
                    <td>
                        <label for="ccm_access"><?php echo _("Core Config Manager access:"); ?></label>
                        <i tabindex="9" class="fa fa-question-circle pop" data-content="<?php echo _('Allow user to view and access the CCM.'); ?><br><br><b><?php echo _('None'); ?></b> - <?php echo _('Cannot use or view the CCM.'); ?><br><br><b><?php echo _('Login'); ?></b> - <?php echo _('Can view CCM links and must log in with a CCM user account.'); ?><br><br><b><?php echo _('Limited'); ?></b> - <?php echo _('Integrated CCM access. User can only access the objects they can view in the interface normally. Allows for setting specific permissions for the user.'); ?><br><br><b><?php echo _('Full'); ?></b> - <?php echo _('Integrated CCM access. Can access all objects with no admin features.'); ?>"></i>
                    </td>
                    <td>
                        <select id="ccm_access" name="ccm_access" class="form-control ccm_access">
                            <option value="0" <?php if ($ccm_access == 0) { echo 'selected'; } ?>><?php echo _("None"); ?></option>
                            <option value="1" <?php if ($ccm_access == 1) { echo 'selected'; } ?>><?php echo _("Login"); ?></option>
                            <option value="2" <?php if ($ccm_access == 2) { echo 'selected'; } ?>><?php echo _("Limited"); ?></option>
                            <option value="3" <?php if ($ccm_access == 3) { echo 'selected'; } ?>><?php echo _("Full"); ?></option>
                        </select>
                    </td>
                </tr>

                <tr class="ccm-limited-access-settings<?php if ($ccm_access != 2) { echo ' hide'; } ?>">
                    <td colspan="2">
                        <div class="well" style="margin: 0;">

                            <div style="position: relative; padding-bottom: 10px;"><b><?php echo _('Limited Access CCM Permissions'); ?></b> <div style="position: absolute; right: 0; top: 0;"><?php echo _('Toggle'); ?> <a class="toggle-all"><?php echo _('All') ?></a> / <a class="toggle-none"><?php echo _('None'); ?></a></div></div>
                            <p>
                                <?php echo _('Users can only VIEW the below object types.'); ?><br>
                                <?php echo _('Select the object types to give them access to ADD, REMOVE, and EDIT.'); ?>
                            </p>

                            <table class="table table-condensed table-no-border table-bordered">
                                <thead style="border-bottom: 1px solid #EEE;">
                                    <tr>
                                        <th colspan="3"><?php echo _("Alerting Permissions"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="width: 33%;">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list contact" name="ccm_access_list[]" value="contact"<?php if (in_array('contact', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Contacts'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td style="width: 33%;">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list contactgroup" name="ccm_access_list[]" value="contactgroup"<?php if (in_array('contactgroup', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Contact Groups'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td style="width: 33%;">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list timeperiod" name="ccm_access_list[]" value="timeperiod"<?php if (in_array('timeperiod', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Time Periods'); ?>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list hostescalation" name="ccm_access_list[]" value="hostescalation"<?php if (in_array('hostescalation', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Host Escalations'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list serviceescalation" name="ccm_access_list[]" value="serviceescalation"<?php if (in_array('serviceescalation', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Service Escalations'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <thead style="border-bottom: 1px solid #EEE; border-top: 1px solid #EEE;">
                                    <tr>
                                        <th colspan="3"><?php echo _("Template Permissions"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list hosttemplate" name="ccm_access_list[]" value="hosttemplate"<?php if (in_array('hosttemplate', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Host Templates'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list servicetemplate" name="ccm_access_list[]" value="servicetemplate"<?php if (in_array('servicetemplate', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Service Templates'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list contacttemplate" name="ccm_access_list[]" value="contacttemplate"<?php if (in_array('contacttemplate', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Contact Templates'); ?>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <thead style="border-bottom: 1px solid #EEE; border-top: 1px solid #EEE;">
                                    <tr>
                                        <th colspan="3"><?php echo _("Command Permissions"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list command" name="ccm_access_list[]" value="command"<?php if (in_array('command', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Commands'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <thead style="border-bottom: 1px solid #EEE; border-top: 1px solid #EEE;">
                                    <tr>
                                        <th colspan="3"><?php echo _("Advanced Permissions"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list hostdependency" name="ccm_access_list[]" value="hostdependency"<?php if (in_array('hostdependency', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Host Dependencies'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list servicedependency" name="ccm_access_list[]" value="servicedependency"<?php if (in_array('servicedependency', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Service Dependencies'); ?>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <thead style="border-bottom: 1px solid #EEE; border-top: 1px solid #EEE;">
                                    <tr>
                                        <th colspan="3"><?php echo _("Tool Permissions"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list staticconfig" name="ccm_access_list[]" value="staticconfig"<?php if (in_array('staticconfig', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Static Config Editor'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list usermacros" name="ccm_access_list[]" value="usermacros"<?php if (in_array('usermacros', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('User Macros'); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list import" name="ccm_access_list[]" value="import"<?php if (in_array('import', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Import Config Files'); ?>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" class="ccm_access_list configmanagement" name="ccm_access_list[]" value="configmanagement"<?php if (in_array('configmanagement', $ccm_access_list)) { echo ' checked'; } ?>> <?php echo _('Config File Management'); ?>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="formButtons" style="margin-top: 3em;">
        <a href="index.php?cmd=cancel" class="btn btn-sm btn-default" name="cancelButton"><?php echo _('Cancel'); ?></a>
        <button type="button" class="btn btn-sm btn-primary import" name="updateButton" disabled><?php echo _('Import'); ?> <i class="fa fa-chevron-right r"></i></button>
    </div>

    </form>
<?php
}

/**
 * Function to create Nagios XI users based on the selected AD or LDAP users.
 */
function create_nagios_users($users, $preferences, $security)
{
    global $request;
    $server = grab_array_var($_SESSION, 'import_ldap_ad_server');

    $count = count($users);
    foreach ($users as $k => $user) {

        $username = $user["username"];
        $email = $user["email"];
        $name = $user["displayname"];
        $ldap_ad_username = $user["ldap_ad_username"];
        $ldap_ad_dn = $user["ldap_ad_dn"];
        $password = random_string(12);

        $user_id = add_user_account($username, $password ,$name, $email, $security[$k]['auth_level'], 0, $preferences[$k]['create_contact'], $security[$k]['api_enabled'], $errmsg, false);

        // Don't continue if the user_id doesn't actually exist!
        if (empty($user_id)) {
            continue;
        }

        set_user_meta($user_id, 'language', $preferences[$k]['language']);
        set_user_meta($user_id, 'theme', $preferences[$k]['theme']);
        set_user_meta($user_id, "date_format", $preferences[$k]['date_format']);
        set_user_meta($user_id, "number_format", $preferences[$k]['number_format']);
        set_user_meta($user_id, "authorized_for_all_objects", $security[$k]['can_see']);
        set_user_meta($user_id, "authorized_to_configure_objects", $security[$k]['can_reconfigure']);
        set_user_meta($user_id, "authorized_for_all_object_commands", $security[$k]['can_control']);
        set_user_meta($user_id, "authorized_for_monitoring_system", $security[$k]['can_control_engine']);
        set_user_meta($user_id, "advanced_user", $security[$k]['advanced_user']);
        set_user_meta($user_id, "readonly_user", $security[$k]['read_only']);

        set_user_meta($user_id, "auth_type", $server['conn_method']);
        set_user_meta($user_id, "auth_server_id", $server['id']);
        set_user_meta($user_id, "ldap_ad_username", $ldap_ad_username);
        set_user_meta($user_id, "ldap_ad_dn", $ldap_ad_dn);

        // Set CCM access for normal users
        if ($security[$k]['auth_level'] != 255) {
            set_user_meta($user_id, "ccm_access", $security[$k]['ccm_access']);
            $ccm_access_list = array();
            if (!empty($security[$k]['ccm_access_list'])) {
                $ccm_access_list = $security[$k]['ccm_access_list'];
            }
            set_user_meta($user_id, "ccm_access_list", base64_encode(serialize($ccm_access_list)));
        }

        // Update nagios cgi config file
        update_nagioscore_cgi_config();
        
        // Add to audit log
        if ($security[$k]['auth_level'] == L_GLOBALADMIN) {
            send_to_audit_log("User account '".$original_user."' was created with GLOBAL ADMIN privileges from LDAP/AD User Import function.", AUDITLOGTYPE_SECURITY);   
        }
    }

    finish_page($count);
}

function finish_page($count) {
    do_page_start(array("page_title" => _("LDAP / Active Directory Import Users")), true);
    echo '<h1>'._("LDAP / Active Directory Import Users").'</h1>';
    echo '<div class="message"><ul class="infoMessage"><li>'._("Successfully added").' '.$count.' '._("users").'.</li></ul></div>';
    collect_credentials();
}