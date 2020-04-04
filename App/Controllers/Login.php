<?php


namespace App\Controllers;

use App\Auth;
use App\Flash;
use App\Models\User;
use Core\Controller;
use Core\View;

class Login extends Controller
{
    public function indexAction(): void
    {
        if (Auth::getUser()) {
            $this->redirectTo('/');
        }

        View::renderTemplate('Login/index.html');
    }

    public function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = User::authenticate($_POST['login'], $_POST['password']);
            $remember_me = isset($_POST['remember_me']);

            if ($user) {
                if ($user->getIsActive()) {
                    Auth::login($user, $remember_me);
                    $this->redirectTo(Auth::getReturnPage());
                } else {
                    Flash::addMessage('Before you can login, you must activate your account.',
                        Flash::ERROR
                    );
                    View::renderTemplate('Login/index.html', [
                        'login' => $_POST['login'],
                        'remember_me' => $remember_me
                    ]);
                }
            } else {
                Flash::addMessage('Incorrect login or password, please try again.', Flash::ERROR);
                View::renderTemplate('Login/index.html', [
                    'login' => $_POST['login'],
                    'remember_me' => $remember_me
                ]);
            }
        } else {
            $this->redirectTo('/');
        }
    }

    public function destroyAction(): void
    {
        Auth::logout();
        $this->redirectTo('/');
    }
}