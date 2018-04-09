<?php

namespace App\Src;


class Router
{
    // Définie la méthode à appeler en fonction du chemin de chacune des requêtes
    const RULES  = [
        // Articles
        "{^articles/create/?$}" => ["ArticlesController::createAction", UserRight::WRITER],
        "{^articles/edit/(?P<id>\d+)/?$}" => ["ArticlesController::editAction", UserRight::WRITER],
        "{^articles/edit/?$}" => ["ArticlesController::editAllAction", UserRight::WRITER],
        "{^articles/delete/?$}" => ["ArticlesController::deleteAction", UserRight::WRITER],
        "{^articles/(?P<id>\d+)/?$}" => ["ArticlesController::displayAction", UserRight::INVITE],
        "{^articles\?}" => ["ArticlesController::displayAllAction", UserRight::INVITE],
        "{^articles/?$}" => ["ArticlesController::displayAllAction", UserRight::INVITE],

        // Users
        "{^users/edit/(?P<id>\d+)/?$}" => ["UsersController::editAction", UserRight::WRITER],

        // Categories
        "{^categories/delete/?$}" => ["CategoriesController::deleteAction", UserRight::ADMIN],

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
        "{^setting/?$}" => ["UsersController::settingAction", UserRight::USER],
        "{^/?$}" => ["ArticlesController::displayAllAction", UserRight::INVITE],
    ];

    // Vérifie que le chemin existe dans RULES et renvoie la chaine de caractère associée au dispatcher
    static public function match(Request $request)
    {
        foreach(self::RULES as $pattern => $method){
            if(preg_match($pattern, $request->getUrl(), $params)){
                // Enregistre les paramètres passés dans la requete
                $request->setParams($params);
                $method[0] = "App\\Controllers\\" . $method[0];
                return $method;
            }
        }
        return false;
    }
}

