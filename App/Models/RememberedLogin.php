<?php


namespace App\Models;


use App\Token;
use Core\Model;
use PDO;

class RememberedLogin extends Model
{
    private $token_hash;
    private $user_id;
    private $expiration_time;

    /**
     * Find a remembered login model by the token
     *
     * @param string $token_value
     * @return RememberedLogin|null
     */
    public static function findByToken(string $token_value): ?RememberedLogin
    {
        $token = new Token($token_value);
        $token_hash = $token->getHash();
        $db = static::getDatabase();
        $sql = 'SELECT * FROM remembered_logins WHERE token_hash = :token_hash';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    /**
     * Get the user model associated with this remembered login.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return User::findByID($this->user_id);
    }

    /**
     * Check if the remember token has expired or not.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        if (strtotime($this->expiration_time) < time()) {
            $this->delete();
            return true;
        }
        return false;
    }

    /**
     * Delete this model
     *
     * @return void
     */
    public function delete(): void
    {
        $db = static::getDatabase();
        $sql = 'DELETE FROM remembered_logins WHERE token_hash = :token_hash';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();
    }
}