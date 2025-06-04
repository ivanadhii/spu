<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Links History</title>

<div class="page-heading">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Links History</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Links History</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header">
                Daftar Shortlink
            </div>
            <div class="card-body">
                <table class="table table-striped" id="tableLinksHistory">

                    <thead>
                        <tr>
			    <th>No.</th>
                            <th>User Id</th>
                            <th>Original Url</th>
                            <th>Alias Url</th>
                            <th>Shortlink</th>
                            <th>Enkripsi</th>
                            <th>Created At</th>
                            <th>Expiry</th>
                            <th>Expired</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (isset($links)): ?>
			    <?php $no = 1; ?>
                            <?php foreach ($links as $link): ?>
                                <tr>
				    <td><?= $no++ ?></td>
                                    <td><?= esc($link['user_id']) ?></td>
                                    <td><?= esc($link['original_url']) ?></td>
                                    <td><?= esc($link['alias_url']) ?></td>
                                    <td><?= esc($link['shortened_url']) ?></td>
                                    <td><?= esc($link['is_encrypted']) ?></td>
                                    <td><?= esc($link['created_at']) ?></td>
                                    <td><?= esc($link['expiry']) ?></td>
                                    <td><?= esc($link['expired_at']) ?></td>
                                    <!-- <td><?= esc($link['updated_at']) ?></td>
                                    <td><?= esc($link['deleted_at']) ?></td> -->
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="/assets/vendors/simple-datatables/style.css">
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="/assets/vendors/simple-datatables/simple-datatables.js"></script>

<script>
    let tableLinksHistory = document.querySelector('#tableLinksHistory');
    let dataTable = new simpleDatatables.DataTable(tableLinksHistory);
</script>

<?= $this->endSection() ?>
