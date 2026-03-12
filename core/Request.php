<?php

namespace Core;

class Request
{
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
}
