<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;
use App\Models\BuyerModel;

class UserController extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            $userModel        = new UserModel();
            $data['title']    = 'Users Management';
            $data['segment1'] = 'Administrator';
            $data['users']    = $userModel->getUser();
            // $data['users']    = $userModel->getUserPaginated(10); // 10 data per halaman
            // $data['pager']    = $userModel->pager;
            return view('users/index', $data);
        }
        return view('auth/login');
    }

    public function getUserById($id)
    {
        $userModel = new UserModel();
        $buyerModel = new BuyerModel();

        // Data user
        $user = $userModel->getUser()
            ->where('users.user_id', $id)
            ->first();

        // Semua buyers untuk dropdown
        $buyers = $buyerModel->select('buyer_id, buyer_name')->findAll();

        return $this->response->setJSON([
            'user'   => $user,
            'buyers' => $buyers
        ]);

        return $this->response->setJSON($user);
    }

    public function updateUser()
    {
        $id       = $this->request->getPost('user_id');
        $buyer_id = $this->request->getPost('buyer_id');

        $userModel = new UserModel();
        $userModel->update($id, [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ]);

        // Update relasi buyer
        $db = \Config\Database::connect();
        $builder = $db->table('user_has_buyers');
        $builder->where('user_id', $id)->delete(); // hapus dulu
        if (!empty($buyer_id)) {
            $builder->insert([
                'user_id'  => $id,
                'buyer_id' => $buyer_id
            ]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }


    public function createUser()
    {
        $password = $this->request->getPost('password');
        if (strlen($password) < 6) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Password minimal 6 karakter']);
        }

        $userModel = new UserModel();
        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];
        $userId = $userModel->insert($data);

        // Insert relasi buyer
        $buyer_id = $this->request->getPost('buyer_id');
        if ($buyer_id) {
            $db = \Config\Database::connect();
            $builder = $db->table('user_has_buyers');
            $builder->insert([
                'user_id'  => $userId,
                'buyer_id' => $buyer_id,
            ]);
        }

        // Insert relasi role
        $role_id = $this->request->getPost('role_id');
        if ($role_id) {
            $db = \Config\Database::connect();
            $builderRole = $db->table('user_has_roles');
            $builderRole->insert([
                'user_id' => $userId,
                'role_id' => $role_id,
            ]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }
}
