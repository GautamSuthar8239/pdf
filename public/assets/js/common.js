const CommonCheckboxUtils = {
    getSelected: ($checkboxes, useDataAttr = false) =>
        $checkboxes.filter(":checked").map((_, el) =>
            useDataAttr ? $(el).data("id") : $(el).val()
        ).get(),

    syncSelectAll: ($checkboxes, $selectAll) => {
        $selectAll.prop("checked", $checkboxes.length === $checkboxes.filter(":checked").length);
    },

    updateSelectionUI: ($container, $countEl, selectedCount) => {
        $countEl.text(selectedCount);
        $container
            .toggleClass("d-none", selectedCount === 0)
            .toggleClass("d-flex justify-content-between", selectedCount > 0);
    },

    reloadPage: (timeout = 1500, redirectUrl = null) => {
        setTimeout(() => {
            if (redirectUrl) location.href = redirectUrl;
            else location.reload();
        }, timeout);
    },

    redirectTo: (url, timeout = 1000) => {
        setTimeout(() => {
            location.href = url;
        }, timeout);
    },

    // robust init
    init: function (selectAllSelector, itemSelector, onChangeCallback = null) {
        const $selectAll = $(selectAllSelector);
        if (!$selectAll.length) return;

        $selectAll.on("change", function () {
            const checked = this.checked;
            const $checkboxes = $(itemSelector);
            $checkboxes.prop("checked", checked).trigger('change.selectall');
            if (onChangeCallback) onChangeCallback($checkboxes);
        });

        $(document).on("change", itemSelector, function (e) {
            const $checkboxes = $(itemSelector);
            $selectAll.prop("checked", $checkboxes.length > 0 && $checkboxes.filter(":checked").length === $checkboxes.length);
            if (onChangeCallback) onChangeCallback($checkboxes);
        });

        // initial sync
        const $initial = $(itemSelector);
        $selectAll.prop("checked", $initial.length > 0 && $initial.filter(":checked").length === $initial.length);
        if (onChangeCallback) onChangeCallback($initial);
    }
};

