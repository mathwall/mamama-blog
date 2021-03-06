<?php

namespace App\Models;

class ArticlesTable extends Table
{

    protected $table = "articles";

    // Récupère uniquement les articles rédigés par l'utilisateur courant
    public function getIsOwn($username){

        $articles = parent::query("SELECT * FROM $this->table WHERE id_writer = (SELECT id FROM users WHERE username = '{$username}')", [$username]);
        self::getAdjuntContent($articles);
        self::getShort($articles);
        return $articles;
    }

    // Sélectionne les articles selon les critères de filtrage (titre, auteur, catégorie, tags) définis dans la searchbar
    public function getFiltered($post){

        $sql = "SELECT * FROM $this->table WHERE";
        $array = [];

        if(!empty($post["text"])){
            $array[] = strtolower($post["text"]); 
            $sql .= " (title LIKE '%{$post['text']}%' OR id_writer IN (SELECT id FROM users WHERE username LIKE '%{$post['text']}%' AND user_group != 'USER') OR content LIKE '%{$post['text']}%')";
        }
        if(!empty($post["category"]) && $post["category"] != "all"){
            if(!empty($post["text"]))
                $sql .= " AND";
        
            $sql .= " id_category='{$post['category']}'";
            $array[] = $post["category"];
        }

        if(!empty($post["tag"])){
            if(!is_array($post["tag"])) {
                $post["tag"]=[$post["tag"]];
            }
            if((!empty($post["text"])) && ($post["category"] != "all"))
                $sql .= " AND";

            $tags = implode(', ', $post["tag"]);

            $sql .= " id IN (SELECT id_article FROM articles_tags WHERE id_tag IN ({$tags}))";
            $array[] = $tags;
        }

        $sql .="ORDER BY creation_date DESC";
        $articles = parent::query($sql, $array);
        self::getAdjuntContent($articles);

        return $articles;
    }

    // Ajoute le nom de son auteur, le nom de sa catégorie et son nombre de comments à chaque article récupérés dans une précédente requete
    public function getAdjuntContent(&$data){

        $user = new UsersTable();
        $category = new CategoriesTable();
        $comments = new CommentsTable();

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
        self::getShort($all);
        return $all;
    }

    public function getShort(&$data)
    {
        foreach ($data as $key => $article) {
            $data[$key]['content'] = self::ShorterContent($article['content'], 50);
        }
        return $data;
    }

    protected function ShorterContent($content, $nb_car)
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
