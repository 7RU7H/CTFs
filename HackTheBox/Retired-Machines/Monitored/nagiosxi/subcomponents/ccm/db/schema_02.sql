SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


CREATE TABLE IF NOT EXISTS `tbl_permission` (
  `user_id` int(11) unsigned,
  `object_id` int(11) unsigned,
  `type` int(2) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`object_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `tbl_permission_inactive` (
  `user_id` int(11) unsigned,
  `object_id` int(11) unsigned,
  `type` int(2) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`object_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

