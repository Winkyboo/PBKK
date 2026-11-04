<?= $this->extend('layouts/public') ?>
<?= $this->section('title') ?>Daftar<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="auth-box">
    <h1 class="hand page-title">Buat akun</h1>
    <form action="<?= site_url('daftar') ?>" method="post" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= esc(old('name'), 'attr') ?>" maxlength="100" autocomplete="name" required autofocus>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email'), 'attr') ?>" maxlength="100" autocomplete="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Kata sandi</label>
            <input type="password" class="form-control" id="password" name="password" minlength="8" autocomplete="new-password" aria-describedby="pwHelp" required>
            <div id="pwHelp" class="form-text">Minimal 8 karakter.</div>
        </div>
        <div class="mb-3">
            <label for="passconf" class="form-label">Ulangi kata sandi</label>
            <input type="password" class="form-control" id="passconf" name="passconf" autocomplete="new-password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Daftar</button>
    </form>
    <p class="mt-3 mb-0 small">Sudah punya akun? <a href="<?= site_url('login') ?>">Masuk</a></p>
</div>
<?= $this->endSection() ?>
