<?php


namespace App\Controllers\Admin;


use App\Models\Comment;
use App\Models\Notification;
use Core\View;

class Notifications extends Admin
{
    public function getNotificationsAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['user_id'])) {
            $notifications = Notification::getAllNotifications($_POST['user_id']);

            $result['notifications_array'] = $notifications;
            $result['html_code'] = View::getTemplate('Admin/Inc/notifications.html', [
                'notifications' => $notifications
            ]);

            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    public
    function destroyNotificationAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['post_id'])) {
            Comment::markNotificationAsSeen($_POST['post_id']);
        }
    }
}