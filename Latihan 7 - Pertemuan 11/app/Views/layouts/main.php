<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F2E8DA">
    <title><?= esc($title ?? 'ShopEase') ?> · ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <?= $this->include('partials/navbar') ?>
    <main id="main-content">
        <?= $this->include('partials/flash') ?>
        <?= $this->renderSection('content') ?>
    </main>
    <?= $this->include('partials/footer') ?>
</body>
</html>
