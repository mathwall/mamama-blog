<?php

namespace App\Controllers;

use App\Models\CategoriesTable;

class CategoriesController extends Controller{

    static public function searchCategory(&$search){
        
        $categories = CategoriesTable::getByDesc();

        if (!$categories) {
            $error = "No articles found";
        } else {
            foreach ($categories as $key => $category) {
                $categories[$key] = parent::beforeRender($category);
            }
        }
        $search["categories"] = $categories;
    }
}