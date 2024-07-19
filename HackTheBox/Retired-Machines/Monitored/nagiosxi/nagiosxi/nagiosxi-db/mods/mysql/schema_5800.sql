ALTER TABLE xi_cmp_ccm_backups ADD config_dir varchar(200);
ALTER TABLE xi_cmp_ccm_backups ADD config_hash varchar(50);
ALTER TABLE xi_cmp_ccm_backups ADD config_changes text;
ALTER TABLE xi_cmp_ccm_backups ADD config_diff text;

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
