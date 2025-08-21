<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ApiController extends BaseController
{
    public function inventory()
    {
        $data['title'] = 'Report Inventory';
        $data['segment1'] = 'Report';
        return view('api/inventory', $data);
    }
}
