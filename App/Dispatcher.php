<?php

namespace App;

use App\Src\Router;
use App\Src\Request;

class Dispatcher
{
    static public function redirect(Request $request)
    {
        $tab = Router::match($request);

        if(!$tab) {
            // TODO 404
            die("Page 404");
        } else {
            if (!is_callable($tab[0])) {
                // TODO Steven
                die("Merde ! Steven nous a encore hacké !");
            } else {
//            if (Session::read('group') >= $tab[1]) {
                //TODO session read group
                if (3 >= $tab[1]) {
                    $tab[0]($request);
                } else {
                    //TODO pas les droits
                    die("Pas les droits");
                }
            }
        }
    }
}
