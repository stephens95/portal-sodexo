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
                <h5 class="mb-0">Additional Document Invoice</h5>
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
                <div class="export-container">
                    <div class="export-title">
                        <i class="fas fa-download"></i> Export
                    </div>
                    <div class="d-flex flex-wrap align-items-center">
                        <small class="text-muted me-3">Download Report:</small>
                        <button type="button" class="btn btn-success btn-sm btn-export me-2" data-type="excel">
                            <i class="fas fa-file-excel"></i> Excel (.xlsx)
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="summaryTable" class="table table-sm table-bordered table-striped table-hover w-100" style="white-space: nowrap; font-size: 11px;">
                        <thead class="table-dark">
                            <tr>
                                <th>Invoice</th>
                                <th>Invoice Date</th>
                                <th>End Customer</th>
                                <th>Doc Inv</th>
                                <th>Doc PL</th>
                                <th>Doc AW Bill</th>
                                <th>Doc COO</th>
                                <th>Doc INS</th>
                                <th>Upload</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <!-- <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Data is cached for 30 minutes. Use "Refresh Data" to get the latest data.
                    </small>
                </div> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm">
                    <input type="hidden" id="uploadSalesOrder" name="invoice">
                    <input type="hidden" id="uploadEndCustomer" name="end_customer">
                    <div class="mb-3">
                        <label class="form-label">Invoice</label>
                        <div id="lblInvoice" class="form-control-plaintext text-primary fw-bold"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">End Customer</label>
                        <div id="lblEndCustomer" class="form-control-plaintext text-success fw-bold"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Document Type</label>
                        <select class="form-select" name="doc_type" required>
                            <option value="">Select document type...</option>
                            <option value="INV">Invoice (PDF)</option>
                            <option value="PL">Packing List (PDF/Excel)</option>
                            <option value="BL_RW">BL/RW Bill (PDF)</option>
                            <option value="COO">COO (PDF)</option>
                            <option value="INS">Insurance (PDF)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Document File</label>
                        <input type="file" class="form-control" name="document" required accept=".pdf,.xlsx,.xls">
                        <div class="form-text">Maximum file size: 3MB</div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="uploadButton">Upload</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noteModalLabel">Tambah Catatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="noteForm">
                    <input type="hidden" id="noteInvoice" name="invoice">
                    <input type="hidden" id="noteEndCustomer" name="end_customer">
                    <div class="mb-3">
                        <label for="noteText" class="form-label">Catatan</label>
                        <textarea class="form-control" id="noteText" name="note" rows="20"
                            maxlength="1000"
                            placeholder="Tulis catatan di sini... (max 1000 karakter)"></textarea>
                        <small id="charCount" class="text-muted">0 / 1000</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveNoteBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Excel Modal -->
