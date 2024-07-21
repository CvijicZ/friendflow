<?php

namespace App\Models;

use PDO;

class Message
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function store($senderId, $recipientId, $message)
    {
        $sql = "INSERT INTO messages(sender_id, recipient_id, message) VALUES(:senderId, :recipientId, :message)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':senderId', $senderId, PDO::PARAM_INT);
        $stmt->bindParam(':recipientId', $recipientId, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);

        return $stmt->execute();
    }

    function getMessages($userId, $friendId, $limit, $offset) {
        $stmt = $this->db->prepare("
            SELECT id, message, created_at, sender_id FROM messages 
            WHERE (sender_id = :userId AND recipient_id = :friendId) 
            OR (sender_id = :friendId AND recipient_id = :userId)
            ORDER BY created_at ASC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
