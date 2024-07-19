SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


CREATE TABLE IF NOT EXISTS `tbl_command` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `command_name` varchar(255) NOT NULL default '',
  `command_line` text,
  `command_type` tinyint(3) unsigned NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`command_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_contact` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `contact_name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `contactgroups` int(10) unsigned NOT NULL default '0',
  `contactgroups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `host_notifications_enabled` tinyint(3) unsigned NOT NULL default '2',
  `service_notifications_enabled` tinyint(3) unsigned NOT NULL default '2',
  `host_notification_period` int(10) unsigned NOT NULL default '0',
  `service_notification_period` int(10) unsigned NOT NULL default '0',
  `host_notification_options` varchar(20) NOT NULL default '',
  `service_notification_options` varchar(20) NOT NULL default '',
  `host_notification_commands` int(10) unsigned NOT NULL default '0',
  `host_notification_commands_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `service_notification_commands` int(10) unsigned NOT NULL default '0',
  `service_notification_commands_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `can_submit_commands` tinyint(3) unsigned NOT NULL default '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL default '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL default '2',
  `email` varchar(255) default NULL,
  `pager` varchar(255) default NULL,
  `address1` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `address3` varchar(255) default NULL,
  `address4` varchar(255) default NULL,
  `address5` varchar(255) default NULL,
  `address6` varchar(255) default NULL,
  `name` varchar(255) NOT NULL default '',
  `use_variables` tinyint(3) unsigned NOT NULL default '0',
  `use_template` tinyint(3) unsigned NOT NULL default '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`contact_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_contactgroup` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `contactgroup_name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `members` int(10) unsigned NOT NULL default '0',
  `contactgroup_members` int(10) unsigned NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`contactgroup_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_contacttemplate` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `template_name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `contactgroups` int(10) unsigned NOT NULL default '0',
  `contactgroups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `host_notifications_enabled` tinyint(3) unsigned NOT NULL default '2',
  `service_notifications_enabled` tinyint(3) unsigned NOT NULL default '2',
  `host_notification_period` int(11) NOT NULL default '0',
  `service_notification_period` int(11) NOT NULL default '0',
  `host_notification_options` varchar(20) NOT NULL default '',
  `service_notification_options` varchar(20) NOT NULL default '',
  `host_notification_commands` int(10) unsigned NOT NULL default '0',
  `host_notification_commands_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `service_notification_commands` int(10) unsigned NOT NULL default '0',
  `service_notification_commands_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `can_submit_commands` tinyint(3) unsigned NOT NULL default '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL default '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL default '2',
  `email` varchar(255) default NULL,
  `pager` varchar(255) default NULL,
  `address1` varchar(255) default NULL,
  `address2` varchar(255) default NULL,
  `address3` varchar(255) default NULL,
  `address4` varchar(255) default NULL,
  `address5` varchar(255) default NULL,
  `address6` varchar(255) default NULL,
  `use_variables` tinyint(3) unsigned NOT NULL default '0',
  `use_template` tinyint(3) unsigned NOT NULL default '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`template_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_domain` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `domain` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `server` varchar(255) NOT NULL default '',
  `method` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `basedir` varchar(255) NOT NULL default '',
  `hostconfig` varchar(255) NOT NULL default '',
  `serviceconfig` varchar(255) NOT NULL default '',
  `backupdir` varchar(255) NOT NULL default '',
  `hostbackup` varchar(255) NOT NULL default '',
  `servicebackup` varchar(255) NOT NULL default '',
  `nagiosbasedir` varchar(255) NOT NULL default '',
  `importdir` varchar(255) NOT NULL default '',
  `commandfile` varchar(255) NOT NULL default '',
  `binaryfile` varchar(255) NOT NULL default '',
  `pidfile` varchar(255) NOT NULL default '',
  `version` tinyint(3) unsigned NOT NULL default '0',
  `access_rights` varchar(255) NOT NULL default '',
  `active` enum('0','1') NOT NULL default '1',
  `nodelete` enum('0','1') NOT NULL default '0',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;


