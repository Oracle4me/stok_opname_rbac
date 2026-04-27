<?php $session = \Config\Services::session(); ?>
<?php helper('permission'); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card shadow-sm" style="border-radius: 3px;">
    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-custom nav-justified">
            <?php if (can('stok_view')): ?>    
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-data">
                        Data Opname
                    </a>
                </li>
            <?php endif; ?>
            <?php if (can('stok_create')): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-form">
                        Stok Opname
                    </a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Datatables Stok Opname  -->
        <div class="tab-content p-3">
            <div class="tab-pane active" id="tab-data">
                <div class="table-responsive">
                    <table id="table-opname-data" class="table table-theme nowrap w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th class="text-center">Status</th>
                                <th>User</th>
                                <?php if (can('stok_edit') || can('stok_final')): ?>
                                    <th class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <?php if (can('stok_create')): ?>
                <div class="tab-pane" id="tab-form">
                    <form id="form-opname">
                        <?= csrf_field(); ?>
                        <input type="hidden" id="id">

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Tanggal</label>
                                <input type="date" id="tanggal" class="form-control" required>
                            </div>
                            <div class="col-md-8">
                                <label>Keterangan</label>
                                <input type="text" id="keterangan" class="form-control" placeholder="Stok opname bulanan">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Pilih Barang</label>
                                <select id="select-barang" class="form-control" style="max-height: 10px !important;"></select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-success" id="btn-add-barang">
                                    <i class="bx bx-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Table Opname -->
                        <div class="table-responsive" style="max-height: 400px; overflow:auto;">
                            <table class="table table-theme">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Barang</th>
                                        <th class="text-center">Stok Sistem</th>
                                        <th class="text-center">Stok Fisik</th>
                                        <th class="text-center">Selisih</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table-opname">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Belum ada barang dipilih
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <?php if (can('stok_draft')): ?>
                                <button type="button" class="btn btn-secondary me-2" id="btn-draft">
                                    Simpan Draft
                                </button>
                            <?php endif; ?>

                            <?php if (can('stok_final')): ?>
                                <button type="button" class="btn btn-primary" id="btn-final">
                                    Finalisasi
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-detail" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Detail Stok Opname</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="mb-3">
            <div class="d-flex">
                <div style="width:150px;"><b>Tanggal</b></div>
                <div style="width:10px;">:</div>
                <div><span id="detail-tanggal"></span></div>
            </div>

            <div class="d-flex">
                <div style="width:150px;"><b>Keterangan</b></div>
                <div style="width:10px;">:</div>
                <div><span id="detail-keterangan"></span></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Stok Sistem</th>
                        <th>Stok Fisik</th>
                        <th>Selisih</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="detail-table"></tbody>
            </table>
        </div>

      </div>

    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('scripts/stok_opname') ?>
<?= $this->endSection() ?>