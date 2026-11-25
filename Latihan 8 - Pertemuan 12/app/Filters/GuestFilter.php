<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GuestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return null;
        }

        $destination = match ($session->get('role')) {
            'admin'    => url_to('admin.dashboard'),
            'operator' => url_to('operator.dashboard'),
            default    => url_to('account'),
        };

        return redirect()->to($destination);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
