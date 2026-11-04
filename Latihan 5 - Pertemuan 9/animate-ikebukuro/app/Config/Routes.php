<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Shop::index');
$routes->get('produk/(:num)', 'Shop::show/$1');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('daftar', 'Auth::register');
$routes->post('daftar', 'Auth::store');
$routes->post('logout', 'Auth::logout');
$routes->get('keranjang', 'Cart::index');
$routes->post('keranjang/tambah', 'Cart::add');
$routes->post('keranjang/ubah', 'Cart::update');
$routes->post('keranjang/(:num)/hapus', 'Cart::remove/$1');

$routes->group('pelanggan', ['filter' => 'role:customer'], static function ($routes) {
    $routes->get('checkout', 'Cart::checkout');
    $routes->post('checkout', 'Cart::placeOrder');
    $routes->get('pesanan', 'Orders::mine');
});

$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('produk', 'Admin\Products::index');
    $routes->get('produk/baru', 'Admin\Products::new');
    $routes->post('produk', 'Admin\Products::create');
    $routes->get('produk/(:num)/ubah', 'Admin\Products::edit/$1');
    $routes->post('produk/(:num)', 'Admin\Products::update/$1');
    $routes->post('produk/(:num)/hapus', 'Admin\Products::delete/$1');
    $routes->get('kategori', 'Admin\Categories::index');
    $routes->post('kategori', 'Admin\Categories::create');
    $routes->post('kategori/(:num)/hapus', 'Admin\Categories::delete/$1');
    $routes->get('pesanan', 'Admin\Orders::index');
    $routes->post('pesanan/(:num)/status', 'Admin\Orders::updateStatus/$1');
});
