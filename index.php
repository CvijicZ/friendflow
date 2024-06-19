<?php

require_once 'vendor/autoload.php';

use App\Core\Router;

$router = new Router();
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($uri, $method);