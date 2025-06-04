<?= $this->extend('admin/layouts/app'); ?>

<?= $this->section('content'); ?>

<title>My Url</title>

<div class="page-content">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>

    <div class="page-heading">
        <h2 class="my-4"><b>My Url</b></h2>
        <p>QR Code / Shortlink / Original URL</p>
    </div>

    <section class="row">
        <div class="col-12 col-lg-15">
            <div class="row">

                <form method="GET" action="<?= base_url('myurl') ?>">
                    <div class="row mb-4 align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="<?= esc($start_date ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="<?= esc($end_date ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="encryption" class="form-label">Filter by:</label>
                            <select class="form-control" id="encryption" name="encryption"
                                onchange="this.form.submit()">
                                <option value="general" <?= (isset($_GET['encryption']) && $_GET['encryption'] == 'general') ? 'selected' : '' ?>>Semua</option>
                                <option value="encrypted" <?= (isset($_GET['encryption']) && $_GET['encryption'] == 'encrypted') ? 'selected' : '' ?>>Encryption</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>

                <?php if (!empty($links)): ?>
                    <?php $links = array_reverse($links); ?>
                    <?php foreach ($links as $link): ?>
                        <div class="card mb-3" id="card-<?= esc($link['id']) ?>">
                            <div class="expires-text">
				<span class="countdown"
                                    data-expiry="<?= $link['expiry'] === 'Tanpa Batasan Periode Waktu' ? '' : $link['expired_at'] ?>"
                                    data-expiry-type="<?= $link['expiry'] ?>">
                                </span>
                            </div>
                            <div class="row g-0">

				<div class="col-md-2 d-flex align-items-center justify-content-center">
                                    <div class="qr-code-container" style="width: 100%; max-width: 200px;">
                                        <canvas id="qrCode_<?= esc($link['id']) ?>"
                                            onclick="showQRCodeModal('<?= esc($link['shortened_url']) ?>')"
                                            data-url="<?= esc($link['shortened_url']) ?>"
                                            style="width: 100%; height: auto; cursor: pointer;">
                                        </canvas>
                                    </div>
                                </div>

                                <div class="col-md-10">
                                    <div class="card-body">
                                        <!-- <h5 class="card-title"><a href="<?= esc($link['shortened_url']) ?>" target="_blank"
                                                id="shortened-url-<?= esc($link['id']) ?>"><?= esc($link['shortened_url']) ?></a>
                                        </h5> -->

					<h5 class="card-title">
                                            <a href="<?= esc($link['shortened_url']) ?>" target="_blank"
                                                id="shortened-url-<?= esc($link['id']) ?>"
                                                class="<?= (new DateTime() > new DateTime($link['expired_at'])) ? 'text-muted' : '' ?>">
                                                <?= esc($link['shortened_url']) ?>
                                            </a>
                                        </h5>

                                        <p>Original URL: <a href="<?= esc($link['original_url']) ?>"
                                                target="_blank"><?= esc($link['original_url']) ?></a></p>
                                        <hr>

                                        <p>Dibuat: <?= date('j F Y, H.i', strtotime($link['created_at'])) ?></p>

                                        <p>Kadaluarsa: <?= esc($link['expiry']) ?></p>

                                        <p>Waktu Kadaluarsa:
					    <span class="countdown"
                                                data-expiry="<?= $link['expiry'] === 'Tanpa Batasan Periode Waktu' ? '' : $link['expired_at'] ?>"
                                                data-expiry-type="<?= $link['expiry'] ?>">
                                            </span>
                                        </p>

					<?php if (new DateTime() > new DateTime($link['expired_at'])): ?>
	       				<div class="alert alert-danger">Shortlink Kadalurasa</div>
					<?php endif; ?>

                                        <button class="btn btn-secondary"
                                            onclick="showCopyModal('<?= esc($link['shortened_url']) ?>')">Copy</button>
                                        <button class="btn btn-danger"
                                            onclick="deleteLink(<?= esc($link['id']) ?>)">Delete</button>

                                        <div id="spinner-<?= esc($link['id']) ?>" class="spinner-grow text-primary d-none"
                                            role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>

                                        <?php if (!empty($link['password'])): ?>
                                            <?php
                                            $config = new \Config\Encryption();
                                            $config->key = 'Pupr#book.2024';
                                            $config->driver = 'OpenSSL';
                                            $encrypter = \Config\Services::encrypter($config);
                                            try {
                                                $decrypted_password = $encrypter->decrypt(base64_decode($link['password']));
                                            } catch (\CodeIgniter\Encryption\Exceptions\EncryptionException $e) {
                                                $decrypted_password = 'Decryption failed';
                                            }
                                            ?>
                                            <button class="btn btn-outline-success"
                                                onclick="showPasswordModal('<?= esc($decrypted_password) ?>')">Show
                                                Password</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Shortlink tidak ditemukan!</p>
                <?php endif; ?>

                <div class="orb-infinite-scroll">
                    <div class="results-end">
                        <hr>
                        <p class="orb-typography p stripped center text-center">Batas Akhir Shortlink</p>
                        <hr>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="copyTextModal" tabindex="-1" aria-labelledby="copyTextModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="copyTextModalLabel">Shortlink</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="copyTextInput" readonly>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="copyButton">Copy</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="passwordInput" readonly>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="copyPasswordButton">Copy</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus shortlink ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qrCodeImageInModal" src="" class="img-fluid" alt="QR Code with Logo">
            </div>
            <div class="modal-footer">
                <a id="downloadQRCode" href="#" class="btn btn-primary" download="qr-code.png">Save as PNG</a>
                <button id="emailQRCode" class="btn btn-secondary">Share via Email</button>
                <button id="whatsappQRCode" class="btn btn-success">Share via WhatsApp</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
