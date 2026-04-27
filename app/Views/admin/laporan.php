<?php $session = \Config\Services::session(); ?>
<?php helper('permission'); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card shadow-sm">
    <div class="card-body">
        <?php $first = true; ?>
        <ul class="nav nav-tabs nav-tabs-custom nav-justified">
            <?php if (can('laporan_rekap_view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $first ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-rekap">
                        Rekap Stok
                    </a>
                </li>
                <?php $first = false; ?>
            <?php endif; ?>

            <?php if (can('laporan_detail_view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $first ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-detail">
                        Detail Distribusi
                    </a>
                </li>
                <?php $first = false; ?>
            <?php endif; ?>

            <?php if (can('laporan_mutasi_view')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $first ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-mutasi">
                        Mutasi Stok
                    </a>
                </li>
                <?php $first = false; ?>
            <?php endif; ?>
        </ul>

        <div class="tab-content p-3">
            <?php $first = true; ?>
            <?php if (can('laporan_rekap_view')): ?>
                <div class="tab-pane <?= $first ? 'active' : '' ?>" id="tab-rekap">
                    <table class="table table-theme w-100" id="table-rekap">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="text-center">Stok Awal</th>
                                <th class="text-center">Penggunaan</th>
                                <th class="text-center">Tambahan</th>
                                <th class="text-center">Sisa</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <?php $first = false; ?>
            <?php endif; ?>

            <?php if (can('laporan_detail_view')): ?>
                <div class="tab-pane <?= $first ? 'active' : '' ?>" id="tab-detail">
                    <table class="table table-theme w-100" id="table-detail">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th>Team Leader</th>
                                <th class="text-center">Qty</th>
                                <th>Area</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <?php $first = false; ?>
            <?php endif; ?>

            <?php if (can('laporan_mutasi_view')): ?>
                <div class="tab-pane <?= $first ? 'active' : '' ?>" id="tab-mutasi">
                    <table class="table table-theme w-100" id="table-mutasi">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Stok Sebelum</th>
                                <th class="text-center">Selisih</th>
                                <th class="text-center">Stok Sesudah</th>
                                <th>User</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<?= $this->include('scripts/laporan') ?>
<?= $this->endSection() ?>