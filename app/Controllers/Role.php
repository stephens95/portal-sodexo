<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Role extends BaseController
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
