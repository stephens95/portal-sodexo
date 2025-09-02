<?= $this->extend('layouts/template') ?>
<?= $this->section('css') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar-title') ?>
<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <form method="post" action="<?= base_url('/buyers/refresh') ?>">
                    <?= csrf_field() ?>
                    <button id="btnRefreshBuyers" type="submit" class="btn btn-primary">
                        <i class="ti ti-refresh"></i> Refresh Data from SAP
                    </button>
                </form>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="buyersTable" class="table table-sm table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>Buyer ID</th>
                                <th>Buyer Name</th>
                                <th>Country</th>
                                <th>Country Name</th>
                                <th>Group Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($buyers) && is_array($buyers)): ?>
                                <?php foreach ($buyers as $b): ?>
                                    <tr>
                                        <td><?= esc($b['buyer_id']) ?></td>
                                        <td><?= esc($b['buyer_name']) ?></td>
                                        <td><?= esc($b['country'] ?? '') ?></td>
                                        <td><?= esc($b['country_name'] ?? '') ?></td>
                                        <td><?= esc($b['group_name'] ?? 'Sodexo Global') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No data</td>
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

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        $('#buyersTable').DataTable({
            responsive: true,
            pageLength: 10,
        });
    });
</script>
<?= $this->endSection() ?>