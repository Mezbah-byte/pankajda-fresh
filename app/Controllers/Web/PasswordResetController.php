<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use Config\Database;

class PasswordResetController extends BaseController
{
    public function forgot()
    {
        return view('auth/forgot_password', ['title' => 'Forgot Password']);
    }

    public function sendLink()
    {
        if (! $this->validate(['email' => 'required|valid_email'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $email = strtolower(trim($this->request->getPost('email')));
        $db    = Database::connect();
        $user  = $db->table('users')->where('email', $email)->where('deleted_at', null)->get()->getRowArray();

        // Always show same message — prevent user enumeration
        if ($user) {
            $token     = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expires   = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $db->table('password_resets')->insert([
                'email'      => $email,
                'token'      => $tokenHash,
                'expires_at' => $expires,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // TODO: Send email — for now log reset URL
            $resetUrl = site_url('reset-password?token=' . $token . '&email=' . urlencode($email));
            log_message('info', 'Password reset link: ' . $resetUrl);

            // In production: send via email service. Uncomment below:
            // \Config\Services::email()->setTo($email)->setSubject('Password Reset')
            //   ->setMessage('<a href="' . $resetUrl . '">Reset Password</a>')->send();
        }

        return redirect()->to('forgot-password')->with('success', 'If that email exists, a reset link has been sent.');
    }

    public function reset()
    {
        $token = $this->request->getGet('token');
        $email = $this->request->getGet('email');
        if (! $token || ! $email) return redirect()->to('forgot-password')->with('error', 'Invalid reset link.');

        return view('auth/reset_password', [
            'title' => 'Reset Password',
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function doReset()
    {
        if (! $this->validate([
            'email'                 => 'required|valid_email',
            'token'                 => 'required',
            'password'              => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email     = strtolower(trim($this->request->getPost('email')));
        $token     = $this->request->getPost('token');
        $tokenHash = hash('sha256', $token);
        $db        = Database::connect();

        $record = $db->table('password_resets')
            ->where('email', $email)
            ->where('token', $tokenHash)
            ->where('used_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get()->getRowArray();

        if (! $record) {
            return redirect()->to('forgot-password')->with('error', 'Reset link is invalid or expired.');
        }

        $user = $db->table('users')->where('email', $email)->where('deleted_at', null)->get()->getRowArray();
        if (! $user) {
            return redirect()->to('forgot-password')->with('error', 'Account not found.');
        }

        $db->table('users')->where('id', $user['id'])->update([
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        $db->table('password_resets')->where('id', $record['id'])->update(['used_at' => date('Y-m-d H:i:s')]);

        return redirect()->to('login')->with('success', 'Password reset successful. Please login.');
    }
}
