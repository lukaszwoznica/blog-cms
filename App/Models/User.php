<?php


namespace App\Models;


use Core\Model;
use PDO;

class User extends Model
{
    private $username;
    private $email;
    private $password;

    public function __construct(array $user_data)
    {
        foreach ($user_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function create()
    {
        $db = static::getDatabase();
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        $sql_query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

        $stmt = $db->prepare($sql_query);
        $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
        $stmt->bindValue(":email", $this->email, PDO::PARAM_STR);
        $stmt->bindValue(":password", $password_hash, PDO::PARAM_STR);

        $stmt->execute();
    }
}