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
        $data['title'] = 'SoTracebility Report';
        $data['segment1'] = 'Report';
        return view('report/sd/index', $data);
    }

    private function getCacheKey()
    {
        return 'SoTracebility_data_' . md5($this->apiUrl);
    }

    private function getSoTracebilityFilePath()
    {
        return WRITEPATH . 'cache/SoTracebility.json';
    }

    private function getData()
    {
        $cacheKey = $this->getCacheKey();
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
                    $this->saveSoTracebilityData($data);
                } else {
                    throw new \Exception('API returned status: ' . $response->getStatusCode());
                }
            } catch (\Exception $e) {
                $data = $this->loadSoTracebilityData();
                if (!$data) {
                    throw new \Exception('API unavailable and no SoTracebility data found');
                }
            }
        }

        return $data;
    }

    private function saveSoTracebilityData($data)
    {
        $SoTracebilityFile = $this->getSoTracebilityFilePath();
        if (!is_dir(dirname($SoTracebilityFile))) {
            mkdir(dirname($SoTracebilityFile), 0777, true);
        }

        $SoTracebilityData = [
            'api_url' => $this->apiUrl,
            'updated_at' => date('Y-m-d H:i:s'),
            'data' => $data
        ];

        file_put_contents($SoTracebilityFile, json_encode($SoTracebilityData));
    }

    private function loadSoTracebilityData()
    {
        $SoTracebilityFile = $this->getSoTracebilityFilePath();
        if (file_exists($SoTracebilityFile)) {
            $SoTracebilityData = json_decode(file_get_contents($SoTracebilityFile), true);

            // Check if the API URL matches current configuration
            if (isset($SoTracebilityData['api_url']) && $SoTracebilityData['api_url'] === $this->apiUrl) {
                return $SoTracebilityData['data'] ?? null;
            }
        }
        return null;
    }


    public function getSoTracebilityData()
    {
        try {
            $data = $this->getData();

            $draw = intval($this->request->getPost('draw'));
            $start = intval($this->request->getPost('start'));
            $length = intval($this->request->getPost('length'));
            $searchValue = $this->request->getPost('search')['value'] ?? '';

            // Handle sorting
            $orderColumnIndex = intval($this->request->getPost('order')[0]['column'] ?? 0);
            $orderDirection = $this->request->getPost('order')[0]['dir'] ?? 'asc';

            // Map column index to data field
            $sortableColumns = [
                0 => null, // # column - not sortable
                1 => 'QO SSA',
                2 => 'PO SSA',
                3 => 'PO BUYER',
                4 => 'END CUSTOMER',
                5 => 'SALES ORDER (AMT)',
                6 => 'BUYER STYLE',
                7 => 'SSA STYLE',
                8 => 'COLOUR',
                9 => 'ORDER QTY',
                10 => 'QO SSA AMOUNT',
                11 => 'PO SSA AMOUNT',
                12 => 'DELIVERY NOTE',
                13 => 'SHIPMENT QTY',
                14 => 'OUTSTANDING PO QTY',
                15 => 'INVOICE QTY',
                16 => 'INVOICE AMOUNT',
                17 => 'DOC CURR INVOICE',
            ];

            $filteredData = $data;
            if (!empty($searchValue)) {
                $filteredData = array_filter($data, function ($item) use ($searchValue) {
                    $searchFields = [
                        $item['QO_SSA'] ?? '',
                        $item['PO_SSA'] ?? '',
                        $item['PO_BUYER'] ?? '',
                        $item['END_CUSTOMER'] ?? '',
                        $item['SO'] ?? '',
                        $item['BUYER_STYLE'] ?? '',
                        $item['SSA_STYLE'] ?? '',
                        $item['COLOR'] ?? '',
                        $item['ORDER_QTY'] ?? '',
                        $item['QO_SSA_AMOUNT'] ?? '',
                        $item['PO_SSA_AMOUNT'] ?? '',
                        $item['DN'] ?? '',
                        $item['QTY_SHIP'] ?? '',
                        $item['OUTS_PO_QTY'] ?? '',
                        $item['INV_NO'] ?? '',
                        $item['INV_AMOUNT'] ?? '',
                        $item['DOC_CURR_DN'] ?? '',
                    ];

                    foreach ($searchFields as $field) {
                        if (stripos($field, $searchValue) !== false) {
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

                    if ($orderDirection === 'asc') {
                        return $valueA <=> $valueB;
                    } else {
                        return $valueB <=> $valueA;
                    }
                });
            }

            $totalRecords = count($data);
            $filteredRecords = count($filteredData);

            // Reset array keys after filtering and sorting
            $filteredData = array_values($filteredData);

            $pagedData = array_slice($filteredData, $start, $length);
            $processedData = [];
            $counter = $start + 1;

            foreach ($pagedData as $item) {
                $processedData[] = [
                    $counter++,
                    $item['QO_SSA'],
                    $item['PO_SSA'],
                    $item['PO_BUYER'],
                    $item['END_CUSTOMER'],
                    $item['SO'],
                    $item['BUYER_STYLE'],
                    $item['SSA_STYLE'],
                    $item['COLOR'],
                    $item['ORDER_QTY'],
                    $item['QO_SSA_AMOUNT'],
                    $item['PO_SSA_AMOUNT'],
                    $item['DN'],
                    $item['QTY_SHIP'],
                    $item['OUTS_PO_QTY'],
                    $item['INV_NO'],
                    $item['INV_AMOUNT'],
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
            $filter = $this->request->getGet('filter') ?? 'all';

            $buyerCountries = array_map('strtoupper', array_column(auth()->buyers(), 'country') ?: []);
            $hasOtherCountry = false;
            foreach ($buyerCountries as $bc) {
                if ($bc !== 'SG' && $bc !== 'MY' && !auth()->isAdmin()) {
                    $hasOtherCountry = true;
                    break;
                }
            }

            if ($hasOtherCountry && is_array($data)) {
                $data = array_filter($data, function ($item) {
                    $country = strtoupper(trim($item['COUNTRY'] ?? ''));
                    return $country !== 'SG' && $country !== 'MY';
                });
            }

            // $data = array_filter($data, function ($item) {
            //     return !empty($item) && isset($item['PROD_YEAR']);
            // });

            // Filter berdasarkan tab
            if ($filter === 'free-stock') {
                $data = array_filter($data, function ($item) {
                    return empty($item['SO_ACTUAL']) && empty($item['QUOT_ACTUAL']) && empty($item['PO_BUYER']);
                });
            } elseif ($filter === 'stock-allocated') {
                $data = array_filter($data, function ($item) {
                    return !empty($item['SO_ACTUAL']) || !empty($item['QUOT_ACTUAL']) || !empty($item['PO_BUYER']);
                });
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getProperties()
                ->setCreator('Sodexo Portal')
                ->setTitle('SoTracebility Report')
                ->setSubject('SoTracebility Data Export')
                ->setDescription('Complete SoTracebility data from Sodexo Portal');

            $headers = [
                '#',
                'Forecast Quotation No.',
                'Forecast SO No.',
                'Allocated to SO No.',
                'Customer Name',
                'Allocated to Quotation No',
                'Allocated to Customer PO No.',
                'Style',
                'Colour',
                'Universal Size',
                'Qty (Pcs)',
                'Production Year',
                'Aging (days)',
                'Country',
            ];

            if (auth()->isAdmin()) {
                $headers = array_merge($headers, [
                    'Material Code',
                    'Special Stock',
                    'Batch',
                ]);
            }

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
                // $country = $this->getCountryByCustomer($item['CUSTOMER'] ?? '');

                $sheet->setCellValue('A' . $row, $counter++);
                $sheet->setCellValue('B' . $row, $item['FORECAST_QUOTATION'] ?? '');
                $sheet->setCellValue('C' . $row, $item['SO_FORECAST'] ?? '');
                $sheet->setCellValue('D' . $row, $item['SO_ACTUAL'] ?? '');
                $sheet->setCellValue('E' . $row, $item['CUSTOMER_NAME'] ?? '');
                $sheet->setCellValue('F' . $row, $item['QUOT_ACTUAL'] ?? '');
                $sheet->setCellValue('G' . $row, $item['PO_BUYER'] ?? '');
                $sheet->setCellValue('H' . $row, !empty($item['STYLE']) ? explode(' ', ltrim($item['STYLE']))[0] : '' ?? '');
                $sheet->setCellValue('I' . $row, $item['COLOR'] ?? '');
                $sheet->setCellValue('J' . $row, $item['SIZE'] ?? '');
                $sheet->setCellValue('K' . $row, $item['QTY'] ?? 0);
                $sheet->setCellValue('L' . $row, $item['PROD_YEAR'] ?? '');
                $sheet->setCellValue('N' . $row, $item['COUNTRY'] . '-' . $item['COUNTRY_NAME'] ?? '');
                if (auth()->isAdmin()) {
                    $sheet->setCellValue('O' . $row, $item['MATERIAL'] ?? '');
                    $sheet->setCellValue('P' . $row, $item['SO'] . '/' . $item['LINE_ITEM'] ?? '');
                    $sheet->setCellValue('Q' . $row, $item['BATCH'] ?? '');
                }
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

            $sheet->getStyle('A2:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B2:B' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H2:H' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J2:J' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $filename = 'SoTracebility_Report_' . date('Y-m-d_H-i-s') . '.xlsx';

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

            $data = array_filter($data, function ($item) {
                return !empty($item) && isset($item['PROD_YEAR']);
            });

            $filename = 'SoTracebility_Report_' . date('Y-m-d_H-i-s') . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $output = fopen('php://output', 'w');

            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

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
        $cacheKey = $this->getCacheKey();
        $this->cache->delete($cacheKey);

        $SoTracebilityFile = $this->getSoTracebilityFilePath();
        if (file_exists($SoTracebilityFile)) {
            unlink($SoTracebilityFile);
        }

        try {
            $this->getData();
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Cache refreshed successfully and new data loaded'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to refresh cache: ' . $e->getMessage()
            ]);
        }
    }
}
