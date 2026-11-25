<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$statusLabels = ['pending' => 'Menunggu diproses', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Diterima'];
?>
<section class="management-page page-width">
    <div class="management-heading management-heading-compact"><div><span class="eyebrow"><span class="eyebrow-dot"></span> Area customer</span><h1>Pesanan <em>saya.</em></h1><p>Pantau proses dan informasi pengiriman pesananmu.</p></div><a class="text-link" href="<?= url_to('catalog') ?>">Lanjut belanja <span aria-hidden="true">↗</span></a></div>
    <?php if ($orders === []): ?>
        <div class="empty-state"><span class="eyebrow">Belum ada riwayat</span><h2>Pesananmu akan muncul di sini.</h2><p>Pilih produk dari katalog untuk memulai.</p><a class="button button-accent" href="<?= url_to('catalog') ?>">Jelajahi produk <span aria-hidden="true">↗</span></a></div>
    <?php else: ?>
        <div class="table-scroll"><table class="data-table order-table"><thead><tr><th>Nomor pesanan</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Pengiriman</th><th></th></tr></thead><tbody>
        <?php foreach ($orders as $order): ?><tr><td><strong><?= esc($order['order_number']) ?></strong></td><td><?= esc(date('d M Y', strtotime($order['created_at']))) ?></td><td>Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></td><td><span class="order-status order-status-<?= esc($order['status'], 'attr') ?>"><?= esc($statusLabels[$order['status']] ?? $order['status']) ?></span></td><td><?= esc(ucfirst($order['shipping_method'])) ?><?= $order['tracking_number'] ? '<br><small>' . esc($order['tracking_number']) . '</small>' : '' ?></td><td><a class="table-action" href="<?= url_to('orders.show', $order['id']) ?>">Detail</a></td></tr><?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
