<?php
//
// Copyright (c) 2008-2020 Nagios Enterprises, LLC. All rights reserved.
//

require_once(dirname(__FILE__) . '/constants-nagioscore.inc.php');

// DATABASES
define("DB_NAGIOSXI", "nagiosxi");
define("DB_NDOUTILS", "ndoutils");
define("DB_NAGIOSQL", "nagiosql");

// PAGES
define("PAGE_LOGIN", "auth");
define("PAGE_HOME", "home");
define("PAGE_MYVIEWS", "myviews");
define("PAGE_MYDASHBOARDS", "mydashboards");
define("PAGE_ADMIN", "admin");
define("PAGE_ACCTINFO", "acctinfo");

// PAGE FILES
define("PAGEFILE_AJAXHELPER", "ajaxhelper.php");
define("PAGEFILE_AJAXPROXY", "ajaxproxy.php");
define("PAGEFILE_SUGGEST", "suggest.php");
define("PAGEFILE_LOGIN", "login.php");
define("PAGEFILE_INSTALL", "install.php");
define("PAGEFILE_UPGRADE", "upgrade.php");

// PAGE OPTIONS
define("PAGEOPT_LOGIN", "login");
define("PAGEOPT_LOGOUT", "logout");

// COMMON COLORS
define("COMMONCOLOR_GREEN", "#5CDF45");
define("COMMONCOLOR_YELLOW", "#FEFF6F");
define("COMMONCOLOR_ORANGE", "#FFBD6F");
define("COMMONCOLOR_RED", "#FF795F");
define("COMMONCOLOR_BLUE", "#5FB7FF");

// NAGIOS XI THEME/STYLE DEFAULTS
define("NAGIOSXI_SMALL_LOGO", "nagiosxi-logo-small.png");

// LOOP DEFINITIONS
define("LOOPTYPE_OBJECT", 0);
define("LOOPTYPE_STATUS", 1);

define("LOOPLEVEL_HOSTGROUP", "looplevel_hostgroup");
define("LOOPLEVEL_HOST", "looplevel_host");
define("LOOPLEVEL_SERVICE", "looplevel_service");
define("LOOPLEVEL_OBJECTARGS", "looplevel_objectargs");

define("LOOPCALLBACK_LEVELSTART", "level_start_func");
define("LOOPCALLBACK_LEVELEND", "level_end_func");
define("LOOPCALLBACK_ITEMSTART", "item_start_func");
define("LOOPCALLBACK_ITEMEND", "item_end_func");

// COMPONENT DEFINITIONS
define("COMPONENT_API_VERSION", 2); // API version
define("COMPONENT_NAME", "name");
define("COMPONENT_DIRECTORY", "directory");
define("COMPONENT_ARGS", "args");

define("COMPONENT_PROTECTED", "protected");
define("COMPONENT_ENCRYPTED", "encrypted");
define("COMPONENT_TYPE", "type");
define("COMPONENT_TITLE", "title");
define("COMPONENT_VERSION", "version");
define("COMPONENT_AUTHOR", "author");
define("COMPONENT_DESCRIPTION", "description");
define("COMPONENT_DATE", "date");
define("COMPONENT_COPYRIGHT", "copyright");
define("COMPONENT_LICENSE", "license");
define("COMPONENT_HOMEPAGE", "homepage");
define("COMPONENT_CONFIGFUNCTION", "configfunc");
define("COMPONENT_REQUIRES_ROOT_SETUP", "requires_root_setup");
define("COMPONENT_REQUIRES_VERSION", "requires_version");

define("COMPONENT_TYPE_CORE", "core");
define("COMPONENT_TYPE_USER", "user");

//define("COMPONENT_PASSBACK_DATA","passbackdata");
define("COMPONENT_CONFIGMODE_GETSETTINGSHTML", "getsettingshtml");
define("COMPONENT_CONFIGMODE_SAVESETTINGS", "savesettings");
define("COMPONENT_ERROR_MESSAGES", "errormessages");
define("COMPONENT_INFO_MESSAGES", "infomessages");


// DASHBOARD DEFINITIONS
define("HOMEPAGE_DASHBOARD_ID", "home");
define("HOMEPAGE_DASHBOARD_TITLE", "Home Page");
define("SCREEN_DASHBOARD_ID", "screen");
define("SCREEN_DASHBOARD_TITLE", "[ Screen ]");

