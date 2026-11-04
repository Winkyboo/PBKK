<?php if (session('success')): ?>
    <div class="alert alert-success" role="status"><?= esc(session('success')) ?></div>
<?php endif; ?>
<?php if (session('error')): ?>
    <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
<?php endif; ?>
<?php if (session('errors')): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Periksa isian berikut:</strong>
        <ul class="mb-0">
            <?php foreach ((array) session('errors') as $msg): ?>
                <li><?= esc($msg) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
