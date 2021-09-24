<?php

namespace App\Controllers\Seeding;

use App\Seeding\WebPageSeeding;
use Fux\OracleDB;

class WebpageSeedingController
{

    const CONTENT_MAX_LENGTH = 1024;

    public static function webpageSeeding()
    {
        ini_set('max_execution_time',0);
        $stmt = OracleDB::query('CALL populate_web_page(200)');
        oci_free_statement($stmt);
        $stmt = OracleDB::query('SELECT * FROM web_pages');
        $webpages = OracleDB::fetchAll($stmt);
        oci_free_statement($stmt);

        foreach ($webpages as $r) {
            $words = WebPageSeeding::getRandomTextOf(rand(250, 500), true);
            $text = implode(' ', $words);

            if (strlen($text) > self::CONTENT_MAX_LENGTH) $text = substr($text,0,self::CONTENT_MAX_LENGTH);

            //Content update stmt
            $stmt = OracleDB::query("UPDATE web_pages SET page_content = '$text' WHERE URL = '$r[URL]'");
            oci_free_statement($stmt);

            //Words insert stms
            foreach ($words as $w) {
                $stmt = OracleDB::query("INSERT INTO terms (term, page_url) VALUES ('$w',(select ref(w) from web_pages w where URL = '$r[URL]'))");
                oci_free_statement($stmt);
            }
        }

        return "Seeding completed";
    }

}