// DASHLET DEFINITIONS
define("DASHLET_API_VERSION", 1); // API version
define("DASHLET_ID", "id");
define("DASHLET_NAME", "name");
define("DASHLET_ARGS", "args");
define("DASHLET_VERSION", "version");
define("DASHLET_AUTHOR", "author");
define("DASHLET_DESCRIPTION", "description");
define("DASHLET_DATE", "date");
define("DASHLET_COPYRIGHT", "copyright");
define("DASHLET_LICENSE", "license");
define("DASHLET_HOMEPAGE", "homepage");
define("DASHLET_PREVIEW_IMAGE", "preview_image");
define("DASHLET_FUNCTION", "func");
define("DASHLET_URL", "url");
define("DASHLET_HTML", "html");
define("DASHLET_TITLE", "title");
define("DASHLET_ADDTODASHBOARDTITLE", "addtitle");
define("DASHLET_CSS_FILE", "css_file");
define("DASHLET_JS_FILE", "js_file");
define("DASHLET_CONFIGHTML", "confightml");
//define("DASHLET_DISPLAYARGS","displayargs");
define("DASHLET_CLASS", "class");
define("DASHLET_OUTBOARD_CLASS", "outboard_class");
define("DASHLET_INBOARD_CLASS", "inboard_class");
define("DASHLET_PREVIEW_CLASS", "preview_class");
define("DASHLET_BOARD_STATUS", "board_status");
define("DASHLET_BOARD_STATUS_OUTBOARD", "outboard");
define("DASHLET_BOARD_STATUS_INBOARD", "inboard");
define("DASHLET_BOARD_STATUS_PREVIEW", "outboard");
define("DASHLET_MODE", "mode");
define("DASHLET_MODE_PREVIEW", "preview");
define("DASHLET_MODE_INBOARD", "inboard");
define("DASHLET_MODE_OUTBOARD", "outboard");
define("DASHLET_MODE_GETCONFIGHTML", "getconfightml");
define("DASHLET_MODE_GETRECONFIGHTML", "getreconfightml");
define("DASHLET_OPTS", "opts");
define("DASHLET_WIDTH", "width");
define("DASHLET_HEIGHT", "height");
define("DASHLET_OPACITY", "opacity");
define("DASHLET_BACKGROUND", "background");
define("DASHLET_REFRESHRATE", "refresh_rate");
define("DASHLET_SHOWASAVAILABLE", "show_as_available");
define("DASHLET_PERMS", "perms");
define("DASHLET_REQUIREDCOMPONENTS", "required_components");
define("DASHLET_REQUIRES_ROOT_SETUP", "requires_root_setup");

// PERFORMANCE GRAPH/DATA
define("PERFGRAPH_MODE_HOSTSOVERVIEW", 0);
define("PERFGRAPH_MODE_HOSTOVERVIEW", 1);
define("PERFGRAPH_MODE_SERVICEDETAIL", 2);
define("PERFGRAPH_MODE_GOTOSERVICEDETAIL", 3);
define("DELETE_ALL_PERFDATA", 0);


// DATE FORMATS
define("DF_AUTO", 0);
define("DF_ISO8601", 1); // 2008-01-31 23:30:01
define("DF_US", 2); // 01/31/2008 23:30:01
define("DF_EURO", 3); // 31/01/2008 23:30:01

// DATE/TIME TYPES
define("DT_AUTO", 0);
define("DT_LONG_DATE_TIME", 1);
define("DT_SHORT_DATE_TIME", 2);
define("DT_SHORT_DATE", 3);
define("DT_SHORT_TIME", 4);
define("DT_HTTP_DATE_TIME", 5);
define("DT_SQL_DATE_TIME", 6);
define("DT_SQL_DATE", 7);
define("DT_SQL_TIME", 8);
define("DT_UNIX", 9);

// NUMBER FORMATS
define("NF_AUTO", 0);
define("NF_1", 1); // 1000000.00  - English w/o commas
define("NF_2", 2); // 1,000,000.00 - English
define("NF_3", 3); // 1.000.000,00  - German
define("NF_4", 4); // 1 000 000,00 - French
define("NF_5", 5); // 1'000'000,00 - Swiss

define("WF_US", 0);
define("WF_EURO", 1);

// USER LEVELS
define("L_USER", 1);
define("L_GLOBALADMIN", 255);


// INSTANCE/OBJECT PERMISSIONS
define("P_NONE", 0);
define("P_READ", 1);
define("P_WRITE", 2);
define("P_EXECUTE", 4);
define("P_LIST", 8);
define("P_ADMIN", 16);
define("P_RESERVED1", 32);
define("P_RESERVED2", 64);
define("P_RESERVED3", 128);
define("P_ALL", 255);

