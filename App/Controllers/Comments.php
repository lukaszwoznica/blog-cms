<?php


namespace App\Controllers;


use App\Auth;
use App\Flash;
use App\Models\Comment;
use App\Models\Post;

class Comments extends Authenticated
{
    public function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;
            $post_data['user_id'] = Auth::getUser()->getId();
            $comment = new Comment($post_data);
            $post = Post::findByID($post_data['post_id']);

            if ($post->getUserId() == $post_data['user_id']) {
                $comment->setNotificationSeen(true);
            }

            if ($comment->save()) {
                Flash::addMessage('Comment has been successfully added', Flash::SUCCESS);
            } else {
                Flash::addMessage('An error occurred while sending the comment', Flash::ERROR);
            }

            $this->redirectTo(Auth::getReturnPage());
        } else {
            $this->redirectTo('/');
        }
    }
}