<?php

namespace App\Core;

use App\Middlewares\AuthMiddleware;
use App\Core\Database;
use App\Middlewares\CSRFMiddleware;

class Router
{
    private $routes = [
        'GET' => [
            '/' => 'App\Controllers\HomeController@index',
            '/error' => 'App\Controllers\HomeController@error',
            '/login' => 'App\Controllers\AuthController@showLoginForm',
            '/logout' => 'App\Controllers\AuthController@logout',
            '/register' => 'App\Controllers\AuthController@showRegisterForm',
            '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\UserController@editProfile'],
        ],
        'POST' => [
            '/login' => 'App\Controllers\AuthController@login',
            '/register' => 'App\Controllers\AuthController@register',
            '/updateProfile' => 'App\Controllers\UserController@updateProfile',
            '/post' => 'App\Controllers\PostController@create',
        ],
        'PUT' => [
            '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\UserController@update'],
        ],
        'DELETE' => [
            '/profile' => ['middleware' => 'auth', 'controller' => 'App\Controllers\UserController@delete'],
            '/post' => ['middleware' => 'auth', 'controller' => 'App\Controllers\PostController@delete'],
        ]
    ];

    private $baseUri = '/friendflow';
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function dispatch($uri, $method)
    {
        // Remove the base URI from the requested URI
        $uri = $this->removeBaseUri($uri);

        // If request is POST always handle CSRF token
        if ($method === 'POST' && isset($this->routes[$method][$uri])) {
            CSRFMiddleware::handle();
        }

        // Match the route and extract the id for DELETE requests
        if ($method === 'DELETE') {
            $route = $this->matchRoute($uri, 'DELETE');
            if ($route) {
                $uriParts = explode('/', $uri);
                $id = end($uriParts);

                if (is_numeric($id)) {
                    $this->handleMiddleware($route['middleware']);
                    $this->callAction($route['controller'], ['id' => $id]);
                    return;
                }
            }
        } else if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
            if (is_array($route) && isset($route['middleware'])) {
                $this->handleMiddleware($route['middleware']);
                $this->callAction($route['controller']);
            } else {
                $this->callAction($route);
            }
            return;
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function removeBaseUri($uri)
    {
        $baseUri = rtrim($this->baseUri, '/') . '/';
        if (strpos($uri, $baseUri) === 0) {
            $uri = substr($uri, strlen($baseUri) - 1);
        }
        return $uri;
    }

    private function handleMiddleware($middleware)
    {
        switch ($middleware) {
            case 'auth':
                AuthMiddleware::handle();
                break;
        }
    }

    private function callAction($controllerAction, $params = [])
    {
        list($controller, $action) = explode('@', $controllerAction);
        $controller = new $controller($this->db);

        // Make sure params are passed as positional arguments
        if (!empty($params)) {
            call_user_func_array([$controller, $action], array_values($params));
        } else {
            $controller->$action();
        }
    }

    private function matchRoute($uri, $method)
    {
        foreach ($this->routes[$method] as $route => $config) {
            if (preg_match('#^' . preg_quote($route, '#') . '/\d+$#', $uri)) {
                return $config;
            }
        }
        return null;
    }
}
