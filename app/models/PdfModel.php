<?php

class PdfModel
{
    // use Model;

    public function extractData($text)
    {
        return [
            'seller_details' => $this->extractSellerDetails($text),
            'service_provider_details' => $this->extractServiceProviderDetails($text),
            'raw_text' => $text
        ];
    }

    private function extractSellerDetails($text)
    {
        $details = [];

        // 🔹 Limit regex scope to only the "Seller Details" block
        if (preg_match('/Seller Details(.*?)(?=Service Provider|$)/is', $text, $section)) {
            $sellerText = $section[1];

            // Company Name
            if (preg_match('/Company Name\s*:\s*([^\n]+)/i', $sellerText, $match)) {
                $details['company_name'] = trim($match[1]);
            }

            // GeM Seller ID
            if (preg_match('/GeM Seller ID\s*:\s*([^\n]+)/i', $sellerText, $match)) {
                $details['gem_seller_id'] = trim($match[1]);
            }

            // Contact Number
            if (preg_match('/Contact No\.?\s*:\s*([^\n]+)/i', $sellerText, $match)) {
                $details['contact_number'] = trim($match[1]);
            }

            // Email
            if (preg_match('/Email ID\s*:\s*([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $sellerText, $match)) {
                $details['email'] = trim($match[1]);
            }

            // Address
            if (preg_match('/Address\s*:\s*(.*?)(?=एमएसएमई|MSME|GSTIN|$)/is', $sellerText, $match)) {
                $details['address'] = $this->cleanText($match[1]);
            }

            // GSTIN
            if (preg_match('/GSTIN\s*:\s*([^\n]+)/i', $sellerText, $match)) {
                $details['gstin'] = trim($match[1]);
            }

            // MSME Registration
            if (preg_match('/MSME Registration(?: number)?\s*:\s*([^\n]+)/i', $sellerText, $match)) {
                $details['msme_registration'] = trim($match[1]);
            }
        }

        return $details;
    }

    private function extractServiceProviderDetails($text)
    {
        $details = [];

        // 🔹 Limit regex scope to only the "Service Provider" block
        if (preg_match('/Service Provider(.*?)(?=Buyer Details|Financial Approval Detail|$)/is', $text, $section)) {
            $spText = $section[1];

            // Company Name
            if (preg_match('/Company Name\s*:\s*([^\n]+)/i', $spText, $match)) {
                $details['company_name'] = trim($match[1]);
            }

            // GeM Seller ID
            if (preg_match('/GeM Seller ID\s*:\s*([^\n]+)/i', $spText, $match)) {
                $details['gem_seller_id'] = trim($match[1]);
            }

            // Contact Number
            if (preg_match('/Contact No\.?\s*:\s*([^\n]+)/i', $spText, $match)) {
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
            if (preg_match('/GSTIN\s*:\s*([^\n]+)/i', $spText, $match)) {
                $details['gstin'] = trim($match[1]);
            }

            // MSME Registration
            if (preg_match('/MSME Registration(?: number)?\s*:\s*([^\n]+)/i', $spText, $match)) {
                $details['msme_registration'] = trim($match[1]);
            }
        }

        return $details;
    }
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


    private function extractContactInfo($text)
    {
        $contacts = [];

        // Extract all emails
        if (preg_match_all('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', $text, $matches)) {
            $contacts['emails'] = array_unique($matches[0]);
        }

        // Extract all phone numbers
        if (preg_match_all('/\+?\d{10,15}/', $text, $matches)) {
            $contacts['phone_numbers'] = array_unique($matches[0]);
        }

        return $contacts;
    }

    private function cleanText($text)
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}
