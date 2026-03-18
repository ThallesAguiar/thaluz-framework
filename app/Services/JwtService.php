<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    public static function makeAccessToken(int $userId): array
    {
        return self::makeToken($userId, 'access', (int) ($_ENV['JWT_ACCESS_TTL_MINUTES'] ?? 15) * 60);
    }

    public static function makeRefreshToken(int $userId): array
    {
        return self::makeToken($userId, 'refresh', (int) ($_ENV['JWT_REFRESH_TTL_DAYS'] ?? 30) * 86400);
    }

    public static function decodeAccessToken(string $token): array
    {
        return self::decodeByType($token, 'access');
    }

    public static function decodeRefreshToken(string $token): array
    {
        return self::decodeByType($token, 'refresh');
    }

    private static function decodeByType(string $token, string $expectedType): array
    {
        $decoded = JWT::decode($token, new Key(self::secret(), 'HS256'));
        $claims = (array) $decoded;

        if (($claims['iss'] ?? null) !== self::issuer()) {
            throw new \UnexpectedValueException('Invalid token issuer.');
        }

        if (($claims['type'] ?? null) !== $expectedType) {
            throw new \UnexpectedValueException('Invalid token type.');
        }

        return $claims;
    }

    private static function makeToken(int $userId, string $type, int $ttlSeconds): array
    {
        $now = time();
        $expiresAt = $now + $ttlSeconds;

        $payload = [
            'iss' => self::issuer(),
            'sub' => $userId,
            'jti' => bin2hex(random_bytes(12)),
            'type' => $type,
            'iat' => $now,
            'exp' => $expiresAt,
        ];

        $token = JWT::encode($payload, self::secret(), 'HS256');

        return [
            'token' => $token,
            'expires_at_unix' => $expiresAt,
            'expires_at' => date('Y-m-d H:i:s', $expiresAt),
        ];
    }

    private static function secret(): string
    {
        $secret = $_ENV['JWT_SECRET'] ?? '';

        if ($secret === '') {
            throw new \RuntimeException('JWT_SECRET nao configurado no .env.');
        }

        return $secret;
    }

    private static function issuer(): string
    {
        return $_ENV['JWT_ISSUER'] ?? ($_ENV['APP_NAME'] ?? 'thaluz');
    }
}
