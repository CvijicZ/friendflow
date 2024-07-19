<?php

namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Core\Database;
use App\Models\Message;
use Dotenv\Dotenv;
use App\Services\JWTService;
use Exception;
use App\Models\User;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $messageModel;
    protected $db;
    protected $userConnections;
    protected $userModel;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->db = new Database();
        $this->messageModel = new Message($this->db->getConnection());
        $this->userConnections = [];
        $this->userModel = new User($this->db->getConnection());

        echo "WebSocket server started\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $queryParams = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);
        $jwtToken = $queryParams['token'] ?? null;
        $user_id = $queryParams['user_id'] ?? null;

        if ($jwtToken && $user_id) {
            try {
                $jwtService = new JWTService();
                $decoded = $jwtService->decode($jwtToken);

                $tokenUserId = $decoded->sub ?? null;

                if ($tokenUserId && $tokenUserId == $user_id) {
                    $this->userConnections[$user_id] = $conn;
                    $this->clients->attach($conn);

                    $connectedUsers = $this->getConnectedUsers();
                    $conn->send(json_encode([
                        'type' => 'connectedUsers',
                        'users' => $connectedUsers
                    ]));
                    $this->broadcastStatus($user_id, 'online');
                    echo "New connection for user {$user_id}! ({$conn->resourceId})\n";
                } else {
                    echo "Invalid token or user ID. Connection rejected.\n";
                    $conn->close();
                }
            } catch (Exception $e) {
                echo "Token validation failed: {$e->getMessage()}\n";
                $conn->close();
            }
        } else {
            echo "JWT token or user ID not found in query parameters.\n";
            $conn->close();
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Invalid JSON message received.\n";
            return;
        } else {
            $message = $data->message ?? '';
            $recipientId = $data->recipientId ?? null;
            $senderId = array_search($from, $this->userConnections, true);

            if ($senderId !== false && $recipientId) {
                $this->storeMessage($senderId, $recipientId, $message);
                $this->sendToRecipient($recipientId, $message, $senderId);
            } else {
                echo "Invalid sender or recipient ID.\n";
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $user_id = array_search($conn, $this->userConnections, true);
        if ($user_id !== false) {
            $this->broadcastStatus($user_id, 'offline');
            unset($this->userConnections[$user_id]);
            $this->clients->detach($conn);
            $conn->close();
            echo "Connection for user {$user_id} has disconnected\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "WebSocket error: " . $e->getMessage() . "\n";
        $conn->close();
    }

    protected function storeMessage($senderId, $recipientId, $message)
    {
        $this->messageModel->store($senderId, $recipientId, $message);
    }

    protected function sendToRecipient($recipientId, $message, $senderId)
    {
        if (isset($this->userConnections[$recipientId])) {
            $conn = $this->userConnections[$recipientId];

            $senderData = $this->userModel->show($senderId);

            $payload = json_encode([
                'message' => $message,
                'senderId' => $senderId,
                'senderName' => $senderData['name'],
                'senderSurname' => $senderData['surname']
            ]);

            $conn->send($payload);
            echo "Message sent to user {$recipientId}\n";
        } else {
            echo "Recipient ID {$recipientId} not connected\n";
        }
    }

    public function getConnectedUsers()
    {
        $connectedUsers = [];
        foreach ($this->userConnections as $userId => $conn) {
            $connectedUsers[$userId] = 'online';
        }

        return $connectedUsers;
    }
    protected function broadcastStatus($userId, $status)
    {
        $payload = json_encode([
            'type' => 'status',
            'userId' => $userId,
            'status' => $status
        ]);

        foreach ($this->clients as $client) { // TODO: implement feature to send this message only to friends
            $client->send($payload);
        }
    }
}
