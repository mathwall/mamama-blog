<?php

namespace App\Src;


class Router
{
    //TODO créer constantes rights
    const RULES  = [
        "{^articles/create/?$}" => ["ArticlesController::createAction", UserRight::WRITER],
        "{^articles/edit/(?P<id>\d+)/?$}" => ["ArticlesController::editAction", UserRight::WRITER],
        "{^articles/edit/?$}" => ["ArticlesController::editAllAction", UserRight::WRITER],
        "{^articles/delete/?$}" => ["ArticlesController::deleteAction", UserRight::WRITER],
        "{^articles/(?P<id>\d+)/?$}" => ["ArticlesController::displayAction", UserRight::INVITE],
        "{^articles/?$}" => ["ArticlesController::displayAllAction", UserRight::INVITE],

        // Admin
        "{^admin/users/edit/?$}" => ["AdminController::displayUsersAction", UserRight::ADMIN],
        "{^admin/tags/edit/?$}" => ["AdminController::displayTagsAction", UserRight::ADMIN],
        "{^admin/comments/edit/?$}" => ["AdminController::displayCommentsAction", UserRight::ADMIN],
        "{^admin/articles/edit/?$}" => ["AdminController::displayArticlesAction", UserRight::WRITER],
        "{^admin/categories/edit/?$}" => ["AdminController::displayCategoriesAction", UserRight::WRITER],
        "{^admin/?$}" => ["AdminController::displayAllAction", UserRight::WRITER],

        "{^login/?$}" => ["UsersController::loginAction", UserRight::BANNED],
        "{^logout/?$}" => ["UsersController::logoutAction", UserRight::BANNED],
        "{^register/?$}" => ["UsersController::registerAction", UserRight::BANNED],
        "{^setting/?$}" => ["UsersController::editAction", UserRight::USER],
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

