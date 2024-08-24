<?php

require_once 'vendor/autoload.php';

use App\Core\Router;
use App\Api\ApiRouter;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove the base URI '/friendflow' from the request URI
$baseUri = '/friendflow';
$uri = str_replace($baseUri, '', $uri);

// Check if the URI starts with '/api'
if (strpos($uri, '/api') === 0) {
    $router = new ApiRouter();
} else {
    $router = new Router();
}

$router->dispatch($uri, $method);
