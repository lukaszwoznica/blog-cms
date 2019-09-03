<?php


namespace App\Controllers\User;

use App\Auth;
use App\Controllers\Authenticated;
use App\Flash;
use Core\View;

class Profile extends Authenticated
{
    public function showAction(): void
    {
        $this->requireLogin();
        View::renderTemplate('User/Profile/show.html');
    }

    public function editAction(): void
    {
        View::renderTemplate('User/Profile/edit.html');
    }

    public function updateAction(): void
    {
        $user = Auth::getUser();

        if ($user->updateProfile($_POST)) {
            Flash::addMessage('Changes saved successfully.', Flash::SUCCESS);
            $this->redirectTo('/user/profile/show');
        } else {
            View::renderTemplate('User/Profile/edit.html', [
                'errors' => $user->getValidationErrors()
            ]);
        }
    }
}