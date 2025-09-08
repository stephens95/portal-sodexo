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

    .table-responsive thead th:first-child {
        z-index: 1030 !important;
    }

    /* Tab content styling */
    .tab-content {
        margin-top: 1rem;
    }

    /* Ensure DataTables sorting arrows are visible */
    table.dataTable thead th.sorting,
    table.dataTable thead th.sorting_asc,
    table.dataTable thead th.sorting_desc {
        cursor: pointer;
        position: relative;
    }

    table.dataTable thead th.sorting:after,
    table.dataTable thead th.sorting_asc:after,
    table.dataTable thead th.sorting_desc:after {
        position: absolute;
        top: 12px;
        right: 8px;
        display: block;
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
    }

    table.dataTable thead th.sorting:after {
        content: "\f0dc";
        color: #999;
        font-size: 0.8em;
        padding-top: 0.12em;
    }

    table.dataTable thead th.sorting_asc:after {
        content: "\f0de";
        color: #fff;
    }

    table.dataTable thead th.sorting_desc:after {
        content: "\f0dd";
        color: #fff;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->include('layouts/navbar-title') ?>

<div class="row mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sales Order Reports</h5>
                <div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="refreshCache">
                        <i class="fas fa-sync-alt"></i> Refresh Data
                    </button>
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

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="traceability-tab" data-bs-toggle="tab" data-bs-target="#traceability" type="button" role="tab" aria-controls="traceability" aria-selected="true">
                            <i class="fas fa-search"></i> Sales Order Tracebility
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="reportTabsContent">
                    <!-- Traceability Tab -->
                    <div class="tab-pane fade show active" id="traceability" role="tabpanel" aria-labelledby="traceability-tab">
                        <div class="export-container">
                            <div class="export-title">
                                <i class="fas fa-download"></i> Export Sales Order Traceability Data
                            </div>
                            <div class="d-flex flex-wrap align-items-center">
                                <small class="text-muted me-3">Download complete traceability data:</small>
                                <button type="button" class="btn btn-success btn-sm btn-export" id="exportTraceabilityExcel">
                                    <i class="fas fa-file-excel"></i> Excel (.xlsx)
                                </button>
                                <button type="button" class="btn btn-primary btn-sm btn-export" id="exportTraceabilityCsv">
                                    <i class="fas fa-file-csv"></i> CSV
                                </button>
                            </div>
                        </div>

                        <div class="table-container position-relative">
                            <div class="loading-overlay d-none" id="loadingOverlayTraceability">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="mt-2">Loading traceability data...</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="traceabilityTable" class="table table-sm table-bordered table-striped table-hover w-100" style="white-space: nowrap; font-size: 11px;">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>QO SSA</th>
                                            <th>PO SSA</th>
                                            <th>PO Buyer</th>
                                            <th>End Customer</th>
                                            <th>Sales Order (AMT)</th>
                                            <th>Buyer Style</th>
                                            <th>SSA Style</th>
                                            <th>Colour</th>
                                            <th>Order Qty</th>
                                            <th>Delivery Note</th>
                                            <th>Shipment Qty</th>
                                            <th>Outstanding PO Qty</th>
                                            <th>Invoice Number</th>
                                            <!-- <th>Due Date</th>
                                            <th>Broker Fee</th>
                                            <th>Management Fee</th>
                                            <th>Payment Receive Date</th>
                                            <th>Attachment</th> -->
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
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
        let traceabilityTable;

        // Initialize Traceability DataTable
        function initTraceabilityTable() {
            traceabilityTable = $('#traceabilityTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '<?= base_url('report-so/data') ?>',
                    type: 'POST',
                    data: function(d) {
                        d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                    },
                    beforeSend: function() {
                        $('#loadingOverlayTraceability').removeClass('d-none');
                    },
                    complete: function() {
                        $('#loadingOverlayTraceability').addClass('d-none');
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTables error:', error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Failed to load traceability data. Please try again.');
                        } else {
                            alert('Failed to load traceability data. Please try again.');
                        }
                        $('#loadingOverlayTraceability').addClass('d-none');
                    }
                },
                columns: [{
                        data: 0,
                        orderable: false,
                        searchable: false
                    }, // #
                    {
                        data: 1,
                        orderable: true,
                        searchable: true
                    }, // QO SSA
                    {
                        data: 2,
                        orderable: true,
                        searchable: true
                    }, // PO SSA
                    {
                        data: 3,
                        orderable: true,
                        searchable: true
                    }, // PO Buyer
                    {
                        data: 4,
                        orderable: true,
                        searchable: true
                    }, // End Customer
                    {
                        data: 5,
                        orderable: true,
                        searchable: true
                    }, // Sales Order (AMT)
                    {
                        data: 6,
                        orderable: true,
                        searchable: true
                    }, // Buyer Style
                    {
                        data: 7,
                        orderable: true,
                        searchable: true
                    }, // SSA Style
                    {
                        data: 8,
                        orderable: true,
                        searchable: true
                    }, // Colour
                    {
                        data: 9,
                        className: 'text-end',
                        orderable: true,
                        searchable: true
                    }, // Order Qty
                    {
                        data: 10,
                        orderable: true,
                        searchable: true
                    }, // Delivery Note
                    {
                        data: 11,
                        className: 'text-end',
                        orderable: true,
                        searchable: true
                    }, // Shipment Qty
                    {
                        data: 12,
                        className: 'text-end',
                        orderable: true,
                        searchable: true
                    }, // Outstanding PO Qty
                    {
                        data: 13,
                        orderable: true,
                        searchable: true
                    } // Invoice Number
                    // {
                    //     data: 14,
                    //     orderable: true,
                    //     searchable: true
                    // }, // Due Date
                    // {
                    //     data: 15,
                    //     className: 'text-end',
                    //     orderable: true,
                    //     searchable: true
                    // }, // Broker Fee
                    // {
                    //     data: 16,
                    //     className: 'text-end',
                    //     orderable: true,
                    //     searchable: true
                    // }, // Management Fee
                    // {
                    //     data: 17,
                    //     orderable: true,
                    //     searchable: true
                    // }, // Payment Receive Date
                    // {
                    //     data: 18,
                    //     orderable: false,
                    //     searchable: false
                    // } // Attachment
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                order: [
                    [1, 'asc']
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel (Current Page)',
                        className: 'btn btn-success btn-sm me-2',
                        title: 'Traceability Report (Current Page)',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF (Current Page)',
                        className: 'btn btn-danger btn-sm me-2',
                        title: 'Traceability Report (Current Page)',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print (Current Page)',
                        className: 'btn btn-info btn-sm',
                        title: 'Traceability Report (Current Page)',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });
        }

        // Refresh Cache
        $('#refreshCache').click(function() {
            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');

            $.ajax({
                url: '<?= base_url('report-so/refresh-cache') ?>',
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

                    // Reload both tables if they exist
                    if (traceabilityTable) {
                        traceabilityTable.ajax.reload();
                    }
                    if (inventoryTable) {
                        inventoryTable.ajax.reload();
                    }
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

        // Export functions for Traceability
        $('#exportTraceabilityExcel').click(function() {
            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Preparing...');

            if (typeof toastr !== 'undefined') {
                toastr.info('Preparing Excel file with all traceability data. This may take a moment...');
            }

            window.location.href = '<?= base_url('report-traceability/export-excel') ?>';

            setTimeout(function() {
                button.prop('disabled', false).html(originalText);
                if (typeof toastr !== 'undefined') {
                    toastr.success('Excel file download started');
                }
            }, 2000);
        });

        $('#exportTraceabilityCsv').click(function() {
            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Preparing...');

            if (typeof toastr !== 'undefined') {
                toastr.info('Preparing CSV file with all traceability data. This may take a moment...');
            }

            window.location.href = '<?= base_url('report-traceability/export-csv') ?>';

            setTimeout(function() {
                button.prop('disabled', false).html(originalText);
                if (typeof toastr !== 'undefined') {
                    toastr.success('CSV file download started');
                }
            }, 2000);
        });

        // Export functions for Inventory
        $('#exportInventoryExcel').click(function() {
            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Preparing...');

            if (typeof toastr !== 'undefined') {
                toastr.info('Preparing Excel file with all inventory data. This may take a moment...');
            }

            window.location.href = '<?= base_url('report-inventory/export-excel') ?>';

            setTimeout(function() {
                button.prop('disabled', false).html(originalText);
                if (typeof toastr !== 'undefined') {
                    toastr.success('Excel file download started');
                }
            }, 2000);
        });

        $('#exportInventoryCsv').click(function() {
            const button = $(this);
            const originalText = button.html();

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Preparing...');

            if (typeof toastr !== 'undefined') {
                toastr.info('Preparing CSV file with all inventory data. This may take a moment...');
            }

            window.location.href = '<?= base_url('report-inventory/export-csv') ?>';

            setTimeout(function() {
                button.prop('disabled', false).html(originalText);
                if (typeof toastr !== 'undefined') {
                    toastr.success('CSV file download started');
                }
            }, 2000);
        });

        // Auto-refresh every 10 minutes
        setInterval(function() {
            if (traceabilityTable) {
                traceabilityTable.ajax.reload(null, false);
            }
        }, 600000);
    });
</script>
<?= $this->endSection() ?>