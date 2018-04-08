<?php

namespace App\Controllers;

//use App\Dispatcher;
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
            "username" => $user->getUsername(),
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

    static public function dateFormat($string)
    {
        return substr($string, 0, 10);
    }

    /**
     * @param $param array [string "url", Request "request"]
     */
    static public function redirect($param) {
        try {
            $request = $param["request"];
            $url = $param["url"];
            $request->setUrl($url);
            header("Location: $url");

            // J'utilise le header pour l'instant, car le dispatcher ne change pas le nouveau url sur la barre de navigation :(
//            Dispatcher::redirect($request);
            die();
        } catch (\Exception $e) {
            die("missing parameter");
        }

    }

    static public function render($file, $params = []) {
        self::beforeRender($params);
        TwigLoader::render($file, $params);
    }


    /**
     * @param $file
     * @param string $prefix_path Prefix /media/{prefix}/
     * @param null $name Si null, on genere un nom aleatoire avec uniqid, sinon on le nomme avec la variable
     * @return null|string
     */
    static protected function saveUploadFile($file, $prefix_path = "", $name = null) {
        try {
            if($name) {
                $path = "media/" . $prefix_path . "/" . $name;
            } else {
                $path = "media/" . $prefix_path . "/" . uniqid(time());
            }
            move_uploaded_file($file, $path);
            return $path;
        } catch (\Exception $e) {
            return null;
        }
    }
}
