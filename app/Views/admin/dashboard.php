<?php $session = \Config\Services::session(); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h5>Dashboard</h5>

<div class="row">

    <!-- Total Barang -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <span class="text-muted">Total Barang</span>
                <h4>
                    <span class="counter-value" data-target="<?= $totalBarang ?>">0</span>
                </h4>
            </div>
        </div>
    </div>

    <!-- Total Stok -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <span class="text-muted">Total Stok</span>
                <h4>
                    <span class="counter-value" data-target="<?= $totalStok ?>">0</span>
                </h4>
            </div>
        </div>
    </div>

    <!-- Stok Masuk -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <span class="text-muted">Stok Masuk</span>
                <h4>
                    <span class="counter-value" data-target="<?= $totalMasuk ?>">0</span>
                </h4>
            </div>
        </div>
    </div>

    <!-- Distribusi -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <span class="text-muted">Distribusi</span>
                <h4>
                    <span class="counter-value" data-target="<?= $totalKeluar ?>">0</span>
                </h4>
            </div>
        </div>
    </div>

</div>

<div class="row mt-3">

    <!-- CHART -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h5>Grafik Stok Masuk vs Keluar</h5>
                <div id="chart-stok"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-danger text-center">
                        <i class="bx bx-error-circle me-1"></i> Stok Menipis
                    </h5>
                    <span class="badge bg-danger">
                        <?= count($stokMenipis) ?> item
                    </span>
                </div>

                <?php if (empty($stokMenipis)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bx bx-check-circle" style="font-size: 40px;"></i>
                        <p class="mt-2 mb-0">Semua stok aman</p>
                    </div>
                <?php else: ?>

                    <div style="max-height: 250px; overflow-y:auto;">
                        <?php foreach ($stokMenipis as $s): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">

                                <div>
                                    <div class="fw-semibold"><?= $s['nama'] ?></div>
                                    <small class="text-muted">Perlu restock</small>
                                </div>

                                <span class="badge bg-danger">
                                    <?= $s['qty'] ?>
                                </span>

                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

</div>

<div class="row mt-3">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h5>Aktivitas Transaksi</h5>

                <div>
                    <table id="table-transaksi" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Tipe</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('scripts/dashboard') ?>
<?= $this->endSection() ?>