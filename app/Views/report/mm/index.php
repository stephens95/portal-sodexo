<?= $this->extend('layouts/template') ?>

<?= $this->section('css') ?>
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
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

    .btn-export {
        margin: 0.25rem;
    }

    /* Custom styles for export buttons */
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

    /* Add some spacing between buttons */
    .dt-buttons .btn {
        margin-right: 0.5rem;
    }

    .dt-buttons .btn:last-child {
        margin-right: 0;
    }

    /* ========== Sticky header for the table ========== */
    /* Limit the table viewport height so body can scroll while thead stays sticky */
    .table-responsive {
        max-height: 60vh;
        /* sesuaikan tinggi sesuai kebutuhan */
        overflow: auto;
    }

    /* Make the header cells sticky */
    .table-responsive thead th {
        position: sticky !important;
        top: 0 !important;
        z-index: 1020 !important;
        background-color: #343a40 !important;
        color: #fff !important;
        background-clip: padding-box;
        /* hindari border bleed */
    }

    /* pastikan kolom pertama tetap di atas (jika ada masalah overlap) */
    .table-responsive thead th:first-child {
        z-index: 1030 !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar-title') ?>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Inventory Report</h5>
                <div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="refreshCache">
                        <i class="fas fa-sync-alt"></i> Refresh Data
                    </button>
                    <!-- <button type="button" class="btn btn-outline-secondary btn-sm" id="refreshTable">
                        <i class="fas fa-redo"></i> Reload Table
                    </button> -->
                </div>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="export-container">
                    <div class="export-title">
                        <i class="fas fa-download"></i> Export All Data
                    </div>
                    <div class="d-flex flex-wrap align-items-center">
                        <small class="text-muted me-3">Download complete inventory data:</small>
                        <button type="button" class="btn btn-success btn-sm btn-export" id="exportExcel" style="background-color: #198754; border-color: #198754;">
                            <i class="fas fa-file-excel"></i> Excel (.xlsx)
                        </button>
                        <button type="button" class="btn btn-primary btn-sm btn-export" id="exportCsv">
                            <i class="fas fa-file-csv"></i> CSV
                        </button>
                    </div>
                </div>

                <div class="table-container position-relative">
                    <div class="loading-overlay d-none" id="loadingOverlay">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="mt-2">Loading inventory data...</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="inventoryTable" class="table table-sm table-bordered table-striped table-hover w-100" style="white-space: nowrap; font-size: 11px;">
                            <thead class="table-dark">
                                <tr>
                                    <th width="3%">#</th>
                                    <th>Quotation Forecast<br>SO Forecast</th>
                                    <!-- <th>SO Forecast</th> -->
                                    <th>SO Actual<br>(Allocated)</th>
                                    <th>Customer Name</th>
                                    <th>Quotation Actual</th>
                                    <th>PO Customer</th>
                                    <th>Style</th>
                                    <th>Colour</th>
                                    <th width="3%">Size</th>
                                    <th class="text-end">Qty</th>
                                    <th>Production<br>Year</th>
                                    <th>Aging<br>(days)</th>
                                    <th>Country</th>
                                    <th>Material Code</th>
                                    <th>Special Stock</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Data is cached for 30 minutes. Use "Refresh Data" to get the latest data.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        const table = $('#inventoryTable').DataTable({
            // processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '<?= base_url('report-inventory/data') ?>',
                type: 'POST',
                data: function(d) {
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                },
                beforeSend: function() {
                    $('#loadingOverlay').removeClass('d-none');
                },
                complete: function() {
                    $('#loadingOverlay').addClass('d-none');
                },
                error: function(xhr, error, code) {
                    // console.error('DataTables error:', error);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load inventory data. Please try again.');
                    } else {
                        alert('Failed to load inventory data. Please try again.');
                    }
                    $('#loadingOverlay').addClass('d-none');
                }
            },
            columns: [{
                    data: 0,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 1
                },
                // {
                //     data: 2
                // },
                {
                    data: 2
                },
                {
                    data: 3
                },
                {
                    data: 4
                },
                {
                    data: 5
                },
                {
                    data: 6
                },
                {
                    data: 7 // Special Stock
                },
                {
                    data: 8 // Kode Material
                },
                {
                    data: 9,
                    className: 'text-end'
                },
                {
                    data: 10
                },
                {
                    data: 11,
                    className: 'text-center'
                },
                {
                    data: 12,
                    className: 'text-end'
                },
                {
                    data: 13,
                    // className: 'text-center'
                },
                {
                    data: 14,
                    // className: 'text-center'
                }
            ],
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            order: [
                [11, 'desc']
            ],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel (Current Page)',
                    className: 'btn btn-success btn-sm me-2', // tambah margin end
                    title: 'Inventory Report (Current Page)',
                    exportOptions: {
                        columns: ':visible' // hanya export kolom yang visible
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF (Current Page)',
                    className: 'btn btn-danger btn-sm me-2', // tambah margin end
                    title: 'Inventory Report (Current Page)',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print (Current Page)',
                    className: 'btn btn-info btn-sm', // tidak perlu margin di tombol terakhir
                    title: 'Inventory Report (Current Page)',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ],
            // language: {
            //     processing: "Loading inventory datas...",
            //     search: "Search inventory:",
            //     lengthMenu: "Show _MENU_ entries per page",
            //     info: "Showing _START_ to _END_ of _TOTAL_ inventory items",
            //     infoEmpty: "No inventory data available",
            //     infoFiltered: "(filtered from _MAX_ total entries)",
            //     zeroRecords: "No matching inventory records found",
            //     emptyTable: "No inventory data available",
            //     paginate: {
            //         first: "First",
            //         previous: "Previous",
            //         next: "Next",
            //         last: "Last"
            //     }
            // }
        });

        $('#refreshCache').click(function() {
            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');

            $.ajax({
                url: '<?= base_url('report-inventory/refresh-cache') ?>',
                type: 'POST',
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Cache refreshed successfully');
                    } else {
                        alert('Cache refreshed successfully');
                    }
                    table.ajax.reload();
                },
                error: function() {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to refresh cache');
                    } else {
                        alert('Failed to refresh cache');
                    }
                },
                complete: function() {
                    button.prop('disabled', false).html(originalText);
                }
            });
        });

        $('#refreshTable').click(function() {
            table.ajax.reload();
            if (typeof toastr !== 'undefined') {
                toastr.info('Table reloaded');
            }
        });

        $('#exportExcel').click(function() {
            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Preparing...');

            if (typeof toastr !== 'undefined') {
                toastr.info('Preparing Excel file with all data. This may take a moment...');
            }

            window.location.href = '<?= base_url('report-inventory/export-excel') ?>';

            setTimeout(function() {
                button.prop('disabled', false).html(originalText);
                if (typeof toastr !== 'undefined') {
                    toastr.success('Excel file download started');
                }
            }, 2000);
        });

        $('#exportCsv').click(function() {
            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Preparing...');

            if (typeof toastr !== 'undefined') {
                toastr.info('Preparing CSV file with all data. This may take a moment...');
            }

            window.location.href = '<?= base_url('report-inventory/export-csv') ?>';

            setTimeout(function() {
                button.prop('disabled', false).html(originalText);
                if (typeof toastr !== 'undefined') {
                    toastr.success('CSV file download started');
                }
            }, 2000);
        });

        setInterval(function() {
            table.ajax.reload(null, false);
        }, 600000);
    });
</script>
<?= $this->endSection() ?>