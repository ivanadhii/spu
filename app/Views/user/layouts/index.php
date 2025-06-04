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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <link rel="shortcut icon" href="<?= base_url() ?>/assets/images/favicon.svg" type="image/x-icon">

</head>

<body>
    <div id="app">

        <?= $this->include('user/layouts/navbar') ?>

        <div id="main">
            <?= $this->renderSection('content') ?>
        </div>

        <?= $this->include('user/layouts/footer') ?>

    </div>

    <script src="/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>

    <?= $this->renderSection('javascript') ?>

    <script src="/assets/js/main.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
        });
    </script>

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <script src="<?= base_url() ?>/assets/js/homepage.js"></script>

</body>

</html>
