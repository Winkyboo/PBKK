<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $recent = $db->table('orders o')->select('o.*,u.name AS customer_name')->join('users u', 'u.id=o.user_id')->orderBy('o.id', 'DESC')->limit(8)->get()->getResultArray();
        return view('admin/dashboard', [
            'title' => 'Dashboard toko',
            'products' => $db->table('products')->countAllResults(),
            'orders' => $db->table('orders')->countAllResults(),
            'pending' => $db->table('orders')->where('status', 'Menunggu')->countAllResults(),
            'customers' => $db->table('users')->where('role', 'customer')->countAllResults(),
            'recent' => $recent,
        ]);
    }
}
