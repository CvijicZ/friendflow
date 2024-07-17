<?php

namespace App\Validation;

use App\Models\Friends;
use App\Models\User;

class FriendsValidation
{
    private $errors = [];
    private $friendsModel;
    private $userModel;

    public function __construct(Friends $friendsModel, User $userModel)
    {
        $this->friendsModel = $friendsModel;
        $this->userModel = $userModel;
    }

    public function validateFriendRequest($receiverId, $requestorId)
    {
        if (empty($receiverId) || empty($requestorId)) {
            $this->errors['friend_request'] = "User id missing in request";
            return $this->errors;
        }
        if (!$this->userModel->show($receiverId)) {
            $this->errors['friend_request'] = "Could not find user to send friend request";
            return $this->errors;
        }

        if ($this->friendsModel->isFriendRequestSent($receiverId, $requestorId)) {
            $this->errors['friend_request'] = "Friend request already exists";
            return $this->errors;
        }
    }
}
