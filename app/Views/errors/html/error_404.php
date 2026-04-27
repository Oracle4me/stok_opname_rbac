<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Error 404 | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.ico') ?>">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/preloader.min.css') ?>">
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.min.css') ?>" rel="stylesheet">
</head>

<body>
<div class="my-5 pt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <h1 class="display-1 fw-semibold">
                        4<span class="text-primary mx-2">0</span>4
                    </h1>

                    <h4 class="text-uppercase">
                        <?php if (ENVIRONMENT !== 'production'): ?>
                            <?= esc($message ?? 'Page not found') ?>
                        <?php else: ?>
                            Halaman tidak ditemukan
                        <?php endif; ?>
                    </h4>

                    <div class="mt-5 text-center">
                        <a class="btn btn-primary" href="<?= base_url('admin/dashboard') ?>">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-xl-8 text-center">
                <img src="<?= base_url('assets/images/error-img.png') ?>" 
                     alt="error" 
                     class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/metismenu/metisMenu.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/simplebar/simplebar.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/node-waves/waves.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/feather-icons/feather.min.js') ?>"></script>
<script src="<?= base_url('assets/libs/pace-js/pace.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>

</body>
</html>