$(document).ready(function () {

    $(document).on('hide.bs.modal', '.modal', function () {
        document.activeElement?.blur();
    });
    // ============================================================
    // 🧩 CUSTOM SELECT DROPDOWN
    // ============================================================
    $(document).on("click", ".custom-select__trigger", function (e) {
        e.stopPropagation();
        const $select = $(this).closest(".custom-select");
        $(".custom-select.open").not($select).removeClass("open");
        $select.toggleClass("open");
    });

    $(document).on("click", ".custom-select__option", function (e) {
        e.stopPropagation();
        const $option = $(this);
        const $select = $option.closest(".custom-select");
        const text = $.trim($option.text());
        const value = $option.data("value");

        $select.find(".custom-select__option").removeClass("selected");
        $option.addClass("selected");
        $select.find(".custom-select__selected").text(text);
        $select.removeClass("open");

        $select.trigger("customSelect:change", { value, text, select: $select });
    });

    $(document).on("click", function () {
        $(".custom-select.open").removeClass("open");
    });

    $(document).on("keydown", function (e) {
        if (e.key === "Escape") $(".custom-select.open").removeClass("open");
    });

    const $fileInput = $('#pdfFiles');
    const $fileLabel = $('#fileLabel');
    const $fileList = $('#fileList');
    const $fileListCard = $('#fileListCard');
    const $submitBtn = $('#submitBtn');
    const $uploadForm = $('#uploadForm');
    const fileCount = $('#fileCount');

    let extractedData = null; // Store extracted data

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
        $("#fileListCol").removeClass("d-none");
        $("#fileListCard").removeClass("d-none");
        const files = $fileInput[0].files;
        $fileList.empty().removeClass('active');

        if (!files.length) {
            showTopAlert("Please select at least one PDF file to continue.", "warning");
            $fileList.append('<div class="file-item">No files uploaded.</div>');
            $fileInput.val('');
            return;
        }

        // Validate PDFs only
        const invalidFiles = Array.from(files).some(f => f.type !== 'application/pdf');
        if (invalidFiles) {
            showTopAlert("Only PDF files are allowed. Please remove non-PDF files.", "error");
            $fileList.append('<div class="file-item">No files uploaded.</div>');
            $fileInput.val('');
            return;
        }

        // Create file items dynamically
        $.each(files, function (i, file) {
            const $item = $(`
                <div class="file-item d-flex justify-content-between align-items-center">
                    <span class="file-item-name d-flex align-items-center ">
                        <i class="material-symbols-outlined" style="vertical-align: middle; font-size: 20px; margin-right: 5px;">description</i>
                        ${file.name}
                    </span>
                    <span class="file-size">${(file.size / 1024).toFixed(1)} KB</span>
                </div>
            `);
            $fileList.append($item);
        });

        fileCount.text(`(${files.length} files — ${formatBytes(totalSize(files))})`);

        // Extract PDF data in background
        extractPdfData(files);
    }
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + " B";
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
        return (bytes / (1024 * 1024)).toFixed(1) + " MB";
    }

    function renderDuplicateSummary(summary) {
        const $card = $("#duplicateSummaryCard");
        const $duplicateList = $("#duplicateSummaryBody #duplicateList");
        const $count = $("#duplicateCount");

        $duplicateList.empty();

        if (!summary) {
            console.error("summary is undefined:", summary);
            return;
        }

        // ✅ summary keys must match backend
        const nameOnly = summary.name_only || [];
        const contentOnly = summary.content_only || [];
        const fullDup = summary.full_duplicates || [];

        const totalDuplicates =
            nameOnly.length +
            contentOnly.length +
            fullDup.length;

        $count.text(`(${totalDuplicates}) files.`);

        if (totalDuplicates === 0) return;

        let sections = [];

        const sectionCard = (icon, title, tooltip, list) => `
        <div class="file-item d-flex flex-column">
            <h6 class="d-flex align-items-center justify-content-between w-100 mb-0">
                <span class="d-flex align-items-center gap-2" title="${tooltip}">
                    <i class="material-symbols-rounded">${icon}</i> ${title}
                </span>
                <span class="text-sm">${list.length}</span>
            </h6>

            ${list.map((f, i) => `
                <span class="ms-3 d-flex align-items-center justify-content-between" style="font-size: 12px;">
                    <span>${i + 1}. ${f.duplicate_of}</span>
                    <span>(${formatSize(f.size_bytes)})</span>
                </span>
            `).join("")}
        </div>
    `;

        if (nameOnly.length > 0) {
            sections.push(sectionCard(
                "badge",
                "By Name",
                "Duplicate By Name Only",
                nameOnly
            ));
        }

        if (contentOnly.length > 0) {
            sections.push(sectionCard(
                "content_copy",
                "By Content",
                "Duplicate By Content Only",
                contentOnly
            ));
        }

        if (fullDup.length > 0) {
            sections.push(sectionCard(
                "library_books",
                "Duplicate File",
                "Fully Duplicate (Name + Content)",
                fullDup
            ));
        }

        $duplicateList.html(sections.join(""));

        $card.removeClass("d-none");
    }




    function totalSize(files) {
        let size = 0;
        for (let f of files) size += f.size;
        return size;
    }

    // === Extract PDF Data (async, non-blocking) ===
    async function extractPdfData(files) {
        $submitBtn.prop('disabled', true).html(`
            <i class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; animation: spin 1s linear infinite;">sync</i> Extracting...
        `);

        try {
            extractedData = await processPdfFilesJQ(files);

            // ✅ Render duplicate summary

            renderDuplicateSummary(extractedData.summary);

            $submitBtn.prop('disabled', false).html(`
                <i class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px;">send</i>Fetch & View Data
                `);
            showTopAlert("PDF data extracted successfully.", "success");
        } catch (error) {
            console.error('PDF extraction failed:', error);
            showTopAlert("Failed to extract PDF data. Please try again.", "error");
            $submitBtn.prop('disabled', false).html(`
                <i class="material-symbols-outlined " style="vertical-align: middle; font-size: 18px;">send</i>Fetch & View Data
                `);
        }
    }

    CommonCheckboxUtils.init('#selectAllData', '.data-filter:not(:disabled)', function ($checkboxes) {

        const selectedIds = CommonCheckboxUtils.getSelected($checkboxes);
        console.log("📧 Selected campaigns:", selectedIds);
    });

    // === Form Submit (on button click) ===
    $uploadForm.on('submit', function (e) {
        e.preventDefault();

        if (!$fileInput[0].files.length) {
            showTopAlert("Please select at least one PDF file to continue.", "warning");
            $fileList.append('<div class="file-item">No files uploaded.</div>');
            $fileInput.val('');
            return;
        }

        if (!extractedData) {
            showTopAlert("PDF extraction is still in progress. Please wait.", "warning");
            return;
        }

        $submitBtn.prop('disabled', true).html(`
            <i class="material-icons" style="vertical-align: middle; font-size: 18px; animation: spin 1s linear infinite;">sync</i> Processing...
        `);

        // Create hidden form to POST data
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/pdf/upload';

        // Add selected filters
        const selectedFilters = CommonCheckboxUtils.getSelected($('.data-filter:not(:disabled)'));
        const filterInput = document.createElement('input');
        filterInput.type = 'hidden';
        filterInput.name = 'filters';
        filterInput.value = JSON.stringify(selectedFilters);
        form.appendChild(filterInput);


        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content;
        form.appendChild(csrfInput);

        // Add extracted data
        const dataInput = document.createElement('input');
        dataInput.type = 'hidden';
        dataInput.name = 'result';
        dataInput.value = JSON.stringify(extractedData);
        form.appendChild(dataInput);

        document.body.appendChild(form);
        form.submit();
        // console.log("extractedData: ", extractedData);
    });


    // ✅ Free View
    $("#freeViewBtn").on("click", function () {
        $(this).addClass("active");
        $("#scrollViewBtn").removeClass("active");

        $("#detailsFreeView").removeClass("d-none");
        $("#detailsScrollView").addClass("d-none");
    });

    // ✅ Scroll View
    $("#scrollViewBtn").on("click", function () {
        $(this).addClass("active");
        $("#freeViewBtn").removeClass("active");

        $("#detailsFreeView").addClass("d-none");
        $("#detailsScrollView").removeClass("d-none");
    });


    function updateToggleVisibility() {
        let activeTab = $("#resultTabs .nav-link.active").attr("data-bs-target");

        // Hide all badges first
        $("#duplicatesBadge, #sellerBadge, #serviceProviderBadge, #detailsToggleBtns, #combinedBadge").addClass("d-none");

        switch (activeTab) {
            case "#duplicates-tab":
                $("#duplicatesBadge").removeClass("d-none");
                break;

            case "#seller":
                $("#sellerBadge").removeClass("d-none");
                break;
            case "#combined":
                $("#combinedBadge").removeClass("d-none");
                break;

            case "#service-provider":
                $("#serviceProviderBadge").removeClass("d-none");
                break;

            case "#details":
                $("#combinedBadge").removeClass("d-none");
                $("#detailsToggleBtns").removeClass("d-none");
                break;
        }
    }


    // Fire once on load
    updateToggleVisibility();

    // Fire whenever a tab changes
    $('#resultTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        updateToggleVisibility();
    });
});







