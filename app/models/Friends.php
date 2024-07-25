<?php

namespace App\Models;

use PDO;
use Exception;

class Friends
{
    private const DEFAULT_FRIEND_REQUEST_STATUS = "pending";
    private const ACCEPTED_FRIEND_REQUEST_STATUS = "accepted";
    private const DECLINED_FRIEND_REQUEST_STATUS = "declined";
    private const DEFAULT_FRIENDSHIP_TYPE = "friends";

    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllSentFriendRequests($userId){
        $sql="SELECT receiver_id FROM friend_requests WHERE requestor_id=:userId";

        $stmt=$this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_NUM);
    }

    public function getAllReceivedFriendRequests($userId){
        $sql="SELECT requestor_id FROM friend_requests WHERE receiver_id=:userId";

        $stmt=$this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_NUM);
    }

    public function areFriends(int $userId1, int $userId2)
    {
        $sumOfIds = $userId1 + $userId2;

        $sql = "SELECT id FROM friends WHERE sum_of_user_ids=:sumOfIds;";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':sumOfIds', $sumOfIds);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isFriendRequestSent(int $userId1, int $userId2)
    {
        $sumOfIds = $userId1 + $userId2;

        $sql = "SELECT status FROM friend_requests WHERE (sum_of_user_ids=:sumOfIds)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('sumOfIds', $sumOfIds);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getFriendRequests($userId)
    {

        $status = Friends::DEFAULT_FRIEND_REQUEST_STATUS;

        $sql = "SELECT * FROM friend_requests WHERE receiver_id=:userId AND status=:status";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':status', $status);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countFriendRequests($userId)
    {

        $status = Friends::DEFAULT_FRIEND_REQUEST_STATUS;

        $sql = "SELECT COUNT(*) AS requestCount FROM friend_requests WHERE receiver_id = :userId AND status=:status";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['requestCount'])) {
            return (int) $result['requestCount'];
        } else {
            return 0;
        }
    }

    public function acceptFriendRequest($friendRequestId)
    {

        $this->db->beginTransaction();

        try {
            $status = Friends::ACCEPTED_FRIEND_REQUEST_STATUS;

            $sql = "SELECT * FROM friend_requests WHERE id = :friendRequestId";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':friendRequestId', $friendRequestId);
            $stmt->execute();
            $friendRequest = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $sql = "UPDATE friend_requests SET status = :status WHERE id = :friendRequestId";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':friendRequestId', $friendRequestId);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            $stmt->closeCursor();

            $stmt = $this->db->prepare("INSERT INTO friends (requestor_id, receiver_id, friendshipType) VALUES (:requestor_id, :receiver_id, :friendshipType)");
            $friendshipType = Friends::DEFAULT_FRIENDSHIP_TYPE;;
            $stmt->bindParam(':requestor_id', $friendRequest['requestor_id']);
            $stmt->bindParam(':receiver_id', $friendRequest['receiver_id']);
            $stmt->bindParam(':friendshipType', $friendshipType);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            echo $e->getMessage();
        }
    }

    public function sendFriendRequest($receiverId, $requestorId)
    {
        $sql = "INSERT INTO friend_requests(receiver_id, requestor_id) VALUES(:receiverId, :requestorId)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':receiverId', $receiverId);
        $stmt->bindParam(':requestorId', $requestorId);

        return $stmt->execute();
    }

    public function getAllFriends($userId)
    {
        $sql = "CALL getAllFriends(:userId)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        $result = $stmt->fetchAll();
        return array_column($result, 'friend_id');
    }

    public function getFriendSuggestions($userId)
    {
        try {
            $friends = $this->getAllFriends($userId);
            $alreadySentRequestUsers = $this->getAllSentFriendRequests($userId);
            $receivedRequests=$this->getAllReceivedFriendRequests($userId);
    
            $friends = $this->flattenArray($friends);
            $alreadySentRequestUsers = $this->flattenArray($alreadySentRequestUsers);
            $receivedRequests=$this->flattenArray($receivedRequests);
    
            $excludeIds = array_merge($friends, $alreadySentRequestUsers, $receivedRequests);
    
            if (empty($excludeIds)) {
                $excludeIds = [$userId];
            } else {
                $excludeIds[] = $userId;
            }

            $placeholders = rtrim(str_repeat('?,', count($excludeIds)), ',');

            $sql = "SELECT id, name, surname, profile_image_name 
                    FROM users 
                    WHERE id NOT IN ($placeholders) 
                    LIMIT 10";
    
            $stmt = $this->db->prepare($sql);
    
            foreach ($excludeIds as $index => $id) {
                $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
            }

            $stmt->execute();

            $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $suggestions;
    
        } catch (\PDOException $e) {
            echo "SQL Error: " . $e->getMessage();
        }
    }
    

    private function flattenArray($array) {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $result = array_merge($result, $this->flattenArray($item));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }
}

