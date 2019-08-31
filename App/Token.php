<?php


namespace App;

use Core\Error;

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
     */
    public function __construct(string $token_val = null)
    {
        if ($token_val) {
            $this->token_value = $token_val;
        } else {
            try{
                $this->token_value = bin2hex(random_bytes(16));
            } catch (\Exception $e) {
                Error::exceptionHandler($e, true);
            }

        }
    }

    public function getValue(): string
    {
        return $this->token_value;
    }

    public function getHash(): string
    {
        return hash_hmac('sha256', $this->token_value, Config::SECRET_KEY);
    }

}