<?= $this->extend('layouts/auth-template') ?>

<?= $this->section('content') ?>
<div class="text-center mb-4">
    <h6 class="text-muted">Reset Your Password</h6>
    <p class="text-muted small">Enter your email and new password to reset your account password.</p>
</div>

<form method="post" action="<?= base_url('/forgot-password') ?>">
    <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= old('email') ?>" required />
        <label for="email">Email</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required />
        <label for="new_password">New Password</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required />
        <label for="confirm_password">Confirm New Password</label>
    </div>
    <div class="d-grid mt-4">
        <button type="submit" class="btn text-white" style="background-color: #2a378b;">Reset Password</button>
    </div>
</form>

<div class="text-center mt-3">
    <p class="mb-0">Remember your password? <a href="<?= base_url('/') ?>" class="text-primary">Sign in</a></p>
</div>
<?= $this->endSection() ?>