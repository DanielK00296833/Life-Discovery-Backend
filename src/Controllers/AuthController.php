<?php

require_once __DIR__ . '/../Services/AuthServices.php';
require_once __DIR__ . '/../Helpers/Response.php';

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        $result = $this->authService->register($name, $email, $password);

        if (!$result['success']) {
            Response::json($result, 400);
        }

        Response::json($result, 201);
    }

    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        $result = $this->authService->login($email, $password);

        if (!$result['success']) {
            Response::json($result, 401);
        }

        Response::json($result, 200);
    }
}