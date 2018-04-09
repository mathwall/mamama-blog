<?php

namespace App\Controllers;

use App\Models\ArticlesTable;
use App\Models\CategoriesTable;
use App\Models\CommentsTable;
use App\Models\TagsTable;
use App\Src\Request;
use App\Src\User;
use App\Src\UserRight;

class ArticlesController extends Controller
{
    public static function displayAllAction(Request $request)
    {
        $errors = [];
        $article_table = new ArticlesTable();
        $tags_models = new TagsTable();
        $post = $request->getMethodParams();
        if (!empty($post) && (!empty($post['text']) || (!empty($post['category']) && $post['category'] != "all") || !empty($post['tag']))) {
            $post = parent::secureDataArray($post);
            $articles = $article_table->getFiltered($post);
        } else {
            $articles = $article_table->getAll();
        }

        if (!$articles) {
            $errors["articles"] = "No articles found";
        } else {
            foreach ($articles as $key => $article) {
                $articles[$key]['path_image'] = $article['path_image'];
                $articles[$key]['creation_date'] = parent::dateFormat($article['creation_date']);
                $articles[$key]['tags'] = $tags_models->getByArticleId($article['id']);
            }
        }
        // Recuperation des categories en passant par le MODEL
        $categories_models = new CategoriesTable();
        $categories = $categories_models->getByDesc();

        $tags = $tags_models->getAll();

        $comments_table = new CommentsTable();
        $comments = $comments_table->getByArticleId($article['id']);
        foreach ($comments as $key => $comment) {
            $comments[$key]['creation_date'] = parent::dateFormat($comment['creation_date']);
        }

        parent::render('/Articles/articles.html.twig', [
            "articles" => $articles,
            "comments" => $comments,
            "search" => ["categories" => $categories, "tags" => $tags],
            "errors" => $errors,
        ]);
    }

    public static function displayAction(Request $request)
    {
        $id = $request->getParams()["id"];
        $article_table = new ArticlesTable();
        $comments_table = new CommentsTable();
        $categories_table = new CategoriesTable();
        $tags_models = new TagsTable();

        // 1. RÉCUPÉRER LES DONNÉES DE L'ARTICLE
        $article = $article_table->getById($id);
        if ($article) {
            $no_article = false;
            if ($request->getMethod() == "POST") {
                $new_comment = [];
                $new_comment['id_writer'] = User::getInstance()->getId();
                $new_comment['id_article'] = $article['id'];
                $new_comment['content'] = $request->getMethodParams()['content'];
                $new_comment['creation_date'] = date("Y-m-d H:i:s");
                if (!empty($new_comment['content'])) {
                    $comments_table->create($new_comment);
                }
            }
            $article['path_image'] = $article['path_image'];
            $article['creation_date'] = parent::dateFormat($article['creation_date']);
            // 2. RÉCUPÉRER LES COMMENTAIRES
            $comments = $comments_table->getByArticleId($article['id']);
            foreach ($comments as $key => $comment) {
                $comments[$key]['creation_date'] = parent::dateFormat($comment['creation_date']);
            }
            // 3. RÉCUPÉRER LES TAGS
            $tags = $tags_models->getByArticleId($article['id']);
            parent::render('/Articles/article.html.twig', [
                "no_article" => $no_article,
                "article" => $article,
                "comments" => $comments,
                "tags" => $tags,
            ]);
        } else {
            $no_article = true;
            parent::render('/Articles/article.html.twig', ["no_article" => $no_article]);
        }
    }

