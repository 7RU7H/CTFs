
CREATE TYPE e_mib_type as ENUM (
    'upload',
    'process_manual',
    'process_nxti'
);

CREATE SEQUENCE xi_mibs_mib_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE xi_mibs (
    mib_id integer DEFAULT nextval('xi_mibs_mib_id_seq'::regclass) NOT NULL,
    mib_name character varying(64) NOT NULL,
    mib_uploaded timestamp,
    mib_last_processed timestamp,
    mib_type e_mib_type,
    PRIMARY KEY (mib_id),
    UNIQUE (mib_id),
    UNIQUE (mib_name)
);

ALTER TABLE xi_cmp_trapdata ADD COLUMN trapdata_parent_mib_name character varying(64);