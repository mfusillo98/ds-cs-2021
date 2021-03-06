<?php

include_once(__DIR__ . "/../php/FuxFramework/Middleware/FuxMiddleware.php");

use Fux\FuxMiddleware;

class ExampleMiddleware extends FuxMiddleware
{

    public function handle()
    {
        $canPassMiddleware = true;
        if (!$canPassMiddleware){
            return new FuxResponse("ERROR","You cannot view this page");
        }

        return $this->resolve();
    }

}
