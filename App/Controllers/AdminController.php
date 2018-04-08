<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\UsersTable;
use App\Src\User;

class AdminController extends Controller{

    public static function displayAllAction(){

        $articles_table = new ArticlesTable();
        $user = User::getInstance();

        if($user->getRight() == "WRITER"){
            echo "here";
            $articles = $articles_table->getIsOwn($user->getUsername());

        } else {
            $articles = $articles_table->getAll();
            $users_table = new UsersTable();
            $users = $users_table->getAll();

            if(!users)
                $errors["users"] = "No users found";
        }

        if(!$articles)
                $errors["articles"] = "No articles found";

        parent::render('/Admin/admin.html.twig', [
            "articles" => $articles,
            "users" => $users,
            "errors" => $errors,
        ]);
    }
}