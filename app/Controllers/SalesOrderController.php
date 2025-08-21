<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SalesOrderController extends BaseController
{
    public function index()
    {
        $data['title'] = 'Tracebility Sales Order';
        $data['segment1'] = 'Report';
        return view('report/sd/index', $data);
    }
}
