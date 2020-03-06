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
    private $introduction;
    private $content;
    private $category_id;
    private $user_id;
    private $create_time;
    private $last_modified;
    private $is_published;
    private $url_slug;
    private $image;
    private $validation_errors = [];

    // Additional properties for SQL queries with JOIN

    /**
     * Username of post author
     */
    private $author_name;

    /**
     * Post category name
     */
    private $category_name;


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
            case 'cat_name':
                if(empty($this->category_name)){
                    $this->category_name = $value;
                }
                break;
        }
    }

    public function saveToDatabase(): bool
    {
        $this->validate();

        if (empty($this->validation_errors)) {
            $db = static::getDatabase();
            $sql = 'INSERT INTO posts (title, introduction, content, category_id, user_id, is_published, url_slug, image)
                VALUES (:title, :introduction, :content, :category_id, :user_id, :is_published, :url_slug, :image)';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':title', trim($this->title), PDO::PARAM_STR);
            $stmt->bindValue(':introduction', trim($this->introduction), PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
            $stmt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':is_published', $this->is_published, PDO::PARAM_BOOL);
            $stmt->bindValue(':url_slug', $this->url_slug, PDO::PARAM_STR);
            $stmt->bindValue(':image', $this->image, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public function validate(): void
    {
        // Title
        if (strlen(trim($this->title)) < 3 || strlen(trim($this->title)) > 255) {
            $this->validation_errors[] = 'Post name must be between 3 and 255 characters';
        }

        // Slug
        if (strlen($this->url_slug) < 3 || strlen($this->url_slug) > 255) {
            $this->validation_errors[] = 'Slug must be between 3 and 25 characters';
        }
        if (preg_match("/^[a-z0-9-]+$/",  $this->url_slug) === 0) {
            $this->validation_errors[] = 'Slug can only contain alphanumeric characters (lowercase letters a-z, numbers 0-9) and dash';
        }
        if (static::slugExist($this->url_slug, $this->id ?? null)) {
            $this->validation_errors[] = 'URL slug is already taken';
        }

        // Introduction
        if (strlen($this->introduction) > 255) {
            $this->validation_errors[] = 'Introduction can have a maximum of 255 characters';
        }
    }

    public static function getAllPosts(bool $getDrafts = true, int $limit_offset = 0, int $limit_row_num = PHP_INT_MAX): array
    {
        $db = static::getDatabase();
        $sql = "SELECT posts.*, users.username, categories.name cat_name
                FROM posts 
                INNER JOIN users ON user_id = users.id
                LEFT OUTER JOIN categories ON category_id = categories.id";
        if (!$getDrafts) {
            $sql .= "\nWHERE is_published = 1";
        }
        $sql .= "\nORDER BY create_time DESC 
                 LIMIT $limit_offset, $limit_row_num";

        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $result = $stmt->fetchAll();

        return $result;
    }

    public static function findByID(int $post_id): ?Post
    {
        $db = static::getDatabase();
        $sql = 'SELECT posts.*, users.username, categories.name cat_name
                FROM posts
                INNER JOIN users ON user_id = users.id
                LEFT OUTER JOIN categories ON category_id = categories.id
                WHERE posts.id = :post_id';

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

    public function update(array $post_data): bool
    {
        $this->title = trim($post_data['title']);
        $this->introduction = $post_data['introduction'];
        $this->content = $post_data['content'];
        $this->category_id = $post_data['category_id'];
        $this->last_modified = date('Y-m-d H:i:s');
        $this->is_published = $post_data['is_published'];
        $this->url_slug = $post_data['url_slug'];
        $this->image = $post_data['image'];

        $this->validate();

        if (empty($this->validation_errors)) {
            $db = static::getDatabase();
            $sql = 'UPDATE posts
                    SET title = :title,
                    introduction = :introduction,
                    content = :content,
                    category_id = :category_id,
                    last_modified = :last_modified,
                    is_published = :is_published,
                    url_slug = :url_slug,
                    image = :image
                    WHERE id = :id';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':introduction', $this->introduction, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
            $stmt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
            $stmt->bindValue(':last_modified', $this->last_modified, PDO::PARAM_STR);
            $stmt->bindValue(':is_published', $this->is_published, PDO::PARAM_BOOL);
            $stmt->bindValue(':url_slug', $this->url_slug, PDO::PARAM_STR);
            $stmt->bindValue(':image', $this->image, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    public static function getTotal(bool $count_drafts = true): int
    {
        $db = static::getDatabase();
        $sql = "SELECT COUNT(*) FROM posts";
        if (!$count_drafts) {
            $sql .= "\nWHERE is_published = 1";
        }
        $stmt = $db->query($sql);

        return $stmt->fetchColumn();
    }

    public static function findBySlug(string $slug): ?Post
    {
        $db = static::getDatabase();
        $sql = 'SELECT posts.*, users.username, categories.name cat_name
                FROM posts
                INNER JOIN users ON user_id = users.id
                LEFT OUTER JOIN categories ON category_id = categories.id
                WHERE url_slug = :url_slug';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":url_slug", $slug, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public static function slugExist(string $slug, int $ignore_id = null): bool
    {
        $post = static::findBySlug($slug);
        if ($post && $post->id != $ignore_id){
            return true;
        }
        return false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getCreateTime(): ?string
    {
        return $this->create_time;
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }

    public function getLastModified(): ?string
    {
        return $this->last_modified;
    }

    public function getIsPublished(): ?bool
    {
        return $this->is_published;
    }

    public function getAuthorName(): ?string
    {
        return $this->author_name;
    }

    public function getCategoryName(): ?string
    {
        return $this->category_name;
    }

    public function getUrlSlug(): ?string
    {
        return $this->url_slug;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setValidationErrors(array $validation_errors): void
    {
        $this->validation_errors = $validation_errors;
    }
}