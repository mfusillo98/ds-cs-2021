-- ######################
-- Triggers for sequences
-- ######################

CREATE SEQUENCE term_seq START WITH 1;
CREATE OR REPLACE TRIGGER term_id
    BEFORE INSERT
    ON terms
    FOR EACH ROW
BEGIN
    SELECT term_seq.NEXTVAL
    INTO :new.term_id
    FROM dual;
END;
/
CREATE SEQUENCE media_seq START WITH 1;
CREATE OR REPLACE TRIGGER media_id
    BEFORE INSERT
    ON media
    FOR EACH ROW
BEGIN
    SELECT media_seq.NEXTVAL
    INTO :new.media_id
    FROM dual;
END;
/
CREATE SEQUENCE query_seq START WITH 1;
CREATE OR REPLACE TRIGGER query_id
    BEFORE INSERT
    ON queries
    FOR EACH ROW
BEGIN
    SELECT query_seq.NEXTVAL
    INTO :new.query_id
    FROM dual;
END;
/
CREATE OR REPLACE TRIGGER result_rank
    BEFORE INSERT
    ON results
    FOR EACH ROW
BEGIN
    SELECT r
    INTO :new.rank
    FROM (
             SELECT (rank+1) as r FROM results WHERE query_id = :new.query_id ORDER BY rank DESC
         );
END;


-- ##########################
-- Triggers for unique fields
-- ##########################

CREATE OR REPLACE TRIGGER unique_media_in_page
    AFTER INSERT
    ON media
    FOR EACH ROW
DECLARE
    row_count INTEGER;
BEGIN
    SELECT COUNT(*)
    INTO row_count
    FROM (SELECT COUNT(*) AS cnt
          FROM media
          GROUP BY (url, page_url)
          HAVING COUNT(*) > 1
          ORDER BY cnt)
    WHERE ROWNUM < 2;
    IF row_count != 0 THEN
        RAISE_APPLICATION_ERROR(1001, 'Duplicated media URL in page');
    END IF;
END;