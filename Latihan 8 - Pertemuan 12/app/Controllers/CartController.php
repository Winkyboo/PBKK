<?php

namespace App\Controllers;

use App\Libraries\CartService;
use App\Models\ProductModel;

class CartController extends BaseController
{
    public function index(): string
    {
        $cartService = new CartService();
        $items       = $cartService->items();

        return view('frontend/cart', [
            'title'      => 'Keranjang belanja',
            'active'     => 'cart',
            'items'      => $items,
            'subtotal'   => $cartService->subtotal($items),
            'canCheckout' => $cartService->canCheckout($items),
        ]);
    }

    public function add()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity  = filter_var($this->request->getPost('quantity'), FILTER_VALIDATE_INT);
        $product   = (new ProductModel())->findActiveWithCategory($productId);

        if ($product === null || $quantity === false || $quantity < 1) {
            return redirect()->back()->with('error', 'Produk atau jumlah yang dipilih tidak valid.');
        }

        if ((int) $product['stock'] < 1) {
            return redirect()->back()->with('error', 'Stok produk sedang habis.');
        }

        $cart = session()->get('cart');
        $cart = is_array($cart) ? $cart : [];
        $newQuantity = (int) ($cart[$productId] ?? 0) + $quantity;

        if ($newQuantity > (int) $product['stock']) {
            return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $cart[$productId] = $newQuantity;
        session()->set('cart', $cart);

        return redirect()->to(url_to('cart'))->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(int $productId)
    {
        $quantity = filter_var($this->request->getPost('quantity'), FILTER_VALIDATE_INT);
        $product  = (new ProductModel())->findActiveWithCategory($productId);
        $cart     = session()->get('cart');

        if (! is_array($cart) || ! array_key_exists($productId, $cart)) {
            return redirect()->to(url_to('cart'))->with('error', 'Produk tidak ditemukan di keranjang.');
        }

        if ($product === null || $quantity === false || $quantity < 1 || $quantity > (int) $product['stock']) {
            return redirect()->to(url_to('cart'))->with('error', 'Jumlah tidak valid atau melebihi stok tersedia.');
        }

        $cart[$productId] = $quantity;
        session()->set('cart', $cart);

        return redirect()->to(url_to('cart'))->with('success', 'Jumlah produk diperbarui.');
    }

    public function remove(int $productId)
    {
        $cart = session()->get('cart');
        if (is_array($cart)) {
            unset($cart[$productId]);
            if ($cart === []) {
                session()->remove('cart');
            } else {
                session()->set('cart', $cart);
            }
        }

        return redirect()->to(url_to('cart'))->with('success', 'Produk dihapus dari keranjang.');
    }
}
