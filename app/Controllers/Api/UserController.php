<?php

namespace App\Controllers\Api;

use App\Repositories\UserRepository;
use App\Services\UserService;

class UserController extends BaseApiController
{
    private UserService    $service;
    private UserRepository $users;

    public function __construct()
    {
        $this->service = new UserService();
        $this->users   = new UserRepository();
    }

    /**
     * GET /api/v1/users
     */
    public function index()
    {
        $pg      = $this->parsePagination();
        $filters = [
            'q'      => $this->request->getGet('q'),
            'role'   => $this->request->getGet('role'),
            'status' => $this->request->getGet('status'),
        ];
        $result = $this->service->list($filters, $pg['page'], $pg['per_page']);
        return $this->paginated($result['items'], $result['page'], $result['per_page'], $result['total']);
    }

    /**
     * GET /api/v1/users/{un_id}
     */
    public function show($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        $user = $this->service->get($unId);
        if (! $user) {
            return $this->failNotFound('User not found.');
        }
        return $this->ok($user);
    }

    /**
     * POST /api/v1/users
     */
    public function create()
    {
        $body  = $this->request->getJSON(true) ?? $this->request->getPost();
        $rules = [
            'name'     => 'required|min_length[2]|max_length[150]',
            'email'    => 'required|valid_email|max_length[190]',
            'password' => 'required|min_length[8]',
            'role'     => 'required|in_list[super_admin,admin,manager,accountant,staff]',
        ];

        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }

        if ($this->users->emailExists($body['email'] ?? '')) {
            return $this->failValidation(['email' => 'This email address is already in use.']);
        }

        $user = $this->service->create($body);
        return $this->created($user, 'User created');
    }

    /**
     * PUT /api/v1/users/{un_id}
     */
    public function update($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        $body  = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $rules = [
            'name'   => 'permit_empty|min_length[2]|max_length[150]',
            'email'  => 'permit_empty|valid_email|max_length[190]',
            'role'   => 'permit_empty|in_list[super_admin,admin,manager,accountant,staff]',
            'status' => 'permit_empty|in_list[active,inactive]',
        ];

        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }

        if (! empty($body['email']) && $this->users->emailExists($body['email'], $unId)) {
            return $this->failValidation(['email' => 'This email address is already in use by another user.']);
        }

        try {
            $user = $this->service->update($unId, $body);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }

        return $this->ok($user, 'User updated');
    }

    /**
     * DELETE /api/v1/users/{un_id}
     */
    public function delete($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }

        // Prevent deleting own account
        if ($this->authUserUnId() === $unId) {
            return $this->fail('You cannot delete your own account.', 403);
        }

        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }

        return $this->ok(null, 'User deleted');
    }

    /**
     * PUT /api/v1/users/{un_id}/password
     */
    public function changePassword($unId = null)
    {
        if (! $unId) {
            return $this->failNotFound();
        }
        $body  = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $rules = [
            'new_password' => 'required|min_length[8]',
        ];

        if (! $this->validateData($body, $rules)) {
            return $this->failValidation($this->validator->getErrors());
        }

        try {
            $this->service->changePassword($unId, $body['new_password']);
        } catch (\InvalidArgumentException $e) {
            return $this->failNotFound($e->getMessage());
        }

        return $this->ok(null, 'Password changed successfully');
    }
}
