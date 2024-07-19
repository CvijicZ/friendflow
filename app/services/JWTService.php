<?php

namespace App\Services;

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JWTService
{
    private $secretKey;

    public function __construct()
    {
        // $dotenv = Dotenv::createImmutable(__DIR__);
        // $dotenv->load();

        if (!isset($_ENV['JWT_SECRET_KEY'])) {
            throw new \Exception('JWT_SECRET_KEY is not set in environment variables');
        }
        $this->secretKey = $_ENV['JWT_SECRET_KEY'];
    }
        /**
     * Decode and verify a JWT token
     *
     * @param string $token
     * @return object
     * @throws \Exception
     */
    public function decode(string $token): object
    {
        try {
            // Decode the token using the secret key
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            // Handle decoding or verification errors
            throw new \Exception("Token decoding failed: " . $e->getMessage());
        }
    }

    public function generateToken($userId)
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $userId
        ];

        return JWT::encode($payload, $this->secretKey, "HS256");
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, $this->secretKey);
            return $decoded->sub;
        } catch (\Exception $e) {
            return false;
        }
    }
}
