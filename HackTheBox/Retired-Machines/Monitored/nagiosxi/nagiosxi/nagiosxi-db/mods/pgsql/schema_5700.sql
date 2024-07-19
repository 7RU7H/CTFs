
CREATE SEQUENCE xi_cmp_ccm_backups_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE xi_cmp_ccm_backups (
    config_id integer DEFAULT nextval('xi_cmp_ccm_backups_id_seq'::regclass) NOT NULL,
    config_creator integer,
    config_name character varying(200),
    config_date timestamp,
    archived smallint DEFAULT 0,
    PRIMARY KEY (config_id)
);

CREATE SEQUENCE xi_cmp_nagiosbpi_backups_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE xi_cmp_nagiosbpi_backups (
    config_id integer DEFAULT nextval('xi_cmp_nagiosbpi_backups_id_seq'::regclass) NOT NULL,
    config_creator integer,
    config_name character varying(200),
    config_file character varying(64),
    config_hash character varying(50),
    config_changes text,
    config_diff text,
    config_date timestamp,
    archived smallint DEFAULT 0,
    PRIMARY KEY (config_id)
);

CREATE SEQUENCE xi_deploy_jobs_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE xi_deploy_jobs (
    job_id integer DEFAULT nextval('xi_deploy_jobs_id_seq'::regclass) NOT NULL,
    job_name character varying(64),
    creator_id integer,
    version character varying(10),
    os character varying(24),
    addresses text,
    ncpa_token text,
    username character varying(64),
    password text,
    vault_password text,
    sudo smallint DEFAULT 0,
    status integer,
    pid integer,
    metadata text,
    PRIMARY KEY (job_id)
);

CREATE SEQUENCE xi_deploy_agents_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE xi_deploy_agents (
    deploy_id integer DEFAULT nextval('xi_deploy_agents_id_seq'::regclass) NOT NULL,
    creator_id integer,
    deployed_date timestamp,
    last_updated_date timestamp,
    last_status_check timestamp,
    available smallint DEFAULT 0,
    version character varying(10),
    address character varying(60),
    hostname character varying(250),
    os character varying(24),
    metadata text,
    PRIMARY KEY (deploy_id)
);

CREATE SEQUENCE xi_cmp_favorites_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE xi_cmp_favorites (
    item_id integer DEFAULT nextval('xi_cmp_favorites_id_seq'::regclass) NOT NULL,
    user_id integer DEFAULT 0,
    title character varying(63),
    partial_href text,
    PRIMARY KEY (item_id)
);
