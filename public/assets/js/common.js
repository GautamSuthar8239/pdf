// $(document).ready(function () {
//     const $fileInput = $('#pdfFiles');
//     const $fileLabel = $('#fileLabel');
//     const $fileList = $('#fileList');
//     const $submitBtn = $('#submitBtn');
//     const $uploadForm = $('#uploadForm');
//     const fileCount = $('#fileCount');

//     // === Drag & Drop Events ===
//     $fileLabel.on('dragover', function (e) {
//         e.preventDefault();
//         $(this).addClass('dragover');
//     });

//     $fileLabel.on('dragleave', function () {
//         $(this).removeClass('dragover');
//     });

//     $fileLabel.on('drop', function (e) {
//         e.preventDefault();
//         $(this).removeClass('dragover');
//         $fileInput[0].files = e.originalEvent.dataTransfer.files;
//         updateFileList();
//     });

//     // === File Input Change ===
//     $fileInput.on('change', updateFileList);

//     // === Update File List ===
//     function updateFileList() {
//         const files = $fileInput[0].files;
//         $fileList.empty().removeClass('active');

//         if (!files.length) {
//             showTopAlert("Please select at least one PDF file to continue.", "warning");
//             return;
//         };

//         // Validate PDFs only
//         const invalidFiles = Array.from(files).some(f => f.type !== 'application/pdf');
//         if (invalidFiles) {
//             showTopAlert("Only PDF files are allowed. Please remove non-PDF files.", "error");
//             $fileInput.val('');
//             return;
//         }

//         // Create file items dynamically
//         $.each(files, function (i, file) {
//             const $item = $(`
//                 <div class="file-item d-flex justify-content-between align-items-center">
//                     <span class="file-item-name">
//                         <i class="material-icons" style="vertical-align: middle; font-size: 16px; margin-right: 5px;">description</i>
//                         ${file.name}
//                     </span>
//                     <span class="file-size">${(file.size / 1024).toFixed(1)} KB</span>
//                 </div>
//             `);
//             $fileList.append($item);
//         });

//         // Update file count
//         fileCount.text('(' + files.length + ')');
//     }

//     // === Form Submit ===
//     $uploadForm.on('submit', function (e) {
//         if (!$fileInput[0].files.length) {
//             e.preventDefault();
//             showTopAlert("Please select at least one PDF file to continue.", "warning");
//             return;
//         }

//         $submitBtn.prop('disabled', true).html(`
//             <i class="material-icons" style="vertical-align: middle; font-size: 18px; animation: spin 1s linear infinite;">sync</i> Processing...
//         `);
//     });

//     $("#pdfFiles").on("change", async function () {
//         let files = this.files;
//         if (!files.length) return;

//         let allData = await processPdfFilesJQ(files);
//         console.log("Extracted data:", allData);
//         // Create hidden form to POST data
//         const form = document.createElement('form');
//         form.method = 'POST';
//         form.action = '/pdf/upload';

//         // Add CSRF token if needed
//         const csrfInput = document.createElement('input');
//         csrfInput.type = 'hidden';
//         csrfInput.name = '_token';
//         csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content;
//         form.appendChild(csrfInput);

//         // Add data
//         const dataInput = document.createElement('input');
//         dataInput.type = 'hidden';
//         dataInput.name = 'allData';
//         dataInput.value = JSON.stringify(allData);
//         form.appendChild(dataInput);

//         document.body.appendChild(form);
//         form.submit(); // Submits and redirects automatically
//     });
// });


// async function processPdfFilesJQ(files) {
//     let data = []; // same as PHP $allData
//     let file_names = [];

//     for (let i = 0; i < files.length; i++) {
//         let file = files[i];

//         // Extract full text via PDF.js
//         let text = await extractPdfText(file);

//         file_names.push(file.name);

//         // // Push final array (PHP → JS identical)
//         data.push({ file_name: file.name, raw_text: text });
//     }

//     result = {
//         allData: data,
//         file_names: file_names
//     };

