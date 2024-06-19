<?php

namespace App\Core;

use App\Middlewares\AuthMiddleware;

class Router {
    private $routes = [
        'GET' => [
            '/' => 'App\Controllers\HomeController@index',
            '/login' => 'App\Controllers\AuthController@showLoginForm',
            '/register' => 'App\Controllers\AuthController@showRegisterForm',
            '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\ProfileController@index'],
        ],
        'POST' => [
            '/login' => 'App\Controllers\AuthController@login',
            '/register' => 'App\Controllers\AuthController@register',
        ],
        'PUT' => [
            '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\ProfileController@update'],
        ],
        'DELETE' => [
            '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\ProfileController@delete'],
        ]
    ];

    private $baseUri = '/friendflow';

    public function dispatch($uri, $method) {
        // Remove the base URI from the requested URI
        $uri = $this->removeBaseUri($uri);

        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
            if (is_array($route) && isset($route['middleware'])) {
                $this->handleMiddleware($route['middleware']);
                $this->callAction(...explode('@', $route['controller']));
            } else {
                $this->callAction(...explode('@', $route));
            }
        } else {
            // 404 Not Found
            http_response_code(404);
            echo "404 Not Found";
        }
    } 

    private function removeBaseUri($uri) {
        $baseUri = rtrim($this->baseUri, '/') . '/';
        if (strpos($uri, $baseUri) === 0) {
            $uri = substr($uri, strlen($baseUri) - 1);
        }
        return $uri;
    }

    private function handleMiddleware($middleware) {
        switch ($middleware) {
            case 'auth':
                AuthMiddleware::handle();
                break;
        }
    }

    private function callAction($controller, $action) {
        $controller = new $controller();
        $controller->$action();
    }
}
