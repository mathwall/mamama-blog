<?php

namespace App\Controllers;

use App\Src\TwigLoader;

abstract class Controller {
    static public function loadModel($model) {
        $modelObj = "App\\Models\\" . ucfirst($model) . "Table";
        return class_exists($modelObj) ? new $modelObj : false;
    }

    static public function render($file, $params = []) {
        TwigLoader::render($file, $params);
    }
}