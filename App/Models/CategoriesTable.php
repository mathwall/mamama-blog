<?php

namespace App\Models;

class CategoriesTable extends Table
{
    protected $table = "categories";

    public function getNameById($id)
    {
        $all = parent::getById($id);
        return $all['name'];
    }
}