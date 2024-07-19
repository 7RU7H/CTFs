SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

# Set collate to utf8_bin on the host/services table to handle case sensitivity in object names...

ALTER TABLE tbl_host MODIFY `host_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '';
ALTER TABLE tbl_service MODIFY `config_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '';
ALTER TABLE tbl_service MODIFY `service_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '';
