<?php

namespace App\Models;

use Core\Model;
use PDO;

/**
 * Post Model
 */

class Post extends Model
{
    private $id;
    private $title;
    private $content;
    private $category_id;
    private $user_id;
    private $create_time;

    public function __construct(array $post_data = [])
    {
        foreach ($post_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function saveToDatabase(): bool
    {
        $db = static::getDatabase();
        $sql = 'INSERT INTO posts (title, content, category_id, user_id)
                VALUES (:title, :content, :category_id, :user_id)';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function getAllPosts(): array
    {
        $db = static::getDatabase();
        $sql = 'SELECT * FROM posts';

        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $result = $stmt->fetchAll();

        return $result;
    }

    /*
     * Getters
     */

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getCreateTime(): string
    {
        return $this->create_time;
    }
}