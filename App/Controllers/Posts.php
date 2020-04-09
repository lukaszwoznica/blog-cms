<?php


namespace App\Controllers;


use App\Models\Comment;
use App\Models\Post;
use App\Paginator;
use Core\Controller;
use Core\View;
use Exception;

class Posts extends Controller
{
    private $post;
    private $page;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->post = Post::findByID($route_params['id']);
        } elseif (isset($route_params['slug'])) {
            $this->post = Post::findBySlug($route_params['slug']);
        }

        if (isset($route_params['page'])) {
            $this->page = (int)$route_params['page'];
        } else {
            $this->page = 1;
        }

    }

    public function indexAction(): void
    {
        $paginator = new Paginator($this->page, 10, Post::getTotal(false));

        $posts = Post::getAllPosts(false, $paginator->getOffset(), $paginator->getLimit());

        View::renderTemplate('Posts/index.html', [
            'posts' => $posts,
            'page' => $this->page,
            'total_pages' => $paginator->getTotalPages(),
            'path' => '/posts/page/'
        ]);
    }

    public function showAction(): void
    {
        if ($this->post) {
            $comments = Comment::getAllComments(0, PHP_INT_MAX, $this->post->getId());

            View::renderTemplate('Posts/show.html', [
                'post' => $this->post,
                'comments' => $comments
            ]);
        } else {
            throw new Exception("Post with given id or slug does not exist", 404);
        }
    }
}