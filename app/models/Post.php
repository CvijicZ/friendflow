<?php

namespace App\Models;

use App\Middlewares\AuthMiddleware;
use PDO;
use App\Models\User;
use App\Models\Friends;


class Post
{
    protected $db;
    private $userModel;
    private $commentModel;
    private $friendsModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->userModel = new User($this->db);
        $this->commentModel = new Comment($this->db);
        $this->friendsModel = new Friends($this->db);
    }

    public function index()
    {
        $sql = "SELECT * FROM posts ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as &$post) {
            $post['user'] = $this->userModel->show($post['user_id']);
            $comments = $this->commentModel->index($post['id']);

            foreach ($comments as &$comment) {
                $comment['user'] = $this->userModel->show($comment['user_id']);
            }

            $post['comments'] = $comments;
        }

        return $posts;
    }

    public function show($id)
    {
        $sql = "SELECT * FROM posts WHERE id=:id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function store(string $content, string $image = null)
    {
        $sql = "INSERT INTO posts(user_id, content, image_name) VALUES(:user_id, :content, :image_name)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':image_name', $image);

        return $stmt->execute();
    }

    public function update($id, $newContent)
    {
        $sql = "UPDATE posts SET content=:newContent WHERE id=:id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':newContent', $newContent);

        return $stmt->execute();
    }

    public function destroy($id)
    {
        $sql = "DELETE FROM posts WHERE id=:id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function getPostsFromFriends($userId)
    {
        $friends = $this->friendsModel->getAllFriends($userId);
        $friends[] = AuthMiddleware::getUserId(); // Add and users id to get his own posts

        if (empty($friends)) {
            return [];
        }

        $placeholders = rtrim(str_repeat('?,', count($friends)), ',');

        $sql = "SELECT * FROM posts WHERE user_id IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($friends);

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as &$post) {
            $post['user'] = $this->userModel->show($post['user_id']);
            $comments = $this->commentModel->index($post['id']);
            $post['numberofComments'] = $this->countComments($post['id']);

            foreach ($comments as &$comment) {
                $comment['user'] = $this->userModel->show($comment['user_id']);
            }

            $post['comments'] = $comments;
        }
        return $posts;
    }

    public function countComments($postId)
    {
        $sql = "SELECT COUNT(*) FROM comments WHERE post_id=:postId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();

        $result = $stmt->fetchColumn();

        return $result;
    }
}
