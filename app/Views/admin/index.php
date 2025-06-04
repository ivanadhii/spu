<?= $this->extend('admin/layouts/app') ?>

<?= $this->section('content') ?>

<title>Dashboard</title>

<div class="page-content">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>

    <div class="page-heading">
        <h3>Dashboard</h3>
    </div>

    <section class="row">
        <div class="col-12 col-lg-15">
            <div class="row">

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon purple">
                                        <i class="iconly-boldShow"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">User Visit</h6>
                                    <h6 class="font-extrabold mb-0">
                                        <?= isset($auth_logins) ? esc($auth_logins) : 'Data not available' ?>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon blue">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Total Users</h6>
                                    <h6 class="font-extrabold mb-0">
                                        <?= isset($users) ? esc(count($users)) : 'Data not available' ?>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

		<div class="col-6 col-lg-3 col-md-6">
                    <div id="inactive-users-card" class="card clickable-card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon red">
                                        <i class="iconly-boldAdd-User"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Inactive User</h6>
                                    <h6 class="font-extrabold mb-0">
                                        <?= isset($inactiveUsersCount) ? esc($inactiveUsersCount) : 'Data not available' ?>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-3 py-4-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stats-icon green">
                                        <i class="iconly-boldBookmark"></i>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h6 class="text-muted font-semibold">Total Shortlink</h6>
                                    <h6>
                                        <?= isset($total_links) ? esc($total_links) : 'Data not available' ?>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

	    <section class="section">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>User Per Bulan</h4>
                            </div>
                            <div class="card-body">
                                <div id="user-per-month-chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Shortlink Per Bulan</h4>
                            </div>
                            <div class="card-body">
                                <div id="urls-per-month-chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>User Per Minggu</h4>
                            </div>
                            <div class="card-body">
                                <div id="user-chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Shortlink Per Minggu</h4>
                            </div>
                            <div class="card-body">
                                <div id="url-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Unit Organisasi</h4>
                        </div>
                        <div class="card-body">
                            <div id="unit-organisasi-chart"></div>
                        </div>
                    </div>

			<div class="card">
                        <div class="card-header">
                            <h4>Unit Kerja</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="unitOrganisasiSelect" class="form-label">Pilih Unit Organisasi:</label>
                                <select id="unitOrganisasiSelect" class="form-select">
                                    <option value="" class="text-muted" disabled selected>Pilih Unit Organisasi</option>
                                    <?php foreach ($unitOrganisasi as $org => $units): ?>
                                        <option value="<?= esc($org, 'attr') ?>"><?= esc($org) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div id="unitKerjaContainer" style="display: none;">
                                <table id="unitKerjaTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Unit Kerja</th>
                                            <th>Jumlah Pengguna</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Logs</h4>
                    </div>
                    <div class="card-body">
                        <form method="get" action="">
                            <div class="form-group">
                                <label for="log_date">Select Date:</label>
                                <input type="date" id="log_date" name="log_date" class="form-control"
                                    value="<?= esc($selectedDate) ?>">
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">View Logs</button>
                        </form>
                        <div id="log-level-chart" class="mt-3"></div>
                        <div class="log-container mt-3">
                            <pre id="log-content" class="mt-3">
                                    <?= esc($logs) ?>
                                </pre>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
	</div>
    </section>
</div>

<div class="modal fade" id="inactiveUsersModal" tabindex="-1" aria-labelledby="inactiveUsersModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inactiveUsersModalLabel">Inactive User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>Fullname</th>
                            <th>Unit Organisasi</th>
                            <th>Unit Kerja</th>
                        </tr>
                    </thead>
                    <tbody id="inactiveUsersTableBody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>

<script>
    var countUnitOrganisasi = <?= json_encode($countUnitOrganisasi) ?>;
    var userPerMonth = <?= json_encode($userPerMonth) ?>;
    var urlsPerMonth = <?= json_encode($urlsPerMonth) ?>;
    var userDataMonthly = <?= json_encode($userDataMonthly) ?>;
    var urlDataMonthly = <?= json_encode($urlDataMonthly) ?>;
    var unitKerjaData = <?= json_encode($unitOrganisasi) ?>;
    var countUnitKerja = <?= json_encode($countUnitKerja) ?>;
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logContent = document.getElementById('log-content');
        const lines = logContent.innerText.split('\n');
        logContent.innerHTML = lines.map((line, index) => `<span>${index + 1}</span> ${line}`).join('\n');

        var logLevelOptions = {
            series: [{
                name: 'Logs',
                data: [
                    <?= esc($logCounts['emergency']) ?>,
                    <?= esc($logCounts['alert']) ?>,
                    <?= esc($logCounts['critical']) ?>,
                    <?= esc($logCounts['error']) ?>,
                    <?= esc($logCounts['warning']) ?>,
                    <?= esc($logCounts['notice']) ?>,
                    <?= esc($logCounts['info']) ?>,
                    <?= esc($logCounts['debug']) ?>
                ]
            }],
            chart: {
                type: 'area',
                height: 350
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                categories: ['Emergency', 'Alert', 'Critical', 'Error', 'Warning', 'Notice', 'Info', 'Debug'],
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            }
        };
        var logLevelChart = new ApexCharts(document.querySelector("#log-level-chart"), logLevelOptions);
        logLevelChart.render();
    });
</script>

<script src="/assets/vendors/apexcharts/apexcharts.js"></script>
<script src="/assets/js/pages/dashboard.js"></script>

<?= $this->endSection() ?>
