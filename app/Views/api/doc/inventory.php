<?= $this->extend('layouts/template') ?>

<?= $this->section('css') ?>
<style>
    .table-container {
        background: white;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .export-container {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #f8f9fa;
        margin-bottom: 1rem;
    }

    .export-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .dt-buttons .btn-success {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: #fff !important;
    }

    .dt-buttons .btn-success:hover {
        background-color: #157347 !important;
        border-color: #146c43 !important;
    }

    .dt-buttons .btn-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #fff !important;
    }

    .dt-buttons .btn-danger:hover {
        background-color: #bb2d3b !important;
        border-color: #b02a37 !important;
    }

    .dt-buttons .btn-info {
        background-color: #0dcaf0 !important;
        border-color: #0dcaf0 !important;
        color: #fff !important;
    }

    .dt-buttons .btn-info:hover {
        background-color: #31d2f2 !important;
        border-color: #25cff2 !important;
    }

    .table-responsive {
        max-height: 60vh;
        overflow: auto;
    }

    .table-responsive thead th {
        position: sticky !important;
        top: 0 !important;
        z-index: 1020 !important;
        background-color: #343a40 !important;
        color: #fff !important;
        background-clip: padding-box;
        cursor: pointer;
    }

    .nav-tabs .nav-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .tab-content {
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        padding: 1rem;
        background-color: #fff;
    }

    .dt-buttons .btn {
        margin-right: 0.5rem;
    }

    .dt-buttons .btn:last-child {
        margin-right: 0;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar-title') ?>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <!-- Flash Messages -->
                <?php if ($message = session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($message = session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>


                <!-- Tab Content -->
                <!-- Table Section -->
                <div class="table-container position-relative">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped table-hover w-100" style="white-space: nowrap; font-size: 11px;">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>API Name</th>
                                    <th>Expired Date</th>
                                    <th>Documentation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td>Inventory</td>
                                    <td><?= isset($api[0]) ? $api[0]['expired_at'] : 'No Token Found' ?></td>
                                    <td> <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#docModal">
                                            📘 View Docs
                                        </button></td>
                                </tr>
                            </tbody>
                        </table>

                      
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?= $this->endSection() ?>

                <?= $this->section('js') ?>
                <script>
                </script>
                <?= $this->endSection() ?>