<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

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
        $data['title'] = 'Sales Order Tracebility';
        $data['segment1'] = 'Report';
        return view('report/sd/index', $data);
    }

    private function getCacheKey()
    {
        return 'so_data_' . md5($this->apiUrl);
    }

    private function getSOFilePath()
    {
        return WRITEPATH . 'cache/so.json';
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
                    $this->saveSOData($data);
                } else {
                    throw new \Exception('API returned status: ' . $response->getStatusCode());
                }
            } catch (\Exception $e) {
                $data = $this->loadSOData();
                if (!$data) {
                    throw new \Exception('API unavailable and no inventory data found');
                }
            }
        }

        return $data;
    }

    private function saveSOData($data)
    {
        $inventoryFile = $this->getSOFilePath();
        if (!is_dir(dirname($inventoryFile))) {
            mkdir(dirname($inventoryFile), 0777, true);
        }

        $inventoryData = [
            'api_url' => $this->apiUrl,
            'updated_at' => date('Y-m-d H:i:s'),
            'data' => $data
        ];

        file_put_contents($inventoryFile, json_encode($inventoryData));
    }

    private function loadSOyData()
    {
        $inventoryFile = $this->getSOFilePath();
        if (file_exists($inventoryFile)) {
            $inventoryData = json_decode(file_get_contents($inventoryFile), true);

            // Check if the API URL matches current configuration
            if (isset($inventoryData['api_url']) && $inventoryData['api_url'] === $this->apiUrl) {
                return $inventoryData['data'] ?? null;
            }
        }
        return null;
    }

    public function getSOData()
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
                // 18 => 'DN NO',
                // 19 => 'DN AMOUNT',
                // 20 => 'DOC CURR DN',
                // 21 => 'DUE DATE',
                // 22 => 'PAYMENT RECEIVE DATE',
                // 23 => 'BROKER FEE',
                // 24 => 'MANAGEMENT FEE',
                // 25 => 'ATTACHMENT',
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
}
