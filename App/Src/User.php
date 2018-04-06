<?php

namespace App\Src;

use App\Models\UsersTable;

class User {
    static public $instance;

    public $id;
    public $username;
    public $email;
    public $user_group;
    public $status;

    static public function load() {
        self::$instance = self::getInstance();
    }

    static public function getInstance() {
        return self::$instance === null ? new User(Session::read("Auth.User.id")) : self::$instance;
    }

    protected function __construct($id = null) {
        if($id) {
            $this->login($id);
        }
    }

    public function login($id) {
        $userModel = new UsersTable();
        $userData = $userModel->getById($id);
        if(count($userData) > 0) {
            Session::write("Auth.User.username", $userData["username"]);
            Session::write("Auth.User.email", $userData["email"]);
            Session::write("Auth.User.user_group", $userData["user_group"]);
            Session::write("Auth.User.status", $userData["status"]);

            $this->id = $id;
            $this->username = $userData["username"];
            $this->email = $userData["email"];
            $this->user_group = $userData["user_group"];
            $this->status = $userData["status"];
        }
    }

    public function logout() {
        Session::destroy();

        $this->id = null;
        $this->username = null;
        $this->email = null;
        $this->user_group = null;
        $this->status = null;

    }

    public function isLogged() {
        return $this->id ? true : false;
    }

    public function getRight() {
        $group = $this->user_group;
        if($group === null) {
            return UserRight::INVITE;
        } else if ($group === "USER") {
            return UserRight::USER;
        } else if($group === "WRITER") {
            return UserRight::WRITER;
        } else if($group === "ADMIN") {
            return UserRight::ADMIN;
        } else {
            return UserRight::INVITE;
        }
    }
}