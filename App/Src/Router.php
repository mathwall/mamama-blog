<?php

namespace App\Src;


class Router
{
    //TODO créer constantes rights
    const RULES  = [
        "{articles/edit/(?P<id>\d+)/?}" => ["ArticlesController::editAction", UserRight::WRITER],
        "{articles/(?P<id>\d+)/?}" => ["ArticlesController::displayAction", UserRight::INVITE],
        "{articles}" => ["ArticlesController::displayAllAction", UserRight::INVITE],
    ];

    static public function match(Request $request)
    {
        foreach(self::RULES as $pattern => $method){
            if(preg_match($pattern, $request->getUrl(), $params)){
                $request->setParams($params);
                $method[0] = "App\\Controllers\\" . $method[0];
                return $method;
            }
        }
        return false;
    }
}
