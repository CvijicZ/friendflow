<?php

namespace App\Core;

class Flash
{
    public static function set($key, $message)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['flash_messages'][$key])) {
            $_SESSION['flash_messages'][$key] = [];
        }
        $_SESSION['flash_messages'][$key][] = $message;
    }

    public static function get($key)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($_SESSION['flash_messages'][$key])) {
            $messages = $_SESSION['flash_messages'][$key];
            unset($_SESSION['flash_messages'][$key]);
            return $messages;
        }
        return [];
    }
}
