<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Friends;
use App\Middlewares\AuthMiddleware;
use App\Models\User;
use App\Validation\FriendsValidation;

class FriendsController extends Controller
{
    private $friendsModel;
    private $userModel;
    private $validator;

    public function __construct()
    {
        $db = new Database();
        $this->friendsModel = new Friends($db->getConnection());
        $this->userModel = new User($db->getConnection());
        $this->validator = new FriendsValidation($this->friendsModel, $this->userModel);
    }

    public function getAllFriends()
    {
        header('Content-Type: application/json; charset=utf-8');

        AuthMiddleware::handle();

        $friendsIds = $this->friendsModel->getAllFriends($_SESSION['user_id']);

        $allFriendsInfo = [];

        foreach ($friendsIds as $friendId) {
            $userInfo = $this->userModel->show($friendId['friend_id']);
            $allFriendsInfo[] = $userInfo;
        }

        if ($allFriendsInfo) {
            echo json_encode(['status' => "success", "data" => $allFriendsInfo]);
            exit();
        }
        echo json_encode(['status' => "error", "message" => "No friends found."]);
        exit();
    }

    public function addFriend()
    {
        header('Content-Type: application/json; charset=utf-8');

        $receiverId = $_POST['receiverId'];
        $error = $this->validator->validateFriendRequest($receiverId, $_SESSION['user_id']);

        if (empty($error)) {

            if ($this->friendsModel->sendFriendRequest($receiverId, $_SESSION['user_id'])) {
                echo json_encode(['status' => "success", "message" => "Friend request sent."]);
                exit();
            }
            echo json_encode(['status' => "error", "message" => "Can't send friend request, try again later."]);
            exit();
        }
        echo json_encode(['status' => "error", "message" => $error['friend_request']]);
        exit();
    }

    public function acceptFriendRequest()
    { // TODO: Add validation to check if receiver_id is equal to $_SESION user_id
        header('Content-Type: application/json; charset=utf-8');

        $result = $this->friendsModel->acceptFriendRequest($_POST['friendRequestId']);
        if ($result) {
            echo json_encode(['status' => "success", "message" => "Friend request accepted."]);
            exit();
        }
        echo json_encode(['status' => "error", "message" => "Something went wrong."]);
        exit();
    }

    public function countFriendRequests()
    {
        header('Content-Type: application/json; charset=utf-8');

        $result = $this->friendsModel->countFriendRequests($_SESSION['user_id']);

        echo json_encode(['status' => "success", "number_of_requests" => $result]);
        exit();
    }

    public function getFriendRequests()
    {
        header('Content-Type: application/json; charset=utf-8');

        AuthMiddleware::handle();

        $result = $this->friendsModel->getFriendRequests($_SESSION['user_id']);
        if ($result) {
            $dataToReturn = [];

            foreach ($result as $row) {
                $requestorDetails = $this->userModel->show($row['requestor_id']);

                $requestData = [
                    'id' => $row['id'],
                    'name' => $requestorDetails['name'],
                    'surname' => $requestorDetails['surname'],
                    'datetime' => $row['date']
                ];
                $dataToReturn[] = $requestData;
            }
            echo json_encode(['status' => "success", "data" => $dataToReturn]);
            exit();
        } else {
            echo json_encode(['status' => "error", "message" => "No friend requests"]);
            exit();
        }
    }
}
