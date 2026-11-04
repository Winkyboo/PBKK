<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    public const OPEN_HOUR  = 9;
    public const CLOSE_HOUR = 22;
    /** Status yang menahan jadwal: booking menunggu pun sudah memesan slot. */
    public const ACTIVE = ['menunggu', 'dikonfirmasi'];

    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'studio_id', 'booking_date', 'start_hour', 'end_hour', 'total_price', 'status', 'notes', 'payment_proof'];
    protected $useTimestamps    = true;

    /** Query dasar dengan nama studio dan pelanggan. */
    public function withDetails()
    {
        return $this->select('bookings.*, studios.name AS studio_name, users.name AS user_name')
            ->join('studios', 'studios.id = bookings.studio_id')
            ->join('users', 'users.id = bookings.user_id');
    }

    /** True bila jam yang diminta beririsan dengan booking lain yang memegang jadwal. */
    public function hasConflict(int $studioId, string $date, int $start, int $end, int $excludeId = 0, array $statuses = self::ACTIVE): bool
    {
        $builder = $this->builder()
            ->where('studio_id', $studioId)
            ->where('booking_date', $date)
            ->whereIn('status', $statuses)
            ->where('start_hour <', $end)
            ->where('end_hour >', $start);
        if ($excludeId > 0) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function scheduleFor(int $studioId, string $date): array
    {
        return $this->withDetails()
            ->where('bookings.studio_id', $studioId)
            ->where('bookings.booking_date', $date)
            ->whereIn('bookings.status', self::ACTIVE)
            ->orderBy('bookings.start_hour', 'ASC')
            ->findAll();
    }
}
