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

    /* Fix untuk input search agar tidak interfere dengan sorting */
    .column-search-input {
        width: 100%;
        padding: 2px 4px;
        font-size: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        background-color: #fff;
        margin-top: 2px;
        position: relative;
        z-index: 10;
    }

    .column-search-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 0.1rem rgba(0, 123, 255, 0.25);
    }

    .column-search th {
        padding: 2px !important;
        position: relative;
    }

    .dataTables_scrollHead thead th {
        background-color: #343a40 !important;
        color: #fff !important;
        border-color: #454d55 !important;
    }

    .dataTables_scrollHead thead tr:nth-child(2) th {
        background-color: #495057 !important;
        padding: 2px !important;
        border-color: #6c757d !important;
    }

    #inventoryTable thead tr:first-child th {
        background-color: #343a40 !important;
        color: #fff !important;
        border-color: #454d55 !important;
    }

    #inventoryTable thead tr:nth-child(2) th {
        background-color: #495057 !important;
        padding: 2px !important;
        border-color: #6c757d !important;
    }

    @media (max-width: 768px) {
        .column-search-input {
            font-size: 9px;
            padding: 1px 2px;
        }
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
                                <tr class="column-search">
                                    <th></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
                                    <th><input type="text" placeholder="Search..." class="column-search-input" onclick="event.stopPropagation()"></th>
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

<script>
    $(document).ready(function() {
        // Initialize DataTable with proper configuration
        const table = $('#inventoryTable').DataTable({
            processing: false,
            serverSide: true,
            responsive: false,
            scrollX: true,
            scrollY: '60vh',
            scrollCollapse: true,
            paging: true,
            ordering: true, // Make sure ordering is enabled
            searching: true,
            fixedHeader: {
                header: true,
                // headerOffset: 80 // Adjust based on your navbar height
            },
            ajax: {
                url: '<?= base_url('report-inventory/data') ?>',
                type: 'POST',
                data: function(d) {
                    d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                    
                    // Add column search parameters dengan class yang baru
                    $('.column-search-input').each(function(i) {
                        if ($(this).val()) {
                            if (!d.columns[i + 1]) d.columns[i + 1] = {};
                            if (!d.columns[i + 1].search) d.columns[i + 1].search = {};
                            d.columns[i + 1].search.value = $(this).val();
                        }
                    });
                },
                beforeSend: function() {
                    $('#loadingOverlay').removeClass('d-none');
                },
                complete: function() {
                    $('#loadingOverlay').addClass('d-none');
                },
                error: function(xhr, error, code) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load inventory data. Please try again.');
                    } else {
                        alert('Failed to load inventory data. Please try again.');
                    }
                    $('#loadingOverlay').addClass('d-none');
                }
            },
            columns: [
                {
                    data: 0,
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 1,
                    orderable: true,
                    searchable: true,
                    name: 'forecast_quotation'
                },
                {
                    data: 2,
                    orderable: true,
                    searchable: true,
                    name: 'so_actual'
                },
                {
                    data: 3,
                    orderable: true,
                    searchable: true,
                    name: 'customer_name'
                },
                {
                    data: 4,
                    orderable: true,
                    searchable: true,
                    name: 'quotation_actual'
                },
                {
                    data: 5,
                    orderable: true,
                    searchable: true,
                    name: 'po_customer'
                },
                {
                    data: 6,
                    orderable: true,
                    searchable: true,
                    name: 'style'
                },
                {
                    data: 7,
                    orderable: true,
                    searchable: true,
                    name: 'color'
                },
                {
                    data: 8,
                    orderable: true,
                    searchable: true,
                    name: 'size',
                },
                {
                    data: 9,
                    orderable: true,
                    searchable: true,
                    name: 'qty',
                    className: 'text-end'
                },
                {
                    data: 10,
                    orderable: true,
                    searchable: true,
                    name: 'production_year'
                },
                {
                    data: 11,
                    orderable: true,
                    searchable: true,
                    name: 'aging',
                    className: 'text-center'
                },
                {
                    data: 12,
                    orderable: true,
                    searchable: true,
                    name: 'country'
                },
                {
                    data: 13,
                    orderable: true,
                    searchable: true,
                    name: 'material_code'
                },
                {
                    data: 14,
                    orderable: true,
                    searchable: true,
                    name: 'special_stock'
                }
            ],
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            order: [
                [11, 'desc'] // Default sort by aging column
            ],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel (Current Page)',
                    className: 'btn btn-success btn-sm me-2',
                    title: 'Inventory Report (Current Page)',
                    exportOptions: {
                        columns: ':visible:not(:first-child)' // Exclude row number
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF (Current Page)',
                    className: 'btn btn-danger btn-sm me-2',
                    title: 'Inventory Report (Current Page)',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:first-child)' // Exclude row number
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print (Current Page)',
                    className: 'btn btn-info btn-sm',
                    title: 'Inventory Report (Current Page)',
                    exportOptions: {
                        columns: ':visible:not(:first-child)' // Exclude row number
                    }
                }
            ],
            initComplete: function() {
                // Apply FixedHeader after table is fully initialized
                setTimeout(function() {
                    if (table.fixedHeader) {
                        table.fixedHeader.disable();
                        table.fixedHeader.enable();
                    }
                }, 100);

                // Setup column search functionality
                setupColumnSearch();
            },
            drawCallback: function() {
                // Re-apply FixedHeader after each draw
                setTimeout(function() {
                    if (table.fixedHeader) {
                        table.fixedHeader.adjust();
                    }
                }, 50);
            }
        });

        // Setup column search with proper debouncing
        function setupColumnSearch() {
            let searchTimeouts = {};
            
            // Prevent sorting when clicking on input
            $('.column-search-input').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                $(this).focus();
                return false;
            });

            // Prevent sorting on mousedown
            $('.column-search-input').on('mousedown', function(e) {
                e.stopPropagation();
            });

            // Handle input events
            $('.column-search-input').on('input', function(e) {
                e.stopPropagation();
                const columnIndex = $(this).closest('th').index();
                const value = this.value;
                
                // Clear previous timeout for this column
                if (searchTimeouts[columnIndex]) {
                    clearTimeout(searchTimeouts[columnIndex]);
                }
                
                // Set new timeout
                searchTimeouts[columnIndex] = setTimeout(function() {
                    if (table.column(columnIndex).search() !== value) {
                        table.column(columnIndex).search(value).draw();
                    }
                }, 500);
            });

            // Handle special keys
            $('.column-search-input').on('keydown', function(e) {
                e.stopPropagation();
                const columnIndex = $(this).closest('th').index();
                
                if (e.key === 'Escape') {
                    $(this).val('');
                    table.column(columnIndex).search('').draw();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = this.value;
                    if (table.column(columnIndex).search() !== value) {
                        table.column(columnIndex).search(value).draw();
                    }
                }
            });

            // Handle change events
            $('.column-search-input').on('change', function(e) {
                e.stopPropagation();
                const columnIndex = $(this).closest('th').index();
                const value = this.value;
                if (table.column(columnIndex).search() !== value) {
                    table.column(columnIndex).search(value).draw();
                }
            });

            // Handle focus events
            $('.column-search-input').on('focus', function(e) {
                e.stopPropagation();
            });

            // Handle blur events
            $('.column-search-input').on('blur', function(e) {
                e.stopPropagation();
            });
        }

        // Clear all column searches
        function clearAllColumnSearches() {
            $('.column-search-input').val('');
            table.columns().search('').draw();
        }

        // Add clear all search button functionality
        $(document).on('click', '.clear-all-search', function() {
            clearAllColumnSearches();
        });

        // Refresh cache functionality
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
                    clearAllColumnSearches(); 
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

        // Refresh table functionality
        $('#refreshTable').click(function() {
            table.ajax.reload();
            if (typeof toastr !== 'undefined') {
                toastr.info('Table reloaded');
            }
        });

        // Export Excel functionality
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

        // Export CSV functionality
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

        // Auto refresh every 10 minutes
        setInterval(function() {
            table.ajax.reload(null, false); // false = stay on current page
        }, 600000);

        // Handle window resize for FixedHeader
        $(window).on('resize', function() {
            if (table.fixedHeader) {
                table.fixedHeader.adjust();
            }
        });
    });
</script>
<?= $this->endSection() ?>