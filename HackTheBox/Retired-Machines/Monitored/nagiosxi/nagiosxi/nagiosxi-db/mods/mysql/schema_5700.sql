
CREATE TABLE IF NOT EXISTS `xi_cmp_ccm_backups` (
    `config_id` int auto_increment,
    `config_creator` int,
    `config_name` varchar(200),
    `config_date` timestamp,
    `archived` smallint default 0,
    primary key (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `xi_cmp_nagiosbpi_backups` (
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

CREATE TABLE IF NOT EXISTS `xi_cmp_favorites` (
    `item_id` int NOT NULL auto_increment,
    `user_id` int default 0 NOT NULL,
    `title` varchar(63) NOT NULL,
    `partial_href` text NOT NULL,
    INDEX user_menu_index (user_id),
    PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
