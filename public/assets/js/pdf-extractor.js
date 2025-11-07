// // pdf-extractor.js

// let allPdfData = [];

// document.addEventListener("DOMContentLoaded", () => {
//     const input = document.getElementById("pdfFiles");
//     if (!input) return;

//     input.addEventListener("change", handlePdfSelection);
// });

// async function handlePdfSelection(event) {
//     const files = [...event.target.files];

//     allPdfData = [];

//     document.getElementById("fileList").innerHTML = "Processing...";

//     for (const file of files) {
//         const text = await extractPdfText(file);
//         const extracted = extractData(text);
//         extracted.file_name = file.name;

//         allPdfData.push(extracted);
//     }

//     updateFileList(files);
//     renderResults(allPdfData);
// }

// function updateFileList(files) {
//     const list = document.getElementById("fileList");
//     const count = document.getElementById("fileCount");

//     count.textContent = `{${files.length}}`;

//     if (files.length === 0) {
//         list.innerHTML = "No files uploaded.";
//         return;
//     }

//     list.innerHTML = files
//         .map(f => `<div class="file-item">${f.name}</div>`)
//         .join("");
// }

// // ✅ Generate Excel file (3 sheets like PHP)
// function downloadExcel() {
//     const wb = XLSX.utils.book_new();

//     createSellerSheet(wb);
//     createServiceProviderSheet(wb);
//     createCombinedSheet(wb);

//     XLSX.writeFile(wb, "contract_data.xlsx");
// }

// // SHEET GENERATION (same columns as PHP)
// function createSellerSheet(wb) {
//     const rows = [
//         ["File Name", "Company Name", "GeM Seller ID", "Contact", "Email", "Address", "GSTIN", "MSME Registration"]
//     ];

//     allPdfData.forEach(d => {
//         const s = d.seller_details;
//         if (!s || Object.keys(s).length === 0) return;

//         rows.push([
//             d.file_name,
//             s.company_name,
//             s.gem_seller_id,
//             s.contact_number,
//             s.email,
//             s.address,
//             s.gstin,
//             s.msme_registration
//         ]);
//     });

//     const ws = XLSX.utils.aoa_to_sheet(rows);
//     XLSX.utils.book_append_sheet(wb, ws, "Seller Details");
// }

// function createServiceProviderSheet(wb) {
//     const rows = [
//         ["File Name", "Company Name", "GeM Seller ID", "Contact", "Email", "Address", "GSTIN", "MSME Registration"]
//     ];

//     allPdfData.forEach(d => {
//         const s = d.service_provider_details;
//         if (!s || Object.keys(s).length === 0) return;

//         rows.push([
//             d.file_name,
//             s.company_name,
//             s.gem_seller_id,
//             s.contact_number,
//             s.email,
//             s.address,
//             s.gstin,
//             s.msme_registration
//         ]);
//     });

//     const ws = XLSX.utils.aoa_to_sheet(rows);
//     XLSX.utils.book_append_sheet(wb, ws, "Service Provider Details");
// }

// function createCombinedSheet(wb) {
//     const rows = [
//         ["File Name", "Type", "Company Name", "GeM Seller ID", "Contact", "Email", "Address", "GSTIN", "MSME Registration"]
//     ];

//     allPdfData.forEach(d => {
//         const seller = d.seller_details;
//         const sp = d.service_provider_details;

//         if (seller && Object.keys(seller).length > 0) {
//             rows.push([
//                 d.file_name,
//                 "Seller",
//                 seller.company_name,
//                 seller.gem_seller_id,
//                 seller.contact_number,
//                 seller.email,
//                 seller.address,
//                 seller.gstin,
//                 seller.msme_registration
//             ]);
//         }

//         if (sp && Object.keys(sp).length > 0) {
//             rows.push([
//                 d.file_name,
//                 "Service Provider",
//                 sp.company_name,
//                 sp.gem_seller_id,
//                 sp.contact_number,
//                 sp.email,
//                 sp.address,
//                 sp.gstin,
//                 sp.msme_registration
//             ]);
//         }
//     });

//     const ws = XLSX.utils.aoa_to_sheet(rows);
//     XLSX.utils.book_append_sheet(wb, ws, "Combined Details");
// }


async function processPdfFilesJQ(files) {
    let allData = []; // ✅ same as PHP $allData
    let file_names = [];

    for (let i = 0; i < files.length; i++) {
        let file = files[i];

        // ✅ Extract full text via PDF.js
        let text = await extractPdfText(file);

        // ✅ Parse data using regex (your JS version of PdfModel)
        // let data = extractData(text);

        // ✅ Add filename (same as PHP)
        // data.file_name = file.name;

        // // ✅ Add raw text
        // data.raw_text = text;
        file_names.push(file.name);

        // // ✅ Push final array (PHP → JS identical)
        allData.push({ file_name: file.name, raw_text: text });
    }

    allData.file_names = file_names;

    return allData;
}
