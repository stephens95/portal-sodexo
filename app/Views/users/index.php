<?= $this->extend('layouts/template') ?>
<!-- Additional CSS -->
<!-- <?= $this->section('css') ?>
 <?= $this->endSection() ?> -->

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar-title') ?>
<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User List</h5>
                <!-- <a href="<?= base_url('/users/create') ?>" class="btn btn-primary btn-sm">Create User</a> -->
                <button type="button" class="btn btn-primary btn-sm" id="btnCreateUser">Create User</button>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table id="usersTable" class="table table-sm table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Buyer</th>
                                <th>Group</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users) && is_array($users)) : ?>
                                <?php foreach ($users as $i => $user) : ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= esc($user['name']) ?></td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td><?= esc($user['buyer_name']) ?></td>
                                        <td><?= esc($user['group_name']) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="<?= $user['user_id'] ?>">
                                                Edit
                                            </button>

                                            <a href="<?= base_url('/users/delete/' . $user['user_id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5" class="text-center">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!-- Additional JS -->
<!-- <?= $this->section('js') ?>
 <?= $this->endSection() ?> -->