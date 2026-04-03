<?php

require_once __DIR__ . '/../Services/JwtServices.php';
require_once __DIR__ . '/../Helpers/Response.php';

class AuthMiddleware
{
    public static function authenticate(): object
    {
        $headers = getallheaders();

        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authHeader) {
            Response::json([
                'success' => false,
                'message' => 'Authorization header missing'
            ], 401);
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::json([
                'success' => false,
                'message' => 'Invalid authorization format'
            ], 401);
        }

        $token = $matches[1];

        try {
            $jwtService = new JwtService();
            return $jwtService->validateToken($token);
        } catch (Exception $e) {
            Response::json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }
    }
}