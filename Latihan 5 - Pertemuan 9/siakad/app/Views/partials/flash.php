<?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $type): ?>
    <?php if ($message = session()->getFlashdata($key)): ?><div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert"><?= esc($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button></div><?php endif ?>
<?php endforeach ?>
