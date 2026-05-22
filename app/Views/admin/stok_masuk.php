<?php $session = \Config\Services::session(); ?>
<?php helper('permission'); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm" style="border-radius: 3px;">
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-custom nav-justified">
            <?php if (can('stok_masuk_view')): ?>
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-data">
                        Data Stok Masuk
                    </a>
                </li>
            <?php endif; ?>
            <?php if (can('stok_masuk_create')): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-form">
                        Input Stok Masuk
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content p-3">
            <div class="tab-pane active" id="tab-data">
                <div class="table-responsive">
                    <table id="datatables" class="table table-theme nowrap w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Qty</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (can('stok_masuk_create')): ?>
                <div class="tab-pane" id="tab-form">
                    <form id="form">
                        <input type="hidden" class="form-control" id="id" name="id" required>
                        <?= csrf_field(); ?>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Tanggal</label>
                                <input type="date" id="tanggal" class="form-control" required>
                            </div>

                            <div class="col-md-8">
                                <label>Keterangan</label>
                                <input type="text" id="keterangan" class="form-control" placeholder="Contoh: Pembelian barang">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Barang</label>
                                <select id="barang_id" class="form-select select2" required>
                                    <option value="">Pilih Barang</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Qty</label>
                                <input type="number" id="qty" class="form-control" placeholder="Masukkan jumlah" required min="1">
                            </div>
                        </div>

                        <div class="d-flex flex-row gap-2 justify-content-end align-items-center">
                            <button class="btn btn-dark" type="button" id="btn_reset">
                                <i class="fa fa-undo me-1"></i>Reset
                            </button>
                            <?php if (can('stok_masuk_create')): ?>
                                <button type="submit" class="btn btn-primary" id="btn_save">
                                    <i class="fa fa-save me-1"></i>Simpan
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('scripts/stok_masuk') ?>
<?= $this->endSection() ?>