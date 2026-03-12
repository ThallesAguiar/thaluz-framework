<?php

namespace Core;

class Router
{
    protected static $routes = [];

    public static function get($path, $handler)
    {
        static::addRoute('GET', $path, $handler);
    }

    public static function post($path, $handler)
    {
        static::addRoute('POST', $path, $handler);
    }

    public static function put($path, $handler)
    {
        static::addRoute('PUT', $path, $handler);
    }

    public static function delete($path, $handler)
    {
        static::addRoute('DELETE', $path, $handler);
    }

    protected static function addRoute($method, $path, $handler)
    {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $path);
        $pattern = "#^" . $pattern . "$#";

        static::$routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public static function dispatch($requestUri, $requestMethod)
    {
        $requestUri = parse_url($requestUri, PHP_URL_PATH);

        foreach (static::$routes as $route) {
            if ($route['method'] === $requestMethod && preg_match($route['pattern'], $requestUri, $matches)) {
                array_shift($matches);
                return static::executeHandler($route['handler'], $matches);
            }
        }

        return Response::error('Route not found', 'The requested URI does not exist.', 404);
    }

    protected static function executeHandler($handler, $params = [])
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler)) {
            [$controller, $method] = explode('@', $handler);
            $controller = "App\\Controllers\\$controller";
            $instance = new $controller();
            return call_user_func_array([$instance, $method], $params);
        }
    }
}
