<?php

namespace App\Services;

use App\Repositories\RefreshTokenRepository;
use App\Repositories\UserRepository;

/**
 * AuthService - registration, login, token issuance, current-user lookup.
 */
class AuthService extends BaseService
{
    private UserRepository $users;
    private RefreshTokenRepository $refreshTokens;

    public function __construct(?UserRepository $users = null, ?RefreshTokenRepository $refreshTokens = null)
    {
        $this->users         = $users ?? new UserRepository();
        $this->refreshTokens = $refreshTokens ?? new RefreshTokenRepository();
    }

    /**
     * Register a new user. Returns the new user array (without password_hash).
     *
     * @throws \InvalidArgumentException
     */
    public function register(array $input): array
    {
        $email = strtolower(trim($input['email'] ?? ''));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('A valid email is required.');
        }
        if ($this->users->emailExists($email)) {
            throw new \InvalidArgumentException('Email already registered.');
        }
        if (strlen($input['password'] ?? '') < 8) {
            throw new \InvalidArgumentException('Password must be at least 8 characters.');
        }

        $unId = $this->users->create([
            'name'          => trim($input['name'] ?? ''),
            'email'         => $email,
            'phone'         => $input['phone'] ?? null,
            'password_hash' => password_hash($input['password'], PASSWORD_BCRYPT),
            'role'          => $input['role'] ?? ROLE_STAFF,
            'status'        => STATUS_ACTIVE,
        ]);

        $this->audit('user.registered', 'user', $unId, ['email' => $email]);
        $user = $this->users->findByUnId($unId);
        unset($user['password_hash']);
        return $user;
    }

    /**
     * Verify credentials and return tokens + user.
     *
     * @return array{access_token:string,refresh_token:string,expires_in:int,user:array}
     * @throws \InvalidArgumentException on bad credentials
     */
    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);
        if ($user === null || ! password_verify($password, $user['password_hash'] ?? '')) {
            throw new \InvalidArgumentException('Invalid email or password.');
        }
        if (($user['status'] ?? STATUS_ACTIVE) !== STATUS_ACTIVE) {
            throw new \InvalidArgumentException('Account is not active.');
        }

        $this->users->touchLastLogin($user['un_id']);

        return $this->issueTokens($user);
    }

    /**
     * Issue a fresh pair of tokens and persist the refresh token.
     */
    public function issueTokens(array $user): array
    {
        $jwt = service('jwt');

        $access = $jwt->generateAccessToken($user['un_id'], [
            'role'  => $user['role'] ?? ROLE_STAFF,
            'email' => $user['email'],
            'name'  => $user['name'],
        ]);
        $refresh = $jwt->generateRefreshToken($user['un_id']);

        $this->refreshTokens->store($user['un_id'], $refresh, (int) (env('jwt.refreshTokenTTL') ?? 604800));

        unset($user['password_hash']);

        return [
            'access_token'  => $access,
            'refresh_token' => $refresh,
            'token_type'    => 'Bearer',
            'expires_in'    => $jwt->getAccessTokenTTL(),
            'user'          => $user,
        ];
    }

    /**
     * Exchange a refresh token for a new access/refresh pair (token rotation).
     */
    public function refresh(string $refreshToken): array
    {
        if (! $this->refreshTokens->isValid($refreshToken)) {
            throw new \InvalidArgumentException('Refresh token is invalid or revoked.');
        }
        try {
            $payload = service('jwt')->decode($refreshToken);
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException('Refresh token decode failed.');
        }
        if (($payload['type'] ?? null) !== 'refresh') {
            throw new \InvalidArgumentException('Wrong token type.');
        }
        $user = $this->users->findByUnId($payload['sub'] ?? '');
        if ($user === null) {
            throw new \InvalidArgumentException('User not found.');
        }

        // rotate
        $this->refreshTokens->revoke($refreshToken);
        return $this->issueTokens($user);
    }

    public function logout(string $refreshToken): void
    {
        $this->refreshTokens->revoke($refreshToken);
    }

    public function resolveUserByUnId(string $unId): ?array
    {
        $user = $this->users->findByUnId($unId);
        if ($user) {
            unset($user['password_hash']);
        }
        return $user;
    }

    /**
     * Web-session login. Used by the admin panel form.
     */
    public function loginWebSession(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);
        if ($user === null || ! password_verify($password, $user['password_hash'] ?? '')) {
            throw new \InvalidArgumentException('Invalid email or password.');
        }
        if (($user['status'] ?? STATUS_ACTIVE) !== STATUS_ACTIVE) {
            throw new \InvalidArgumentException('Account is not active.');
        }
        $this->users->touchLastLogin($user['un_id']);

        $session = service('session');
        $session->set([
            'user_un_id' => $user['un_id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_role'  => $user['role'],
        ]);

        unset($user['password_hash']);
        return $user;
    }
}
