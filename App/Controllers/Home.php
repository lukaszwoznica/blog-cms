<?php

namespace App\Controllers;

use App\Models\Post;
use Core\Controller;
use Core\View;

/**
 * Home Controller
 */
class Home extends Controller
{
    public function indexAction(): void
    {
        $posts = Post::getAllPosts();

        View::renderTemplate('Home/index', [
            'posts' => $posts
        ]);
    }
}