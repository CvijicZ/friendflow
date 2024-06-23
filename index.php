<?php

require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


$router = new Router();
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($uri, $method);