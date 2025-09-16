<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="<?= base_url('/home') ?>" class="b-brand text-primary">
                <img src="<?= base_url('assets/logo.jpeg') ?>" alt="Logo" width="150" height="auto" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">

                <!-- Cara Cara implementasi Roles & Buyers -->
                <!-- Roles --> <?php if (auth()->isAdmin()): ?> <?php endif; ?>
                <!-- Roles --> <?php if (auth()->hasRoles(['Admin', 'Test'])): ?><?php endif; ?>
                <!-- Buyers --> <?php if (auth()->hasBuyers(['1000000038', '1000002046'])): ?><?php endif; ?>

                <!-- Administrator Section - Only for Admin -->
                <?php if (auth()->isAdmin()): ?>
                    <li class="pc-item pc-caption">
                        <label>Administrator</label>
                        <i class="ti ti-settings"></i>
                    </li>
                    <li class="pc-item">
                        <a href="<?= base_url('/users') ?>" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                            <span class="pc-mtext">Users</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="<?= base_url('/buyers') ?>" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-building"></i></span>
                            <span class="pc-mtext">Buyers</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="pc-item pc-caption">
                    <label>Dashboard</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item">
                    <a href="<?= base_url('/news-updates') ?>" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-news"></i></span>
                        <span class="pc-mtext">News & Updates</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Report</label>
                    <i class="ti ti-apps"></i>
                </li>

                <!-- Material Management -->
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-database-export"></i></span>
                        <span class="pc-mtext">Material Management</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a class="pc-link" href="<?= base_url('/report-inventory') ?>">Inventory</a>
                        </li>
                    </ul>
                </li>

                <!-- Sales Distribution -->
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-database-export"></i></span>
                        <span class="pc-mtext">Sales Distribution</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="<?= base_url('/report-so') ?>">Sales Order Status</a></li>
                        <li class="pc-item"><a class="pc-link" href="<?= base_url('/report-summary') ?>">Add. Doc Invoice </a></li>
                    </ul>
                </li>

                <!-- Financial -->
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-database-export"></i></span>
                        <span class="pc-mtext">Financial</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="<?= base_url('/report-dn') ?>">CN Status Report</a></li>
                        <li class="pc-item"><a class="pc-link" href="<?= base_url('/report-dn') ?>">DN Status Report</a></li>
                    </ul>
                </li>


                <!-- <li class="pc-item pc-caption">
                    <label>API</label>
                    <i class="ti ti-news"></i>
                </li>
                <li class="pc-item">
                    <a class="pc-link" target="_blank" href="<?= base_url('/doc-api') ?>"> -->
                <!-- <a class="pc-link" href="<?= base_url('/doc-api') ?>">
                    <span class="pc-micon"><i class="ti ti-screen-share"></i></span>
                    <span class="pc-mtext">Documentation</span>
                </a>
                </li> -->
            </ul>
        </div>
    </div>
</nav>