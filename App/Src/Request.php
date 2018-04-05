<?php

namespace App\Src;

class Request {
    
    private $params;
    private $url;
    
    function __construct(){
        
        $this->url = $_SERVER["REQUEST_URI"];
        $this->url = ltrim($this->url, '/');
        $this->url = strtolower($this->url);
    }

    function setParams($params){

        $this->params;
    }

    function getParams(){
        return $this->params;
    }

    function getUrl(){
        return $this->url;
    }
}