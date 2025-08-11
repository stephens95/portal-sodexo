<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Inventory extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {

            $data['title']    = 'Report Inventory';
            $data['segment1'] = 'Report';
            return view('report/mm/inventory', $data);
        }

        return view('auth/login');
    }
}
