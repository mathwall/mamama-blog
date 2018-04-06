<?php

namespace App\Controllers;

use App\Models\UsersTable;
use App\Src\Request;

class ArticlesController extends Controller {

    static public function displayAllAction(Request $request) {
        die("page displayAll");
    }

    static public function displayAction(Request $request) {
        $id = $request->getParams()["id"];
        die("page display pour id $id");
    }

    static public function editAction(Request $request) {
        // TODO Ceci est un exemple,
        // A adapter selon l'action
        $model = new UsersTable();
        $params = $request->getParams();
        parent::render("edit.html.twig", [
            "id" => $params["id"],
        ]);
    }
}