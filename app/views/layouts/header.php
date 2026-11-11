<?php
$cartCount = class_exists('Cart') ? Cart::count() : 0;
$success   = flash('success');
$error     = flash('error');

// Meta description per halaman: controller bisa pass $metaDescription
$metaDescription = $metaDescription
    ?? 'ZORA — pilihan elektronik untuk keseharian, dengan pengalaman belanja yang sederhana.';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($pageTitle ?? 'ZORA Electronics') ?> — ZORA</title>

    <!-- FIX (L1): Meta description kini bisa di-override per halaman -->
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="robots" content="index, follow">

    <!-- Google Fonts preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FIX (M5/L14): Bootstrap Icons dipindahkan ke <link> tag, bukan @import di CSS.
         @import dalam CSS memaksa browser melakukan request seri (lambat).
         <link> memungkinkan parallel request (lebih cepat). -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- ZORA CSS dengan cache-buster otomatis agar setiap perubahan CSS langsung aktif di browser -->
    <link href="<?= asset('css/style.css') ?>?v=<?= file_exists(__DIR__ . '/../../../public/assets/css/style.css') ? filemtime(__DIR__ . '/../../../public/assets/css/style.css') : time() ?>" rel="stylesheet">
    <link href="<?= asset('css/responsive.css') ?>?v=<?= file_exists(__DIR__ . '/../../../public/assets/css/responsive.css') ? filemtime(__DIR__ . '/../../../public/assets/css/responsive.css') : time() ?>" rel="stylesheet">

    <?php if (is_admin()): ?>
        <link href="<?= asset('css/admin.css') ?>?v=<?= file_exists(__DIR__ . '/../../../public/assets/css/admin.css') ? filemtime(__DIR__ . '/../../../public/assets/css/admin.css') : time() ?>" rel="stylesheet">
    <?php endif; ?>
</head>
<body>

<?php require __DIR__ . '/navbar.php'; ?>

<main class="site-main">
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?= e($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <?= e($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>
    </div>
