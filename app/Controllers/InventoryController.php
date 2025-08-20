<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InventoryController extends BaseController
{
    protected $cache;
    protected $apiUrl = 'http://10.2.38.143:8000/zapi_sth/zapi_sodexo/zstock?sap-client=180';

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    public function index()
    {
        $data['title'] = 'Report Inventory';
        $data['segment1'] = 'Report';
        return view('report/mm/index', $data);
    }

    private function getData()
    {
        $cacheKey = 'inventory_data';
        $data = $this->cache->get($cacheKey);

        if ($data === null) {
            try {
                $client = \Config\Services::curlrequest();
                $response = $client->get($this->apiUrl, [
                    'timeout' => 30,
                    'headers' => ['Accept' => 'application/json']
                ]);

                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody(), true);

                    $this->cache->save($cacheKey, $data, 1800);

                    $this->saveBackup($data);
                } else {
                    throw new \Exception('API returned status: ' . $response->getStatusCode());
                }
            } catch (\Exception $e) {
                $data = $this->loadFromBackup();
                if (!$data) {
                    throw new \Exception('API unavailable and no backup data found');
                }
            }
        }

        return $data;
    }

    private function saveBackup($data)
    {
        $backupFile = WRITEPATH . 'cache/inventory_backup.json';
        if (!is_dir(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0777, true);
        }
        file_put_contents($backupFile, json_encode($data));
    }

    private function loadFromBackup()
    {
        $backupFile = WRITEPATH . 'cache/inventory_backup.json';
        if (file_exists($backupFile)) {
            return json_decode(file_get_contents($backupFile), true);
        }
        return null;
    }

    private function calculateAging($grDate)
    {
        if (empty($grDate) || strlen($grDate) !== 8) {
            return '';
        }
        
        $grDateTime = \DateTime::createFromFormat('Ymd', $grDate);
        if ($grDateTime) {
            $today = new \DateTime();
            $interval = $today->diff($grDateTime);
            return $interval->days . ' days';
        }
        
        return '';
    }

    public function getInventoryData()
    {
        try {
            $data = $this->getData();

            $draw = intval($this->request->getPost('draw'));
            $start = intval($this->request->getPost('start'));
            $length = intval($this->request->getPost('length'));
            $searchValue = $this->request->getPost('search')['value'] ?? '';

            $filteredData = $data;
            if (!empty($searchValue)) {
                $filteredData = array_filter($data, function($item) use ($searchValue) {
                    $searchFields = [
                        $item['CUSTOMER_NAME'] ?? '',
                        $item['STYLE'] ?? '',
                        $item['MATERIAL'] ?? '',
                        $item['FORECAST_QUOTATION'] ?? '',
                        $item['BATCH'] ?? '',
                        $item['COLOR'] ?? '',
                        $item['SIZE'] ?? ''
                    ];

                    foreach ($searchFields as $field) {
                        if (stripos($field, $searchValue) !== false) {
                            return true;
                        }
                    }
                    return false;
                });
            }

            $totalRecords = count($data);
            $filteredRecords = count($filteredData);
            $pagedData = array_slice($filteredData, $start, $length);
            $processedData = [];
            $counter = $start + 1;
            
            foreach ($pagedData as $item) {
                $processedData[] = [
                    $counter++,
                    $item['FORECAST_QUOTATION'] ?? '',
                    $item['SO_FORECAST'] ?? '',
                    $item['SO_ACTUAL'] ?? '',
                    $item['CUSTOMER_NAME'] ?? '',
                    $item['QUOT_ACTUAL'] ?? '',
                    $item['PO_BUYER'] ?? '',
                    $item['STYLE'] ?? '',
                    $item['COLOR'] ?? '',
                    $item['SIZE'] ?? '',
                    number_format($item['QTY'] ?? 0, 0),
                    $item['PROD_YEAR'] ?? '',
                    $this->calculateAging($item['GR_DATE'] ?? '')
                ];
            }

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $processedData
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'draw' => intval($this->request->getPost('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exportExcel()
    {
        try {
            $data = $this->getData();

            $data = array_filter($data, function($item) {
                return !empty($item) && isset($item['PROD_YEAR']);
            });

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getProperties()
                ->setCreator('Sodexo Portal')
                ->setTitle('Inventory Report')
                ->setSubject('Inventory Data Export')
                ->setDescription('Complete inventory data from Sodexo Portal');

            $headers = [
                '#',
                'Forecast Quotation',
                'SO Forecast', 
                'SO Actual (Allocated)',
                'Customer',
                'Actual Quotation',
                'PO Buyer',
                'Style',
                'Color',
                'Size',
                'Qty',
                'Production Year',
                'Aging (days)'
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            $headerRange = 'A1:' . chr(ord('A') + count($headers) - 1) . '1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4CAF50']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            $row = 2;
            $counter = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $counter++);
                $sheet->setCellValue('B' . $row, $item['FORECAST_QUOTATION'] ?? '');
                $sheet->setCellValue('C' . $row, $item['SO_FORECAST'] ?? '');
                $sheet->setCellValue('D' . $row, $item['SO_ACTUAL'] ?? '');
                $sheet->setCellValue('E' . $row, $item['CUSTOMER_NAME'] ?? '');
                $sheet->setCellValue('F' . $row, $item['QUOT_ACTUAL'] ?? '');
                $sheet->setCellValue('G' . $row, $item['PO_BUYER'] ?? '');
                $sheet->setCellValue('H' . $row, $item['STYLE'] ?? '');
                $sheet->setCellValue('I' . $row, $item['COLOR'] ?? '');
                $sheet->setCellValue('J' . $row, $item['SIZE'] ?? '');
                $sheet->setCellValue('K' . $row, $item['QTY'] ?? 0);
                $sheet->setCellValue('L' . $row, $item['PROD_YEAR'] ?? '');
                $sheet->setCellValue('M' . $row, $this->calculateAging($item['GR_DATE'] ?? ''));
                $row++;
            }

            $lastColumn = chr(ord('A') + count($headers) - 1);
            foreach (range('A', $lastColumn) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $dataRange = 'A1:' . $lastColumn . ($row - 1);
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);

            for ($i = 2; $i < $row; $i += 2) {
                $sheet->getStyle('A' . $i . ':' . $lastColumn . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5']
                    ]
                ]);
            }

            $sheet->getStyle('A2:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
            $sheet->getStyle('B2:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Year
            $sheet->getStyle('H2:H' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Qty
            $sheet->getStyle('J2:J' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Line Item
            $sheet->getStyle('U2:U' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // GR Date
            $sheet->getStyle('V2:V' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Aging

            $filename = 'Inventory_Report_' . date('Y-m-d_H-i-s') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            exit;

        } catch (\Exception $e) {
            log_message('error', 'Export Excel error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export Excel file: ' . $e->getMessage());
        }
    }

    public function exportCsv()
    {
        try {
            $data = $this->getData();

            $data = array_filter($data, function($item) {
                return !empty($item) && isset($item['PROD_YEAR']);
            });

            $filename = 'Inventory_Report_' . date('Y-m-d_H-i-s') . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $output = fopen('php://output', 'w');

            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            $headers = [
                '#', 'Forecast Quotation', 'SO Forecast', 'SO Actual (Allocated)',
                'Customer', 'Actual Quotation', 'PO Buyer', 'Style', 'Color',
                'Size', 'Qty', 'Production Year', 'Aging (days)'
            ];

            fputcsv($output, $headers);

            $counter = 1;
            foreach ($data as $item) {
                $row = [
                    $counter++,
                    $item['FORECAST_QUOTATION'] ?? '',
                    $item['SO_FORECAST'] ?? '',
                    $item['SO_ACTUAL'] ?? '',
                    $item['CUSTOMER_NAME'] ?? '',
                    $item['QUOT_ACTUAL'] ?? '',
                    $item['PO_BUYER'] ?? '',
                    $item['STYLE'] ?? '',
                    $item['COLOR'] ?? '',
                    $item['SIZE'] ?? '',
                    $item['QTY'] ?? 0,
                    $item['PROD_YEAR'] ?? '',
                    $this->calculateAging($item['GR_DATE'] ?? '')
                ];
                fputcsv($output, $row);
            }
            
            fclose($output);
            exit;
            
        } catch (\Exception $e) {
            log_message('error', 'Export CSV error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export CSV file: ' . $e->getMessage());
        }
    }

    public function refreshCache()
    {
        $cacheKey = 'inventory_data';
        $this->cache->delete($cacheKey);
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Cache refreshed successfully'
        ]);
    }
}
