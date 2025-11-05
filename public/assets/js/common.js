$(document).ready(function () {
    const $fileInput = $('#pdfFiles');
    const $fileLabel = $('#fileLabel');
    const $fileList = $('#fileList');
    const $submitBtn = $('#submitBtn');
    const $uploadForm = $('#uploadForm');
    const fileCount = $('#fileCount');

    // === Drag & Drop Events ===
    $fileLabel.on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    $fileLabel.on('dragleave', function () {
        $(this).removeClass('dragover');
    });

    $fileLabel.on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        $fileInput[0].files = e.originalEvent.dataTransfer.files;
        updateFileList();
    });

    // === File Input Change ===
    $fileInput.on('change', updateFileList);

    // === Update File List ===
    function updateFileList() {
        const files = $fileInput[0].files;
        $fileList.empty().removeClass('active');

        if (!files.length) {
            showTopAlert("Please select at least one PDF file to continue.", "warning");
            return;
        };

        // Validate PDFs only
        const invalidFiles = Array.from(files).some(f => f.type !== 'application/pdf');
        if (invalidFiles) {
            showTopAlert("Only PDF files are allowed. Please remove non-PDF files.", "error");
            $fileInput.val('');
            return;
        }

        // Create file items dynamically
        $.each(files, function (i, file) {
            const $item = $(`
                <div class="file-item d-flex justify-content-between align-items-center">
                    <span class="file-item-name">
                        <i class="material-icons" style="vertical-align: middle; font-size: 16px; margin-right: 5px;">description</i>
                        ${file.name}
                    </span>
                    <span class="file-size">${(file.size / 1024).toFixed(1)} KB</span>
                </div>
            `);
            $fileList.append($item);
        });

        // Update file count
        fileCount.text('(' + files.length + ')');
    }

    // === Form Submit ===
    $uploadForm.on('submit', function (e) {
        if (!$fileInput[0].files.length) {
            e.preventDefault();
            showTopAlert("Please select at least one PDF file to continue.", "warning");
            return;
        }

        $submitBtn.prop('disabled', true).html(`
            <i class="material-icons" style="vertical-align: middle; font-size: 18px; animation: spin 1s linear infinite;">sync</i> Processing...
        `);
    });

});
