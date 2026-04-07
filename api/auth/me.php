<?php

require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/Helpers/Response.php';

$payload = AuthMiddleware::authenticate();

Response::json([
    'success' => true,
    'user' => [
        'id' => $payload->sub,
        'email' => $payload->email,
        'name' => $payload->name
    ]
]);