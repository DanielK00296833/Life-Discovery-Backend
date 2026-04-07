<?php

require_once __DIR__ . '/../../src/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/Controllers/FavouriteController.php';

$payload = AuthMiddleware::authenticate();

$controller = new FavouriteController();
$controller->list($payload->sub);