CREATE TABLE IF NOT EXISTS `tbl_host` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `host_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL default '',
  `display_name` varchar(255) default '',
  `address` varchar(255) NOT NULL default '',
  `parents` tinyint(3) unsigned NOT NULL default '0',
  `parents_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `hostgroups` tinyint(3) unsigned NOT NULL default '0',
  `hostgroups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `check_command` text,
  `use_template` tinyint(3) unsigned NOT NULL default '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `initial_state` varchar(20) default '',
  `max_check_attempts` int(11) default NULL,
  `check_interval` int(11) default NULL,
  `retry_interval` int(11) default NULL,
  `active_checks_enabled` tinyint(3) unsigned NOT NULL default '2',
  `passive_checks_enabled` tinyint(3) unsigned NOT NULL default '2',
  `check_period` int(11) NOT NULL default '0',
  `obsess_over_host` tinyint(3) unsigned NOT NULL default '2',
  `check_freshness` tinyint(3) unsigned NOT NULL default '2',
  `freshness_threshold` int(11) default NULL,
  `event_handler` int(11) NOT NULL default '0',
  `event_handler_enabled` tinyint(3) unsigned NOT NULL default '2',
  `low_flap_threshold` int(11) default NULL,
  `high_flap_threshold` int(11) default NULL,
  `flap_detection_enabled` tinyint(3) unsigned NOT NULL default '2',
  `flap_detection_options` varchar(20) default '',
  `process_perf_data` tinyint(3) unsigned NOT NULL default '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL default '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL default '2',
  `contacts` tinyint(3) unsigned NOT NULL default '0',
  `contacts_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `contact_groups` tinyint(3) unsigned NOT NULL default '0',
  `contact_groups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `notification_interval` int(11) default NULL,
  `notification_period` int(11) NOT NULL default '0',
  `first_notification_delay` int(11) default NULL,
  `notification_options` varchar(20) default '',
  `notifications_enabled` tinyint(3) unsigned NOT NULL default '2',
  `stalking_options` varchar(20) default '',
  `notes` varchar(255) default '',
  `notes_url` varchar(255) default '',
  `action_url` varchar(255) default '',
  `icon_image` varchar(255) default '',
  `icon_image_alt` varchar(255) default '',
  `vrml_image` varchar(255) default '',
  `statusmap_image` varchar(255) default '',
  `2d_coords` varchar(255) default '',
  `3d_coords` varchar(255) default '',
  `use_variables` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`host_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_hostdependency` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `config_name` varchar(255) NOT NULL default '',
  `dependent_host_name` tinyint(3) unsigned NOT NULL default '0',
  `dependent_hostgroup_name` tinyint(3) unsigned NOT NULL default '0',
  `host_name` tinyint(3) unsigned NOT NULL default '0',
  `hostgroup_name` tinyint(3) unsigned NOT NULL default '0',
  `inherits_parent` tinyint(3) unsigned NOT NULL default '0',
  `execution_failure_criteria` varchar(20) default '',
  `notification_failure_criteria` varchar(20) default '',
  `dependency_period` int(11) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_hostescalation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `config_name` varchar(255) NOT NULL default '',
  `host_name` tinyint(3) unsigned NOT NULL default '0',
  `hostgroup_name` tinyint(3) unsigned NOT NULL default '0',
  `contacts` tinyint(3) unsigned NOT NULL default '0',
  `contact_groups` tinyint(3) unsigned NOT NULL default '0',
  `first_notification` int(11) default NULL,
  `last_notification` int(11) default NULL,
  `notification_interval` int(11) default NULL,
  `escalation_period` int(11) NOT NULL default '0',
  `escalation_options` varchar(20) default '',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_hostextinfo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `host_name` int(11) default NULL,
  `notes` varchar(255) NOT NULL default '',
  `notes_url` varchar(255) NOT NULL default '',
  `action_url` varchar(255) NOT NULL default '',
  `statistik_url` varchar(255) NOT NULL default '',
  `icon_image` varchar(255) NOT NULL default '',
  `icon_image_alt` varchar(255) NOT NULL default '',
  `vrml_image` varchar(255) NOT NULL default '',
  `statusmap_image` varchar(255) NOT NULL default '',
  `2d_coords` varchar(255) NOT NULL default '',
  `3d_coords` varchar(255) NOT NULL default '',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`host_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_hostgroup` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `hostgroup_name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `members` tinyint(3) unsigned NOT NULL default '0',
  `hostgroup_members` tinyint(3) unsigned NOT NULL default '0',
  `notes` varchar(255) default NULL,
  `notes_url` varchar(255) default NULL,
  `action_url` varchar(255) default NULL,
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`hostgroup_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_hosttemplate` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `template_name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `parents` tinyint(3) unsigned NOT NULL default '0',
  `parents_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `hostgroups` tinyint(3) unsigned NOT NULL default '0',
  `hostgroups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `check_command` text,
  `use_template` tinyint(3) unsigned NOT NULL default '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `initial_state` varchar(20) default '',
  `max_check_attempts` int(11) default NULL,
  `check_interval` int(11) default NULL,
  `retry_interval` int(11) default NULL,
  `active_checks_enabled` tinyint(3) unsigned NOT NULL default '2',
  `passive_checks_enabled` tinyint(3) unsigned NOT NULL default '2',
  `check_period` int(11) NOT NULL default '0',
  `obsess_over_host` tinyint(3) unsigned NOT NULL default '2',
  `check_freshness` tinyint(3) unsigned NOT NULL default '2',
  `freshness_threshold` int(11) default NULL,
  `event_handler` int(11) NOT NULL default '0',
  `event_handler_enabled` tinyint(3) unsigned NOT NULL default '2',
  `low_flap_threshold` int(11) default NULL,
  `high_flap_threshold` int(11) default NULL,
  `flap_detection_enabled` tinyint(3) unsigned NOT NULL default '2',
  `flap_detection_options` varchar(20) default '',
  `process_perf_data` tinyint(3) unsigned NOT NULL default '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL default '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL default '2',
  `contacts` tinyint(3) unsigned NOT NULL default '0',
  `contacts_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `contact_groups` tinyint(3) unsigned NOT NULL default '0',
  `contact_groups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `notification_interval` int(11) default NULL,
  `notification_period` int(11) NOT NULL default '0',
  `first_notification_delay` int(11) default NULL,
  `notification_options` varchar(20) default '',
  `notifications_enabled` tinyint(3) unsigned NOT NULL default '2',
  `stalking_options` varchar(20) default '',
  `notes` varchar(255) default '',
  `notes_url` varchar(255) default '',
  `action_url` varchar(255) default '',
  `icon_image` varchar(255) default '',
  `icon_image_alt` varchar(255) default '',
  `vrml_image` varchar(255) default '',
  `statusmap_image` varchar(255) default '',
  `2d_coords` varchar(255) default '',
  `3d_coords` varchar(255) default '',
  `use_variables` tinyint(3) unsigned NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`template_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_info` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `key1` varchar(200) NOT NULL default '',
  `key2` varchar(200) NOT NULL default '',
  `version` varchar(50) NOT NULL default '',
  `language` varchar(50) NOT NULL default '',
  `infotext` text,
  PRIMARY KEY  (`id`),
  KEY `keypair` (`key1`,`key2`,`version`,`language`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=223 ;


CREATE TABLE IF NOT EXISTS `tbl_lnkContactgroupToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContactgroupToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContacttemplateToCommandHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContacttemplateToCommandService` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContacttemplateToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContacttemplateToContacttemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL default '0',
  `idTable` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`idMaster`,`idSlave`,`idTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContacttemplateToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContactToCommandHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContactToCommandService` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContactToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContactToContacttemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL default '0',
  `idTable` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`idMaster`,`idSlave`,`idTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkContactToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostdependencyToHostgroup_DH` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostdependencyToHostgroup_H` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostdependencyToHost_DH` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostdependencyToHost_H` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostescalationToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostescalationToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostescalationToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostescalationToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostgroupToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostgroupToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHosttemplateToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHosttemplateToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHosttemplateToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` boolean NOT NULL default 0,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHosttemplateToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` boolean NOT NULL default 0,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHosttemplateToHosttemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL default '0',
  `idTable` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`idMaster`,`idSlave`,`idTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHosttemplateToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostToHosttemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL default '0',
  `idTable` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`idMaster`,`idSlave`,`idTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkHostToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToHostgroup_DH` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToHostgroup_H` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToHost_DH` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToHost_H` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToService_DS` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToService_S` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToServicegroup_DS` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToServicegroup_S` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceescalationToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceescalationToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceescalationToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceescalationToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceescalationToService` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` boolean NOT NULL default 0,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceescalationToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicegroupToService` (
  `idMaster` int(11) NOT NULL,
  `idSlaveH` int(11) NOT NULL default '0',
  `idSlaveHG` int(11) NOT NULL default '0',
  `idSlaveS` int(11) NOT NULL default '0',
  PRIMARY KEY  (`idMaster`,`idSlaveH`,`idSlaveHG`,`idSlaveS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicegroupToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicetemplateToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicetemplateToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicetemplateToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicetemplateToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicetemplateToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicetemplateToServicetemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL default '0',
  `idTable` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`idMaster`,`idSlave`,`idTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServicetemplateToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceToServicetemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL default '0',
  `idTable` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`idMaster`,`idSlave`,`idTable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkServiceToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_lnkTimeperiodToTimeperiod` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tbl_logbook` (
  `id` bigint(20) NOT NULL auto_increment,
  `time` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `user` varchar(255) NOT NULL default '',
  `ipadress` varchar(255) NOT NULL default '',
  `domain` varchar(255) NOT NULL default '',
  `entry` tinytext character set utf8 collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


CREATE TABLE IF NOT EXISTS `tbl_mainmenu` (
  `id` tinyint(4) NOT NULL auto_increment,
  `order_id` tinyint(4) NOT NULL default '0',
  `menu_id` tinyint(4) NOT NULL default '0',
  `item` varchar(20) NOT NULL default '',
  `link` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;


CREATE TABLE IF NOT EXISTS `tbl_service` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `config_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `host_name` tinyint(3) unsigned NOT NULL default '0',
  `host_name_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `hostgroup_name` tinyint(3) unsigned NOT NULL default '0',
  `hostgroup_name_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `service_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `display_name` varchar(255) NOT NULL default '',
  `servicegroups` tinyint(3) unsigned NOT NULL default '0',
  `servicegroups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `use_template` tinyint(3) unsigned NOT NULL default '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `check_command` text,
  `is_volatile` tinyint(3) unsigned NOT NULL default '2',
  `initial_state` varchar(20) NOT NULL default '',
  `max_check_attempts` int(11) default NULL,
  `check_interval` int(11) default NULL,
  `retry_interval` int(11) default NULL,
  `active_checks_enabled` tinyint(3) unsigned NOT NULL default '2',
  `passive_checks_enabled` tinyint(3) unsigned NOT NULL default '2',
  `check_period` int(11) NOT NULL default '0',
  `parallelize_check` tinyint(3) unsigned NOT NULL default '2',
  `obsess_over_service` tinyint(3) unsigned NOT NULL default '2',
  `check_freshness` tinyint(3) unsigned NOT NULL default '2',
  `freshness_threshold` int(11) default NULL,
  `event_handler` int(11) NOT NULL default '0',
  `event_handler_enabled` tinyint(3) unsigned NOT NULL default '2',
  `low_flap_threshold` int(11) default NULL,
  `high_flap_threshold` int(11) default NULL,
  `flap_detection_enabled` tinyint(3) unsigned NOT NULL default '2',
  `flap_detection_options` varchar(20) NOT NULL default '',
  `process_perf_data` tinyint(3) unsigned NOT NULL default '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL default '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL default '2',
  `notification_interval` int(11) default NULL,
  `first_notification_delay` int(11) default NULL,
  `notification_period` int(11) NOT NULL default '0',
  `notification_options` varchar(20) NOT NULL default '',
  `notifications_enabled` tinyint(3) unsigned NOT NULL default '2',
  `contacts` tinyint(3) unsigned NOT NULL default '0',
  `contacts_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `contact_groups` tinyint(3) unsigned NOT NULL default '0',
  `contact_groups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `stalking_options` varchar(20) NOT NULL default '',
  `notes` varchar(255) NOT NULL default '',
  `notes_url` varchar(255) NOT NULL default '',
  `action_url` varchar(255) NOT NULL default '',
  `icon_image` varchar(255) NOT NULL default '',
  `icon_image_alt` varchar(255) NOT NULL default '',
  `use_variables` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_servicedependency` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `config_name` varchar(255) NOT NULL default '',
  `dependent_host_name` tinyint(3) unsigned NOT NULL default '0',
  `dependent_hostgroup_name` tinyint(3) unsigned NOT NULL default '0',
  `dependent_service_description` tinyint(3) unsigned NOT NULL default '0',
  `dependent_servicegroup_name` tinyint(3) unsigned NOT NULL default '0',
  `host_name` tinyint(3) unsigned NOT NULL default '0',
  `hostgroup_name` tinyint(3) unsigned NOT NULL default '0',
  `service_description` tinyint(3) unsigned NOT NULL default '0',
  `servicegroup_name` tinyint(3) unsigned NOT NULL default '0',
  `inherits_parent` tinyint(3) unsigned NOT NULL default '0',
  `execution_failure_criteria` varchar(20) default '',
  `notification_failure_criteria` varchar(20) default '',
  `dependency_period` int(11) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_serviceescalation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `config_name` varchar(255) NOT NULL default '',
  `host_name` tinyint(3) unsigned NOT NULL default '0',
  `hostgroup_name` tinyint(3) unsigned NOT NULL default '0',
  `service_description` tinyint(3) unsigned NOT NULL default '0',
  `servicegroup_name` tinyint(3) unsigned NOT NULL default '0',
  `contacts` tinyint(3) unsigned NOT NULL default '0',
  `contact_groups` tinyint(3) unsigned NOT NULL default '0',
  `first_notification` int(11) default NULL,
  `last_notification` int(11) default NULL,
  `notification_interval` int(11) default NULL,
  `escalation_period` int(11) NOT NULL default '0',
  `escalation_options` varchar(20) default '',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `config_name` (`config_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_serviceextinfo` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `host_name` int(11) default NULL,
  `service_description` int(11) NOT NULL default '0',
  `notes` varchar(255) NOT NULL default '',
  `notes_url` varchar(255) NOT NULL default '',
  `action_url` varchar(255) NOT NULL default '',
  `statistic_url` varchar(255) NOT NULL default '',
  `icon_image` varchar(255) NOT NULL default '',
  `icon_image_alt` varchar(255) NOT NULL default '',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`host_name`,`service_description`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_servicegroup` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `servicegroup_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL default '',
  `members` tinyint(3) unsigned NOT NULL default '0',
  `servicegroup_members` tinyint(3) unsigned NOT NULL default '0',
  `notes` varchar(255) default NULL,
  `notes_url` varchar(255) default NULL,
  `action_url` varchar(255) default NULL,
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`servicegroup_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_servicetemplate` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `template_name` varchar(255) NOT NULL default '',
  `host_name` tinyint(3) unsigned NOT NULL default '0',
  `host_name_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `hostgroup_name` tinyint(3) unsigned NOT NULL default '0',
  `hostgroup_name_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `service_description` varchar(255) NOT NULL default '',
  `display_name` varchar(255) NOT NULL default '',
  `servicegroups` tinyint(3) unsigned NOT NULL default '0',
  `servicegroups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `use_template` tinyint(3) unsigned NOT NULL default '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `check_command` text,
  `is_volatile` tinyint(3) unsigned NOT NULL default '2',
  `initial_state` varchar(20) NOT NULL default '',
  `max_check_attempts` int(11) default NULL,
  `check_interval` int(11) default NULL,
  `retry_interval` int(11) default NULL,
  `active_checks_enabled` tinyint(3) unsigned NOT NULL default '2',
  `passive_checks_enabled` tinyint(3) unsigned NOT NULL default '2',
  `check_period` int(11) NOT NULL default '0',
  `parallelize_check` tinyint(3) unsigned NOT NULL default '2',
  `obsess_over_service` tinyint(3) unsigned NOT NULL default '2',
  `check_freshness` tinyint(3) unsigned NOT NULL default '2',
  `freshness_threshold` int(11) default NULL,
  `event_handler` int(11) NOT NULL default '0',
  `event_handler_enabled` tinyint(3) unsigned NOT NULL default '2',
  `low_flap_threshold` int(11) default NULL,
  `high_flap_threshold` int(11) default NULL,
  `flap_detection_enabled` tinyint(3) unsigned NOT NULL default '2',
  `flap_detection_options` varchar(20) NOT NULL default '',
  `process_perf_data` tinyint(3) unsigned NOT NULL default '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL default '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL default '2',
  `notification_interval` int(11) default NULL,
  `first_notification_delay` int(11) default NULL,
  `notification_period` int(11) NOT NULL default '0',
  `notification_options` varchar(20) NOT NULL default '',
  `notifications_enabled` tinyint(3) unsigned NOT NULL default '2',
  `contacts` tinyint(3) unsigned NOT NULL default '0',
  `contacts_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `contact_groups` tinyint(3) unsigned NOT NULL default '0',
  `contact_groups_tploptions` tinyint(3) unsigned NOT NULL default '2',
  `stalking_options` varchar(20) NOT NULL default '',
  `notes` varchar(255) NOT NULL default '',
  `notes_url` varchar(255) NOT NULL default '',
  `action_url` varchar(255) NOT NULL default '',
  `icon_image` varchar(255) NOT NULL default '',
  `icon_image_alt` varchar(255) NOT NULL default '',
  `use_variables` tinyint(3) unsigned NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `config_name` (`template_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_settings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `category` varchar(20) NOT NULL default '',
  `name` varchar(30) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;


CREATE TABLE IF NOT EXISTS `tbl_submenu` (
  `id` tinyint(4) NOT NULL auto_increment,
  `id_main` tinyint(4) NOT NULL default '0',
  `order_id` tinyint(4) NOT NULL default '0',
  `item` varchar(20) NOT NULL default '',
  `link` varchar(50) NOT NULL default '',
  `access_rights` varchar(8) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;


CREATE TABLE IF NOT EXISTS `tbl_timedefinition` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tipId` int(10) unsigned NOT NULL default '0',
  `definition` varchar(255) NOT NULL default '',
  `range` text,
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_timeperiod` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `timeperiod_name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `exclude` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `active` enum('0','1') NOT NULL default '1',
  `last_modified` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `access_rights` varchar(8) default NULL,
  `config_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `timeperiod_name` (`timeperiod_name`,`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 PACK_KEYS=0 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `access_rights` varchar(8) default NULL,
  `wsauth` enum('0','1') NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '0',
  `nodelete` enum('0','1') NOT NULL default '0',
  `last_login` timestamp NOT NULL DEFAULT '1970-02-02 01:01:01' ON UPDATE current_timestamp,
  `last_modified` datetime NOT NULL DEFAULT '1970-02-02 01:01:01',
  `locale` varchar(6) DEFAULT 'en_EN',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


CREATE TABLE IF NOT EXISTS `tbl_variabledefinition` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `last_modified` datetime NOT NULL DEFAULT '1970-02-02 01:01:01',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
