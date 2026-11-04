<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Halaman publik
$routes->get('/', 'Home::index');

// Autentikasi
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('daftar', 'Auth::register');
$routes->post('daftar', 'Auth::store');
$routes->post('keluar', 'Auth::logout', ['filter' => 'auth']);

// Bukti pembayaran: hanya pemilik booking atau admin
$routes->get('bukti/(:num)', 'Files::proof/$1', ['filter' => 'auth']);

// Area pelanggan
$routes->group('pelanggan', ['namespace' => 'App\Controllers\Customer', 'filter' => 'role:pelanggan'], static function ($routes) {
    $routes->get('booking', 'Booking::index');
    $routes->get('booking/baru', 'Booking::create');
    $routes->post('booking', 'Booking::store');
    $routes->post('booking/(:num)/bukti', 'Booking::uploadProof/$1');
    $routes->post('booking/(:num)/batal', 'Booking::cancel/$1');
});

// Area admin
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'role:admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');

    $routes->get('studio', 'Studios::index');
    $routes->get('studio/baru', 'Studios::createForm');
    $routes->post('studio', 'Studios::create');
    $routes->get('studio/(:num)/ubah', 'Studios::edit/$1');
    $routes->post('studio/(:num)', 'Studios::update/$1');
    $routes->post('studio/(:num)/hapus', 'Studios::delete/$1');

    $routes->get('alat', 'Equipments::index');
    $routes->get('alat/baru', 'Equipments::createForm');
    $routes->post('alat', 'Equipments::create');
    $routes->get('alat/(:num)/ubah', 'Equipments::edit/$1');
    $routes->post('alat/(:num)', 'Equipments::update/$1');
    $routes->post('alat/(:num)/hapus', 'Equipments::delete/$1');

    $routes->get('booking', 'Bookings::index');
    $routes->post('booking/(:num)/konfirmasi', 'Bookings::confirm/$1');
    $routes->post('booking/(:num)/selesai', 'Bookings::finish/$1');
    $routes->post('booking/(:num)/batal', 'Bookings::cancel/$1');
});
