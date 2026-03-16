<?php

namespace Core;

class Router
{
    protected static $routes = [];
    protected static $middlewares = [];
    protected static $groupStack = [];

    public static function get($path, $handler, array $middlewares = [])
    {
        static::addRoute('GET', $path, $handler, $middlewares);
    }

    public static function post($path, $handler, array $middlewares = [])
    {
        static::addRoute('POST', $path, $handler, $middlewares);
    }

    public static function put($path, $handler, array $middlewares = [])
    {
        static::addRoute('PUT', $path, $handler, $middlewares);
    }

    public static function delete($path, $handler, array $middlewares = [])
    {
        static::addRoute('DELETE', $path, $handler, $middlewares);
    }

    public static function middleware(string $alias, string $class): void
    {
        static::$middlewares[$alias] = $class;
    }

    public static function group(array $attributes, callable $callback): void
    {
        static::$groupStack[] = $attributes;
        $callback();
        array_pop(static::$groupStack);
    }

    protected static function addRoute($method, $path, $handler, array $middlewares = [])
    {
        $middlewares = array_values(array_unique(array_merge(static::getGroupMiddlewares(), $middlewares)));
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $path);
        $pattern = "#^" . $pattern . "$#";

        static::$routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public static function dispatch($requestUri, $requestMethod)
    {
        $requestUri = parse_url($requestUri, PHP_URL_PATH);

        foreach (static::$routes as $route) {
            if ($route['method'] === $requestMethod && preg_match($route['pattern'], $requestUri, $matches)) {
                array_shift($matches);
                static::runMiddlewares($route['middlewares'], $matches);
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

    protected static function runMiddlewares(array $middlewares, array $params = []): void
    {
        foreach ($middlewares as $middleware) {
            $class = static::$middlewares[$middleware] ?? $middleware;

            if (!class_exists($class)) {
                Response::error('Middleware Not Found', "Middleware [$middleware] nao foi encontrado.", 500);
            }

            $instance = new $class();

            if (!method_exists($instance, 'handle')) {
                Response::error('Middleware Invalid', "Middleware [$middleware] nao possui metodo handle.", 500);
            }

            $instance->handle($params);
        }
    }

    protected static function getGroupMiddlewares(): array
    {
        $middlewares = [];

        foreach (static::$groupStack as $group) {
            if (!empty($group['middleware']) && is_array($group['middleware'])) {
                $middlewares = array_merge($middlewares, $group['middleware']);
            }
        }

        return $middlewares;
    }
}
