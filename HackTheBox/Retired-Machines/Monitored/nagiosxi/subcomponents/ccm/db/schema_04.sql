SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

# Create new linking tables

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

CREATE TABLE IF NOT EXISTS `tbl_lnkServiceescalationToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY  (`idMaster`,`idSlave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Add columns to the current service dependency table

ALTER TABLE tbl_servicedependency ADD COLUMN dependent_servicegroup_name tinyint(3) unsigned NOT NULL DEFAULT 0;
ALTER TABLE tbl_servicedependency ADD COLUMN servicegroup_name tinyint(3) unsigned NOT NULL DEFAULT 0;

# Add columns to host templates for excluding hosts and hostgroups

ALTER TABLE tbl_lnkHosttemplateToHost ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
ALTER TABLE tbl_lnkHosttemplateToHostgroup ADD COLUMN exclude boolean NOT NULL DEFAULT 0;

# Add columns to service escalations for servicegroups

ALTER TABLE tbl_serviceescalation ADD COLUMN servicegroup_name tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER service_description;
