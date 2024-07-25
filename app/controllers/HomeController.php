<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;
use App\Core\Database;
use App\Models\User;
use App\Models\Post;
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
            $postModel = new Post($db->getConnection());
            $friendsModel= new Friends($db->getConnection());

            $userInfo = $userModel->show($_SESSION['user_id']);
            $allPosts = $postModel->getPostsFromFriends(AuthMiddleware::getUserId());
            $friendSuggestions = $friendsModel->getFriendSuggestions(AuthMiddleware::getUserId());

            $this->view('home/index', ['auth_user' => $userInfo, 'posts' => $allPosts, 'suggested_friends' => $friendSuggestions]);
        } else {
            $this->view('home/guest-index');
        }
    }
    public function error()
    {
        $this->view('home/error');
    }
}