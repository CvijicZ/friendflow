<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;
use App\Core\Database;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        session_start();
        if (AuthMiddleware::isLoggedIn()) {

            $db = new Database();
            $userModel = new User($db->getConnection());
            $userInfo = $userModel->show($_SESSION['user_id']);

            $this->view('home/index', $userInfo);
        } else {
            $this->view('home/guest-index');
        }
    }

    public function error()
    {
        $this->view('home/error');
    }
}