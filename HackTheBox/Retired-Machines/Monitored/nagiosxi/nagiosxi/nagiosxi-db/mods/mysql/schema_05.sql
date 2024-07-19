CREATE TABLE IF NOT EXISTS `nagiosxi`.`xi_mibs` (
    `mib_id` int auto_increment,
    `mib_name` varchar(64),
    `mib_uploaded` datetime,
    `mib_last_processed` datetime,
    `mib_type` enum('upload', 'process_manual', 'process_nxti'),
    primary key (`mib_id`),
    unique (`mib_id`),
    unique (`mib_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE xi_cmp_trapdata ADD trapdata_parent_mib_name varchar(64);