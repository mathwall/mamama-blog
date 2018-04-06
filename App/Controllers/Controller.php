<?php

namespace App\Controllers;

use App\Src\TwigLoader;

abstract class Controller
{
    public static function loadModel($model)
    {
        $modelObj = "App\\Models\\" . ucfirst($model) . "Table";
        return class_exists($modelObj) ? new $modelObj : false;
    }

    static public function beforeRender($array){

        foreach($array as $key => $value){
            $array[$key] = nl2br(htmlspecialchars($value));
        }
        return $array;
    }
    
    public static function dateFormat($string)
    {
        return substr($string, 0, 10);
    }
    
    static public function render($file, $params = []) {
        TwigLoader::render($file, $params);
    }
}
