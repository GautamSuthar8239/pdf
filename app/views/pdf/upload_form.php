<div class="container-fluid min-vh-75 px-2 py-1">
    <div class="row g-4 align-items-start justify-content-center pt-1" id="uploadRow">
        <!-- Upload Section -->
        <div class="col-lg-8 col-md-10" id="uploadCol">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header border-radius-lg bg-orange d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-white d-flex align-items-center gap-2">
                        <i class="material-symbols-outlined">cloud_upload</i> Upload PDF Files
                    </h5>

                    <!-- Checkbox dropdown for selecting data to show and export to Excel -->
                    <div class="dropdown">
                        <a class="btn btn-light d-flex align-items-center gap-1 px-2 py-1 shadow-sm hover-lift mb-0"
                            data-bs-toggle="dropdown" aria-expanded="false" role="button"
                            style="border-radius: 6px; border: 1px solid #e5e7eb; transition: all 0.2s;">
                            <i class="material-symbols-outlined text-orange" style="font-size: 18px;">tune</i>
                            <span class="fw-semibold text-orange" style="font-size: 13px;">Filter Data</span>
                            <i class="material-symbols-outlined text-orange ms-1" style="font-size: 16px;">expand_more</i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end p-2 border-0 shadow-lg"
                            style="min-width: 240px; border-radius: 10px; margin-top: 6px;">

                            <!-- Header -->
                            <div class="mb-2 border-bottom gap-2 d-flex align-items-center ">
                                <div class="form-check m-0 ps-1 p-0">
                                    <input class="form-check-input" id="selectAllData"
                                        type="checkbox"
                                        style="width: 18px; height: 18px;">
                                </div>

                                <div class="d-flex align-items-start flex-column gap-0">
                                    <h6 class="mb-0 fw-bold" style="color: #1f2937; font-size: 12px;">
                                        Select Data to Display
                                    </h6>
                                    <small class="text-muted ms-2" style="font-size: 10px;">
                                        Choose which columns to show
                                    </small>
                                </div>
                            </div>

                            <!-- ✅ Dynamic Filter Options -->
                            <div class="filter-options">

                                <?php
                                // Icon mapping
                                $iconMap = [
                                    'seller' => 'store',
                                    'service' => 'support_agent',
                                    'contract' => 'description',
                                    'raw_text' => 'text_snippet',
                                    'buyer' => 'badge',
                                    'consignee' => 'local_shipping',
                                    'organisation' => 'apartment',
                                    'financial' => 'payments',
                                    'paying' => 'account_balance',
                                ];

                                foreach ($sectionList as $key => $label):

                                    $disabled = ($key === 'seller' || $key === 'service') ? 'disabled checked' : '';
                                    $cursor = $disabled ? "not-allowed" : "pointer";
                                    $bg = $disabled ? "background:#e0eaf5ff;" : "background:transparent;";
                                    $icon = $iconMap[$key] ?? 'fact_check';
                                ?>

                                    <label class="filter-item d-flex align-items-center justify-content-between px-2 py-1 rounded mb-1"
                                        style="cursor: <?= $cursor ?>; <?= $bg ?> transition: background 0.2s;"
                                        onmouseover="if(!('<?= $disabled ?>')) this.style.background='#f3f4f6';"
                                        onmouseout="if(!('<?= $disabled ?>')) this.style.background='transparent';">

                                        <div class="d-flex align-items-center gap-2">
                                            <i class="material-symbols-outlined text-primary" style="font-size: 18px;">
                                                <?= $icon ?>
                                            </i>
                                            <span class="fw-semibold" style="font-size: 12px; color: #374151;">
                                                <?= $label ?>
                                            </span>
                                        </div>

                                        <div class="form-check m-0">
                                            <input class="form-check-input data-filter"
                                                type="checkbox"
                                                value="<?= $key ?>"
                                                <?= $disabled ?>
                                                style="width: 16px; height: 16px;">
                                        </div>
                                    </label>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    </div>

                    <style>
                        .hover-lift:hover {
                            transform: translateY(-1px);
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
                        }

                        .form-check-input:checked {
                            background-color: #3b82f6;
                            border-color: #3b82f6;
                        }

                        .form-check-input:focus {
                            border-color: #3b82f6;
                            box-shadow: 0 0 0 0.15rem rgba(59, 130, 246, 0.25);
                        }

                        .dropdown-menu {
                            animation: slideDown 0.2s ease-out;
                        }

                        @keyframes slideDown {
                            from {
                                opacity: 0;
                                transform: translateY(-8px);
                            }

                            to {
                                opacity: 1;
                                transform: translateY(0);
                            }
                        }
                    </style>

                </div>

                <div class="card-body p-4">
                    <form id="uploadForm" action="/pdf/upload" method="post" enctype="multipart/form-data" class="row g-4">
                        <!-- <form class="row g-4" id="uploadForm" onsubmit="return false;"> -->
                        <div class="col-lg-8 col-md-7">
                            <div class="file-input-wrapper">
                                <input type="file" id="pdfFiles" name="pdf_files[]" accept="application/pdf" multiple>
                                <label for="pdfFiles" class="file-label" id="fileLabel">
                                    <div class="upload-icon"><i class="material-symbols-outlined text-xl">cloud_upload</i></div>
                                    <div class="upload-text">Click to upload or drag and drop</div>
                                    <div class="upload-subtext">PDF files only • Multiple files supported</div>
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-5">
                            <div class="info-box mb-2">
                                <span class="info-box-icon">✓</span> <strong>Supports multiple PDF uploads</strong><br>
                                <span class="info-box-icon">✓</span> Automatically extracts data<br>
                                <span class="info-box-icon">✓</span> Export organized Excel sheets
                            </div>

                            <button type="submit" class="submit-btn w-100 btn mb-0 bg-orange gap-2 d-flex align-items-center justify-content-center" id="submitBtn">
                                <i class="material-symbols-outlined" style=" font-size: 18px;">export_notes</i>
                                Extract Data
                            </button>
                        </div>
                    </form>
                </div>

                <hr class="horizontal dark my-2">

                <div class="card-body p-4 mt-0">
                    <h6 class="fw-bold text-purple d-flex align-items-center gap-1">
                        <i class="material-symbols-outlined text-orange" style="vertical-align: middle;">info</i>
                        How It Works
                    </h6>
                    <ol class="ps-3 mb-0" style="line-height: 1.8;">
                        <li><strong>Upload:</strong> Select one or more PDF files.</li>
                        <li><strong>Extract:</strong> The system extracts all details automatically.</li>
                        <li><strong>Review:</strong> View data in organized, readable tables.</li>
                        <li><strong>Download:</strong> Export to Excel spreadsheets instantly.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- File List -->
        <div class="col-lg-4 col-md-10 d-none" id="fileListCol">
            <div class="card border-0 shadow-sm border-radius-lg d-none" id="fileListCard" style="min-height: 245px;">
                <div class="card-header py-2" style="background: #dcdcf8ff;">
                    <h6 class="fw-bold text-info d-flex align-items-center justify-content-between mb-0">
                        <div class="d-flex align-items-center gap-1">
                            <i class="material-symbols-outlined text-info mb-0">description</i>
                            Uploaded Files
                        </div>
                        <span id="fileCount">{0}</span>
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="file-list text-center border-dashed border-1 border-info rounded-3 p-2" id="fileList">
                        <div class="file-item d-flex justify-content-center gap-2 align-items-center">
                            <span class="material-symbols-outlined text-info">attach_file</span>
                            No files uploaded.
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm mt-3 border-radius-lg d-none" id="duplicateSummaryCard" style="min-height: 245px; max-height: 242px;">
                <div class="card-header py-2" style="background: #d6a4ffff;">
                    <h6 class="fw-bold text-white d-flex align-items-center justify-content-between mb-0">
                        <div class="d-flex align-items-center gap-1">
                            <i class="material-symbols-outlined mb-0">file_copy</i>
                            Duplicate Summary
                        </div>
                        <span id="duplicateCount" class="text-white text-md fw-bold">{0}</span>
                    </h6>
                </div>
                <div class="card-body p-2" id="duplicateSummaryBody" style="min-height: 130px;">
                    <div class="file-list border-dashed border-1 border-info rounded-3 p-2" style="max-height: 200px !important;" id="duplicateList">
                        <div class="file-item d-flex justify-content-center gap-2 align-items-center">
                            <i class="material-symbols-outlined text-info">info</i>
                            No duplicate files found.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
<!-- <script src="<?= ROOT; ?>/assets/js/common.js"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
<!-- SheetJS (Excel generation) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>