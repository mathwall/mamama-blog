<?php

namespace App\Controllers;

use App\Models\CategoriesTable;
use App\Src\Request;
use App\Src\User;
use App\Src\UserRight;


class CategoriesController extends Controller {
    static public function editAction(Request $request) {
        // variable de depart pour twig
        $msg = [];

        $modifyParameters = [];
        $id = $request->getParams()["id"];
        $categoriesModel = new CategoriesTable();
        $category = $categoriesModel->getById($id);


        // si l'utilisateur n'existe pas, 404
        if (!$category) {
            self::notFoundPageAction($request);
            die();
        }

        if($request->getMethod() === "POST") {
            $id = $request->getParams()["id"];
            $formParams = $request->getMethodParams();
            $formParams = self::secureDataArray($formParams);
            $name = $formParams["name"];
            $parent_id = $formParams["parent_id"];

            if (empty($name)) {
                $msg["alert"] = "You must fill all fields";
            } else {
                $modifyParameters = ["name" => $name];
                if ($parent_id !== "0") {
                    $modifyParameters["parent_id"] = intval($parent_id);
                }

                if ($categoriesModel->modifyById($id, $modifyParameters)) {
                    $msg["success"] = "Category correctly Updated";
                    self::redirect([
                        "request" => $request,
                        "url" => "/admin/categories/edit/",
                    ]);
                } else {
                    $msg["alert"] = "Problem during the update of the category... Damn!";
                }
            }
        }

        $categories = $categoriesModel->getByDesc();
        parent::render('/Categories/edit.html.twig', [
            "msg" => $msg,
            "currentCategory" => $category,
            "categories" => $categories,
        ]);

    }

    static public function createAction(Request $request) {
        $msg = [];
        $categoriesModel = new CategoriesTable();

        if($request->getMethod() === "POST") {
            $formParams = $request->getMethodParams();
            $formParams = self::secureDataArray($formParams);
            $name = $formParams["name"];
            $parent_id = $formParams["parent_id"];

            if (empty($name)) {
                $msg["alert"] = "You must fill all fields";
            } else {
                $insertFields = ["name" => $name];
                if($parent_id !== "0") {
                    $insertFields["parent_id"] = intval($parent_id);
                }

                if($categoriesModel->create($insertFields)) {
                    $msg["success"] = "Category correctly created";
                    self::redirect([
                        "request" => $request,
                        "url" => "/admin/categories/edit",
                    ]);
                } else {
                    // Check si le login ou email existe deja
                    if($categoriesModel->getByParams(["name" => $name])) {
                        $msg["alert"] = "Category not available";
                    } else {
                        $msg["alert"] = "Problem during the creation of the category... Damn!";
                    }
                }
            }
        }


        $categories = $categoriesModel->getByDesc();
        parent::render('/Categories/create.html.twig', [
            "categories" => $categories,
            "msg" => $msg,
        ]);
    }

    // Ceci est un controller pour de l'AJAX uniquement
    static public function deleteAction(Request $request)
    {
        if ($request->getMethod() === "DELETE") {
            $data = $request->getMethodParams();
            $id = $data["id"];
            if (empty($id)) {
                self::sendJsonErrorAndDie("Id is empty!");
            }

            $categoriesModel = new CategoriesTable();
            // On check si l'article existe bien
            $categories = $categoriesModel->getById($id);
            if (!$categories) {
                self::sendJsonErrorAndDie("Categories does not exist!");
            }

            $user = User::getInstance();
            // On check si l'utilisateur a les droits pour supprimer l'article
            if ($user->getRight() >= UserRight::ADMIN ) {
                if($categoriesModel->deleteById($id)) {
                    self::sendJsonDataAndDie(["success" => true]);
                } else {
                    self::sendJsonErrorAndDie("Can't delete, check if articles are not attached to this category!");
                }
            } else {
                self::sendJsonErrorAndDie("You don't have right to delete this article!");
            }
        } else {
            self::sendJsonErrorAndDie("Steven, is it you??");
        }
    }
}
