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
        $comments = $this->query("SELECT * FROM " . $this->table . " WHERE id_article = ?", [$id]);
        foreach($comments as $key => $comment) {
            $comments[$key]['author'] = $user->getNamebyId($comment['id_writer']);        
        }
        return $comments;
    }

}
