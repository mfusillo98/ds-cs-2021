<?php

namespace App\Controllers;

use Fux\OracleDB;

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
        return view("savedQueries",[
            "queries" => $queries
        ]);
    }

}
