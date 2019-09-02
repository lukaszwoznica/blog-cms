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
        View::renderTemplate('Signup/new');
    }

    public function createAction(): void
    {
        $user = new User($_POST);

        if ($user->saveToDatabase()) {
            $user->sendActivationEmail();
            $this->redirectTo('/signup/success');
        } else {
            View::renderTemplate('Signup/new', [
                'user' => $user
            ]);
        }
    }

    /**
     * Check if username is available (AJAX)
     */
    public function validateUsernameAction(): void
    {
        if (isset($_GET['username']) && !empty($_GET['username'])) {
            $username_valid = !User::usernameExist($_GET['username']);
            header('Content-Type: application/json');
            echo json_encode($username_valid);
        }
    }

    /**
     * Check if email is available (AJAX)
     */
    public function validateEmailAction(): void
    {
        if (isset($_GET['email']) && !empty($_GET['email'])){
            $email_valid = !User::emailExist($_GET['email']);
            header('Content-Type: application/json');
            echo json_encode($email_valid);
        }
    }

    public function successAction(): void
    {
        View::renderTemplate('Signup/success');
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