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
        $posts = Post::getAllPosts(false, 0,7);

        View::renderTemplate('Home/index.html', [
            'posts' => $posts
        ]);
    }
}