<?php
$uri = service('uri');
$uri1 = $uri->getSegment(1) ?: 'index';
?>

<nav class="navbar navbar-expand-lg bg-dark sticky-top custom-navbar">
    <div class="container-fluid">
        <a href="<?= base_url('/'); ?>" class="navbar-brand">
            <span class="text-primary">PU</span>
            <span class="text-white ms-1">Shortlink</span>
        </a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
            aria-label="Toggle navigation">
            Menu
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarResponsive">
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item <?= ($uri1 == 'homepage') ? 'active' : '' ?>">
                    <a class='nav-link <?= ($uri1 == 'homepage') ? 'text-warning' : 'text-white' ?>'
                        href="<?= base_url('homepage'); ?>">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= base_url('myurl'); ?>">MyUrl</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= base_url('profile'); ?>">Profile</a>
                </li>
                <?php if (in_groups('admin')): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= base_url('dashboard'); ?>">Dashboard</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#" data-bs-toggle="modal"
                        data-bs-target="#logoutModal">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a class="btn btn-danger" href="<?= base_url('logout'); ?>">Ya</a>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-navbar {
        border-radius: 55px;
        margin-top: 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .navbar-brand {
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .navbar-toggler {
        border: none;
    }

    @media (max-width: 991.98px) {
        .custom-navbar {
            border-radius: 0;
            margin-top: 0;
            max-width: 100%;
        }

        .navbar-collapse {
            background-color: #212529;
            padding: 1rem;
            border-radius: 0 0 15px 15px;
        }

        .navbar-nav {
            text-align: center;
        }
    }
</style>
