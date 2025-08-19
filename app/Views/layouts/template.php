<!doctype html>
<html lang="en">
<head>
    <title><?= isset($title) ? $title . ' | ' : '' ?>Sodexo Portal</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <link rel="icon" href="<?= base_url('assets/images/logo_account.jpg') ?>" type="image/x-icon" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" id="main-font-link" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/phosphor/duotone/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/tabler-icons.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/feather.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/fontawesome.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/material.css') ?>" />

    <!-- Required CSS - All Pages -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>" id="main-style-link" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style-preset.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Additional CSS - every page -->
    <?= $this->renderSection('css') ?>
</head>

<body>
    <!-- Loader -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <!-- Sidebar -->
    <?= $this->include('layouts/sidebar') ?>

    <!-- Navbar -->
    <?= $this->include('layouts/navbar') ?>

    <!-- Main Content -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- Breadcrumb (Navbar Title) -->
            <?php if (isset($showBreadcrumb) && $showBreadcrumb): ?>
                <?= $this->include('layouts/navbar-title') ?>
            <?php endif; ?>

            <!-- Page Content -->
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Footer -->
    <?= $this->include('layouts/footer') ?>

    <!-- Required JS - All Pages -->
    <script src="<?= base_url('assets/js/plugins/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/simplebar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/fonts/custom-font.js') ?>"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <script src="<?= base_url('assets/js/theme.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/feather.min.js') ?>"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Additional JS - Every Page -->
    <?= $this->renderSection('js') ?>

    <!-- Theme Scripts -->
    <script>
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
    </script>
</body>
</html>