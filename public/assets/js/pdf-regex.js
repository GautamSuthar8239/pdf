// ✅ pdf-regex.js (FIXED - accurate field extraction)

function extractData(text) {
    return {
        // seller_details: extractSellerDetails(text),
        // service_provider_details: extractServiceProviderDetails(text),
        raw_text: text
    };
}

function extractSellerDetails(text) {
    const details = {};

    const sellerSection = text.match(/Seller Details(.*?)(?=Service Provider|$)/is);
    if (!sellerSection) return details;

    const seller = sellerSection[1];

    details.company_name = match(seller, /Company Name\s*:\s*(.*?)(?=\s*(GeM Seller ID|Contact|Email|Address|GSTIN|MSME|$))/is);
    details.gem_seller_id = match(seller, /GeM Seller ID\s*:\s*([A-Z0-9-]+)/i);

    details.contact_number = match(seller,
        /Contact No\.?\s*:\s*([\d+\- ]{5,20})(?=\s*(Email|Address|GSTIN|MSME|$))/i
    );

    details.email = match(seller,
        /Email ID\s*:\s*([a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})(?=\s*(Address|GSTIN|MSME|$))/i
    );

    details.address = match(seller,
        /Address\s*:\s*(.*?)(?=\s*(MSME|GSTIN|Email|Contact|$))/is
    );

    details.gstin = match(seller, /GSTIN\s*:\s*([A-Z0-9]{15})/i);

    details.msme_registration = match(seller,
        /MSME Registration(?: number)?\s*:\s*(.*?)(?=\s*(GSTIN|Email|Contact|$))/is
    );

    return details;
}

function extractServiceProviderDetails(text) {
    const details = {};

    const section = text.match(/Service Provider(.*?)(?=Buyer Details|Financial Approval Detail|$)/is);
    if (!section) return details;

    const sp = section[1];

    details.company_name = match(sp, /Company Name\s*:\s*(.*?)(?=\s*(GeM Seller ID|Contact|Email|Address|GSTIN|MSME|$))/is);
    details.gem_seller_id = match(sp, /GeM Seller ID\s*:\s*([A-Z0-9-]+)/i);

    details.contact_number = match(sp,
        /Contact No\.?\s*:\s*([\d+\- ]{5,20})(?=\s*(Email|Address|GSTIN|MSME|$))/i
    );

    details.email = match(sp,
        /Email ID\s*:\s*([a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})(?=\s*(Address|GSTIN|MSME|$))/i
    );

    details.address = match(sp,
        /Address\s*:\s*(.*?)(?=\s*(MSME|GSTIN|Email|Contact|$))/is
    );

    details.gstin = match(sp, /GSTIN\s*:\s*([A-Z0-9]{15})/i);

    details.msme_registration = match(sp,
        /MSME Registration(?: number)?\s*:\s*(.*?)(?=\s*(GSTIN|Email|Contact|$))/is
    );

    return details;
}

function match(text, regex) {
    const m = text.match(regex);
    return m ? m[1].trim() : "N/A";
}