//     return {
//         result: result
//     }
// }

$(document).ready(function () {

    const msgs = [
        "Automation saves hours — keep going!",
        "Your work builds real impact.",
        "Small progress daily = big results.",
        "Smart tools create smart outcomes."
    ];

    let currentMsgIndex = 0;
    let charIndex = 0;
    let isTyping = true;
    const bannerText = document.getElementById('bannerText');

    function typeCharacter() {
        if (!isTyping) return;

        const currentMsg = msgs[currentMsgIndex];

        if (charIndex <= currentMsg.length) {
            bannerText.textContent = currentMsg.substring(0, charIndex);
            bannerText.className = 'typing cursor';
            charIndex++;
            setTimeout(typeCharacter, 60);
        } else {
            // Typing complete - remove cursor
            bannerText.classList.remove('cursor');

            // Wait a moment, then slide left
            setTimeout(() => {
                slideLeftAndNext();
            }, 1500);
        }
    }

    function slideLeftAndNext() {
        isTyping = false;
        const currentMsg = msgs[currentMsgIndex];
        bannerText.textContent = currentMsg;
        bannerText.classList.remove('cursor');
        bannerText.classList.add('slide-left');

        // After slide animation completes, bring same text from right
        setTimeout(() => {
            bannerText.className = 'slide-from-right';

            // After slide-in completes, move to next message and start typing
            setTimeout(() => {
                currentMsgIndex = (currentMsgIndex + 1) % msgs.length;
                charIndex = 0;
                bannerText.textContent = '';
                bannerText.className = '';
                isTyping = true;
                typeCharacter();
            }, 4000);

        }, 3500);
    }

    // Start the animation
    typeCharacter();

    const $fileInput = $('#pdfFiles');
    const $fileLabel = $('#fileLabel');
    const $fileList = $('#fileList');
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

        // Update file count
        // fileCount.text('(' + files.length + ')');
        fileCount.text(`(${files.length} files — ${formatBytes(totalSize(files))})`);

        // Extract PDF data in background
        extractPdfData(files);
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
            $submitBtn.prop('disabled', false).html(`
                <i class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px;">send</i>Fetch Data
                `);
        } catch (error) {
            console.error('PDF extraction failed:', error);
            showTopAlert("Failed to extract PDF data. Please try again.", "error");
            $submitBtn.prop('disabled', false).html(`
                <i class="material-symbols-outlined " style="vertical-align: middle; font-size: 18px;">send</i>Fetch Data
                `);
        }
    }

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
        console.log("extractedData: ", extractedData);
    });
});

// async function processPdfFilesJQ(files) {
//     let data = [];
//     let file_names = [];

//     for (let i = 0; i < files.length; i++) {
//         let file = files[i];
//         let text = await extractPdfText(file);
//         file_names.push(file.name);
//         data.push({ file_name: file.name, raw_text: text });
//     }

//     return {
//         allData: data,
//         file_names: file_names
//     };
// }
async function processPdfFilesJQ(files) {
    let data = [];
    let file_names = [];
    let seenFiles = {};
    let seenContent = {};
    let totalFileSize = 0;

    for (let file of files) {
        let baseName = normalizeFileName(file.name);

        // Always add original size
        totalFileSize += file.size;

        if (seenFiles[baseName]) {
            console.warn(`Skipping duplicate file: ${file.name}`);
            continue;
        }

        seenFiles[baseName] = true;

        const text = await extractPdfText(file);

        const contentKey = text.replace(/\s+/g, "").toLowerCase();

        if (seenContent[contentKey]) {
            console.warn(`Skipping duplicate content from: ${file.name}`);
            continue;
        }

        seenContent[contentKey] = true;

        data.push({
            file_name: file.name,
            base_name: baseName,
            raw_text: text,
            size_bytes: file.size
        });

        file_names.push(file.name);
    }

    return {
        allData: data,
        file_names,
        total_size_bytes: totalFileSize,
        total_size_readable: formatBytes(totalFileSize)
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
