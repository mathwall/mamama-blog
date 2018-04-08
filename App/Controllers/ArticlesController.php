<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\CategoriesTable;
use App\Models\TagsTable;
use App\Models\CommentsTable;
use App\Src\Request;
use App\Src\User;

class ArticlesController extends Controller
{
    public static function displayAllAction(Request $request)
    {
        $errors = [];
        $article_table = new ArticlesTable();
        $post = $request->getMethodParams();

        if(isset($post) && !empty($post["text"])){
            $post = parent::secureDataArray($post);
            $articles = $article_table->getFiltered($post);
        } else {
            $articles = $article_table->getAll();
        }

        if (!$articles) {
            $errors["articles"] = "No articles found";
        } else {
            foreach ($articles as $key => $article) {
                $articles[$key]['path_image'] = "/media/" . $article['path_image'];
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
            "search" => ["categories" => $categories, "tags" => $tags],
            "errors" => $errors,
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
        // $request = new Request();
        $article = $article_table->getById($id);
        if ($article) {
            if ($request->getMethod() == "POST") {
                $new_comment = [];
                $new_comment['id_writer'] = User::getInstance()->getId();
                $new_comment['id_article'] = $article['id'];
                $new_comment['content'] = $request->getMethodParams()['content'];
                $new_comment['creation_date'] = date("Y-m-d H:i:s");
                //TODO vérifier que le commentaire n'est pas vide !
                $comments_table->create($new_comment);
            }
            $article['path_image'] = "/media/" . $article['path_image'];
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
        $article_table = new ArticlesTable();
        $categories_table = new CategoriesTable();
        if ($request->getMethod() == "POST") {
            $new_article = $request->getMethodParams();
            $new_article['id_writer'] = User::getInstance()->getId();
            $new_article['creation_date'] = date("Y-m-d H:i:s");
            $article_table->create($new_article);
        }
        // renvoyer la liste des catégories sous forme de tableau
        $categories = $categories_table->getAll();
        //renvoyer un tableau edit qui indique qu'on est en mode "create"
        $edit = [];
        $edit['status'] = "Create";
        $edit['title'] = "";
        $edit['content'] = "";
        parent::render('/Articles/create.html.twig', ["edit" => $edit, "categories" => $categories]);
    }

    public static function editAction(Request $request)
    {
        $id = $request->getParams()["id"];
        $article_table = new ArticlesTable();
        $categories_table = new CategoriesTable();
        if ($request->getMethod() == "POST") {
            $edit_article = $request->getMethodParams();
            $edit_article['edition_date'] = date("Y-m-d H:i:s");
            $article_table->modifyById($id, $edit_article);
        }
        $article = $article_table->getById($id);
        $categories = $categories_table->getAll();
        parent::render('/Articles/edit.html.twig', ["article" => $article, "categories" => $categories]);
    }
}
