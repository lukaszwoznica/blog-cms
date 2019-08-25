<?php


namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;

class Login extends Controller
{
    public function indexAction(): void
    {
        View::renderTemplate('Login/login');
    }

    public function createAction(): void
    {
        $user = User::authenticate($_POST['login'], $_POST['password']);

        if ($user) {
            session_regenerate_id();

            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getUsername();

            $this->redirectTo('/');
        } else {
            View::renderTemplate('Login/login', [
                'login' => $_POST['login']
            ]);
        }
    }

    public function destroyAction(): void
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

        $this->redirectTo('/');
    }
}