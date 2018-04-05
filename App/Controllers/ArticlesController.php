<?php

namespace App\Controllers;

use App\Models\UsersTable;
use App\Src\Request;

class ArticlesController extends Controller {

    static public function editAction(Request $request) {

//        $model = parent::loadModel("users");
        $model = new UsersTable();

//        var_dump($model->createUser("Mathildea", "mariea@caca.fr", "denis"));

        $params = $request->getParams();
        parent::render("edit.html.twig", [
            "id" => $params["id"],
        ]);
    }
}