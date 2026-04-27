<?php $session = \Config\Services::session(); ?>
<?php helper('permission'); ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card shadow-sm" style="border-radius: 3px;">
	<div class="card-body">
		<ul id="tab" class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
			<?php if (can('user_view')): ?>
				<li class="nav-item">
					<a class="nav-link active" data-bs-toggle="tab" href="#tab-data" role="tab" aria-selected="false">
						<span class="d-block d-sm-none"><i class="fas fa-table"></i></span>
						<span class="d-none d-sm-block">Data</span>
					</a>
				</li>
			<?php endif; ?>
			<?php if (can('user_create')): ?>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#tab-form" role="tab" aria-selected="true">
						<span class="d-block d-sm-none"><i class="fab fa-wpforms"></i></span>
						<span id="formTitle" class="d-none d-sm-block" data-add="Tambah" data-edit="Update">Tambah</span>
					</a>
				</li>
			<?php endif; ?>
		</ul>
		<div class="tab-content p-3 text-muted">
			<div class="tab-pane active" id="tab-data" role="tabpanel">
				<hr>
				<div class="row">
					<div class="table-responsive">
						<table id="datatable" class="table table-theme nowrap w-100">
							<thead>
								<tr>
									<th><span>#</span></th>
									<th><span>Username</span></th>
									<th><span>Nama User</span></th>
									<th><span>Email User</span></th>
									<th><span>Role</span></th>
									<?php if (can('user_edit') || can('user_delete')): ?>
										<th class="text-center">Aksi</th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
			<?php if (can('user_create')): ?>
				<div class="tab-pane" id="tab-form" role="tabpanel">
					<div class="card-body">
						<form id="form">
							<input type="hidden" class="form-control" id="id" name="id" required>
							<?= csrf_field(); ?>
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label>Username</label>
										<input type="text" class="form-control" id="username" name="username" autocomplete="one-time-code" placeholder="Username" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label>Nama</label>
										<input type="text" class="form-control" id="nama" name="nama" autocomplete="one-time-code" placeholder="Nama lengkap" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label>Email</label>
										<input type="email" class="form-control" id="email" name="email" autocomplete="one-time-code" placeholder="Email user" required>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label for="user_web_password">Password</label>
										<div class="input-group auth-pass-inputgroup">
											<input type="password" class="form-control" id="password" name="password" autocomplete="one-time-code" aria-describedby="password-addon" placeholder="Password untuk login" required>
											<button class="btn btn-light ms-0" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label>Role</label>
										<select class="form-control" name="role_id" id="select_role" style="max-height: 10px !important;" required></select>
									</div>
								</div>

								<div class="col-md-6">
									<div class="mb-3">
										<label>Status</label>
										<div class="form-check form-switch">
											<input class="form-check-input" type="checkbox" id="stat" name="status" value="1" checked>
											<label class="form-check-label" for="status">
												Aktif
											</label>
										</div>
										<small class="text-muted">
											Jika nonaktif, user tidak dapat login ke sistem
										</small>
									</div>
								</div>
							</div>
							<div class="d-flex flex-row gap-2 justify-content-end align-items-center">
								<button class="btn btn-dark" type="reset" id="btn_reset">
									<i class="fa fa-undo me-1"></i>Reset
								</button>
								<?php if (can('user_create')): ?>
									<button type="submit" class="btn btn-primary" id="btn_save">
										<i class="fa fa-save me-1"></i>Simpan
									</button>
								<?php endif; ?>
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
<?= $this->include('scripts/user') ?>
<?= $this->endSection() ?>