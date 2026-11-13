<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('user')) return redirect()->to(site_url());
        return view('auth/login', ['title' => 'Masuk']);
    }
    public function attempt()
    {
        if (! $this->validate(['email' => 'required|valid_email', 'password' => 'required'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $user = model(UserModel::class)->where('email', strtolower(trim((string) $this->request->getPost('email'))))->first();
        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) return redirect()->back()->withInput()->with('error', 'Email atau kata sandi tidak sesuai.');
        session()->regenerate();
        session()->set('user', ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]);
        return redirect()->to(site_url());
    }
    public function register()
    {
        if (session()->get('user')) return redirect()->to(site_url());
        return view('auth/register', ['title' => 'Buat akun']);
    }
    public function store()
    {
        $data = ['name' => trim((string) $this->request->getPost('name')), 'email' => strtolower(trim((string) $this->request->getPost('email'))), 'password' => (string) $this->request->getPost('password'), 'confirmation' => (string) $this->request->getPost('confirmation')];
        if (! $this->validateData($data, ['name' => 'required|min_length[2]|max_length[120]', 'email' => 'required|valid_email|max_length[150]|is_unique[users.email]', 'password' => 'required|min_length[8]|max_length[72]', 'confirmation' => 'required|matches[password]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        model(UserModel::class)->insert(['name' => $data['name'], 'email' => $data['email'], 'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT), 'role' => 'customer']);
        return redirect()->to(site_url('login'))->with('success', 'Akun berhasil dibuat. Silakan masuk.');
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url())->with('success', 'Anda telah keluar.');
    }
}
