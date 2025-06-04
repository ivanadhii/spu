<?= $this->extend('auth/layouts/index'); ?>

<?= $this->section('content') ?>

<div class="container-reset-password">
    <h1 class="auth-title text-center">Ubah Kata Sandi</h1>
    <p class="auth-subtitle mb-5 text-center">Silahkan masukkan Email dan Kata Sandi Baru Anda</p>

    <?php if (session('error')): ?>
        <div class="alert alert-danger"><?= session('error') ?></div>
    <?php endif ?>

    <form action="<?= url_to('reset-password') ?>" method="post" class="users">
        <?= csrf_field() ?>

        <div class="form-floating mb-3">
            <input type="text" class="form-control <?php if (session('errors.token')): ?>is-invalid<?php endif ?>"
                name="token" placeholder="<?= lang('Auth.token') ?>" value="<?= old('token', $token ?? '') ?>" hidden>
            <label for="floatingInput"><?= lang('Auth.token') ?></label>
            <div class="invalid-feedback">
                <?= session('errors.token') ?>
            </div>
        </div>

        <div class="form-floating mb-3">
            <input type="email" class="form-control <?php if (session('errors.email')): ?>is-invalid<?php endif ?>"
                name="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>">
            <label for="floatingInput"><?= lang('Auth.email') ?></label>
            <div class="invalid-feedback">
                <?= session('errors.email') ?>
            </div>
        </div>

        <div class="form-floating mb-3">
            <input type="password"
                class="form-control <?php if (session('errors.password')): ?>is-invalid<?php endif ?>" name="password"
                placeholder="<?= lang('Auth.newPassword') ?>">
            <label for="floatingInput">Masukkan Kata Sandi Baru</label>
            <div class="invalid-feedback">
                <?= session('errors.password') ?>
            </div>
        </div>

        <div class="form-floating mb-3">
            <input type="password"
                class="form-control <?php if (session('errors.pass_confirm')): ?>is-invalid<?php endif ?>"
                name="pass_confirm" placeholder="<?= lang('Auth.newPasswordRepeat') ?>">
            <label for="floatingInput">Ulangi Kata Sandi</label>
            <div class="invalid-feedback">
                <?= session('errors.pass_confirm') ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">
            Ubah Kata Sandi
        </button>
    </form>

    <div class="text-center mt-5 text-lg">
        <p class='text-gray-600'>Ingat kata sandi Anda? <a href="<?= url_to('login') ?>" class="font-bold">Masuk</a></p>
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


<div class="modal fade" id="resetSuccessModal" tabindex="-1" aria-labelledby="resetSuccessModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div>
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h5 class="modal-title mb-3" id="resetSuccessModalLabel">Berhasil</h5>
                <p>Perubahan kata sandi telah berhasil dilakukan. Silahkan Login dengan kata sandi baru.</p>
                <button type="button" class="btn btn-primary"
                    onclick="window.location.href='<?= url_to('login') ?>'">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="resetFailedModal" tabindex="-1" aria-labelledby="resetFailedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div>
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                </div>
                <h5 class="modal-title mb-3" id="resetFailedModalLabel">Gagal</h5>
                <p id="resetFailedMessage"></p>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>
