<?php $session = \Config\Services::session(); ?>
<?php helper('permission'); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm" style="border-radius: 3px;">
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <?php if (can('barang_view')): ?>
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-data">
                        <span>Data</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (can('barang_create')): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-form">
                        <span id="formTitle" class="d-none d-sm-block" data-add="Tambah" data-edit="Update">Tambah</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content p-3">
            <div class="tab-pane active" id="tab-data">
                <hr>
                <div class="table-responsive">
                    <table id="datatable" class="table table-theme nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <?php if (can('barang_edit') || can('barang_delete')): ?>
                                    <th class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <?php if (can('barang_create')): ?>
                <div class="tab-pane" id="tab-form">
                    <form id="form">
                        <?= csrf_field(); ?>
                        <input type="hidden" class="form-control" id="id" name="id" required>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" required >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Kode Barang</label>
                                    <input type="text" class="form-control" name="code" id="code" placeholder="BRG001" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Nama Barang</label>
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama barang" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Kategori</label>
                                    <select class="form-control" name="kategori_id" id="kategori_id" required>
                                        <option value="">Pilih Kategori</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Satuan</label>
                                    <select class="form-control" name="satuan_id" id="satuan_id" required>
                                        <option value="">Pilih Satuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
								<button class="btn btn-dark" type="reset" id="btn_reset">
									<i class="fa fa-undo me-1"></i>Reset
								</button>
                            <?php if (can('barang_create')): ?>
                                <button type="submit" class="btn btn-primary">
                                    Simpan
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
<?= $this->include('scripts/master_barang') ?>
<?= $this->endSection() ?>