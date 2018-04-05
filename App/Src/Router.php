<?php

namespace App\Src;

use App\Config\Configuration;
use App\Src\Request;

class Router
{
    //TODO créer constantes rights
    const RULES  = array(
        "{articles/edit/(?P<id>\d+)}" => ["ArticlesController::editAction", 2],
        "{articles/(?P<id>\d+)}" => ["ArticlesController::render", 0],
        "{articles}" => ["ArticlesController::render", 0],
        "" => ["ArticlesController::render", 0],
    );

    static public function match(Request $request)
    {
        foreach(self::RULES as $pattern => $method){
            if(preg_match($pattern, $path, $params)){
                $request->setParams($params);
                return $method;
            }
        }
    }
}