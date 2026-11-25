<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId  = (int) $session->get('user_id');

        if (! $session->get('logged_in') || $userId < 1) {
            return redirect()->to(url_to('login'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk membuka halaman tersebut.');
        }

        $user = (new UserModel())->find($userId);

        if ($user === null || (int) $user['is_active'] !== 1) {
            $session->destroy();

            return redirect()->to(url_to('login'))
                ->with('error', 'Sesi berakhir atau akun tidak aktif. Silakan masuk kembali.');
        }

        // Role and name come from the database, not from the session or a form.
        $session->set([
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'role'       => $user['role'],
        ]);

        $allowedRoles = $arguments ?? [];
        if ($allowedRoles !== [] && ! in_array($user['role'], $allowedRoles, true)) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setBody(view('errors/html/error_403', [
                    'title' => 'Akses ditolak',
                    'role'  => $user['role'],
                ]));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
