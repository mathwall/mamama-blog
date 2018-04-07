<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\CategoriesTable;
use App\Models\CommentsTable;
use App\Models\UsersTable;
use App\Src\Request;
//use App\Controllers\CategoriesController;

//TODO récupérer $error

class ArticlesController extends Controller
{

    /*
    private static function searchBar(){

        $search = [];
        CategoriesController::searchCategory($search);
        return $search;
    }
    */

    public static function displayAllAction(Request $request)
    {
        $article_table = new ArticlesTable();
        $articles = $article_table->getShortAll();
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

        parent::render('/Articles/articles.html.twig', [
            "articles" => $articles,
            "search" => ["categories" => $categories],
        ]);

//        parent::render('/Articles/articles.html.twig', ["articles" => $articles, "search" => self::searchBar()]);
        /* FIXME : Woaaa je ne sais pas qui a fait ca (searchBar), mais c'est pas top :)
         * Il faut vraiment cibler le besoin, ici, si je comprend bienm il faut la liste des categories.
         * Pourquoi creer un controlleur Categories alors que c'est un besoin adapter pour le model des categories!
         * Un controlleur est fait pour gerer des liens de pages, sauf si plus tard on aura besoin de pages vraimeent dediees
         * pour les categories, autrement ya pas de raison de creer le categorycontrolleur.
        */
    }

    public static function displayAction(Request $request)
    {
        $id = $request->getParams()["id"];
        $article_table = new ArticlesTable();
        $comments_table = new CommentsTable();
        $article = $article_table->getById($id);
        // Il faut gerer si l'id fourni dans le url n'existe pas
        if($article) {
            $article['path_image'] = "/img/" . $article['path_image'];
            $article['creation_date'] = parent::dateFormat($article['creation_date']);
            $comments = $comments_table->getByArticleId($article['id']);
            foreach($comments as $key => $comment) {
                $comments[$key]['creation_date'] = parent::dateFormat($comment['creation_date']);
            }
            parent::render('/Articles/article.html.twig', ["article" => $article, "comments" => $comments]);
        } else {
            // TODO
            die("Il n'ya pas d'article...");
        }
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
