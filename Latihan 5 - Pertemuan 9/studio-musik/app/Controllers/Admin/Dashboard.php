<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\EquipmentModel;
use App\Models\StudioModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $bookings = model(BookingModel::class);
        $today    = date('Y-m-d');
        $first    = date('Y-m-01');
        $last     = date('Y-m-t');

        $income = $bookings->builder()->selectSum('total_price', 'total')
            ->whereIn('status', ['dikonfirmasi', 'selesai'])
            ->where('booking_date >=', $first)->where('booking_date <=', $last)
            ->get()->getRowArray();

        return view('admin/dashboard', [
            'pending'    => $bookings->builder()->where('status', 'menunggu')->countAllResults(),
            'studios'    => model(StudioModel::class)->where('is_active', 1)->countAllResults(),
            'equipments' => model(EquipmentModel::class)->countAllResults(),
            'income'     => (int) ($income['total'] ?? 0),
            'today'      => $bookings->withDetails()
                ->where('bookings.booking_date', $today)
                ->whereIn('bookings.status', BookingModel::ACTIVE)
                ->orderBy('bookings.start_hour', 'ASC')->findAll(),
        ]);
    }
}
