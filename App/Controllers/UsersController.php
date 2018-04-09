<?php

namespace App\Controllers;

use App\Models\UsersTable;
use App\Src\User;
use App\Src\Request;


class UsersController extends Controller {
    static public function loginAction(Request $request) {
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

    static public function registerAction(Request $request) {
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
                    User::getInstance()->login($email);
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

    static public function settingAction(Request $request) {
        // variable de depart pour twig
        $msg = [];

        $modifyParameters = [];
        $currentUserId = User::getInstance()->getId();
        $userModel = new UsersTable();
        $user = $userModel->getById($currentUserId);


        // si l'utilisateur n'existe pas, 404
        if (!$user) {
            self::notFoundPageAction($request);
            die();
        }

        if($request->getMethod() === "POST") {
            $formParams = $request->getMethodParams();
            $formParams = self::secureDataArray($formParams);
            $current_password = $formParams["current-password"];
            $password = $formParams["new-password"];
            $password_confirmation = $formParams["password_confirmation"];

            if (empty($current_password)) {
                $msg["alert"] = "Password not defined";
            } else {
                $fileAvatar = $request->getFiles()["avatar"]["tmp_name"];
                if($fileAvatar) {
                    $filePath = self::saveUploadFile($fileAvatar, "avatar/", $user["username"]);
                    $modifyParameters["path_avatar"] = $filePath;
                }

                if(!empty($password)) {
                    if ($password != $password_confirmation) {
                        $msg["alert"] = "password not same";
                    } else if (!self::verifyPassword($current_password, $user["password"] )) {
                        $msg["alert"] = "password wrong";
                    } else {
                        $hashed_password = self::hashPassword($password);
                        $modifyParameters["password"] = $hashed_password;
                    }
                }

                if(count($modifyParameters) > 0) {
                    $userModel->modifyById($currentUserId, $modifyParameters);
                    $msg["success"] = "Account Updated";
                    $user = $userModel->getById($currentUserId);
                } else {
                    $msg["alert"] = "Nothing to update";
                }
            }
        }
        parent::render('/Users/setting.html.twig', [
            "msg" => $msg,
            "user" => $user,
        ]);

    }

    static public function editAction(Request $request) {
        // variable de depart pour twig
        $msg = [];

        $modifyParameters = [];
        $id = $request->getParams()["id"];
        $userModel = new UsersTable();
        $user = $userModel->getById($id);


        // si l'utilisateur n'existe pas, 404
        if (!$user) {
            self::notFoundPageAction($request);
            die();
        }

        if($request->getMethod() === "POST") {
            $formParams = $request->getMethodParams();
            $formParams = self::secureDataArray($formParams);
            $password = $formParams["password"];
            $status = isset($formParams["status"]) ? "1" : "0";

            $fileAvatar = $request->getFiles()["avatar"]["tmp_name"];
            if($fileAvatar) {
                $filePath = self::saveUploadFile($fileAvatar, "avatar/", $user["username"]);
                $modifyParameters["path_avatar"] = $filePath;
            }

            if(!empty($password)) {
                $hashed_password = self::hashPassword($password);
                $modifyParameters["password"] = $hashed_password;
            }

            $modifyParameters["status"] = $status;

            $modifyParameters["user_group"] = $formParams["user_group"];

            if(count($modifyParameters) > 0) {
                $userModel->modifyById($id, $modifyParameters);
                $msg["success"] = "Account Updated";
                $user = $userModel->getById($id);
            } else {
                $msg["alert"] = "Nothing to update";
            }
        }
        parent::render('/Users/edit.html.twig', [
            "msg" => $msg,
            "user" => $user,
        ]);

    }

    static public function logoutAction(Request $request) {
        User::getInstance()->logout();
        parent::redirect([
            "request" => $request,
            "url" => "articles/",
        ]);
    }

    static public function bannishAction(Request $request) {
        parent::render('/Users/bannish.html.twig', []);
    }

    static protected function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    static protected function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

}
