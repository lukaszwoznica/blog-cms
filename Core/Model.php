<?php

namespace Core;

use PDO;
use App\Config;
use PDOException;

abstract class Model
{
    /**
     * Get the database connection
     */
    protected static function getDatabase(): PDO
    {
        static $db = null;

        if ($db === null){
            try {
                $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
                $db = new PDO($dsn, Config::DB_USERNAME, Config::DB_PASSWORD);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e){
                echo $e->getMessage();
            }
        }

        return $db;
    }
}