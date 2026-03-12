<?php

namespace App\Models;

use Core\Database;
use PDO;

class User
{
    private static $table = 'users';

    public static function all()
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM " . self::$table);
        return $stmt->fetchAll();
    }

    public static function find($id)
    {
        $db = Database::getConnection();
        // Diferente do mysqli, no PDO usamos prepared statements de forma simples
        $stmt = $db->prepare("SELECT * FROM " . self::$table . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create($data)
    {
        $db = Database::getConnection();
        $sql = "INSERT INTO " . self::$table . " (name) VALUES (:name)";
        $stmt = $db->prepare($sql);
        $stmt->execute(['name' => $data['name']]);
        
        $id = $db->lastInsertId();
        return self::find($id);
    }

    public static function update($id, $data)
    {
        $db = Database::getConnection();
        $sql = "UPDATE " . self::$table . " SET name = :name WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'id' => $id
        ]);

        return self::find($id);
    }

    public static function delete($id)
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM " . self::$table . " WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
