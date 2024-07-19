SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

ALTER TABLE tbl_servicetemplate MODIFY max_check_attempts int(11) DEFAULT NULL;
ALTER TABLE tbl_servicetemplate MODIFY check_interval int(11) DEFAULT NULL;
ALTER TABLE tbl_servicetemplate MODIFY retry_interval int(11) DEFAULT NULL;
