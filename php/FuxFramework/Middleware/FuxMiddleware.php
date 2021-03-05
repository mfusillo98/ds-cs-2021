<?php


namespace Fux;

include_once __DIR__.'/../Routing/Request.php';

include_once 'IMiddleware.php';

class FuxMiddleware implements IMiddleware
{
    protected $next = null;
    protected $request = null;

    public function handle()
    {
        return $this->resolve();
    }

    public function resolve(){
        if ($this->next instanceof IMiddleware){
            return $this->next->handle();
        }else{
            if (is_callable($this->next)) {
                return $this->{"next"}($this->request);
            }
        }
    }

    public function setNext($closure){
        $this->next = $closure;
    }

    public function setRequest(Request $request){
        $this->request = $request;
    }

    public function __call($method, $args) {
        if(isset($this->$method) && is_callable($this->$method)) {
            return call_user_func_array(
                $this->$method,
                $args
            );
        }
    }

}