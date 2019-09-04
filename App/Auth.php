<?php


namespace App;

use App\Models\RememberedLogin;
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
            if ($user->rememberLogin()){
                setcookie(
                    'remember_me',
                    $user->getRememberToken(),
                    $user->getRememberTokenExpireTime(),
                    '/'
                );
            }
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

        // Forget remembered login
        $cookie = $_COOKIE['remember_me'] ?? null;

        if ($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);
            if ($remembered_login) {
                $remembered_login->delete();
            }
            setcookie('remember_me', '', time() - 3600);
        }
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
     * Get the current logged-in user from a session or the remember_me cookie
     *
     * @return User|null
     */
    public static function getUser(): ?User
    {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return User::findByID($_SESSION['user_id']);
        }

        return static::loginFromRememberCookie();
    }


    /**
     * Login the user from a remembered login cookie
     *
     * @return User|null
     */
    private static function loginFromRememberCookie(): ?User
    {
        $cookie = $_COOKIE['remember_me'] ?? null;

        if ($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);

            if ($remembered_login && !$remembered_login->hasExpired()) {
                $user = $remembered_login->getUser();
                static::login($user, false);
                return $user;
            }
        }

        return null;
    }


}