// INSTANCE OBJECT PERMISSION CALCULATION METHODS
define("PM_NONE", 0);
define("PM_NAGIOS", 1);
define("PM_CUSTOM", 2);
define("PM_RXL", 4);
define("PM_ADMIN", 128);
define("PM_ALL", 255);

// OBJECT TYPES
define("OBJECTTYPE_HOST", 1);
define("OBJECTTYPE_SERVICE", 2);
define("OBJECTTYPE_HOSTGROUP", 3);
define("OBJECTTYPE_SERVICEGROUP", 4);
define("OBJECTTYPE_HOSTESCALATION", 5);
define("OBJECTTYPE_SERVICEESCALATION", 6);
define("OBJECTTYPE_HOSTDEPENDENCY", 7);
define("OBJECTTYPE_SERVICEDEPENDENCY", 8);
define("OBJECTTYPE_TIMEPERIOD", 9);
define("OBJECTTYPE_CONTACT", 10);
define("OBJECTTYPE_CONTACTGROUP", 11);
define("OBJECTTYPE_COMMAND", 12);

// COMMENT TYPES
define("COMMENTTYPE_ACKNOWLEDGEMENT", 4);
define("COMMENTTYPE_FLAPDETECTION", 3);
define("COMMENTTYPE_DOWNTIME", 2);
define("COMMENTTYPE_USER", 1);

// TIMED EVENT TYPES
define("TIMEDEVENTTYPE_HOSTCHECK", 12);
define("TIMEDEVENTTYPE_SERVICECHECK", 0);

// CHECK TYPES
define("ACTIVE_CHECK", 0);
define("PASSIVE_CHECK", 1);


// SUBSYSTEM COMPONENT STATUS
define("SUBSYS_COMPONENT_RUNNING", 1);
define("SUBSYS_COMPONENT_STOPPED", 2);
define("SUBSYS_COMPONENT_CRASHED", 3);
define("SUBSYS_COMPONENT_STOPPING", 4);
define("SUBSYS_COMPONENT_RESTARTING", 5);
define("SUBSYS_COMPONENT_RELOADING", 6);

define("SUBSYS_COMPONENT_STATUS_OK", 0);
define("SUBSYS_COMPONENT_STATUS_ERROR", 1);
define("SUBSYS_COMPONENT_STATUS_UNKNOWN", 2);


// MESSAGE STRING
define("MSG_OK", "OK");
define("MSG_ERROR", "ERROR");


// RESULT CODES
define("RESULT_ERROR", -1);
define("RESULT_OK", 0);

// COMMAND STATUS CODES
define("COMMAND_STATUS_QUEUED", 0);
define("COMMAND_STATUS_PROCESSING", 1);
define("COMMAND_STATUS_COMPLETED", 2);

// COMMAND RESULT CODES
define("COMMAND_RESULT_OK", 0);
define("COMMAND_RESULT_ERROR", 1);


// COMMANDS
define("COMMAND_NONE", 0);

define("COMMAND_NAGIOSCORE_GETSTATUS", 10);
define("COMMAND_NAGIOSCORE_START", 11);
define("COMMAND_NAGIOSCORE_STOP", 12);
define("COMMAND_NAGIOSCORE_RESTART", 13);
define("COMMAND_NAGIOSCORE_RELOAD", 14);
define("COMMAND_NAGIOSCORE_CHECKCONFIG", 15);
define("COMMAND_NAGIOSCORE_SUBMITCOMMAND", 16);
define("COMMAND_NAGIOSCORE_APPLYCONFIG", 17);
define("COMMAND_NAGIOSCORE_RECONFIGURE", 18);
define("COMMAND_NAGIOSCORE_IMPORTONLY", 19);

// NO LONGER USED, NDO doesn't use a daemon
define("COMMAND_NDO2DB_GETSTATUS", 100);
define("COMMAND_NDO2DB_START", 101);
define("COMMAND_NDO2DB_STOP", 102);
define("COMMAND_NDO2DB_RESTART", 103);
define("COMMAND_NDO2DB_RELOAD", 104);

define("COMMAND_NPCD_GETSTATUS", 110);
define("COMMAND_NPCD_START", 111);
define("COMMAND_NPCD_STOP", 112);
define("COMMAND_NPCD_RESTART", 113);
define("COMMAND_NPCD_RELOAD", 114);

define("COMMAND_NAGIOSQL_DELETECONTACT", 201);
define("COMMAND_NAGIOSQL_DELETETIMEPERIOD", 202);
define("COMMAND_NAGIOSQL_DELETESERVICE", 203);
define("COMMAND_NAGIOSQL_DELETEHOST", 204);

