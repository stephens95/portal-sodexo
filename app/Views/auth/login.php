<!doctype html>
<html lang="en">

<head>
    <title>Login | Sodexo Portal</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
        name="description"
        content="Berry is trending dashboard template made using Bootstrap 5 design framework. Berry is available in Bootstrap, React, CodeIgniter, Angular,  and .net Technologies." />
    <meta
        name="keywords"
        content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard" />
    <meta name="author" content="codedthemes" />
    <link rel="icon" href="<?= base_url('assets/images/favicon.svg') ?>" type="image/x-icon" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" id="main-font-link" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/phosphor/duotone/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/tabler-icons.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/feather.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/fontawesome.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fonts/material.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>" id="main-style-link" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style-preset.css') ?>" />
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
                        <a href="#" class="d-flex justify-content-center">
                            <img src="<?= base_url('assets/logo.jpeg') ?>" alt="image" />
                        </a>
                        <h5 class="my-4 d-flex justify-content-center">Sodexo Web Portal</h5>
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form method="post" action="<?= base_url('/login') ?>">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput" name="username" placeholder="Email" required />
                                <label for="floatingInput">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="floatingInput1" name="password" placeholder="Password" required />
                                <label for="floatingInput1">Password</label>
                            </div>
                            <div class="d-flex mt-1 justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" name="remember_me" value="1" checked="" />
                                    <label class="form-check-label text-muted" for="customCheckc1">Remember me</label>
                                </div>
                                <!-- <h5 class="text-secondary">Forgot Password?</h5> -->
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn text-white" style="background-color: #2a378b;">Sign In</button>
                            </div>
                        </form>
                        <hr />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/plugins/popper.min.js"></script>
    <script src="../assets/js/plugins/simplebar.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script src="../assets/js/icon/custom-font.js"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script src="../assets/js/plugins/feather.min.js"></script>

    <script>
        layout_change('light');
    </script>

    <script>
        font_change('Roboto');
    </script>

    <script>
        change_box_container('false');
    </script>

    <script>
        layout_caption_change('true');
    </script>

    <script>
        layout_rtl_change('false');
    </script>

    <script>
        preset_change('preset-1');
    </script>
</body>

</html>