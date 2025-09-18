<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CreditNoteController extends BaseController
{
    protected $cache;
    protected $apiUrl;

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
        $this->apiUrl = env('sap.api.ficn.url');
    }

    public function index()
    {
        $data['title'] = 'Credit Note Document';
        $data['segment1'] = 'Report';
        return view('report/fi/cn/index', $data);
    }

    private function getCacheKey()
    {
        return 'ficn_data_' . md5($this->apiUrl);
    }

    private function getFicnFilePath()
    {
        return WRITEPATH . 'cache/ficn.json';
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
                    $this->saveFicnData($data);
                } else {
                    throw new \Exception('API returned status: ' . $response->getStatusCode());
                }
            } catch (\Exception $e) {
                $data = $this->loadFicnData();
                if (!$data) {
                    throw new \Exception('API unavailable and no data found');
                }
            }
        }

        return $data;
    }

    private function saveFicnData($data)
    {
        $ficnFile = $this->getFicnFilePath();
        if (!is_dir(dirname($ficnFile))) {
            mkdir(dirname($ficnFile), 0777, true);
        }

        $ficnData = [
            'api_url' => $this->apiUrl,
            'updated_at' => date('Y-m-d H:i:s'),
            'data' => $data
        ];

        file_put_contents($ficnFile, json_encode($ficnData));
    }

    private function loadFicnData()
    {
        $ficnFile = $this->getFicnFilePath();
        if (file_exists($ficnFile)) {
            $ficnData = json_decode(file_get_contents($ficnFile), true);

            if (isset($ficnData['api_url']) && $ficnData['api_url'] === $this->apiUrl) {
                return $ficnData['data'] ?? null;
            }
        }
        return null;
    }

    public function getFicnData()
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
                3 => 'CLEARING_DATE',
                4 => 'DOC_YEAR',
                5 => 'VENDOR',
                6 => 'CURRENCY',
                7 => 'COMMISSION',
                8 => 'TEXT'
            ];

            $filteredData = $data;
            if (!empty($searchValue)) {
                $filteredData = array_filter($data, function ($item) use ($searchValue) {
                    $searchFields = [
                        $item['DOC_NUMBER'] ?? '',
                        $item['DOC_DATE'] ?? '',
                        $item['CLEARING_DATE'] ?? '',
                        $item['DOC_YEAR'] ?? '',
                        $item['VENDOR'] ?? '',
                        $item['CURRENCY'] ?? '',
                        $item['COMMISSION'] ?? '',
                        $item['TEXT'] ?? ''
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
                    if (in_array($sortField, ['DOC_YEAR', 'COMMISSION'])) {
                        $valueA = floatval($valueA);
                        $valueB = floatval($valueB);
                    } else {
                        $valueA = strtolower((string)$valueA);
                        $valueB = strtolower((string)$valueB);
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
                    $item['CLEARING_DATE'] ?? '',
                    $item['DOC_YEAR'] ?? '',
                    $item['VENDOR'] ?? '',
                    $item['CURRENCY'] ?? '',
                    number_format($item['COMMISSION'] ?? 0, 2),
                    $item['TEXT'] ?? ''
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

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $spreadsheet->getProperties()
                ->setCreator('Sodexo Portal')
                ->setTitle('Credit Note Report')
                ->setSubject('Credit Note Data Export')
                ->setDescription('Complete credit note data from Sodexo Portal');

            $headers = [
                '#',
                'Document Date',
                'Document Number',
                'Clearing Date',
                'Document Year',
                'Vendor',
                'Currency',
                'Commission',
                'Text',
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
                $sheet->setCellValue('D' . $row, $item['CLEARING_DATE'] ?? '');
                $sheet->setCellValue('E' . $row, $item['DOC_YEAR'] ?? '');
                $sheet->setCellValue('F' . $row, $item['VENDOR'] ?? '');
                $sheet->setCellValue('G' . $row, $item['CURRENCY'] ?? '');
                $sheet->setCellValue('H' . $row, $item['COMMISSION'] ?? 0);
                $sheet->setCellValue('I' . $row, $item['TEXT'] ?? '');
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
            $sheet->getStyle('H2:H' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $filename = 'Credit_Note_Report_' . date('Y-m-d_H-i-s') . '.xlsx';

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

            $filename = 'Credit_Note_Report_' . date('Y-m-d_H-i-s') . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $output = fopen('php://output', 'w');

            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headers = [
                '#',
                'Document Date',
                'Document Number',
                'Clearing Date',
                'Document Year',
                'Vendor',
                'Currency',
                'Commission',
                'Text'
            ];

            fputcsv($output, $headers);

            $counter = 1;
            foreach ($data as $item) {
                $row = [
                    $counter++,
                    $item['DOC_DATE'] ?? '',
                    $item['DOC_NUMBER'] ?? '',
                    $item['CLEARING_DATE'] ?? '',
                    $item['DOC_YEAR'] ?? '',
                    $item['VENDOR'] ?? '',
                    $item['CURRENCY'] ?? '',
                    $item['COMMISSION'] ?? 0,
                    $item['TEXT'] ?? ''
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

        $ficnFile = $this->getFicnFilePath();
        if (file_exists($ficnFile)) {
            unlink($ficnFile);
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
