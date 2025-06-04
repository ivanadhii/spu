<?= $this->extend('user/layouts/index'); ?>

<?= $this->section('content'); ?>

<title>Home</title>

<div class="page-content">

    <div class="page-heading" data-aos="fade-down">
        <h2 class="my-4"><b>PU Shortlink</b></h2>
        <p>Permudah akses informasi dengan URL yang lebih singkat dan mudah diingat. <b>Efisiensi dalam setiap
                komunikasi
                digital.</b></p>
    </div>

    <section class="row">
        <div class="col-10 col-lg-15">
            <div class="card p-4 animate__animated animate__fadeInUp" data-aos="fade-up">

                <form id="shortenForm" action="<?= base_url('/shortener/shorten'); ?>" method="post" class="links">
                    <?= csrf_field() ?>
		    <input type="hidden" name="csrf_token" value="<?= csrf_hash() ?>">
		    <div style="display:none">
            		<label>Fill This Field</label>
            		<input type="text" name="honeypot" value="">
       		    </div>

		   <div class="mb-4">
                        <label for="original_url" class="form-label fw-bold">Masukkan Original Url</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" class="form-control form-control-lg" id="original_url" name="original_url"
                                placeholder="https://puprtes-my.sharepoint.com/....." required pattern="https?://.+"
                                autocomplete="off">
                        </div>
                    </div>

                    <br>

                    <label for="alias_url" class="form-label fw-bold">Customize URL</label>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-text text-white" style="background-color:#00215E">s.pu.go.id/</span>
                            <input type="text" class="form-control form-control-xl" id="alias_url" name="alias_url"
                                placeholder="Opsional" oninput="checkInput()" autocomplete="off">
                        </div>
                    </div>

                    <br>

		    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="encryption" name="encryption" value="1">
                            <label class="form-check-label fw-bold" for="encryption">Enkripsi URL</label>
                        </div>
                    </div>
                    <div class="mb-4" id="passwordField" style="display:none;">
                        <label for="password" class="form-label fw-bold">Password Enkripsi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-warning text-dark"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control form-control-lg" id="password"
                                name="password_hash" placeholder="Masukkan password untuk enkripsi URL">
                        </div>
                    </div>

                    <br>

                    <label for="expiry" class="form-label fw-bold">Masa Berlaku Shortlink</label>
                    <div class="form-group">
                        <select
                            class="form-select form-control-xl <?php if (session('errors.expiry')): ?>?>is-invalid<?php endif ?>"
                            id="expiry" name="expiry" required>
                            <option value="" class="text-muted" disabled selected>Pilih</option>
                            <option value="1 Minggu">1 Minggu</option>
                            <option value="2 Minggu">2 Minggu</option>
                            <option value="1 Bulan">1 Bulan</option>
			    <option value="Tanpa Batasan Periode Waktu">Tanpa Batasan Periode Waktu</option>
                        </select>
                    </div>

                    <br>

                    <div class="mb-4" id="shortenedLinkGroup" style="display: none;">
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white">s.pu.go.id/</span>
                            <input type="text" class="form-control form-control-lg" id="shortened_url"
                                name="shortenedLinkInput" readonly>
                            <!-- <button type="button" id="copyBtn" class="btn btn-outline-primary">
                                <i class="bi bi-clipboard"></i> Salin
                            </button> -->
                        </div>
                    </div>

		    <div class="cf-turnstile" 
                        data-sitekey="0x4AAAAAAA4EiM4R4PrOsixk"
                        data-callback="handleTurnstileCallback">
                    </div>

                    <br>

                    <div class="form-group">
                        <div class="col">
                            <button type="button" style="background-color: #00215E; border-radius: 40px;" id="createBtn"
                                class="btn btn-primary btn-xl btn-block">Create</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </section>
</div>

<div class="loader-container" id="loaderContainer">
    <div class="spinner-grow text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="modal fade" id="urlModal" tabindex="-1" aria-labelledby="urlModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="urlModalLabel">Shortlink</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid">
                </div>

                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-primary me-2" id="saveAsPngBtn">Simpan Gambar</button>
                    <!-- <button type="button" class="btn btn-secondary me-2" id="shareViaEmailBtn">Share via Email</button>
                    <button type="button" class="btn btn-success" id="shareViaWhatsAppBtn">Share via WhatsApp</button> -->
                </div>

                <hr>

                <div class="input-group mb-3" id="copyBtn">
                    <input type="text" class="form-control" id="modalUrl" readonly draggable="true">
                    <button type="button" id="modalCopyBtn" class="btn btn-secondary" data-bs-toggle="tooltip"
                        title="Copy">
                        <i class="bi bi-clipboard" aria-hidden="true"></i>
                    </button>
                </div>

                <div id="passwordGroup" style="display:none;">
                    <label for="modalPassword" class="form-label">Password:</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="modalPassword" readonly>
                        <button type="button" id="modalPasswordCopyBtn" class="btn btn-secondary"
                            data-bs-toggle="tooltip" title="Copy">
                            <i class="bi bi-clipboard" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="aliasErrorModal" tabindex="-1" aria-labelledby="aliasErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div>
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                </div>
                <h5 class="modal-title mb-3" id="aliasErrorModalLabel">Gagal</h5>
                <div id="aliasErrorMessage"></div>
                <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="virusTotalModal" tabindex="-1" aria-labelledby="virusTotalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div>
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                </div>
                <h5 class="modal-title mb-3" id="virusTotalModalLabel">Peringatan</h5>
                <div>URL yang Anda masukkan terdeteksi tidak aman.</div>
                <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="duplicateAliasModal" tabindex="-1" aria-labelledby="duplicateAliasModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div>
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                </div>
                <h5 class="modal-title mb-3" id="duplicateAliasModalLabel">Peringatan</h5>
                <div id="duplicateAliasModalBody"></div>
                <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="aliasLengthErrorModal" tabindex="-1" aria-labelledby="aliasLengthErrorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div>
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                </div>
                <h5 class="modal-title mb-3" id="aliasLengthErrorModalLabel">Gagal</h5>
                <div id="aliasLengthErrorMessage"></div>
                <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
