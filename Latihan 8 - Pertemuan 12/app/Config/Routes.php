<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index', ['as' => 'home']);
$routes->get('about', 'AboutController::index', ['as' => 'about']);
$routes->get('contact', 'ContactController::index', ['as' => 'contact']);
$routes->post('contact/send', 'ContactController::send', ['as' => 'contact.send']);
$routes->get('catalog', 'ProductController::index', ['as' => 'catalog']);
$routes->get('product/(:num)', 'ProductController::show/$1', ['as' => 'product.show']);
$routes->get('product-image/(:segment)', 'ProductImageController::show/$1', ['as' => 'product.image']);
$routes->get('cart', 'CartController::index', ['as' => 'cart']);
$routes->post('cart/add', 'CartController::add', ['as' => 'cart.add']);
$routes->put('cart/(:num)', 'CartController::update/$1', ['as' => 'cart.update']);
$routes->delete('cart/(:num)', 'CartController::remove/$1', ['as' => 'cart.remove']);

$routes->group('', ['filter' => 'guest'], static function ($routes): void {
    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::attemptRegister', ['as' => 'register.attempt']);
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin', ['as' => 'login.attempt']);
});

$routes->post('logout', 'AuthController::logout', ['as' => 'logout', 'filter' => 'auth']);
$routes->get('akun', 'AccountController::index', ['as' => 'account', 'filter' => 'auth']);
$routes->get('akun/pesanan', 'CheckoutController::orders', ['as' => 'orders.index', 'filter' => 'auth:customer']);
$routes->get('akun/pesanan/(:num)', 'CheckoutController::showOrder/$1', ['as' => 'orders.show', 'filter' => 'auth:customer']);
$routes->get('checkout', 'CheckoutController::index', ['as' => 'checkout', 'filter' => 'auth:customer']);
$routes->post('checkout', 'CheckoutController::placeOrder', ['as' => 'checkout.place', 'filter' => 'auth:customer']);
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes): void {
    $routes->get('dashboard', 'DashboardController::admin', ['as' => 'admin.dashboard']);

    $routes->get('orders', 'Admin\OrderController::index', ['as' => 'admin.orders.index']);
    $routes->get('orders/(:num)', 'Admin\OrderController::show/$1', ['as' => 'admin.orders.show']);
    $routes->put('orders/(:num)', 'Admin\OrderController::update/$1', ['as' => 'admin.orders.update']);

    $routes->get('products', 'Admin\ProductController::index', ['as' => 'admin.products.index']);
    $routes->get('products/new', 'Admin\ProductController::newForm', ['as' => 'admin.products.new']);
    $routes->post('products', 'Admin\ProductController::create', ['as' => 'admin.products.create']);
    $routes->get('products/(:num)/edit', 'Admin\ProductController::edit/$1', ['as' => 'admin.products.edit']);
    $routes->put('products/(:num)', 'Admin\ProductController::update/$1', ['as' => 'admin.products.update']);
    $routes->delete('products/(:num)', 'Admin\ProductController::delete/$1', ['as' => 'admin.products.delete']);

    $routes->get('categories', 'Admin\CategoryController::index', ['as' => 'admin.categories.index']);
    $routes->get('categories/new', 'Admin\CategoryController::newForm', ['as' => 'admin.categories.new']);
    $routes->post('categories', 'Admin\CategoryController::create', ['as' => 'admin.categories.create']);
    $routes->get('categories/(:num)/edit', 'Admin\CategoryController::edit/$1', ['as' => 'admin.categories.edit']);
    $routes->put('categories/(:num)', 'Admin\CategoryController::update/$1', ['as' => 'admin.categories.update']);
    $routes->delete('categories/(:num)', 'Admin\CategoryController::delete/$1', ['as' => 'admin.categories.delete']);
});
$routes->get('operator/dashboard', 'DashboardController::operator', [
    'as'     => 'operator.dashboard',
    'filter' => 'auth:operator',
]);
