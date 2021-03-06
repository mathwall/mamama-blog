<?php

namespace App\Models;

use App\Config\Db;

abstract class Table
{
    protected $table = "";

    protected function query(...$params)
    {
        return DB::query(...$params);
    }

    public function create($fields)
    {
        $keys = [];
        $values = [];
        $params = [];
        foreach ($fields as $key => $value) {
            $keys[] = $key;
            $values[] = "?";
            $params[] = $value;
        }

        $keys = implode(",", $keys);
        $values = implode(",", $values);
        return $this->query("INSERT INTO " . $this->table . "($keys) VALUES($values) ", $params);
    }

    public function getById($id)
    {
        return $this->query("SELECT * FROM " . $this->table . " WHERE id = {$id}", null, true);
    }

    public function getByParams($fields, $ifOne = false)
    {
        $modif_params = [];
        $params = [];
        foreach ($fields as $key => $value) {
            if ($value) {
                $modif_params[] = "$key = ?";
                $params[] = $value;
            }
        }
        $modif_params = implode(",", $modif_params);

        return $this->query("SELECT * FROM " . $this->table . " WHERE $modif_params", $params, $ifOne);
    }

    public function getAll($orderBy = null, $direction = "ASC")
    {
        $strOrderBy = $this->strOrderBy($orderBy, $direction);
        return $this->query("SELECT * FROM " . $this->table . " $strOrderBy");
    }

    protected function strOrderBy($orderBy = null, $direction = "ASC", $table_prefix = null)
    {
        if ($table_prefix) {
            return $orderBy ? " ORDER BY " . $table_prefix . ".$orderBy $direction " : "";
        } else {
            return $orderBy ? " ORDER BY $orderBy $direction " : "";
        }
    }

    public function modifyById($id, $fields)
    {
        $modif_params = [];
        $params = [];
        foreach ($fields as $key => $value) {
            if ($value !== null) {
                $modif_params[] = "$key = ?";
                $params[] = $value;
            }
        }
        $modif_params = implode(",", $modif_params);
        $params[] = $id;
        return $this->query("UPDATE " . $this->table . " SET $modif_params WHERE id = ?", $params);
    }

    public function deleteById($id)
    {
        return $this->query("DELETE FROM " . $this->table . " WHERE id = ?", [$id]);
    }
}
