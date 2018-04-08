<?php

namespace App\Models;

class CommentsTable extends Table
{
    protected $table = "comments";

    public function getNbCommentsById($id)
    {
        $comments = $this->query("SELECT id FROM " . $this->table . " WHERE id_article = ?", [$id]);
        return count($comments);
    }
    public function getByArticleId($id)
    {
        $user = new UsersTable();

        // FIXME
        // Pour Mathilde : Exemple de requete avec JOIN

        return $this->query("
                    SELECT *, users.username as author, users.path_avatar as path_avatar
                    FROM {$this->table}
                    JOIN users ON {$this->table}.id_writer = users.id
                    WHERE id_article = ?
                    ", [$id]);

//        $comments = $this->query("SELECT * FROM " . $this->table . " WHERE id_article = ?", [$id]);
//        foreach($comments as $key => $comment) {
//            $comments[$key]['author'] = $user->getNamebyId($comment['id_writer']);
//        }
//        return $comments;
    }

}
