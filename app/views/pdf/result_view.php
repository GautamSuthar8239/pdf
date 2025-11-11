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
                        <div class="d-flex flex-wrap flex-md-nowrap gap-3 align-items-center justify-content-end">
                            <?php if ($hasSeller || $hasService): ?>
                                <div class="d-flex gap-2 flex-wrap">
                                    <!-- ✅ Show Download only when any valid data is present -->
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
                            <?php endif; ?>

                            <span class="text-white">|</span>

                            <!-- <div class="d-flex gap-2"> -->
                            <div class="stat-item text-center">
                                <div class="stat-number">
                                    <?php
                                    echo count($allFiles);
                                    ?>
                                </div>
                                <div class="stat-label">Files</div>
                            </div>
                            <span class="text-white">|</span>
                            <div class="stat-item text-center">
                                <div class="stat-number">
                                    <?= $raw_text ?>
                                </div>
                                <div class="stat-label">Characters</div>
                            </div>
                            <!-- </div> -->
                        </div>
                    </div>
                </div>


                <!-- Body -->
                <div class="card-body px-4 py-2">

                    <?php if ($hasAnyData): ?>
                        <!-- Navigation Tabs -->
                        <div class="d-flex flex-wrap tabs-scroll-wrapper align-items-center justify-content-between mb-3 border-bottom">
                            <ul class="nav nav-tabs gap-1 border-0"
                                id="resultTabs" role="tablist">
                                <?php if ($hasSeller && $hasService): ?>
                                    <!-- Show Combined only if both present -->
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link <?= $hasSeller && $hasService ? 'active' : '' ?> d-flex align-item-center justify-content-between gap-1 mb-0"
                                            data-bs-toggle="tab"
                                            data-bs-target="#combined"
                                            type="button">
                                            <i class="material-symbols-outlined mb-0 text-lg">dashboard</i>
                                            <span style="font-size:13px;">Combined View</span>
                                        </button>
                                    </li>

                                <?php endif; ?>

                                <?php if ($hasService): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= !$hasSeller && !$hasService ? '' : (!$hasSeller ? 'active' : '') ?>  d-flex align-item-center justify-content-between gap-1 mb-0"
                                            data-bs-toggle="tab" data-bs-target="#service-provider" type="button">
                                            <i class="material-symbols-outlined mb-0 text-lg">home_repair_service</i>
                                            <span style="font-size:13px;"> Service Providers</span>
                                        </button>
                                    </li>
                                <?php endif; ?>

                                <?php if ($hasSeller): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?= (!$hasService && !$hasSeller) ? '' : (!$hasService ? 'active' : '') ?> d-flex align-item-center justify-content-between gap-1 mb-0"
                                            data-bs-toggle="tab" data-bs-target="#seller" type="button">
                                            <!-- <i class="material-symbols-outlined mb-0">real_estate_agent</i> Sellers -->
                                            <i class="material-symbols-outlined mb-0 text-lg">real_estate_agent</i>
                                            <span style="font-size:13px;">Sellers</span>
                                        </button>
                                    </li>
                                <?php endif; ?>

                                <?php if (!empty($duplicates)): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link d-flex align-item-center justify-content-between gap-1 mb-0"
                                            data-bs-toggle="tab" data-bs-target="#duplicates-tab" type="button">
                                            <i class="material-symbols-outlined mb-0 text-lg">file_copy</i>
                                            <span style="font-size:13px;">Duplicates</span>
                                        </button>
                                    </li>
                                <?php endif; ?>

                                <!-- Details Tabs -->
                                <?php
                                $model          = new PdfModel();
                                $detailSections = $model->getDetailSections();
                                $filterMap      = $model->filterKeyMap();
                                $iconMap        = $model->getIconMap();
                                $uniqueCount = count($allData);
                                $duplicateGroupCount = count($duplicates);

                                // neglected files
                                $neglectedCount = 0;
                                foreach ($duplicates as $group) {
                                    $neglectedCount += max(0, count($group['files']) - 1);
                                }

                                // Count total duplicate files
                                $totalDuplicateFiles = 0;
                                foreach ($duplicates as $group) {
                                    $totalDuplicateFiles += count($group['files']);
                                }

                                $activeFilters = array_filter($filters ?? [], fn($v) => $v == 1);
                                ?>

                                <?php foreach ($detailSections as $sectionKey => $label): ?>

                                    <?php
                                    // Fallback filter key
                                    $filterKey = $filterMap[$sectionKey] ?? $sectionKey;

                                    // ✅ Only hide tabs IF at least one filter is actually active
                                    if (!empty($activeFilters)) {
                                        if (empty($activeFilters[$filterKey])) {
                                            continue;
                                        }
                                    }

                                    // ✅ Skip if no file contains this section
                                    $hasSection = false;
                                    foreach ($allData as $one) {
                                        if (!empty($one[$sectionKey])) {
                                            $hasSection = true;
                                            break;
                                        }
                                    }
                                    if (!$hasSection) continue;

                                    $icon = $iconMap[$filterKey] ?? 'info';
                                    ?>

                                    <li class="nav-item">
                                        <button class="nav-link d-flex align-item-center justify-content-between gap-1 mb-0"
                                            data-bs-toggle="tab"
                                            data-bs-target="#<?= $sectionKey ?>">
                                            <i class="material-symbols-outlined mb-0 text-lg"><?= $icon ?></i>
                                            <span style="font-size:13px;"><?= $label ?></span>
                                        </button>
                                    </li>

                                <?php endforeach; ?>


                                <!-- All Details always visible -->
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link d-flex align-item-center justify-content-between gap-2 mb-0"
                                        data-bs-toggle="tab" data-bs-target="#details" type="button">
                                        <i class="material-symbols-outlined mb-0 text-lg">list_alt_check</i>
                                        <span style="font-size:13px;">All Details</span>
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link d-flex align-item-center justify-content-between gap-2 mb-0"
                                        data-bs-toggle="tab" data-bs-target="#allFiles" type="button">
                                        <i class="material-symbols-outlined mb-0 text-lg">list</i>
                                        <span style="font-size:13px;">All Files</span>
                                    </button>
                                </li>
                            </ul>

                            <div class="d-flex gap-2 align-items-center justify-content-between  my-md-1 mt-0 mb-0">
                                <div id="duplicatesBadge" class="d-flex justify-content-center align-items-center mb-0">
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 fw-semibold">
                                        Duplicates: <?= $totalDuplicateFiles ?> files (<?= $duplicateGroupCount ?> groups, <?= $neglectedCount ?> ignored)
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
                                <div id="allFilesBadge" class="d-flex justify-content-center align-items-center d-none mb-0">

                                    <span class="badge bg-warning-subtle text-purple px-2 py-2">
                                        <?= count($allFiles) ?> File(s)
                                    </span>
                                    <span class="text-warning ms-2 mb-0 pb-0">|</span>
                                </div>

                                <?php foreach ($detailSections as $sectionKey => $label): ?>
                                    <div id="<?= $sectionKey ?>Badge"
                                        class="d-flex justify-content-center align-items-center d-none mb-0">
                                        <span class="badge bg-secondary-subtle text-dark px-2 py-2">
                                            <?= $model->countFilesWithSection($allData, $sectionKey) ?> File(s)
                                        </span>
                                        <span class="text-warning ms-2 mb-0 pb-0">|</span>
                                    </div>
                                <?php endforeach; ?>


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
                            </div>
                        </div>


                        <?php
                        if ($hasSeller && $hasService) {
                            $defaultActive = 'combined';
                        } elseif ($hasSeller) {
                            $defaultActive = 'seller';
                        } elseif ($hasService) {
                            $defaultActive = 'service-provider';
                        } else {
                            $defaultActive = 'allFiles';
                        }
                        ?>

                        <div class="tab-content mt-2">

                            <?php

                            usort($duplicates, function ($a, $b) {
                                return strtolower($a['company_name'] ?? '') <=> strtolower($b['company_name'] ?? '');
                            });

                            if (!empty($duplicates)):  ?>
                                <div class="tab-pane fade detail-card" id="duplicates-tab">
                                    <div class="table-responsive scroll-wrapper">
                                        <table class="table tablehover align-middle sticky-table">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <th class="sticky-col col-1">Company Name</th>
                                                    <th>Contact</th>
                                                    <th>Duplicate Files</th>
                                                    <!-- <th>Total Files</th> -->
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach ($duplicates as $dup): ?>
                                                    <tr>

                                                        <!-- ✅ company -->
                                                        <td class="sticky-col fw-bold text-orange">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="material-symbols-outlined text-muted" style="font-size:18px;">business</i>
                                                                <?= esc($dup['company_name']) ?>
                                                            </div>
                                                        </td>

                                                        <!-- ✅ contact -->
                                                        <td>
                                                            <i class="material-symbols-outlined text-muted" style="font-size:16px;">call</i>
                                                            <?= esc($dup['contact']) ?>
                                                        </td>

                                                        <!-- ✅ duplicate files list -->
                                                        <td>
                                                            <?php
                                                            $files = $dup['files'];
                                                            $firstFile = $files[0];
                                                            $extraFiles = array_slice($files, 1);
                                                            $extraCount = count($extraFiles);
                                                            $uid = 'files_' . md5($dup['contact']); // unique collapse id
                                                            ?>

                                                            <!-- ✅ INLINE: First File + "+X more" -->
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="material-symbols-outlined text-muted" style="font-size:16px;">description</i>
                                                                <span class="fw-semibold"><?= esc($firstFile['file_name']) ?></span>

                                                                <?php if ($extraCount > 0): ?>
                                                                    <a
                                                                        class="text-primary small fw-semibold ms-2 cursor-pointer"
                                                                        data-bs-toggle="collapse"
                                                                        href="#<?= $uid ?>"
                                                                        aria-expanded="false">
                                                                        +<?= $extraCount ?> more file(s)
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- ✅ COLLAPSIBLE list (visible only when expanded) -->
                                                            <?php if ($extraCount > 0): ?>
                                                                <div class="collapse mt-0" id="<?= $uid ?>">
                                                                    <?php foreach ($extraFiles as $file): ?>
                                                                        <div class="d-flex align-items-center gap-2 mb-0">
                                                                            <i class="material-symbols-outlined text-muted" style="font-size:16px;">description</i>
                                                                            <span><?= esc($file['file_name']) ?></span>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php
                            // ✅ Sort by company name (ascending A → Z)
                            usort($allData, function ($a, $b) {
                                $nameA = strtolower($a['service_provider_details']['company_name']
                                    ?? $a['seller_details']['company_name']
                                    ?? '');
                                $nameB = strtolower($b['service_provider_details']['company_name']
                                    ?? $b['seller_details']['company_name']
                                    ?? '');

                                return $nameA <=> $nameB;  // ascending order
                            });
                            ?>
                            <?php if ($hasSeller && $hasService): ?>
                                <!-- Combined View -->
                                <div class="tab-pane fade detail-card <?= $defaultActive === 'combined' ? 'show active' : '' ?>" id="combined">
                                    <div class="table-responsive scroll-wrapper">
                                        <table class="table tablehover align-middle">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <th>File Name</th>
                                                    <th>Type</th>
                                                    <th>Company Name</th>
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
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="material-symbols-outlined text-muted" style="font-size:18px;">description</i>
                                                                <?= esc(basename($data['base_name'])); ?>
                                                            </div>
                                                        </td>
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

                            <?php
                            // ✅ Sort by company name (ascending A → Z)
                            usort($allFiles, function ($a, $b) {
                                $nameA = strtolower($a['service_provider_details']['company_name']
                                    ?? $a['seller_details']['company_name']
                                    ?? '');
                                $nameB = strtolower($b['service_provider_details']['company_name']
                                    ?? $b['seller_details']['company_name']
                                    ?? '');

                                return $nameA <=> $nameB;  // ascending order
                            });
                            $duplicateCompanies = [];

                            foreach ($duplicates as $group) {
                                $name = strtolower(trim($group['company_name']));
                                if ($name !== '') {
                                    $duplicateCompanies[$name] = true;
                                }
                            }
                            ?>

                            <!-- all Files -->
                            <div class="tab-pane fade detail-card <?= $defaultActive === 'allFiles' ? 'show active' : '' ?>" id="allFiles">
                                <div class="table-responsive scroll-wrapper">
                                    <table class="table tablehover align-middle">
                                        <thead class="table-light text-uppercase small">
                                            <tr>
                                                <th>File Name</th>
                                                <th>Type</th>
                                                <th>Company Name</th>
                                                <th>GeM Seller ID</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>GSTIN</th>
                                                <th>Address</th>
                                                <th>Registration</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($allFiles as $data): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i class="material-symbols-outlined text-muted" style="font-size:18px;">description</i>
                                                            <?= esc(basename($data['base_name'])); ?>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <?php
                                                        if (!empty($data['service_provider_details']) && !empty($data['seller_details'])) echo 'Both';
                                                        elseif (!empty($data['service_provider_details'])) echo 'Service Provider';
                                                        elseif (!empty($data['seller_details'])) echo 'Seller';
                                                        else echo '---';
                                                        ?>
                                                    </td>

                                                    <!-- <td class="fw-bold text-orange">
                                                        <?= esc($data['service_provider_details']['company_name']
                                                            ?? $data['seller_details']['company_name']
                                                            ?? '---'); ?>
                                                    </td> -->
                                                    <?php
                                                    $company = $data['service_provider_details']['company_name']
                                                        ?? $data['seller_details']['company_name']
                                                        ?? '---';

                                                    $key = strtolower(trim($company));
                                                    $isDuplicate = isset($duplicateCompanies[$key]);
                                                    ?>
                                                    <td class="fw-bold <?= $isDuplicate ? ' duplicate-company ' : ' text-orange ' ?>">
                                                        <!-- <span class="<?= $isDuplicate ? ' duplicate-company ' : ' text-orange ' ?>"> -->
                                                        <?= esc($company) ?>
                                                        <!-- </span> -->
                                                    </td>


                                                    <td><?= esc($data['seller_details']['gem_seller_id'] ?? '---'); ?></td>

                                                    <td><?= esc($data['service_provider_details']['contact_number']
                                                            ?? $data['seller_details']['contact_number']
                                                            ?? '---'); ?></td>

                                                    <td><?= esc($data['service_provider_details']['email']
                                                            ?? $data['seller_details']['email']
                                                            ?? '---'); ?></td>

                                                    <td><?= esc($data['service_provider_details']['gstin']
                                                            ?? $data['seller_details']['gstin']
                                                            ?? '---'); ?></td>

                                                    <td><?= esc($data['service_provider_details']['address']
                                                            ?? $data['seller_details']['address']
                                                            ?? '---'); ?></td>

                                                    <td class="fw-semibold text-success">
                                                        <?= esc($data['service_provider_details']['msme_registration']
                                                            ?? $data['seller_details']['msme_registration']
                                                            ?? '---'); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <?php
                            $serviceData = array_filter($allData, fn($d) => !empty($d['service_provider_details']));

                            usort($serviceData, function ($a, $b) {
                                $nameA = strtolower($a['service_provider_details']['company_name'] ?? '');
                                $nameB = strtolower($b['service_provider_details']['company_name'] ?? '');
                                return $nameA <=> $nameB;
                            });

                            $sellerData = array_filter($allData, fn($d) => !empty($d['seller_details']));

                            usort($sellerData, function ($a, $b) {
                                $nameA = strtolower($a['seller_details']['company_name'] ?? '');
                                $nameB = strtolower($b['seller_details']['company_name'] ?? '');
                                return $nameA <=> $nameB;
                            });
                            ?>

                            <?php if ($hasService): ?>
                                <!-- Service Provider -->
                                <div class="tab-pane fade detail-card <?= $defaultActive === 'service-provider' ? 'show active' : '' ?>" id="service-provider">
                                    <div class="table-responsive scroll-wrapper">
                                        <table class="table tablehover align-middle sticky-table">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <!-- <th class="sticky-col col-1">File Name</th> -->
                                                    <th class="sticky-col col-1">Company Name</th>
                                                    <th>GeM Seller ID</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>GSTIN</th>
                                                    <th>MSME Registration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($serviceData as $data): ?>
                                                    <?php if (!empty($data['service_provider_details'])): ?>
                                                        <tr>
                                                            <!-- <td class="sticky-col col-1"><?= esc(basename($data['file_name'])); ?></td> -->
                                                            <td class="sticky-col col-1 fw-bold text-orange">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <i class="material-symbols-outlined text-muted" style="font-size:18px;">business</i>
                                                                    <?= esc($data['service_provider_details']['company_name'] ?? '---'); ?>
                                                                </div>
                                                            </td>
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
                                <div class="tab-pane fade detail-card <?= $defaultActive === 'seller' ? 'show active' : '' ?>" id="seller">
                                    <div class="table-responsive scroll-wrapper">
                                        <table class="table tablehover align-middle sticky-table">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <!-- <th class="sticky-col col-1">File Name</th> -->
                                                    <th class="sticky-col col-1">Company Name</th>
                                                    <th>GeM Seller ID</th>
                                                    <th>Contact</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>GSTIN</th>
                                                    <th>Registration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($sellerData as $data): ?>
                                                    <?php if (!empty($data['seller_details'])): ?>
                                                        <tr>
                                                            <!-- <td class="sticky-col col-1"><?= esc(basename($data['file_name'])); ?></td> -->
                                                            <td class="sticky-col col-1 fw-bold text-orange">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <i class="material-symbols-outlined text-muted" style="font-size:18px;">business</i>
                                                                    <?= esc($data['seller_details']['company_name'] ?? '---'); ?>
                                                                </div>
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

                            <?php
                            function renderDetailTable($sectionKey, $label, $allData)
                            {
                                $rows = [];

                                foreach ($allData as $data) {
                                    if (!empty($data[$sectionKey])) {
                                        $rows[] = [
                                            'file_name' => basename($data['base_name']),
                                            'values'    => $data[$sectionKey]
                                        ];
                                    }
                                }

                                if (empty($rows)) return ''; // nothing to show

                                // Extract headers (keys inside section)
                                $headers = array_keys($rows[0]['values']);

                                ob_start(); ?>

                                <div class="tab-pane fade detail-card" id="<?= $sectionKey ?>">
                                    <div class="table-responsive scroll-wrapper">

                                        <table class="table tablehover align-middle sticky-table">
                                            <thead class="table-light text-uppercase small">
                                                <tr>
                                                    <th class="sticky-col col-1">File Name</th>
                                                    <?php foreach ($headers as $h): ?>
                                                        <th><?= esc(str_replace('_', ' ', $h)); ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php foreach ($rows as $row): ?>
                                                    <tr>
                                                        <td class="sticky-col col-1 fw-bold text-orange">
                                                            <?= esc($row['file_name']) ?>
                                                        </td>

                                                        <?php foreach ($headers as $h): ?>
                                                            <td><?= esc($row['values'][$h] ?? '---'); ?></td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>

                                        </table>

                                    </div>
                                </div>

                            <?php return ob_get_clean();
                            }

                            $detailSections = (new PdfModel())->getDetailSections();  // full list
                            $filterMap      = (new PdfModel())->filterKeyMap();       // convert keys

                            foreach ($detailSections as $sectionKey => $label):

                                // Convert extracted key → filter key
                                $filterKey = $filterMap[$sectionKey] ?? null;

                                // ✅ Show ONLY filtered sections
                                if (!empty($filters)) {
                                    if (empty($filters[$filterKey])) {
                                        continue;   // skip unselected
                                    }
                                }

                                // ✅ Check if any file contains this section
                                $hasSection = false;
                                foreach ($allData as $one) {
                                    if (!empty($one[$sectionKey])) {
                                        $hasSection = true;
                                        break;
                                    }
                                }
                                if (!$hasSection) continue;

                                // ✅ Render table
                                echo renderDetailTable($sectionKey, $label, $allData);

                            endforeach;
                            ?>

                            <!-- All Details -->
                            <div class="tab-pane fade " id="details">
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
<style>
    .duplicate-company {
        background: #fadcc5 !important;
        color: #d35400 !important;
        padding: 2px 6px;
        border-radius: 4px;
    }
</style>