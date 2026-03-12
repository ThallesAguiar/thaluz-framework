<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Core\Request;

// Load routes
require_once __DIR__ . '/../routes/api.php';

// Dispatch request
Router::dispatch(Request::uri(), Request::method());
