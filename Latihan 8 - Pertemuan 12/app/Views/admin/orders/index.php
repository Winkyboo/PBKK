<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $statusLabels = ['pending' => 'Menunggu diproses', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Diterima']; ?>
<section class="management-page page-width">
    <div class="management-heading"><div><span class="eyebrow"><span class="eyebrow-dot"></span> Panel admin</span><h1>Pesanan <em>ShopEase.</em></h1><p>Periksa informasi checkout dan kelola status pengiriman.</p></div></div>
    <nav class="management-nav" aria-label="Menu pengelolaan"><a class="is-active" href="<?= url_to('admin.orders.index') ?>">Pesanan</a><a href="<?= url_to('admin.products.index') ?>">Produk</a><a href="<?= url_to('admin.categories.index') ?>">Kategori</a><a href="<?= url_to('admin.dashboard') ?>">Dashboard</a></nav>
    <div class="management-summary"><span><?= count($orders) ?> pesanan terbaru</span><span>Masukkan resi saat status pesanan diubah menjadi dikirim.</span></div>
    <div class="table-scroll"><table class="data-table order-table"><thead><tr><th>Pesanan</th><th>Customer</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
    <?php if ($orders === []): ?><tr><td class="table-empty" colspan="6">Belum ada pesanan dari customer.</td></tr><?php endif; ?>
    <?php foreach ($orders as $order): ?><tr><td><strong><?= esc($order['order_number']) ?></strong><small class="order-subline"><?= esc(ucfirst($order['shipping_method'])) ?> · <?= esc($order['payment_method'] === 'cod' ? 'COD' : $order['payment_method']) ?></small></td><td><?= esc($order['customer_name']) ?><small class="order-subline"><?= esc($order['customer_email']) ?></small></td><td><?= esc(date('d M Y', strtotime($order['created_at']))) ?></td><td>Rp <?= number_format((float) $order['total'], 0, ',', '.') ?></td><td><span class="order-status order-status-<?= esc($order['status'], 'attr') ?>"><?= esc($statusLabels[$order['status']] ?? $order['status']) ?></span></td><td><a class="table-action" href="<?= url_to('admin.orders.show', $order['id']) ?>">Kelola</a></td></tr><?php endforeach; ?>
    </tbody></table></div>
</section>
<?= $this->endSection() ?>
