<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DocumentationController extends BaseController
{
    public function inventory()
    {
        $data['title'] = 'Report Inventory';
        $data['segment1'] = 'Report';
        return view('api/inventory', $data);
    }
}
