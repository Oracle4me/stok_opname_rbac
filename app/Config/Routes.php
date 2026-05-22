<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentication route
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::loginProcess');
$routes->get('logout', 'AuthController::logout');

// Admin
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/chart-stok', 'DashboardController::chartStok');
    $routes->get('dashboard/transaksi', 'DashboardController::transaksi');

    // Manajemen Role
    $routes->group('role', [
        'filter' => 'permission:role_view'
    ], function ($routes) {
        // View
        $routes->get('man_roles', 'RoleController::index');

        // Data
        $routes->get('/', 'RoleController::getData');

        // Select2 Data
        $routes->get('select', 'RoleController::getRoleSelect');

        // Save & update
        $routes->post('save', 'RoleController::save', [
            'filter' => 'permission:role_create'
        ]);

        // Soft delete
        $routes->post('delete/(:num)', 'RoleController::delete/$1', [
            'filter' => 'permission:role_delete'
        ]);
    });

    // Manajemen Users
    $routes->group('users', [
        'filter' => 'permission:user_view'
    ], function ($routes) {

        // View
        $routes->get('man_users', 'UserController::index');

        // Data
        $routes->get('/', 'UserController::getData');

        // Save & update
        $routes->post('save', 'UserController::save', [
            'filter' => 'permission:user_create'
        ]);

        // Soft delete
        $routes->post('delete/(:num)', 'UserController::delete/$1', [
            'filter' => 'permission:user_delete'
        ]);
    });

    // Manajemen Hak Akses
    $routes->group('permissions', [
        'filter' => 'permission:permission_view'
    ], function ($routes) {

        // View 
        $routes->get('man_hak_akses', 'PermissionController::index');

        // Data
        $routes->get('get/(:num)', 'PermissionController::get/$1');

        // Update
        $routes->post('update', 'PermissionController::update', [
            'filter' => 'permission:permission_edit'
        ]);
    });

    // Master Barang
    $routes->group('barang', [
        'filter' => 'permission:barang_view'
    ], function ($routes) {
        // View
        $routes->get('master_barang', 'MasterBarangController::index');

        // Data
        $routes->get('/', 'MasterBarangController::getData');
        $routes->get('(:num)', 'MasterBarangController::get/$1');

        // Save & Update
        $routes->post('save', 'MasterBarangController::save', [
            'filter' => 'permission:barang_create'
        ]);

        // Soft deleted
        $routes->post('delete/(:num)', 'MasterBarangController::delete/$1', [
            'filter' => 'permission:barang_delete'
        ]);

        // Select2
        $routes->get('kategori', 'MasterBarangController::getKategori');
        $routes->get('satuan', 'MasterBarangController::getSatuan');
    });

    // Stok Opname
    $routes->group('stok', [
        'filter' => 'permission:stok_view'
    ], function ($routes) {

        // halaman utama
        $routes->get('opname', 'StokOpnameController::index');
        $routes->get('opname/search-barang', 'StokOpnameController::searchBarang');
        $routes->get('opname/get-barang', 'StokOpnameController::getBarang');

        $routes->get('opname/data', 'StokOpnameController::getData');

        $routes->get('opname/detail/(:num)', 'StokOpnameController::detail/$1');

        $routes->post('opname/save', 'StokOpnameController::save', [
            'filter' => 'permission:stok_create'
        ]);

        $routes->post('opname/delete/(:num)', 'StokOpnameController::delete/$1');
    });

    $routes->group('stok-masuk', [
        'filter' => 'permission:stok_masuk_view'
    ], function ($routes) {

        $routes->get('/', 'StokMasukController::index');
        $routes->get('data', 'StokMasukController::getData');
        $routes->get('data-barang', 'StokMasukController::getBarang');

        $routes->get('detail/(:num)', 'StokMasukController::detail/$1');

        $routes->post('save', 'StokMasukController::save', [
            'filter' => 'permission:stok_masuk_create'
        ]);

        $routes->post('update/(:num)', 'StokMasukController::update/$1', [
            'filter' => 'permission:stok_masuk_edit'
        ]);
        $routes->post('delete/(:num)', 'StokMasukController::delete/$1', [
            'filter' => 'permission:stok_delete'
        ]);
    });

    $routes->group('distribusi', [
        'filter' => 'permission:distribusi_barang_view'
    ], function ($routes) {

        $routes->get('/', 'DistribusiBarangController::index');
        $routes->get('data', 'DistribusiBarangController::getData');

        $routes->get('detail/(:num)', 'DistribusiBarangController::detail/$1');

        $routes->post('save', 'DistribusiBarangController::save', [
            'filter' => 'permission:distribusi_barang_create'
        ]);

        $routes->post('update/(:num)', 'DistribusiBarangController::update/$1', [
            'filter' => 'permission:distribusi_barang_edit'
        ]);
        $routes->post('delete/(:num)', 'DistribusiBarangController::delete/$1', [
            'filter' => 'permission:distribusi_barang_delete'
        ]);
    });

    $routes->group('laporan', [
        'filter' => 'permission:laporan_view'
    ], function ($routes) {

        $routes->get('/', 'LaporanController::index');

        // Data
        $routes->get('rekap', 'LaporanController::rekap');
        $routes->get('detail', 'LaporanController::detail');
        $routes->get('mutasi', 'LaporanController::mutasi');

        // Export
        $routes->get('export/rekap', 'LaporanController::exportRekap');
        $routes->get('export/detail', 'LaporanController::exportDetail');
        $routes->get('export/mutasi', 'LaporanController::exportMutasi');
    });
});
