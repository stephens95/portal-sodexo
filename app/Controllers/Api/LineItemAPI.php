<?php

namespace App\Controllers\Api;

use CodeIgniter\Config\Services;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class LineItemAPI extends BaseController
{
    use ResponseTrait;

    public function getLineItems($do)
    {
        $client = \Config\Services::curlrequest();

        $url = 'http://10.2.38.133:8000/zapi_sth/zpl_slt/zreport_lineitem';
        $params = [
            'sap-client' => '888',
            'do' => $do,
        ];

        try {
            $response = $client->get($url, [
                'query' => $params,
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            // Ambil hanya isi dari "DTRESPON"
            $lineItems = [];

            if (!empty($data[0]['DTRESPON'])) {
                $lineItems = json_decode($data[0]['DTRESPON'], true);
            }

            return $this->respond($lineItems);
        } catch (\Exception $e) {
            return $this->failServerError('Gagal mengambil data: ' . $e->getMessage());
        }
    }
}
