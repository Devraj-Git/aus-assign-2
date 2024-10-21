<?php

namespace App\System\Core;
use App\Models\Users;

class Router
{
    protected $routes = [];
    protected $basePath = '';

    public function __construct() {
        session_start();
        $appUrl = config('app_url');
        $parsedUrl = parse_url($appUrl);
        $this->basePath = isset($parsedUrl['path']) ? rtrim($parsedUrl['path'], '/') : '';
    }

    private function addRoute(string $route, string $controller, string $action, string $method)
    {
        $this->routes[strtoupper($method)][$route] = compact('controller', 'action');
    }

    public function get($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, "GET");
    }

    public function post($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, "POST");
    }

    public function delete($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, "DELETE");
    }

    public function put($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, "PUT");
    }

    public function patch($route, $controller, $action)
    {
        $this->addRoute($route, $controller, $action, "PATCH");
    }

    private function matchRoute($uri, $method)
    {
        foreach ($this->routes[$method] as $route => $handler) {
            // Convert routes like "/user/{id}" to a regex pattern
            $pattern = preg_replace('/\{[a-zA-Z0-9]+\}/', '([a-zA-Z0-9]+)', $route);
            $uriWithoutBase = preg_replace("#^" . preg_quote($this->basePath) . "#", '', $uri);
            if (preg_match("#^$pattern$#", $uriWithoutBase, $matches)) {
                array_shift($matches); // Remove the full match from the beginning
                return [$handler, $matches];
            }
        }
        return false;
    }

    public function dispatch()
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        // Check if the request is for a static file (like /assets/images/favicon.png)
        if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $uri)) {
            $filePath = realpath($_SERVER['DOCUMENT_ROOT'] . $uri);

            // Check if the file exists and prevent directory traversal
            if ($filePath && strpos($filePath, $_SERVER['DOCUMENT_ROOT']) === 0 && file_exists($filePath)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                header('Content-Type: ' . finfo_file($finfo, $filePath));
                finfo_close($finfo);
                readfile($filePath);
                exit;
            } else {
                http_response_code(404);
                echo "File not found!";
                exit;
            }
        }

        // Match the URI against the routes
        $routeMatch = $this->matchRoute($uri, $method);

        if ($routeMatch) {
            list($handler, $params) = $routeMatch;
            $controller = new $handler['controller']();
            call_user_func_array([$controller, $handler['action']], $params);
        } else {
            http_response_code(404);
            // view('404',compact('user'));
            redirect(url("404"));
            // echo url("404");
            exit;
        }
    }
}
