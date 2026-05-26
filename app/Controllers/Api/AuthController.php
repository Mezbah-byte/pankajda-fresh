<?php

namespace App\Controllers\Api;

class AuthController extends BaseApiController
{
    public function login()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $email = (string) ($body['email'] ?? '');
        $password = (string) ($body['password'] ?? '');
        if ($email === '' || $password === '') {
            return $this->failValidation([
                'email'    => 'Email is required.',
                'password' => 'Password is required.',
            ]);
        }

        try {
            $tokens = service('auth')->login($email, $password);
        } catch (\InvalidArgumentException $e) {
            return $this->failUnauthorized($e->getMessage());
        }
        return $this->ok($tokens, 'Login successful');
    }

    public function register()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'name'     => 'required|min_length[2]|max_length[150]',
            'email'    => 'required|valid_email|max_length[190]',
            'password' => 'required|min_length[8]',
        ];
        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }
        try {
            $user = service('auth')->register($body);
        } catch (\InvalidArgumentException $e) {
            return $this->failValidation(['email' => $e->getMessage()]);
        }
        return $this->created($user, 'Account created');
    }

    public function refresh()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $token = (string) ($body['refresh_token'] ?? '');
        if ($token === '') {
            return $this->failValidation(['refresh_token' => 'Refresh token is required.']);
        }
        try {
            $tokens = service('auth')->refresh($token);
        } catch (\InvalidArgumentException $e) {
            return $this->failUnauthorized($e->getMessage());
        }
        return $this->ok($tokens, 'Token refreshed');
    }

    public function logout()
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $token = (string) ($body['refresh_token'] ?? '');
        if ($token !== '') {
            service('auth')->logout($token);
        }
        return $this->ok(null, 'Logged out');
    }

    public function me()
    {
        $user = $this->getAuthUser();
        if ($user === null) {
            return $this->failUnauthorized();
        }
        return $this->ok($user);
    }
}
