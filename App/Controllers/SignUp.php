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

    /**
     * Check if username is available (AJAX)
     */
    public function validateUsernameAction(): void
    {
        $username_valid = !User::usernameExist($_GET['username']);
        header('Content-Type: application/json');
        echo json_encode($username_valid);
    }

    /**
     * Check if email is available (AJAX)
     */
    public function validateEmailAction(): void
    {
        $email_valid = !User::emailExist($_GET['email']);
        header('Content-Type: application/json');
        echo json_encode($email_valid);
    }

    public function successAction(): void
    {
        View::renderTemplate('Signup/success');
    }


}