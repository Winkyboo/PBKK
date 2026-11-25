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
            'description' => 'Admin dapat mengelola pesanan, produk, dan kategori ShopEase.',
            'adminLinks'  => true,
        ]);
    }

    public function operator(): string
    {
        return view('auth/role_dashboard', [
            'title'       => 'Dashboard Operator',
            'active'      => 'dashboard',
            'roleTitle'   => 'Operator ShopEase',
            'description' => 'Akun operator memiliki akses ke area kerja operator ShopEase.',
        ]);
    }
}
