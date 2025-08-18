<?= $this->extend('layouts/template') ?>
<?= $this->section('css') ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= $this->include('layouts/navbar-title') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('/account/update') ?>" id="accountForm">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" value="<?= session()->get('name') ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= session()->get('email') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter password (minimum 6 characters)" minlength="6">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       placeholder="Re-type new password" minlength="6">
                                <div class="invalid-feedback" id="passwordError"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <?php if (auth()->isAdmin()): ?>
                            <div class="mb-3">
                                <label for="buyer_ids" class="form-label">Buyers <span class="text-danger">*</span></label>
                                <select class="form-select" id="buyer_ids" name="buyer_ids[]" multiple required>
                                    <?php foreach ($buyers as $buyer) : ?>
                                        <?php 
                                        $selected = '';
                                        if (isset($user['buyer_ids']) && !empty($user['buyer_ids'])) {
                                            $userBuyerIds = explode(',', $user['buyer_ids']);
                                            $selected = in_array($buyer['buyer_id'], $userBuyerIds) ? 'selected' : '';
                                        }
                                        ?>
                                        <option value="<?= $buyer['buyer_id'] ?>" <?= $selected ?>><?= esc($buyer['buyer_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Select at least one buyer</small>
                            </div>
                            <?php endif; ?>

                            <?php if (auth()->isAdmin()): ?>
                            <div class="mb-3">
                                <label for="role_ids" class="form-label">Roles <span class="text-danger">*</span></label>
                                <select class="form-select" id="role_ids" name="role_ids[]" multiple required>
                                    <?php foreach ($roles as $role) : ?>
                                        <?php 
                                        $selected = '';
                                        if (isset($user['role_ids']) && !empty($user['role_ids'])) {
                                            $userRoleIds = explode(',', $user['role_ids']);
                                            $selected = in_array($role['role_id'], $userRoleIds) ? 'selected' : '';
                                        }
                                        ?>
                                        <option value="<?= $role['role_id'] ?>" <?= $selected ?>><?= esc($role['role_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Select at least one role</small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#buyer_ids, #role_ids').select2({
        theme: 'bootstrap-5',
        placeholder: 'Select options...',
        allowClear: true,
        width: '100%'
    });

    $('#confirm_password').on('input', function() {
        validatePasswordMatch();
    });

    $('#password').on('input', function() {
        validatePasswordMatch();
    });

    // Validate buyers selection
    $('#buyer_ids').on('change', function() {
        const buyerIds = $(this).val();
        if (!buyerIds || buyerIds.length === 0) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('Please select at least one buyer');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Validate roles selection
    $('#role_ids').on('change', function() {
        const roleIds = $(this).val();
        if (!roleIds || roleIds.length === 0) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').text('Please select at least one role');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    $('#accountForm').on('submit', function(e) {
        let isValid = true;

        // Validate password match
        if (!validatePasswordMatch()) {
            isValid = false;
        }

        // Validate buyers (only if admin)
        if ($('#buyer_ids').length > 0) {
            const buyerIds = $('#buyer_ids').val();
            if (!buyerIds || buyerIds.length === 0) {
                $('#buyer_ids').addClass('is-invalid');
                $('#buyer_ids').siblings('.invalid-feedback').text('Please select at least one buyer');
                isValid = false;
            }
        }

        // Validate roles (only if admin)
        if ($('#role_ids').length > 0) {
            const roleIds = $('#role_ids').val();
            if (!roleIds || roleIds.length === 0) {
                $('#role_ids').addClass('is-invalid');
                $('#role_ids').siblings('.invalid-feedback').text('Please select at least one role');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    });

    function validatePasswordMatch() {
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();

        if (password || confirmPassword) {
            if (password !== confirmPassword) {
                $('#confirm_password').addClass('is-invalid');
                $('#passwordError').text('Passwords do not match');
                return false;
            } else if (password.length > 0 && password.length < 6) {
                $('#password').addClass('is-invalid');
                $('#confirm_password').removeClass('is-invalid');
                return false;
            } else {
                $('#password, #confirm_password').removeClass('is-invalid');
                $('#passwordError').text('');
                return true;
            }
        } else {
            $('#password, #confirm_password').removeClass('is-invalid');
            $('#passwordError').text('');
            return true;
        }
    }
});
</script>
<?= $this->endSection() ?>