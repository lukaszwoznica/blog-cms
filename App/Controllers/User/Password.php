<?php


namespace App\Controllers\User;


use App\Models\User;
use Core\Controller;
use Core\View;

class Password extends Controller
{
    public function forgotAction(): void
    {
        View::renderTemplate('User/Password/forgot');
    }

    public function requestResetAction(): void
    {
        if(isset($_POST['login'])) {
            User::passwordReset($_POST['login']);

            View::renderTemplate('User/Password/reset-requested');
        }
    }

    public function resetAction(): void
    {
        $token = $this->route_params['token'];
        $user = User::findByPasswordResetToken($token);

        if ($user) {
            View::renderTemplate('User/Password/reset');
        } else
            echo "Token is invalid";
    }
}