<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>/assets/pupr.ico" />
    <title>Masukkan Kata Sandi - PUPR Shortlink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: #00215E;
            color: white;
            border-bottom: none;
            padding: 20px;
        }

        .card-body {
            padding: 40px;
        }

        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
        }

        .btn-primary {
            background-color: #00215E;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #001845;
            transform: translateY(-2px);
        }

        .card-footer {
            background-color: #f8f9fa;
            border-top: none;
            font-size: 0.9rem;
        }

        .lock-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #00215E;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card text-center">
                    <div class="card-header">
                        <h4 class="mb-0">Shortlink Encrypted</h4>
                    </div>
                    <div class="card-body">
                        <i class="bi bi-shield-lock-fill lock-icon pulse"></i>
                        <h5 class="card-title mb-4">
                            <?= 'https://s.pu.go.id/' . esc($shortened_url); ?>
                        </h5>
                        <p class="text-muted mb-4">Masukkan kata sandi untuk akses shortlink</p>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= session()->getFlashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('shortener/decrypt') ?>" method="post">
                            <input type="hidden" name="shortened_url" value="<?= esc($shortened_url); ?>">
                            <div class="form-group mb-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" class="form-control" name="password" required
                                        placeholder="Masukkan kata sandi">
                                </div>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="bi bi-unlock-fill me-2"></i>Buka Akses
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-muted">
                        <small>
                            Pusat Data dan Teknologi Informasi<br>
                            &copy; 2024 Kementerian Pekerjaan Umum dan Perumahan Rakyat
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
