<?php

namespace App\Models;

class TagsTable extends Table {

    protected $table = "tags";

    public function getByArticleId($id){

        $tags = [];
        $result = parent::query("SELECT id_tag FROM articles_tags WHERE id_article = {$id}", [$id]);
        
        foreach($result as $tag){
            $tags[] = $this->getById($tag["id_tag"]);
        }
        return $tags;
    }
}