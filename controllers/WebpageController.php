<?php

namespace App\Controllers;

use Fux\FuxResponse;
use Fux\OracleDB;
use Fux\Request;

class WebpageController
{

    public static function addWebPagePage()
    {
        return view("addWebPage");
    }


    /**
     * Save (or update) a web page
     *
     * @param Request $request
     * @return string
     * @var $body array{url: string, title: string, page_content: string}
     *
     */
    public static function addWebPage(Request $request)
    {
        $body = $request->getBody();
        $compulsoryFields = ["url", "title", "page_content"];
        foreach ($compulsoryFields as $f) {
            if (!isset($body[$f]) || !$body[$f]) {
                return new FuxResponse("ERROR", "The field \"$f\" is compulsory");
            }
        }

        //Save or update the page
        $stmt_exists = OracleDB::query("SELECT * FROM web_pages WHERE url = :url", [
            "url" => $body['url']
        ]);
        $webpages = OracleDB::fetchAll($stmt_exists);
        oci_free_statement($stmt_exists);

        if (is_array($webpages) && count($webpages)) {
            $stmt = OracleDB::query("UPDATE web_pages SET title = :title, page_content = :page_content WHERE url = :url", [
                "url" => $body['url'],
                "title" => $body['title'],
                "page_content" => $body['page_content']
            ]);
        } else {
            $stmt = OracleDB::query("INSERT INTO web_pages (url, title, page_content) VALUES (:url, :title, :page_content)", [
                "url" => $body['url'],
                "title" => $body['title'],
                "page_content" => $body['page_content']
            ]);
        }

        if (!$stmt) {
            return new FuxResponse("ERROR", "Something went wrong, try again later!");
        }
        oci_free_statement($stmt);


        //Save new terms
        $stmt_delete_terms = OracleDB::query("DELETE FROM terms WHERE deref(page_url).url = :url", [
            "url" => $body['url']
        ]);
        if (!$stmt_delete_terms) {
            return new FuxResponse("ERROR", "Something went wrong, try again later.");
        }
        oci_free_statement($stmt_delete_terms);

        $terms = explode(" ", $body['page_content']);

        //Words insert stms
        foreach ($terms as $w) {
            $w = preg_replace('/[^\w-]/', '', $w);
            $stmt_term = OracleDB::query("INSERT INTO terms (term, page_url) VALUES (:word,(select ref(w) from web_pages w where URL = :url))", [
                "url" => $body['url'],
                "word" => $w
            ]);
            oci_free_statement($stmt_term);
        }

        return view("addWebPage", [
            "success" => is_array($webpages) && count($webpages) ? "Web page update correctly" : "Web page added successfully"
        ]);
    }


    /**
     * Display web page info
     *
     * @param Request $request
     * @return string
     * @var $queryParams array{page_url: integer}
     *
     */
    public static function viewWebPagePage(Request $request)
    {
        $params = $request->getParams();
        $params['page_url'] = base64_decode($params['page_url']);
        $stmt_page = OracleDB::query("SELECT * FROM web_pages WHERE url = :page_url", [
            "page_url" => $params['page_url']
        ]);
        $page = oci_fetch_assoc($stmt_page);
        oci_free_statement($stmt_page);
        if (!$page) {
            return new FuxResponse("ERROR", "The web page doesn't exists anymore!", null, true);
        }

        $stmt_terms = OracleDB::query("SELECT * FROM terms WHERE page_url = (select ref(w) from web_pages w where URL = :page_url)", [
            "page_url" => $params['page_url']
        ]);
        $terms = OracleDB::fetchAll($stmt_terms);

        return view("viewWebPage",[
            "webpage" => $page,
            "terms" => $terms
        ]);
    }

}
