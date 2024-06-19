<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;

class HomeController extends Controller {
    public function index() {
        if (AuthMiddleware::isLoggedIn()) {
            $this->view('home/index');
        } else {
            $this->view('home/guest-index');
        }
    }
}