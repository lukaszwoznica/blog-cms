<?php


namespace App\Models;


use Core\Model;
use PDO;

class User extends Model
{
    private $username;
    private $email;
    private $password;
    private $validation_errors = [];

    public function __construct(array $user_data)
    {
        foreach ($user_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function saveToDatabase(): bool
    {
        $this->validate();

        if (empty($this->validation_errors)) {
            $db = static::getDatabase();
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql_query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

            $stmt = $db->prepare($sql_query);
            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $stmt->bindValue(":email", $this->email, PDO::PARAM_STR);
            $stmt->bindValue(":password", $password_hash, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
    
    public function validate(): void
    {
        // Username
        if (strlen($this->username) < 3) {
            $this->validation_errors[] = "Username must be at least 3 characters";
        }
        if (static::usernameExist($this->username)) {
            $this->validation_errors[] = "Username is already taken";
        }

        // Email
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->validation_errors[] = "Invalid email";
        }
        if (static::emailExist($this->email)) {
            $this->validation_errors[] = "Email is already taken";
        }

        // Password
        if (strlen($this->password) < 6) {
            $this->validation_errors[] = "Password must be at least 8 characters";
        }

        if (preg_match("/.*[a-z]+.*/i", $this->password) === 0) {
            $this->validation_errors[] = "Password must contain at least one letter";
        }

        if (preg_match("/.*\d+.*/i", $this->password) === 0) {
            $this->validation_errors[] = "Password must contain at least one number";
        }
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }

    public static function usernameExist($username): bool
    {
        $db = Model::getDatabase();
        $sql = "SELECT * FROM users WHERE username = :username";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    public static function emailExist($email): bool
    {
        $db = Model::getDatabase();
        $sql = "SELECT * FROM users WHERE email = :email";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }
}