<?php

namespace App\Models;

use Core\Error;
use Core\Model;
use PDO;
use PDOException;

/**
 * Post Model
 */

class Post extends Model
{
    public static function getAllPosts(): array
    {
        $db = static::getDatabase();
        $stmt = $db->query("SELECT * FROM posts");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}