<div class="modal fade" id="exportExcelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Excel - Invoice Range</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportExcelForm">
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="downloadExcelBtn" class="btn btn-success">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        var table = $('#summaryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "<?= site_url('report/sd/summary/getData') ?>",
            pageLength: 25,
            lengthChange: false, // hide length menu
            columns: [{
                    data: 0
                }, // Invoice
                {
                    data: 1
                }, // Invoice Date
                {
                    data: 2
                }, // End Customer
                {
                    data: 3
                }, // Doc Inv
                {
                    data: 4
                }, // Doc PL
                {
                    data: 5
                }, // Doc AW Bill
                {
                    data: 6
                }, // Doc COO
                {
                    data: 7
                }, // Doc INS
                {
                    data: 0, // pakai Invoice sebagai identifier
                    render: function(data, type, row) {
                        return `
                        <button type="button" class="btn btn-sm btn-outline-success upload-btn" 
                                data-invoice="${row[0]}" data-endcustomer="${row[2]}">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                         <button type="button" class="btn btn-sm btn-outline-primary download-all-btn" 
                    data-invoice="${row[0]}" data-endcustomer="${row[2]}">
                <i class="fas fa-download"></i> Download All
            </button>
             <button type="button" class="btn btn-sm btn-outline-warning note-btn" 
                    data-invoice="${row[0]}" data-endcustomer="${row[2]}">
                <i class="fas fa-sticky-note"></i> Note
            </button>`;
                    }
                }
            ]
        });
        // 🔄 Refresh button
        $('#refreshCache').on('click', function() {
            table.ajax.reload(null, false); // false = biar tetap di halaman sekarang
        });

        // $('#summaryTable').on('click', '.upload-btn', function() {
        //     var salesOrder = $(this).data('salesorder');
        //     $('#uploadSalesOrder').val(salesOrder);
        //     $('#uploadModal').modal('show');
        // });

        $('#summaryTable').on('click', '.upload-btn', function() {
            var invoice = $(this).data('invoice');
            var endCustomer = $(this).data('endcustomer'); // pastikan ada di button

            $('#lblInvoice').text(invoice);
            $('#lblEndCustomer').text(endCustomer);

            $('#uploadSalesOrder').val(invoice);
            $('#uploadEndCustomer').val(endCustomer); // isi hidden input end_customer

            $('#uploadModal').modal('show');
        });


        $('#uploadButton').on('click', function() {
            var formData = new FormData($('#uploadForm')[0]);

            $.ajax({
                url: "<?= site_url('report/sd/summary/upload') ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#uploadButton').prop('disabled', true).text('Uploading...');
                },
                success: function(res) {
                    alert('Upload berhasil!');
                    $('#uploadModal').modal('hide');
                    $('#uploadForm')[0].reset();
                    $('#summaryTable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert('Upload gagal: ' + xhr.responseText);
                },
                complete: function() {
                    $('#uploadButton').prop('disabled', false).html('<i class="fas fa-upload"></i> Upload');
                }
            });
        });

        // Download All
        $('#summaryTable').on('click', '.download-all-btn', function() {
            var invoice = $(this).data('invoice');
            var endCustomer = $(this).data('endcustomer');

            // arahkan ke controller untuk download zip
            window.location.href = "<?= site_url('document/downloadAll') ?>?invoice=" + invoice + "&end_customer=" + endCustomer;
        });

        $('#summaryTable').on('click', '.note-btn', function() {
            let invoice = $(this).data('invoice');
            let endCustomer = $(this).data('endcustomer');

            // simpan di modal biar bisa dipakai saat save
            $('#noteInvoice').val(invoice);
            $('#noteEndCustomer').val(endCustomer);

            // reset textarea dulu
            $('#noteText').val('');

            // load note existing
            $.get("<?= site_url('document/getNote') ?>", {
                invoice: invoice,
                end_customer: endCustomer
            }, function(res) {
                if (res.status === 'success') {
                    $('#noteText').val(res.note);

                    // 🔑 hitung ulang panjang isi yang sudah ada
                    var length = res.note.length;
                    $('#charCount').text(length + ' / 1000');
                }
                $('#noteModal').modal('show');
            });
        });

        $('#saveNoteBtn').on('click', function() {
            var formData = $('#noteForm').serialize();

            $.ajax({
                url: "<?= site_url('document/saveNote') ?>",
                type: 'POST',
                data: formData,
                success: function(res) {
                    alert('Catatan berhasil disimpan!');
                    $('#noteModal').modal('hide');
                },
                error: function(xhr) {
                    alert('Gagal menyimpan catatan: ' + xhr.responseText);
                }
            });
        });
    });

    $('#noteText').on('input', function() {
        var length = $(this).val().length;
        $('#charCount').text(length + ' / 1000');
    });

    // Tampilkan modal saat klik tombol export
    $('.btn-export[data-type="excel"]').on('click', function() {
        $('#exportExcelModal').modal('show');
    });

    // Aksi download
    $('#downloadExcelBtn').on('click', function() {
        let formData = $('#exportExcelForm').serialize(); // start_date & end_date
        window.location.href = "<?= site_url('document/exportExcelByDate') ?>?" + formData;
        $('#exportExcelModal').modal('hide');
    });
</script>
<?= $this->endSection() ?>