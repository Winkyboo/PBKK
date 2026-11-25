<?php foreach (['success', 'error', 'info'] as $type): ?>
    <?php if ($message = session()->getFlashdata($type)): ?>
        <div class="flash-message flash-<?= esc($type, 'attr') ?> page-width" role="status">
            <?= esc($message) ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
