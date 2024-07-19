SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


CREATE TABLE IF NOT EXISTS `tbl_session` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `session_id` varchar(120) NOT NULL DEFAULT '',
  `ip` varchar(64) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `obj_id` int(10) unsigned NOT NULL DEFAULT 0,
  `started` varchar(20) NOT NULL DEFAULT '',
  `last_updated` varchar(20) NOT NULL DEFAULT '',
  `active` boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tbl_session_locks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sid` int(10) unsigned NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT '',
  `obj_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE tbl_lnkServiceToHost ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkServiceToHostgroup ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkServicetemplateToHost ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkServicetemplateToHostgroup ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkServiceescalationToHost ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkServiceescalationToHostgroup ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkHostescalationToHost ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkHostescalationToHostgroup ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkHostgroupToHost ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkHostgroupToHostgroup ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
