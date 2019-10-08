<?php


namespace App\Controllers;


use App\Models\Post;
use Core\Controller;
use Core\View;
use Exception;

class Posts extends Controller
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

        View::renderTemplate('Posts/index.html', [
            'posts' => $posts
        ]);
    }

    public function showAction(): void
    {
        if ($this->post) {
            View::renderTemplate('Posts/show.html', [
                'post' => $this->post
            ]);
        } else {
            throw new Exception("Post with given id does not exist", 404);
        }
    }
}