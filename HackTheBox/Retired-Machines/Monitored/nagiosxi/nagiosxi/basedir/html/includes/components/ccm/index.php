<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2021 Nagios Enterprises, LLC
//
//  File: index.php
//  Desc: Main page in the CCM
//

// Include the Nagios XI helper functions through the component helper file and initialize
// anything we will need to authenticate ourselves to the CCM
require_once(dirname(__FILE__).'/../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables and check pre-reqs
grab_request_vars();
check_prereqs();
check_authentication(false);

// Verify access
if (!user_can_access_ccm()) {
    die(_('You do not have access to this page.'));
}

// Set the location of the CCM root directory
define('BASEDIR', dirname(__FILE__).'/');
require_once('includes/constants.inc.php');
require_once('includes/session.inc.php');

// Do session tracking / edit locking
licensed_feature_check(true, false, true);

$obj_id = intval(grab_request_var('id', 0));
$enable_locking = get_option('ccm_enable_locking', 1);

// Do session tracking for page locks
$ccm_session_id = session_tracking();

// Check if there is currently a session on this page
$lock = false;
if ($enable_locking) {
    $lock = session_get_lock();
}

$loginStatus = grab_array_var($_SESSION, 'loginStatus', false);

ob_start();
print page_router();
$page_html = ob_get_clean();
ob_end_clean();

// Display page heading
do_page_start(array("page_title" => _('CCM')), true);

// Let's display the red asterik next to apply config if we have apply configuration needed
?>
<script type="text/javascript">
var CCM_SESSION_ID = <?php echo $ccm_session_id ? $ccm_session_id : 0; ?>;
var CCM_LOCK = <?php if (!empty($lock)) { echo json_encode($lock); } else { echo '{ }'; } ?>;

$(document).ready(function() {

    if (CCM_SESSION_ID) {

        $(window).bind('beforeunload', function(e) {
            $.ajax({
                url: 'ajax.php',
                method: 'POST',
                async: false,
                data: { cmd: 'removesession', ccm_session_id: CCM_SESSION_ID }
            });
        });

        // Update the session if user is just sitting on a page (or editing it)
        var update_id = setInterval(update_session_and_lock, 10000);

        check_page_usage();
    }

    $(window).resize(function() {
        $('#lock-notice').center().css('top', '250px');
    });

    $('#remove-lock').click(function() {
        $.post('ajax.php', { cmd: 'takelock', lock_id: CCM_LOCK.id, ccm_session_id: CCM_SESSION_ID }, function(d) {
            if (d.success) {
                CCM_LOCK = { }
                $('#lock-notice').hide();
                clear_whiteout();
            }
        }, 'json');
    });
});

function update_session_and_lock()
{
    // Update session and return lock values
    var vars = { cmd: 'updatesession', ccm_session_id: CCM_SESSION_ID, obj_id: <?php echo $obj_id; ?> };
    if (CCM_LOCK.id) {
        vars.lock_id = CCM_LOCK.id;
    }

    // Update session and get new lock if there is one
    $.post('ajax.php', vars, function(d) {
        if (d.has_new_lock) {
            CCM_LOCK = d.lock;
            $('.lock-text').html(d.locktext);
            check_page_usage();
        }
    }, 'json');
}

function check_page_usage()
{
    if (CCM_LOCK.id) {
        whiteout();
        $('#lock-notice').center().css('top', '250px').show();
    }
}

<?php
    $ac_needed = get_option("ccm_apply_config_needed", 0);
    if ($ac_needed == 1) {
        $cmd = grab_request_var('cmd', '');
?>
window.parent.$("#ccm-apply-menu-link").html('<span class="tooltip-apply" data-placement="right" title="<?php echo _("There are modifications to objects that have not been applied yet. Apply configuration for new changes to take affect."); ?>"><i class="fa fa-fw fa-asterisk urgent"></i> <?php echo _("Apply Configuration"); ?></span>');
window.parent.$('.tooltip-apply').tooltip({ template: '<div class="tooltip ccm-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>', container: 'body', trigger: 'hover' });

<?php if (empty($cmd)) { ?>
window.parent.$('#fullscreen').addClass('white');
window.parent.$('div#leftnav a').click(function() {
    window.parent.$('#fullscreen').removeClass('white');
    window.parent.$('div#leftnav a').unbind();
});
<?php
        }
    }
?>

</script>

    <div id="screen-overlay"></div>
    <div id="whiteout"></div>
    <div id="lock-notice" class="hide info-popup" style="text-align: center; padding: 25px;">
        <h4><i class="fa fa-exclamation-triangle" style="vertical-align: middle;"></i> <?php echo _('The page is currently being edited by another user.'); ?></h4>
        <div class="lock-text">
            <?php if (!empty($lock)) { ?>
            <b><?php echo encode_form_val($lock['username']); ?></b> <?php echo _('started editing at'); ?> <?php echo get_datetime_string($lock['started'], DT_SHORT_DATE_TIME, DF_AUTO, "null"); ?>
            <?php } ?>
        </div>
        <div class="btns">
            <button type="button" id="remove-lock" class="btn btn-sm btn-danger"><?php echo _('Remove Lock'); ?></button>
            <a href="<?php echo urlencode(encode_form_val(grab_request_var('returnUrl', ''))); ?>" class="btn btn-sm btn-default"><?php echo _('Cancel'); ?></a>
        </div>
    </div>
    <div id="loginMsgDiv" <?php if (get_user_meta(0, 'ccm_access', 0) > 1 || is_admin()) { echo 'style="display: none;"'; } ?>>
        <span <?php if(!($loginStatus === false)) echo "class='deselect'"; ?>>
            <div <?php if($loginStatus === false) echo "class='error'"; ?>>
                <?php if (!empty($_SESSION['loginMessage'])) { echo $_SESSION['loginMessage']; } ?>
            </div>
        </span>
    </div>

<?php
// Display the actual page through the page router...
print $page_html;

do_page_end(true);
