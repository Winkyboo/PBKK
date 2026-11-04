<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?>Booking<?= $this->endSection() ?>
<?= $this->section('heading') ?>Booking<?= $this->endSection() ?>
<?= $this->section('content') ?>
<form method="get" action="<?= site_url('admin/booking') ?>" class="d-flex flex-wrap align-items-end gap-2 mb-3">
    <div>
        <label for="status" class="form-label">Filter status</label>
        <select class="form-select" id="status" name="status">
            <option value="">Semua status</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= esc($s, 'attr') ?>" <?= $status === $s ? 'selected' : '' ?>><?= esc(status_label($s)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-outline-dark" type="submit">Terapkan</button>
</form>

<div class="panel">
<?php if (! $bookings): ?>
    <p class="text-secondary mb-0">Tidak ada booking<?= $status ? ' dengan status ' . esc(status_label($status)) : '' ?>.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table studio-table align-middle mb-0">
            <thead><tr><th scope="col">Tanggal</th><th scope="col">Jam</th><th scope="col">Studio</th><th scope="col">Pelanggan</th><th scope="col">Total</th><th scope="col">Status</th><th scope="col">Bukti</th><th scope="col">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= date_id($b['booking_date']) ?></td>
                    <td><?= hour_label($b['start_hour']) ?> - <?= hour_label($b['end_hour']) ?></td>
                    <td><?= esc($b['studio_name']) ?></td>
                    <td><?= esc($b['user_name']) ?></td>
                    <td><?= rupiah($b['total_price']) ?></td>
                    <td><?= status_badge($b['status']) ?></td>
                    <td>
                        <?php if ($b['payment_proof']): ?>
                            <a href="<?= site_url('bukti/' . (int) $b['id']) ?>" target="_blank" rel="noopener">Lihat</a>
                        <?php else: ?>
                            <span class="text-secondary">Belum ada</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                        <?php if ($b['status'] === 'menunggu'): ?>
                            <form action="<?= site_url('admin/booking/' . (int) $b['id'] . '/konfirmasi') ?>" method="post"><?= csrf_field() ?><button class="btn btn-primary btn-sm" type="submit">Konfirmasi</button></form>
                        <?php endif; ?>
                        <?php if ($b['status'] === 'dikonfirmasi'): ?>
                            <form action="<?= site_url('admin/booking/' . (int) $b['id'] . '/selesai') ?>" method="post"><?= csrf_field() ?><button class="btn btn-outline-dark btn-sm" type="submit">Tandai selesai</button></form>
                        <?php endif; ?>
                        <?php if (in_array($b['status'], ['menunggu', 'dikonfirmasi'], true)): ?>
                            <form action="<?= site_url('admin/booking/' . (int) $b['id'] . '/batal') ?>" method="post" onsubmit="return confirm('Batalkan booking ini?');"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm" type="submit">Batalkan</button></form>
                        <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</div>
<?= $this->endSection() ?>
