USE `nagiosxi`;

INSERT INTO xi_options (`name`, `value`) VALUES ('url', 'http://localhost/nagiosxi/');
INSERT INTO xi_options (`name`, `value`) VALUES ('default_language', 'en');
INSERT INTO xi_options (`name`, `value`) VALUES ('default_theme', 'none');
INSERT INTO xi_options (`name`, `value`) VALUES ('auto_update_check', '1');
INSERT INTO xi_options (`name`, `value`) VALUES ('default_date_format', '1');
INSERT INTO xi_options (`name`, `value`) VALUES ('default_number_format', '1');

INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('dbmaint', 'a:1:{s:10:"last_check";i:1255437004;}', '2009-10-13 07:30:04.1862');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('cleaner', 'a:1:{s:10:"last_check";i:1255437004;}', '2009-10-13 07:30:04.729231');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('nom', 'a:1:{s:10:"last_check";i:1255437004;}', '2009-10-13 07:30:04.206198');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('reportengine', 'a:1:{s:10:"last_check";i:1255437033;}', '2009-10-13 07:30:33.584388');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('dbbackend', 'a:5:{s:12:"last_checkin";s:19:"2009-10-13 07:29:33";s:15:"bytes_processed";s:8:"36359614";s:17:"entries_processed";s:6:"305085";s:12:"connect_time";s:19:"2009-10-12 18:42:59";s:15:"disconnect_time";s:19:"0000-00-00 00:00:00";}', '2009-10-13 07:30:26.779238');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('daemons', 'a:3:{s:10:"nagioscore";a:4:{s:6:"daemon";s:6:"nagios";s:6:"output";s:32:"nagios (pid 32440) is running...";s:11:"return_code";i:0;s:6:"status";i:0;}s:3:"pnp";a:4:{s:6:"daemon";s:4:"npcd";s:6:"output";s:13:"NPCD running.";s:11:"return_code";i:0;s:6:"status";i:0;}s:8:"ndoutils";a:4:{s:6:"daemon";s:6:"ndo2db";s:6:"output";s:32:"ndo2db (pid 15293) is running...";s:11:"return_code";i:0;s:6:"status";i:0;}}', '2009-10-13 07:30:26.936026');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('nagioscore', 'a:9:{s:15:"hostcheckevents";a:3:{s:4:"1min";s:1:"0";s:4:"5min";s:2:"13";s:5:"15min";s:2:"13";}s:18:"servicecheckevents";a:3:{s:4:"1min";s:2:"13";s:4:"5min";s:2:"41";s:5:"15min";s:2:"44";}s:11:"timedevents";a:3:{s:4:"1min";s:2:"42";s:4:"5min";s:3:"176";s:5:"15min";s:3:"185";}s:16:"activehostchecks";a:3:{s:4:"1min";s:1:"0";s:4:"5min";s:2:"13";s:5:"15min";s:2:"13";}s:17:"passivehostchecks";a:3:{s:4:"1min";s:1:"0";s:4:"5min";s:1:"0";s:5:"15min";s:1:"0";}s:19:"activeservicechecks";a:3:{s:4:"1min";s:2:"20";s:4:"5min";s:2:"76";s:5:"15min";s:2:"82";}s:20:"passiveservicechecks";a:3:{s:4:"1min";s:1:"0";s:4:"5min";s:1:"0";s:5:"15min";s:1:"0";}s:19:"activehostcheckperf";a:6:{s:11:"min_latency";s:5:"0.108";s:11:"max_latency";s:5:"0.271";s:11:"avg_latency";s:16:"0.19123076923077";s:18:"min_execution_time";s:7:"0.01779";s:18:"max_execution_time";s:7:"3.02042";s:18:"avg_execution_time";s:16:"0.37582384615385";}s:22:"activeservicecheckperf";a:6:{s:11:"min_latency";s:5:"0.009";s:11:"max_latency";s:4:"0.32";s:11:"avg_latency";s:16:"0.14542424242424";s:18:"min_execution_time";s:7:"0.01816";s:18:"max_execution_time";s:7:"4.12315";s:18:"avg_execution_time";s:16:"0.65895454545455";}}', '2009-10-13 07:30:26.978483');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('load', 'a:3:{s:5:"load1";s:4:"1.06";s:5:"load5";s:4:"0.90";s:6:"load15";s:4:"0.81";}', '2009-10-13 07:30:27.002811');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('memory', 'a:6:{s:5:"total";s:3:"501";s:4:"used";s:3:"449";s:4:"free";s:2:"51";s:6:"shared";s:1:"0";s:7:"buffers";s:2:"11";s:6:"cached";s:3:"178";}', '2009-10-13 07:30:27.032445');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('swap', 'a:3:{s:5:"total";s:4:"1043";s:4:"used";s:2:"74";s:4:"free";s:3:"968";}', '2009-10-13 07:30:27.053822');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('iostat', 'a:6:{s:4:"user";s:5:"12.40";s:4:"nice";s:4:"1.60";s:6:"system";s:4:"3.40";s:6:"iowait";s:4:"0.80";s:5:"steal";s:4:"0.00";s:4:"idle";s:5:"81.80";}', '2009-10-13 07:30:32.095978');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('sysstat', 'a:1:{s:10:"last_check";i:1255437026;}', '2009-10-13 07:30:32.143179');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('cmdsubsys', 'a:1:{s:10:"last_check";i:1255437034;}', '2009-10-13 07:30:34.160423');
INSERT INTO xi_sysstat (`metric`, `value`, `update_time`) VALUES ('feedprocessor', 'a:1:{s:10:"last_check";i:1255437034;}', '2009-10-13 07:30:34.104182');

INSERT INTO xi_usermeta (`user_id`, `keyname`, `keyvalue`, `autoload`) VALUES (1, 'userlevel', '255', 1);

INSERT INTO xi_users (`user_id`, `username`, `password`, `name`, `email`, `backend_ticket`, `enabled`, `api_key`, `api_enabled`) VALUES (1, 'nagiosadmin', '40be4e59b9a2a2b5dffb918c0e86b3d7', 'Nagios Admin', 'root@localhost', '1234', 1, MD5(LEFT(UUID(), 8)), 1);