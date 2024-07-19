
-- Alter username length
ALTER TABLE xi_users ALTER COLUMN username TYPE character varying(255);

-- Trap data sequences

CREATE SEQUENCE xi_cmp_trapdata_trapdata_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE SEQUENCE xi_cmp_trapdata_trapdata_log_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

-- Add trap data tables

CREATE TABLE xi_cmp_trapdata (
    trapdata_id integer DEFAULT nextval('xi_cmp_trapdata_trapdata_id_seq'::regclass) NOT NULL,
    trapdata_updated timestamp,
    trapdata_enabled smallint DEFAULT 1,
    trapdata_event_name character varying(128) NOT NULL,
    trapdata_event_oid character varying(256) NOT NULL,
    trapdata_category character varying(128) NOT NULL,
    trapdata_severity character varying(64) NOT NULL,
    trapdata_format_string text,
    trapdata_exec text,
    trapdata_desc text,
    PRIMARY KEY (trapdata_id),
    UNIQUE (trapdata_event_name)
);

CREATE TABLE xi_cmp_trapdata_log (
    trapdata_log_id integer DEFAULT nextval('xi_cmp_trapdata_trapdata_log_id_seq'::regclass) NOT NULL,
    trapdata_log_event_name character varying(128) NOT NULL,
    trapdata_log_event_oid character varying(50) NOT NULL,
    trapdata_log_numeric_oid character varying(100),
    trapdata_log_symbolic_oid character varying(100),
    trapdata_log_community character varying(20),
    trapdata_log_trap_hostname character varying(100),
    trapdata_log_trap_ip character varying(16),
    trapdata_log_agent_hostname character varying(100),
    trapdata_log_agent_IP character varying(16),
    trapdata_log_category character varying(20) NOT NULL,
    trapdata_log_severity character varying(20) NOT NULL,
    trapdata_log_uptime character varying(20) NOT NULL,
    trapdata_log_datetime timestamp,
    trapdata_log_bindings text,
    PRIMARY KEY (trapdata_log_id),
    UNIQUE (trapdata_log_id)
);

-- Auth token sequence
CREATE SEQUENCE xi_auth_tokens_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

-- Auth tokens table
CREATE TABLE xi_auth_tokens (
    auth_token_id integer DEFAULT nextval('xi_auth_tokens_id_seq'::regclass) NOT NULL,
    auth_user_id integer NOT NULL,
    auth_session_id integer NOT NULL,
    auth_token character varying(128),
    auth_valid_until timestamp,
    auth_expires_at timestamp,
    auth_restrictions text,
    auth_used smallint DEFAULT 0,
    PRIMARY KEY (auth_token_id),
    UNIQUE (auth_token_id)
);

-- Sessions sequence
CREATE SEQUENCE xi_sessions_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

-- Sessions table
CREATE TABLE xi_sessions (
    session_id integer DEFAULT nextval('xi_sessions_id_seq'::regclass) NOT NULL,
    session_phpid character varying(128),
    session_created timestamp,
    session_user_id integer NOT NULL,
    session_address character varying(128),
    session_page character varying(255),
    session_data text,
    session_last_active timestamp,
    PRIMARY KEY (session_id),
    UNIQUE (session_id)
);
