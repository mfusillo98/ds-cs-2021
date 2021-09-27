-- ########################
-- Procedure for population
-- ########################

-- To perform a correct "seeding" operation follow thse steps:
-- 1) Navigate the route http://{domain}/{project-dir}/seeding/web-page (it could take some minutes to complete)
-- 2) Execute query "CALL populate()"

CREATE OR REPLACE PROCEDURE populate_web_page(num IN INTEGER) AS
BEGIN
    DELETE FROM terms;
    DELETE FROM web_pages;
    FOR i IN 1..num
        LOOP
            INSERT INTO web_pages (url, title)
            VALUES (CONCAT(
                            CONCAT(
                                    'https://',
                                    DBMS_RANDOM.STRING('X', TRUNC(DBMS_RANDOM.VALUE(4, 25))) -- hostname
                                ),
                            CONCAT(
                                    '.',
                                    DBMS_RANDOM.STRING('L', TRUNC(DBMS_RANDOM.VALUE(2, 3))) -- TLD
                                )
                        ), -- url
                    DBMS_RANDOM.STRING('A', TRUNC(DBMS_RANDOM.VALUE(10, 64))) -- title
                   );
        END LOOP;
    DBMS_OUTPUT.PUT_LINE('Generated ' || num || ' webpages');
END;
/


CREATE OR REPLACE PROCEDURE populate_web_page_links(linksNum IN INTEGER) AS
BEGIN
    DELETE FROM web_page_links;
    FOR page IN (SELECT * FROM web_pages)
        LOOP
            FOR i IN 1..linksNum
                LOOP
                    INSERT INTO web_page_links (source_url, destination_url)
                    SELECT page.url, w.url
                    FROM web_pages w
                    WHERE NOT EXISTS(
                            SELECT * FROM web_page_links l WHERE l.source_url = page.url AND l.destination_url = w.url
                        )
                    ORDER BY Dbms_Random.Value;
                END LOOP;
        end loop;
    DBMS_OUTPUT.PUT_LINE('Generated ' || linksNum || ' web page links for each web page');
END;
/


CREATE OR REPLACE PROCEDURE populate_queries(queriesNum IN INTEGER, keywordsNum IN INTEGER) AS
BEGIN
    DELETE FROM queries;
    FOR i IN 1..queriesNum
        LOOP
            INSERT INTO queries (query_id, keywords)
            SELECT null, LISTAGG(term, ', ')
            FROM (SELECT DISTINCT term FROM terms ORDER BY Dbms_Random.Value)
            WHERE ROWNUM <= keywordsNum;
        END LOOP;

    DBMS_OUTPUT.PUT_LINE('Generated ' || queriesNum || ' queries');
END;
/


CREATE OR REPLACE PROCEDURE populate_queries_results AS
BEGIN
    DELETE FROM results;

    INSERT INTO results (query_id, page_url, rank)
    SELECT q.query_id, t.page_url.url, row_number() OVER (PARTITION BY q.query_id ORDER BY DBMS_RANDOM.VALUE) rank
    FROM queries q
             JOIN terms t ON q.keywords LIKE CONCAT('%', CONCAT(t.term, '%'))
    GROUP BY (q.query_id, t.page_url);

    DBMS_OUTPUT.PUT_LINE('Generated queries results');
END;
/


CREATE OR REPLACE PROCEDURE populate AS
BEGIN
    populate_web_page_links(3);
    populate_queries(800, 3);
    populate_queries_results();

    DBMS_OUTPUT.PUT_LINE('Population completed');
END;
/

