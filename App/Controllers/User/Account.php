<?php


namespace App\Controllers\User;


use App\Models\User;
use Core\Controller;

class Account extends Controller
{
    /**
     * Check if username is available (AJAX)
     */
    public function validateUsernameAction(): void
    {
        if (isset($_GET['username']) && !empty($_GET['username'])) {
            $username_valid = !User::usernameExist($_GET['username'], $_GET['ignore_id'] ?? null);
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
            $email_valid = !User::emailExist($_GET['email'], $_GET['ignore_id'] ?? null);
            header('Content-Type: application/json');
            echo json_encode($email_valid);
        }
    }

}