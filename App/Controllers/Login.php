<?php


namespace App\Controllers;

use App\Auth;
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
            Auth::login($user);
            $this->redirectTo(Auth::getReturnPage());
        } else {
            View::renderTemplate('Login/login', [
                'login' => $_POST['login']
            ]);
        }
    }

    public function destroyAction(): void
    {
        Auth::logout();
        $this->redirectTo('/');
    }
}