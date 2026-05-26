<?php

namespace App\Libraries;

use CodeIgniter\HTTP\IncomingRequest;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JWT Service.
 *
 * Handles signing, verification, and extraction of access/refresh tokens
 * for the API and admin panel.
 */
class JwtService
{
    private string $secret;
    private string $algorithm;
    private int $accessTokenTTL;
    private int $refreshTokenTTL;
    private string $issuer;

    public function __construct()
    {
        $this->secret           = (string) (env('jwt.secret') ?? 'CHANGE_ME');
        $this->algorithm        = (string) (env('jwt.algorithm') ?? 'HS256');
        $this->accessTokenTTL   = (int) (env('jwt.accessTokenTTL') ?? 3600);
        $this->refreshTokenTTL  = (int) (env('jwt.refreshTokenTTL') ?? 604800);
        $this->issuer           = (string) (env('jwt.issuer') ?? 'pankajda-erp');
    }

    public function generateAccessToken(string $userUnId, array $claims = []): string
    {
        $now = time();
        $payload = array_merge([
            'iss'  => $this->issuer,
            'aud'  => $this->issuer,
            'iat'  => $now,
            'nbf'  => $now,
            'exp'  => $now + $this->accessTokenTTL,
            'sub'  => $userUnId,
            'type' => 'access',
        ], $claims);

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function generateRefreshToken(string $userUnId): string
    {
        $now = time();
        $payload = [
            'iss'  => $this->issuer,
            'aud'  => $this->issuer,
            'iat'  => $now,
            'nbf'  => $now,
            'exp'  => $now + $this->refreshTokenTTL,
            'sub'  => $userUnId,
            'type' => 'refresh',
            'jti'  => bin2hex(random_bytes(16)),
        ];
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Decode and validate a token. Returns the payload as an array
     * or throws on failure.
     */
    public function decode(string $token): array
    {
        $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
        return (array) $decoded;
    }

    /**
     * Pull a Bearer token from the Authorization header. Returns null
     * if no header or wrong scheme.
     */
    public function extractFromRequest(IncomingRequest $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
        if ($header === '') {
            return null;
        }
        if (! preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return null;
        }
        return $matches[1];
    }

    public function getAccessTokenTTL(): int
    {
        return $this->accessTokenTTL;
    }
}
