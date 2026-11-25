<?php

namespace App\Controllers;

use App\Libraries\CartService;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class CheckoutController extends BaseController
{
    public function index(): string
    {
        $cartService = new CartService();
        $items       = $cartService->items();
        if ($items === []) {
            return redirect()->to(url_to('cart'))->with('error', 'Keranjang masih kosong.');
        }

        if (! $cartService->canCheckout($items)) {
            return redirect()->to(url_to('cart'))->with('error', 'Periksa kembali produk dan jumlah stok di keranjang.');
        }

        return view('frontend/checkout', [
            'title'        => 'Checkout',
            'active'       => 'cart',
            'items'        => $items,
            'subtotal'     => $cartService->subtotal($items),
            'shippingRates'=> CartService::SHIPPING_RATES,
            'errors'       => session()->getFlashdata('errors') ?? [],
            'values'       => session()->getFlashdata('old_values') ?? [],
        ]);
    }

    public function placeOrder()
    {
        $rules = [
            'recipient_name'  => 'required|min_length[3]|max_length[100]',
            'phone'           => 'required|min_length[8]|max_length[20]',
            'shipping_address'=> 'required|min_length[10]|max_length[500]',
            'city'            => 'required|min_length[2]|max_length[100]',
            'postal_code'     => 'required|alpha_numeric|min_length[4]|max_length[10]',
            'shipping_method' => 'required|in_list[regular,express]',
            'payment_method'  => 'required|in_list[cod]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(url_to('checkout'))
                ->with('errors', $this->validator->getErrors())
                ->with('old_values', $this->request->getPost());
        }

        $cartService = new CartService();
        $items       = $cartService->items();
        if (! $cartService->canCheckout($items)) {
            return redirect()->to(url_to('cart'))->with('error', 'Stok berubah. Sesuaikan kembali jumlah produk sebelum checkout.');
        }

        $shippingMethod = (string) $this->request->getPost('shipping_method');
        $shippingCost   = CartService::SHIPPING_RATES[$shippingMethod];
        $subtotal       = $cartService->subtotal($items);
        $db             = db_connect();
        $db->transBegin();

        try {
            $orders = new OrderModel();
            $orderId = $orders->insert([
                'order_number'    => 'SE-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
                'user_id'         => (int) session()->get('user_id'),
                'customer_name'   => trim((string) $this->request->getPost('recipient_name')),
                'customer_email'  => (string) session()->get('user_email'),
                'phone'           => trim((string) $this->request->getPost('phone')),
                'shipping_address'=> trim((string) $this->request->getPost('shipping_address')),
                'city'            => trim((string) $this->request->getPost('city')),
                'postal_code'     => strtoupper(trim((string) $this->request->getPost('postal_code'))),
                'shipping_method' => $shippingMethod,
                'shipping_cost'   => $shippingCost,
                'subtotal'        => $subtotal,
                'total'           => $subtotal + $shippingCost,
                'payment_method'  => (string) $this->request->getPost('payment_method'),
                'payment_status'  => 'unpaid',
                'status'          => 'pending',
                'tracking_number' => null,
            ], true);

            if (! $orderId) {
                throw new RuntimeException('Pesanan gagal disimpan.');
            }

            $orderItems = new OrderItemModel();
            foreach ($items as $item) {
                $quantity = (int) $item['quantity'];
                $updated  = $db->table('products')
                    ->set('stock', 'stock - ' . $quantity, false)
                    ->where('id', (int) $item['id'])
                    ->where('status', 'active')
                    ->where('stock >=', $quantity)
                    ->update();

                if (! $updated || $db->affectedRows() !== 1) {
                    throw new RuntimeException('Stok produk berubah saat checkout.');
                }

                if (! $orderItems->insert([
                    'order_id'     => (int) $orderId,
                    'product_id'   => (int) $item['id'],
                    'product_name' => (string) $item['name'],
                    'product_image'=> (string) $item['image'],
                    'unit_price'   => (float) $item['price'],
                    'quantity'     => $quantity,
                    'line_total'   => (float) $item['price'] * $quantity,
                ])) {
                    throw new RuntimeException('Rincian produk gagal disimpan.');
                }
            }

            if ($db->transStatus() === false) {
                throw new RuntimeException('Transaksi pesanan tidak berhasil.');
            }

            $db->transCommit();
            session()->remove('cart');

            return redirect()->to(url_to('orders.show', $orderId))
                ->with('success', 'Pesanan berhasil dibuat. Simpan nomor pesanan untuk memeriksa status pengiriman.');
        } catch (Throwable $exception) {
            $db->transRollback();

            log_message('error', '[ShopEase checkout] ' . $exception->getMessage());

            return redirect()->to(url_to('checkout'))
                ->with('error', 'Checkout belum berhasil. Periksa kembali stok produk dan coba lagi.');
        }
    }

    public function orders(): string
    {
        return view('frontend/orders', [
            'title'  => 'Pesanan saya',
            'active' => 'orders',
            'orders' => (new OrderModel())->forCustomer((int) session()->get('user_id')),
        ]);
    }

    public function showOrder(int $id): string
    {
        $order = (new OrderModel())->find($id);
        if ($order === null || (int) $order['user_id'] !== (int) session()->get('user_id')) {
            throw PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan.');
        }

        return view('frontend/order_detail', [
            'title' => 'Pesanan ' . $order['order_number'],
            'active'=> 'orders',
            'order' => $order,
            'items' => (new OrderItemModel())->forOrder($id),
        ]);
    }
}
