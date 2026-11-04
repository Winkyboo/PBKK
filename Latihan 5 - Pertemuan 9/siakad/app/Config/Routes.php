<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Dashboard::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->post('logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('data/(:segment)', 'Admin\MasterData::index/$1');
    $routes->get('data/(:segment)/baru', 'Admin\MasterData::form/$1');
    $routes->post('data/(:segment)', 'Admin\MasterData::save/$1');
    $routes->get('data/(:segment)/(:num)/ubah', 'Admin\MasterData::form/$1/$2');
    $routes->post('data/(:segment)/(:num)', 'Admin\MasterData::save/$1/$2');
    $routes->post('data/(:segment)/(:num)/hapus', 'Admin\MasterData::delete/$1/$2');
});

$routes->group('dosen', ['filter' => 'role:dosen'], static function ($routes) {
    $routes->get('/', 'Dosen::dashboard');
    $routes->get('kelas', 'Dosen::classes');
    $routes->get('kelas/(:num)', 'Dosen::classDetail/$1');
    $routes->post('kelas/(:num)/nilai', 'Dosen::saveGrades/$1');
    $routes->get('perwalian', 'Dosen::advisees');
});

$routes->group('mahasiswa', ['filter' => 'role:mahasiswa'], static function ($routes) {
    $routes->get('/', 'Mahasiswa::profile');
    $routes->get('frs', 'Mahasiswa::frs');
    $routes->post('frs', 'Mahasiswa::addClass');
    $routes->post('frs/(:num)/hapus', 'Mahasiswa::removeClass/$1');
    $routes->get('nilai', 'Mahasiswa::grades');
    $routes->get('frs/cetak', 'Mahasiswa::printFrs');
    $routes->get('transkrip/cetak', 'Mahasiswa::printTranscript');
});
