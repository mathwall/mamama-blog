<?php

namespace App\Controllers;

use App\Src\TwigLoader;
use App\Src\User;
use App\Src\UserRight;

abstract class Controller
{
    public static function loadModel($model)
    {
        $modelObj = "App\\Models\\" . ucfirst($model) . "Table";
        return class_exists($modelObj) ? new $modelObj : false;
    }

    static public function beforeRender(&$array){
        self::secureDataArray($array);
        $user = User::getInstance();
        $array["currentUser"] = [
            "isLogged" => $user->isLogged(),
            "user_group" => $user->getRight(),
        ];

        $array["constant"] = [
            "group" => [
                "invite" => UserRight::INVITE,
                "user" => UserRight::USER,
                "writer" => UserRight::WRITER,
                "admin" => UserRight::ADMIN,
            ],
        ];

    }

    static public function secureDataArray($array)
    {
        foreach($array as $key => $value){
            if(is_array($array[$key])) {
                $array[$key] = self::secureDataArray($array[$key]);
            } else {
                $array[$key] = nl2br(htmlspecialchars($value));
            }
        }
        return $array;
    }
    
    public static function dateFormat($string)
    {
        return substr($string, 0, 10);
    }
    
    static public function render($file, $params = []) {
        self::beforeRender($params);
        TwigLoader::render($file, $params);
    }
}
