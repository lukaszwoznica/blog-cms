<?php


namespace App;

use App\Models\User;

/**
 * Authentication class
 */

class Auth
{
    public static function login(User $user, bool $remember_me): void
    {
        session_regenerate_id();
        $_SESSION['user_id'] = $user->getId();

        if ($remember_me) {
            $user->rememberLogin();
        }
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

    /**
     * Check if the user is logged in
     */
    public static function isLoggedIn(): bool
    {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }

    /**
     * Remember the originally-requested page in the session
     */
    public static function rememberRequestedPage(): void
    {
        $_SESSION['return_page'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the originally-requested page to return to after login
     */
    public static function getReturnPage(): string
    {
        return $_SESSION['return_page'] ?? '/';
    }

    /**
     * Get the current logged-in user
     */
    public static function getUser(): ?User
    {
        if (isset($_SESSION['user_id'])) {
            return User::findByID($_SESSION['user_id']);
        }
        return null;
    }
}