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
        $this->db = new Database(); // Initialize your Database connection
        $this->messageModel = new Message($this->db->getConnection()); // Initialize Message model with Database connection
        $this->userConnections = []; // Initialize an array to store user connections
        $this->userModel = new User($this->db->getConnection());

        echo "WebSocket server started\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Fetch the JWT token from query parameters
        $queryParams = [];
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);
        $jwtToken = $queryParams['token'] ?? null;
        $user_id = $queryParams['user_id'] ?? null;

        if ($jwtToken) {
            try {
                $jwtService = new JWTService();
                $decoded = $jwtService->decode($jwtToken);

                // Access properties of the decoded object
                $tokenUserId = $decoded->sub ?? null;

                if ($tokenUserId && $tokenUserId == $user_id) {
                    // Validate user ID and store connection
                    $this->userConnections[$user_id] = $conn;
                    $this->clients->attach($conn);
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
            echo "JWT token not found in query parameters.\n";
            $conn->close();
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);
        $message = $data->message;
        $recipientId = $data->recipientId;
        $senderId = array_search($from, $this->userConnections);

        if ($senderId !== false) {
            $this->storeMessage($senderId, $recipientId, $message);
            $this->sendToRecipient($recipientId, $message, $senderId);
        } else {
            echo "Sender ID not found in connections.\n";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $user_id = array_search($conn, $this->userConnections);
        if ($user_id !== false) {
            unset($this->userConnections[$user_id]);
            $this->clients->detach($conn);
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

            // Create the message payload
            $payload = json_encode([
                'message' => $message,
                'senderId' => $senderId,
                'senderName' => $senderData['name'],
                'senderSurname' => $senderData['surname']
            ]);

            // Send the payload
            $conn->send($payload);
            echo "Message sent to user {$recipientId}\n";
        } else {
            echo "Recipient ID {$recipientId} not connected\n";
        }
    }
}