define("COMMAND_NAGIOSXI_SET_HTACCESS", 1100);
define("COMMAND_DELETE_CONFIGWIZARD", 1101);
define("COMMAND_INSTALL_CONFIGWIZARD", 1102);
define("COMMAND_PACKAGE_CONFIGWIZARD", 1103);
define("COMMAND_DELETE_DASHLET", 1104);
define("COMMAND_INSTALL_DASHLET", 1105);
define("COMMAND_PACKAGE_DASHLET", 1106);
define("COMMAND_DELETE_COMPONENT", 1107);
define("COMMAND_INSTALL_COMPONENT", 1108);
define("COMMAND_PACKAGE_COMPONENT", 1109);

define("COMMAND_DELETE_CONFIGSNAPSHOT", 1110);
define("COMMAND_RESTORE_CONFIGSNAPSHOT", 1111);
define("COMMAND_NAGIOSXI_DEL_HTACCESS", 1112);
define("COMMAND_RESTORE_NAGIOSQL_SNAPSHOT", 1113);
define("COMMAND_ARCHIVE_SNAPSHOT", 1114);
define("COMMAND_DELETE_ARCHIVE_SNAPSHOT", 1115);
define("COMMAND_RENAME_ARCHIVE_SNAPSHOT", 1116);

define("COMMAND_CREATE_SYSTEM_BACKUP", 1117);
define("COMMAND_RENAME_SYSTEM_BACKUP", 1118);
define("COMMAND_DELETE_SYSTEM_BACKUP", 1119);

define("COMMAND_UPDATE_XI_TO_LATEST", 1120);
define("COMMAND_CHANGE_TIMEZONE", 1121);
define("COMMAND_RESTART_HTTP", 1122);
define("COMMAND_RESTART_SNMPTT", 1123);
define("COMMAND_RESTART_PHP_FPM", 1124);

define("COMMAND_UPGRADE_CONFIGWIZARD", 1130);
define("COMMAND_UPGRADE_COMPONENT", 1131);

define("COMMAND_RUN_CHECK_CMD", 1132);

define("COMMAND_ENABLE_HTTP_REDIRECT", 1140);
define("COMMAND_DISABLE_HTTP_REDIRECT", 1141);
define("COMMAND_UPDATE_SSL_CONFIG", 1142);

define("COMMAND_BPI_SYNC", 1150);
define("COMMAND_BPI_REMOVE_MISSING", 1151);

define("COMMAND_CCM_UPDATE_PERMS", 1160);

define("COMMAND_START_DEPLOY", 1170);
define("COMMAND_DELETE_DEPLOY", 1171);

define("COMMAND_START_MIGRATE", 1180);

define("COMMAND_STOP_SHELLINABOX", 1251);
define("COMMAND_START_SHELLINABOX", 1252);
define("COMMAND_ENABLE_SHELLINABOX", 1253);
define("COMMAND_DISABLE_SHELLINABOX", 1254);

define("COMMAND_SEND_TO_LOGSERVER", 1260);
define("COMMAND_STOP_SENDING_TO_LOGSERVER", 1261);

// META TYPES
define("METATYPE_NONE", 0);
define("METATYPE_EVENT", 1);
define("METATYPE_COMMAND", 2);
define("METATYPE_NOTIFICATION", 3);
define("METATYPE_CONFIGWIZARD", 4);
define("METATYPE_OTHER", 999);


// HOST STATUS TYPES
define("HOSTSTATUSATTR_INDOWNTIME", 1);
define("HOSTSTATUSATTR_NOTINDOWNTIME", 2);
define("HOSTSTATUSATTR_ACKNOWLEDGED", 4);
define("HOSTSTATUSATTR_NOTACKNOWLEDGED", 8);
define("HOSTSTATUSATTR_CHECKSDISABLED", 16);
define("HOSTSTATUSATTR_CHECKSENABLED", 32);
define("HOSTSTATUSATTR_EVENTHANDLERDISABLED", 64);
define("HOSTSTATUSATTR_EVENTHANDLERENABLED", 128);
define("HOSTSTATUSATTR_FLAPDETECTIONDISABLED", 256);
define("HOSTSTATUSATTR_FLAPDETECTIONENABLED", 512);
define("HOSTSTATUSATTR_ISFLAPPING", 1024);
define("HOSTSTATUSATTR_ISNOTFLAPPING", 2048);
define("HOSTSTATUSATTR_NOTIFICATIONSDISABLED", 4096);
define("HOSTSTATUSATTR_NOTIFICATIONSENABLED", 8192);
define("HOSTSTATUSATTR_PASSIVECHECKSDISABLED", 16384);
define("HOSTSTATUSATTR_PASSIVECHECKSENABLED", 32768);
define("HOSTSTATUSATTR_PASSIVECHECK", 65536);
define("HOSTSTATUSATTR_ACTIVECHECK", 131072);
define("HOSTSTATUSATTR_HARDSTATE", 262144);
define("HOSTSTATUSATTR_SOFTSTATE", 524288);

