<?php

namespace App;

use App\Controllers\Controller;
use App\Controllers\UsersController;
use App\Src\Router;
use App\Src\Request;
use App\Src\User;
use App\Src\UserRight;


class Dispatcher
{
    static public function redirect(Request $request)
    {
        $tab = Router::match($request);

        if(!$tab) {
            Controller::notFoundPageAction($request);
        } else {
            if (!is_callable($tab[0])) {
                // TODO Steven
                die("Merde ! Steven nous a encore hacké !");
            } else {
                $userRight = User::getInstance()->getRight();
                if ($userRight >= $tab[1]) {
                    $tab[0]($request);
                } else {
                    if ($userRight === UserRight::BANNED) {
                        UsersController::bannishAction($request);
                    } else {
                        Controller::forbiddenAction($request);
                    }
                }
            }
        }
    }
}
