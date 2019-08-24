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
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getUsername();

            $this->redirectTo('/');
        } else {
            View::renderTemplate('Login/login', [
                'login' => $_POST['login']
            ]);
        }

    }
}