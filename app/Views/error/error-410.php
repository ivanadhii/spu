<?= $this->extend('admin/layouts/error') ?>

<?= $this->section('content') ?>

<title>Expired</title>

<div class="error-page container">
    <div class="col-md-8 col-12 offset-md-2">
        <div class="text-center">
            <img class="img-error mx-auto d-block" src="/assets/images/samples/image.png" alt="Not Found"
                style="height: 35rem; width: 35rem;">
        </div>
        <div class="text-center">
            <h1 class="error-title">SHORTLINK SUDAH EXPIRED</h1>
            <!-- <p class='fs-5 text-gray-600'>Shortlink sudah tidak aktif.</p> -->
            <a href="<?= base_url() ?>" class="btn btn-lg btn-outline-primary mt-3">Kembali</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
