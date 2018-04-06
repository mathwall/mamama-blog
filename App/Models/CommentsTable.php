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

}
