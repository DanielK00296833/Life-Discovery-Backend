<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secret;
    private string $issuer;
    private string $audience;
    private int $expiration;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/jwt.php';

        $this->secret = $config['secret'];
        $this->issuer = $config['issuer'];
        $this->audience = $config['audience'];
        $this->expiration = $config['expiration'];
    }

    public function generateToken(array $user): string
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + $this->expiration;

        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'sub' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name']
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function validateToken(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, 'HS256'));
    }
}