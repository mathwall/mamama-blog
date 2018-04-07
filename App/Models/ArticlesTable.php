<?php

namespace App\Models;

class ArticlesTable extends Table
{

    protected $table = "articles";

    public function getAll($orderby = "creation_date", $direction = "DESC")
    {
        $all = parent::getAll($orderby, $direction);
        $user = new UsersTable();
        $category = new CategoriesTable();
        $comments = new CommentsTable();
        //ajouter le nom de l'auteur, le nom de la catégorie, le nombre de comments
        foreach ($all as $key => $article) {
            $all[$key]['author'] = $user->getNamebyId($article['id_writer']);
            $all[$key]['category'] = $category->getNamebyId($article['id_category']);
            $all[$key]['nb_comments'] = $comments->getNbCommentsbyId($article['id']);
        }
        return $all;
    }
    public function getShortAll()
    {
        $all = self::getAll();
        foreach ($all as $key => $article) {
            $all[$key]['content'] = self::getShorterContent($article['content'], 50);
        }
        return $all;
    }
    protected function getShorterContent($content, $nb_car)
    {
        $length = $nb_car;
        if ($nb_car < strlen($content)) {
            $last_space = strrpos(substr($content, 0, $nb_car), ' ');
            return substr($content, 0, $last_space) . "...";
        } else {
            return $content;
        }
    }
    public function getById($id)
    {
        $article = parent::getById($id);
        if(!$article) {
            return false;
        }
        // il faut checker si y a eu un resultat,
        // si c'est vide, la suite va crasher ...
        $user = new UsersTable();
        $category = new CategoriesTable();
        $article['author'] = $user->getNamebyId($article['id_writer']);
        $article['category'] = $category->getNamebyId($article['id_category']);
        return $article;
    }
}
