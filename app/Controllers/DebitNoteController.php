<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DebitNoteController extends BaseController
{
    public function index()
    {
        $data['title'] = 'Debit Note Document';
        $data['segment1'] = 'Report';
        return view('report/sd/dn/index', $data);
    }
}
