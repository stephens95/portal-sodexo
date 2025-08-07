<?php include('layouts/header.php'); ?>
<?php include('layouts/sidebar.php'); ?>
<?php include('layouts/navbar.php'); ?>
<?php include('layouts/navbar-title.php'); ?>

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
                        <p>Updated</p>
                    </h5>
                </div>
                <div class="card-body pc-component">
                    <p>No updates found.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php include('layouts/footer.php'); ?>