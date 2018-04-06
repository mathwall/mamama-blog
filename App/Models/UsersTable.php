<?php

namespace App\Models;

class UsersTable extends Table
{
    protected $table = "users";
    public function createUser($username, $email, $password, $group = "USER")
    {
        return parent::create([
            "username" => $username,
            "email" => $email,
            "password" => $password,
            "user_group" => $group,
            "status" => true,
            "creation_date" => date('Y-m-d H:i:s'),
        ]);
    }
    public function getNameById($id)
    {
        $all = parent::getById($id);
        return $all['username'];
    }
}
