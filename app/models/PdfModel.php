<?php

class PdfModel
{
    /**
     * Maps UI filter keys → extracted data array keys.
     * Example: 'contract' → 'contract_details'
     */
    public function getSectionToFieldMap()
    {
        return [
            'seller'        => 'seller_details',
            'service'       => 'service_provider_details',
            'contract'      => 'contract_details',
            'buyer'         => 'buyer_details',
            'consignee'     => 'consignee_details',
            'organisation'  => 'organisation_details',
            'financial'     => 'financial_approval',
            'paying'        => 'paying_authority_details',
            'product'       => 'product_details',
        ];
    }

    /**
     * Maps extracted data keys → filter keys (for view rendering).
     * Example: 'contract_details' → 'contract'
     */
    public function filterKeyMap()
    {
        return [
            'contract_details'         => 'contract',
            'buyer_details'            => 'buyer',
            'consignee_details'        => 'consignee',
            'organisation_details'     => 'organisation',
            'financial_approval'       => 'financial',
            'paying_authority_details' => 'paying',
            'product_details'          => 'product',
        ];
    }

    /**
     * Labels used for rendering tabs in Details View.
     */
    public function getDetailSections()
    {
        return [
            'contract_details'         => 'Contract Details',
            'buyer_details'            => 'Buyer Details',
            'consignee_details'        => 'Consignee Details',
            'organisation_details'     => 'Organisation Details',
            'financial_approval'       => 'Financial Approval',
            'paying_authority_details' => 'Paying Authority Details',
            'product_details'          => 'Product Details',
        ];
    }

    /**
     * Sections available in the filter dropdown (Upload Page).
     */
    public function getAvailableSections()
    {
        return [
            'seller'        => 'Seller Details',
            'service'       => 'Service Provider Details',
            'contract'      => 'Contract Details',
            'buyer'         => 'Buyer Details',
            'consignee'     => 'Consignee Details',
            'organisation'  => 'Organisation Details',
            'financial'     => 'Financial Approval',
            'paying'        => 'Paying Authority',
            'product'       => 'Product Details',
        ];
    }

    /**
     * Icons for each filter/tab key.
     */
    public function getIconMap()
    {
        return [
            'seller'        => 'store',
            'service'       => 'support_agent',
            'contract'      => 'description',
            'buyer'         => 'badge',
            'consignee'     => 'local_shipping',
            'organisation'  => 'apartment',
            'financial'     => 'payments',
            'product'       => 'inventory',
            'paying'        => 'account_balance',
            'raw_text'      => 'text_snippet',
        ];
    }

    /**
     * Main extraction method - extracts all data from PDF text
     */
    public function extractData($text)
    {
        return [
            // 'file_type' => $this->detectFileType($text),
            'paying_authority_details' => $this->extractPayingAuthority($text),
            'buyer_details' => $this->extractBuyerDetails($text),
            'consignee_details' => $this->extractConsigneeDetails($text),
            'organisation_details' => $this->extractOrganisationDetails($text),
            'financial_approval' => $this->extractFinancialApproval($text),
            'seller_details' => $this->extractSellerDetails($text),
            'contract_details' => $this->extractContractDetails($text),
            'service_provider_details' => $this->extractServiceProviderDetails($text),
            'raw_text' => $text,
            'product_details' => $this->extractProductDetails($text),

        ];
    }

    /**
     * Detect document type based on sections present
     */
    private function detectFileType($text)
    {
        $hasSellerBlock = preg_match('/Seller Details.*?Company Name/is', $text);
        $hasServiceBlock = preg_match('/Service Provider.*?Company Name/is', $text);

        if ($hasSellerBlock && $hasServiceBlock) {
            return 'Seller & Service Provider';
        } elseif ($hasSellerBlock) {
            return 'Seller';
        } elseif ($hasServiceBlock) {
            return 'Service Provider';
        }

        return 'Unknown';
    }

