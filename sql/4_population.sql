-- ########################
-- Procedure for population
-- ########################

-- To perform a correct "seeding" operation call the "populate" procedure


CREATE OR REPLACE FUNCTION generate_random_url RETURN VARCHAR AS
BEGIN

    return CONCAT(
            CONCAT(
                    'https://',
                    DBMS_RANDOM.STRING('X', TRUNC(DBMS_RANDOM.VALUE(4, 25))) -- hostname
                ),
            CONCAT(
                    '.',
                    DBMS_RANDOM.STRING('L', TRUNC(DBMS_RANDOM.VALUE(2, 3))) -- TLD
                )
        );
end;
/
CREATE OR REPLACE FUNCTION get_random_term RETURN VARCHAR AS
    word VARCHAR(25);
BEGIN
    SELECT term
    INTO word
    FROM (
             SELECT column_value as term
             FROM (sys.dbms_debug_vc2coll('In', 'una', 'terra', 'lontana', 'dietro', 'le', 'montagne', 'Parole', 'lontani',
                                          'dalle', 'terre', 'di', 'Vocalia', 'e', 'Consonantia', 'vivono', 'i', 'testi',
                                          'casuali', 'Vivono', 'isolati', 'nella', 'cittadina', 'Lettere', 'sulle',
                                          'coste',
                                          'del', 'Semantico', 'un', 'immenso', 'oceano', 'linguistico', 'Un', 'piccolo',
                                          'ruscello', 'chiamato', 'Devoto', 'Oli', 'attraversa', 'quei', 'luoghi',
                                          'rifornendoli', 'tutte', 'regolalie', 'cui', 'hanno', 'bisogno', 'e',
                                          'paradismatica', 'paese', 'della', 'cuccagna', 'in', 'golose', 'porzioni',
                                          'proposizioni', 'arrostite', 'volano', 'bocca', 'a', 'chi', 'desideri', 'Non',
                                          'volta', 'casuali', 'sono', 'stati', 'dominati', 'dallonnipotente',
                                          'Interpunzione',
                                          'vita', 'davvero', 'non', 'ortografica', 'giorno', 'pero', 'accadde', 'che',
                                          'la',
                                          'piccola', 'riga', 'testo', 'casuale', 'nome', 'Lorem', 'ipsum', 'decise',
                                          'andare',
                                          'esplorare', 'vasta', 'Grammatica', 'Il', 'grande', 'Oximox', 'tento',
                                          'dissuaderla',
                                          'poiche', 'quel', 'luogo', 'pullulava', 'virgole', 'spietate', 'punti',
                                          'interrogativi', 'selvaggi', 'subdoli', 'virgola', 'ma', 'il', 'casuale', 'si',
                                          'fece', 'certo', 'fuorviare', 'Raccolse', 'sue', 'sette', 'maiuscole',
                                          'scorrere',
                                          'sua', 'iniziale', 'cintura', 'mise', 'cammino', 'Quando', 'supero', 'primi',
                                          'colli', 'dei', 'monti', 'Corsivi', 'volto', 'guardare', 'unultima', 'skyline',
                                          'citta', 'headline', 'villaggio', 'Alfabeto', 'subline', 'stessa', 'strada',
                                          'vicolo', 'Riga', 'Una', 'domanda', 'retorica', 'gli', 'scorse',
                                          'malinconicamente',
                                          'sulla', 'guancia', 'quindi', 'rimise', 'Lungo', 'strada', 'incontro', 'copy',
                                          'guardia', 'nel', 'da', 'veniva', 'era', 'stato', 'riscritto', 'molte', 'volte',
                                          'tutto', 'quello', 'rimaneva', 'forma', 'originaria', 'congiunzione', 'e',
                                          'Esorto',
                                          'fare', 'marcia', 'indietro', 'tornare', 'sicura', 'proveniva', 'Tuttavia',
                                          'nessun',
                                          'argomento', 'poteva', 'persuadere', 'passo', 'molto', 'tempo', 'due', 'perfidi',
                                          'copywriter', 'ne', 'approfittarono', 'lo', 'fecero', 'ubriacare', 'longe',
                                          'parole',
                                          'trascinarono', 'loro', 'agenzia', 'dove', 'abusarono', 'lui', 'ripetutamente',
                                          'per', 'progetti', 'E', 'se', 'fosse', 'riscritto', 'sfrutterebbero', 'ancora',
                                          'Gregorio', 'Samsa', 'svegliandosi', 'una', 'mattina', 'da', 'sogni', 'agitati',
                                          'si', 'trovo', 'trasformato', 'nel', 'suo', 'letto', 'in', 'un', 'enorme',
                                          'insetto',
                                          'immondo', 'Riposava', 'sulla', 'schiena', 'dura', 'come', 'corazza', 'e',
                                          'sollevando', 'poco', 'il', 'capo', 'vedeva', 'ventre', 'arcuato', 'bruno',
                                          'diviso',
                                          'tanti', 'segmenti', 'ricurvi', 'cima', 'a', 'cui', 'la', 'coperta', 'vicina',
                                          'scivolar', 'giu', 'tutta', 'manteneva', 'fatica', 'Le', 'gambe', 'numerose',
                                          'sottili', 'far', 'pieta', 'rispetto', 'alla', 'sua', 'corporatura', 'normale',
                                          'tremolavano', 'senza', 'tregua', 'confuso', 'luccichio', 'dinanzi', 'ai',
                                          'suoi',
                                          'occhi', 'Cosa', 'me', 'avvenuto', 'penso', 'Non', 'era', 'sogno', 'La',
                                          'camera',
                                          'stanzetta', 'di', 'giuste', 'proporzioni', 'soltanto', 'po', 'piccola', 'se',
                                          'ne',
                                          'stava', 'tranquilla', 'fra', 'le', 'quattro', 'ben', 'note', 'pareti', 'Sulla',
                                          'tavola', 'campionario', 'disfatto', 'tessuti', 'Samsa', 'commesso',
                                          'viaggiatore',
                                          'sopra', 'appeso', 'parete', 'ritratto', 'ritagliato', 'lui', 'non', 'molto',
                                          'rivista', 'illustrata', 'messo', 'dentro', 'bella', 'cornice', 'dorata',
                                          'raffigurava', 'donna', 'seduta', 'ma', 'dritta', 'sul', 'busto', 'con',
                                          'berretto',
                                          'boa', 'pelliccia;', 'essa', 'levava', 'incontro', 'chi', 'guardava', 'pesante',
                                          'manicotto', 'scompariva', 'tutto', 'lavambraccio', 'Lo', 'sguardo', 'rivolse',
                                          'allora', 'verso', 'finestra', 'cielo', 'fosco', 'si', 'sentivano', 'battere',
                                          'gocce', 'pioggia', 'sullo', 'zinco', 'della', 'finestra', 'lo', 'immalinconi',
                                          'completamente', 'Che', 'avverrebbe', 'io', 'dormissi', 'ancora', 'dimenticassi',
                                          'ogni', 'pazzia', 'penso', 'cio', 'assolutamente', 'impossibile', 'perche',
                                          'abituato', 'dormire', 'destra', 'poteva', 'nelle', 'sue', 'attuali',
                                          'condizioni',
                                          'mettersi', 'quella', 'posizione', 'Per', 'quanto', 'gettasse', 'tutta', 'forza',
                                          'parte', 'tornava', 'sempre', 'oscillando', 'dorso:', 'provo', 'per', 'cento',
                                          'volte', 'chiuse', 'gli', 'occhi', 'veder', 'zampine', 'dimenanti', 'rinuncio',
                                          'quando', 'comincio', 'sentire', 'fianco', 'dolore', 'sottile', 'sordo', 'mai',
                                          'provato', 'O', 'Dio', 'pensava', 'che', 'professione', 'faticosa', 'ho',
                                          'scelto',
                                          'Ogni', 'giorno', 'su', 'treno', 'Laffanno', 'affari', 'e', 'piu', 'intenso',
                                          'vero',
                                          'proprio', 'ufficio', 've', 'giunta', 'questa', 'piaga', 'del', 'viaggiare',
                                          'preoccupazioni', 'coincidenze', 'dei', 'treni', 'nutrizione', 'irregolare',
                                          'cattiva;', 'relazioni', 'cogli', 'uomini', 'poi', 'cambiano', 'ad', 'momento',
                                          'possono', 'diventare', 'durature', 'ne', 'cordiali', 'Al', 'diavolo', 'cosa',
                                          'Sentendo', 'leggero', 'prurito', 'nella', 'parte', 'alta', 'ventre', 'spinse',
                                          'lentamente', 'schiena', 'colonnetta', 'letto', 'poter', 'alzar', 'meglio',
                                          'capo:',
                                          'punto', 'pizzicava', 'coperto', 'puntini', 'bianchi', 'sapeva', 'pensare;',
                                          'toccarlo', 'gamba', 'subito', 'ritrasse', 'al', 'primo', 'contatto', 'aveva',
                                          'percorso', 'brivido'))
             ORDER BY DBMS_RANDOM.VALUE
         )
    WHERE ROWNUM <= 1;
    RETURN word;
