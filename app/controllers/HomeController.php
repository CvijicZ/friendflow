<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;
use App\Core\Database;
use App\Models\User;
use App\Models\Friends;

class HomeController extends Controller
{
    public function index()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (AuthMiddleware::isLoggedIn()) {

            $db = new Database();
            $userModel = new User($db->getConnection());
            $friendsModel = new Friends($db->getConnection());

            $userInfo = $userModel->show($_SESSION['user_id']);
            $friendSuggestions = $friendsModel->getFriendSuggestions(AuthMiddleware::getUserId());

            $this->view('home/index', ['auth_user' => $this->sanitizeArray($userInfo), 'suggested_friends' => $this->sanitizeArray($friendSuggestions)]);
        } else {
            $this->view('home/guest-index');
        }
    }

    public function error()
    {
        $this->view('home/error');
    }
}
