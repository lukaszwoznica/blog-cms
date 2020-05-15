<?php


namespace App\Models;


use Core\Model;
use JsonSerializable;
use PDO;

class Notification extends Model implements JsonSerializable
{
    private $unseen_comments;
    private $post_title;
    private $post_slug;
    private $post_id;

    public function __construct(array $user_data = [])
    {
        foreach ($user_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function getAllNotifications(int $user_id): array
    {
        $db = static::getDatabase();
        $sql = 'SELECT COUNT(*) unseen_comments, posts.title post_title, posts.url_slug post_slug, posts.id post_id
                FROM comments
                INNER JOIN posts ON post_id = posts.id
                WHERE posts.user_id = :user_id
                AND notification_seen = 0
                GROUP BY post_id, submit_time
                ORDER BY submit_time DESC';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function getUnseenComments(): ?int
    {
        return $this->unseen_comments;
    }

    public function getPostTitle(): ?string
    {
        return $this->post_title;
    }

    public function getPostSlug(): ?string
    {
        return $this->post_slug;
    }

    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    public function jsonSerialize(): object
    {
        return (object) get_object_vars($this);
    }
}