end;
/
CREATE OR REPLACE PROCEDURE populate_web_page(num IN INTEGER) AS
BEGIN
    DELETE FROM terms;
    DELETE FROM web_pages;
    FOR i IN 1..num
        LOOP
            INSERT INTO web_pages (url, title)
            VALUES (generate_random_url(), DBMS_RANDOM.STRING('A', TRUNC(DBMS_RANDOM.VALUE(10, 64))));
        END LOOP;
    DBMS_OUTPUT.PUT_LINE('Generated ' || num || ' webpages');
END;
/
CREATE OR REPLACE PROCEDURE populate_web_page_terms(minTermsNum IN INTEGER, maxTermsNum IN INTEGER) AS
    curr_term    varchar(25);
    curr_page_content varchar(1024);
    termsNum integer;
BEGIN
    DELETE FROM terms;
    FOR page IN (SELECT * FROM web_pages)
        LOOP
            SELECT DBMS_RANDOM.VALUE(minTermsNum, maxTermsNum) INTO termsNum FROM dual; -- Setting terms number for this page
            SELECT '' INTO curr_page_content FROM dual; -- Clear page_content
            -- Generating content + terms list
            FOR i IN 1..termsNum
                LOOP
                    SELECT get_random_term() INTO curr_term FROM dual;
                    IF (LENGTH(curr_page_content) + LENGTH(curr_term) + 1 > 1024) THEN
                        EXIT;
                    end if;
                    SELECT CONCAT(curr_page_content,CONCAT(' ',curr_term)) INTO curr_page_content FROM dual;
                    INSERT INTO terms (term,page_url)
                    VALUES (
                            curr_term,
                            (select ref(w) from web_pages w WHERE url = page.url)
                           );
                end loop;
            UPDATE web_pages SET page_content = curr_page_content WHERE url = page.url;
        END LOOP;