// SERVICE STATUS TYPES
define("SERVICESTATUSATTR_INDOWNTIME", 1);
define("SERVICESTATUSATTR_NOTINDOWNTIME", 2);
define("SERVICESTATUSATTR_ACKNOWLEDGED", 4);
define("SERVICESTATUSATTR_NOTACKNOWLEDGED", 8);
define("SERVICESTATUSATTR_CHECKSDISABLED", 16);
define("SERVICESTATUSATTR_CHECKSENABLED", 32);
define("SERVICESTATUSATTR_EVENTHANDLERDISABLED", 64);
define("SERVICESTATUSATTR_EVENTHANDLERENABLED", 128);
define("SERVICESTATUSATTR_FLAPDETECTIONDISABLED", 256);
define("SERVICESTATUSATTR_FLAPDETECTIONENABLED", 512);
define("SERVICESTATUSATTR_ISFLAPPING", 1024);
define("SERVICESTATUSATTR_ISNOTFLAPPING", 2048);
define("SERVICESTATUSATTR_NOTIFICATIONSDISABLED", 4096);
define("SERVICESTATUSATTR_NOTIFICATIONSENABLED", 8192);
define("SERVICESTATUSATTR_PASSIVECHECKSDISABLED", 16384);
define("SERVICESTATUSATTR_PASSIVECHECKSENABLED", 32768);
define("SERVICESTATUSATTR_PASSIVECHECK", 65536);
define("SERVICESTATUSATTR_ACTIVECHECK", 131072);
define("SERVICESTATUSATTR_HARDSTATE", 262144);
define("SERVICESTATUSATTR_SOFTSTATE", 524288);

// STATES
define("STATE_OK", 0);
define("STATE_WARNING", 1);
define("STATE_CRITICAL", 2);
define("STATE_UNKNOWN", 3);
define("STATE_UP", 0);
define("STATE_DOWN", 1);
define("STATE_UNREACHABLE", 2);

// PSEUDO-STATES (USED IN LINKS, ETC)
define("SERVICESTATE_PENDING", 1);
define("SERVICESTATE_OK", 2);
define("SERVICESTATE_WARNING", 4);
define("SERVICESTATE_UNKNOWN", 8);
define("SERVICESTATE_CRITICAL", 16);
define("SERVICESTATE_ANY", 31);

define("HOSTSTATE_PENDING", 1);
define("HOSTSTATE_UP", 2);
define("HOSTSTATE_DOWN", 4);
define("HOSTSTATE_UNREACHABLE", 8);
define("HOSTSTATE_ANY", 15);


// STATE TYPES
define("STATETYPE_SOFT", 0);
define("STATETYPE_HARD", 1);

// NOTIFICATION REASONS
define("NOTIFICATIONREASON_NORMAL", 0);
define("NOTIFICATIONREASON_ACKNOWLEDGEMENT", 1);
define("NOTIFICATIONREASON_FLAPPINGSTART", 2);
define("NOTIFICATIONREASON_FLAPPINGSTOP", 3);
define("NOTIFICATIONREASON_FLAPPINGDISABLED", 4);
define("NOTIFICATIONREASON_DOWNTIMESTART", 5);
define("NOTIFICATIONREASON_DOWNTIMEEND", 6);
define("NOTIFICATIONREASON_DOWNTIMECANCELLED", 7);
define("NOTIFICATIONREASON_CUSTOM", 99);


// CALLBACKS
define("CALLBACK_SESSION_STARTED", "session_started");
define("CALLBACK_PREREQS_PASSED", "prereqs_passed");
define("CALLBACK_AUTHENTICATION_PASSED", "authentication_passed");

define("CALLBACK_PAGE_HEAD", "page_head");
define("CALLBACK_BODY_START", "body_start");
define("CALLBACK_BODY_END", "body_end");
define("CALLBACK_FOOTER_START", "footer_start");
define("CALLBACK_FOOTER_END", "footer_end");
define("CALLBACK_HEADER_START", "header_start");
define("CALLBACK_HEADER_END", "header_end");
define("CALLBACK_CONTENT_START", "content_start");
define("CALLBACK_CONTENT_END", "content_end");
define("CALLBACK_FRAME_START", "frame_start");

