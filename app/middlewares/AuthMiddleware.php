<?php
namespace App\Middlewares;
class AuthMiddleware {
    public static function handle() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /friendflow/login');
            exit();
        }
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}