<?php

namespace App\Config;

class Db {
    private static $_pdo = null;

    public static function getDb()
    {
        if (is_null(self::$_pdo)) {
            try {
                self::$_pdo = new PDO('mysql:dbname=' . Configuration::DBNAME . ';host=' . Configuration::HOST, Configuration::USER, Configuration::MDP);
            } catch (PDOException $e) {
                die("PDO ERROR: " . $e->getMessage());
            }
        }
        return self::$_pdo;
    }

    public static function query($query, $params = null, $fetchall = true)
    {
        $pdo = self::getDb();
        if ($params == null) {
            $prepared = $pdo->query($query);
        } else {
            $prepared = $pdo->prepare($query);
            $prepared->execute($params);
        }
        if ($fetchall == true) {
            $result = $prepared->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $result = $prepared->fetch(PDO::FETCH_ASSOC);
        }
        return $result;
    }
}
