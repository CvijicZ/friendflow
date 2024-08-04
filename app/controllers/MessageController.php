<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Middlewares\AuthMiddleware;
use App\Models\Message;

class MessageController extends Controller
{
    private $model;

    public function __construct()
    {
        $db = new Database();
        $this->model = new Message($db->getConnection());
    }

    public function countUnseenMessages()
    {
        header('Content-Type: application/json; charset=utf-8');

        AuthMiddleware::handle();

        $numberOfMessages = $this->model->countUnseenMessages(AuthMiddleware::getUserId()); // Returns 0 if nothing is found
        echo json_encode(['status' => "success", "number_of_messages" => $numberOfMessages]);
        exit();
    }

    public function updateStatus()
    {
        $messageId = $_POST['message_id'];

        header('Content-Type: application/json; charset=utf-8');

        AuthMiddleware::handle();

        $message = $this->model->show($messageId);

        if ($message) {

            if ($message['recipient_id'] != AuthMiddleware::getUserId()) {
                echo json_encode(['status' => "error", "message" => "Insufficient permission."]);
                exit();
            }
            if ($this->model->updateStatus($messageId)) {
                echo json_encode(['status' => "success", "message" => "Status changed to 'seen'."]);
                exit();
            } else {
                echo json_encode(['status' => "error", "message" => "Internal server error."]);
                exit();
            }
        } else {
            echo json_encode(['status' => "error", "message" => "Message not found."]);
            exit();
        }
    }

    public function getMessages()
    {
        $userId = $_POST['user_id'];
        $friendId = $_POST['friend_id'];
        $limit = $_POST['limit'];
        $offset = $_POST['offset'];

        header('Content-Type: application/json; charset=utf-8');

        AuthMiddleware::handle();

        if (AuthMiddleware::getUserId() != $userId) {
            echo json_encode(['status' => "error", "message" => "Insufficient permission."]);
            exit();
        }

        $messages = $this->model->getMessages($userId, $friendId, $limit, $offset);
        $sanitizedMessages = $this->sanitizeArray($messages);

        if ($messages) {
            echo json_encode(['status' => "success", "messages" => $sanitizedMessages]);
            exit();
        }
        echo json_encode(['status' => "error", "message" => "No messages found"]);
        exit();
    }
}
