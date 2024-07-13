<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\Database;
use App\Core\Flash;
use App\Validation\UserValidator;

class UserController extends Controller
{
    private $userModel;
    private $validator;

    public function __construct()
    {
        $db = new Database();
        $this->userModel = new User($db->getConnection());
        $this->validator=new UserValidator($this->userModel);

    }

    public function addFriend(){
        header('Content-Type: application/json; charset=utf-8');

        $receiverId=$_POST['receiverId'];
        $error=$this->validator->validateFriendRequest($receiverId, $_SESSION['user_id']);

        if(empty($error)){

          if($this->userModel->sendFriendRequest($receiverId, $_SESSION['user_id'])){
            echo json_encode(['status' => "success", "message" => "Friend request sent."]);
            exit();
          }
          echo json_encode(['status' => "error", "message" => "Can't send friend request, try again later."]);
          exit();
        }
        echo json_encode(['status' => "error", "message" => $error['friend_request']]);
        exit();
    }
    public function editProfile()
    {

        $userInfo = $this->userModel->show($_SESSION['user_id']);
        if ($userInfo) {
            $this->view('user/profile', $userInfo);
        } else {
            Flash::set('error', "Can not find user information.");
            header('Location: /friendflow/');
            exit();
        }

    }
    public function updateProfile()
    {

        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $passwordRepeat = trim($_POST['password_repeated']);
        $birthYear = trim($_POST['birth_year']);
        $birthMonth = trim($_POST['birth_month']);
        $birthDay = trim($_POST['birth_day']);

        $birthDate = "$birthYear-$birthMonth-$birthDay";

        $validator = new UserValidator($this->userModel);
        $errors = $validator->validateProfileInfo([
            "name" => $name,
            "surname" => $surname,
            "email" => $email,
            "password" => $password,
            "password_repeated" => $passwordRepeat,
            "birthday" => $birthDate
        ], true); // Setting second parameter to true to make password optional for this request

        if (!$validator->hasErrors()) {

            if ($this->userModel->update($name, $surname, $email, $birthDate, $password)) {
                Flash::set('success', "Profile successfuly updated.");
                header("Location: /friendflow/profile");
                exit();
            } else {
                Flash::set('error', "Something went wrong.");
            }

        } else {
            foreach ($errors as $error) {
                Flash::set('error', $error);
            }
            header("Location: /friendflow/profile");
            exit();
        }
    }

}