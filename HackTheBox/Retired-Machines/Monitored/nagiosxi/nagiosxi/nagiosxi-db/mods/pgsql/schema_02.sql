-- Event queue sequence
CREATE SEQUENCE xi_eventqueue_eventqueue_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

-- Event queue table
CREATE TABLE xi_eventqueue (
    eventqueue_id integer DEFAULT nextval('xi_eventqueue_eventqueue_id_seq'::regclass) NOT NULL,
    event_time integer,
    event_source smallint,
    event_type smallint DEFAULT 0 NOT NULL,
    event_meta text
);
