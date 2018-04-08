<?php

namespace App\Src;

class Request {
    
    private $params;
    private $files;
    private $url;

    private $method_type;
    private $method_params;

    function __construct(){
        
        $this->url = $_SERVER["REQUEST_URI"];
        $this->url = ltrim($this->url, '/');
        $this->url = strtolower($this->url);
        $this->method_type = $_SERVER["REQUEST_METHOD"];
        if($this->method_type === "POST") {
            $this->method_params = $_POST;
        } else if ($this->method_type === "GET") {
            $this->method_params = $_GET;
        } else if ($this->method_type == "PUT" || $this->method_type == "DELETE") {
            $this->method_params = [];
            parse_str(file_get_contents('php://input'), $this->method_params);
        }
        $this->files = isset($_FILES) ? $_FILES : null;
    }

    function setParams($params){
        $this->params = $params;
    }

    function getParams(){
        return $this->params;
    }

    function setUrl($url){
        $this->url = $url;
    }

    function getUrl(){
        return $this->url;
    }

    function getFiles(){
        return $this->files;
    }

    function getMethod() {
        return $this->method_type;
    }

    function getMethodParams() {
        return $this->method_params;
    }
}