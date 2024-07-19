
DELIMITER $$

DROP PROCEDURE IF EXISTS verify_upgraded_ndo $$
CREATE PROCEDURE verify_upgraded_ndo()
BEGIN

# Add the importance columns to nagios_services, nagios_hosts, angios_contacts if
# they don't already exist for some reason

IF NOT EXISTS (
	(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
     AND COLUMN_NAME='importance' AND TABLE_NAME='nagios_services') 
) THEN
	ALTER TABLE `nagios_services` ADD COLUMN `importance` int NOT NULL DEFAULT 0;
END IF;

IF NOT EXISTS (
	(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
     AND COLUMN_NAME='importance' AND TABLE_NAME='nagios_hosts') 
) THEN
	ALTER TABLE `nagios_hosts` ADD COLUMN `importance` int NOT NULL DEFAULT 0;
END IF;

IF NOT EXISTS (
	(SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
     AND COLUMN_NAME='importance' AND TABLE_NAME='nagios_contacts') 
) THEN
	ALTER TABLE `nagios_contacts` ADD COLUMN `importance` int NOT NULL DEFAULT 0;
END IF;

# Create the nagios_service_parentservices table if it doesn't already exist

CREATE TABLE IF NOT EXISTS `nagios_service_parentservices` (
  `service_parentservice_id` int(11) NOT NULL auto_increment,
  `instance_id` smallint(6) NOT NULL default '0',
  `service_id` int(11) NOT NULL default '0',
  `parent_service_object_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`service_parentservice_id`),
  UNIQUE KEY `instance_id` (`service_id`,`parent_service_object_id`)
) ENGINE=MyISAM  COMMENT='Parent services';

END $$

CALL verify_upgraded_ndo() $$

DELIMITER ;
