<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\UsersTable;
use App\Src\Request;

class ArticlesController extends Controller
{

    public static function displayAllAction(Request $request)
    {
        $article_table = new ArticlesTable();
        $articles = $article_table->getShortAll();
        if (!$article) {
            $error = "No articles found";
        } else {
            foreach ($articles as $key => $article) {
                $articles[$key]['content'] = nl2br(htmlspecialchars($article['content']));
                $articles[$key]['title'] = nl2br(htmlspecialchars($article['title']));
                $articles[$key]['path_image'] = htmlspecialchars($article['path_image']);
                $articles[$key]['author'] = htmlspecialchars($article['author']);
                $articles[$key]['category'] = htmlspecialchars($article['category']);
            }
        }
        //// RETURN SOUS FORMAT TWIIIIIIG
        try {
            echo $twig->render('Views/Articles/articles.html.twig', array("articles" => $articles));
        } catch (Exception $exception) {
            die("Page non trouvee");
        }
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
