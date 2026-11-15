<?php

namespace App\Controllers;

class AccountController extends BaseController
{
    public function index(): string
    {
        return view('auth/account', [
            'title'  => 'Akun saya',
            'active' => 'account',
            'user'   => [
                'name'  => session()->get('user_name'),
                'email' => session()->get('user_email'),
                'role'  => session()->get('role'),
            ],
        ]);
    }
}
