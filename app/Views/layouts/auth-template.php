<!doctype html>
<html lang="en">
<head>
    <title><?= isset($title) ? $title . ' | ' : '' ?>Sodexo Portal</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Sodexo Portal - Authentication System" />
    <meta name="keywords" content="Sodexo, Portal, Login, Register" />
    <meta name="author" content="Sodexo" />
    <link rel="icon" href="<?= base_url('assets/images/logo_account.png') ?>" type="image/x-icon" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" id="main-font-link" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/phosphor/duotone/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/tabler-icons.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/feather.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/fontawesome.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/material.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>" id="main-style-link" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style-preset.css') ?>" />
    
    <!-- Additional CSS -->
    <?= $this->renderSection('css') ?>
</head>

<body>
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="card my-5">
                    <div class="card-body">
                        <!-- Logo Section -->
                        <div style="display: flex; align-items: center; justify-content: center; gap: 32px; padding: 16px 0;">
                            <div style="height:80px; width:140px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:8px; box-shadow:0 2px 8px #0001; padding:6px;">
                                <img src="<?= base_url('assets/logo.jpeg') ?>" style="max-height:100%; max-width:100%; object-fit:contain;">
                            </div>
                            <div style="height:60px; border-left:2px solid #e0e0e0;"></div>
                            <div style="height:80px; width:170px; display:flex; align-items:center; justify-content:center; background:#fff; border-radius:8px; box-shadow:0 2px 8px #0001; padding:6px;">
                                <img src="<?= base_url('assets/images/logo_amt.jpg') ?>" style="max-height:100%; max-width:100%; object-fit:contain;">
                            </div>
                        </div>

                        <h5 class="my-4 d-flex justify-content-center">Sodexo - AMT Web Portal</h5>
                        
                        <!-- Flash Messages -->
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('errors')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                    <div><?= $error ?></div>
                                <?php endforeach; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Form Content -->
                        <?= $this->renderSection('content') ?>
                        
                        <hr />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/js/plugins/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/simplebar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/fonts/custom-font.js') ?>"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <script src="<?= base_url('assets/js/theme.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/feather.min.js') ?>"></script>

    <!-- Additional JS -->
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