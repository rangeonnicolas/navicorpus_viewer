BEGIN;

CREATE DATABASE navicorpus;

CREATE TABLE docs(
  corpus character varying(100) NOT NULL,
  title text,
  url text,
  id_doc text NOT NULL,
  CONSTRAINT docs_pkey PRIMARY KEY (corpus, id_doc));

CREATE TABLE europarl___add_docs(
  id_doc text PRIMARY KEY,
  date text,
  policy text,
  url_votewatch text);

COPY docs (corpus,title,url,id_doc) FROM '/usr/local/app/navicorpus/navicorpus_viewer/sample_data/europarl.csv' DELIMITER ';' CSV HEADER ;
COPY europarl___add_docs (id_doc,date,policy,url_votewatch) FROM '/usr/local/app/navicorpus/navicorpus_viewer/sample_data/europarl_additional_data.csv' DELIMITER ';' CSV HEADER ;

COMMIT;
