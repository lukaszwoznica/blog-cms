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

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }
}