<?= $this->extend('layouts/auth-template') ?>

<?= $this->section('content') ?>
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
        <a href="<?= base_url('/forgot-password') ?>" class="text-secondary">Forgot Password?</a>
    </div>
    <div class="d-grid mt-4">
        <button type="submit" class="btn text-white" style="background-color: #2a378b;">Sign In</button>
    </div>
</form>

<div class="text-center mt-3">
    <p class="mb-0">Don't have an account? <a href="<?= base_url('/register') ?>" class="text-primary">Sign up</a></p>
</div>
<?= $this->endSection() ?>