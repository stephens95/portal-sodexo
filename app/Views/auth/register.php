<?= $this->extend('layouts/auth-template') ?>

<?= $this->section('content') ?>
<form method="post" action="<?= base_url('/register') ?>">
    <div class="form-floating mb-3">
        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" value="<?= old('name') ?>" required />
        <label for="name">Full Name</label>
    </div>
    <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= old('email') ?>" required />
        <label for="email">Email</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
        <label for="password">Password</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />
        <label for="confirm_password">Confirm Password</label>
    </div>
    <div class="d-grid mt-4">
        <button type="submit" class="btn text-white" style="background-color: #2a378b;">Register</button>
    </div>
</form>

<div class="text-center mt-3">
    <p class="mb-0">Already have an account? <a href="<?= base_url('/') ?>" class="text-primary">Sign in</a></p>
</div>
<?= $this->endSection() ?>