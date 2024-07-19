
# Create "nagiosxi" default database schema

CREATE DATABASE `nagiosxi`;
CREATE USER 'nagiosxi'@'localhost' IDENTIFIED BY 'n@gweb';
GRANT ALL ON `nagiosxi`.* TO 'nagiosxi'@'localhost';
GRANT PROCESS ON *.* TO 'nagiosxi'@'localhost';
USE `nagiosxi`;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_auditlog` (
    `auditlog_id` int auto_increment,
    `log_time` timestamp,
    `source` text,
    `user` varchar(200),
    `type` int,
    `message` text,
    `ip_address` varchar(45),
    `details` text,
    primary key (`auditlog_id`),
    index using btree (`log_time`),
    index using btree (`user`),
    index using btree (`type`),
    index using btree (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_commands` (
    `command_id` int auto_increment,
    `group_id` int default 0,
    `submitter_id` int default 0,
    `beneficiary_id` int default 0,
    `command` int NOT NULL default 0,
    `submission_time` datetime,
    `event_time` datetime,
    `frequency_type` int default 0,
    `frequency_units` int default 0,
    `frequency_interval` int default 0,
    `processing_time` datetime,
    `status_code` int default 0,
    `result_code` int default 0,
    `command_data` text,
    `result` text,
    primary key (`command_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_events` (
    `event_id` int auto_increment,
    `event_time` datetime,
    `event_source` smallint,
    `event_type` smallint default 0 NOT NULL,
    `status_code` smallint default 0 NOT NULL,
    `processing_time` datetime,
    primary key (`event_id`),
    index using btree (`event_source`),
    index using btree (`event_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_meta` (
    `meta_id` int auto_increment,
    `metatype_id` int default 0,
    `metaobj_id` int default 0,
    `keyname` varchar(128) not null default '',
    `keyvalue` text,
    primary key (`meta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_options` (
    `option_id` int auto_increment,
    `name` varchar(128) not null default '',
    `value` text,
    primary key (`option_id`),
    index using btree (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_sysstat` (
    `sysstat_id` int auto_increment,
    `metric` varchar(128) not null default '',
    `value` text,
    `update_time` timestamp,
    primary key (`sysstat_id`),
    index using btree (`metric`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_usermeta` (
    `usermeta_id` int auto_increment,
    `user_id` int not null,
    `keyname` varchar(255) not null,
    `keyvalue` longtext,
    `autoload` smallint default 0,
    primary key (`usermeta_id`),
    index using btree (`autoload`),
    constraint `user_unique_key` unique (`user_id`, `keyname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_users` (
    `user_id` int auto_increment,
    `username` varchar(255) not null default '',
    `password` varchar(128) not null default '',
    `name` varchar(100),
    `email` varchar(128) not null,
    `backend_ticket` varchar(128),
    `enabled` smallint default 1,
    `api_key` varchar(128),
    `api_enabled` smallint default 0 not null,
    `login_attempts` smallint(6) default 0 not null,
    `last_attempt` int(11) default 0 not null,
    `last_password_change` int(11) default 0 not null,
    `last_login` int(11) default 0 not null,
    `last_edited` int(11) default 0 not null,
    `last_edited_by` int(11) default 0 not null,
    `created_by` int(11) default 0 not null,
    `created_time` int(11) default 0 not null,
    primary key (`user_id`),
    unique(`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_eventqueue` (
    `eventqueue_id` int auto_increment,
    `event_time` timestamp,
    `event_source` smallint,
    `event_type` smallint default 0 not null,
    `event_meta` text,
    primary key (`eventqueue_id`),
    unique(`eventqueue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_auth_tokens` (
    `auth_token_id` int auto_increment,
    `auth_user_id` int not null,
    `auth_session_id` int not null,
    `auth_token` varchar(128),
    `auth_valid_until` datetime,
    `auth_expires_at` datetime,
    `auth_restrictions` mediumtext,
    `auth_used` smallint default 0 not null,
    primary key (`auth_token_id`),
    unique (`auth_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_sessions` (
    `session_id` int auto_increment,
    `session_phpid` varchar(128),
    `session_created` datetime,
    `session_user_id` int not null,
    `session_address` varchar(128),
    `session_page` varchar(255),
    `session_data` text,
    `session_last_active` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE current_timestamp,
    primary key (`session_id`),
    unique (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_cmp_trapdata` (
    `trapdata_id` int auto_increment,
    `trapdata_updated` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE current_timestamp,
    `trapdata_enabled` smallint default 1,
    `trapdata_event_name` varchar(128) NOT NULL default '',
    `trapdata_event_oid` varchar(256) NOT NULL default '',
    `trapdata_category` varchar(128) NOT NULL default '',
    `trapdata_severity` varchar(64) NOT NULL default '',
    `trapdata_custom_format` text,
    `trapdata_raw_data` text,
    `trapdata_exec` text,
    `trapdata_desc` text,
    `trapdata_wizard_integration_enabled` smallint default 0,
    `trapdata_wizard_integration_data` text,
    `trapdata_parent_mib_name` varchar(128),
    primary key (`trapdata_id`),
    unique (`trapdata_event_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_cmp_trapdata_log` (
    `trapdata_log_id` int auto_increment,
    `trapdata_log_event_name` varchar(128) NOT NULL default '',
    `trapdata_log_event_oid` varchar(50) NOT NULL default '',
    `trapdata_log_numeric_oid` varchar(100),
    `trapdata_log_symbolic_oid` varchar(100),
    `trapdata_log_community` varchar(20),
    `trapdata_log_trap_hostname` varchar(100),
    `trapdata_log_trap_ip` varchar(16),
    `trapdata_log_agent_hostname` varchar(100),
    `trapdata_log_agent_IP` varchar(16),
    `trapdata_log_category` varchar(20) NOT NULL default '',
    `trapdata_log_severity` varchar(20) NOT NULL default '',
    `trapdata_log_uptime` varchar(20) NOT NULL default '',
    `trapdata_log_datetime` datetime,
    `trapdata_log_bindings` text,
    primary key (`trapdata_log_id`),
    unique (`trapdata_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_mibs` (
    `mib_id` int auto_increment,
    `mib_name` varchar(128),
    `mib_uploaded` datetime,
    `mib_last_processed` datetime,
    `mib_type` enum('upload', 'process_manual', 'process_nxti'),
    primary key (`mib_id`),
    unique (`mib_id`),
    unique (`mib_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `xi_cmp_ccm_backups` (
    `config_id` int auto_increment,
    `config_creator` int,
    `config_name` varchar(200),
    `config_dir` varchar(200),
    `config_hash` varchar(50),
    `config_changes` text,
    `config_diff` text,
    `config_date` timestamp,
    `archived` smallint default 0,
    primary key (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_cmp_nagiosbpi_backups` (
    `config_id` int auto_increment,
    `config_creator` int,
    `config_name` varchar(200),
    `config_file` varchar(64),
    `config_hash` varchar(50),
    `config_changes` text,
    `config_diff` text,
    `config_date` timestamp,
    `archived` smallint default 0,
    primary key (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

# Auto deploy portion

CREATE TABLE IF NOT EXISTS `xi_deploy_jobs` (
    `job_id` int auto_increment,
    `job_name` varchar(64),
    `creator_id` int,
    `version` varchar(10),
    `os` varchar(24),
    `addresses` text,
    `ncpa_token` text,
    `username` varchar(64),
    `password` text,
    `vault_password` text,
    `sudo` smallint default 0 NOT NULL,
    `status` int,
    `pid` int,
    `metadata` text,
    primary key (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `xi_deploy_agents` (
    `deploy_id` int auto_increment,
    `creator_id` int,
    `deployed_date` datetime,
    `last_updated_date` datetime,
    `last_status_check` datetime,
    `available` smallint default 0 NOT NULL,
    `version` varchar(10),
    `address` varchar(60),
    `hostname` varchar(250),
    `os` varchar(24),
    `metadata` text,
    primary key (`deploy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

# Favorites component

CREATE TABLE IF NOT EXISTS `xi_cmp_favorites` (
    `item_id` int NOT NULL auto_increment,
    `user_id` int default 0 NOT NULL,
    `title` varchar(63) NOT NULL,
    `partial_href` text NOT NULL,
    INDEX user_menu_index (user_id),
    PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

# Scheduled reporting component

CREATE TABLE IF NOT EXISTS `xi_cmp_scheduledreports_log` (
    `log_id` int auto_increment,
    `report_name` text,
    `report_run` datetime,
    `report_user_id` int,
    `report_status` smallint default 0,
    `report_type` smallint,
    `report_run_type` smallint,
    `report_recipients` text,
    primary key (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_banner_messages` (
    `msg_id` int auto_increment,
    `message` varchar(2500) NOT NULL default '',
    `time_created` datetime,
    `created_by` int NOT NULL default 0,
    `acknowledgeable` BOOLEAN default 1,
    `specify_users` BOOLEAN default 0,
    `banner_color` varchar(40) default 'banner_message_banner_info',
    `message_active` BOOLEAN default 1,
    `start_date` DATE default '0001-01-01',
    `end_date` DATE default '0001-01-01',
    `feature_active` BOOLEAN default 1,
    `schedule_message` BOOLEAN default 0,
    primary key (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_link_users_messages` (
    `id` int auto_increment,
    `msg_id` int NOT NULL,
    `user_id` int NOT NULL,
    `acknowledged` BOOLEAN default 0,
    `specified` BOOLEAN default 0,
    primary key (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
