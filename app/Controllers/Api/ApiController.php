<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Models\ApiTokenModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ApiController extends BaseController
{
    public function docApi()
    {
        $data['title'] = 'API Documentation';
        $model = new ApiTokenModel();

        // Ambil token terakhir yang masih valid
        $data['api'] = $model->findAll();
        return view('api/doc/inventory', $data);
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

    //         // Mapping antara nama parameter di URL dengan field asli dari API
    //         $fieldMap = [
    //             'FCASTQONO'     => 'FORECAST_QUOTATION',
    //             'FCASTSONO'     => 'SO_FORECAST',
    //             'ALCTDSONO'     => 'SO_ACTUAL',
    //             'CUSTNAME'      => 'CUSTOMER_NAME',
    //             'ALCTDQONO'     => 'QUOT_ACTUAL',
    //             'ALCTDCUSTPONO' => 'PO_BUYER',
    //             'STYLE'         => 'STYLE',
    //             'COLOUR'        => 'COLOR',
    //             'UNISIZE'       => 'SIZE',
    //             'QTY'           => 'QTY',
    //             'PRODYEAR'      => 'PROD_YEAR',
    //             'COUNTRY'       => 'COUNTRY_NAME',
    //         ];

    //         // Ambil semua parameter dari request
    //         $params = $this->request->getGet();

    //         // Filter data sesuai parameter
    //         $result = array_filter($result, function ($item) use ($params, $fieldMap) {
    //             foreach ($params as $paramKey => $paramValue) {
    //                 if (isset($fieldMap[$paramKey])) {
    //                     $field = $fieldMap[$paramKey];
    //                     if (!isset($item[$field]) || $item[$field] != $paramValue) {
    //                         return false;
    //                     }
    //                 }
    //             }
    //             return true;
    //         });

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

    //         return $this->response->setJSON(array_values($filtered));
    //     } catch (\Exception $e) {
    //         return $this->response->setJSON([
    //             'error' => true,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // With Login Account
    public function getInventory()
    {
        $request = service('request');

        // Ambil Authorization Header
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || strpos($authHeader, 'Basic ') !== 0) {
            return $this->response->setHeader('WWW-Authenticate', 'Basic realm="MyAPI"')->setJSON([
                'error' => true,
                'message' => 'Missing or invalid Authorization header'
            ])->setStatusCode(401);
        }

        // Decode base64 username:password
        $encoded = substr($authHeader, 6);
        $decoded = base64_decode($encoded);
        [$email, $password] = explode(':', $decoded, 2);

        // Validasi user dari tabel
        $userModel = new UserModel();

        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setHeader('WWW-Authenticate', 'Basic realm="MyAPI"')->setJSON([
                'error' => true,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        // Kalau lolos auth, lanjut ke API SAP
        $client = \Config\Services::curlrequest();
        $url = "http://10.2.38.133:8000/zapi_sth/zapi_sodexo/zstock?sap-client=888";

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $result = json_decode($response->getBody(), true);

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

            $params = $this->request->getGet();

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

    public function getSO()
    {
        $request = service('request');

        // Ambil Authorization Header
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || strpos($authHeader, 'Basic ') !== 0) {
            return $this->response->setHeader('WWW-Authenticate', 'Basic realm="MyAPI"')->setJSON([
                'error' => true,
                'message' => 'Missing or invalid Authorization header'
            ])->setStatusCode(401);
        }

        // Decode base64 username:password
        $encoded = substr($authHeader, 6);
        $decoded = base64_decode($encoded);
        [$email, $password] = explode(':', $decoded, 2);

        // Validasi user dari tabel
        $userModel = new UserModel();

        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setHeader('WWW-Authenticate', 'Basic realm="MyAPI"')->setJSON([
                'error' => true,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        // Kalau lolos auth, lanjut ke API SAP
        $client = \Config\Services::curlrequest();
        $url = "http://10.2.38.133:8000/zapi_sth/zapi_sodexo/ztrcb?sap-client=888";

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            $fieldMap = [
                'QOSSA'    => 'QO_SSA',
                'POSSA'    => 'PO_SSA',
                'POBYR'    => 'PO_BUYER',
                'ENDCST'   => 'END_CUSTOMER',
                'SO'       => 'SO',
                'BYRSTYL'  => 'BUYER_STYLE',
                'SSASTYL'  => 'SSA_STYLE',
                'COLOUR'   => 'COLOR',
                'ORDQTY'   => 'ORDER_QTY',
                'DLVNOT'   => 'DO',
                'SHPQTY'   => 'QTY_SHIP',
                'OUTPOQTY' => 'OUTS_PO_QTY',
                'INVNUMB'  => 'INV_NO',
                'INVAMNT'  => 'INV_AMOUNT',
                'INVCURR'  => 'INV_CURR',
                'DUEDATE'  => 'DUE_DATE',
                'PMT_DATE' => 'PMT_DATE',
                'BRKFEE'   => 'BRK_FEE',
                'MNGFEE'   => 'MNG_FEE',
            ];

            $params = $this->request->getGet();

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

            $filtered = array_map(function ($item) {
                return [
                    'QOSSA'      => $item['QO_SSA'] ?? null,
                    'POSSA'      => $item['PO_SSA'] ?? null,
                    'POBYR'      => $item['PO_BUYER'] ?? null,
                    'ENDCST'     => $item['END_CUSTOMER'] ?? null,
                    'SO'         => $item['SO'] ?? null,
                    'BYRSTYL'    => $item['BUYER_STYLE'] ?? null,
                    'SSASTYL'    => $item['SSA_STYLE'] ?? null,
                    'COLOUR'     => $item['COLOR'] ?? null,
                    'ORDQTY'     => $item['ORDER_QTY'] ?? null,
                    'DLVNOT'     => $item['DO'] ?? null,
                    'SHPQTY'     => $item['QTY_SHIP'] ?? null,
                    'OUTPOQTY'   => $item['OUTS_PO_QTY'] ?? null,
                    'INVNUMB'    => $item['INV_NO'] ?? null,
                    'INVAMNT'    => $item['INV_AMOUNT'] ?? null,
                    'INVCURR'    => $item['INV_CURR'] ?? null,
                    'DUEDATE'    => $item['DUE_DATE'] ?? null,
                    'PMT_DATE'   => $item['PMT_DATE'] ?? null,
                    'BRKFEE'     => $item['BRK_FEE'] ?? null,
                    'MNGFEE'     => $item['MNG_FEE'] ?? null,
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

    public function getDN()
    {
        $request = service('request');

        // Ambil Authorization Header
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || strpos($authHeader, 'Basic ') !== 0) {
            return $this->response->setHeader('WWW-Authenticate', 'Basic realm="MyAPI"')->setJSON([
                'error' => true,
                'message' => 'Missing or invalid Authorization header'
            ])->setStatusCode(401);
        }

        // Decode base64 username:password
        $encoded = substr($authHeader, 6);
        $decoded = base64_decode($encoded);
        [$email, $password] = explode(':', $decoded, 2);

        // Validasi user dari tabel
        $userModel = new UserModel();

        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setHeader('WWW-Authenticate', 'Basic realm="MyAPI"')->setJSON([
                'error' => true,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        // Kalau lolos auth, lanjut ke API SAP
        $client = \Config\Services::curlrequest();
        $url = "http://10.2.38.133:8000/zapi_sth/zapi_sodexo/zdndoc?sap-client=888";

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            $fieldMap = [
                'DOCNUM'    => 'DOC_NUMBER',
                'DOCDATE'   => 'DOC_DATE',
                'DOCYEAR'   => 'DOC_YEAR',
                'CUST'      => 'CUSTOMER',
                'CURR'      => 'CURRENCY',
                'TEXT'      => 'TEXT',
                'COURIER'   => 'COURIER',
                'LOCCHRG'   => 'LOCAL_CHARGE',
                'DUTY'      => 'DUTY',
                'SAMPLE'    => 'SAMPLE',
                'PALLET'    => 'PALLET',
                'BANKCHRG'  => 'BANK_CHARGE',
                'PPN'       => 'PPN',
                'FRGHTINS'  => 'FREIGHT_INSURANCE',
                'FRGHTOUT'  => 'FREIGHT_OUT',
                'ANOTHER'   => 'ANOTHER',
            ];

            $params = $this->request->getGet();

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

            $filtered = array_map(function ($item) {
                return [
                    'DOCNUM'    => $item['DOC_NUMBER'] ?? null,
                    'DOCDATE'   => $item['DOC_DATE'] ?? null,
                    'DOCYEAR'   => $item['DOC_YEAR'] ?? null,
                    'CUST'      => $item['CUSTOMER'] ?? null,
                    'CURR'      => $item['CURRENCY'] ?? null,
                    'TEXT'      => $item['TEXT'] ?? null,
                    'COURIER'   => $item['COURIER'] ?? null,
                    'LOCCHRG'   => $item['LOCAL_CHARGE'] ?? null,
                    'DUTY'      => $item['DUTY'] ?? null,
                    'SAMPLE'    => $item['SAMPLE'] ?? null,
                    'PALLET'    => $item['PALLET'] ?? null,
                    'BANKCHRG'  => $item['BANK_CHARGE'] ?? null,
                    'PPN'       => $item['PPN'] ?? null,
                    'FRGHTINS'  => $item['FREIGHT_INSURANCE'] ?? null,
                    'FRGHTOUT'  => $item['FREIGHT_OUT'] ?? null,
                    'ANOTHER'   => $item['ANOTHER'] ?? null,
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

    public function getCN()
    {
        $request = service('request');

        // Ambil Authorization Header
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || strpos($authHeader, 'Basic ') !== 0) {
            return $this->response->setHeader('WWW-Authenticate', 'Basic realm="MyAPI"')->setJSON([
                'error' => true,
                'message' => 'Missing or invalid Authorization header'
            ])->setStatusCode(401);
        }

        // Decode base64 username:password
        $encoded = substr($authHeader, 6);
        $decoded = base64_decode($encoded);
        [$email, $password] = explode(':', $decoded, 2);

        // Validasi user dari tabel
        $userModel = new UserModel();

        $user = $userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setHeader('WWW-Authenticate', 'Basic realm="MyAPI"')->setJSON([
                'error' => true,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        // Kalau lolos auth, lanjut ke API SAP
        $client = \Config\Services::curlrequest();
        $url = "http://10.2.38.133:8000/zapi_sth/zapi_sodexo/zcndoc?sap-client=888";

        try {
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            $fieldMap = [
                'DOCNUM'   => 'DOC_NUMBER',
                'DOCDATE'  => 'DOC_DATE',
                'CLRDATE'  => 'CLEARING_DATE',
                'DOCYEAR'  => 'DOC_YEAR',
                'VENDOR'   => 'VENDOR',
                'CURR'     => 'CURRENCY',
                'TEXT'     => 'TEXT',
                'COMM'     => 'COMMISSION',
            ];

            $params = $this->request->getGet();

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

            $filtered = array_map(function ($item) {
                return [
                    'DOCNUM'   => $item['DOC_NUMBER'] ?? null,
                    'DOCDATE'  => $item['DOC_DATE'] ?? null,
                    'CLRDATE'  => $item['CLEARING_DATE'] ?? null,
                    'DOCYEAR'  => $item['DOC_YEAR'] ?? null,
                    'VENDOR'   => $item['VENDOR'] ?? null,
                    'CURR'     => $item['CURRENCY'] ?? null,
                    'TEXT'     => $item['TEXT'] ?? null,
                    'COMM'     => $item['COMMISSION'] ?? null,
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

    // With Token
    // public function getInventory()
    // {
    //     $client = \Config\Services::curlrequest();
    //     $url = "http://10.2.38.133:8000/zapi_sth/zapi_sodexo/zstock?sap-client=888";

    //     try {
    //         $authHeader = $this->request->getHeaderLine('Authorization');

    //         if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    //             return $this->response->setJSON([
    //                 'error' => true,
    //                 'message' => 'Unauthorized: Token is missing'
    //             ])->setStatusCode(401);
    //         }

    //         $incomingToken = $matches[1];

    //         $model = new ApiTokenModel();

    //         // Ambil token terakhir yang masih valid
    //         $tokenRow = $model->where('token', $incomingToken)
    //             ->where('expired_at >=', date('Y-m-d H:i:s'))
    //             ->first();

    //         if (!$tokenRow) {
    //             return $this->response->setJSON([
    //                 'error' => true,
    //                 'message' => 'Unauthorized: Token invalid atau expired'
    //             ])->setStatusCode(401);
    //         }

    //         $response = $client->get($url, [
    //             'headers' => [
    //                 'Accept'        => 'application/json',
    //                 'Authorization' => 'Bearer ' . $tokenRow['token']
    //             ]
    //         ]);

    //         $result = json_decode($response->getBody(), true);

    //         //         // Mapping antara nama parameter di URL dengan field asli dari API
    //         $fieldMap = [
    //             'FCASTQONO'     => 'FORECAST_QUOTATION',
    //             'FCASTSONO'     => 'SO_FORECAST',
    //             'ALCTDSONO'     => 'SO_ACTUAL',
    //             'CUSTNAME'      => 'CUSTOMER_NAME',
    //             'ALCTDQONO'     => 'QUOT_ACTUAL',
    //             'ALCTDCUSTPONO' => 'PO_BUYER',
    //             'STYLE'         => 'STYLE',
    //             'COLOUR'        => 'COLOR',
    //             'UNISIZE'       => 'SIZE',
    //             'QTY'           => 'QTY',
    //             'PRODYEAR'      => 'PROD_YEAR',
    //             'COUNTRY'       => 'COUNTRY_NAME',
    //         ];

    //         // Ambil semua parameter dari request
    //         $params = $this->request->getGet();

    //         // Filter data sesuai parameter
    //         $result = array_filter($result, function ($item) use ($params, $fieldMap) {
    //             foreach ($params as $paramKey => $paramValue) {
    //                 if (isset($fieldMap[$paramKey])) {
    //                     $field = $fieldMap[$paramKey];
    //                     if (!isset($item[$field]) || $item[$field] != $paramValue) {
    //                         return false;
    //                     }
    //                 }
    //             }
    //             return true;
    //         });

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

    //         return $this->response->setJSON(array_values($filtered));
    //     } catch (\Exception $e) {
    //         return $this->response->setJSON([
    //             'error' => true,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

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
