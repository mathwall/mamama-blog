<?php

namespace App\Models;

class ArticlesTable extends Table
{

    protected $table = "articles";

    public function getFiltered($post){

        $sql = "SELECT * FROM $this->table WHERE ";
        $array = [];
        
        if(!empty($post["text"])){
            $array[] = $post["text"]; 
            $sql .= " (title={$post['text']} OR id_writer = (SELECT id FROM users WHERE name LIKE '%{$post['text']}%' AND user_group != 'USER'))";
        }
        if(!empty($post["category"])){
            if(!empty($post["text"]))
                $sql .= " AND";
        
            $sql .= " id_category={$post['category']}";
            $array[] = $post["category"];
        }
        if(!empty($post["tag"])){
            if(!empty($post["text"]) || (!empty($post["category"])))
                $sql .= " AND";

            $tags = "";
            foreach($post["tag"] as $id_tag){
                $tags .= $id_tag . ",  ";
            }
            rtrim($tags, ', ');

            $sql .= " id IN (SELECT id_articles FROM articles_tags WHERE id_tags IN ({$tags}))";
            $array[] = $tags;
        }

        $articles = parent::query($sql, $array);
        self::getAdjuntContent($articles);
        return $articles;
    }

    public function getAdjuntContent(&$data){

        $user = new UsersTable();
        $category = new CategoriesTable();
        $comments = new CommentsTable();
        //ajouter le nom de l'auteur, le nom de la catégorie, le nombre de comments

        foreach ($data as $key => $article) {
            $data[$key]['author'] = $user->getNamebyId($article['id_writer']);
            $data[$key]['category'] = $category->getNamebyId($article['id_category']);
            $data[$key]['nb_comments'] = $comments->getNbCommentsbyId($article['id']);
        }
    }

    public function getAll($orderby = "creation_date", $direction = "DESC")
    {
        $all = parent::getAll($orderby, $direction);
        self::getAdjuntContent($all);
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
