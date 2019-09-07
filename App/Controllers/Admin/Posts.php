<?php


namespace App\Controllers\Admin;


use App\Auth;
use App\Flash;
use App\Models\Post;
use Core\View;

class Posts extends Admin
{
    private $id;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);
        $this->id = $route_params['id'] ?? null;
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
        View::renderTemplate('Admin/Posts/new.html');
    }

    public function createAction(): void
    {
        $author_id = Auth::getUser()->getId();
        $post_data = $_POST;
        $post_data['user_id'] = $author_id;
        if ($post_data['category_id'] == 0) {
            $post_data['category_id'] = null;
        }

        $post = new Post($post_data);

        if ($post->saveToDatabase()) {
            Flash::addMessage('Post successfully added', Flash::SUCCESS);
            $this->redirectTo('/admin/posts');
        } else {
            View::renderTemplate('Admin/Posts/new.html', [
                'post' => $post
            ]);
        }
    }

    public function editAction(): void
    {
        $post = $this->getPostById($this->id);

        View::renderTemplate('Admin/Posts/edit.html', [
            'post' => $post
        ]);
    }

    public function updateAction(): void
    {
        $post = $this->getPostById($this->id);
        $post_data = $_POST;
        if ($post_data['category_id'] == 0) {
            $post_data['category_id'] = null;
        }

        if ($post->update($post_data)){
            Flash::addMessage('Post successfully updated', Flash::SUCCESS);
            $this->redirectTo('/admin/posts');
        } else {
            View::renderTemplate('Admin/Posts/edit.html', [
                'post' => $post
            ]);
        }
    }

    public function destroyAction(): void
    {
        $post = $this->getPostById($this->id);

        if ($post->delete()) {
            Flash::addMessage('Post has been successfully deleted', Flash::SUCCESS);
        } else {
            Flash::addMessage('An error occurred while deleting the post', Flash::SUCCESS);
        }

        $this->redirectTo('/admin/posts');
    }

    private function getPostById(int $id): ?Post
    {
        return Post::findByID($id);
    }
}