<?php
namespace App\Api;

use App\Core\Database;
use App\Controllers\Api\Controller;

class ApiRouter
{
    private $routes = [];
    private $db;
    private $controller;

    public function __construct()
    {
        $this->db = new Database();
        $this->loadRoutes();
        $this->controller = new Controller();
    }

    private function loadRoutes()
    {
        $this->routes = include(__DIR__ . '/api_routes.php');
    }

    public function dispatch($uri, $method)
    {
        // Extract the path component from the URI (excluding query params)
        $uriPath = parse_url($uri, PHP_URL_PATH);
        $uri = $this->removeBaseUri($uriPath);
    
        if ($method === 'OPTIONS') {
            $this->handleOptionsRequest();
            return;
        }
    
        $id = $this->extractIdFromUri($uri);
        $queryParams = $_GET; // Extract query parameters
    
        // Check if the HTTP method exists in the routes array
        if (isset($this->routes[$method])) {
            $route = $this->matchRoute($uri, $method);
            if ($route) {
                $this->callAction($route, $id, $queryParams);
                return;
            }
        }
    
        $this->controller->errorResponse(null, "Not implemented", 404);
        exit();
    }
    

    private function removeBaseUri($uri)
    {
        $baseUri = '/api/';
        if (strpos($uri, $baseUri) === 0) {
            $uri = substr($uri, strlen($baseUri));
        }
        return trim($uri, '/'); // Remove leading/trailing slashes
    }

    private function callAction($controllerAction, $id = null, $queryParams = [])
    {
        list($controllerClass, $action) = explode('@', $controllerAction);
        $controller = new $controllerClass($this->db);

        if ($id !== null) {
            $controller->$action($id, $queryParams);
        } else {
            $controller->$action($queryParams);
        }
    }

    private function matchRoute($uri, $method)
    {
        foreach ($this->routes[$method] as $routePattern => $controllerAction) {
            $pattern = $this->convertRouteToPattern($routePattern);

            if (preg_match($pattern, $uri, $matches)) {
                return $controllerAction;
            }
        }
        return null;
    }

    private function convertRouteToPattern($route)
    {
        // Escape all regex special characters except for {id}
        $escapedRoute = preg_quote($route, '#');

        // Replace {id} with (\d+) to match numeric IDs
        $pattern = str_replace('\{id\}', '(\d+)', $escapedRoute);

        // Return the pattern with proper delimiters and start/end anchors
        return "#^$pattern$#";
    }

    private function extractIdFromUri($uri)
    {
        $parts = explode('/', $uri);
        $id = end($parts);
        return is_numeric($id) ? $id : null;
    }

    private function handleOptionsRequest()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("HTTP/1.1 200 OK");
        exit();
    }
}
