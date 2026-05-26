<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function loginForm()
    {
        if (session('user_un_id')) {
            return redirect()->to('admin/dashboard');
        }
        return view('auth/login');
    }

    public function doLogin()
    {
        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');
        if ($email === '' || $password === '') {
            return redirect()->back()->with('error', 'Email and password are required.');
        }
        try {
            service('auth')->loginWebSession($email, $password);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->to('admin/dashboard');
    }

    public function logout()
    {
        service('session')->destroy();
        return redirect()->to('login')->with('success', 'You have been logged out.');
    }
}
