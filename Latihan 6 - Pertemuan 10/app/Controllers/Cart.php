<?php

namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class Cart extends BaseController
{
    public function index()
    {
        return view('cart/index', ['title' => 'Keranjang belanja', 'items' => $this->items()]);
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity = max(1, (int) $this->request->getPost('quantity'));
        $product = model(ProductModel::class)->find($productId);
        if (! $product || (int) $product['stock'] < 1) return redirect()->back()->with('error', 'Produk sedang tidak tersedia.');
        $cart = session()->get('cart') ?? [];
        $cart[$productId] = min((int) $product['stock'], ($cart[$productId] ?? 0) + $quantity);
        session()->set('cart', $cart);
        return redirect()->to(site_url('keranjang'))->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update()
    {
        $cart = session()->get('cart') ?? [];
        foreach (($this->request->getPost('quantity') ?? []) as $id => $quantity) {
            $id = (int) $id;
            $product = model(ProductModel::class)->find($id);
            if (! $product || (int) $quantity < 1) { unset($cart[$id]); continue; }
            $cart[$id] = min((int) $product['stock'], (int) $quantity);
        }
        session()->set('cart', $cart);
        return redirect()->to(site_url('keranjang'))->with('success', 'Keranjang diperbarui.');
    }

    public function remove(int $id)
    {
        $cart = session()->get('cart') ?? [];
        unset($cart[$id]);
        session()->set('cart', $cart);
        return redirect()->to(site_url('keranjang'))->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout()
    {
        $items = $this->items();
        if (! $items) return redirect()->to(site_url('keranjang'))->with('error', 'Keranjang Anda masih kosong.');
        return view('cart/checkout', ['title' => 'Checkout', 'items' => $items]);
    }

    public function placeOrder()
    {
        if (! $this->validate(['shipping_name' => 'required|max_length[120]', 'shipping_phone' => 'required|max_length[30]', 'shipping_address' => 'required|max_length[500]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $items = $this->items();
        if (! $items) return redirect()->to(site_url('keranjang'))->with('error', 'Keranjang Anda masih kosong.');
        $db = db_connect();
        $db->transBegin();
        $total = 0;
        foreach ($items as $item) {
            if ((int) $item['stock'] < (int) $item['quantity']) { $db->transRollback(); return redirect()->to(site_url('keranjang'))->with('error', 'Stok ' . $item['name'] . ' tidak mencukupi.'); }
            $total += (float) $item['price'] * (int) $item['quantity'];
        }
        $orderNumber = 'IKB' . date('ymd') . strtoupper(bin2hex(random_bytes(3)));
        $orderId = model(OrderModel::class)->insert([
            'order_number' => $orderNumber, 'user_id' => session()->get('user')['id'], 'status' => 'Menunggu', 'total_amount' => $total,
            'shipping_name' => trim((string) $this->request->getPost('shipping_name')),
            'shipping_phone' => trim((string) $this->request->getPost('shipping_phone')),
            'shipping_address' => trim((string) $this->request->getPost('shipping_address')),
        ]);
        foreach ($items as $item) {
            $db->table('products')->set('stock', 'stock - ' . (int) $item['quantity'], false)->where('id', (int) $item['id'])->where('stock >=', (int) $item['quantity'])->update();
            if ($db->affectedRows() < 1) { $db->transRollback(); return redirect()->to(site_url('keranjang'))->with('error', 'Stok berubah saat checkout. Periksa keranjang dan coba lagi.'); }
            model(OrderItemModel::class)->insert(['order_id' => $orderId, 'product_id' => $item['id'], 'product_name' => $item['name'], 'quantity' => $item['quantity'], 'unit_price' => $item['price']]);
        }
        if ($db->transStatus() === false) { $db->transRollback(); return redirect()->back()->with('error', 'Pesanan belum dapat disimpan.'); }
        $db->transCommit();
        session()->remove('cart');
        return redirect()->to(site_url('pelanggan/pesanan'))->with('success', 'Pesanan ' . $orderNumber . ' berhasil dibuat.');
    }

    private function items(): array
    {
        $cart = session()->get('cart') ?? [];
        if (! $cart) return [];
        $products = model(ProductModel::class)->whereIn('id', array_map('intval', array_keys($cart)))->findAll();
        foreach ($products as &$product) { $product['quantity'] = (int) ($cart[$product['id']] ?? 0); $product['subtotal'] = $product['price'] * $product['quantity']; }
        unset($product);
        return $products;
    }
}
