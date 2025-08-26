<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BuyerModel;

class BuyerController extends BaseController
{
    public function index()
    {
        $buyerModel       = new BuyerModel();

        $data['title']    = 'Buyers';
        $data['segment1'] = 'Administrator';
        $data['buyers']   = $buyerModel->select('buyer_id, buyer_name, country, country_name, group_name')->orderBy('buyer_name')->findAll();

        return view('master/buyers', $data);
    }

    public function listAll()
    {
        $buyerModel = new BuyerModel();
        $buyers = $buyerModel->select('buyer_id, buyer_name, country, country_name, group_name')->orderBy('buyer_name')->findAll();
        return $this->response->setJSON($buyers);
    }

    public function refreshFromSap()
    {
        $url = env('sap.api.buyer.url');
        if (!$url) {
            return redirect()->to('/buyers')->with('error', 'SAP buyer URL not configured');
        }

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get($url, ['timeout' => 15, 'headers' => ['Accept' => 'application/json']]);
            $body = (string) $response->getBody();
            $json = json_decode($body, true);

            if (!is_array($json)) {
                throw new \Exception('Invalid JSON response from SAP');
            }

            $buyerModel = new BuyerModel();
            $inserted = 0;
            $updated = 0;

            foreach ($json as $item) {
                $id = $item['CUSTOMER'] ?? null;
                if (!$id) continue;

                $groupName = 'Sodexo Global';
                if ((string) $id === '2000000002') {
                    $groupName = 'SSA';
                }
                $data = [
                    'buyer_id'    => $id,
                    'buyer_name'  => $item['CUSTOMER_NAME'] ?? null,
                    'country'     => $item['COUNTRY'] ?? null,
                    'country_name'=> $item['COUNTRY_NAME'] ?? null,
                    'group_name'  => $groupName,
                ];

                $exists = $buyerModel->find($id);
                if ($exists) {
                    $buyerModel->update($id, $data);
                    $updated++;
                } else {
                    $buyerModel->insert($data);
                    $inserted++;
                }
            }

            return redirect()->to('/buyers')->with('success', "Refresh complete. Inserted: {$inserted}, Updated: {$updated}");
        } catch (\Exception $e) {
            log_message('error', 'Buyer refresh error: ' . $e->getMessage());
            return redirect()->to('/buyers')->with('error', 'Refresh failed: ' . $e->getMessage());
        }
    }
}
