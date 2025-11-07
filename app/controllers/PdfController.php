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

        $data = [
            'title' => 'Data Extractor',
            'breadcrumb' =>  ['Home / PDF Uploader'],
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

        $datas = $result['allData'];
        // $file_names = $result['file_names'];

        // show($result);       // Debug
        // show($file_names);   // Debug
        // show($datas);        // Debug

        $model = new PdfModel();

        $allData = [];

        foreach ($datas as $item) {

            // ✅ Extract text
            $extracted = $model->extractData($item['raw_text']);

            // ✅ Add correct file name
            $extracted['file_name'] = $item['file_name'];

            // ✅ Add into final array
            $allData[] = $extracted;
        }

        // show($allData);


        // Generate Excel with multiple sheets
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remove default sheet

        // Sheet 1: Seller Details
        $this->createSellerDetailsSheet($spreadsheet, $allData);

        // Sheet 2: Service Provider Details
        $this->createServiceProviderSheet($spreadsheet, $allData);

        // Sheet 3: Combined Sheet
        $this->createCombinedSheet($spreadsheet, $allData);

        // Sheet 4: Contract Details
        $this->createContractDetailsSheet($spreadsheet, $allData);

        // Save Excel file
        // Store $allData in session for download later
        $_SESSION['pdf_excel_data'] = $allData;

        // Render result view
        $data = [
            'allData' => $allData,
            'excel_path' => '/pdf/downloadExcel', // new route for Excel download
            'title' => 'Extracted Data',
            'breadcrumb' => ['PDF', 'Extracted Data']
        ];
        $this->view('pdf/result_view', $data);
        // show($allData);
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
