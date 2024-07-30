<?php

namespace App\Middlewares;

use App\Core\Flash;

class CSRFMiddleware
{
    public static function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $token = $_POST['csrf_token'] ?? '';
            if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] != $token) {
                Flash::set('error', 'Invalid CSRF token.');
                http_response_code(403);
                header('Location: /friendflow/error');
                exit();
            }
        }
    }

    public static function compare($_token)
    {
        return $_SESSION['csrf_token'] == $_token;
    }

    public static function generateToken()
    {
        if (!isset($_SESSION)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    public static function getToken()
    {
        if (!isset($_SESSION)) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
        if (!isset($_SESSION['csrf_token'])) {
            return self::generateToken();
        }
        return $_SESSION['csrf_token'];
    }
}
