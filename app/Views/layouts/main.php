<!DOCTYPE html>
<html lang="en">
<?= $this->include('partials/head-main') ?>

<body>
    <div id="layout-wrapper">
        <?= $this->include('partials/menu') ?>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
            <?= $this->include('partials/footer') ?>
        </div>
    </div>
    <?= $this->include('partials/right-sidebar') ?>
    <?= $this->include('partials/scripts') ?>
    <?= $this->renderSection('scripts') ?>
</body>
</html>