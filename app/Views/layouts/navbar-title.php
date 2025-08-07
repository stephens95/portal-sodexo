<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-header-title">
                    <h5 class="m-b-10"><?= $title ?></h5>
                </div>
            </div>
            <div class="col-auto">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('/home') ?>">Home</a></li>
                    <?php if (isset($segment1)): ?>
                        <li class="breadcrumb-item"><?= isset($segment1) ? $segment1 : '' ?></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item" aria-current="page"><?= $title ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>