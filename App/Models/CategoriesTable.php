<?php

namespace App\Models;

class CategoriesTable extends Table
{
    protected $table = "categories";

    public function getNameById($id)
    {
        $category = parent::getById($id);
        return $category['name'];
    }
    
    // Retourne un tableau contenant les catégories, triées par descendance
    public function getByDesc(){

        $categories = [];
        $result = parent::query("SELECT * FROM categories WHERE parent_id IS NULL", null);

        foreach($result as $category){
            $categories[] = $category;
            self::getChildren($categories, $category["id"]);
        }
        return $categories;
    }

    public function getChildren(&$categories, $parent_id){

        $result = parent::query("SELECT * FROM $this->table WHERE parent_id = {$parent_id}", [$parent_id]);

        if(!empty($result)){
            foreach($result as $children){
                $categories[] = $children;
                self::getChildren($categories, $children["id"]);
            }
        }
    }
}