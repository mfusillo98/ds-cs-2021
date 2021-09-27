<?php

namespace App\Controllers;

use Fux\FuxResponse;
use Fux\OracleDB;
use Fux\Request;

class SearchController
{

    /**
     * Show webpages that match terms entered in the query field
     *
     * @param Request $request
     * @return string
     * @var $queryStringParams array{q: string}
     *
     */
    public static function index(Request $request)
    {
        $queryStringParams = $request->getQueryStringParams();
        $keywords = array_filter(array_map("trim", explode(" ", $queryStringParams['q'])));
        $termsJoin = [];
        foreach ($keywords as $k) {
            $termsJoin[] = "JOIN terms t ON DEREF(t.page_url).url = p.url AND t.term LIKE '$k'";
        }
        $joins = implode(' ', $termsJoin);

        $sql = "
            SELECT w.* FROM (
                SELECT DISTINCT p.url as url FROM web_pages p $joins
            ) t 
            JOIN web_pages w ON t.url = w.url
        ";

        $stmt = OracleDB::query($sql);
        $results = OracleDB::fetchAll($stmt);
        oci_free_statement($stmt);

        return view('search', [
            "results" => $results,
            "query" => $queryStringParams['q']
        ]);
    }


    /**
     * Salve a query with specific keywords and web pages
     *
     * @param Request $request
     * @return string
     * @var $queryStringParams array{q: string}
     *
     */
    public static function saveSearchQuery(Request $request)
    {
        $queryStringParams = $request->getQueryStringParams();
        $keywords = array_filter(array_map("trim", explode(" ", $queryStringParams['q'])));
        $termsJoin = [];
        foreach ($keywords as $k) {
            $termsJoin[] = "JOIN terms t ON DEREF(t.page_url).url = p.url AND t.term LIKE '$k'";
        }
        $joins = implode(' ', $termsJoin);

        $query_id = null;

        //Check if query exits
        $stmt_query = OracleDB::query("SELECT * FROM queries where keywords = '" . implode(", ", $keywords) . "'");
        $existingQueries = OracleDB::fetchAll($stmt_query);
        oci_free_statement($stmt_query);

        if (is_array($existingQueries) && count($existingQueries)) {
            $query_id = $existingQueries[0]['QUERY_ID'];
        } else {
            $stmt_query = OracleDB::query(
                "INSERT INTO queries (keywords) values ('" . implode(", ", $keywords) . "') RETURNING query_id INTO :new_id",
                [
                    "new_id" => &$query_id
                ]
            );
            oci_free_statement($stmt_query);
        }

        if (!$query_id) {
            return new FuxResponse("ERROR", "Something went wrong, try again later!");
        }

        //Delete all previous search results if exists
        $stmt_delete = OracleDB::query("DELETE FROM results WHERE query_id = $query_id");
        if (!$stmt_delete) {
            return new FuxResponse("ERROR", "Something went wrong. Try again later!");
        }
        oci_free_statement($stmt_delete);

        $sql = "
            INSERT INTO results (query_id, page_url, rank) 
            SELECT $query_id as query_id, w.url, ROW_NUMBER() OVER(ORDER BY 1)  FROM (
                SELECT DISTINCT p.url as url FROM web_pages p $joins
            ) t 
            JOIN web_pages w ON t.url = w.url
        ";

        $stmt = OracleDB::query($sql);
        if (!$stmt) {
            return new FuxResponse("ERROR", "Something went wrong, try again later.");
        }
        oci_free_statement($stmt);

        return new FuxResponse("OK",
            is_array($existingQueries) && count($existingQueries) ? "Your query has been updated!" : "Your query has been saved!",
            [
                "forwardLink" => routeFullUrl('/search?') . http_build_query($queryStringParams),
                "forwardLinkText" => "Go back to your query"
            ], true);
    }

}
