<?php

namespace App\Src;


class Router
{
    //TODO créer constantes rights
    const RULES  = [
        "{^articles/create/?$}" => ["ArticlesController::createAction", UserRight::WRITER],
        "{^articles/edit/(?P<id>\d+)/?$}" => ["ArticlesController::editAction", UserRight::WRITER],
        "{^articles/edit/?$}" => ["ArticlesController::createAction", UserRight::WRITER],
        "{^articles/(?P<id>\d+)/?$}" => ["ArticlesController::displayAction", UserRight::INVITE],
        "{^articles/?$}" => ["ArticlesController::displayAllAction", UserRight::INVITE],
        "{^login/?$}" => ["UsersController::loginAction", UserRight::INVITE],
        "{^logout/?$}" => ["UsersController::logoutAction", UserRight::INVITE],
        "{^register/?$}" => ["UsersController::registerAction", UserRight::INVITE],
        "{^/?$}" => ["ArticlesController::displayAllAction", UserRight::INVITE],
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

