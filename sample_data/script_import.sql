CREATE TABLE docs(
  corpus character varying(100),
  title text,
  url text,
  id_doc text);

CREATE TABLE europarl___add_docs(
  id_doc text,
  date text,
  policy text,
  url_votewatch text);

COPY docs (corpus,title,url,id_doc) FROM '/usr/local/app/navicorpus/viewer/sample_data/europarl.csv' DELIMITER ';' CSV HEADER ;
COPY europarl___add_docs (id_doc,date,policy,url_votewatch) FROM '/usr/local/app/navicorpus/viewer/sample_data/europarl_additional_data.csv' DELIMITER ';' CSV HEADER ;

