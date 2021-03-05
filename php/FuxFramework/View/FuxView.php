<?php
class FuxView {

    private $viewPath = "";
    private $dataCallback = null;

    /**
     * @description Create a View object which can be used to output the view to the client
     * @param string $viewPath The path to the view file relative to the project global "view" directory
     * @param callable $dataCallback A function which return an object that will be passed as data of the composed view
     */
    public function __construct($viewPath, $dataCallback = null)
    {
        $this->viewPath = $viewPath;
        $this->dataCallback = $dataCallback;
    }

    public function getPath(){
        return $this->viewPath;
    }

    public function getData(){
        if ($this->dataCallback && is_callable($this->dataCallback)){
            return call_user_func($this->dataCallback);
        }
        return [];
    }

}
