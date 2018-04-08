<?php

namespace App\Controllers;

use App\Models\UsersTable;
use App\Src\User;
use App\Src\Request;


class UsersController extends Controller {
    public static function loginAction(Request $request) {
        // variable de depart pour twig
        $msg = [];

        if($request->getMethod() === "POST") {
            $formParams = $request->getMethodParams();
            $formParams = self::secureDataArray($formParams);

            $email = $formParams["email"];
            $password = $formParams["password"];
            if(empty($email) || empty($password)) {
                $msg["alert"] = "You must fill all fields";
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $msg["alert"] = "Email invalid";
            } else {
                $userModel = new UsersTable();
                $userData = $userModel->getByParams(["email" => $email], true);
                if(self::verifyPassword($password, $userData["password"])) {
                    User::getInstance()->login($email);
                    self::redirect([
                        "request" => $request,
                        "url" => "/",
                    ]);
                } else {
                    $msg["alert"] = "Wrong email/password";
                }
            }
        }
        parent::render('/Users/login.html.twig', [
            "msg" => $msg,
        ]);
    }

    public static function registerAction(Request $request) {
        // variable de depart pour twig
        $msg = [];
        $fields = []; // Sert a garder en memoire les valeurs des inputs du formulaire

        if($request->getMethod() === "POST") {
            $formParams = $request->getMethodParams();
            $formParams = self::secureDataArray($formParams);
            $username = $formParams["username"];
            $email = $formParams["email"];
            $password = $formParams["password"];
            $password_confirmation = $formParams["password_confirmation"];

            $fields = ["username" => $username, "email" => $email];

            if (empty($username) || empty($email) || empty($password) || empty($password_confirmation)) {

                $msg["alert"] = "You must fill all fields";
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $msg["alert"] = "Email invalid";
            } else if ($password != $password_confirmation) {
                $msg["alert"] = "password not same";
            } else {
                $hashed_password = self::hashPassword($password);
                $userModel = new UsersTable();
                if($userModel->createUser($username, $email, $hashed_password)) {
                    var_dump("LOL");
                    User::getInstance()->login($email);
                    var_dump(User::getInstance()->isLogged());
                    self::redirect([
                        "request" => $request,
                        "url" => "/",
                    ]);
                } else {
                    // Check si le login ou email existe deja
                    if($userModel->getByParams(["username" => $username])) {
                        $msg["alert"] = "Username not available";
                    } else if($userModel->getByParams(["email" => $email])) {
                        $msg["alert"] = "Email not available";
                    } else {
                        $msg["alert"] = "Problem during the creation of the account... Damn!";
                    }
                }
            }
        }
        parent::render('/Users/register.html.twig', [
            "msg" => $msg,
            "fields" => $fields,
        ]);
    }

    public static function logoutAction(Request $request) {
        User::getInstance()->logout();
        parent::redirect([
            "request" => $request,
            "url" => "login/",
        ]);
    }

    protected static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    protected static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

}
