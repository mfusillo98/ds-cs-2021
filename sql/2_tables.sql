-- ##################
-- Tables definitions
-- ##################

CREATE TABLE web_pages OF t_web_page
(
    url PRIMARY KEY
);
/
CREATE TABLE terms OF t_term
(
    term_id PRIMARY KEY, -- auto generated
    term NOT NULL,
    page_url NOT NULL
);
/
CREATE TABLE media OF t_media
(
    media_id PRIMARY KEY,  -- auto generated
    url NOT NULL ,
    mime_type NOT NULL,
    page_url NOT NULL
);
/
CREATE TABLE web_page_links
(
    source_url      VARCHAR(255) REFERENCES web_pages (url) ON DELETE CASCADE,
    destination_url VARCHAR(255)  REFERENCES web_pages (url) ON DELETE CASCADE,
    PRIMARY KEY (source_url, destination_url)
);
/
CREATE TABLE queries OF t_query
(
    query_id PRIMARY KEY,  -- auto generated
    keywords NOT NULL
);
/
CREATE TABLE results
(
    query_id INTEGER REFERENCES queries(query_id),
    page_url VARCHAR(255) REFERENCES web_pages(url) ON DELETE CASCADE,
    rank INTEGER,
    PRIMARY KEY(query_id, page_url)
);
/
CREATE INDEX queryKeywordIdx ON queries(keywords);
/
CREATE INDEX termsIdx ON terms(term);