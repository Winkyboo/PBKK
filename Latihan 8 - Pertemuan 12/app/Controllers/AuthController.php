<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function register(): string
    {
        return view('auth/register', [
            'title'  => 'Buat akun ShopEase',
            'active' => 'register',
        ]);
    }

    public function attemptRegister()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'phone'            => 'permit_empty|max_length[20]',
            'password'         => 'required|min_length[8]|max_length[72]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Email ini sudah terdaftar. Silakan masuk dengan akun Anda.',
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi kata sandi belum sama.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            $oldValues = [
                'name'  => (string) $this->request->getPost('name'),
                'email' => (string) $this->request->getPost('email'),
                'phone' => (string) $this->request->getPost('phone'),
            ];

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old_values', $oldValues);
        }

        $users = new UserModel();
        $users->insert([
            'name'          => trim((string) $this->request->getPost('name')),
            'email'         => mb_strtolower(trim((string) $this->request->getPost('email'))),
            'phone'         => trim((string) $this->request->getPost('phone')) ?: null,
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            // Public registration can only create a customer account.
            'role'          => 'customer',
            'is_active'     => 1,
        ]);

        return redirect()->to(url_to('login'))
            ->with('success', 'Akun berhasil dibuat. Silakan masuk menggunakan email dan kata sandi Anda.');
    }

    public function login(): string
    {
        return view('auth/login', [
            'title'  => 'Masuk ke ShopEase',
            'active' => 'login',
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email|max_length[150]',
            'password' => 'required|max_length[72]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('old_email', (string) $this->request->getPost('email'));
        }

        $email    = mb_strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');
        $user     = (new UserModel())->findActiveByEmail($email);

        if ($user === null || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()
                ->with('error', 'Email atau kata sandi tidak cocok.')
                ->with('old_email', $email);
        }

        session()->regenerate(true);
        session()->set([
            'user_id'    => (int) $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'role'       => $user['role'],
            'logged_in'  => true,
        ]);

        $destination = match ($user['role']) {
            'admin'    => url_to('admin.dashboard'),
            'operator' => url_to('operator.dashboard'),
            default    => url_to('account'),
        };

        return redirect()->to($destination)
            ->with('success', 'Selamat datang, ' . $user['name'] . '.');
    }

    public function logout()
    {
        $session = session();
        $session->remove([
            'user_id',
            'user_name',
            'user_email',
            'role',
            'logged_in',
        ]);
        $session->regenerate(true);

        return redirect()->to(url_to('home'))
            ->with('success', 'Anda sudah keluar dari akun.');
    }
}
