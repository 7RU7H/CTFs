
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
