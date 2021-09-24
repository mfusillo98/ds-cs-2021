-- ##################
-- Types definitions
-- ##################
CREATE OR REPLACE TYPE t_web_page FORCE AS OBJECT
(
    url       varchar(255),
    "content" CLOB,
    title     varchar(255)
);
/
CREATE OR REPLACE TYPE t_term FORCE AS OBJECT
(
    term_id  INTEGER,
    term     VARCHAR(25),
    page_url REF t_web_page
);
/
CREATE OR REPLACE TYPE t_media FORCE AS OBJECT
(
    media_id  INTEGER,
    url       varchar(255),
    mime_type varchar(15),
    page_url  REF t_web_page
);
/
CREATE OR REPLACE TYPE t_query FORCE AS OBJECT
(
    query_id INTEGER,
    keywords varchar(255)
);
