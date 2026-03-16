<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app.php';

use App\Middleware\AuthMiddleware;
use Core\Request;
use Core\Router;

// Registra middlewares globais/aliases
Router::middleware('auth', AuthMiddleware::class);

// Carrega rotas
require_once __DIR__ . '/../routes/api.php';

// Despacha request
Router::dispatch(Request::uri(), Request::method());
