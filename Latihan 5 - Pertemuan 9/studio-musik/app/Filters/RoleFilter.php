<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Membatasi halaman berdasarkan peran, contoh pemakaian pada rute: 'filter' => 'role:admin' */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session('user');
        if (! $user) {
            return redirect()->to('/login')->with('error', 'Silakan masuk terlebih dahulu.');
        }
        if (! in_array($user['role'], $arguments ?? [], true)) {
            return service('response')->setStatusCode(403)->setBody(view('errors/forbidden'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
