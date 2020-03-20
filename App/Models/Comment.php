<?php


namespace App\Models;


use Core\Model;
use PDO;

class Comment extends Model
{
    private $id;
    private $user_id;
    private $post_id;
    private $content;
    private $submit_time;
    private $author_name;
    private $validation_errors = [];

    public function __construct(array $user_data = [])
    {
        foreach ($user_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __set($name, $value): void
    {
        if ($name == 'username') {
            if (empty($this->author_name)) {
                $this->author_name = $value;
            }
        }
    }

    public static function getAllComments(int $post_id = null): array
    {
        $db = static::getDatabase();
        $sql = "SELECT comments.*, username FROM comments 
                INNER JOIN users ON user_id = users.id";
        if ($post_id) {
            $sql .= "\nWHERE post_id = :post_id";
        }

        $stmt = $db->prepare($sql);
        if ($post_id) {
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        }

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function save(): bool
    {
        $this->validate();

        if (empty($this->validation_errors)) {
            $db = self::getDatabase();
            $sql = 'INSERT INTO comments (user_id, post_id, content)
                    VALUES (:user_id, :post_id, :content)';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':post_id', $this->post_id, PDO::PARAM_INT);
            $stmt->bindValue(':content', trim($this->content), PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function validate(): void
    {
        if (strlen(trim($this->content)) < 1 || strlen(trim($this->content)) > 2000) {
            $this->validation_errors[] = 'Category name must be between 1 and 2000 characters';
        }
        if (!User::findByID($this->user_id)) {
            $this->validation_errors[] = 'Invalid user identifier';
        }
        if (!Post::findByID($this->post_id)) {
            $this->validation_errors[] = 'Invalid post identifier';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getSubmitTime(): ?string
    {
        return $this->submit_time;
    }

    public function getAuthorName(): ?string
    {
        return $this->author_name;
    }
}