define("CALLBACK_EVENT_ADDED", "event_added");
define("CALLBACK_EVENT_PROCESSED", "event_processed");

define("CALLBACK_MENUS_DEFINED", "menus_defined");
define("CALLBACK_MENUS_INITIALIZED", "menus_initialized");
define("CALLBACK_MENUS_INITIALIZED_FINAL", "menus_initialized_final");

define("CALLBACK_SERVICE_TABS_INIT", "service_tabs_init");
define("CALLBACK_HOST_TABS_INIT", "host_tabs_init");

define("CALLBACK_USER_CREATED", "user_created");
define("CALLBACK_USER_PASSWORD_CHANGED", "user_password_changed");
define("CALLBACK_USER_DELETED", "user_deleted");

define("CALLBACK_USER_EDIT_PROCESS", "user_edit_process");
define("CALLBACK_USER_EDIT_HTML_GENERAL_SETTINGS", "user_edit_general_settings");
define("CALLBACK_USER_EDIT_HTML_PREFERENCES", "user_edit_preferences");
define("CALLBACK_USER_EDIT_HTML_AUTH_SETTINGS", "user_edit_auth_settings");
define("CALLBACK_USER_EDIT_HTML_API_SETTINGS", "user_edit_api_settings");
define("CALLBACK_USER_EDIT_HTML_GENERIC", "user_edit_generic");

define("CALLBACK_USER_NOTIFICATION_METHODS_TABS_INIT", "user_notification_methods_tabs_init");
define("CALLBACK_USER_NOTIFICATION_MESSAGES_TABS_INIT", "user_notification_messages_tabs_init");

//status table callbacks
define("CALLBACK_CUSTOM_TABLE_ICONS", 'custom_table_icons');
define("CALLBACK_CUSTOM_TABLE_HEADERS", 'custom_table_headers');
define("CALLBACK_CUSTOM_TABLE_DATA", 'custom_table_data');

define("CALLBACK_CONFIG_SPLASH_SCREEN", "config_splash_screen");

define("CALLBACK_PROCESS_AUTH_INFO", "process_auth_info");
define("CALLBACK_TWO_FACTOR_AUTH", "process_2fa_auth");
define("CALLBACK_TWO_FACTOR_POST", "process_2fa_post");

define("CALLBACK_HOST_DETAIL_ACTION_LINK", "host_detail_action_link");
define("CALLBACK_SERVICE_DETAIL_ACTION_LINK", "service_detail_action_link");

//added callbacks for Core Command API 
define("CALLBACK_CORECMD_SVC_LINK", "core_command_service_link");
define("CALLBACK_CORECMD_HOST_LINK", "core_command_host_link");

define("CALLBACK_REPORTS_ACTION_LINK", "reports_action_link");

define("CALLBACK_HOME_PAGE_OPTIONS", "home_page_options");

//apply configuration callback
define("CALLBACK_SUBSYS_APPLYCONFIG", 'subsys_applyconfig');

// Callback for generating table names
define("CALLBACK_TABLE_NAMES_GENERATED", 'table_names_generated');

//callbacks for subsystem cron jobs
define('CALLBACK_SUBSYS_CLEANER', 'subsys_cleaner');
define('CALLBACK_SUBSYS_DBMAINT', 'subsys_dbmaint');
define('CALLBACK_SUBSYS_GENERIC', 'subsys_generic');

// Callbacks for notification modification
define('CALLBACK_NOTIFICATION_TEMPLATE_EMAIL', 'notification_template_email');
define('CALLBACK_NOTIFICATION_TEMPLATE_SMS', 'notification_template_sms');
define('CALLBACK_NOTIFICATION_EMAIL', 'notification_email');
define('CALLBACK_NOTIFICATION_SMS', 'notification_sms');
define('CALLBACK_NOTIFICATION_EMAIL_SENT', 'notification_email_sent');
define('CALLBACK_NOTIFICATION_SMS_SENT', 'notification_sms_sent');

// Callbacks for CCM
define('CALLBACK_CCM_INSERT_HOSTSERVICE', 'ccm_insert_hostservice');
define('CALLBACK_CCM_MODIFY_HOSTSERVICE', 'ccm_modify_hostservice');
define('CALLBACK_CCM_INSERT_GROUP', 'ccm_insert_group');
define('CALLBACK_CCM_MODIFY_GROUP', 'ccm_modify_group');
define('CALLBACK_CCM_APPLY_MODIFY_HOSTSERVICE', 'ccm_apply_modify_hostservice');

