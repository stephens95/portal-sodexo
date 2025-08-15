<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\BuyerModel;

class BuyerController extends BaseController
{
    public function index()
    {
        //
    }

    public function listAll()
    {
        $buyerModel = new BuyerModel();
        $buyers = $buyerModel->select('buyer_id, buyer_name')->findAll();
        return $this->response->setJSON($buyers);
    }
}
