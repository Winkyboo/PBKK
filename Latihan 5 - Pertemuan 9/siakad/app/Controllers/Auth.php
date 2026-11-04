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
        if (! $this->validate(['email' => 'required|valid_email', 'password' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $user = model(UserModel::class)->where('email', $email)->first();
        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau kata sandi tidak sesuai.');
        }
        session()->regenerate();
        session()->set('user', ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']]);
        return redirect()->to(site_url());
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))->with('success', 'Anda sudah keluar.');
    }
}
