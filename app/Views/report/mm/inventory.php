<?= view('layouts/header') ?>
<?= view('layouts/sidebar.php'); ?>
<?= view('layouts/navbar.php'); ?>
<?= view('layouts/navbar-title.php'); ?>

<?php if (!empty($updates)) : ?>
    <?php foreach ($updates as $update): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Updated - <?= date('d M Y', strtotime($update['created_at'])) ?></h5>
                    </div>
                    <div class="card-body pc-component">
                        <dl class="dl-horizontal row">
                            <dt class="col-sm-3">Version</dt>
                            <dd class="col-sm-9"><strong><?= esc($update['version']) ?></strong></dd>

                            <dt class="col-sm-3">Title</dt>
                            <dd class="col-sm-9"><?= esc($update['title']) ?></dd>

                            <dd class="col-sm-12"><?= esc($update['description']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <p>Filter By</p>
                    </h5>
                </div>
                <div class="card-body pc-component">
                    <form id="filterForm">
                        <div class="row g-3">
                            <!-- FORECAST_QUOTATION -->
                            <div class="col-md-6">
                                <label for="forecast_quotation" class="form-label">FORECAST QUOTATION</label>
                                <input type="text" class="form-control" id="forecast_quotation" name="forecast_quotation" placeholder="Masukkan Forecast Quotation">
                            </div>

                            <!-- STYLE -->
                            <div class="col-md-6">
                                <label for="style" class="form-label">
                                    STYLE <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="style" name="style" placeholder="Masukkan Style" required>
                            </div>

                            <!-- Tombol -->
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?= view('layouts/footer.php'); ?>