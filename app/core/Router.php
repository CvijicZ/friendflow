<?php

namespace App\Core;

use App\Middlewares\AuthMiddleware;
use App\Core\Database;
use App\Middlewares\CSRFMiddleware;

class Router
{
    private $routes = [];
    private $baseUri = '/friendflow';
    private $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->loadRoutes();
    }

    private function loadRoutes()
    {
        $this->routes = include(__DIR__ . '/routes.php');
    }

    public function dispatch($uri, $method)
    {
        $uri = $this->removeBaseUri($uri);

        if ($method == 'POST' && isset($this->routes[$method][$uri])) {
            CSRFMiddleware::handle();
        }

        if ($method === 'DELETE' || $method === 'PUT') {
            $route = $this->matchRoute($uri, $method);
            if ($route) {
                $uriParts = explode('/', $uri);
                $id = end($uriParts);

                if (is_numeric($id)) {
                    $this->handleMiddleware($route['middleware']);
                    $data = ['id' => $id];
                    parse_str(file_get_contents('php://input'), $_PUT);

                    if (!empty($_PUT)) {
                        $data = array_merge($data, $_PUT);
                    }

                    $this->callAction($route['controller'], $data);
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

        if (strpos($controller, 'App\Controllers\\') === false) {
            $controller = 'App\Controllers\\' . $controller;
        }

        $controller = new $controller($this->db);

        if (!empty($params)) {
            call_user_func_array([$controller, $action], $params);
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
