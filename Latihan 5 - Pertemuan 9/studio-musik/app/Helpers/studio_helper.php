<?php

if (! function_exists('current_user')) {
    function current_user(): ?array
    {
        return session('user');
    }
}

if (! function_exists('has_role')) {
    function has_role(string ...$roles): bool
    {
        $u = session('user');

        return $u !== null && in_array($u['role'], $roles, true);
    }
}

if (! function_exists('rupiah')) {
    function rupiah($n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }
}

if (! function_exists('hour_label')) {
    function hour_label($h): string
    {
        return sprintf('%02d:00', (int) $h);
    }
}

if (! function_exists('date_id')) {
    function date_id(?string $date): string
    {
        if (! $date) {
            return '-';
        }
        $t      = strtotime(substr($date, 0, 10));
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return date('j', $t) . ' ' . $months[(int) date('n', $t) - 1] . ' ' . date('Y', $t);
    }
}

if (! function_exists('status_label')) {
    function status_label(string $status): string
    {
        return [
            'menunggu'    => 'Menunggu',
            'dikonfirmasi' => 'Dikonfirmasi',
            'selesai'     => 'Selesai',
            'dibatalkan'  => 'Dibatalkan',
        ][$status] ?? ucfirst($status);
    }
}

if (! function_exists('status_badge')) {
    /** Lencana status: warna selalu disertai teks. */
    function status_badge(string $status): string
    {
        return '<span class="st st-' . esc($status, 'attr') . '">' . esc(status_label($status)) . '</span>';
    }
}
