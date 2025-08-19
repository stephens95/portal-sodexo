<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class HomeController extends BaseController
{
    public function index()
    {
        $data['title'] = 'Home';
        $data['showBreadcrumb'] = false;
        
        return view('home', $data);
    }
}
