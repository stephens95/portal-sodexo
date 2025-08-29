<?= $this->extend('layouts/auth-template') ?>

<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

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
    <div class="mb-3">
        <label for="buyer_ids" class="form-label">Select Buyers <span class="text-danger">*</span></label>
        <select class="form-select" id="buyer_ids" name="buyer_ids[]" multiple required>
            <?php foreach ($buyers as $buyer): ?>
                <option value="<?= $buyer['buyer_id'] ?>" <?= in_array($buyer['buyer_id'], old('buyer_ids') ?: []) ? 'selected' : '' ?>>
                    <?= esc($buyer['buyer_name']) ?><?= ($buyer['group_name'] === 'SSA') ? ' (' . esc($buyer['group_name']) . ')' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted">Select one or more buyers you are associated with</small>
    </div>
    <div class="d-grid mt-4">
        <button type="submit" class="btn text-white" style="background-color: #2a378b;">Register</button>
    </div>
</form>

<div class="text-center mt-3">
    <p class="mb-0">Already have an account? <a href="<?= base_url('/') ?>" class="text-primary">Sign in</a></p>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#buyer_ids').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select buyers...',
        allowClear: true
    });
});
</script>
<?= $this->endSection() ?>