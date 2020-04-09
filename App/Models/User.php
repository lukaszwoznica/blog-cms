<?php


namespace App\Models;


use App\Mail;
use App\Token;
use Core\Model;
use Core\View;
use PDO;

class User extends Model
{
    private $id;
    private $username;
    private $email;
    private $password;
    private $validation_errors = [];
    private $remember_token;
    private $remember_token_expire_time;
    private $password_reset_token;
    private $password_reset_expiry;
    private $activation_token;
    private $is_active = 0;
    private $role_id = 2;
    private $avatar;
    private $join_date;

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
            $token = new Token();
            $this->activation_token = $token->getValue();

            $sql_query = "INSERT INTO users (username, email, password, is_active, role_id, activation_hash) 
                          VALUES (:username, :email, :password, :is_active, :role_id, :activation_hash)";

            $stmt = $db->prepare($sql_query);
            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':is_active', $this->is_active, PDO::PARAM_BOOL);
            $stmt->bindValue(':role_id', $this->role_id, PDO::PARAM_INT);
            if ($this->is_active) {
                $stmt->bindValue(':activation_hash', null, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':activation_hash', $token->getHash(), PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }

    public function validate(): void
    {
        // Username
        if (strlen($this->username) < 3 || strlen($this->username) > 25) {
            $this->validation_errors[] = "Username must be between 3 and 25 characters";
        }
        if (preg_match("/^[a-z0-9_]+$/i", $this->username) === 0) {
            $this->validation_errors[] = "Username can only contain alphanumeric characters (letters A-Z, numbers 0-9) and underscore";
        }
        if (preg_match("/^([a-z]+).*([a-z0-9]+)$/i", $this->username) === 0) {
            $this->validation_errors[] = "Username must start with a letter and end with a letter or number";
        }
        if (static::usernameExist($this->username, $this->id ?? null)) {
            $this->validation_errors[] = "Username is already taken";
        }

        // Email
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->validation_errors[] = "Invalid email";
        }
        if (static::emailExist($this->email, $this->id ?? null)) {
            $this->validation_errors[] = "Email is already taken";
        }

        // Password
        if (isset($this->password)) {
            if (strlen($this->password) < 6) {
                $this->validation_errors[] = "Password must be at least 6 characters";
            }

            if (preg_match("/.*[a-z]+.*/i", $this->password) === 0) {
                $this->validation_errors[] = "Password must contain at least one letter";
            }

            if (preg_match("/.*\d+.*/i", $this->password) === 0) {
                $this->validation_errors[] = "Password must contain at least one number";
            }
        }

        // Is active
        if (!($this->is_active == 0 || $this->is_active == 1)) {
            $this->validation_errors[] = "Is_Active field must be boolean value";
        }

        // Role
        if (!($this->role_id == 1 || $this->role_id == 2)) {
            $this->validation_errors[] = "Invalid user role identifier";
        }
    }

    public static function findByUsernameOrEmail(string $username_or_email): ?User
    {
        $db = static::getDatabase();
        $sql = "SELECT * FROM users WHERE username = :login OR email = :login";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':login', $username_or_email);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public static function findByID(int $user_id): ?User
    {
        $db = static::getDatabase();
        $sql = "SELECT * FROM users WHERE id = :user_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public static function usernameExist(string $username, int $ignore_id = null): bool
    {
        $user = static::findByUsernameOrEmail($username);
        if ($user && $user->id != $ignore_id)
            return true;
        return false;
    }

    public static function emailExist(string $email, int $ignore_id = null): bool
    {
        $user = static::findByUsernameOrEmail($email);
        if ($user && $user->id != $ignore_id)
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

    /**
     * Remember login by inserting a unique token into remembered_logins table in database.
     *
     * @return bool
     */
    public function rememberLogin(): bool
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();
        $this->remember_token_expire_time = time() + 60 * 60 * 24 * 30; // 30 days
        $db = static::getDatabase();
        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expiration_time)
                VALUES (:token_hash, :user_id, :expiration_time)';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expiration_time', date('Y-m-d H:i:s', $this->remember_token_expire_time), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Generate password reset token and send an email with instructions to the user.
     *
     * @param string $login
     */
    public static function passwordReset(string $login): void
    {
        $user = static::findByUsernameOrEmail($login);

        if ($user) {
            if ($user->generatePasswordResetToken()) {
                $user->sendPasswordResetEmail();
            }
        }
    }

    public static function findByPasswordResetToken(string $token): ?User
    {
        $token = new Token($token);
        $token_hash = $token->getHash();
        $db = static::getDatabase();
        $sql = 'SELECT * FROM users WHERE password_reset_hash = :token_hash';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $user = $stmt->fetch();
        if ($user && strtotime($user->password_reset_expiry) > time()) {
            return $user;
        }

        return null;
    }

    public function resetPassword(string $password): bool
    {
        $this->password = $password;
        $this->validate();

        if (empty($this->validation_errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $db = static::getDatabase();
            $sql = 'UPDATE users
                    SET password = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_expiry = NULL
                    WHERE id = :id';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Send account activation link in an email to the user.
     */
    public function sendActivationEmail(): void
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $html_body = View::getTemplate('Signup/activation-email.html', [
            'url' => $url,
            'username' => $this->username
        ]);
        $plaintext_body = View::getTemplate('Signup/activation-email.txt', [
            'url' => $url,
            'username' => $this->username
        ]);

        Mail::send($this->email, 'Account activation', $html_body, $plaintext_body);
    }

    /**
     * Activate the user account with the activation token.
     *
     * @param $token_value
     * @return bool
     */
    public static function activate(string $token_value): bool
    {
        $token = new Token($token_value);
        $db = self::getDatabase();
        $sql = 'UPDATE users
                SET is_active = 1,
                activation_hash = null
                WHERE activation_hash = :token_hash';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token->getHash(), PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            return true;
        }
        return false;
    }

    public function updateProfile(array $data): bool
    {
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->avatar = $data['avatar'];
        if (!empty($data['password']))
            $this->password = $data['password'];

        $this->validate();

        if (empty($this->validation_errors)) {
            $db = static::getDatabase();

            $sql = 'UPDATE users
                    SET username = :username,
                        email = :email,
                        avatar = :avatar';
            if (!empty($data['password']))
                $sql .= ', password = :password';
            $sql .= "\nWHERE id = :id";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':avatar', $this->avatar, PDO::PARAM_STR);
            if (!empty($data['password'])) {
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }

        return false;
    }

    public static function getAllUsers(int $limit_offset = 0, int $limit_row_num = PHP_INT_MAX): array
    {
        $db = static::getDatabase();
        $sql = "SELECT * FROM users LIMIT $limit_offset, $limit_row_num";

        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $result = $stmt->fetchAll();

        return $result;
    }

    public function delete(): bool
    {
        $db = static::getDatabase();
        $sql = 'DELETE FROM users WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id);

        return $stmt->execute();
    }

    public function update(array $user_data): bool
    {
        $this->username = $user_data['username'];
        $this->email = $user_data['email'];
        $this->role_id = $user_data['role_id'];
        $this->is_active = $user_data['is_active'];
        $this->avatar = $user_data['avatar'];

        $this->validate();

        if (empty($this->validation_errors)) {
            $db = static::getDatabase();

            $sql = 'UPDATE users 
                    SET username = :username,
                        email = :email,
                        role_id = :role_id,
                        is_active = :is_active,
                        avatar = :avatar
                    WHERE id = :id';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':role_id', $this->role_id, PDO::PARAM_INT);
            $stmt->bindValue(':is_active', $this->is_active, PDO::PARAM_BOOL);
            $stmt->bindValue(':avatar', $this->avatar, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    public static function getTotal(): int
    {
        $db = static::getDatabase();
        $sql = "SELECT COUNT(*) FROM users";
        $stmt = $db->query($sql);

        return $stmt->fetchColumn();
    }

    public static function getAllUsersContainsFilter(string $filter): array
    {
        $db = static::getDatabase();
        $sql = 'SELECT * FROM users 
                WHERE username LIKE :filter
	            OR email LIKE :filter';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':filter', "%$filter%", PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }

    public function setValidationErrors(array $validation_errors): void
    {
        $this->validation_errors = $validation_errors;
    }

    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function getRememberTokenExpireTime(): ?string
    {
        return $this->remember_token_expire_time;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function getRoleId(): ?int
    {
        return $this->role_id;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getJoinDate(): ?string
    {
        return $this->join_date;
    }

    /**
     * Generate a new token for password reset process.
     *
     * @return bool
     */
    private function generatePasswordResetToken(): bool
    {
        $token = new Token();
        $token_hash = $token->getHash();
        $this->password_reset_token = $token->getValue();
        $expiry_timestamp = time() + 60 * 60 * 4; // 4 hours
        $db = static::getDatabase();
        $sql = 'UPDATE users 
                SET password_reset_hash = :token_hash, 
                    password_reset_expiry = :expiry_time
                WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(':expiry_time', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Send password reset link in an email to the user.
     */
    private function sendPasswordResetEmail(): void
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/user/password/reset/' . $this->password_reset_token;

        $html_body = View::getTemplate('User/Password/reset-email.html', [
            'url' => $url,
            'username' => $this->username
        ]);
        $plaintext_body = View::getTemplate('User/Password/reset-email.txt', [
            'url' => $url,
            'username' => $this->username
        ]);

        Mail::send($this->email, 'Password reset', $html_body, $plaintext_body);
    }
}