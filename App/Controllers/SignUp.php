<?php


namespace App\Controllers;

use App\Auth;
use App\Flash;
use App\Models\User;
use Core\Controller;
use Core\View;

class SignUp extends Controller
{
    protected function before()
    {
        if (Auth::getUser()) {
            $this->redirectTo('/');
        }
    }

    public function indexAction(): void
    {
        View::renderTemplate('Signup/new.html');
    }

    public function createAction(): void
    {
        $user = new User($_POST);

        if ($user->saveToDatabase()) {
            $user->sendActivationEmail();
            $this->redirectTo('/signup/success');
        } else {
            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);
        }
    }

    public function successAction(): void
    {
        View::renderTemplate('Signup/success.html');
    }

    public function activateAction(): void
    {
        if (User::activate($this->route_params['token'])){
            Flash::addMessage("Success! Your account is active. You can log in now.",
                Flash::SUCCESS);
            $this->redirectTo('/');
        }
    }

}