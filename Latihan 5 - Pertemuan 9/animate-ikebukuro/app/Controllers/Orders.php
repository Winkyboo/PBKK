<?php

namespace App\Controllers;

use App\Models\OrderModel;

class Orders extends BaseController
{
    public function mine()
    {
        $orders = model(OrderModel::class)->where('user_id', session()->get('user')['id'])->orderBy('id', 'DESC')->findAll();
        $items = [];
        if ($orders) $items = db_connect()->table('order_items')->whereIn('order_id', array_column($orders, 'id'))->orderBy('id')->get()->getResultArray();
        $grouped = [];
        foreach ($items as $item) $grouped[$item['order_id']][] = $item;
        return view('orders/mine', ['title' => 'Pesanan saya', 'orders' => $orders, 'itemsByOrder' => $grouped]);
    }
}
