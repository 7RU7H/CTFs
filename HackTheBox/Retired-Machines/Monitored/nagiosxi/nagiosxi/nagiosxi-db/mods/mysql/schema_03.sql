# Update user passwords to 255 chars
ALTER TABLE xi_users MODIFY username VARCHAR(255) NOT NULL;

# Add auth tokens table
CREATE TABLE IF NOT EXISTS `xi_auth_tokens` (
    `auth_token_id` int auto_increment,
    `auth_user_id` int NOT NULL,
    `auth_session_id` int NOT NULL,
    `auth_token` varchar(128),
    `auth_valid_until` datetime,
    `auth_expires_at` datetime,
    `auth_restrictions` mediumtext,
    `auth_used` smallint default 0,
    primary key (`auth_token_id`),
    unique (`auth_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

# Add sessions
CREATE TABLE IF NOT EXISTS `xi_sessions` (
    `session_id` int auto_increment,
    `session_phpid` varchar(128),
    `session_created` datetime,
    `session_user_id` int NOT NULL,
    `session_address` varchar(128),
    `session_page` varchar(255),
    `session_data` text,
    `session_last_active` timestamp,
    primary key (`session_id`),
    unique (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

# Add trapdata tables to db

CREATE TABLE IF NOT EXISTS `xi_cmp_trapdata` (
    `trapdata_id` int auto_increment,
    `trapdata_updated` timestamp ON UPDATE current_timestamp,
    `trapdata_enabled` smallint default 1,
    `trapdata_event_name` varchar(128) NOT NULL,
    `trapdata_event_oid` varchar(256) NOT NULL,
    `trapdata_category` varchar(128) NOT NULL,
    `trapdata_severity` varchar(64) NOT NULL,
    `trapdata_format_string` text,
    `trapdata_exec` text,
    `trapdata_desc` text,
    primary key (`trapdata_id`),
    unique (`trapdata_event_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `xi_cmp_trapdata_log` (
    `trapdata_log_id` int auto_increment,
    `trapdata_log_event_name` varchar(128) NOT NULL,
    `trapdata_log_event_oid` varchar(50) NOT NULL,
    `trapdata_log_numeric_oid` varchar(100),
    `trapdata_log_symbolic_oid` varchar(100),
    `trapdata_log_community` varchar(20),
    `trapdata_log_trap_hostname` varchar(100),
    `trapdata_log_trap_ip` varchar(16),
    `trapdata_log_agent_hostname` varchar(100),
    `trapdata_log_agent_IP` varchar(16),
    `trapdata_log_category` varchar(20) NOT NULL,
    `trapdata_log_severity` varchar(20) NOT NULL,
    `trapdata_log_uptime` varchar(20) NOT NULL,
    `trapdata_log_datetime` datetime,
    `trapdata_log_bindings` text,
    primary key (`trapdata_log_id`),
    unique (`trapdata_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
