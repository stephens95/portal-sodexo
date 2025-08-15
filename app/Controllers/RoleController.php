<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\RoleModel;

class RoleController extends BaseController
{
    public function index()
    {
        //
    }

    public function listAll()
    {
        $roleModel = new RoleModel();
        $roles = $roleModel->select('role_id, role_name')->findAll();

        return $this->response->setJSON($roles);
    }
}
