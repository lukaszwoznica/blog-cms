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
    private $last_modified;
    private $is_published;
    private $validation_errors = [];

    // Additional properties for SQL queries with JOIN

    /**
     * Username of post author
     */
    private $author_name;


    public function __construct(array $post_data = [])
    {
        foreach ($post_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'username':
                if(empty($this->author_name)){
                    $this->author_name = $value;
                }
                break;
        }
    }

    public function saveToDatabase(): bool
    {
        $this->validate();

        if (empty($this->validation_errors)) {
            $db = static::getDatabase();
            $sql = 'INSERT INTO posts (title, content, category_id, user_id, is_published)
                VALUES (:title, :content, :category_id, :user_id, :is_published)';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
            $stmt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':is_published', $this->is_published, PDO::PARAM_BOOL);

            return $stmt->execute();
        }
        return false;
    }

    public function validate(): void
    {
        // Title
        if (empty($this->title)) {
            $this->validation_errors[] = 'Post title cannot be empty';
        }
        if (strlen($this->title) > 255) {
            $this->validation_errors[] = 'Post title cannot be longer than 255 characters';
        }
    }

    public static function getAllPosts(): array
    {
        $db = static::getDatabase();
        $sql = 'SELECT posts.*, users.username 
                FROM posts 
                INNER JOIN users ON user_id = users.id';

        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $result = $stmt->fetchAll();

        return $result;
    }

    public static function findByID(int $post_id): ?Post
    {
        $db = static::getDatabase();
        $sql = 'SELECT * FROM posts WHERE id = :post_id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":post_id", $post_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public function delete(): bool
    {
        $db = static::getDatabase();
        $sql = 'DELETE FROM posts WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id);

        return $stmt->execute();
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

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }

    public function getLastModified(): string
    {
        return $this->last_modified;
    }

    public function getIsPublished(): bool
    {
        return $this->is_published;
    }

    public function getAuthorName(): string
    {
        return $this->author_name;
    }
}