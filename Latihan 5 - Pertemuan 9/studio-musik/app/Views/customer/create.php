<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?>Ajukan booking<?= $this->endSection() ?>
<?= $this->section('content') ?>
<h1 class="hand page-title">Ajukan booking</h1>

<div class="row g-4">
    <div class="col-lg-5">
        <form action="<?= site_url('pelanggan/booking/baru') ?>" method="get" class="panel">
            <h2 class="hand section-title">1. Pilih studio dan tanggal</h2>
            <div class="mb-3">
                <span class="form-label d-block">Pilih ruang</span>
                <?php if (! $studios): ?>
                    <div class="empty-state">Belum ada studio yang aktif untuk dibooking.</div>
                <?php else: ?>
                    <div class="booking-studio-grid">
                        <?php foreach ($studios as $s): $inputId = 'studio-' . (int) $s['id']; ?>
                            <div class="booking-studio-option">
                                <input class="visually-hidden" type="radio" id="<?= esc($inputId, 'attr') ?>" name="studio_id" value="<?= (int) $s['id'] ?>" <?= (int) $studioId === (int) $s['id'] ? 'checked' : '' ?> required>
                                <label for="<?= esc($inputId, 'attr') ?>">
                                    <?php if ($s['photo']): ?>
                                        <img src="<?= base_url('uploads/studios/' . esc($s['photo'], 'url')) ?>" alt="" loading="lazy">
                                    <?php else: ?>
                                        <span class="booking-studio-placeholder">Foto belum tersedia</span>
                                    <?php endif; ?>
                                    <span class="booking-studio-copy">
                                        <strong><?= esc($s['name']) ?></strong>
                                        <span><?= rupiah($s['hourly_rate']) ?> / jam</span>
                                        <small>Kapasitas <?= (int) $s['capacity'] ?> orang</small>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="booking_date" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="booking_date" name="booking_date" min="<?= date('Y-m-d') ?>" value="<?= esc($date, 'attr') ?>" required>
            </div>
            <button class="btn btn-primary" type="submit" <?= ! $studios ? 'disabled' : '' ?>>Lihat jadwal</button>
            <?php if ($error): ?><p class="text-danger mt-3 mb-0" role="alert"><?= esc($error) ?></p><?php endif; ?>
        </form>
    </div>

    <div class="col-lg-7">
        <?php if (! $selected): ?>
            <div class="panel">
                <h2 class="hand section-title">2. Pilih jam</h2>
                <p class="text-secondary mb-0">Pilih studio dan tanggal di sebelah kiri, lalu klik Lihat jadwal. Jadwal yang sudah terisi akan tampil di sini.</p>
            </div>
        <?php else: ?>
            <div class="panel mb-3">
                <h2 class="hand section-title">Jadwal <?= esc($selected['name']) ?>, <?= date_id($date) ?></h2>
                <?php if (! $schedule): ?>
                    <p class="text-secondary mb-0">Belum ada booking pada tanggal ini. Studio kosong sepanjang hari.</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($schedule as $row): ?>
                            <li class="d-flex gap-3 py-1">
                                <strong><?= hour_label($row['start_hour']) ?> - <?= hour_label($row['end_hour']) ?></strong>
                                <?= status_badge($row['status']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <form action="<?= site_url('pelanggan/booking') ?>" method="post" class="panel" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="studio_id" value="<?= (int) $selected['id'] ?>">
                <input type="hidden" name="booking_date" value="<?= esc($date, 'attr') ?>">
                <h2 class="hand section-title">2. Pilih jam</h2>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="start_hour" class="form-label">Jam mulai</label>
                        <select class="form-select" id="start_hour" name="start_hour" required>
                            <?php for ($h = \App\Models\BookingModel::OPEN_HOUR; $h < \App\Models\BookingModel::CLOSE_HOUR; $h++): ?>
                                <option value="<?= $h ?>" <?= (string) old('start_hour') === (string) $h ? 'selected' : '' ?>><?= hour_label($h) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="end_hour" class="form-label">Jam selesai</label>
                        <select class="form-select" id="end_hour" name="end_hour" required>
                            <?php for ($h = \App\Models\BookingModel::OPEN_HOUR + 1; $h <= \App\Models\BookingModel::CLOSE_HOUR; $h++): ?>
                                <option value="<?= $h ?>" <?= (string) old('end_hour') === (string) $h ? 'selected' : '' ?>><?= hour_label($h) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="my-3">
                    <label for="notes" class="form-label">Catatan (opsional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2" maxlength="200"><?= esc(old('notes')) ?></textarea>
                </div>
                <p class="small text-secondary">Tarif <?= rupiah($selected['hourly_rate']) ?> per jam. Total dihitung otomatis dari lama sewa.</p>
                <button class="btn btn-primary" type="submit">Ajukan booking</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
