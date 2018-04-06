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
    static public function getByDesc(){

        $categories = [];
        $result = parent::query("SELECT * FROM categories WHERE parent_id IS NULL", null, true);

        foreach($result as $category){
            $categories[] = $category;
            self::getChildren($categories, $result[$category]["id"]);
        }
        return $categories;
    }

    static public function getChildren(&$categories, $parent_id){

        $result = parent::query("SELECT * FROM categories WHERE parent_id = {$parent_id}", [$parent_id], true);
            
        if(!empty($result)){
            
            foreach($result as $children){
                $categories[] = $children;
                self::getChildren($categories, $result[$children]["id"]);
            }
        }
    }
}