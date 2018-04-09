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
            $category["prefix"] = 0;
            $categories[] = $category;
            self::getChildren($categories, $category["id"], 0);
        }
        return $categories;
    }

    protected function getChildren(&$categories, $parent_id, $prefix){

        $result = parent::query("
                SELECT category.*, category_parent.name parent_name
                FROM {$this->table} as category
                JOIN {$this->table} as category_parent
                ON category.parent_id = category_parent.id
                WHERE category.parent_id = $parent_id
                ");

        if(!empty($result)){
            foreach($result as $children){
                $children["prefix"] = $prefix + 1;
                $categories[] = $children;
                self::getChildren($categories, $children["id"], $prefix + 1);
            }
        }
    }
}