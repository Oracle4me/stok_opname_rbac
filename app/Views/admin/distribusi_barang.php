<?php $session = \Config\Services::session(); ?>
<?php helper('permission'); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <?php if (can('distribusi_barang_view')): ?>
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-data">
                        Data Distribusi
                    </a>
                </li>
            <?php endif; ?>
            <?php if (can('distribusi_barang_create')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= !can('distribusi_barang_view') ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-form">
                        Input Distribusi
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content p-3">
            <?php if (can('distribusi_barang_view')): ?>
                <div class="tab-pane <?= can('distribusi_barang_view') ? 'active' : '' ?>" id="tab-data">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-theme nowrap w-100" >
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th class="text-center">Qty</th>
                                    <th>Team</th>
                                    <th>Area</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (can('distribusi_barang_create')): ?>
                <div class="tab-pane <?= !can('distribusi_barang_view') ? 'active' : '' ?>" id="tab-form">
                    <div class="card-body">
                        <form id="form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Tanggal</label>
                                        <input type="date" id="tanggal" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Team Leader</label>
                                        <input type="text" id="team_leader" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Barang</label>
                                        <select id="barang_id" class="form-select" required>
                                            <option value="">Pilih Barang</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Qty</label>
                                        <input type="number" id="qty" class="form-control" required min="1">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Area</label>
                                        <input type="text" id="area" class="form-control" placeholder="Kec. Gayamsari">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Note</label>
                                        <input type="text" id="note" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-dark" type="button" id="btn_reset">
                                    <i class="fa fa-undo me-1"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary" id="btn_save">
                                    <i class="fa fa-save me-1"></i>Simpan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('scripts/distribusi_barang') ?>
<?= $this->endSection() ?>