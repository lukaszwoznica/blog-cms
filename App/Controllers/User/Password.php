<?php


namespace App\Controllers\User;


use App\Models\User;
use Core\Controller;
use Core\View;

class Password extends Controller
{
    public function forgotAction(): void
    {
        View::renderTemplate('User/Password/forgot.html');
    }

    public function requestResetAction(): void
    {
        if(isset($_POST['login'])) {
            User::passwordReset($_POST['login']);

            View::renderTemplate('User/Password/reset-requested.html');
        }
    }

    public function resetAction(): void
    {
        $token = $this->route_params['token'];
        $this->getUserOrExit($token);

        View::renderTemplate('User/Password/reset.html', [
            'token' => $token
        ]);
    }

    public function resetPasswordAction(): void
    {
        $user = $this->getUserOrExit($_POST['token']);
        if ($user->resetPassword($_POST['password'])) {
            View::renderTemplate('User/Password/reset-success.html');
        } else {
            View::renderTemplate('User/Password/reset.html', [
                'token' => $_POST['token'],
                'user' => $user
            ]);
        }

    }

    private function getUserOrExit(string $token): ?User
    {
        $user = User::findByPasswordResetToken($token);
        if ($user) {
            return $user;
        }

        View::renderTemplate('User/Password/token-invalid.html');
        exit();
    }
}