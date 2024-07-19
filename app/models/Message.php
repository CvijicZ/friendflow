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
}
