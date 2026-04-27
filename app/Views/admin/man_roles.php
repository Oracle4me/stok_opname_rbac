<?php $session = \Config\Services::session(); ?>
<?php helper('permission'); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card shadow-sm" style="border-radius: 3px;">
    <div class="card-body">
        <ul id="tab" class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <?php if (can('role_view')): ?>
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-data" role="tab" aria-selected="false">
                        <span class="d-block d-sm-none"><i class="fas fa-table"></i></span>
                        <span class="d-none d-sm-block">Data</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if (can('role_create')): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-form" role="tab" aria-selected="true">
                        <span class="d-block d-sm-none"><i class="fab fa-wpforms"></i></span>
                        <span id="formTitle" class="d-none d-sm-block" data-add="Tambah" data-edit="Update">Tambah</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <?php if (can('role_create')): ?>
            <div class="tab-content p-3 text-muted">
                <div class="tab-pane active" id="tab-data" role="tabpanel">
                    <hr>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-theme nowrap w-100">
                                <thead>
                                    <tr>
                                        <th><span>#</span></th>
                                        <th><span>Role</span></th>
                                        <?php if (can('role_edit') || can('role_delete')): ?>
                                            <th style="width: 200px;">Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-form" role="tabpanel">
                    <div class="row" id="alert_user_exist"></div>
                    <div class="card-body">
                        <form id="form">
                            <input type="hidden" class="form-control" id="id" name="id" required>
                            <?= csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label>Role User</label>
                                        <input type="text" class="form-control" id="nama" name="nama" autocomplete="one-time-code" placeholder="Admin, Staff, dll" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label>Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Jika nonaktif, role tidak bisa digunakan untuk user baru
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex flex-row gap-2 justify-content-end align-items-center">
                                <button class="btn btn-dark" type="reset" id="btn_reset">
                                    <i class="fa fa-undo me-1"></i>Reset
                                </button>
                                <?php if (can('role_create')): ?>
                                    <button type="submit" class="btn btn-primary" id="btn_save">
                                        <i class="fa fa-save me-1"></i>Simpan
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('scripts/role') ?>
<?= $this->endSection() ?>