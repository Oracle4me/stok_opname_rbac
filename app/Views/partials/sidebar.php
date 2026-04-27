<!-- ========== Left Sidebar Start ========== -->
<?php helper('permission'); ?>
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                
                <!-- Dashboard -->
                <li>
                    <a href="<?= base_url('admin/dashboard') ?>">
                        <i class="bx bxs-dashboard"></i> 
                        <span>Dashboard</span>
                    </a>
                </li>

                <?php if (
                    can('role_view') ||
                    can('user_view') ||
                    can('permission_view')
                ): ?>
                    
                    <li class="menu-title">Administrator</li>

                    <?php if (can('role_view')): ?>
                    <li>
                        <a href="<?= base_url('admin/role/man_roles') ?>">
                            <i class="bx bxs-user-circle"></i>
                            <span>Manajemen Role</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if (can('user_view')): ?>
                    <li>
                        <a href="<?= base_url('admin/users/man_users') ?>">
                            <i class="bx bx-user"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if (can('permission_view')): ?>
                    <li>
                        <a href="<?= base_url('admin/permissions/man_hak_akses') ?>">
                            <i class="bx bx-key"></i>
                            <span>Manajemen Hak Akses</span>
                        </a>
                    </li>
                    <?php endif; ?>

                <?php endif; ?>
                
                <?php if (can('barang_view')): ?>
                    <li class="menu-title">Master Data</li>
                    
                    <!-- Master Barang -->
                    <?php if (can('barang_view')): ?>
                    <li> 
                        <a href="<?= base_url('admin/barang/master_barang') ?>">
                            <i class="bx bx-box"></i>
                            <span>Master Barang</span>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (
                    can('stok_view') ||
                    can('stok_masuk_view') ||
                    can('distribusi_barang_view')
                ): ?>

                    <li class="menu-title">Transaksi</li>
                    <!-- Stock Opname -->
                    <?php if (can('stok_view')): ?>
                    <li>
                        <a href="<?= base_url('admin/stok/opname') ?>">
                            <i class="bx bx-clipboard"></i>
                            <span>Stock Opname</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- Stock Opname -->
                    <?php if (can('stok_masuk_view')): ?>
                        <li>
                            <a href="<?= base_url('admin/stok-masuk') ?>">
                                <i class="bx bx-log-in"></i>
                                <span>Stock Masuk</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (can('distribusi_barang_view')): ?>
                        <li>
                            <a href="<?= base_url('admin/distribusi') ?>">
                                <i class="bx bx-log-out"></i>
                                <span>Distribusi Barang</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (can('laporan_view')): ?>
                    <li class="menu-title">Laporan</li>
                    <!-- Laporan -->
                    <?php if (can('laporan_view')): ?>
                    <li>
                        <a href="<?= base_url('admin/laporan') ?>">
                            <i class="bx bx-bar-chart-alt-2"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->