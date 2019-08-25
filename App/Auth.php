<?php


namespace App;

use App\Models\User;

/**
 * Authentication class
 */

class Auth
{
    public static function login(User $user): void
    {
        session_regenerate_id();

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_name'] = $user->getUsername();
    }

    public static function logout(): void
    {
        // Unset all of the session variables
        $_SESSION = [];

        // Delete the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Finally destroy the session
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }
}