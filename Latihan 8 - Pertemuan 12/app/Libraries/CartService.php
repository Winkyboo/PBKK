<?php

namespace App\Libraries;

use App\Models\ProductModel;

class CartService
{
    public const SHIPPING_RATES = [
        'regular' => 15000,
        'express' => 30000,
    ];

    public function items(): array
    {
        $cart = session()->get('cart');
        if (! is_array($cart) || $cart === []) {
            return [];
        }

        $normalized = [];
        foreach ($cart as $id => $quantity) {
            $id       = (int) $id;
            $quantity = (int) $quantity;
            if ($id > 0 && $quantity > 0) {
                $normalized[$id] = $quantity;
            }
        }

        if ($normalized === []) {
            session()->remove('cart');

            return [];
        }

        $products = (new ProductModel())->withCategory()
            ->whereIn('products.id', array_keys($normalized))
            ->findAll();

        $items = [];
        foreach ($products as $product) {
            $quantity             = $normalized[(int) $product['id']];
            $product['quantity']  = $quantity;
            $product['line_total'] = (float) $product['price'] * $quantity;
            $product['available'] = $product['status'] === 'active'
                && (int) $product['stock'] >= $quantity
                && (int) $product['stock'] > 0;
            $items[] = $product;
        }

        $foundIds = array_map(static fn (array $item): int => (int) $item['id'], $items);
        $normalized = array_intersect_key($normalized, array_flip($foundIds));
        if ($normalized === []) {
            session()->remove('cart');
        } else {
            session()->set('cart', $normalized);
        }

        return $items;
    }

    public function subtotal(array $items): float
    {
        return array_reduce(
            $items,
            static fn (float $total, array $item): float => $total + (float) $item['line_total'],
            0.0,
        );
    }

    public function canCheckout(array $items): bool
    {
        if ($items === []) {
            return false;
        }

        foreach ($items as $item) {
            if (! $item['available']) {
                return false;
            }
        }

        return true;
    }
}
