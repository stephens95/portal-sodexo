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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Debit Note Document</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" id="refreshCache">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>

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

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs" id="debitNoteTabs" role="tablist">
                    <?php
                    $tabs = [
                        ['id' => 'all-dn', 'icon' => 'file-invoice', 'label' => 'All DN Document', 'active' => true],
                    ];
                    foreach ($tabs as $tab): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $tab['active'] ? 'active' : '' ?>"
                                id="<?= $tab['id'] ?>-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#<?= $tab['id'] ?>"
                                type="button" role="tab">
                                <i class="fas fa-<?= $tab['icon'] ?>"></i> <?= $tab['label'] ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="debitNoteTabContent">
                    <?php
                    $tabContents = [
                        ['id' => 'all-dn', 'tableId' => 'dnTable', 'overlayId' => 'loadingOverlay1', 'filter' => 'all', 'title' => 'All Data', 'active' => true]
                    ];
                    foreach ($tabContents as $content): ?>
                        <div class="tab-pane fade <?= $content['active'] ? 'show active' : '' ?>"
                            id="<?= $content['id'] ?>" role="tabpanel">

                            <!-- Export Section -->
                            <div class="export-container">
                                <div class="export-title">
                                    <i class="fas fa-download"></i> Export <?= $content['title'] ?>
                                </div>
                                <div class="d-flex flex-wrap align-items-center">
                                    <small class="text-muted me-3">Download DN data:</small>
                                    <button type="button" class="btn btn-success btn-sm btn-export me-2" data-table="<?= $content['filter'] ?>" data-type="excel">
                                        <i class="fas fa-file-excel"></i> Excel (.xlsx)
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm btn-export me-2" data-table="<?= $content['filter'] ?>" data-type="csv">
                                        <i class="fas fa-file-csv"></i> CSV
                                    </button>
                                    <?php if (auth()->isAdmin() || auth()->hasRoles(['Admin02'])) { ?>
                                        <a target="_blank" href="<?= base_url('/api-dn') ?>"
                                            class="btn btn-secondary btn-sm">
                                            <i class="ti ti-screen-share"></i> JSON
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="table-container position-relative">
                                <div class="loading-overlay d-none" id="<?= $content['overlayId'] ?>">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="mt-2">Loading data...</div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="<?= $content['tableId'] ?>" class="table table-sm table-bordered table-striped table-hover w-100" style="white-space: nowrap; font-size: 11px;">
                                        <thead class="table-dark">
                                            <tr>
                                                <?php
                                                $headers = [
                                                    ['text' => '#', 'width' => '2%'],
                                                    ['text' => 'Doc Date', 'width' => '8%'],
                                                    ['text' => 'Doc Number', 'width' => '10%'],
                                                    ['text' => 'Cur', 'width' => '5%'],
                                                    ['text' => 'Text', 'width' => '25%'],
                                                    ['text' => 'Courier', 'class' => 'text-end', 'width' => '8%'],
                                                    ['text' => 'Local Chg', 'class' => 'text-end', 'width' => '8%'],
                                                    ['text' => 'Duty', 'class' => 'text-end', 'width' => '8%'],
                                                    ['text' => 'Others', 'class' => 'text-end', 'width' => '8%']
                                                ];

                                                foreach ($headers as $header): ?>
                                                    <th <?= isset($header['width']) ? 'width="' . $header['width'] . '"' : '' ?>
                                                        <?= isset($header['class']) ? 'class="' . $header['class'] . '"' : '' ?>>
                                                        <?= $header['text'] ?>
                                                    </th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
    class DebitNoteReport {
        constructor() {
            this.tables = {};
            this.baseUrl = '<?= base_url() ?>';
            this.csrfToken = '<?= csrf_token() ?>';
            this.csrfHash = '<?= csrf_hash() ?>';

            this.init();
        }

        init() {
            this.initTables();
            this.bindEvents();
        }

        getTableConfig() {
            const columns = [{
                    data: 0,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 1,
                    orderable: true
                },
                {
                    data: 2,
                    orderable: true
                },
                {
                    data: 3,
                    orderable: true
                },
                {
                    data: 4,
                    orderable: true
                },
                {
                    data: 5,
                    orderable: true,
                    className: 'text-end',
                    type: 'num'
                },
                {
                    data: 6,
                    orderable: true,
                    className: 'text-end',
                    type: 'num'
                },
                {
                    data: 7,
                    orderable: true,
                    className: 'text-end',
                    type: 'num'
                },
                {
                    data: 8,
                    orderable: true,
                    className: 'text-end',
                    type: 'num'
                }
            ];

            return {
                processing: true,
                serverSide: true,
                responsive: true,
                ordering: true,
                order: [
                    [1, 'desc']
                ],
                columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: [5, 6, 7, 8],
                        className: 'text-end'
                    }
                ],
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel (Current Page)',
                        className: 'btn btn-success btn-sm me-2',
                        title: 'Debit Note Report (Current Page)',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF (Current Page)',
                        className: 'btn btn-danger btn-sm me-2',
                        title: 'Debit Note Report (Current Page)',
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
                        title: 'Debit Note Report (Current Page)',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ],
                columns,
                language: {
                    processing: '<div class="text-center"><div class="spinner-border text-primary" role="status"></div><br>Loading data...</div>',
                    loadingRecords: 'Loading...',
                    emptyTable: 'No data available in table',
                    zeroRecords: 'No matching records found'
                },
                drawCallback: function() {
                    $('#loadingOverlay1').addClass('d-none');
                }
            };
        }

        createTable(tableId, filter, overlayId) {
            return $(`#${tableId}`).DataTable({
                ...this.getTableConfig(),
                ajax: {
                    url: `${this.baseUrl}/report-dn/data`,
                    type: 'POST',
                    timeout: 60000,
                    data: (d) => {
                        d[this.csrfToken] = this.csrfHash;
                        d.filter = filter;
                        return d;
                    },
                    beforeSend: () => {
                        $(`#${overlayId}`).removeClass('d-none');
                    },
                    complete: () => {
                        $(`#${overlayId}`).addClass('d-none');
                    },
                    error: (xhr, status, error) => {
                        console.error('AJAX Error:', status, error);
                        this.showNotification('error', 'Failed to load data. Please try again.');
                        $(`#${overlayId}`).addClass('d-none');
                    }
                }
            });
        }

        initTables() {
            try {
                this.tables['all'] = this.createTable('dnTable', 'all', 'loadingOverlay1');
            } catch (error) {
                console.error('Error initializing tables:', error);
                this.showNotification('error', 'Failed to initialize tables');
            }
        }

        bindEvents() {
            $('#refreshCache').on('click', () => this.refreshCache());
            $('.btn-export').on('click', (e) => this.handleExport(e));

            // Auto refresh every 10 minutes
            setInterval(() => this.autoRefresh(), 600000);
        }

        refreshCache() {
            const $button = $('#refreshCache');
            const originalText = $button.html();

            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');

            $.ajax({
                url: `${this.baseUrl}/report-dn/refresh-cache`,
                type: 'POST',
                timeout: 60000,
                data: {
                    [this.csrfToken]: this.csrfHash
                },
                success: (response) => {
                    if (response.status === 'success') {
                        this.showNotification('success', 'Cache refreshed successfully');
                        Object.values(this.tables).forEach(table => {
                            if (table && typeof table.ajax === 'object') {
                                table.ajax.reload();
                            }
                        });
                    } else {
                        this.showNotification('error', response.message || 'Failed to refresh cache');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Refresh cache error:', status, error);
                    this.showNotification('error', 'Failed to refresh cache');
                },
                complete: () => {
                    $button.prop('disabled', false).html(originalText);
                }
            });
        }

        handleExport(e) {
            const $button = $(e.currentTarget);
            const originalText = $button.html();
            const tableType = $button.data('table');
            const exportType = $button.data('type');

            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Preparing...');

            this.showNotification('info', `Preparing ${exportType.toUpperCase()} file. This may take a moment...`);

            window.location.href = `${this.baseUrl}/report-dn/export-${exportType}?filter=${tableType}`;

            setTimeout(() => {
                $button.prop('disabled', false).html(originalText);
                this.showNotification('success', `${exportType.toUpperCase()} file download started`);
            }, 2000);
        }

        autoRefresh() {
            Object.values(this.tables).forEach(table => {
                if (table && typeof table.ajax === 'object') {
                    table.ajax.reload(null, false);
                }
            });
        }

        showNotification(type, message) {
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            } else {
                console.log(`${type.toUpperCase()}: ${message}`);
                alert(message);
            }
        }
    }

    $(document).ready(() => {
        try {
            new DebitNoteReport();
        } catch (error) {
            console.error('Error initializing DebitNoteReport:', error);
        }
    });
</script>
<?= $this->endSection() ?>