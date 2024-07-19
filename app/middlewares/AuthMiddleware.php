<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public static function handle()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                // If it's an AJAX request, return a JSON response
                header('Content-Type: application/json');
                echo json_encode(['authenticated' => false, 'redirect' => '/friendflow/login']);
                exit();
            } else {
                // For regular requests, perform the redirection
                header('Location: /friendflow/login');
                exit();
            }
        }
    }
    
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public static function getUserId(){

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'];
    }
}
