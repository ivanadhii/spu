<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="assets2/pupr.ico" />

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/bootstrap.css">

    <link rel="stylesheet" href="/assets/vendors/iconly/bold.css">

    <link rel="stylesheet" href="/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">

    <?= $this->renderSection('styles') ?>

    <link rel="shortcut icon" href="/assets/images/favicon.svg" type="image/x-icon">
</head>

<body>
    <?php
    $uri = service('uri')->getSegments();
    $uri1 = $uri[0] ?? 'index';
    $uri2 = $uri[1] ?? '';
    $uri3 = $uri[2] ?? '';
    ?>

    <header class="mb-3">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <?php if (in_groups('admin')): ?>
                            <li class="sidebar-item active <?= ($uri1 == 'dashboard') ? 'active' : '' ?>">
                                <a class='sidebar-link' href="<?= base_url('dashboard') ?>">
                                    <i class="bi bi-grid-fill"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>

                            <hr>

                            <li class="sidebar-item <?= ($uri1 == 'createnew') ? 'active' : '' ?>">
                                <a class='sidebar-link' href="<?= base_url('createnew'); ?>">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    <span>Create New</span>
                                </a>
                            </li>

                            <li class="sidebar-item <?= ($uri1 == 'datausers') ? 'active' : '' ?>">
                                <a class='sidebar-link' href="<?= base_url('datausers'); ?>">
                                    <i class="bi bi-list-ul"></i>
                                    <span>Data Users</span>
                                </a>
                            </li>

                            <li class="sidebar-item <?= ($uri1 == 'linkshistory') ? 'active' : '' ?>">
                                <a class='sidebar-link' href="<?= base_url('linkshistory'); ?>">
                                    <i class="bi bi-link"></i>
                                    <span>Links History</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_groups('user')): ?>
                            <li class="sidebar-item active <?= ($uri1 == 'createnew') ? 'active' : '' ?>">
                                <a class='sidebar-link' href="<?= base_url('createnew'); ?>">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    <span>Create New</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <hr>

                        <li class="sidebar-item <?= ($uri1 == 'homepage') ? 'active' : '' ?>">
                            <a class='sidebar-link' href="<?= base_url('homepage'); ?>">
                                <i class="bi bi-house-door-fill"></i>
                                <span>Home</span>
                            </a>
                        </li>

                        <li class="sidebar-item <?= ($uri1 == 'myurl') ? 'active' : '' ?>">
                            <a class='sidebar-link' href="<?= base_url('myurl'); ?>">
                                <i class="bi bi-textarea"></i>
                                <span>My Url</span>
                            </a>
                        </li>

                        <li class="sidebar-item <?= ($uri1 == 'profile') ? 'active' : '' ?>">
                            <a class='sidebar-link' href="<?= base_url('profile'); ?>">
                                <i class="bi bi-person-square"></i>
                                <span>Profile</span>
                            </a>
                        </li>

                        <hr>

                        <li class="sidebar-item <?= ($uri1 == 'logout') ? 'active' : '' ?>">
                            <a class='sidebar-link' href="<?= base_url('logout'); ?>">
                                <i class="bi bi-arrow-left-square-fill"></i>
                                <span>Logout</span>
                            </a>
                        </li>

                    </ul>
                </div>

                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>

            </div>
        </div>
    </header>

    <?= $this->renderSection('content') ?>

    <script src="/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>

    <?= $this->renderSection('javascript') ?>

    <script src="<?= base_url() ?>/assets/js/main.js"></script>

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

</body>

</html>
