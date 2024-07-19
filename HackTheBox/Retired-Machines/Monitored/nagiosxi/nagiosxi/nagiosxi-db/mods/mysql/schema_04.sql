# Add missing trapdata table columns and remove unused ones

ALTER TABLE xi_cmp_trapdata ADD COLUMN trapdata_custom_format text;
ALTER TABLE xi_cmp_trapdata ADD COLUMN trapdata_raw_data text;
ALTER TABLE xi_cmp_trapdata ADD COLUMN trapdata_wizard_integration_enabled smallint default 0;
ALTER TABLE xi_cmp_trapdata ADD COLUMN trapdata_wizard_integration_data text;

ALTER TABLE xi_cmp_trapdata DROP COLUMN trapdata_format_string;