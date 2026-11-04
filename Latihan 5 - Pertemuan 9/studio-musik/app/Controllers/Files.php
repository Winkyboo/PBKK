<?php

namespace App\Controllers;

use App\Models\BookingModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Files\File;

/** Menyajikan bukti pembayaran dari folder writable, hanya untuk pemilik booking atau admin. */
class Files extends BaseController
{
    public function proof(int $bookingId)
    {
        $booking = model(BookingModel::class)->find($bookingId);
        if (! $booking || empty($booking['payment_proof'])) {
            throw PageNotFoundException::forPageNotFound();
        }
        if (! has_role('admin') && (int) $booking['user_id'] !== (int) current_user()['id']) {
            return $this->response->setStatusCode(403)->setBody(view('errors/forbidden'));
        }

        $path = WRITEPATH . 'uploads/proofs/' . basename($booking['payment_proof']);
        if (! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setContentType((new File($path))->getMimeType())
            ->setBody(file_get_contents($path));
    }
}
