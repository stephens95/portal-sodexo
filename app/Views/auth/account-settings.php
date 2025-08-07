<?php include(__DIR__ . '/../layouts/header.php'); ?>
<?php include(__DIR__ . '/../layouts/sidebar.php'); ?>
<?php include(__DIR__ . '/../layouts/navbar.php'); ?>
<?php include(__DIR__ . '/../layouts/navbar-title.php'); ?>

<div class="row mt-4">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h5>Account Settings</h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form method="post" action="<?= base_url('/account/update') ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= session()->get('email') ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include(__DIR__ . '/../layouts/footer.php'); ?>