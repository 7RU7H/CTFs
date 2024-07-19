ALTER TABLE xi_cmp_ccm_backups ADD COLUMN config_dir character varying(200);
ALTER TABLE xi_cmp_ccm_backups ADD COLUMN config_hash character varying(50);
ALTER TABLE xi_cmp_ccm_backups ADD COLUMN config_changes text;
ALTER TABLE xi_cmp_ccm_backups ADD COLUMN config_diff text;

CREATE SEQUENCE xi_cmp_scheduledreports_log_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE xi_cmp_scheduledreports_log (
    log_id integer DEFAULT nextval('xi_cmp_scheduledreports_log_id_seq'::regclass) NOT NULL,
    report_name text,
    report_run timestamp,
    report_user_id integer,
    report_status smallint DEFAULT 0,
    report_type smallint,
    report_run_type smallint,
    report_recipients text,
    PRIMARY KEY (log_id)
);
