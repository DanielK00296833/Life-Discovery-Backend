<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Entities/User.php';

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new User(
            (int)$data['id'],
            $data['name'],
            $data['email'],
            $data['password']
        );
    }

    public function create(User $user): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :password)
        ");

        return $stmt->execute([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password
        ]);
    }
}