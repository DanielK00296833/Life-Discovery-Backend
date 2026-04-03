<?php

require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/Helpers/Response.php';

$user = AuthMiddleware::authenticate();

Response::json([
    'success' => true,
    'message' => 'Authenticated user',
    'user' => $user
]);