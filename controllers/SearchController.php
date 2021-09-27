<?php

namespace App\Controllers;

use Fux\OracleDB;
use Fux\Request;

class SearchController
{

    /**
     * Show webpages that match terms entered in the query field
     *
     * @param Request $request
     * @var $queryStringParams array{q: string}
     */
    public static function index(Request $request)
    {
        $queryStringParams = $request->getQueryStringParams();
        $keywords = array_filter(array_map("trim", explode(" ", $queryStringParams['q'])));
        $termsJoin = [];
        foreach($keywords as $k){
            $termsJoin[] = "JOIN terms t ON DEREF(t.page_url).url = p.url AND t.term LIKE '$k'";
        }
        $joins = implode(' ', $termsJoin);

        $stmt = OracleDB::query("
            SELECT p.* FROM web_pages p $joins
        ");
        $results = OracleDB::fetchAll($stmt);

        return view('search',[
            "results" => $results,
            "query" => $queryStringParams['q']
        ]);
    }

}
