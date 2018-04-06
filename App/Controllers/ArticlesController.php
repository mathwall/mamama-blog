<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\CommentsTable;
use App\Models\UsersTable;
use App\Src\Request;

class ArticlesController extends Controller
{

    public static function displayAllAction(Request $request)
    {
        $article_table = new ArticlesTable();
        $articles = $article_table->getShortAll();
        if (!$articles) {
            $error = "No articles found";
        } else {
            foreach ($articles as $key => $article) {
                $articles[$key] = parent::beforeRender($articles[$key]);
                $articles[$key]['path_image'] = "/img/" . $article['path_image'];
                $articles[$key]['creation_date'] = parent::dateFormat($article['creation_date']);
            }
        }
        parent::render('/Articles/articles.html.twig', ["articles" => $articles]);
    }

    public static function displayAction(Request $request)
    {
        $id = $request->getParams()["id"];
        $article_table = new ArticlesTable();
        $comments_table = new CommentsTable();
        $article = $article_table->getById($id);

// if method = post
// $new_comment = [];
// $new_comment['id_writer'] = get user_id from session
// $new_comment['id_article'] = $article['id'];
// $new_comment['content'] = get post content
// $comments_table->create($new_comment);
// else :


        $article = parent::beforeRender($article);
        $article['path_image'] = "/img/" . $article['path_image'];
        $article['creation_date'] = parent::dateFormat($article['creation_date']);        
        $comments = $comments_table->getByArticleId($article['id']);
        foreach($comments as $key => $comment) {
            $comments[$key] = parent::beforeRender($comments[$key]);
            $comments[$key]['creation_date'] = parent::dateFormat($comment['creation_date']);   
        }
        parent::render('/Articles/article.html.twig', ["article" => $article, "comments" => $comments]);
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
