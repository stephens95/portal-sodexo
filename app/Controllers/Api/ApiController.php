<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ApiController extends BaseController
{
    public function getInventory()
    {
        $client = \Config\Services::curlrequest();
        $url = "http://10.2.38.133:8000/zapi_sth/zapi_sodexo/zstock?sap-client=888";

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            // Mapping antara nama parameter di URL dengan field asli dari API
            $fieldMap = [
                'FCASTQONO'     => 'FORECAST_QUOTATION',
                'FCASTSONO'     => 'SO_FORECAST',
                'ALCTDSONO'     => 'SO_ACTUAL',
                'CUSTNAME'      => 'CUSTOMER_NAME',
                'ALCTDQONO'     => 'QUOT_ACTUAL',
                'ALCTDCUSTPONO' => 'PO_BUYER',
                'STYLE'         => 'STYLE',
                'COLOUR'        => 'COLOR',
                'UNISIZE'       => 'SIZE',
                'QTY'           => 'QTY',
                'PRODYEAR'      => 'PROD_YEAR',
                'COUNTRY'       => 'COUNTRY_NAME',
            ];

            // Ambil semua parameter dari request
            $params = $this->request->getGet();

            // Filter data sesuai parameter
            $result = array_filter($result, function ($item) use ($params, $fieldMap) {
                foreach ($params as $paramKey => $paramValue) {
                    if (isset($fieldMap[$paramKey])) {
                        $field = $fieldMap[$paramKey];
                        if (!isset($item[$field]) || $item[$field] != $paramValue) {
                            return false;
                        }
                    }
                }
                return true;
            });

            // Pilih hanya field tertentu
            $filtered = array_map(function ($item) {
                return [
                    'FCASTQONO'      => $item['FORECAST_QUOTATION'] ?? null,
                    'FCASTSONO'      => $item['SO_FORECAST'] ?? null,
                    'ALCTDSONO'      => $item['SO_ACTUAL'] ?? null,
                    'CUSTNAME'       => $item['CUSTOMER_NAME'] ?? null,
                    'ALCTDQONO'      => $item['QUOT_ACTUAL'] ?? null,
                    'ALCTDCUSTPONO'  => $item['PO_BUYER'] ?? null,
                    'STYLE'          => $item['STYLE'] ?? null,
                    'COLOUR'         => $item['COLOR'] ?? null,
                    'UNISIZE'        => $item['SIZE'] ?? null,
                    'QTY'            => $item['QTY'] ?? null,
                    'PRODYEAR'       => $item['PROD_YEAR'] ?? null,
                    'AGINGDAYS'      => $this->calculateAging($item['GR_DATE']) ?? null,
                    'COUNTRY'        => $item['COUNTRY_NAME'] ?? null,
                ];
            }, $result);

            return $this->response->setJSON(array_values($filtered));
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }
    // public function getInventory()
    // {
    //     $client = \Config\Services::curlrequest();
    //     $url = "http://10.2.38.133:8000/zapi_sth/zapi_sodexo/zstock?sap-client=888";

    //     try {
    //         $response = $client->get($url, [
    //             'headers' => [
    //                 'Accept' => 'application/json'
    //             ]
    //         ]);

    //         $result = json_decode($response->getBody(), true);

    //         // Ambil parameter dari request
    //         $fcastqono = $this->request->getGet('FCASTQONO');
    //         $fcastsono = $this->request->getGet('FCASTSONO');
    //         $alctdsono = $this->request->getGet('ALCTDSONO');

    //         // Filter jika parameter ada
    //         if ($fcastqono) {
    //             $result = array_filter($result, function ($item) use ($fcastqono) {
    //                 return isset($item['FORECAST_QUOTATION']) && $item['FORECAST_QUOTATION'] == $fcastqono;
    //             });
    //         }

    //         if ($fcastsono) {
    //             $result = array_filter($result, function ($item) use ($fcastsono) {
    //                 return isset($item['SO_FORECAST']) && $item['SO_FORECAST'] == $fcastsono;
    //             });
    //         }

    //         if ($alctdsono) {
    //             $result = array_filter($result, function ($item) use ($alctdsono) {
    //                 return isset($item['ALCTDSONO']) && $item['ALCTDSONO'] == $alctdsono;
    //             });
    //         }

    //         // Pilih hanya field tertentu
    //         $filtered = array_map(function ($item) {
    //             return [
    //                 'FCASTQONO'      => $item['FORECAST_QUOTATION'] ?? null,
    //                 'FCASTSONO'      => $item['SO_FORECAST'] ?? null,
    //                 'ALCTDSONO'      => $item['SO_ACTUAL'] ?? null,
    //                 'CUSTNAME'       => $item['CUSTOMER_NAME'] ?? null,
    //                 'ALCTDQONO'      => $item['QUOT_ACTUAL'] ?? null,
    //                 'ALCTDCUSTPONO'  => $item['PO_BUYER'] ?? null,
    //                 'STYLE'          => $item['STYLE'] ?? null,
    //                 'COLOUR'         => $item['COLOR'] ?? null,
    //                 'UNISIZE'        => $item['SIZE'] ?? null,
    //                 'QTY'            => $item['QTY'] ?? null,
    //                 'PRODYEAR'       => $item['PROD_YEAR'] ?? null,
    //                 'AGINGDAYS'      => $this->calculateAging($item['GR_DATE']) ?? null,
    //                 'COUNTRY'        => $item['COUNTRY_NAME'] ?? null,
    //             ];
    //         }, $result);

    //         return $this->response->setJSON($filtered);
    //     } catch (\Exception $e) {
    //         return $this->response->setJSON([
    //             'error' => true,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    private function calculateAging($grDate)
    {
        if (empty($grDate) || strlen($grDate) !== 8) {
            return 0;
        }

        $grDateTime = \DateTime::createFromFormat('Ymd', $grDate);
        if ($grDateTime) {
            $today = new \DateTime();
            $interval = $today->diff($grDateTime);
            return $interval->days;
        }

        return 0;
    }
    // public function getInventory()
    // {
    //     $client = \Config\Services::curlrequest();

    //     // URL API SAP
    //     $url = "http://10.2.38.133:8000/zapi_sth/zapi_sodexo/zstock?sap-client=888";

    //     try {
    //         $response = $client->get($url, [
    //             'headers' => [
    //                 'Accept' => 'application/json'
    //             ]
    //         ]);

    //         // Decode hasil JSON
    //         $result = json_decode($response->getBody(), true);

    //         // Tampilkan hasil ke view atau return JSON
    //         return $this->response->setJSON($result);
    //     } catch (\Exception $e) {
    //         return $this->response->setJSON([
    //             'error' => true,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
}
