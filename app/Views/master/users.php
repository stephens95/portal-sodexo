<?= $this->extend('layouts/template') ?>
<?= $this->section('css') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar-title') ?>
<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Users</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="usersTable" class="table table-sm table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Buyer</th>
                                <th>Roles</th>
                                <th width="10%">Verified</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users) && is_array($users)) : ?>
                                <?php foreach ($users as $i => $user) : ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= esc($user['name']) ?></td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td>
                                            <?php if (!empty($user['buyer_name'])) : ?>
                                                <?php $buyerNames = explode(', ', $user['buyer_name']); ?>
                                                <?php foreach ($buyerNames as $index => $buyerName) : ?>
                                                    <span class="badge bg-primary me-1 mb-1"><?= esc(trim($buyerName)) ?></span>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <span class="text-muted">No buyers assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($user['role_name'])) : ?>
                                                <?php $roleNames = explode(', ', $user['role_name']); ?>
                                                <?php foreach ($roleNames as $index => $roleName) : ?>
                                                    <span class="badge bg-secondary me-1 mb-1"><?= esc($roleName) ?></span>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <span class="text-muted">No roles assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    id="verify_<?= $user['user_id'] ?>"
                                                    <?= $user['verified'] ? 'checked' : '' ?>
                                                    onchange="toggleVerification(<?= $user['user_id'] ?>, this)">
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm" onclick="editUser(<?= $user['user_id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="<?= base_url('/users/delete/' . $user['user_id']) ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Create User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger" id="passwordRequired">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" minlength="6">
                                <div class="invalid-feedback"></div>
                                <small class="text-muted" id="passwordHint">Minimum 6 characters</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger" id="confirmPasswordRequired">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="buyer_ids" class="form-label">Buyers <span class="text-danger">*</span></label>
                                <select class="form-select" id="buyer_ids" name="buyer_ids[]" multiple required>
                                    <?php foreach ($buyers as $buyer) : ?>
                                        <option value="<?= $buyer['buyer_id'] ?>"><?= esc($buyer['buyer_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Select at least one buyer</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role_ids" class="form-label">Roles <span class="text-danger">*</span></label>
                                <select class="form-select" id="role_ids" name="role_ids[]" multiple required>
                                    <?php foreach ($roles as $role) : ?>
                                        <option value="<?= $role['role_id'] ?>"><?= esc($role['role_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Select at least one role</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="verified" name="verified" value="1">
                                <label class="form-check-label" for="verified">
                                    Verified User
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#usersTable').DataTable({
            responsive: true,
            pageLength: 10,
        });

        $('#buyer_ids, #role_ids').select2({
            theme: 'bootstrap-5',
            placeholder: 'Select options...',
            allowClear: true,
            dropdownParent: $('#userModal')
        });

        $('#userForm').on('submit', function(e) {
            e.preventDefault();

            // Validate password match
            if ($('#password').val() !== $('#confirm_password').val()) {
                $('#confirm_password').addClass('is-invalid');
                $('#confirm_password').siblings('.invalid-feedback').text('Passwords do not match');
                return;
            }

            // Validate buyers selection
            const buyerIds = $('#buyer_ids').val();
            if (!buyerIds || buyerIds.length === 0) {
                $('#buyer_ids').addClass('is-invalid');
                $('#buyer_ids').siblings('.invalid-feedback').text('Please select at least one buyer');
                return;
            } else {
                $('#buyer_ids').removeClass('is-invalid');
            }

            // Validate roles selection
            const roleIds = $('#role_ids').val();
            if (!roleIds || roleIds.length === 0) {
                $('#role_ids').addClass('is-invalid');
                $('#role_ids').siblings('.invalid-feedback').text('Please select at least one role');
                return;
            } else {
                $('#role_ids').removeClass('is-invalid');
            }

            const formData = new FormData(this);
            const userId = $('#user_id').val();
            const url = userId ? '<?= base_url('/users/update') ?>' : '<?= base_url('/users/create') ?>';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#submitBtn').prop('disabled', true).text('Saving...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#userModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message || 'Error occurred');
                        if (response.errors) {
                            displayErrors(response.errors);
                        }
                    }
                },
                error: function() {
                    alert('Error occurred while saving user');
                },
                complete: function() {
                    $('#submitBtn').prop('disabled', false).text('Save');
                }
            });
        });

        // Real-time validation
        $('#confirm_password').on('input', function() {
            if ($(this).val() !== $('#password').val()) {
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('Passwords do not match');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('#buyer_ids').on('change', function() {
            const buyerIds = $(this).val();
            if (!buyerIds || buyerIds.length === 0) {
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('Please select at least one buyer');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        $('#role_ids').on('change', function() {
            const roleIds = $(this).val();
            if (!roleIds || roleIds.length === 0) {
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('Please select at least one role');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });

    function toggleVerification(userId, checkbox) {
        const verified = checkbox.checked ? 1 : 0;

        $.ajax({
            url: '<?= base_url('/users/toggle-verification') ?>',
            method: 'POST',
            data: {
                user_id: userId,
                verified: verified
            },
            success: function(response) {
                if (response.status !== 'success') {
                    checkbox.checked = !checkbox.checked;
                    alert('Failed to update verification status');
                }
            },
            error: function() {
                checkbox.checked = !checkbox.checked;
                alert('Error occurred while updating verification');
            }
        });
    }

    function resetForm() {
        $('#userForm')[0].reset();
        $('#userModalLabel').text('Create User');
        $('#user_id').val('');
        $('#password').prop('required', true);
        $('#confirm_password').prop('required', true);
        $('#passwordRequired, #confirmPasswordRequired').show();
        $('#passwordHint').text('Minimum 6 characters');
        $('#buyer_ids, #role_ids').val(null).trigger('change');
        $('#verified').prop('checked', false);
        $('.form-control, .form-select').removeClass('is-invalid');
    }

    function editUser(userId) {
        $.ajax({
            url: '<?= base_url('/users/getUserById') ?>/' + userId,
            method: 'GET',
            success: function(response) {
                $('#userModalLabel').text('Edit User');
                $('#user_id').val(response.user.user_id);
                $('#name').val(response.user.name);
                $('#email').val(response.user.email);
                $('#verified').prop('checked', response.user.verified == 1);

                $('#password').prop('required', false);
                $('#confirm_password').prop('required', false);
                $('#passwordRequired, #confirmPasswordRequired').hide();
                $('#passwordHint').text('Leave blank to keep current password');

                if (response.user.buyer_ids) {
                    const buyerIds = response.user.buyer_ids.split(',');
                    $('#buyer_ids').val(buyerIds).trigger('change');
                }

                if (response.user.role_ids) {
                    const roleIds = response.user.role_ids.split(',');
                    $('#role_ids').val(roleIds).trigger('change');
                }

                $('#userModal').modal('show');
            },
            error: function() {
                alert('Error loading user data');
            }
        });
    }

    function displayErrors(errors) {
        $('.form-control, .form-select').removeClass('is-invalid');

        for (const field in errors) {
            const input = $(`#${field}`);
            input.addClass('is-invalid');
            input.siblings('.invalid-feedback').text(errors[field]);
        }
    }
</script>
<?= $this->endSection() ?>