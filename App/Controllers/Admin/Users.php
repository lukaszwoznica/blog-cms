<?php


namespace App\Controllers\Admin;


use App\Flash;
use App\Models\User;
use App\Paginator;
use Core\View;

class Users extends Admin
{
    private $page;
    private $user;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->user = User::findByID($route_params['id']);
        }
        if (isset($route_params['page'])) {
            $this->page = (int)$route_params['page'];
        } else {
            $this->page = 1;
        }
    }

    public function indexAction(): void
    {
        $context_data = [];

        if (isset($_GET['search_query'])) {
            $users = User::getAllUsersContainsFilter($_GET['search_query']);
            $context_data['search_query'] = $_GET['search_query'];
        } else {
            $paginator = new Paginator($this->page, 10, User::getTotal());
            $users = User::getAllUsers($paginator->getOffset(), $paginator->getLimit());
            $context_data['page'] = $this->page;
            $context_data['total_pages'] = $paginator->getTotalPages();
        }
        $context_data['users'] = $users;

        View::renderTemplate('Admin/Users/index.html', $context_data);
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
                $img_path = dirname(__DIR__, 3) . '/public' . $this->user->getAvatar();
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
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
            $post_data = $_POST;

            if ($post_data['username'] != $this->user->getUsername() && $this->user->getAvatar() != null) {
                $img_path = dirname(__DIR__, 3) . '/public' . $this->user->getAvatar();
                if (file_exists($img_path)) {
                    $extension = pathinfo($img_path, PATHINFO_EXTENSION);
                    $new_filename = $post_data['username'] . '.' . $extension;
                    $new_path = dirname(__DIR__, 3) . '/public/uploads/users-avatars/' . $new_filename;
                    rename($img_path, $new_path);
                    $post_data['avatar'] = '/uploads/users-avatars/' . $new_filename;
                }
            } else {
                $post_data['avatar'] = $this->user->getAvatar();
            }

            if ($this->user->update($post_data)) {
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