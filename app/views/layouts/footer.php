</div>
</main>

<?php if ($toast = Flash::get('toast')): ?>
    <div class="toast-container-custom position-fixed bottom-0 end-0 p-3 user-select-none">
        <div class="toast show text-bg-<?= htmlspecialchars($toast['type']); ?> border-0 shadow-lg fade-out"
            role="alert" aria-live="assertive" aria-atomic="true" id="flashToast">
            <div class="d-flex align-items-start justify-content-between">
                <div class="toast-body d-flex align-items-start gap-2">
                    <i class="material-symbols-rounded fs-4 flex-shrink-0"><?= htmlspecialchars($toast['icon']); ?></i>
                    <div>
                        <?php if (is_array($toast['message'])): ?>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($toast['message'] as $group): ?>
                                    <?php if (is_array($group)): ?>
                                        <?php foreach ($group as $message): ?>
                                            <li><?= $message; /* allow HTML */ ?></li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li><?= $group; /* allow HTML */ ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?= $toast['message']; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 mt-2"
                    data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-progress-bar"></div>
        </div>
    </div>
<?php endif; ?>

<!-- Top Alert Card -->
<div id="topAlert" class="position-fixed start-50 translate-middle-x d-none user-select-none"
    style="top: 20; z-index: 9999; max-width: 550px; width: 90%;">
    <div class="alert rounded-4 border-0 text-center d-flex flex-column align-items-center p-3 pb-2 mb-0 animate-slide-middle">
        <div class="alert-icon-wrapper mb-2 d-flex align-items-center justify-content-center rounded-circle">
            <i id="topAlertIcon" class="material-symbols-rounded text-5xl">info</i>
        </div>

        <div id="topAlertMessage" class="fw-semibold fs-6 mb-1 text-center">
            Message goes here.
        </div>

        <div id="alertDivider" class="w-100 my-1 border-top border-light opacity-25"></div>

        <div id="topAlertActions" class="d-none w-100 mt-1">
            <button id="alertConfirmBtn" class="btn btn-light shadow-none btn-sm px-3 me-2 mb-0">Yes</button>
            <button id="alertCancelBtn" class="btn btn-outline-light btn-sm px-3 mb-0">Cancel</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript" defer src="<?= ROOT; ?>/assets/js/main.js"></script>
<script src="<?= ROOT; ?>/assets/js/common.js"></script>
<script src="<?= ROOT; ?>/assets/js/headline.js"></script>
<script src="<?= ROOT; ?>/assets/js/core/popper.min.js"></script>
<script src="<?= ROOT; ?>/assets/js/core/bootstrap.min.js"></script>
<script src="<?= ROOT; ?>/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="<?= ROOT; ?>/assets/js/plugins/smooth-scrollbar.min.js"></script>

<script src=" https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>

<!-- Github buttons -->
<!-- <script async defer src="https://buttons.github.io/buttons.js"></script> -->
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="<?= ROOT; ?>/assets/js/material-dashboard.min.js"></script>
</body>

</html>