// EVENT SOURCES
define("EVENTSOURCE_OTHER", 0);
define("EVENTSOURCE_NAGIOSXI", 1);
define("EVENTSOURCE_NAGIOSCORE", 2);

// EVENT TYPES
define("EVENTTYPE_OTHER", 0);
define("EVENTTYPE_STATECHANGE", 1);
define("EVENTTYPE_NOTIFICATION", 2);

// EVENT STATUS CODES
define("EVENTSTATUS_QUEUED", 0);
define("EVENTSTATUS_PROCESSING", 1);
define("EVENTSTATUS_COMPLETED", 2);

// MIB UPLOAD MODES
define("MIB_UPLOAD_DO_NOTHING", 0);
define("MIB_UPLOAD_PROCESS_ONLY", 1);
define("MIB_UPLOAD_NXTI", 2);
define("MIB_TYPE_UNKNOWN", 3);

// CONFIG WIZARDS
define("CONFIGWIZARD_API_VERSION", 2); // API version
define("CONFIGWIZARD_TYPE", "type");
define("CONFIGWIZARD_NAME", "name");
define("CONFIGWIZARD_VERSION", "version");
define("CONFIGWIZARD_DATE", "date");
define("CONFIGWIZARD_COPYRIGHT", "copyright");
define("CONFIGWIZARD_AUTHOR", "author");
define("CONFIGWIZARD_DESCRIPTION", "description");
define("CONFIGWIZARD_DISPLAYTITLE", "display_title");
define("CONFIGWIZARD_PREVIEWIMAGE", "preview_image");
define("CONFIGWIZARD_FUNCTION", "callback_function");
define("CONFIGWIZARD_FUNCTION_ARGS", "callback_function_args");
define("CONFIGWIZARD_DISPLAYFUNCTION", "display_function");
define("CONFIGWIZARD_SHOWASAVAILABLE", "show_as_available");
define("CONFIGWIZARD_REQUIRES_ROOT_SETUP", "requires_root_setup");
define("CONFIGWIZARD_FILTER_GROUPS", "filter_groups");

define("CONFIGWIZARD_ERROR_MESSAGES", "error_messages");
define("CONFIGWIZARD_PASSBACK_DATA", "passback_data");
define("CONFIGWIZARD_NAGIOS_OBJECTS", "nagios_objects");
define("CONFIGWIZARD_SKIP_OBJECTS_RECONFIGURE", "skip_reconfigure");

define("CONFIGWIZARD_TYPE_MONITORING", "monitoring");

define("CONFIGWIZARD_MODE_GETSTAGE1HTML", "get_stage1_html");
define("CONFIGWIZARD_MODE_VALIDATESTAGE1DATA", "validate_stage1_data");
define("CONFIGWIZARD_MODE_GETSTAGE2HTML", "get_stage2_html");
define("CONFIGWIZARD_MODE_VALIDATESTAGE2DATA", "validate_stage2_data");
define("CONFIGWIZARD_MODE_GETSTAGE3HTML", "get_stage3_html");
define("CONFIGWIZARD_MODE_GETSTAGE3OPTS", "get_stage3_opts");
define("CONFIGWIZARD_MODE_VALIDATESTAGE3DATA", "validate_stage3_data");
define("CONFIGWIZARD_MODE_GETSTAGE4OPTS", "get_stage4_opts");
define("CONFIGWIZARD_MODE_GETSTAGE5OPTS", "get_stage5_opts");
define("CONFIGWIZARD_MODE_COMMITOK", "commit_ok");
define("CONFIGWIZARD_MODE_COMMITERROR", "commit_error");
define("CONFIGWIZARD_MODE_COMMITPERMSERROR", "commit_perms_error");
define("CONFIGWIZARD_MODE_COMMITCONFIGERROR", "commit_config_error");

define("CONFIGWIZARD_MODE_GETFINALSTAGEHTML", "get_final_stage_html");
define("CONFIGWIZARD_MODE_GETOBJECTS", "get_objects");

define("CONFIGWIZARD_HIDE_OPTIONS", 99); // hiden all/some options
define("CONFIGWIZARD_HIDDEN_OPTIONS", "hidden_options"); // hidden options array
define("CONFIGWIZARD_HIDE_NOTIFICATION_OPTIONS", "hide_notification_options");
define("CONFIGWIZARD_HIDE_NOTIFICATION_DELAY", "hide_notification_delay");
define("CONFIGWIZARD_HIDE_NOTIFICATION_INTERVAL", "hide_notification_interval");
define("CONFIGWIZARD_HIDE_NOTIFICATION_TARGETS", "hide_notification_targets");

