<?php

namespace Core;

class Request
{
    private static array $attributes = [];

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function uri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function all()
    {
        return json_decode(file_get_contents('php://input'), true) ?? $_POST;
    }

    public static function header(string $key, $default = null)
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        }

        if (empty($headers)) {
            foreach ($_SERVER as $name => $value) {
                if (str_starts_with($name, 'HTTP_')) {
                    $normalized = str_replace('_', '-', strtolower(substr($name, 5)));
                    $headers[$normalized] = $value;
                }
            }
        }

        foreach ($headers as $name => $value) {
            if (strtolower($name) === strtolower($key)) {
                return $value;
            }
        }

        return $default;
    }

    public static function bearerToken(): ?string
    {
        $authorization = static::header('Authorization');

        if (!$authorization) {
            return null;
        }

        if (preg_match('/Bearer\s+(.+)/i', $authorization, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    public static function set(string $key, $value): void
    {
        static::$attributes[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return static::$attributes[$key] ?? $default;
    }

    public static function setUser(array $user): void
    {
        static::set('user', $user);
    }

    public static function user(): ?array
    {
        return static::get('user');
    }
}
