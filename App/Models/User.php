<?php


namespace App\Models;


use Core\Model;
use PDO;

class User extends Model
{
    private $id;
    private $username;
    private $email;
    private $password;

    private $validation_errors = [];


    public function __construct(array $user_data = [])
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

    public static function findByUsernameOrEmail(string $username_or_email): ?User
    {
        $db = Model::getDatabase();
        $sql = "SELECT * FROM users WHERE username = :login OR email = :login";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(":login", $username_or_email);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public static function findByID(int $user_id): ?User
    {
        $db = Model::getDatabase();
        $sql = "SELECT * FROM users WHERE id = :user_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public static function usernameExist(string $username): bool
    {
        if (static::findByUsernameOrEmail($username) !== null)
            return true;
        return false;
    }

    public static function emailExist(string $email): bool
    {
        if (static::findByUsernameOrEmail($email) !== null)
            return true;
        return false;
    }

    /**
     * Authenticate a user by username/email and password.
     * @param string $login
     * @param string $password
     * @return User|null
     */
    public static function authenticate(string $login, string $password): ?User
    {
        $user = static::findByUsernameOrEmail($login);

        if ($user && password_verify($password, $user->password))
            return $user;

        return null;
    }

    /*
     * Getters
     */

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }
}