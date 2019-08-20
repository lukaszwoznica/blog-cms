<?php


namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;

class SignUp extends Controller
{
    public function indexAction(): void
    {
        View::renderTemplate("Signup/new");
    }

    public function createAction(): void
    {
        $user = new User($_POST);
        $user->create();
    }


}