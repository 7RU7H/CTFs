CREATE TABLE IF NOT EXISTS `xi_eventqueue` (
    `eventqueue_id` int auto_increment,
    `event_time` timestamp,
    `event_source` smallint,
    `event_type` smallint default 0 not null,
    `event_meta` text,
    primary key (`eventqueue_id`),
    unique(`eventqueue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;