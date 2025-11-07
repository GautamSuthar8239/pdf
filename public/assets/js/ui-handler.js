// ui-handler.js

function renderResults(allData) {
    const container = document.getElementById("resultsContainer");
    if (!container) return;

    container.innerHTML = `
        <h3 class="mb-3">Extraction Results</h3>
        <button class="btn btn-success mb-3" onclick="downloadExcel()">
            Download Excel
        </button>

        ${renderCombined(allData)}
        ${renderTable("Service Provider Details", allData, "service_provider_details")}
        ${renderTable("Seller Details", allData, "seller_details")}
        `;
        // ${renderAllDetails(allData)}
}

function renderCombined(data) {
    let rows = data.map(d => `
        <tr>
            <td>${d.file_name}</td>
            <td>${d.seller_details.company_name || "---"}</td>
            <td>${d.seller_details.gem_seller_id || "---"}</td>
            <td>${d.seller_details.contact_number || "---"}</td>
        </tr>
    `);

    return `
        <h5>Combined View</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Company</th>
                    <th>GeM Seller ID</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>${rows.join("")}</tbody>
        </table>
    `;
}

function renderTable(title, data, key) {
    let rows = "";

    data.forEach(d => {
        const block = d[key];
        if (!block || Object.keys(block).length === 0) return;

        rows += `
        <tr>
            <td>${block.company_name}</td>
            <td>${block.gem_seller_id}</td>
            <td>${block.contact_number}</td>
            <td>${block.email}</td>
            <td>${block.address}</td>
            <td>${block.gstin}</td>
            <td>${block.msme_registration}</td>
        </tr>`;
    });

    return `
        <h5>${title}</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>GeM ID</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>GSTIN</th>
                    <th>MSME</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>`;
}

function renderAllDetails(data) {
    return `
        <h5>All Details</h5>
        <pre>${JSON.stringify(data, null, 2)}</pre>
    `;
}
