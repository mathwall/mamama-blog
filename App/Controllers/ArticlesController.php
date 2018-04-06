<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\UsersTable;
use App\Src\Request;

class ArticlesController extends Controller
{

    public static function displayAllAction(Request $request)
    {
        $article = new ArticlesTable();
        $articles_list = $article->getShortAll();
        if (!$articles_list) {
            $error = "No articles found";
        } else {
            foreach ($articles_list as $key => $article) {
                $articles_list[$key]['content'] = nl2br(htmlspecialchars($article['content']));
                $articles_list[$key]['title'] = nl2br(htmlspecialchars($article['title']));
                $articles_list[$key]['path_image'] = htmlspecialchars($article['path_image']);
                $articles_list[$key]['author'] = htmlspecialchars($article['author']);
                $articles_list[$key]['category'] = htmlspecialchars($article['category']);            
            }
        }
        return $articles_list;
    }

    public static function displayAction(Request $request)
    {
        $id = $request->getParams()["id"];
        die("page display pour id $id");
    }

    public static function editAction(Request $request)
    {
        // TODO Ceci est un exemple,
        // A adapter selon l'action
        $model = new UsersTable();
        $params = $request->getParams();
        parent::render("edit.html.twig", [
            "id" => $params["id"],
        ]);
    }
}
