<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">

    <link rel="icon" type="image/x-icon" href="assets/pupr.ico" />

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/bootstrap.css">

    <link rel="stylesheet" href="<?= base_url() ?>/assets/vendors/iconly/bold.css">

    <link rel="stylesheet" href="<?= base_url() ?>/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/app.css">

    <?= $this->renderSection('styles') ?>

    <link rel="shortcut icon" href="<?= base_url() ?>/assets/images/favicon.svg" type="image/x-icon">
</head>

<!-- <header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header> -->

<body>
    <div id="app">

        <div id="main">
            <?= $this->include('admin/layouts/sidebar') ?>

            <div class="content-wrapper">
                <?= $this->renderSection('content') ?>
            </div>

            <?= $this->include('admin/layouts/footer') ?>
        </div>

    </div>

    <script src="<?= base_url() ?>/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/bootstrap.bundle.min.js"></script>

    <?= $this->renderSection('javascript') ?>

    <script src="<?= base_url() ?>/assets/js/main.js"></script>

    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
//    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script src="https://www.google.com/recaptcha/api.js?render=6LciH9YqAAAAABq6Bk8n7oThEh-Xdvmj-ywrCSdb"></script>

    <script src="<?= base_url() ?>/assets/js/app.js"></script>

</body>

</html>
