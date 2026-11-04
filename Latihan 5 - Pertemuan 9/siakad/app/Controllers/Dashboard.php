<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $user = session()->get('user');
        if (! $user) return redirect()->to(site_url('login'));
        return match ($user['role']) {
            'admin' => redirect()->to(site_url('admin')),
            'dosen' => redirect()->to(site_url('dosen')),
            default => redirect()->to(site_url('mahasiswa')),
        };
    }
}
