SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

# Add columns to service escalation to allow exclude services

ALTER TABLE tbl_lnkServiceescalationToService ADD COLUMN exclude boolean NOT NULL DEFAULT 0;
