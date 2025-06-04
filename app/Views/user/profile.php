<?= $this->extend('admin/layouts/app'); ?>

<?= $this->section('content'); ?>

<title>Profile</title>

<div class="page-content">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>

    <div class="page-heading">
        <h2 class="my-4"><b>My Profile</b></h2>
        <p>Update / Edit Profile</p>
    </div>

    <section class="row">
        <div class="col-12 col-lg-15">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <small>Full Name</small>
                        <h5 class="card-title"><?= esc($users->fullname) ?></h5>
                        <small>Email</small>
                        <p class="card-text text-dark"><?= esc($users->email) ?></p>
                        <small>Username</small>
                        <p class="card-text text-dark"><?= esc($users->username) ?></p>
                        <hr>
                        <p class="card-text"><?= esc($users->unit_organisasi) ?></p>
                        <p class="card-text"><?= esc($users->unit_kerja) ?></p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Reset Password</h5>
                        <p class="card-text">Autentikasi reset password akan dikirimkan ke email Anda.</p>

                        <form action="<?= url_to('forgot') ?>" method="post" id="forgotPasswordForm">
                            <?= csrf_field() ?>
                            <input type="email"
                                class="form-control <?php if (session('errors.email')): ?>is-invalid<?php endif ?>"
                                name="email" aria-describedby="emailHelp" placeholder="<?= lang('Auth.email') ?>">
                            <div class="invalid-feedback">
                                <?= session('errors.email') ?>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="emailSentModal" tabindex="-1" aria-labelledby="emailSentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div>
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h5 class="modal-title mb-3" id="emailSentModalLabel">Email Terkirim</h5>
                <p>Email untuk mengubah kata sandi telah dikirim. Silahkan cek email Anda.</p>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none"
    style="background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('forgotPasswordForm');
        const modal = new bootstrap.Modal(document.getElementById('emailSentModal'));
        const loadingOverlay = document.getElementById('loadingOverlay');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            loadingOverlay.classList.remove('d-none');

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
            })
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.classList.add('d-none');
                    if (data.success) {
                        modal.show();
                    } else {
                        alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                })
                .catch(error => {
                    loadingOverlay.classList.add('d-none');
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
        });
    });
</script>

<?= $this->endSection() ?>