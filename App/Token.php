<?php


namespace App;

/**
 * Class for unique random tokens
 */
class Token
{
    private $token_value;

    /**
     * Token constructor. Create a new random token or assign an existing one.
     *
     * @param string|null $token_val
     * @throws \Exception
     */
    public function __construct(string $token_val = null)
    {
        if ($token_val) {
            $this->token_value = $token_val;
        } else {
            $this->token_value = bin2hex(random_bytes(16));
        }
    }

    public function getValue(): string
    {
        return $this->token_value;
    }

    public function getHash(): string
    {
        return hash_hmac('sha-256', $this->token_value, Config::SECRET_KEY);
    }

}