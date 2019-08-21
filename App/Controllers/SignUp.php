<?php


namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;

class SignUp extends Controller
{
    public function indexAction(): void
    {
        View::renderTemplate('Signup/new');
    }

    public function createAction(): void
    {
        $user = new User($_POST);

        if ($user->saveToDatabase()) {
            $this->redirectTo('/signup/success');
        } else {
            View::renderTemplate('Signup/new', [
                'user' => $user
            ]);
        }
    }

    public function successAction(): void
    {
        View::renderTemplate('Signup/success');
    }


}