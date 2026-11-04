<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?>Booking saya<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="hand page-title mb-0">Booking saya</h1>
    <a class="btn btn-primary" href="<?= site_url('pelanggan/booking/baru') ?>">Ajukan booking</a>
</div>

<?php if (! $bookings): ?>
    <p class="text-secondary">Anda belum pernah mengajukan booking. Pilih studio dan tanggal untuk memulai.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table studio-table align-middle">
            <thead>
            <tr><th scope="col">Tanggal</th><th scope="col">Jam</th><th scope="col">Studio</th><th scope="col">Total</th><th scope="col">Status</th><th scope="col">Bukti bayar</th><th scope="col">Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><?= date_id($b['booking_date']) ?></td>
                    <td><?= hour_label($b['start_hour']) ?> - <?= hour_label($b['end_hour']) ?></td>
                    <td><?= esc($b['studio_name']) ?></td>
                    <td><?= rupiah($b['total_price']) ?></td>
                    <td><?= status_badge($b['status']) ?></td>
                    <td>
                        <?php if ($b['payment_proof']): ?>
                            <a href="<?= site_url('bukti/' . (int) $b['id']) ?>" target="_blank" rel="noopener">Lihat bukti</a>
                        <?php else: ?>
                            <span class="text-secondary">Belum ada</span>
                        <?php endif; ?>
                        <?php if ($b['status'] === 'menunggu'): ?>
                            <form action="<?= site_url('pelanggan/booking/' . (int) $b['id'] . '/bukti') ?>" method="post" enctype="multipart/form-data" class="proof-form">
                                <?= csrf_field() ?>
                                <label class="visually-hidden" for="proof<?= (int) $b['id'] ?>">Pilih file bukti pembayaran</label>
                                <input type="file" class="form-control form-control-sm" id="proof<?= (int) $b['id'] ?>" name="proof" accept=".jpg,.jpeg,.png,.pdf" required>
                                <button class="btn btn-outline-dark btn-sm" type="submit">Unggah</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (in_array($b['status'], ['menunggu', 'dikonfirmasi'], true)): ?>
                            <form action="<?= site_url('pelanggan/booking/' . (int) $b['id'] . '/batal') ?>" method="post" onsubmit="return confirm('Batalkan booking ini?');">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline-danger btn-sm" type="submit">Batalkan</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
