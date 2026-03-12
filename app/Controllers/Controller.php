<?php

namespace App\Controllers;

use Core\Response;

abstract class Controller
{
    protected function json($data, $status = 200)
    {
        return Response::json($data, $status);
    }
}
