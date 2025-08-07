<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProgramUpdatesModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProgramUpdates extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            $model            = new ProgramUpdatesModel();
            $data['title']    = 'News & Updates';
            $data['segment1'] = 'Dashboard';
            $data['updates']  = $model->orderBy('created_at', 'DESC')->findAll();
            return view('new_updates', $data);
        }

        return view('auth/login');
    }
}
