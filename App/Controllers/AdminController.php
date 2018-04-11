<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\CategoriesTable;
use App\Models\UsersTable;
use App\Src\Request;
use App\Src\User;

class AdminController extends Controller{

    static public function displayAllAction(){
        $errors = null;
        $user = User::getInstance();

        $usersModel = new UsersTable();
        $users = $usersModel->getAll();

        $articlesModel = new ArticlesTable();

        $categoriesModel = new CategoriesTable();
        $categories = $categoriesModel->getByDesc();

        if($user->getRight() == "WRITER"){
            echo "here";
            $articles = $articlesModel->getIsOwn($user->getUsername());

        } else {
            $articles = $articlesModel->getAll();
            if(!$users)
                $errors["users"] = "No users found";
        }

        if(!$articles)
                $errors["articles"] = "No articles found";

        self::render('/Admin/admin.html.twig', [
            "articles" => $articles,
            "users" => $users,
            "categories" => $categories,
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
        $categoriesModel = new CategoriesTable();
        $errors = null;
        $categories = $categoriesModel->getByDesc();

        self::render('/Admin/admin_categories.html.twig', [
            "categories" => $categories,
            "errors" => $errors,
        ]);

    }
}