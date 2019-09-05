<?php


namespace App\Controllers\Admin;


use App\Auth;
use App\Flash;
use App\Models\Post;
use Core\View;

class Posts extends Admin
{
    public function indexAction(): void
    {
        $posts = Post::getAllPosts();

        View::renderTemplate('Admin/Posts/index.html', [
            'posts' => $posts
        ]);
    }

    public function newAction(): void
    {
        View::renderTemplate('Admin/Posts/new.html');
    }

    public function createAction(): void
    {
        $author_id = Auth::getUser()->getId();
        $post_data = $_POST;
        $post_data['user_id'] = $author_id;
        $post = new Post($post_data);

        if ($post->saveToDatabase()) {
            Flash::addMessage('Post successfully added', Flash::SUCCESS);
            $this->redirectTo('/admin/posts');
        } else {
            View::renderTemplate('Admin/Posts/new.html', [
                'validation_errors' => $post->getValidationErrors()
            ]);
        }

    }

    public function editAction(): void
    {
        $id = $this->route_params["id"];
        $post = Post::findByID($id);

        View::renderTemplate('Admin/Posts/edit.html', [
            'post' => $post
        ]);
    }
}