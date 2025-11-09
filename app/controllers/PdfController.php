<?php

use Smalot\PdfParser\Parser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PdfController
{
    use Controller;

    public function index()
    {
        unset($_SESSION['pdf_excel_data']); // Clear previous session data

        $sectionList = (new PdfModel())->getAvailableSections();
        $data = [
            'title' => 'Data Extractor',
            'breadcrumb' =>  ['Home / PDF Uploader'],
            'sectionList' => $sectionList,
        ];

        $this->view('pdf/upload_form', $data);
    }

    public function upload()
    {
        unset($_SESSION['pdf_excel_data']);

        if (!isset($_POST['result']) || empty($_POST['result'])) {
            Flash::set('toast', 'File upload failed. Please try again.', 'danger');
            redirect('pdf');
        }

        $result = json_decode($_POST['result'], true);
        $filters = json_decode($_POST['filters'], true);

        $datas = $result['allData'];
        $duplicates = $result['duplicates'];
        $summary = $result['summary'];

        // show($result);       // Debug
        // show($filters);   // Debug
        // show($datas);        // Debug

        $model = new PdfModel();

        $allData = [];

        // $sectionMap = $model->getSectionToFieldMap();

        foreach ($datas as $item) {
            $extracted = $model->extractData($item['raw_text']);

            // Add file info
            $extracted['file_name'] = $item['file_name'];
            $extracted['base_name'] = $item['base_name'];

            // ✅ Remove unwanted sections according to filters
            // if (!empty($filters)) {
            //     foreach ($sectionMap as $filterKey => $sectionName) {

            //         // seller & service ALWAYS kept
            //         if ($filterKey === 'seller' || $filterKey === 'service') {
            //             continue;
            //         }

            //         // If this section is NOT selected → remove it
            //         if (empty($filters[$filterKey])) {
            //             unset($extracted[$sectionName]);
            //         }
            //     }
            // }

            $allData[] = $extracted;
        }

        show($allData);

        // // Generate Excel with multiple sheets
        // $spreadsheet = new Spreadsheet();
        // $spreadsheet->removeSheetByIndex(0);

        // $this->createSellerDetailsSheet($spreadsheet, $allData);
        // $this->createServiceProviderSheet($spreadsheet, $allData);
        // $this->createCombinedSheet($spreadsheet, $allData);

        // $sheetMap = [
        //     'contract'     => 'createContractDetailsSheet',
        //     'buyer'        => 'createBuyerDetailsSheet',
        //     'consignee'    => 'createConsigneeDetailsSheet',
        //     'organisation' => 'createOrganisationDetailsSheet',
        //     'financial'    => 'createFinancialApprovalSheet',
        //     'paying'       => 'createPayingAuthoritySheet',
        //     // 'raw_text'   => 'createRawTextSheet',
        // ];

        // if (!empty($filters)) {
        //     foreach ($sheetMap as $key => $method) {
        //         if (!empty($filters[$key])) {
        //             $this->$method($spreadsheet, $allData);
        //         }
        //     }
        // } else {
        //     // Default: create all sheets (fallback)
        //     $this->createSellerDetailsSheet($spreadsheet, $allData);
        //     $this->createServiceProviderSheet($spreadsheet, $allData);
        //     $this->createCombinedSheet($spreadsheet, $allData);
        // }


        // // Save Excel file
        // // Store $allData in session for download later
        // $_SESSION['pdf_excel_data'] = $allData;

        // // Render result view
        // $data = [
        //     'allData' => $allData,
        //     'duplicates' => $duplicates,
        //     'summary' => $summary,
        //     'filters' => $filters,
        //     'excel_path' => '/pdf/downloadExcel', // new route for Excel download
        //     'title' => 'Extracted Data',
        //     'breadcrumb' => ['PDF', 'Extracted Data']
        // ];
        // $this->view('pdf/result_view', $data);
    }



    private function createContractDetailsSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->getSheetByName('Contract Details');
        $newSheet = false;

        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Contract Details');
            $headers = ['File Name', 'Contract No.', 'Generated Date'];
            $this->applyHeaderStyle($sheet, $headers);
            $newSheet = true;
        }
        $row = $sheet->getHighestRow();
        if ($newSheet || $row < 2) {
            $row = 2;
        } else {
            $row += 1;
        }

        foreach ($allData as $data) {
            $details = $data['contract_details'] ?? [];
            if (empty($details)) {
                continue;
            }
            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $details['contract_no'] ?? 'N/A');
            $sheet->setCellValue('C' . $row, $details['generated_date'] ?? 'N/A');
            $row++;
        }
        $this->setColumnWidths($sheet, [20, 25, 20,]);
    }

    private function createSellerDetailsSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->getSheetByName('Seller Details');
        $newSheet = false;

        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Seller Details');
            $headers = ['File Name', 'Company Name', 'GeM Seller ID', 'Contact Number', 'Email ID', 'Address', 'GSTIN', 'MSME Registration'];
            $this->applyHeaderStyle($sheet, $headers);
            $newSheet = true;
        }

        $row = $sheet->getHighestRow();
        if ($newSheet || $row < 2) {
            $row = 2;
        } else {
            $row += 1;
        }

        foreach ($allData as $data) {
            $details = $data['seller_details'] ?? [];
            if (empty($details)) {
                continue;
            }
            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $details['company_name'] ?? 'N/A');
            $sheet->setCellValue('C' . $row, $details['gem_seller_id'] ?? 'N/A');
            $sheet->setCellValue('D' . $row, $details['contact_number'] ?? 'N/A');
            $sheet->setCellValue('E' . $row, $details['email'] ?? 'N/A');
            $sheet->setCellValue('F' . $row, $details['address'] ?? 'N/A');
            $sheet->setCellValue('G' . $row, $details['gstin'] ?? 'N/A');
            $sheet->setCellValue('H' . $row, $details['msme_registration'] ?? 'N/A');
            $row++;
        }

        $this->setColumnWidths($sheet, [20, 25, 20, 18, 25, 40, 20, 25]);
    }


    private function createServiceProviderSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->getSheetByName('Service Provider Details');
        $newSheet = false;

        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Service Provider Details');
            $headers = ['File Name', 'Company Name', 'GeM Seller ID', 'Contact Number', 'Email ID', 'Address', 'GSTIN', 'MSME Registration'];
            $this->applyHeaderStyle($sheet, $headers);
            $newSheet = true;
        }

        $row = $sheet->getHighestRow();
        if ($newSheet || $row < 2) {
            $row = 2;
        } else {
            $row += 1;
        }

        foreach ($allData as $data) {
            $details = $data['service_provider_details'] ?? [];
            if (empty($details)) {
                continue;
            }
            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $details['company_name'] ?? 'N/A');
            $sheet->setCellValue('C' . $row, $details['gem_seller_id'] ?? 'N/A');
            $sheet->setCellValue('D' . $row, $details['contact_number'] ?? 'N/A');
            $sheet->setCellValue('E' . $row, $details['email'] ?? 'N/A');
            $sheet->setCellValue('F' . $row, $details['address'] ?? 'N/A');
            $sheet->setCellValue('G' . $row, $details['gstin'] ?? 'N/A');
            $sheet->setCellValue('H' . $row, $details['msme_registration'] ?? 'N/A');
            $row++;
        }

        $this->setColumnWidths($sheet, [20, 25, 20, 18, 25, 40, 20, 25]);
    }

    private function createCombinedSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Combined Details');

        $headers = [
            'File Name',
            'Type',
            'Company Name',
            'GeM Seller ID',
            'Contact Number',
            'Email',
            'Address',
            'GSTIN',
            'MSME Registration'
        ];
        $this->applyHeaderStyle($sheet, $headers);

        $row = 2;

        foreach ($allData as $data) {
            // Seller row
            $model = new PdfModel();
            $combinedDetails = $model->getCombinedDetails($data);
            foreach ($combinedDetails as $details) {
                $sheet->setCellValue('A' . $row, $data['file_name']);
                $sheet->setCellValue('B' . $row, $details['type'] ?? 'N/A');
                $sheet->setCellValue('C' . $row, $details['company_name'] ?? 'N/A');
                $sheet->setCellValue('D' . $row, $details['gem_seller_id'] ?? 'N/A');
                $sheet->setCellValue('E' . $row, $details['contact_number'] ?? 'N/A');
                $sheet->setCellValue('F' . $row, $details['email'] ?? 'N/A');
                $sheet->setCellValue('G' . $row, $details['address'] ?? 'N/A');
                $sheet->setCellValue('H' . $row, $details['gstin'] ?? 'N/A');
                $sheet->setCellValue('I' . $row, $details['msme_registration'] ?? 'N/A');
                $row++;
            }
        }

        $this->setColumnWidths($sheet, [20, 20, 25, 20, 18, 25, 40, 20, 25]);
    }

    private function createPayingAuthoritySheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->getSheetByName('Paying Authority Details');
        $newSheet = false;

        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Paying Authority Details');

            $headers = [
                'File Name',
                'Role',
                'Payment Mode',
                'Designation',
                'Email',
                'GSTIN',
                'Address'
            ];
            $this->applyHeaderStyle($sheet, $headers);
            $newSheet = true;
        }

        $row = ($newSheet || $sheet->getHighestRow() < 2) ? 2 : $sheet->getHighestRow() + 1;

        foreach ($allData as $data) {
            $details = $data['paying_authority_details'] ?? [];
            if (empty($details)) continue;

            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $details['role'] ?? 'N/A');
            $sheet->setCellValue('C' . $row, $details['payment_mode'] ?? 'N/A');
            $sheet->setCellValue('D' . $row, $details['designation'] ?? 'N/A');
            $sheet->setCellValue('E' . $row, $details['email'] ?? 'N/A');
            $sheet->setCellValue('F' . $row, $details['gstin'] ?? 'N/A');
            $sheet->setCellValue('G' . $row, $details['address'] ?? 'N/A');

            $row++;
        }

        $this->setColumnWidths($sheet, [20, 20, 20, 25, 25, 20, 45]);
    }

    private function createBuyerDetailsSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->getSheetByName('Buyer Details');
        $newSheet = false;

        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Buyer Details');

            $headers = [
                'File Name',
                'Designation',
                'Contact',
                'Email',
                'GSTIN',
                'Address'
            ];
            $this->applyHeaderStyle($sheet, $headers);
            $newSheet = true;
        }

        $row = ($newSheet || $sheet->getHighestRow() < 2) ? 2 : $sheet->getHighestRow() + 1;

        foreach ($allData as $data) {
            $details = $data['buyer_details'] ?? [];
            if (empty($details)) continue;

            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $details['designation'] ?? 'N/A');
            $sheet->setCellValue('C' . $row, $details['contact'] ?? 'N/A');
            $sheet->setCellValue('D' . $row, $details['email'] ?? 'N/A');
            $sheet->setCellValue('E' . $row, $details['gstin'] ?? 'N/A');
            $sheet->setCellValue('F' . $row, $details['address'] ?? 'N/A');

            $row++;
        }

        $this->setColumnWidths($sheet, [20, 20, 20, 25, 25, 40]);
    }

    private function createConsigneeDetailsSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->getSheetByName('Consignee Details');
        $newSheet = false;

        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Consignee Details');

            $headers = [
                'File Name',
                'Designation',
                'Email',
                'Contact',
                'GSTIN',
                'Address'
            ];
            $this->applyHeaderStyle($sheet, $headers);
            $newSheet = true;
        }

        $row = ($newSheet || $sheet->getHighestRow() < 2) ? 2 : $sheet->getHighestRow() + 1;

        foreach ($allData as $data) {
            $details = $data['consignee_details'] ?? [];
            if (empty($details)) continue;

            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $details['gstin'] ?? 'N/A');
            $sheet->setCellValue('C' . $row, $details['address'] ?? 'N/A');

            $row++;
        }

        $this->setColumnWidths($sheet, [20, 20, 55]);
    }

    private function createOrganisationDetailsSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->getSheetByName('Organisation Details');
        $newSheet = false;

        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Organisation Details');

            $headers = [
                'File Name',
                'Type',
                'Ministry',
                'Department',
                'Organisation Name',
                'Office Zone'
            ];
            $this->applyHeaderStyle($sheet, $headers);
            $newSheet = true;
        }

        $row = ($newSheet || $sheet->getHighestRow() < 2) ? 2 : $sheet->getHighestRow() + 1;

        foreach ($allData as $data) {
            $details = $data['organisation_details'] ?? [];
            if (empty($details)) continue;

            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $details['type'] ?? 'N/A');
            $sheet->setCellValue('C' . $row, $details['ministry'] ?? 'N/A');
            $sheet->setCellValue('D' . $row, $details['department'] ?? 'N/A');
            $sheet->setCellValue('E' . $row, $details['organisation_name'] ?? 'N/A');
            $sheet->setCellValue('F' . $row, $details['office_zone'] ?? 'N/A');

            $row++;
        }

        $this->setColumnWidths($sheet, [20, 20, 30, 25, 35, 20]);
    }

    private function createRawTextSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Raw Text');
        $headers = ['File Name', 'Raw Extracted Text'];
        $this->applyHeaderStyle($sheet, $headers);

        $row = 2;

        foreach ($allData as $data) {
            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $data['raw_text'] ?? '');
            $sheet->getColumnDimension('B')->setWidth(80);
            $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);
            $row++;
        }
        $this->setColumnWidths($sheet, [20, 100]);
    }

    private function createFinancialApprovalSheet($spreadsheet, $allData)
    {
        $sheet = $spreadsheet->getSheetByName('Financial Approval');
        $newSheet = false;

        if (!$sheet) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Financial Approval');

            $headers = [
                'File Name',
                'IFD Concurrence',
                'Administrative Approval',
                'Financial Approval'
            ];
            $this->applyHeaderStyle($sheet, $headers);
            $newSheet = true;
        }

        $row = ($newSheet || $sheet->getHighestRow() < 2) ? 2 : $sheet->getHighestRow() + 1;

        foreach ($allData as $data) {
            $details = $data['financial_approval'] ?? [];
            if (empty($details)) continue;

            $sheet->setCellValue('A' . $row, $data['file_name']);
            $sheet->setCellValue('B' . $row, $details['ifd'] ?? 'N/A');
            $sheet->setCellValue('C' . $row, $details['admin_approval'] ?? 'N/A');
            $sheet->setCellValue('D' . $row, $details['financial_approval'] ?? 'N/A');

            $row++;
        }

        $this->setColumnWidths($sheet, [20, 20, 40, 20]);
    }


    public function downloadExcel()
    {
        if (empty($_SESSION['pdf_excel_data'])) {
            Flash::set('toast', 'No data available to download.', 'warning');
            redirect('pdf');
        }

        $allData = $_SESSION['pdf_excel_data'];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $this->createSellerDetailsSheet($spreadsheet, $allData);
        $this->createServiceProviderSheet($spreadsheet, $allData);
        $this->createCombinedSheet($spreadsheet, $allData);
        $this->createContractDetailsSheet($spreadsheet, $allData);

        $filename = 'contract_data_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        unset($_SESSION['pdf_excel_data']); // Clear session data after download
        exit;
    }


    private function applyHeaderStyle($sheet, $headers)
    {
        foreach ($headers as $index => $header) {
            $cell = chr(65 + $index) . '1';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');

            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    private function setColumnWidths($sheet, $widths)
    {
        foreach ($widths as $index => $width) {
            $sheet->getColumnDimension(chr(65 + $index))->setWidth($width);
        }
    }
}
