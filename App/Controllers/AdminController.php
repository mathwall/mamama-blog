<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\UsersTable;
use App\Src\Request;
use App\Src\User;

class AdminController extends Controller{

    static public function displayAllAction(){

        $articles_table = new ArticlesTable();
        $user = User::getInstance();

        // il faut penser a set les variables qui vont passer dans twig au tout debut,
        // par exemple, ici, dans la condition du WRITER, $users n'est pas defini, ce qui creer un probleme a la fin
        $users = null;
        $errors = null;

        if($user->getRight() == "WRITER"){
            echo "here";
            $articles = $articles_table->getIsOwn($user->getUsername());

        } else {
            $articles = $articles_table->getAll();
            $users_table = new UsersTable();
            $users = $users_table->getAll();

            if(!$users)
                $errors["users"] = "No users found";
        }

        if(!$articles)
                $errors["articles"] = "No articles found";

        self::render('/Admin/admin.html.twig', [
            "articles" => $articles,
            "users" => $users,
            "errors" => $errors,
        ]);
    }

    static public function displayUsersAction(Request $request) {
        $usersModel = new UsersTable();

        $errors = null;
        $users = $usersModel->getAll();

        self::render('/Admin/admin_users.html.twig', [
            "users" => $users,
            "errors" => $errors,
        ]);

    }

    static public function displayTagsAction(Request $request) {

    }

    static public function displayCommentsAction(Request $request) {

    }

    static public function displayArticlesAction(Request $request) {
        $articlesModel = new ArticlesTable();
        $errors = null;
        $articles = $articlesModel->getAll();

        self::render('/Admin/admin_articles.html.twig', [
            "articles" => $articles,
            "errors" => $errors,
        ]);
    }

    static public function displayCategoriesAction(Request $request) {

    }
}