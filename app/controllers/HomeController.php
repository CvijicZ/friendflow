<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;
use App\Core\Database;
use App\Models\User;
use App\Models\Post;

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

            $userInfo = $userModel->show($_SESSION['user_id']);
            $allPosts = $postModel->index();
            $allUsers = $userModel->index();

            $this->view('home/index', ['auth_user' => $userInfo, 'posts' => $allPosts, 'all_users' => $allUsers]);
        } else {
            $this->view('home/guest-index');
        }
    }
    public function error()
    {
        $this->view('home/error');
    }
}