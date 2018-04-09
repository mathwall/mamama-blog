<?php

namespace App\Controllers;

use App\Src\Request;


class CategoriesController extends Controller {
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
            $password = trim($password);
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
}
