<?php

class FuxResponse
{


    private $response = [];
    private $canBePretty = false;

    public function __construct($status = null, $message = null, $data = null, $canBePretty = false)
    {
        if ($status !== null) $this->response['status'] = $status;
        if ($message !== null) $this->response['message'] = $message;
        if ($data !== null) $this->response['data'] = $data;
        $this->canBePretty = $canBePretty;
    }

    public function __toString()
    {
        return json_encode($this->response);
    }

    public function isOk()
    {
        return $this->response['status'] == "OK";
    }

    public function isError()
    {
        return $this->response['status'] == "ERROR";
    }

    public function isPretty()
    {
        return $this->canBePretty;
    }

    public function getMessage()
    {
        return $this->response['message'] ?? null;
    }

    public function setMessage($message)
    {
        $this->response['message'] = $message;
    }

    public function getData()
    {
        return $this->response['data'] ?? null;
    }

    public function setData($data)
    {
        $this->response['data'] = $data;
    }

    public function getStatus()
    {
        return $this->response['status'] ?? null;
    }

    public function setStatus($status)
    {
        $this->response['status'] = $status;
    }
}
