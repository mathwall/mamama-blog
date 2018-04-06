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
        foreach($all as $key => $article) {
            $all[$key]['content'] = self::getShorterContent($article['content'], 10);
        }
        return $all;
    }
    protected function getShorterContent($content, $nb_car)
    {
        $length = $nb_car;
        if ($nb_car < strlen($content)) {
            while (($content{$length} != " ") && ($length > 0)) {
                $length--;
            }
            if ($length == 0) {return substr($content, 0, $nb_car) . "...";
            } else {
                return substr($content, 0, $length) . "...";
            }
        } else {
            return $content;
        }
    }
}
