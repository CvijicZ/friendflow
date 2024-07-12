<?php

namespace App\Models;

use PDO;

class Comment
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create($postId, $userId, $content)
    {
        $sql = "INSERT INTO comments(post_id, user_id, content) VALUES(:postId, :userId, :content)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':content', $content);

        return $stmt->execute();
    }

    public function index($postId)
    {
        $sql = "SELECT * FROM comments WHERE post_id=:postId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':postId', $postId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function show($id)
    {
        $sql = "SELECT * FROM comments WHERE id=:id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $content)
    {
        $sql = "UPDATE comments SET content=:content WHERE id=:id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':content', $content);

        return $stmt->execute();
    }

    public function destroy($id)
    {
        $sql = "DELETE FROM comments WHERE id=:id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}