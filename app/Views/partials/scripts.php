<!-- Core -->
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<!-- UI Helper -->
<script src="<?= base_url('assets/libs/metismenu/metisMenu.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/simplebar/simplebar.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/node-waves/waves.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/feather-icons/feather.min.js') ?>"></script>

<!-- Plugins -->
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/select2/select2-min.js') ?>"></script>
<script src="<?= base_url('assets/libs/pace-js/pace.min.js') ?>"></script>

<!-- Charts (HANYA kalau dashboard) -->
<?php if (isset($page) && $page === 'dashboard'): ?>
<script src="<?= base_url('assets/libs/apexcharts/apexcharts.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js') ?>"></script>
<script src="<?= base_url('assets/js/pages/dashboard.init.js') ?>"></script>
<?php endif; ?>

<!-- ================= DATATABLE ================= -->

<!-- Core DataTables -->
<script src="<?= base_url('assets/libs/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>

<!-- Responsive -->
<script src="<?= base_url('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') ?>"></script>

<!-- OPTIONAL (kalau mau export button) -->
<script src="<?= base_url('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/pdfmake/build/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/pdfmake/build/vfs_fonts.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/datatables.net-buttons/js/buttons.print.min.js') ?>"></script>

<!-- ============================================ -->

<!-- Other -->
<script src="https://unpkg.com/scrollreveal@4.0.9/dist/scrollreveal.js"></script>

<!-- Main App -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>