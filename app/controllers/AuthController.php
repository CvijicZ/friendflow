<?php

namespace App\Controllers;
use App\Core\Controller;

class AuthController extends Controller {

   
    public function showRegisterForm() {
        $this->view('auth/register');
    }


    public function showLoginForm() {
        $this->view('auth/login');
    }

    public function login() {
        // Handle login logic here
    }
}
