<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function admin(): string
    {
        return view('auth/role_dashboard', [
            'title'       => 'Dashboard Admin',
            'active'      => 'dashboard',
            'roleTitle'   => 'Admin ShopEase',
            'description' => 'Akun admin dapat membuka area pengelolaan ShopEase.',
        ]);
    }

    public function operator(): string
    {
        return view('auth/role_dashboard', [
            'title'       => 'Dashboard Operator',
            'active'      => 'dashboard',
            'roleTitle'   => 'Operator ShopEase',
            'description' => 'Akun operator dapat membuka area pemrosesan pesanan.',
        ]);
    }
}
