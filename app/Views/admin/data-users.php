<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Data Users</title>

<div class="page-heading">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Users</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Users</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                Daftar Pengguna
            </div>
            <div class="card-body">
                <table class="table table-striped" id="tableDataUsers">

                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Fullname</th>
                            <th>Unit Organisasi</th>
                            <th>Unit Kerja</th>
			    <th>Active</th>
			    <th>Role</th>
                            <th>Edit</th>
                            <!-- <th>Status</th> -->
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (isset($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <td><?= esc($user->id) ?></td>
                                <td><?= esc($user->email) ?></td>
                                <td><?= esc($user->username) ?></td>
                                <td><?= esc($user->fullname) ?></td>
                                <td><?= esc($user->unit_organisasi) ?></td>
                                <td><?= esc($user->unit_kerja) ?></td>
				<td class="text-center"><?= esc($user->active) ?></td>
				<td class="fw-bold"><?= esc($user->role) ?></td>

                                <td><?= esc($user->edit) ?>
                                    <button class="btn btn-primary" onclick="confirmRoleAddition(this, <?= $user->id ?>)">Add
                                        Role</button>
                                    <button class="btn btn-danger"
                                        onclick="confirmUserDeletion(this, <?= $user->id ?>)">Delete</button>
                                </td>

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

<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoleModalLabel">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Pilih role:
                <select class="form-select" id="roleSelection">
                    <option value="" class="text-muted" disabled selected></option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addRoleToUser()">Add Role</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Anda yakin ingin menghapus User ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteUser()">Delete</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>

<link rel="stylesheet" href="/assets/vendors/simple-datatables/style.css">

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>

<script src="/assets/vendors/simple-datatables/simple-datatables.js"></script>

<script src="<?= base_url() ?>/assets/js/datausers.js"></script>

<script>
    let tableDataUsers = document.querySelector('#tableDataUsers');
    let dataTable = new simpleDatatables.DataTable(tableDataUsers);
</script>

<?= $this->endSection() ?>