async function processPdfFilesJQ(files) {
    let allData = [];        // ✅ unique only
    let duplicates = [];     // ✅ all duplicates (full objects)

    let nameMap = {};
    let contentMap = {};

    let summaryNameOnly = [];
    let summaryContentOnly = [];
    let summaryFullDuplicates = [];

    let totalFileSize = 0;

    for (let file of files) {
        let baseName = normalizeFileName(file.name);
        totalFileSize += file.size;

        const text = await extractPdfText(file);
        const contentKey = text.replace(/\s+/g, "").toLowerCase();

        let nameMatch = nameMap[baseName];
        let contentMatch = contentMap[contentKey];

        let duplicate_reason = null;
        let duplicate_of = null;
        let isDuplicate = false;

        // ✅ Full object for both unique & duplicate
        const fileObj = {
            file_name: file.name,
            base_name: baseName,
            raw_text: text,
            size_bytes: file.size,
            duplicate_reason: null,
            duplicate_of: null
        };

        // ✅ FULL DUPLICATE
        if (nameMatch && contentMatch && nameMatch === contentMatch) {
            duplicate_reason = "full_duplicate";
            duplicate_of = nameMatch;
            isDuplicate = true;

            fileObj.duplicate_reason = duplicate_reason;
            fileObj.duplicate_of = duplicate_of;

            duplicates.push(fileObj);
            summaryFullDuplicates.push(fileObj);

            // ✅ NAME DUPLICATE ONLY
        } else if (nameMatch && !contentMatch) {
            duplicate_reason = "duplicate_name_only";
            duplicate_of = nameMatch;
            isDuplicate = true;

            fileObj.duplicate_reason = duplicate_reason;
            fileObj.duplicate_of = duplicate_of;

            duplicates.push(fileObj);
            summaryNameOnly.push(fileObj);

            // ✅ CONTENT DUPLICATE ONLY
        } else if (!nameMatch && contentMatch) {
            duplicate_reason = "duplicate_content_only";
            duplicate_of = contentMatch;
            isDuplicate = true;

            fileObj.duplicate_reason = duplicate_reason;
            fileObj.duplicate_of = duplicate_of;

            duplicates.push(fileObj);
            summaryContentOnly.push(fileObj);

            // ✅ UNIQUE
        } else {
            nameMap[baseName] = file.name;
            contentMap[contentKey] = file.name;

            allData.push(fileObj);
        }
    }

    return {
        allData,           // ✅ unique files only
        duplicates,        // ✅ all duplicates with full information
        total_size_bytes: totalFileSize,
        total_size_readable: formatBytes(totalFileSize),

        // ✅ duplicate categories if you still need them in UI
        summary: {
            name_only: summaryNameOnly,
            content_only: summaryContentOnly,
            full_duplicates: summaryFullDuplicates
        }
    };
}



function formatBytes(bytes) {
    const units = ['Bytes', 'KB', 'MB', 'GB'];
    let i = 0;
    while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
    }
    return bytes.toFixed(2) + ' ' + units[i];
}


function normalizeFileName(name) {
    let ext = name.substring(name.lastIndexOf("."));
    let base = name.substring(0, name.lastIndexOf("."));

    base = base.replace(/\(\d+\)/gi, "");           // (1), (2)
    base = base.replace(/[-_\s]*copy\s*\d*/gi, ""); // copy, -copy, copy 2
    base = base.replace(/\(copy\)/gi, "");          // (copy)

    base = base.replace(/[-\s]+$/g, "").trim();

    return base + ext;
}



// pdf-reader.js
async function extractPdfText(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = async function (e) {
            try {
                const typedArray = new Uint8Array(e.target.result);
                const pdf = await pdfjsLib.getDocument(typedArray).promise;

                let fullText = "";

                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const content = await page.getTextContent();

                    const strings = content.items.map(item => item.str).join(" ");
                    fullText += strings + "\n";
                }

                resolve(fullText);
            } catch (error) {
                reject(error);
            }
        };

        reader.onerror = reject;

        reader.readAsArrayBuffer(file);
    });
}
