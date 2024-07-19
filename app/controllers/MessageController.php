<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Middlewares\AuthMiddleware;
use App\Models\User;
use App\Models\Message;

class MessageController extends Controller
{
    private $userModel;
    private $model;

    public function __construct()
    {
        $db = new Database();

        $this->userModel = new User($db->getConnection());
        $this->model = new Message($db->getConnection());
    }

    public function getMessages()
    {
        $userId=$_POST['user_id'];
        $friendId=$_POST['friend_id'];
        $limit=$_POST['limit'];
        $offset=$_POST['offset'];

        header('Content-Type: application/json; charset=utf-8');

        AuthMiddleware::handle();

        if (AuthMiddleware::getUserId() != $userId) {
            echo json_encode(['status' => "error", "message" => "Insufficient permission."]);
            exit();
        }

        $rawMessages = $this->model->getMessages($userId, $friendId, $limit, $offset);

        $processedMessages = $this->processMessages($userId, $rawMessages);

        if ($processedMessages) {
            echo json_encode(['status' => "success", "messages" => $processedMessages]);
            exit();
        }
        echo json_encode(['status' => "error", "message" => "No messages found"]);
        exit();
    }

    function processMessages($userId, $messages) {
        $result = [
            'user_messages' => [],
            'friend_messages' => []
        ];
    
        foreach ($messages as $message) {
            $messageContent = htmlspecialchars($message['message']);
            $messageData = [
                'content' => $messageContent,
                'created_at' => $message['created_at']
            ];
    
            if ($message['sender_id'] == $userId) {
                $result['user_messages'][] = $messageData;
            } else {
                $result['friend_messages'][] = $messageData;
            }
        }
    
        return $result; 
    }
}
