<?php

namespace App\Controllers;

use Fux\FuxResponse;
use Fux\OracleDB;
use Fux\Request;

class QueryController
{

    public static function savedQueriesPage()
    {
        $stmt = OracleDB::query("
            SELECT q.query_id, q.keywords, COUNT(r.page_url) as resultsNum 
            FROM queries q
            LEFT JOIN results r ON q.query_id = r.query_id
            GROUP BY q.query_id, q.keywords
            ORDER BY query_id DESC
        ");
        $queries = OracleDB::fetchAll($stmt);
        return view("savedQueries", [
            "queries" => $queries
        ]);
    }


    /**
     * Display all web pages associated with a saved query
     *
     * @param Request $request
     * @return string
     * @var $queryStringParams array{query_id: integer}
     *
     */
    public static function viewQueryPage(Request $request)
    {
        $queryStringParams = $request->getQueryStringParams();
        $stmt_query = OracleDB::query("SELECT * FROM queries WHERE query_id = :query_id", [
            "query_id" => $queryStringParams['query_id']
        ]);
        $query = oci_fetch_assoc($stmt_query);
        oci_free_statement($stmt_query);
        if (!$query) {
            return new FuxResponse("ERROR", "The query doesn't exists anymore!", null, true);
        }

        $stmt_results = OracleDB::query("
            SELECT p.* FROM results r 
            JOIN web_pages p ON r.page_url = p.url 
            WHERE r.query_id = :query_id
        ", [
            "query_id" => $queryStringParams['query_id']
        ]);
        $results = OracleDB::fetchAll($stmt_results);

        return view("viewQuery",[
            "query" => $query,
            "results" => $results
        ]);
    }

}
