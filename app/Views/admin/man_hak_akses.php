<?php $session = \Config\Services::session(); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm" style="border-radius: 3px;">
    <div class="card-body">

        <h5 class="mb-4">Manajemen Hak Akses</h5>

        <div class="mb-4">
            <label class="form-label">Pilih Role</label>
            <select id="role_select" class="form-select">
                <option value="">Pilih Role</option>
            </select>
        </div>

        <div id="permission_container" class="mt-3">
            <div class="text-muted text-center">
                Pilih role terlebih dahulu
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<?= $this->include('scripts/permission') ?>
<?= $this->endSection() ?>
