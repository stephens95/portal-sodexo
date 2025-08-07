    </div>
    </div>
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm-6 my-1">
                    <p class="m-0">
                        <?= view_cell(\App\Cells\LatestVersionCell::class . '::version') ?>
                        <!-- Sodexo Portal &copy; September - 2025 -->
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Required Js -->
    <script src="<?= base_url('assets/js/plugins/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/simplebar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/icon/custom-font.js') ?>"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <script src="<?= base_url('assets/js/theme.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugins/feather.min.js') ?>"></script>


    <script>
        layout_change('light');
    </script>

    <script>
        font_change('Roboto');
    </script>

    <script>
        change_box_container('false');
    </script>

    <script>
        layout_caption_change('true');
    </script>

    <script>
        layout_rtl_change('false');
    </script>

    <script>
        preset_change('preset-1');
    </script>
    <script src="<?= base_url('assets/js/plugins/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/pages/dashboard-default.js') ?>"></script>
    </body>

    </html>