define("CONFIGWIZARD_HIDE_HOSTGROUPS", "hide_hostgroups");
define("CONFIGWIZARD_HIDE_SERVICEGROUPS", "hide_servicegroups");
define("CONFIGWIZARD_HIDE_PARENT_HOSTS", "hide_parent_hosts");

define("CONFIGWIZARD_OVERRIDE_OPTIONS", "override_options"); // options to override
define("CONFIGWIZARD_REQUIRES_VERSION", "requires_version");


// NOTIFICATION METHODS
define("NOTIFICATIONMETHOD_FUNCTION", "callback_function");

define("NOTIFICATIONMETHOD_ERROR_MESSAGES", "error_messages");
define("NOTIFICATIONMETHOD_INFO_MESSAGES", "info_messages");

define("NOTIFICATIONMETHOD_MODE_GETCONFIGOPTIONS", "get_config_options");
define("NOTIFICATIONMETHOD_MODE_SETCONFIGOPTIONS", "set_config_options");
define("NOTIFICATIONMETHOD_MODE_GETMESSAGEFORMAT", "get_message_format");
define("NOTIFICATIONMETHOD_MODE_SETMESSAGEFORMAT", "set_message_format");

// MENUS
define("MENU_HOME", "home");
define("MENU_VIEWS", "views");
define("MENU_DASHBOARDS", "dashboards");
define("MENU_REPORTS", "reports");
define("MENU_CONFIGURE", "configure");
define("MENU_CORECONFIGMANAGER", "coreconfigmanager");
define("MENU_HELP", "help");
define("MENU_ADMIN", "admin");
define("MENU_ACCOUNT", "account");
define("MENU_TOOLS", "tools");

define("MENUITEMS", "menuitems");
define("MENUSECTION", "menusection");
define("MENUSECTIONEND", "menusectionend");
define("MENULINK", "link");
define("MENULINKSPACER", "linkspacer");

// AUDIT LOG TYPES
define("AUDITLOGTYPE_NONE", 0);
define("AUDITLOGTYPE_ADD", 1); // Adding objects /users
define("AUDITLOGTYPE_DELETE", 2); // Deleting objects / users
define("AUDITLOGTYPE_MODIFY", 4); // Modifying objects / users
define("AUDITLOGTYPE_MODIFICATION", 4); // Modifying objects / users
define("AUDITLOGTYPE_CHANGE", 8); // Changes (reconfiguring system settings)
define("AUDITLOGTYPE_SYSTEMCHANGE", 8); // Changes (reconfiguring system settings)
define("AUDITLOGTYPE_SECURITY", 16); // Security-related events
define("AUDITLOGTYPE_INFO", 32); // Informational messages
define("AUDITLOGTYPE_OTHER", 64); // Other

// AUDIT LOG SOURCES
define("AUDITLOGSOURCE_USER_INTERFACE", "User Interface");
define("AUDITLOGSOURCE_CCM", "Core Config Manager");
define("AUDITLOGSOURCE_NAGIOSCORE", "Core");
define("AUDITLOGSOURCE_SUBSYSTEM", "Subsystem");
define("AUDITLOGSOURCE_API", "API");
define("AUDITLOGSOURCE_OTHER", "Other");

// PERFDATA/RRD EXPORT TYPES
define('EXPORT_RRD_XML', 	'xml');
define('EXPORT_RRD_JSON', 	'json');
define('EXPORT_RRD_CSV', 	'csv');
define('EXPORT_RRD_ARRAY', 	'array');

// Adding flash message constants for easy memorization
define('FLASH_MSG_INFO', 'info');
define('FLASH_MSG_ERROR', 'error');
define('FLASH_MSG_SUCCESS', 'success');
define('FLASH_MSG_WARNING', 'warning');

// Fix constants for SSL versions in PHP < 5.5
if (!defined('CURL_SSLVERSION_TLSv1_2')) { define('CURL_SSLVERSION_TLSv1_2', 6); }
if (!defined('CURL_SSLVERSION_TLSv1_1')) { define('CURL_SSLVERSION_TLSv1_1', 5); }
if (!defined('CURL_SSLVERSION_TLSv1_0')) { define('CURL_SSLVERSION_TLSv1_0', 4); }
if (!defined('CURL_SSLVERSION_DEFAULT ')) { define('CURL_SSLVERSION_DEFAULT ', 0); }

// Scheduled backups constants
// 0 - Debug, 1 - Info, 2 - Warning, 3 - Error
define('SB_LOGLEVEL', 3);