end;

/
CREATE OR REPLACE PROCEDURE populate_web_page_media(imagesNum IN INTEGER, videosNum IN INTEGER) AS
BEGIN
    DELETE FROM media;
    FOR page IN (SELECT * FROM web_pages)
        LOOP
            FOR i IN 1..imagesNum
                LOOP
                    INSERT INTO media (url, mime_type, page_url)
                    VALUES (CONCAT(generate_random_url(), '/img.png'),
                            'image/png',
                            (select ref(w) from web_pages w WHERE url = page.url));
                END LOOP;
            FOR i IN 1..videosNum
                LOOP
                    INSERT INTO media (url, mime_type, page_url)
                    VALUES (CONCAT(generate_random_url(), '/video.mp4'),
                            'video/mpeg4',
                            (select ref(w) from web_pages w WHERE url = page.url));
                END LOOP;
        end loop;
    DBMS_OUTPUT.PUT_LINE('Generated ' || imagesNum || ' images and ' || videosNum || ' videos for each web page');
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
                            SELECT *
                            FROM web_page_links l
                            WHERE l.source_url = page.url
                              AND l.destination_url = w.url
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
    populate_web_page(1000); -- Create web pages
    populate_web_page_terms(100,200); -- Create page terms
    populate_web_page_media(3, 1); -- Create media
    populate_web_page_links(3); -- Create web pages links
    populate_queries(800, 3); --
    populate_queries_results(); --

    DBMS_OUTPUT.PUT_LINE('Population completed');
END;
/

