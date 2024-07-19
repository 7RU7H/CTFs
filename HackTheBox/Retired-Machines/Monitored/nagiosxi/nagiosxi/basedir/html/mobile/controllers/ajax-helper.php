<?php

require_once('../../includes/common.inc.php');
require_once('main-utils.php');

init_session();
route_request();


function route_request()
{
	$mode = grab_request_var('mode', '');

	switch ($mode) {
		case 'set_toast':
			set_toast();
			break;
		default:
			break;
	}
}