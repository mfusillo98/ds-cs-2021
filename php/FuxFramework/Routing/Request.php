<?php


namespace Fux;

include_once 'IRequest.php';
include_once __DIR__.'/Router.php';

class Request implements IRequest
{
    private $params = [];
    function __construct()
    {
        $this->bootstrapSelf();
    }
    private function bootstrapSelf()
    {
        foreach($_SERVER as $key => $value)
        {
            $this->{$this->toCamelCase($key)} = $value;
        }
        if(isset($this->requestUri)){
            $this->requestUri = $this->formatRoute($this->requestUri);
        }
        $this->{"url"} = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Removes trailing forward slashes from the right of the route.
     * @param route (string)
     */
    private function formatRoute($route)
    {
        if (defined("PROJECT_DIR") && strlen(PROJECT_DIR)) {
            if (substr($route,0,strlen(PROJECT_DIR)) === PROJECT_DIR) {
                $newRoute = substr($route, strlen(PROJECT_DIR));
                //Rimuove la dir di progetto dalla route da usare nel router
                if ($newRoute != $route) {
                    $route = $newRoute; //Aggiungo uno slash iniziale perchè se PROJECT_DIR != "" allora sarà del tip "/subdir" e
                }
            }
        }
        $result = rtrim($route, '/');
        if ($result === '')
        {
            return '/';
        }
        return $result;
    }

    public function matchRoute($route){
        $router = new Router($this);
        return $router->match($route, strtok($this->requestUri,'?'));
    }


    private function toCamelCase($string)
    {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);
        foreach($matches[0] as $match)
        {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }
        return $result;
    }

    public function setParams($params){
            $this->params = $params;
    }

    public function getParams(){
        $params = $this->params;
        array_walk_recursive($params, function (&$value){
            if (is_string($value)) {
                $value = DB::sanitize($value);
            }
        });
        return $params;
    }

    public function getQueryStringParams(){
        $params = [];
        if ($this->requestMethod === "GET"){
            $params = $_GET;
            array_walk_recursive($params, function (&$value){
                if (is_string($value)) {
                    $value = DB::sanitize($value);
                }
            });
        }
        return $params;
    }

    public function getBody()
    {
        if($this->requestMethod === "GET")
        {
            return [];
        }
        if ($this->requestMethod == "POST")
        {
            $body = $_POST;
            if ($_POST) {
                array_walk_recursive($body, function (&$value) {
                    if (is_string($value)) {
                        $value = DB::sanitize($value);
                    }
                });
            }
            return $body;
        }
    }

    public function setBody($body){
        $_POST = $body;
    }

    public function setMethod($method){
        $this->requestMethod = $method;
    }
}
