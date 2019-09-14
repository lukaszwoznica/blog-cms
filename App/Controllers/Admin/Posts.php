<?php


namespace App\Controllers\Admin;


use App\Auth;
use App\Flash;
use App\Models\Category;
use App\Models\Post;
use Core\View;

class Posts extends Admin
{
    private $post;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->post = Post::findByID($route_params['id']);
        }
    }

    public function indexAction(): void
    {
        $posts = Post::getAllPosts();

        View::renderTemplate('Admin/Posts/index.html', [
            'posts' => $posts
        ]);
    }

    public function newAction(): void
    {
        $categories = Category::getAllCategories();

        View::renderTemplate('Admin/Posts/new.html', [
            'categories' => $categories
        ]);
    }

    public function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $author_id = Auth::getUser()->getId();
            $post_data = $_POST;
            $post_data['user_id'] = $author_id;
            if ($post_data['category_id'] == 0) {
                $post_data['category_id'] = null;
            }

            $post = new Post($post_data);

            if ($post->saveToDatabase()) {
                Flash::addMessage('Post has been successfully added', Flash::SUCCESS);
                $this->redirectTo('/admin/posts');
            } else {
                View::renderTemplate('Admin/Posts/new.html', [
                    'post' => $post
                ]);
            }
        } else {
            $this->redirectTo('/admin/posts');
        }
    }

    public function editAction(): void
    {
        $categories = Category::getAllCategories();

        if ($this->post) {
            View::renderTemplate('Admin/Posts/edit.html', [
                'post' => $this->post,
                'categories' => $categories
            ]);
        } else {
            Flash::addMessage('Post with given id does not exist', Flash::WARNING);
            $this->redirectTo('/admin/posts');
        }
    }

    public function updateAction(): void
    {
        if ($this->post && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;
            if ($post_data['category_id'] == 0) {
                $post_data['category_id'] = null;
            }

            if ($this->post->update($post_data)){
                Flash::addMessage('Post has been successfully updated', Flash::SUCCESS);
                $this->redirectTo('/admin/posts');
            } else {
                View::renderTemplate('Admin/Posts/edit.html', [
                    'post' => $this->post
                ]);
            }
        } else {
            $this->redirectTo('/admin/posts');
        }
    }

    public function destroyAction(): void
    {
        if ($this->post) {
            if ($this->post->delete()) {
                Flash::addMessage('Post has been successfully deleted', Flash::SUCCESS);
            } else {
                Flash::addMessage('An error occurred while deleting the post', Flash::ERROR);
            }
        } else {
            Flash::addMessage('Category with given id does not exist', Flash::WARNING);
        }

        $this->redirectTo('/admin/posts');
    }

}