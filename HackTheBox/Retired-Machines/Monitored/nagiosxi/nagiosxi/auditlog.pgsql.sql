
SET client_encoding = 'UTF8';
SET check_function_bodies = false;
SET client_min_messages = warning;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

CREATE TABLE xi_auditlog (
    auditlog_id serial NOT NULL,
    log_time timestamp without time zone,
    source text,
    "user" text,
    "type" integer,
    message text,
    ip_address text,
    details text
);

ALTER TABLE public.xi_auditlog OWNER TO nagiosxi;

ALTER TABLE ONLY xi_auditlog
ADD CONSTRAINT xi_auditlog_pkey PRIMARY KEY (auditlog_id);

CREATE INDEX xi_auditlog_ip_address ON xi_auditlog USING btree (ip_address);

CREATE INDEX xi_auditlog_log_time ON xi_auditlog USING btree (log_time);

CREATE INDEX xi_auditlog_source ON xi_auditlog USING btree (source);

CREATE INDEX xi_auditlog_type ON xi_auditlog USING btree ("type");

CREATE INDEX xi_auditlog_user ON xi_auditlog USING btree ("user");