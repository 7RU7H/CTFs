var ajax_url = '../controllers/ajax-helper.php';

function show_toast(message = '', type = 'info') {
	$(document).ready(function() {
		$('.toast').addClass('show');
		setTimeout(function() {
			$('.toast').removeClass('show');
		}, 2750);
	});
}

function ajax_set_toast(message = '', type= '') {

	$.ajax({
	    type: "POST",
	    async: false,
	    url: ajax_url,
	    data: {mode: 'set_toast', message: message, toast_type: type},
	    success: function (data) {
			
	    }
	});

}

function set_toast_content(message = '', type = '') {
	if (message != '') {
		$('#toast-container').html(message);
		$('#toast-container').addClass(type);
	}
}

const mobile_submit_command = (callingElement, cmd_id, cmd_data_raw, callback = '', cmd_time_raw, cmd_args_raw) =>
{
    var cmd_data = typeof(cmd_data_raw) != 'undefined' ? cmd_data_raw : '';
    var cmd_time = typeof(cmd_time_raw) != 'undefined' ? cmd_time_raw : 0;
    var cmd_args = typeof(cmd_data_args) != 'undefined' ? cmd_args_raw : '';

    var optsarr = {
        "cmd": cmd_id,
        "cmddata": cmd_data,
        "cmdtime": cmd_time,
        "cmdargs": cmd_args
    };
    
    var result = raw_submit_command(optsarr);
    if (result < 0) {
    	set_toast_content('<p>Command Could not be Submitted</p>', 'failure');
    	show_toast();
    } else {
        set_toast_content('<p>Command Submitted Successfully</p>', 'success');
        show_toast();
    }

    if (callback != '') {
    	do_callback(callback, callingElement);
    }
}

function do_callback(callback, object) {
	whitelist = ['toggle_notification_icon'];

	if ( whitelist.indexOf(callback) > -1 ) {
		const callbackFunction = window[callback];
		if (typeof callbackFunction === 'function') {
			callbackFunction(object);
		}
	} else {
		return false;
	}
}

function toggle_notification_icon(element) {
	var functionCall = $(element).attr('onclick');
	var icon = $(element).children('svg');
	var text = $(element).children('span');

	if ( icon.hasClass('fa-bell') ) {
		icon.removeClass('fa-bell').addClass('fa-bell-slash');
		text.html('Disable Notifications');
		// Swap host commands
		functionCall = functionCall.replace('24', '25');
		// Swap service commands
		functionCall = functionCall.replace('22', '23');
		$(element).attr('onclick', functionCall);

	} else {
		icon.removeClass('fa-bell-slash').addClass('fa-bell');
		text.html('Enable Notifications');
		// Swap host commands
		functionCall = functionCall.replace('25', '24');
		// Swap service commands
		functionCall = functionCall.replace('23', '22');
		$(element).attr('onclick', functionCall);
	}

}