<?php

namespace App\Config;

use PDO;

class Db {
    private static $_pdo = null;

    public static function getDb()
    {
        if (self::$_pdo === null) {
            try {
                self::$_pdo = new PDO('mysql:dbname=' . Configuration::DB_NAME . ';host=' . Configuration::DB_HOST . ';port=' . Configuration::DB_PORT . ";charset=UTF8", Configuration::DB_USER, Configuration::DB_PASSWORD);
            } catch (PDOException $e) {
                die("PDO ERROR: " . $e->getMessage());
            }
        }
        return self::$_pdo;
    }

    static function query($sql, $params = NULL, $if_one = false){
        $pdo = self::getDb();
        try {
            if ($params) {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            } else {
                $stmt = $pdo->query($sql);
            }

            if($stmt === false) {
                return false;
            }
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            if( strpos($sql, "INSERT") === 0 ||
                strpos($sql, "UPDATE") === 0 ||
                strpos($sql, "MODIFY") === 0 ||
                strpos($sql, "DELETE") === 0
            ) {
                if($stmt->rowCount() > 0) {
                    return true;
                } else {
                    return false;
                }
            }

            if ($if_one)
                return $stmt->fetch();
            else
                return $stmt->fetchAll();
        } catch (Exception $exception) {
            die("PDO Error: " . $exception->getMessage());
        }
    }

    /**
     * Retourne l'identifiant de la dernière ligne insérée ou la valeur d'une séquence
     * @param null $name
     * @return string
     */
    static public function getLastInsertId($name = null)
    {
        return self::getDb()->lastInsertId($name);
    }
}
