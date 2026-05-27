<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Config\Database;

class UserController extends BaseController
{
    private UserService    $service;
    private UserRepository $users;

    public function __construct()
    {
        $this->service = new UserService();
        $this->users   = new UserRepository();
    }

    public function index()
    {
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $filters = [
            'q'      => $this->request->getGet('q'),
            'role'   => $this->request->getGet('role'),
            'status' => $this->request->getGet('status'),
        ];
        $result = $this->service->list($filters, $page, 20);

        return view('admin/users/index', [
            'title'      => 'Users',
            'users'      => $result['items'],
            'pagination' => [
                'page'      => $result['page'],
                'per_page'  => $result['per_page'],
                'total'     => $result['total'],
                'last_page' => max(1, (int) ceil($result['total'] / max(1, $result['per_page']))),
            ],
            'filters'    => $filters,
        ]);
    }

    public function create()
    {
        return view('admin/users/form', [
            'title'  => 'New User',
            'user'   => null,
            'action' => site_url('admin/users'),
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();

        if (! $this->validate([
            'name'             => 'required|min_length[2]|max_length[150]',
            'email'            => 'required|valid_email|max_length[190]',
            'password'         => 'required|min_length[8]',
            'confirm_password' => 'required',
            'role'             => 'required|in_list[super_admin,admin,manager,accountant,staff]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($post['password'] !== $post['confirm_password']) {
            return redirect()->back()->withInput()->with('errors', ['confirm_password' => 'Passwords do not match.']);
        }

        if ($this->users->emailExists($post['email'])) {
            return redirect()->back()->withInput()->with('errors', ['email' => 'This email address is already in use.']);
        }

        $this->service->create($post);

        return redirect()->to('admin/users')->with('success', 'User created successfully.');
    }

    public function show(string $unId)
    {
        $user = $this->service->get($unId);
        if (! $user) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }

        // Fetch recent activity logs for this user
        $db   = Database::connect();
        $logs = $db->table('activity_logs')
            ->where('user_un_id', $unId)
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('admin/users/show', [
            'title' => esc($user['name']),
            'user'  => $user,
            'logs'  => $logs,
        ]);
    }

    public function edit(string $unId)
    {
        $user = $this->service->get($unId);
        if (! $user) {
            return redirect()->to('admin/users')->with('error', 'User not found.');
        }

        return view('admin/users/form', [
            'title'  => 'Edit User',
            'user'   => $user,
            'action' => site_url('admin/users/' . $unId),
        ]);
    }

    public function update(string $unId)
    {
        $post = $this->request->getPost();

        if (! $this->validate([
            'name'   => 'required|min_length[2]|max_length[150]',
            'email'  => 'required|valid_email|max_length[190]',
            'role'   => 'required|in_list[super_admin,admin,manager,accountant,staff]',
            'status' => 'required|in_list[active,inactive]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->users->emailExists($post['email'], $unId)) {
            return redirect()->back()->withInput()->with('errors', ['email' => 'This email address is already in use by another user.']);
        }

        try {
            $this->service->update($unId, $post);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/users')->with('error', $e->getMessage());
        }

        return redirect()->to('admin/users/' . $unId)->with('success', 'User updated successfully.');
    }

    public function delete(string $unId)
    {
        // Prevent deleting own account
        $sessionUnId = session('user_un_id');
        if ($sessionUnId && $sessionUnId === $unId) {
            return redirect()->to('admin/users')->with('error', 'You cannot delete your own account.');
        }

        try {
            $this->service->delete($unId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/users')->with('error', $e->getMessage());
        }

        return redirect()->to('admin/users')->with('success', 'User deleted.');
    }

    /**
     * POST /admin/users/{un_id}/password
     */
    public function changePassword(string $unId)
    {
        $post = $this->request->getPost();

        if (! $this->validate([
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($post['new_password'] !== $post['confirm_password']) {
            return redirect()->back()->withInput()->with('errors', ['confirm_password' => 'Passwords do not match.']);
        }

        try {
            $this->service->changePassword($unId, $post['new_password']);
        } catch (\InvalidArgumentException $e) {
            return redirect()->to('admin/users')->with('error', $e->getMessage());
        }

        return redirect()->to('admin/users/' . $unId)->with('success', 'Password changed successfully.');
    }
}
