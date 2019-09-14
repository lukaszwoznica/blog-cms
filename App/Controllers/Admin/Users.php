<?php


namespace App\Controllers\Admin;


use App\Flash;
use App\Models\User;
use Core\View;

class Users extends Admin
{
    private $user;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->user = User::findByID($route_params['id']);
        }
    }

    public function indexAction(): void
    {
        $users = User::getAllUsers();

        View::renderTemplate('Admin/Users/index.html', [
            'users' => $users
        ]);
    }

    public function newAction(): void
    {
        View::renderTemplate('Admin/Users/new.html');
    }

    public function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;
            $post_data['is_active'] = 1;
            $user = new User($post_data);

            if ($user->saveToDatabase()) {
                Flash::addMessage('User has been successfully added', Flash::SUCCESS);
                $this->redirectTo('/admin/users');
            } else {
                View::renderTemplate('Admin/Users/new.html', [
                    'user' => $user
                ]);
            }
        } else {
            $this->redirectTo('/admin/users');
        }
    }

    public function destroyAction(): void
    {
        if ($this->user) {
            if ($this->user->delete()) {
                Flash::addMessage('User has been successfully deleted', Flash::SUCCESS);
            } else {
                Flash::addMessage('An error occurred while deleting the user', Flash::ERROR);
            }
        } else {
            Flash::addMessage('User with given id does not exist', Flash::WARNING);
        }

        $this->redirectTo('/admin/users');
    }

    public function editAction(): void
    {
        if ($this->user) {
            View::renderTemplate('Admin/Users/edit.html', [
                'user' => $this->user
            ]);
        } else {
            Flash::addMessage('User with given id does not exist', Flash::WARNING);
            $this->redirectTo('/admin/users');
        }
    }

    public function updateAction(): void
    {
        if ($this->user && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->user->update($_POST)) {
                Flash::addMessage('User has been successfully updated', Flash::SUCCESS);
                $this->redirectTo('/admin/users');
            } else {
                View::renderTemplate('Admin/Users/edit.html', [
                    'user' => $this->user
                ]);
            }
        } else {
            $this->redirectTo('/admin/users');
        }
    }
}