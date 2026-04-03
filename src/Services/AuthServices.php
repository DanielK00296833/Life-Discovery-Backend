<?php

require_once __DIR__ . '/../Repositories/UserRepository.php';
require_once __DIR__ . '/../Entities/User.php';
require_once __DIR__ . '/JwtServices.php';

class AuthService
{
    private UserRepository $userRepository;
    private JwtService $jwtService;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->jwtService = new JwtService();
    }

    public function register(string $name, string $email, string $password): array
    {
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }

        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser) {
            return ['success' => false, 'message' => 'Email already in use'];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $user = new User(null, $name, $email, $hashedPassword);

        $created = $this->userRepository->create($user);

        if (!$created) {
            return ['success' => false, 'message' => 'Failed to register user'];
        }

        return ['success' => true, 'message' => 'User registered successfully'];
    }

    public function login(string $email, string $password): array
    {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if (!password_verify($password, $user->password)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        $token = $this->jwtService->generateToken([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);

        return [
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ];
    }
}