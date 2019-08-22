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
        $user = User::findByUsernameOrEmail($_POST['login']);

        var_dump($user);
    }
}