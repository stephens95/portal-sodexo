<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SalesOrderController extends BaseController
{
    protected $cache;
    protected $apiUrl;

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
        $this->apiUrl = env('sap.api.sotrcb.url');
    }

    public function index()
    {
        $data['title'] = 'Sales Order Status Report';
        $data['segment1'] = 'Report';
        return view('report/sd/index', $data);
    }

    private function getCacheKey()
    {
        return 'sotrcb_data_' . md5($this->apiUrl);
    }

    private function getSoTracebilityFilePath()
    {
        return WRITEPATH . 'cache/sotrcb.json';
    }

    private function getData()
    {
        $cacheKey = $this->getCacheKey();
        $data = $this->cache->get($cacheKey);

        if ($data === null) {
            try {
                ini_set('memory_limit', '512M');
                set_time_limit(120);

                $client = \Config\Services::curlrequest();
                $response = $client->get($this->apiUrl, [
                    'timeout' => 120,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Connection' => 'keep-alive'
                    ]
                ]);

                if ($response->getStatusCode() === 200) {
                    $responseBody = $response->getBody();
                    $data = json_decode($responseBody, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
                    }

                    if (is_array($data)) {
                        // Cache for 30 minutes
                        $this->cache->save($cacheKey, $data, 1800);
                        $this->saveSoTracebilityData($data);
                    } else {
                        throw new \Exception('Invalid data format received from API');
                    }
                } else {
                    throw new \Exception('API returned status: ' . $response->getStatusCode());
                }
            } catch (\Exception $e) {
                log_message('error', 'SO API Error: ' . $e->getMessage());
                $data = $this->loadSoTracebilityData();
                if (!$data) {
                    throw new \Exception('API unavailable and no SO data found: ' . $e->getMessage());
                }
            }
        }

        return is_array($data) ? $data : [];
    }

    private function saveSoTracebilityData($data)
    {
        try {
            $soFile = $this->getSoTracebilityFilePath();
            $directory = dirname($soFile);

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $soData = [
                'api_url' => $this->apiUrl,
                'updated_at' => date('Y-m-d H:i:s'),
                'total_records' => count($data),
                'data' => $data
            ];

            $result = file_put_contents($soFile, json_encode($soData, JSON_UNESCAPED_SLASHES));
            if ($result === false) {
                log_message('error', 'Failed to save SO data to file: ' . $soFile);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error saving SO data: ' . $e->getMessage());
        }
    }

    private function loadSoTracebilityData()
    {
        try {
            $soFile = $this->getSoTracebilityFilePath();
            if (file_exists($soFile)) {
                $soData = json_decode(file_get_contents($soFile), true);

                if (isset($soData['api_url']) && $soData['api_url'] === $this->apiUrl) {
                    return $soData['data'] ?? [];
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error loading SO data: ' . $e->getMessage());
        }
        return [];
    }

    public function getSalesOrderData()
    {
        try {
            // Increase memory and execution time
            ini_set('memory_limit', '256M');
            set_time_limit(60);

            $data = $this->getData();

            if (!is_array($data)) {
                $data = [];
            }

            $draw = intval($this->request->getPost('draw') ?? 1);
            $start = intval($this->request->getPost('start') ?? 0);
            $length = intval($this->request->getPost('length') ?? 25);
            $searchValue = trim($this->request->getPost('search')['value'] ?? '');

            // Handle sorting
            $orderColumnIndex = intval($this->request->getPost('order')[0]['column'] ?? 1);
            $orderDirection = $this->request->getPost('order')[0]['dir'] ?? 'asc';

            // Map column index to data field
            $sortableColumns = [
                0 => null,
                1 => 'QO_SSA',
                2 => 'PO_SSA',
                3 => 'PO_BUYER',
                4 => 'END_CUSTOMER',
                5 => 'SO',
                6 => 'BUYER_STYLE',
                7 => 'SSA_STYLE',
                8 => 'COLOR',
                9 => 'ORDER_QTY',
                10 => 'DO',
                11 => 'QTY_SHIP',
                12 => 'OUTS_PO_QTY',
                13 => 'INV_NO',
                14 => 'INV_AMOUNT',
                15 => 'INV_CURR',
                16 => 'DUE_DATE',
                17 => 'PMT_DATE',
                18 => 'BRK_FEE',
                19 => 'MNG_FEE',
            ];

            $filteredData = $data;

            // Apply search filter
            if (!empty($searchValue)) {
                $filteredData = array_filter($data, function ($item) use ($searchValue) {
                    $searchFields = [
                        $item['QO_SSA'] ?? '',
                        $item['PO_SSA'] ?? '',
                        $item['PO_BUYER'] ?? '',
                        $item['END_CUSTOMER'] ?? '',
                        $item['SO'] ?? '',
                        !empty($item['BUYER_STYLE']) ? explode(' ', ltrim($item['BUYER_STYLE']))[0] : '',
                        !empty($item['SSA_STYLE']) ? explode(' ', ltrim($item['SSA_STYLE']))[0] : '',
                        $item['COLOR'] ?? '',
                        $item['DO'] ?? '',
                        $item['INV_NO'] ?? ''
                    ];

                    foreach ($searchFields as $field) {
                        if (stripos((string)$field, $searchValue) !== false) {
                            return true;
                        }
                    }
                    return false;
                });
            }

            // Apply sorting
            if (isset($sortableColumns[$orderColumnIndex]) && $sortableColumns[$orderColumnIndex] !== null) {
                $sortField = $sortableColumns[$orderColumnIndex];

                usort($filteredData, function ($a, $b) use ($sortField, $orderDirection) {
                    $valueA = $a[$sortField] ?? '';
                    $valueB = $b[$sortField] ?? '';

                    // Handle numeric sorting
                    if (in_array($sortField, ['ORDER_QTY', 'QTY_SHIP', 'OUTS_PO_QTY', 'INV_AMOUNT'])) {
                        $valueA = floatval($valueA);
                        $valueB = floatval($valueB);
                    } else {
                        $valueA = strtolower((string)$valueA);
                        $valueB = strtolower((string)$valueB);
                    }

                    $result = $valueA <=> $valueB;
                    return $orderDirection === 'desc' ? -$result : $result;
                });
            }

            $totalRecords = count($data);
            $filteredRecords = count($filteredData);

            // Reset array keys and paginate
            $filteredData = array_values($filteredData);
            $pagedData = array_slice($filteredData, $start, $length);

            $processedData = [];
            $counter = $start + 1;

            foreach ($pagedData as $item) {
                $buyer_style = !empty($item['BUYER_STYLE']) ? explode(' ', ltrim($item['BUYER_STYLE']))[0] : '';
                $ssa_style = !empty($item['SSA_STYLE']) ? explode(' ', ltrim($item['SSA_STYLE']))[0] : '';
                $dueDate = $item['DUE_DATE'] ?? '';
                if ($dueDate && $dueDate !== '0000-00-00') {
                    $dueDate = date('d M y', strtotime($dueDate));
                } else {
                    $dueDate = '';
                }

                $pmtDate = $item['PMT_DATE'] ?? '';
                if ($pmtDate && $pmtDate !== '0000-00-00') {
                    $pmtDate = date('d M y', strtotime($pmtDate));
                } else {
                    $pmtDate = '';
                }

                $processedData[] = [
                    $counter++,
                    $item['QO_SSA'] . '<br>' . $item['PO_SSA'],
                    // $item['PO_SSA'] ?? '',
                    $item['PO_BUYER'] ?? '',
                    $item['END_CUSTOMER'] ?? '',
                    $item['SO'] ?? '',
                    $buyer_style . '<br>' . $ssa_style,
                    // !empty($item['BUYER_STYLE']) ? explode(' ', ltrim($item['BUYER_STYLE']))[0] : '',
                    // !empty($item['SSA_STYLE']) ? explode(' ', ltrim($item['SSA_STYLE']))[0] : '',
                    $item['COLOR'] ?? '',
                    number_format(floatval($item['ORDER_QTY'] ?? 0), 0),
                    $item['DO'] ?? '',
                    number_format(floatval($item['QTY_SHIP'] ?? 0), 0),
                    number_format(floatval($item['OUTS_PO_QTY'] ?? 0), 0),
                    $item['INV_NO'] ?? '',
                    number_format(floatval($item['INV_AMOUNT'] ?? 0), 2),
                    $item['INV_CURR'] ?? '',
                    $dueDate ?? '',
                    $pmtDate ?? '',
                    number_format(floatval($item['BRK_FEE'] ?? 0), 2),
                    number_format(floatval($item['MNG_FEE'] ?? 0), 2),
                    '',
                ];
            }

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $processedData
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Sales Order Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load data: ' . $e->getMessage()
            ]);
        }
    }

    public function exportExcel()
    {
        try {
            ini_set('memory_limit', '512M');
            set_time_limit(300);

            $data = $this->getData();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getProperties()
                ->setCreator('Sodexo Portal')
                ->setTitle('Sales Order Traceability Report')
                ->setSubject('Sales Order Data Export')
                ->setDescription('Complete Sales Order data from Sodexo Portal');

            $headers = [
                '#',
                'QO SSA',
                'PO SSA',
                'PO Buyer',
                'End Customer',
                'Sales Order (AMT)',
                'Buyer Style',
                'SSA Style',
                'Colour',
                'Order Qty',
                'Delivery Order',
                'Shipment Qty',
                'Outstanding PO Qty',
                'Invoice Number',
                'Invoice Amount'
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            $headerRange = 'A1:O1';
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
                $sheet->setCellValue('B' . $row, $item['QO_SSA'] ?? '');
                $sheet->setCellValue('C' . $row, $item['PO_SSA'] ?? '');
                $sheet->setCellValue('D' . $row, $item['PO_BUYER'] ?? '');
                $sheet->setCellValue('E' . $row, $item['END_CUSTOMER'] ?? '');
                $sheet->setCellValue('F' . $row, $item['SO'] ?? '');
                $sheet->setCellValue('G' . $row, !empty($item['BUYER_STYLE']) ? explode(' ', ltrim($item['BUYER_STYLE']))[0] : '' ?? '');
                $sheet->setCellValue('H' . $row, !empty($item['SSA_STYLE']) ? explode(' ', ltrim($item['SSA_STYLE']))[0] : '' ?? '');
                $sheet->setCellValue('I' . $row, $item['COLOR'] ?? '');
                $sheet->setCellValue('J' . $row, floatval($item['ORDER_QTY'] ?? 0));
                $sheet->setCellValue('K' . $row, $item['DO'] ?? '');
                $sheet->setCellValue('L' . $row, floatval($item['QTY_SHIP'] ?? 0));
                $sheet->setCellValue('M' . $row, floatval($item['OUTS_PO_QTY'] ?? 0));
                $sheet->setCellValue('N' . $row, $item['INV_NO'] ?? '');
                $sheet->setCellValue('O' . $row, floatval($item['INV_AMOUNT'] ?? 0));
                $row++;
            }

            foreach (range('A', 'O') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $filename = 'Sales_Order_Report_' . date('Y-m-d_H-i-s') . '.xlsx';

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
            ini_set('memory_limit', '256M');
            set_time_limit(120);

            $data = $this->getData();

            $filename = 'Sales_Order_Report_' . date('Y-m-d_H-i-s') . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $output = fopen('php://output', 'w');

            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headers = [
                '#',
                'QO SSA',
                'PO SSA',
                'PO Buyer',
                'End Customer',
                'Sales Order (AMT)',
                'Buyer Style',
                'SSA Style',
                'Colour',
                'Order Qty',
                'Delivery Order',
                'Shipment Qty',
                'Outstanding PO Qty',
                'Invoice Number',
                'Invoice Amount'
            ];

            fputcsv($output, $headers);

            $counter = 1;
            foreach ($data as $item) {
                $row = [
                    $counter++,
                    $item['QO_SSA'] ?? '',
                    $item['PO_SSA'] ?? '',
                    $item['PO_BUYER'] ?? '',
                    $item['END_CUSTOMER'] ?? '',
                    $item['SO'] ?? '',
                    !empty($item['BUYER_STYLE']) ? explode(' ', ltrim($item['BUYER_STYLE']))[0] : '',
                    !empty($item['SSA_STYLE']) ? explode(' ', ltrim($item['SSA_STYLE']))[0] : '',
                    $item['COLOR'] ?? '',
                    floatval($item['ORDER_QTY'] ?? 0),
                    $item['DO'] ?? '',
                    floatval($item['QTY_SHIP'] ?? 0),
                    floatval($item['OUTS_PO_QTY'] ?? 0),
                    $item['INV_NO'] ?? '',
                    floatval($item['INV_AMOUNT'] ?? 0)
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
        try {
            $cacheKey = $this->getCacheKey();
            $this->cache->delete($cacheKey);

            $soFile = $this->getSoTracebilityFilePath();
            if (file_exists($soFile)) {
                unlink($soFile);
            }

            $this->getData();
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Cache refreshed successfully and new data loaded'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Refresh cache error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to refresh cache: ' . $e->getMessage()
            ]);
        }
    }

    // public function uploadDocument()
    // {
    //     $file = $this->request->getFile('document');
    //     if ($file && $file->isValid() && !$file->hasMoved()) {
    //         $newName = $file->getRandomName();
    //         $file->move(WRITEPATH . 'uploads/documents', $newName);
    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'File uploaded successfully',
    //             'file_path' => 'uploads/documents/' . $newName
    //         ]);
    //     } else {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => 'File upload failed: ' . ($file ? $file->getErrorString() : 'No file uploaded')
    //         ]);
    //     }
    // }

    // public function uploadDocument()
    // {
    //     $file = $this->request->getFile('document');
    //     $docType = $this->request->getPost('doc_type');
    //     $salesOrder = $this->request->getPost('sales_order');

    //     if (!$file || !$docType || !$salesOrder) {
    //         return $this->response->setJSON([
    //             'status' => 'error',
    //             'message' => 'Missing required parameters'
    //         ]);
    //     }

    //     if ($file->isValid() && !$file->hasMoved()) {
    //         try {
    //             // Get file extension
    //             $ext = $file->getExtension();

    //             // Map document types to prefixes
    //             $docTypePrefixes = [
    //                 'invoice' => 'INV',
    //                 'packing_list' => 'PL',
    //                 'bl_rw' => 'BL',
    //                 'coo' => 'COO',
    //                 'insurance' => 'INS'
    //             ];

    //             // Get prefix for document type
    //             $prefix = $docTypePrefixes[$docType] ?? 'DOC';

    //             // Generate filename: PREFIX_SALESORDER_TIMESTAMP.extension
    //             $newName = sprintf(
    //                 '%s_%s_%s.%s',
    //                 $prefix,
    //                 $salesOrder,
    //                 date('YmdHis'),
    //                 $ext
    //             );

    //             // Create upload directory if it doesn't exist
    //             $uploadPath = WRITEPATH . 'uploads/documents/' . $salesOrder;
    //             if (!is_dir($uploadPath)) {
    //                 mkdir($uploadPath, 0755, true);
    //             }

    //             // Move file to directory
    //             $file->move($uploadPath, $newName);

    //             return $this->response->setJSON([
    //                 'status' => 'success',
    //                 'message' => 'File uploaded successfully',
    //                 'file_path' => 'uploads/documents/' . $salesOrder . '/' . $newName
    //             ]);
    //         } catch (\Exception $e) {
    //             log_message('error', 'File upload error: ' . $e->getMessage());
    //             return $this->response->setJSON([
    //                 'status' => 'error',
    //                 'message' => 'Error processing file upload: ' . $e->getMessage()
    //             ]);
    //         }
    //     }

    //     return $this->response->setJSON([
    //         'status' => 'error',
    //         'message' => 'File upload failed: ' . ($file ? $file->getErrorString() : 'No file uploaded')
    //     ]);
    // }

    public function uploadDocument()
    {
        $file = $this->request->getFile('document');
        $docType = $this->request->getPost('doc_type');
        $salesOrder = $this->request->getPost('sales_order');

        if (!$file || !$docType || !$salesOrder) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing required parameters'
            ]);
        }

        if ($file->isValid() && !$file->hasMoved()) {
            try {
                // Get file extension
                $ext = $file->getExtension();

                // Get current year and month
                $year = date('Y');
                $month = date('m');

                // Map document types to prefixes and add running number format
                $docTypePrefixes = [
                    'invoice' => [
                        'prefix' => 'INV',
                        'format' => 'INV/%s/%s/%s/%04d'  // INV/YEAR/MONTH/SO/SEQUENCE
                    ],
                    'packing_list' => [
                        'prefix' => 'PL',
                        'format' => 'PL/%s/%s/%s/%04d'   // PL/YEAR/MONTH/SO/SEQUENCE
                    ],
                    'bl_rw' => [
                        'prefix' => 'BL',
                        'format' => 'BL/%s/%s/%s/%04d'   // BL/YEAR/MONTH/SO/SEQUENCE
                    ],
                    'coo' => [
                        'prefix' => 'COO',
                        'format' => 'COO/%s/%s/%s/%04d'  // COO/YEAR/MONTH/SO/SEQUENCE
                    ],
                    'insurance' => [
                        'prefix' => 'INS',
                        'format' => 'INS/%s/%s/%s/%04d'  // INS/YEAR/MONTH/SO/SEQUENCE
                    ]
                ];

                // Get document type configuration
                $docConfig = $docTypePrefixes[$docType] ?? [
                    'prefix' => 'DOC',
                    'format' => 'DOC/%s/%s/%s/%04d'
                ];

                // Create directory path for the month
                $uploadPath = WRITEPATH . 'uploads/documents/' . $year . '/' . $month . '/' . $salesOrder;
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Get existing files to determine sequence number
                $files = glob($uploadPath . '/' . $docConfig['prefix'] . '*');
                $sequence = count($files) + 1;

                // Generate document number
                $docNumber = sprintf(
                    $docConfig['format'],
                    $year,
                    $month,
                    $salesOrder,
                    $sequence
                );

                // Generate filename with document number
                $newName = sprintf(
                    '%s_%s.%s',
                    $docConfig['prefix'],
                    date('YmdHis'),
                    $ext
                );

                // Move file to directory
                $file->move($uploadPath, $newName);

                // Store document information (you might want to save this in a database)
                $documentInfo = [
                    'doc_number' => $docNumber,
                    'doc_type' => $docType,
                    'sales_order' => $salesOrder,
                    'file_name' => $newName,
                    'file_path' => 'uploads/documents/' . $year . '/' . $month . '/' . $salesOrder . '/' . $newName,
                    'uploaded_at' => date('Y-m-d H:i:s')
                ];

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'File uploaded successfully',
                    'doc_number' => $docNumber,
                    'file_path' => $documentInfo['file_path']
                ]);
            } catch (\Exception $e) {
                log_message('error', 'File upload error: ' . $e->getMessage());
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Error processing file upload: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'File upload failed: ' . ($file ? $file->getErrorString() : 'No file uploaded')
        ]);
    }
}
