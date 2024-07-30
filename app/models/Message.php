<?php

namespace App\Models;

use PDO;

class Message
{
    private const MESSAGE_STATUS_SEEN = "seen";
    private const MESSAGE_STATUS_UNSEEN = "sent";

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function show($messageId)
    {
        $sql = "SELECT * FROM messages WHERE id=:messageId";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':messageId', $messageId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function store($senderId, $recipientId, $message)
    {
        $sql = "INSERT INTO messages(sender_id, recipient_id, message) VALUES(:senderId, :recipientId, :message)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':senderId', $senderId, PDO::PARAM_INT);
        $stmt->bindParam(':recipientId', $recipientId, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);

        $stmt->execute();

        return $this->db->lastInsertId();
    }

    function getMessages($userId, $friendId, $limit, $offset)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM messages 
            WHERE (sender_id = :userId AND recipient_id = :friendId) 
            OR (sender_id = :friendId AND recipient_id = :userId)
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($messageId)
    {
        $sql = "UPDATE messages SET status=:status WHERE id=:messageId";

        $status = Message::MESSAGE_STATUS_SEEN;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':messageId', $messageId);

        return $stmt->execute();
    }

    public function countUnseenMessages($userId)
    {
        $sql = "SELECT COUNT(*) AS count FROM messages WHERE recipient_id = :userId AND status = :status";

        $status = Message::MESSAGE_STATUS_UNSEEN;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } else {
            return 0;
        }
    }

    public function countUnseenMessagesForSpecificFriend($userId, $friendId)
    {
        $sql = "SELECT COUNT(*) AS count FROM messages WHERE recipient_id = :userId AND status = :status AND sender_id=:friendId";

        $status = Message::MESSAGE_STATUS_UNSEEN;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':friendId', $friendId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } else {
            return 0;
        }
    }
}
