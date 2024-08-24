<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private $secretKey;
    private $keyId;

    public function __construct()
    {
        $this->secretKey = $_ENV['JWT_SECRET_KEY'] ?? 'default_secret_key';
        $this->keyId = $_ENV['JWT_KEY_ID'] ?? 'default_key_id';
    }

    public function generateToken($userId)
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $userId
        ];

        return JWT::encode($payload, $this->secretKey, "HS256", $this->keyId);
    }

    public function decode(string $token): object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            throw new \Exception("Token decoding failed: " . $e->getMessage());
        }
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->sub;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUserIdFromToken($token): ?int
    {
        $userId = $this->validateToken($token);
        return $userId ? intval($userId) : null;
    }

    public function extractAndValidateToken()
    {
        $headers = apache_request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if ($authHeader) {
            $bearerToken = explode(' ', $authHeader);
            if (count($bearerToken) === 2 && $bearerToken[0] === 'Bearer') {
                $token = $bearerToken[1];
                $userId = $this->getUserIdFromToken($token);
                if ($userId) {
                    return $userId;
                }
            }
        }

        throw new \Exception('Invalid or missing token');
    }
}
