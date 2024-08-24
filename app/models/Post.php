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

    // TODO: overcomplicated function, create another function that catches all posts from one person
              // then with foreach catch all posts from friends and concat results in one array and return it
                // Do not forget to sort merged array by created_at
    public function getPostsFromFriends($userId, $limit, $offset, $apiRequest=false)
    {
        $friends = $this->friendsModel->getAllFriends($userId);
        $friends[] = $userId; // Add user's own id to get their own posts

        if (empty($friends)) {
            return [];
        }
        $placeholders = rtrim(str_repeat('?,', count($friends)), ',');
        $sql = "SELECT * FROM posts WHERE user_id IN ($placeholders) ORDER BY created_at DESC LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        foreach ($friends as $k => $friend) {
            $stmt->bindValue(($k + 1), $friend);
        }

        $stmt->bindValue(count($friends) + 1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(count($friends) + 2, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as &$post) {
            $post['user'] = $this->userModel->show($post['user_id']);
            $post['numberofComments'] = $this->countComments($post['id']);
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

    public function getComments($postId)
    {
        $sql = "SELECT * FROM posts WHERE id=:postId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        $comments = $this->commentModel->index($post['id']);

        foreach ($comments as &$comment) {
            $comment['user'] = $this->userModel->show($comment['user_id']);
        }

        $post['comments'] = $comments;

        return $post;
    }

    public function getCreator($postId)
    {
        $postInfo = $this->show($postId);
        return $postInfo['user_id'];
    }
}