    public static function createAction(Request $request)
    {
        // variable pour twig,
        // $msg sera un tableau avec "alert" et "success",
        // ca permettra a twig d'afficher les message de success ou d'echec
        $msg = [];

        $article_table = new ArticlesTable();
        $categories_table = new CategoriesTable();
        //SI L'UTILISATEUR A DÉJÀ CLIQUÉ SUR Create, GÉRER LA CRÉATION DE L'ARTICLE
        if ($request->getMethod() == "POST") {
            //1. Récupérer l'article + catégorie
            $new_article = $request->getMethodParams();
            //2. Récupérer l'image
            if ($request->getFiles()["path_image"]["tmp_name"]) {
                $fileImg = $request->getFiles()["path_image"]["tmp_name"];
            } else {
                $fileImg = false;
            }
            if ($fileImg) {
                $filePath = self::saveUploadFile($fileImg, "article_img");
                $new_article["path_image"] = $filePath;
            }
            //3. Vérifier si les champs Title et Content ne sont pas vides avant de créer l'article
            $title = $new_article["title"];
            $content = $new_article["content"];
            if (!empty($title) && !empty($content)) {
                $new_article['id_writer'] = User::getInstance()->getId();
                $new_article['creation_date'] = date("Y-m-d H:i:s");
                //4. Contrôler si la création de l'article a réussi
                if ($article_table->create($new_article)) {
                    $msg["success"] = "Creation successfull";
                } else {
                    $msg["alert"] = "Error during the creation :(";
                }
            } else {
                $msg["alert"] = "You must fill all fields";
            }
        }
        //POUR PERMETTRE LE CHOIX DE CATÉGORIE AU MOMENT DE LA CRÉATION DE L'ARTICLE, renvoyer la liste des catégories
        $categories = $categories_table->getAll();
        parent::render('/Articles/create.html.twig', [
            "categories" => $categories,
            "msg" => $msg,
        ]);
    }

    public static function editAction(Request $request)
    {
        $id = $request->getParams()["id"];
        $article_table = new ArticlesTable();
        $categories_table = new CategoriesTable();
        $msg = [];

        //SI L'UTILISATEUR A DÉJÀ CLIQUÉ SUR Edit, GÉRER L'ÉDITION DE L'ARTICLE
        if ($request->getMethod() == "POST") {
            //1. Récupérer l'article + catégorie
            $edit_article = $request->getMethodParams();
            //2. Récupérer l'image
            if ($request->getFiles()["path_image"]["tmp_name"]) {
                $fileImg = $request->getFiles()["path_image"]["tmp_name"];
            } else {
                $fileImg = false;
            }
            if ($fileImg) {
                $filePath = self::saveUploadFile($fileImg, "article_img");
                $edit_article["path_image"] = $filePath;
            }
            //3. Vérifier si les champs Title et Content ne sont pas vides avant d'éditer l'article
            $title = $edit_article["title"];
            $content = $edit_article["content"];
            if (!empty($title) && !empty($content)) {
                $edit_article['edition_date'] = date("Y-m-d H:i:s");
                //4. Contrôler si l'édition de l'article a réussi
                if ($article_table->modifyById($id, $edit_article)) {
                    $msg["success"] = "Edition successfull";
                } else {
                    $msg["alert"] = "Error during the edition :(";
                }
            } else {
                $msg["alert"] = "You must fill all fields";
            }
        }
        //PAR DÉFAUT, AFFICHER LES DONNÉES DE L'ARTICLE QUE L'ON SOUHAITE ÉDITER
        $article = $article_table->getById($id);
        $categories = $categories_table->getAll();
        var_dump($msg);
        parent::render('/Articles/edit.html.twig', [
            "article" => $article,
            "categories" => $categories,
            "msg" => $msg,
        ]);
    }

    // Ceci est un controller pour de l'AJAX uniquement
    public static function deleteAction(Request $request)
    {

        if ($request->getMethod() === "DELETE") {
            $data = $request->getMethodParams();
            $id = $data["id"];
            if (empty($id)) {
                self::sendJsonErrorAndDie("Id is empty!");
            }

            $articleModel = new ArticlesTable();
            // On check si l'article existe bien
            $articleData = $articleModel->getById($id);
            if (!$articleData) {
                self::sendJsonErrorAndDie("Article does not exist!");
            }

            $user = User::getInstance();
            // On check si l'utilisateur a les droits pour supprimer l'article
            if ($user->getRight() >= UserRight::ADMIN ||
                ($user->getRight() === UserRight::WRITER && $articleData["id_writer"] === $user->getId())) {
                $articleModel->deleteById($id);
                self::sendJsonDataAndDie(["success" => true]);
            } else {
                self::sendJsonErrorAndDie("You don't have right to delete this article!");
            }
        } else {
            self::sendJsonErrorAndDie("Steven, is it you??");
        }
    }
}
