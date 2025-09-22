<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DebitNoteController extends BaseController
{
    protected $cache;
    protected $apiUrl;

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
        $this->apiUrl = env('sap.api.fidn.url');
    }

    public function index()
    {
        $data['title'] = 'Debit Note Document';
        $data['segment1'] = 'Report';
        return view('report/fi/dn/index', $data);
    }

    private function getCacheKey()
    {
        return 'fidn_data_' . md5($this->apiUrl);
    }

    private function getFidnFilePath()
    {
        return WRITEPATH . 'cache/fidn.json';
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
                    $this->saveFidnData($data);
                } else {
                    throw new \Exception('API returned status: ' . $response->getStatusCode());
                }
            } catch (\Exception $e) {
                $data = $this->loadFidnData();
                if (!$data) {
                    throw new \Exception('API unavailable and no data found');
                }
            }
        }

        return $data;
    }

    private function saveFidnData($data)
    {
        $fidnFile = $this->getFidnFilePath();
        if (!is_dir(dirname($fidnFile))) {
            mkdir(dirname($fidnFile), 0777, true);
        }

        $fidnData = [
            'api_url' => $this->apiUrl,
            'updated_at' => date('Y-m-d H:i:s'),
            'data' => $data
        ];

        file_put_contents($fidnFile, json_encode($fidnData));
    }

    private function loadFidnData()
    {
        $fidnFile = $this->getFidnFilePath();
        if (file_exists($fidnFile)) {
            $fidnData = json_decode(file_get_contents($fidnFile), true);

            if (isset($fidnData['api_url']) && $fidnData['api_url'] === $this->apiUrl) {
                return $fidnData['data'] ?? null;
            }
        }
        return null;
    }

    public function getFidnData()
    {
        try {
            $data = $this->getData();
            $filter = $this->request->getPost('filter') ?? 'all';

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
                1 => 'DOC_DATE',
                2 => 'DOC_NUMBER',
                3 => 'CURRENCY',
                4 => 'TEXT',
                5 => 'COURIER',
                6 => 'LOCAL_CHARGE_CALC',
                7 => 'DUTY_CALC',
                8 => 'OTHERS_CALC'
            ];

            $filteredData = $data;
            if (!empty($searchValue)) {
                $filteredData = array_filter($data, function ($item) use ($searchValue) {
                    // Calculate values first for search
                    $localChargeCalc = $this->calculateLocalCharge($item);
                    $dutyCalc = $this->calculateDuty($item);
                    $othersCalc = $this->calculateOthers($item);

                    $searchFields = [
                        $item['DOC_NUMBER'] ?? '',
                        $item['DOC_DATE'] ?? '',
                        $item['CURRENCY'] ?? '',
                        $item['TEXT'] ?? '',
                        $item['COURIER'] ?? '',
                        number_format($localChargeCalc, 2),
                        number_format($dutyCalc, 2),
                        number_format($othersCalc, 2)
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
                    switch ($sortField) {
                        case 'LOCAL_CHARGE_CALC':
                            $valueA = $this->calculateLocalCharge($a);
                            $valueB = $this->calculateLocalCharge($b);
                            break;
                        case 'DUTY_CALC':
                            $valueA = $this->calculateDuty($a);
                            $valueB = $this->calculateDuty($b);
                            break;
                        case 'OTHERS_CALC':
                            $valueA = $this->calculateOthers($a);
                            $valueB = $this->calculateOthers($b);
                            break;
                        case 'COURIER':
                            $valueA = floatval($a[$sortField] ?? 0);
                            $valueB = floatval($b[$sortField] ?? 0);
                            break;
                        default:
                            $valueA = $a[$sortField] ?? '';
                            $valueB = $b[$sortField] ?? '';
                            if (!is_numeric($valueA)) {
                                $valueA = strtolower((string)$valueA);
                                $valueB = strtolower((string)$valueB);
                            } else {
                                $valueA = floatval($valueA);
                                $valueB = floatval($valueB);
                            }
                            break;
                    }

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
                    $item['DOC_DATE'] ?? '',
                    $item['DOC_NUMBER'] ?? '',
                    $item['CURRENCY'] ?? '',
                    $item['TEXT'] ?? '',
                    number_format($item['COURIER'] ?? 0, 2),
                    number_format($this->calculateLocalCharge($item), 2),
                    number_format($this->calculateDuty($item), 2),
                    number_format($this->calculateOthers($item), 2)
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

    private function calculateLocalCharge($item)
    {
        $localCharge = floatval($item['LOCAL_CHARGE'] ?? 0);
        $text = strtolower(trim($item['TEXT'] ?? ''));
        
        // Jika text dimulai dengan 'local', tambahkan freight out ke local charge
        if (strpos($text, 'local') === 0) {
            $localCharge += floatval($item['FREIGHT_OUT'] ?? 0);
        }
        
        return $localCharge;
    }

    private function calculateDuty($item)
    {
        $duty = floatval($item['DUTY'] ?? 0);
        $text = strtolower(trim($item['TEXT'] ?? ''));
        
        // Jika text dimulai dengan 'duty', tambahkan freight out ke duty
        if (strpos($text, 'duty') === 0) {
            $duty += floatval($item['FREIGHT_OUT'] ?? 0);
        }
        
        return $duty;
    }

    private function calculateOthers($item)
    {
        $sample = floatval($item['SAMPLE'] ?? 0);
        $pallet = floatval($item['PALLET'] ?? 0);
        $bankCharge = floatval($item['BANK_CHARGE'] ?? 0);
        $freightInsurance = floatval($item['FREIGHT_INSURANCE'] ?? 0);
        
        return $sample + $pallet + $bankCharge + $freightInsurance;
    }

    public function exportExcel()
    {
        try {
            $data = $this->getData();
            $filter = $this->request->getGet('filter') ?? 'all';

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getProperties()
                ->setCreator('Sodexo Portal')
                ->setTitle('Debit Note Report')
                ->setSubject('Debit Note Data Export')
                ->setDescription('Complete debit note data from Sodexo Portal');

            $headers = [
                '#',
                'Document Date',
                'Document Number',
                'Currency',
                'Text',
                'Courier',
                'Local Charge',
                'Duty',
                'Others'
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
                $sheet->setCellValue('B' . $row, $item['DOC_DATE'] ?? '');
                $sheet->setCellValue('C' . $row, $item['DOC_NUMBER'] ?? '');
                $sheet->setCellValue('D' . $row, $item['CURRENCY'] ?? '');
                $sheet->setCellValue('E' . $row, $item['TEXT'] ?? '');
                $sheet->setCellValue('F' . $row, $item['COURIER'] ?? 0);
                $sheet->setCellValue('G' . $row, $this->calculateLocalCharge($item));
                $sheet->setCellValue('H' . $row, $this->calculateDuty($item));
                $sheet->setCellValue('I' . $row, $this->calculateOthers($item));
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
            $sheet->getStyle('F2:I' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $filename = 'Debit_Note_Report_' . date('Y-m-d_H-i-s') . '.xlsx';

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

            $filename = 'Debit_Note_Report_' . date('Y-m-d_H-i-s') . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $output = fopen('php://output', 'w');

            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headers = [
                '#',
                'Document Date',
                'Document Number',
                'Currency',
                'Text',
                'Courier',
                'Local Charge',
                'Duty',
                'Others'
            ];

            fputcsv($output, $headers);

            $counter = 1;
            foreach ($data as $item) {
                $row = [
                    $counter++,
                    $item['DOC_DATE'] ?? '',
                    $item['DOC_NUMBER'] ?? '',
                    $item['CURRENCY'] ?? '',
                    $item['TEXT'] ?? '',
                    $item['COURIER'] ?? 0,
                    $this->calculateLocalCharge($item),
                    $this->calculateDuty($item),
                    $this->calculateOthers($item)
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

        $fidnFile = $this->getFidnFilePath();
        if (file_exists($fidnFile)) {
            unlink($fidnFile);
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
