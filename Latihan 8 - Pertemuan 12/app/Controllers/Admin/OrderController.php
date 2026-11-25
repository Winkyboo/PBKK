<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class OrderController extends BaseController
{
    private const STATUSES = ['pending', 'processing', 'shipped', 'delivered'];

    public function index(): string
    {
        return view('admin/orders/index', [
            'title'  => 'Kelola pesanan',
            'active' => 'admin-orders',
            'orders' => (new OrderModel())->forAdmin(),
        ]);
    }

    public function show(int $id): string
    {
        $order = (new OrderModel())->find($id);
        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        return view('admin/orders/show', [
            'title'    => 'Pesanan ' . $order['order_number'],
            'active'   => 'admin-orders',
            'order'    => $order,
            'items'    => (new OrderItemModel())->forOrder($id),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(int $id)
    {
        $model = new OrderModel();
        $order = $model->find($id);
        if ($order === null) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        $rules = [
            'status'         => 'required|in_list[pending,processing,shipped,delivered]',
            'payment_status' => 'required|in_list[unpaid,paid]',
            'tracking_number'=> 'permit_empty|max_length[60]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Status atau nomor resi tidak valid.');
        }

        $status = (string) $this->request->getPost('status');
        $trackingNumber = trim((string) $this->request->getPost('tracking_number'));
        if ($status === 'shipped' && $trackingNumber === '') {
            return redirect()->back()->with('error', 'Masukkan nomor resi sebelum mengubah status menjadi dikirim.');
        }

        if (! $model->update($id, [
            'status'         => $status,
            'payment_status' => (string) $this->request->getPost('payment_status'),
            'tracking_number'=> $trackingNumber !== '' ? $trackingNumber : null,
        ])) {
            return redirect()->back()->with('error', 'Perubahan status pesanan gagal disimpan.');
        }

        return redirect()->to(url_to('admin.orders.show', $id))->with('success', 'Status pesanan diperbarui.');
    }
}
