<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function login()
    {
        if (session('user')) {
            return $this->home();
        }

        return view('auth/login');
    }

    public function attempt(): RedirectResponse
    {
        // Pembatasan percobaan login: maksimal 5 kali per menit per alamat IP.
        if (! service('throttler')->check('login-' . md5($this->request->getIPAddress()), 5, MINUTE)) {
            return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan. Coba lagi dalam satu menit.');
        }

        $data = [
            'email'    => strtolower(trim((string) $this->request->getPost('email'))),
            'password' => (string) $this->request->getPost('password'),
        ];
        $rules = [
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email'],
            'password' => ['label' => 'Kata sandi', 'rules' => 'required'],
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = model(UserModel::class)->where('email', $data['email'])->first();
        if (! $user || ! password_verify($data['password'], $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau kata sandi salah.');
        }

        session()->regenerate();
        session()->set('user', ['id' => (int) $user['id'], 'name' => $user['name'], 'role' => $user['role']]);

        return $this->home();
    }

    public function register()
    {
        if (session('user')) {
            return $this->home();
        }

        return view('auth/register');
    }

    public function store(): RedirectResponse
    {
        $data = [
            'name'     => trim((string) $this->request->getPost('name')),
            'email'    => strtolower(trim((string) $this->request->getPost('email'))),
            'password' => (string) $this->request->getPost('password'),
            'passconf' => (string) $this->request->getPost('passconf'),
        ];
        $rules = [
            'name'     => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[100]|is_unique[users.email]'],
            'password' => ['label' => 'Kata sandi', 'rules' => 'required|min_length[8]|max_length[72]'],
            'passconf' => ['label' => 'Ulangi kata sandi', 'rules' => 'required|matches[password]'],
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        model(UserModel::class)->insert([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'          => 'pelanggan',
        ]);

        return redirect()->to('/login')->with('success', 'Akun berhasil dibuat. Silakan masuk.');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to('/')->with('success', 'Anda sudah keluar.');
    }

    private function home(): RedirectResponse
    {
        return redirect()->to(has_role('admin') ? '/admin' : '/pelanggan/booking');
    }
}
