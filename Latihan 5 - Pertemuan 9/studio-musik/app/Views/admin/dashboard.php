<?= $this->extend('layouts/admin') ?>
<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>
<?= $this->section('heading') ?>Dashboard<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="stat stat-focus h-100">
            <div class="stat-label">Menunggu konfirmasi</div>
            <div class="stat-value hand"><?= (int) $pending ?></div>
            <a class="stat-link" href="<?= site_url('admin/booking?status=menunggu') ?>">Proses sekarang</a>
        </div>
    </div>
    <div class="col-sm-4 col-lg-2">
        <div class="stat h-100"><div class="stat-label">Studio aktif</div><div class="stat-value hand"><?= (int) $studios ?></div></div>
    </div>
    <div class="col-sm-4 col-lg-2">
        <div class="stat h-100"><div class="stat-label">Jenis alat</div><div class="stat-value hand"><?= (int) $equipments ?></div></div>
    </div>
    <div class="col-sm-4 col-lg-3">
        <div class="stat h-100"><div class="stat-label">Pendapatan bulan ini</div><div class="stat-value stat-money"><?= rupiah($income) ?></div><div class="small text-secondary">Booking dikonfirmasi dan selesai</div></div>
    </div>
</div>

<div class="panel">
    <h2 class="hand section-title">Jadwal hari ini</h2>
    <?php if (! $today): ?>
        <p class="text-secondary mb-0">Tidak ada booking aktif untuk hari ini.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table studio-table align-middle mb-0">
                <thead><tr><th scope="col">Jam</th><th scope="col">Studio</th><th scope="col">Pelanggan</th><th scope="col">Status</th></tr></thead>
                <tbody>
                <?php foreach ($today as $b): ?>
                    <tr>
                        <td><?= hour_label($b['start_hour']) ?> - <?= hour_label($b['end_hour']) ?></td>
                        <td><?= esc($b['studio_name']) ?></td>
                        <td><?= esc($b['user_name']) ?></td>
                        <td><?= status_badge($b['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
