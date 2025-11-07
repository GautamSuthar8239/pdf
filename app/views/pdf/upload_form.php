<div class="container-fluid min-vh-75 px-2 py-1">
    <div class="row g-4 align-items-start justify-content-center mt-1" id="uploadRow">
        <!-- Upload Section -->
        <div class="col-lg-8 col-md-10" id="uploadCol">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header border-radius-lg bg-orange">
                    <h5 class="mb-0 text-white d-flex align-items-center gap-2">
                        <i class="material-symbols-outlined">cloud_upload</i> Upload PDF Files
                    </h5>

                    <!-- a checkbox  dropdown for to select to show data and to have data in excel file -->

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
        <div class="col-lg-4 col-md-10">
            <div class="card border-0 shadow-sm border-radius-lg">
                <div class="card-header" style="background: #ececff;">
                    <h6 class="fw-bold text-info d-flex align-items-center justify-content-between mb-0">
                        <div class="d-flex align-items-center gap-1"> <i class="material-symbols-outlined text-info mb-0">description</i>
                            Uploaded Files
                        </div>
                        <span id="fileCount">{0}</span>
                    </h6>
                </div>
                <div class="card-body p-1">
                    <div class="file-list text-center border-dashed border-1 border-info rounded-3 p-2" id="fileList">

                        <div class="file-item d-flex justify-content-center gap-2 align-items-center">
                            <span class="material-symbols-outlined text-info">attach_file</span>
                            No files uploaded.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= ROOT; ?>/assets/js/common.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
<!-- SheetJS (Excel generation) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>