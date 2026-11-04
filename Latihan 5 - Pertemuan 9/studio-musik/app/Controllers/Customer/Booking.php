<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\StudioModel;
use CodeIgniter\HTTP\RedirectResponse;

class Booking extends BaseController
{
    public function index(): string
    {
        $bookings = model(BookingModel::class)->withDetails()
            ->where('bookings.user_id', current_user()['id'])
            ->orderBy('bookings.booking_date', 'DESC')->orderBy('bookings.start_hour', 'DESC')
            ->findAll();

        return view('customer/index', ['bookings' => $bookings]);
    }

    /** Langkah 1: pilih studio dan tanggal (GET). Langkah 2: pilih jam (POST). */
    public function create(): string
    {
        $studios  = model(StudioModel::class)->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
        $studioId = (int) $this->request->getGet('studio_id');
        $date     = (string) $this->request->getGet('booking_date');

        $selected = null;
        $schedule = [];
        $error    = null;
        if ($studioId > 0 && $date !== '') {
            $selected = model(StudioModel::class)->where('is_active', 1)->find($studioId);
            if (! $selected) {
                $error = 'Studio tidak ditemukan.';
            } elseif (! $this->validDate($date)) {
                $error    = 'Tanggal tidak valid.';
                $selected = null;
            } elseif ($date < date('Y-m-d')) {
                $error    = 'Tanggal tidak boleh di masa lalu.';
                $selected = null;
            } else {
                $schedule = model(BookingModel::class)->scheduleFor($studioId, $date);
            }
        }

        return view('customer/create', [
            'studios'  => $studios,
            'studioId' => $studioId,
            'date'     => $date,
            'selected' => $selected,
            'schedule' => $schedule,
            'error'    => $error,
        ]);
    }

    public function store(): RedirectResponse
    {
        $data = [
            'studio_id'    => (int) $this->request->getPost('studio_id'),
            'booking_date' => (string) $this->request->getPost('booking_date'),
            'start_hour'   => (string) $this->request->getPost('start_hour'),
            'end_hour'     => (string) $this->request->getPost('end_hour'),
            'notes'        => trim((string) $this->request->getPost('notes')),
        ];
        $rules = [
            'studio_id'    => ['label' => 'Studio', 'rules' => 'required|is_natural_no_zero'],
            'booking_date' => ['label' => 'Tanggal', 'rules' => 'required|valid_date[Y-m-d]'],
            'start_hour'   => ['label' => 'Jam mulai', 'rules' => 'required|is_natural'],
            'end_hour'     => ['label' => 'Jam selesai', 'rules' => 'required|is_natural'],
            'notes'        => ['label' => 'Catatan', 'rules' => 'permit_empty|max_length[200]'],
        ];
        $back = '/pelanggan/booking/baru?studio_id=' . (int) $data['studio_id'] . '&booking_date=' . urlencode($data['booking_date']);

        if (! $this->validateData($data, $rules)) {
            return redirect()->to($back)->withInput()->with('errors', $this->validator->getErrors());
        }

        $start    = (int) $data['start_hour'];
        $end      = (int) $data['end_hour'];
        $studio   = model(StudioModel::class)->where('is_active', 1)->find($data['studio_id']);
        $bookings = model(BookingModel::class);

        $problem = null;
        if (! $studio) {
            $problem = 'Studio tidak ditemukan atau sedang tidak aktif.';
        } elseif ($data['booking_date'] < date('Y-m-d')) {
            $problem = 'Tanggal tidak boleh di masa lalu.';
        } elseif ($end <= $start) {
            $problem = 'Jam selesai harus setelah jam mulai.';
        } elseif ($start < BookingModel::OPEN_HOUR || $end > BookingModel::CLOSE_HOUR) {
            $problem = 'Studio buka pukul ' . hour_label(BookingModel::OPEN_HOUR) . ' sampai ' . hour_label(BookingModel::CLOSE_HOUR) . '.';
        } elseif ($data['booking_date'] === date('Y-m-d') && $start <= (int) date('G')) {
            $problem = 'Jam mulai sudah lewat atau sedang berjalan.';
        } elseif ($bookings->hasConflict((int) $studio['id'], $data['booking_date'], $start, $end)) {
            $problem = 'Jadwal bentrok dengan booking lain. Pilih jam atau studio lain.';
        }
        if ($problem) {
            return redirect()->to($back)->withInput()->with('error', $problem);
        }

        $bookings->insert([
            'user_id'      => current_user()['id'],
            'studio_id'    => $studio['id'],
            'booking_date' => $data['booking_date'],
            'start_hour'   => $start,
            'end_hour'     => $end,
            'total_price'  => (int) $studio['hourly_rate'] * ($end - $start),
            'status'       => 'menunggu',
            'notes'        => $data['notes'] === '' ? null : $data['notes'],
        ]);

        return redirect()->to('/pelanggan/booking')->with('success', 'Booking diajukan. Unggah bukti pembayaran agar dapat dikonfirmasi admin.');
    }

    public function uploadProof(int $id): RedirectResponse
    {
        $booking = $this->ownBooking($id);
        if (! $booking) {
            return redirect()->to('/pelanggan/booking')->with('error', 'Booking tidak ditemukan.');
        }
        if ($booking['status'] !== 'menunggu') {
            return redirect()->to('/pelanggan/booking')->with('error', 'Bukti hanya dapat diunggah saat booking berstatus Menunggu.');
        }

        $rules = [
            'proof' => [
                'label' => 'Bukti pembayaran',
                'rules' => 'uploaded[proof]|max_size[proof,3072]|ext_in[proof,jpg,jpeg,png,pdf]|mime_in[proof,image/jpg,image/jpeg,image/png,application/pdf]',
            ],
        ];
        if (! $this->validate($rules)) {
            return redirect()->to('/pelanggan/booking')->with('error', implode(' ', $this->validator->getErrors()));
        }

        $file = $this->request->getFile('proof');
        $name = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/proofs', $name);

        if (! empty($booking['payment_proof'])) {
            $old = WRITEPATH . 'uploads/proofs/' . basename($booking['payment_proof']);
            is_file($old) && unlink($old);
        }
        model(BookingModel::class)->update($id, ['payment_proof' => $name]);

        return redirect()->to('/pelanggan/booking')->with('success', 'Bukti pembayaran diunggah.');
    }

    public function cancel(int $id): RedirectResponse
    {
        $booking = $this->ownBooking($id);
        if (! $booking) {
            return redirect()->to('/pelanggan/booking')->with('error', 'Booking tidak ditemukan.');
        }
        if (! in_array($booking['status'], BookingModel::ACTIVE, true)) {
            return redirect()->to('/pelanggan/booking')->with('error', 'Booking ini tidak dapat dibatalkan lagi.');
        }

        model(BookingModel::class)->update($id, ['status' => 'dibatalkan']);

        return redirect()->to('/pelanggan/booking')->with('success', 'Booking dibatalkan.');
    }

    private function ownBooking(int $id): ?array
    {
        return model(BookingModel::class)->where('user_id', current_user()['id'])->find($id);
    }

    private function validDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);

        return $d && $d->format('Y-m-d') === $date;
    }
}
