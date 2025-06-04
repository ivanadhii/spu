<?= $this->extend('admin/layouts/error') ?>

<?= $this->section('content') ?>

<div class="error-page container">
    <div class="col-md-8 col-12 offset-md-2">
        <img class="img-error" src="/assets/images/samples/error-500.png" alt="Not Found">
        <div class="text-center">
            <h1 class="error-title">UNDER MAINTENANCE</h1>
            <p class="fs-5 text-gray-600">Shortlink sedang dalam pemeliharaan. Mohon tunggu beberapa saat.</p>
            <a href="<?= base_url() ?>" class="btn btn-lg btn-outline-primary mt-3">Kembali</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>