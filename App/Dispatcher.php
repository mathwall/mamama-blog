<?php

namespace App;

use App\Src\Router;
use App\Src\Request;

class Dispatcher
{
    static public function redirect(Request $request)
    {
        $tab = Router::match($request);

        if (!is_callable($tab[0])) {
            // TODO
            die("Merde ! Steven nous a encore hacké !");
        } else {
//            if (Session::read('group') >= $tab[1]) {
            //TODO
            if (3 >= $tab[1]) {
                $tab[0]($request);
            } else {
                //TODO
                die("Pas les droits");
            }
        }
    }
}
