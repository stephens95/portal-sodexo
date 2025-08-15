<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;
use App\Models\BuyerModel;
use App\Models\RoleModel;

class UserController extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            $userModel        = new UserModel();
            $buyerModel       = new BuyerModel();
            $roleModel        = new RoleModel();
            
            $data['title']    = 'Users Management';
            $data['segment1'] = 'Administrator';
            $data['users']    = $userModel->getUser();
            $data['buyers']   = $buyerModel->findAll();
            $data['roles']    = $roleModel->findAll();
            
            return view('users/index', $data);
        }
        return view('auth/login');
    }

    public function getUserById($id)
    {
        $userModel = new UserModel();
        $buyerModel = new BuyerModel();
        $roleModel = new RoleModel();

        $user = $userModel->getUserById($id);
        $buyers = $buyerModel->findAll();
        $roles = $roleModel->findAll();

        return $this->response->setJSON([
            'user'   => $user,
            'buyers' => $buyers,
            'roles'  => $roles
        ]);
    }

    public function updateUser()
    {
        $id = $this->request->getPost('user_id');
        $buyer_ids = $this->request->getPost('buyer_ids');
        $role_ids = $this->request->getPost('role_ids');

        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $userModel = new UserModel();
        $userData = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        // Update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            if (strlen($password) < 6) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Password minimal 6 karakter'
                ]);
            }
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $userData);

        $db = \Config\Database::connect();

        // Update relasi buyers
        $db->table('user_has_buyers')->where('user_id', $id)->delete();
        if (!empty($buyer_ids)) {
            foreach ($buyer_ids as $buyer_id) {
                $db->table('user_has_buyers')->insert([
                    'user_id'  => $id,
                    'buyer_id' => $buyer_id
                ]);
            }
        }

        // Update relasi roles
        $db->table('user_has_roles')->where('user_id', $id)->delete();
        if (!empty($role_ids)) {
            foreach ($role_ids as $role_id) {
                $db->table('user_has_roles')->insert([
                    'user_id' => $id,
                    'role_id' => $role_id
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function createUser()
    {
        // Validation
        $rules = [
            'name'     => 'required|min_length[3]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $userModel = new UserModel();
        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];
        $userId = $userModel->insert($data);

        $db = \Config\Database::connect();

        // Insert relasi buyers
        $buyer_ids = $this->request->getPost('buyer_ids');
        if (!empty($buyer_ids)) {
            foreach ($buyer_ids as $buyer_id) {
                $db->table('user_has_buyers')->insert([
                    'user_id'  => $userId,
                    'buyer_id' => $buyer_id,
                ]);
            }
        }

        // Insert relasi roles
        $role_ids = $this->request->getPost('role_ids');
        if (!empty($role_ids)) {
            foreach ($role_ids as $role_id) {
                $db->table('user_has_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $role_id,
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function deleteUser($id)
    {
        $userModel = new UserModel();
        
        if ($userModel->deleteUser($id)) {
            return redirect()->to('/users')->with('success', 'User deleted successfully');
        } else {
            return redirect()->to('/users')->with('error', 'Failed to delete user');
        }
    }
}