    /**
     * Extract seller details section
     */
    private function extractSellerDetails($text)
    {
        $details = [];

        if (!preg_match('/Seller Details(.*?)(?=Service Provider|Product Details|Buyer Details|Consignee|$)/is', $text, $section)) {
            return $details;
        }

        $sellerText = $this->cleanBlock($section[1]);

        // GeM Seller ID (bilingual support)
        if (preg_match('/GeM Seller ID\s*:\s*([A-Z0-9]+)/i', $sellerText, $match)) {
            $details['gem_seller_id'] = trim($match[1]);
        }

        // Company Name (handles M/S, special characters, Hindi)
        if (preg_match('/Company Name\s*[:|]*\s*([^\n\r|]+?)(?=\s*(?:संपक|Contact|Address|GSTIN|$))/isu', $sellerText, $match)) {
            $details['company_name'] = $this->cleanText($match[1]);
        }

        // Contact Number
        if (preg_match('/Contact No\.?\s*:\s*([0-9+\-]+)/', $sellerText, $match)) {
            $details['contact_number'] = preg_replace('/\s+/', '', trim($match[1]));
        }

        // Email
        if (preg_match('/(?:Email ID|ईमेल)\s*[:|]*\s*([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/iu', $sellerText, $match)) {
            $details['email'] = strtolower(trim($match[1]));
        }

        // Address
        if (preg_match('/Address\s*:\s*(.*?)(?=एमएसएमई|MSME|GSTIN|$)/isu', $sellerText, $match)) {

            $details['address'] = $this->cleanText($match[1]);
        }

        // GSTIN
        if (preg_match(
            '/GSTIN\s*:\s*(.*?)(?=\s*(\*|!|#|ज|नाम|GST|एमएसई|MSME|MSE|खरीदार|Buyer|Delivery|Product|उ{पाद|$))/is',
            $sellerText,
            $match
        )) {
            $details['gstin'] = trim($match[1]);
        }

        // MSME Registration
        if (preg_match('/MSME Registration(?:\s+number)?\s*[:|]*\s*([A-Z0-9-]+)/i', $sellerText, $match)) {
            $details['msme_registration'] = trim($match[1]);
        }

        return $details;
    }

