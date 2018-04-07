<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\CategoriesTable;
use App\Models\TagsTable;
use App\Models\CommentsTable;
use App\Models\UsersTable;
use App\Src\User;
use App\Src\Request;

//TODO récupérer $error

class ArticlesController extends Controller
{
    public static function displayAllAction(Request $request)
    {
        $article_table = new ArticlesTable();
        $post = $request->getMethodParams();
        
        if(!empty($post)){
            $post = parent::secureDataArray($post);
            $articles = $article_table->getFiltered($post);
        } else {
            $articles = $article_table->getShortAll();
        }
        
        if (!$articles) {
            $error = "No articles found";
        } else {
            foreach ($articles as $key => $article) {
                $articles[$key]['path_image'] = "/img/" . $article['path_image'];
                $articles[$key]['creation_date'] = parent::dateFormat($article['creation_date']);
            }
        }

        // Recuperation des categories en passant par le MODEL
        $categories_models = new CategoriesTable();
        $categories = $categories_models->getByDesc();

        $tags_models = new TagsTable();
        $tags = $tags_models->getAll();

        parent::render('/Articles/articles.html.twig', [
            "articles" => $articles,
            "search" => ["categories" => $categories, "tags" => $tags]
        ]);
}

    /* POUR MATHILDE

        $tags_models = new TagsTable();
        $tags = $tags_models->getByArticleId(1);
        foreach ($tags as $key => $tag) {
            $tags[$key] = parent::beforeRender($tag);
        }

    */

    public static function displayAction(Request $request)
    {
        $id = $request->getParams()["id"];
        $article_table = new ArticlesTable();
        $comments_table = new CommentsTable();
        $request = new Request();
        $article = $article_table->getById($id);
        if ($article) {
            if ($request->getMethod() == "POST") {
                $new_comment = [];
                $new_comment['id_writer'] = User::getInstance()->getId();
                $new_comment['id_article'] = $article['id'];
                $new_comment['content'] = $request->getMethodParams()['content'];
                $new_comment['creation_date'] = date("Y-m-d H:i:s");
                $comments_table->create($new_comment);
            }
            $article['path_image'] = "/img/" . $article['path_image'];
            $article['creation_date'] = parent::dateFormat($article['creation_date']);
            $comments = $comments_table->getByArticleId($article['id']);
            foreach ($comments as $key => $comment) {
                $comments[$key]['creation_date'] = parent::dateFormat($comment['creation_date']);
            }
            parent::render('/Articles/article.html.twig', ["article" => $article, "comments" => $comments]);
        } else {
            // TODO
            die("Il n'ya pas d'article...");
        }
    }

    public static function createAction(Request $request)
    {
        if ($request->getMethod() == "POST") {

        }
        parent::render('/Articles/article_edit.html.twig', []);
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
