<div class="container-fluid min-vh-75 px-2 py-1">
    <div class="row gx-3 mt-2 align-items-start ">
        <div class="col-lg-12 mt-3 " style="min-height:300px">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden mb-2">

                <!-- Header -->
                <div class="card-header bg-orange border-radius-lg py-2 px-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <h5 class="mb-0 text-white d-flex align-items-center gap-2">
                            <i class="material-symbols-outlined">file_export</i>
                            Extraction Results
                        </h5>

                        <div class="d-flex flex-wrap flex-md-nowrap gap-3 align-items-center justify-content-end">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="<?= esc($excel_path); ?>"
                                    class="btn btn-outline-lavender d-flex align-items-center gap-2 mb-0 py-1 px-3"
                                    download title="Download Excel File">
                                    <i class="material-symbols-outlined">download</i>
                                </a>

                                <a href="/pdf"
                                    class="btn btn-outline-lavender d-flex align-items-center gap-1 mb-0 py-1 px-3"
                                    title="Back to Upload Page">
                                    <i class="material-symbols-outlined">arrow_back</i>
                                </a>
                            </div>

                            <span class="text-white">|</span>

                            <!-- <div class="d-flex gap-2"> -->
                            <div class="stat-item text-center">
                                <div class="stat-number">
                                    <?php
                                    $totalFiles = array_merge($allData, $duplicates);
                                    echo count($totalFiles);
                                    ?>
                                </div>
                                <div class="stat-label">Files</div>
                            </div>
                            <span class="text-white">|</span>
                            <div class="stat-item text-center">
                                <div class="stat-number">
                                    <?= number_format(array_sum(array_map(fn($d) => strlen($d['raw_text']), $totalFiles))); ?>
                                </div>
                                <div class="stat-label">Characters</div>
                            </div>
                            <!-- </div> -->
                        </div>
                    </div>
                </div>


                <!-- Body -->
                <div class="card-body px-4 py-2">
                    <?php
                    // Check data presence
                    $hasSeller = false;
                    $hasService = false;

                    foreach ($allData as $data) {
                        if (!empty($data['seller_details'])) $hasSeller = true;
                        if (!empty($data['service_provider_details'])) $hasService = true;
                    }

                    $hasAnyData = $hasSeller || $hasService;
                    ?>

                    <?php if ($hasAnyData): ?>
                        <!-- Navigation Tabs -->
                        <div class="d-flex flex-wrap tabs-scroll-wrapper align-items-center justify-content-between mb-3 border-bottom">
                            <ul class="nav nav-tabs gap-1 border-0"
                                id="resultTabs" role="tablist">

                                <?php if ($hasSeller && $hasService): ?>
                                    <!-- Show Combined only if both present -->
                                    <li class="nav-item" role="presentation">

                                        <button class="nav-link <?= $hasSeller && $hasService ? 'active' : '' ?> d-flex align-item-center justify-content-between gap-2 mb-0"
                                            data-bs-toggle="tab" data-bs-target="#combined" type="button">
                                            <i class="material-symbols-outlined mb-0">dashboard</i> Combined View
                                        </button>
                                    </li>
                                <?php endif; ?>

                                <?php if ($hasService): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= !$hasSeller && !$hasService ? '' : (!$hasSeller ? 'active' : '') ?> d-flex align-item-center justify-content-between gap-2 mb-0"
                                            data-bs-toggle="tab" data-bs-target="#service-provider" type="button">
                                            <i class="material-symbols-outlined mb-0">home_repair_service</i> Service Providers
                                        </button>
                                    </li>
                                <?php endif; ?>

                                <?php if ($hasSeller): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= (!$hasService && !$hasSeller) ? '' : (!$hasService ? 'active' : '') ?> d-flex align-item-center justify-content-between gap-2 mb-0"
                                            data-bs-toggle="tab" data-bs-target="#seller" type="button">
                                            <i class="material-symbols-outlined mb-0">real_estate_agent</i> Sellers
                                        </button>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($duplicates)): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link d-flex align-item-center justify-content-between gap-2 mb-0"
                                            data-bs-toggle="tab" data-bs-target="#duplicates-tab" type="button">
                                            <i class="material-symbols-outlined mb-0">file_copy</i> Duplicates
                                        </button>
                                    </li>
                                <?php endif; ?>


                                <!-- All Details always visible -->
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link d-flex align-item-center justify-content-between gap-2 mb-0"
                                        data-bs-toggle="tab" data-bs-target="#details" type="button">
                                        <i class="material-symbols-outlined mb-0">list</i> All Details
                                    </button>
                                </li>

                            </ul>

                            <div class="d-flex gap-2 align-items-center justify-content-between mb-sm-2 mb-0">
                                <div id="duplicatesBadge" class="d-flex justify-content-center align-items-center d-none mb-0">
                                    <span class="badge bg-danger-subtle text-danger px-2 py-2">
                                        <?= count($duplicates) ?> File(s)
                                    </span>
                                    <span class="text-warning ms-2 mb-0 pb-0">|</span>
                                </div>

                                <div id="sellerBadge" class="d-flex justify-content-center align-items-center d-none mb-0">
                                    <span class="badge bg-info-subtle text-info px-2 py-2">
                                        <?= count(array_filter($allData, fn($d) => !empty($d['seller_details']))) ?> File(s)
                                    </span>
                                    <span class="text-warning ms-2 mb-0 pb-0">|</span>
                                </div>

                                <div id="serviceProviderBadge" class="d-flex justify-content-center align-items-center d-none mb-0">
                                    <span class="badge bg-warning-subtle text-purple px-2 py-2">
                                        <?= count(array_filter($allData, fn($d) => !empty($d['service_provider_details']))) ?> File(s)
                                    </span>
                                    <span class="text-warning ms-2 mb-0 pb-0">|</span>
                                </div>
                                <div id="combinedBadge" class="d-flex justify-content-center align-items-center d-none mb-0">
                                    <span class="badge bg-warning-subtle text-purple px-2 py-2">
                                        <?= count($allData) ?> File(s)
                                    </span>
                                    <span class="text-warning ms-2 mb-0 pb-0">|</span>
                                </div>

                                <div id="detailsToggleBtns" class="d-flex justify-content-center align-items-center d-none mb-0">
                                    <div class="btn-group btn-group-sm border-radius-sm rounded-0 mb-0 pb-0" role="group">
                                        <button type="button" id="freeViewBtn" title="Free View"
                                            class="btn border-radius-md btn-outline-lavender active mb-0 d-flex align-items-center">
                                            <i class="material-symbols-outlined" style="font-size:18px;">view_headline</i>
                                        </button>

                                        <button type="button" id="scrollViewBtn" title="Scroll View"
                                            class="btn border-radius-md btn-outline-lavender mb-0 d-flex align-items-center">
                                            <i class="material-symbols-outlined" style="font-size:18px;">unfold_more_double</i>
                                        </button>

                                    </div>
                                    <span class="text-warning ms-2 mb-0 pb-0">|</span>
                                </div>
                                <!-- <div class="stat-item text-center">
                                    <div class="stat-number">
                                        <?php
                                        $totalFiles = array_merge($allData, $duplicates);
                                        count($totalFiles) > 0 ? print(count($totalFiles)) : print(0);
                                        ?>
                                    </div>
                                    <div class="stat-label">Files</div>
                                </div>
                                <span class="text-warning">|</span>
                                <div class="stat-item text-center">
                                    <div class="stat-number">
                                        <?= number_format(array_sum(array_map(fn($d) => strlen($d['raw_text']), $totalFiles))); ?>
                                    </div>
                                    <div class="stat-label">Characters</div>
                                </div> -->
                            </div>
                        </div>

                        <div class="tab-content mt-2">

                            <?php if (!empty($duplicates)): ?>
                                <div class="tab-pane fade detail-card" id="duplicates-tab">
                                    <div class="table-responsive scroll-wrapper">
                                        <table class="table tablehover align-middle sticky-table">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <th class="sticky-col col-1">Original File</th>
                                                    <th>Reason</th>
                                                    <th>Duplicate File</th>
                                                    <th>Size</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach ($duplicates as $i => $d): ?>
                                                    <?php $reason = str_replace('_', ' ', $d['duplicate_reason']); ?>
                                                    <tr>

                                                        <td class="sticky-col col-1 fw-bold text-orange">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="material-symbols-outlined text-muted" style="font-size: 18px;">description</i>
                                                                <span class="fw-semibold"><?= esc($d['file_name']) ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $reasonLabel = match ($reason) {
                                                                'full duplicate'        => 'Exact Duplicate (Same Name + Content)',
                                                                'duplicate name only'   => 'Name Duplicate (Different Content)',
                                                                'duplicate content only' => 'Content Duplicate (Different Name)',
                                                                default                 => ucfirst(str_replace('_', ' ', $reason))
                                                            };
                                                            ?>
                                                            <!-- <span class="badge bg-warning-subtle text-warning px-3 py-1"> -->
                                                            <?= esc($reasonLabel) ?>
                                                            <!-- </span> -->

                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="material-symbols-outlined text-muted" style="font-size: 16px;">content_copy</i>
                                                                <span class="fw-semibold"> <?= esc(basename($d['duplicate_of'])) ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?= number_format($d['size_bytes'] / 1024, 1) ?> KB
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($hasSeller && $hasService): ?>
                                <!-- Combined View -->
                                <div class="tab-pane fade show active detail-card" id="combined">
                                    <div class="table-responsive scroll-wrapper">
                                        <table class="table tablehover align-middle">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <th>File Name</th>
                                                    <th>Type</th>
                                                    <th>Company</th>
                                                    <th>GeM Seller ID</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>GSTIN</th>
                                                    <th>Address</th>
                                                    <th>Registration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($allData as $data): ?>
                                                    <tr>
                                                        <td><?= esc(basename($data['base_name'])); ?></td>
                                                        <td>
                                                            <?php
                                                            if (!empty($data['service_provider_details']) && !empty($data['seller_details'])) echo 'Both';
                                                            elseif (!empty($data['service_provider_details'])) echo 'Service Provider';
                                                            elseif (!empty($data['seller_details'])) echo 'Seller';
                                                            else echo '---';
                                                            ?>
                                                        </td>
                                                        <td class="fw-bold text-orange">
                                                            <?= esc($data['service_provider_details']['company_name'] ?? $data['seller_details']['company_name'] ?? '---'); ?>
                                                        </td>
                                                        <td><?= esc($data['seller_details']['gem_seller_id'] ?? '---'); ?></td>
                                                        <td><?= esc($data['service_provider_details']['contact_number'] ?? $data['seller_details']['contact_number'] ?? '---'); ?></td>
                                                        <td><?= esc($data['service_provider_details']['email'] ?? $data['seller_details']['email'] ?? '---'); ?></td>
                                                        <td><?= esc($data['service_provider_details']['gstin'] ?? $data['seller_details']['gstin'] ?? '---'); ?></td>
                                                        <td><?= esc($data['service_provider_details']['address'] ?? $data['seller_details']['address'] ?? '---'); ?></td>
                                                        <td class="fw-semibold text-success">
                                                            <?= esc($data['service_provider_details']['msme_registration'] ?? $data['seller_details']['msme_registration'] ?? '---'); ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($hasService): ?>
                                <!-- Service Provider -->
                                <div class="tab-pane fade <?= (!$hasSeller && !$hasService) ? 'show active' : (!$hasSeller && $hasService ? 'show active' : '') ?> detail-card" id="service-provider">
                                    <div class="table-responsive scroll-wrapper">
                                        <table class="table tablehover align-middle sticky-table">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <!-- <th class="sticky-col col-1">File Name</th> -->
                                                    <th class="sticky-col col-1">Company</th>
                                                    <th>GeM Seller ID</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>GSTIN</th>
                                                    <th>MSME Registration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($allData as $data): ?>
                                                    <?php if (!empty($data['service_provider_details'])): ?>
                                                        <tr>
                                                            <!-- <td class="sticky-col col-1"><?= esc(basename($data['file_name'])); ?></td> -->
                                                            <td class="sticky-col col-1 fw-bold text-orange"><?= esc($data['service_provider_details']['company_name'] ?? '---'); ?></td>
                                                            <td><?= esc($data['service_provider_details']['gem_seller_id'] ?? '---'); ?></td>
                                                            <td><?= esc($data['service_provider_details']['contact_number'] ?? '---'); ?></td>
                                                            <td><?= esc($data['service_provider_details']['email'] ?? '---'); ?></td>
                                                            <td><?= esc($data['service_provider_details']['address'] ?? '---'); ?></td>
                                                            <td><?= esc($data['service_provider_details']['gstin'] ?? '---'); ?></td>
                                                            <td><?= esc($data['service_provider_details']['msme_registration'] ?? '---'); ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($hasSeller): ?>
                                <!-- Seller -->
                                <div class="tab-pane fade <?= (!$hasService && $hasSeller ? 'show active' : '') ?> detail-card" id="seller">
                                    <div class="table-responsive scroll-wrapper">
                                        <table class="table tablehover align-middle sticky-table">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <!-- <th class="sticky-col col-1">File Name</th> -->
                                                    <th class="sticky-col col-1">Company</th>
                                                    <th>GeM Seller ID</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>GSTIN</th>
                                                    <th>Registration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($allData as $data): ?>
                                                    <?php if (!empty($data['seller_details'])): ?>
                                                        <tr>
                                                            <!-- <td class="sticky-col col-1"><?= esc(basename($data['file_name'])); ?></td> -->
                                                            <td class="sticky-col col-1 fw-bold text-orange">
                                                                <?= esc($data['seller_details']['company_name'] ?? '---'); ?>
                                                            </td>
                                                            <td><?= esc($data['seller_details']['gem_seller_id'] ?? '---'); ?></td>
                                                            <td><?= esc($data['seller_details']['contact_number'] ?? '---'); ?></td>
                                                            <td><?= esc($data['seller_details']['email'] ?? '---'); ?></td>
                                                            <td><?= esc($data['seller_details']['address'] ?? '---'); ?></td>
                                                            <td><?= esc($data['seller_details']['gstin'] ?? '---'); ?></td>
                                                            <td><?= esc($data['seller_details']['msme_registration'] ?? '---'); ?></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>


                            <!-- All Details -->
                            <div class="tab-pane fade <?= (!$hasSeller && !$hasService ? 'show active' : '') ?> " id="details">
                                <div id="detailsFreeView">
                                    <?php foreach ($allData as $index => $data): ?>
                                        <div class="card detail-card mb-2 border shadow-sm">
                                            <div class="detail-card-header d-flex align-items-center justify-content-between py-2 px-3">
                                                <h6 class="mb-0 d-flex align-items-center">
                                                    <i class="material-icons me-2 text-orange">description</i>
                                                    <?= esc(basename($data['base_name'])); ?>
                                                </h6>
                                                <?php if (!empty($data['service_provider_details'])): ?>
                                                    <h6 class="fw-semibold text-info d-flex align-items-center justify-content-between gap-2 mb-0"><i class="material-symbols-outlined text-danger">home_repair_service</i> Service Provider Details</h6>
                                                <?php elseif (!empty($data['seller_details'])): ?>
                                                    <h6 class="fw-semibold text-info d-flex align-items-center justify-content-between gap-2 mb-0"><i class="material-symbols-outlined text-warning">real_estate_agent</i> Seller Details</h6> <?php endif; ?>
                                            </div>
                                            <div class="detail-card-body p-2">
                                                <?php if (!empty($data['service_provider_details'])): ?>
                                                    <div class="row mb-2">
                                                        <?php foreach ($data['service_provider_details'] as $key => $value): ?>
                                                            <div class="col-md-3 mb-2">
                                                                <div class="detail-item bg-white border rounded-3 p-2 px-2 ">
                                                                    <small class="fw-semibold text-uppercase text-bold mb-0" style="font-size: 0.90rem">
                                                                        <?= esc(str_replace('_', ' ', $key)); ?>
                                                                    </small>

                                                                    <?php if ($key === 'address'): ?>
                                                                        <div class="ms-2 fw-semibold mt-0" style="font-size: 0.85rem">
                                                                            <?= esc($value ?? '---'); ?>
                                                                        </div>
                                                                    <?php elseif ($key === 'company_name'): ?>
                                                                        <div class="ms-2 fw-semibold text-orange mt-0" style="font-size: 0.85rem">
                                                                            <?= esc($value ?? '---'); ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="ms-2 fw-medium text-dark mt-0" style="font-size: 0.85rem">
                                                                            <?= esc($value ?? '---'); ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($data['seller_details'])): ?>
                                                    <div class="row mb-2">
                                                        <?php foreach ($data['seller_details'] as $key => $value): ?>
                                                            <div class="col-md-3 mb-2">
                                                                <div class="detail-item bg-white border rounded-3 p-2 px-2 ">
                                                                    <small class="fw-semibold text-uppercase text-bold mb-0" style="font-size: 0.90rem">
                                                                        <?= esc(str_replace('_', ' ', $key)); ?>
                                                                    </small>

                                                                    <?php if ($key === 'address'): ?>
                                                                        <div class="ms-2 fw-semibold mt-0" style="font-size: 0.85rem">
                                                                            <?= esc($value ?? '---'); ?>
                                                                        </div>
                                                                    <?php elseif ($key === 'company_name'): ?>
                                                                        <div class="ms-2 fw-semibold text-orange mt-0" style="font-size: 0.85rem">
                                                                            <?= esc($value ?? '---'); ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="ms-2 fw-medium text-dark mt-0" style="font-size: 0.85rem">
                                                                            <?= esc($value ?? '---'); ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div id="detailsScrollView" class="d-none">
                                    <div class="scroll-wrapper" style="max-height:400px; overflow-y:auto;">
                                        <?php foreach ($allData as $index => $data): ?>
                                            <div class="card detail-card mb-2 border shadow-sm">
                                                <div class="detail-card-header d-flex align-items-center justify-content-between py-2 px-3">
                                                    <h6 class="mb-0 d-flex align-items-center">
                                                        <i class="material-icons me-2 text-orange">description</i>
                                                        <?= esc(basename($data['base_name'])); ?>
                                                    </h6>
                                                    <?php if (!empty($data['service_provider_details'])): ?>
                                                        <h6 class="fw-semibold text-info d-flex align-items-center justify-content-between gap-2 mb-0"><i class="material-symbols-outlined text-danger">home_repair_service</i> Service Provider Details</h6>
                                                    <?php elseif (!empty($data['seller_details'])): ?>
                                                        <h6 class="fw-semibold text-info d-flex align-items-center justify-content-between gap-2 mb-0"><i class="material-symbols-outlined text-warning">real_estate_agent</i> Seller Details</h6> <?php endif; ?>
                                                </div>
                                                <div class="detail-card-body p-2">
                                                    <?php if (!empty($data['service_provider_details'])): ?>
                                                        <div class="row mb-2">
                                                            <?php foreach ($data['service_provider_details'] as $key => $value): ?>
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="detail-item bg-white border rounded-3 p-2 px-2 ">
                                                                        <small class="fw-semibold text-uppercase text-bold mb-0" style="font-size: 0.90rem">
                                                                            <?= esc(str_replace('_', ' ', $key)); ?>
                                                                        </small>

                                                                        <?php if ($key === 'address'): ?>
                                                                            <div class="ms-2 fw-semibold mt-0" style="font-size: 0.85rem">
                                                                                <?= esc($value ?? '---'); ?>
                                                                            </div>
                                                                        <?php elseif ($key === 'company_name'): ?>
                                                                            <div class="ms-2 fw-semibold text-orange mt-0" style="font-size: 0.85rem">
                                                                                <?= esc($value ?? '---'); ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <div class="ms-2 fw-medium text-dark mt-0" style="font-size: 0.85rem">
                                                                                <?= esc($value ?? '---'); ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if (!empty($data['seller_details'])): ?>
                                                        <div class="row mb-2">
                                                            <?php foreach ($data['seller_details'] as $key => $value): ?>
                                                                <div class="col-md-3 mb-2">
                                                                    <div class="detail-item bg-white border rounded-3 p-2 px-2 ">
                                                                        <small class="fw-semibold text-uppercase text-bold mb-0" style="font-size: 0.90rem">
                                                                            <?= esc(str_replace('_', ' ', $key)); ?>
                                                                        </small>

                                                                        <?php if ($key === 'address'): ?>
                                                                            <div class="ms-2 fw-semibold mt-0" style="font-size: 0.85rem">
                                                                                <?= esc($value ?? '---'); ?>
                                                                            </div>
                                                                        <?php elseif ($key === 'company_name'): ?>
                                                                            <div class="ms-2 fw-semibold text-orange mt-0" style="font-size: 0.85rem">
                                                                                <?= esc($value ?? '---'); ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <div class="ms-2 fw-medium text-dark mt-0" style="font-size: 0.85rem">
                                                                                <?= esc($value ?? '---'); ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- No Data Message -->
                        <div class="text-center py-5 user-select-none">
                            <i class="material-symbols-outlined text-muted" style="font-size: 64px;">error_outline</i>
                            <h5 class="mt-3 text-secondary">No valid Seller or Service Provider data found.</h5>
                            <p class="text-muted">Please upload a valid PDF to extract details.</p>
                            <a href="/pdf" class="align-self-center btn btn-outline-purple mt-3 py-2 px-2 d-flex align-items-center gap-1" style="width: fit-content;">
                                <i class="material-symbols-outlined">arrow_back</i> Back to Upload
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>