    /**
     * Extract service provider details section
     */
    private function extractServiceProviderDetails($text)
    {
        $details = [];

        if (!preg_match('/Service Provider(.*?)(?=Buyer Details|Financial Approval Detail|$)/is', $text, $section)) {
            return $details;
        }

        $spText = $this->cleanBlock($section[1]);

        // GeM Seller ID
        if (preg_match('/GeM Seller ID\s*:\s*([A-Z0-9]+)/i', $spText, $m)) {
            $details['gem_seller_id'] = trim($m[1]);
        }

        // Company Name
        if (preg_match('/Company Name\s*:\s*([A-Za-z0-9 .,&()-]+)/', $spText, $match)) {
            $details['company_name'] = trim($match[1]);
        }

        // Contact Number
        if (preg_match('/Contact No\.?\s*:\s*([0-9+\-]+)/', $spText, $match)) {
            $details['contact_number'] = trim($match[1]);
        }

        // Email
        if (preg_match('/Email ID\s*:\s*([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $spText, $match)) {
            $details['email'] = trim($match[1]);
        }

        // Address
        if (preg_match('/Address\s*:\s*(.*?)(?=एमएसएमई|MSME|GSTIN|$)/is', $spText, $match)) {
            $details['address'] = $this->cleanText($match[1]);
        }

        // GSTIN
        if (preg_match('/GSTIN\s*:\s*(.*?)(?=एमएसई|MSME|MSE|$)/is', $spText, $match)) {
            $details['gstin'] = trim($match[1]);
        }

        // MSME Registration
        if (preg_match('/MSME Registration(?: number)?\s*:\s*([A-Z0-9-]+)/i', $spText, $match)) {
            $details['msme_registration'] = trim($match[1]);
        }

        return $details;
    }

    /**
     * Extract organisation details section
     */
    private function extractOrganisationDetails($text)
    {
        if (!preg_match('/Organisation Details\s*(.*?)(?=Buyer Details|खरीदार|$)/is', $text, $match)) {
            return [];
        }

        $block = $this->cleanText($match[1]);
        $details = [];

        if (preg_match('/Type\s*[:|]*\s*([A-Za-z\s]+?)(?=Ministry|$)/i', $block, $m)) {
            $details['type'] = trim($m[1]);
        }

        if (preg_match('/Ministry\s*[:|]*\s*(.*?)(?=Department|वभाग|"वभाग|!वभाग|#वभाग|$)/iu', $block, $m)) {
            $details['ministry'] = trim($m[1]);
        }

        if (preg_match('/Department\s*[:|]*\s*(.*?)(?=Organisation|संगठन|$)/iu', $block, $m)) {
            $details['department'] = trim($m[1]);
        }

        if (preg_match('/Organisation Name\s*[:|]*\s*(.*?)(?=Office Zone|काया|$)/iu', $block, $m)) {
            $details['organisation_name'] = trim($m[1]);
        }

        if (preg_match('/Office Zone\s*[:|]*\s*([^\n]+)/i', $block, $m)) {
            $details['office_zone'] = trim($m[1]);
        }

        return $details;
    }

    /**
     * Extract buyer details section
     */
    private function extractBuyerDetails($text)
    {
        if (!preg_match('/Buyer Details\s*(.*?)(?=Financial Approval|वित्तीय|Paying Authority|Seller Details|विक्रेता|$)/is', $text, $match)) {
            return [];
        }

        $block = $this->cleanText($match[1]);
        $details = [];

        if (preg_match('/Designation\s*[:|]*\s*(.*?)(?=संपक|Contact|Email|$)/iu', $block, $m)) {
            $details['designation'] = trim($m[1]);
        }

        if (preg_match('/Contact No\.?\s*[:|]*\s*([0-9+\-\s()]+)/i', $block, $m)) {
            $details['contact'] = preg_replace('/\s+/', '', trim($m[1]));
        }

        if (preg_match('/Email ID\s*[:|]*\s*([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $block, $m)) {
            $details['email'] = strtolower(trim($m[1]));
        }

        if (preg_match('/GSTIN\s*[:|]*\s*([A-Z0-9]{15})\b/i', $block, $m)) {
            $details['gstin'] = trim($m[1]);
        } elseif (preg_match('/GSTIN\s*[:|]*\s*(.*?)(?=\s*(Address|पता|Financial|वित्तीय|Paying|Seller|$))/is', $block, $m)) {
            $details['gstin'] = trim($m[1]);
        }

        if (preg_match('/Address\s*[:,]?\s*(.*?)(?=Seller Details|"व|व|!व|#व|विक्रेता|$)/isu', $block, $m)) {
            $details['address'] = $this->cleanText($m[1]);
        }

        return $details;
    }

    /**
     * Extract financial approval details
     */
    private function extractFinancialApproval($text)
    {
        if (!preg_match('/Financial Approval Detail\s*(.*?)(?=Paying Authority|Seller Details|विक्रेता|$)/is', $text, $match)) {
            return [];
        }

        $block = $this->cleanText($match[1]);
        $details = [];

        // IFD Concurrence
        if (preg_match('/IFD Concurrence\s*[:|]*\s*([^\n]+)/i', $block, $m)) {
            $details['ifd_concurrence'] = $this->extractYesNo($m[1]);
        }

        // Administrative Approval
        if (preg_match('/Administrative Approval.*?:\s*([^\"व|!व|#व|]+)/iu',  $block, $m)) {
            $details['admin_approval'] = trim($m[1]);
        }

        // Financial Approval
        if (preg_match('/Financial Approval\s*[:|]*\s*([^\n]+)/i', $block, $m)) {
            $details['financial_approval'] = $this->extractYesNo($m[1]);
        }

        return $details;
    }

    /**
     * Extract paying authority details
     */
    private function extractPayingAuthority($text)
    {
        if (!preg_match('/Paying Authority Details\s*(.*?)(?=Seller Details|विक्रेता|Seller|$)/is', $text, $match)) {
            return [];
        }

        $block = $this->cleanText($match[1]);
        $details = [];

        if (preg_match('/Role\s*[:|]*\s*(.*?)(?=भुगतान का तरीका|Payment Mode|$)/iu', $block, $m)) {
            $details['role'] = trim($m[1]);
        }

        if (preg_match('/Payment Mode\s*[:|]*\s*(.*?)(?=पद|Designation|$)/iu', $block, $m)) {
            $details['payment_mode'] = trim($m[1]);
        }

        if (preg_match('/Designation\s*[:|]*\s*(.*?)(?=\s*(Email|ईमेल|GSTIN|जीएसट|Address|पता|$))/isu', $block, $m)) {
            $details['designation'] = trim($m[1]);
        }

        if (preg_match('/Email ID\s*[:|]*\s*([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $block, $m)) {
            $details['email'] = strtolower(trim($m[1]));
        }

        if (preg_match('/GSTIN\s*[:|]*\s*([A-Z0-9]{15})\b/i', $block, $m)) {
            $details['gstin'] = trim($m[1]);
        } elseif (preg_match('/GSTIN\s*[:|]*\s*(.*?)(?=\s*(Address|पता|Email|ईमेल|$))/is', $block, $m)) {
            $details['gstin'] = trim($m[1]);
        }

        if (preg_match('/Address\s*[:,]?\s*(.*?)(?=Seller Details|परे|"व|व|!व|#व|विक्रेता|$)/isu', $block, $m)) {
            $details['address'] = $this->cleanText($m[1]);
        }

        return $details;
    }

    /**
     * Extract consignee details section
     */
    private function extractConsigneeDetails($text)
    {
        if (!preg_match('/Consignee Detail\s*(.*?)(?=Product Specification|Terms and Conditions|$)/is', $text, $match)) {
            return [];
        }

        $block = $this->cleanText($match[1]);
        $details = [];

        if (preg_match('/Designation\s*[:|]*\s*(.*?)(?=\s*(Email|ईमेल|Contact|संपक|GSTIN|जीएसट|Address|पता|$))/isu', $block, $m)) {
            $details['designation'] = trim($m[1]);
        }

        if (preg_match('/Email ID\s*[:|]*\s*([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $block, $m)) {
            $details['email'] = strtolower(trim($m[1]));
        }

        if (preg_match('/Contact\s*[:|]*\s*([0-9+\-\s()]+)/i', $block, $m)) {
            $details['contact'] = preg_replace('/\s+/', '', trim($m[1]));
        }

        if (preg_match('/GSTIN\s*[:|]*\s*(.*?)(?=\s*(hp|acer|dell|lenovo|ARU|Zenix|Product|Item|Qty|Quantity|[0-9]{1,2}-[A-Za-z]{3}-[0-9]{4}|पता|Address|$))/is', $block, $m)) {
            $details['gstin'] = $this->cleanGstin($m[1]);
        }

        // Delivery dates
        if (preg_match('/-\s*(\d+)\s+(\d{2}-[A-Za-z]{3}-\d{4})\s+(\d{2}-[A-Za-z]{3}-\d{4})/u', $block, $m)) {
            $details['delivery_quantity'] = (int)$m[1];
            $details['delivery_start'] = $m[2];
            $details['delivery_end'] = $m[3];
        }


        if (preg_match('/(?:पता\s*\|\s*Address|Address)\s*:\s*(.*?India)/is', $block, $m)) {

            $raw = $m[1];

            // ✅ Step 1: Remove product descriptions & quantity/date blocks
            $raw = preg_replace('/(?:hp|acer|dell|lenovo|Asus|Zenix)[^\n]*?(All in One PC|Desktop|Laptop).*?\d{2}-[A-Za-z]{3}-\d{4}.*?\d{2}-[A-Za-z]{3}-\d{4}/is', '', $raw);

            // ✅ Remove any standalone product names
            $raw = preg_replace('/\b(?:hp|acer|dell|okaya|VOLTRIQ|lenovo|asus|zenix)\b[^,]*/i', '', $raw);

            // ✅ Step 2: Cleanup stray hyphens, brackets
            $raw = str_replace(['[', ']'], '', $raw);

            // ✅ Step 3: Collapse extra whitespace
            $raw = preg_replace('/\s+/', ' ', $raw);

            // ✅ Step 4: Make sure it ends at India exactly
            $raw = preg_replace('/India.*/i', 'India', $raw);

            // ✅ Step 5: Remove double commas
            $raw = preg_replace('/,+/', ',', $raw);

            // ✅ Final output
            $details['address'] = trim($raw);
        }

        return $details;
    }

    /**
     * Extract contract details
     */
    private function extractContractDetails($text)
    {
        $details = [];

        if (preg_match('/Contract No\s*[:|]*\s*([A-Z0-9-]+)/i', $text, $m)) {
            $details['contract_no'] = trim($m[1]);
        }

        if (preg_match('/Generated Date\s*:*\s*([0-9A-Za-z-]+)/i', $text, $m)) {
            $details['generated_date'] = trim($m[1]);
        }

        return $details;
    }

    private function extractProductDetails($text)
    {
        $details = [];

        if (!preg_match('/Product Details(.*?)(?=Consignee Detail|Terms and Conditions|$)/is', $text, $section)) {
            return $details;
        }

        $block = $this->cleanBlock($section[1]);

        if (preg_match('/Product Name\s*:\s*(?:Product Name\s*:\s*)?(.+?)(?:\s*(Brand|ांड|ांड|ांड|ांड|$))/isu', $block, $m)) {
            $details['product_name'] = $this->cleanText($m[1]);
        }

        if (preg_match('/Brand\s*:\s*(?:Brand\s*:\s*)?(.+?)(?=\s+(Brand Type|ांड|ांड|ांड|ांड|$))/isu', $block, $m)) {
            $details['brand'] = $this->cleanText($m[1]);
        }


        if (preg_match('/Brand Type\s*:\s*(?:Brand Type\s*:\s*)?(.+?)(?=\s+(Catalogue|क|$))/isu', $block, $m)) {
            $details['brand_type'] = $this->cleanText($m[1]);
        }


        if (preg_match('/Catalogue Status\s*:\s*(?:Catalogue Status\s*:\s*)?(.+?)(?=\s+(Selling As|क|$))/isu', $block, $m)) {
            $details['catalogue_status'] = $this->cleanText($m[1]);
        }


        if (preg_match('/Selling As\s*:\s*(?:Selling As\s*:\s*)?(.+?)(?=\s+(Category|ेणी|sेणी|rेणी|wेणी|tेणी|$))/isu', $block, $m)) {
            $details['selling_as'] = $this->cleanText($m[1]);
        }


        if (preg_match('/Category Name\s*&\s*Quadrant\s*:\s*(?:Category Name\s*&\s*Quadrant\s*:\s*)?(.+?)()(?=\s+(Model|मॉडल|$))/iu', $block, $m)) {
            $details['category'] = $this->cleanText($m[1]);
        }

        if (preg_match('/Model\s*:\s*(?:Model\s*:\s*)?(.+?)(?=\s+(HSN Code|एचएसएन|$))/iu', $block, $m)) {
            $details['model'] = $this->cleanText($m[1]);
        }

        if (preg_match('/HSN Code\s*:\s*(?:HSN Code\s*:\s*)?(.+?)(?=\s+(pieces|Total Order Value|Consignee|Model|Category|[0-9]+\s+pieces|$))/isu', $block, $m)) {
            $details['hsn_code'] = $this->cleanText($m[1]);
        }


        if (preg_match('/Model.*?\s+(\d+)\s*pieces?/isu', $block, $m) || preg_match('/HSN Code.*?\s+(\d+)\s*pieces?/isu', $block, $m) || preg_match('/Catalogue Status.*?\s+(\d+)\s*pieces?/isu', $block, $m)) {
            $details['quantity'] = (int)$m[1];
        }


        if (preg_match('/\d+\s*pieces\s+([0-9,]+)\s+NA/iu', $block, $m)) {
            $details['unit_price'] = (int) str_replace(',', '', $m[1]);
        }


        if (preg_match('/Total Order Value\s*\(in INR\)\s*([0-9,]+)/iu', $block, $m)) {
            $details['total_order_value'] = (int)str_replace(',', '', $m[1]);
        }

        return $details;
    }




    /**
     * Get combined seller and service provider details
     */
    public function getCombinedDetails($data)
    {
        $combined = [];

        if (!empty($data['seller_details'])) {
            $row = $data['seller_details'];
            $row['type'] = 'Seller';
            $combined[] = $row;
        }

        if (!empty($data['service_provider_details'])) {
            $row = $data['service_provider_details'];
            $row['type'] = 'Service Provider';
            $combined[] = $row;
        }

        return $combined;
    }



    // ========== HELPER METHODS ==========

    /**
     * Clean a text block - remove extra spaces and pipes
     */
    private function cleanBlock($text)
    {
        $text = preg_replace('/\|+/', ' ', $text);
        $text = preg_replace('/\s{2,}/', ' ', $text);
        return trim($text);
    }

    /**
     * Clean general text - collapse whitespace
     */
    private function cleanText($text)
    {
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text, " |:\t\n\r\0\x0B");
    }

    /**
     * Extract Yes/No from text
     */
    private function extractYesNo($text)
    {
        if (preg_match('/\b(Yes|No)\b/i', $text, $match)) {
            return ucfirst(strtolower($match[1]));
        }
        return '';
    }

    /**
     * Clean GSTIN - extract only valid format
     */
    private function cleanGstin($raw)
    {
        if (empty($raw)) {
            return '';
        }

        // Try to match standard GSTIN format (15 characters)
        if (preg_match('/\b([A-Z0-9]{15})\b/i', $raw, $match)) {
            return strtoupper($match[1]);
        }

        // Fallback - keep only first alphanumeric token
        if (preg_match('/^[A-Z0-9-]{2,20}/i', $raw, $match)) {
            return trim($match[0]);
        }

        // Last resort - first word
        return trim(explode(' ', $raw)[0]);
    }

    /**
     * Clean address - remove product names and extra formatting
     */
    private function cleanAddress($raw)
    {
        // Remove product descriptions and dates
        $raw = preg_replace('/(?:hp|acer|dell|lenovo|Asus|Zenix)[^\n]*?(All in One PC|Desktop|Laptop).*?\d{2}-[A-Za-z]{3}-\d{4}.*?\d{2}-[A-Za-z]{3}-\d{4}/is', '', $raw);

        // Remove standalone product names
        $raw = preg_replace('/\b(?:hp|acer|dell|lenovo|asus|zenix)\b[^,]*/i', '', $raw);

        // Remove brackets
        $raw = str_replace(['[', ']'], '', $raw);

        // Collapse whitespace
        $raw = preg_replace('/\s+/', ' ', $raw);

        // Ensure it ends at India
        $raw = preg_replace('/India.*/i', 'India', $raw);

        // Remove double commas
        $raw = preg_replace('/,+/', ',', $raw);

        return trim($raw);
    }

    public function countFilesWithSection($allData, $sectionKey)
    {
        return count(array_filter($allData, fn($d) => !empty($d[$sectionKey])));
    }
    
}
