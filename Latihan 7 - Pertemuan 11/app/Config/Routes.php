<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);

$routes->get('/', 'Home::index', ['as' => 'home']);
$routes->get('about', 'AboutController::index', ['as' => 'about']);
$routes->get('contact', 'ContactController::index', ['as' => 'contact']);
$routes->post('contact/send', 'ContactController::send', ['as' => 'contact.send']);

$routes->group('', ['filter' => 'guest'], static function ($routes): void {
    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::attemptRegister', ['as' => 'register.attempt']);
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin', ['as' => 'login.attempt']);
});

$routes->post('logout', 'AuthController::logout', ['as' => 'logout', 'filter' => 'auth']);
$routes->get('akun', 'AccountController::index', ['as' => 'account', 'filter' => 'auth']);
$routes->get('admin/dashboard', 'DashboardController::admin', [
    'as'     => 'admin.dashboard',
    'filter' => 'auth:admin',
]);
$routes->get('operator/dashboard', 'DashboardController::operator', [
    'as'     => 'operator.dashboard',
    'filter' => 'auth:operator',
]);
