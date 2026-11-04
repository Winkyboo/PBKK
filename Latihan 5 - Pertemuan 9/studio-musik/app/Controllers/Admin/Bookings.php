<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Bookings extends BaseController
{
    private const STATUSES = ['menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan'];

    public function index(): string
    {
        $status = (string) $this->request->getGet('status');
        $model  = model(BookingModel::class)->withDetails();
        if (in_array($status, self::STATUSES, true)) {
            $model->where('bookings.status', $status);
        } else {
            $status = '';
        }

        return view('admin/bookings', [
            'bookings' => $model->orderBy('bookings.booking_date', 'DESC')->orderBy('bookings.start_hour', 'DESC')->findAll(),
            'status'   => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function confirm(int $id): RedirectResponse
    {
        $b = $this->find($id);
        if ($b['status'] !== 'menunggu') {
            return $this->back('error', 'Hanya booking berstatus Menunggu yang dapat dikonfirmasi.');
        }
        // Cek ulang bentrok terhadap booking yang sudah dikonfirmasi.
        if (model(BookingModel::class)->hasConflict((int) $b['studio_id'], $b['booking_date'], (int) $b['start_hour'], (int) $b['end_hour'], $id, ['dikonfirmasi'])) {
            return $this->back('error', 'Tidak dapat dikonfirmasi: jadwal bentrok dengan booking lain yang sudah dikonfirmasi.');
        }
        model(BookingModel::class)->update($id, ['status' => 'dikonfirmasi']);

        return $this->back('success', 'Booking dikonfirmasi.');
    }

    public function finish(int $id): RedirectResponse
    {
        $b = $this->find($id);
        if ($b['status'] !== 'dikonfirmasi') {
            return $this->back('error', 'Hanya booking berstatus Dikonfirmasi yang dapat diselesaikan.');
        }
        model(BookingModel::class)->update($id, ['status' => 'selesai']);

        return $this->back('success', 'Booking ditandai selesai.');
    }

    public function cancel(int $id): RedirectResponse
    {
        $b = $this->find($id);
        if (! in_array($b['status'], BookingModel::ACTIVE, true)) {
            return $this->back('error', 'Booking ini tidak dapat dibatalkan lagi.');
        }
        model(BookingModel::class)->update($id, ['status' => 'dibatalkan']);

        return $this->back('success', 'Booking dibatalkan.');
    }

    private function back(string $type, string $message): RedirectResponse
    {
        return redirect()->to('/admin/booking')->with($type, $message);
    }

    private function find(int $id): array
    {
        return model(BookingModel::class)->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }
}
