<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;
use App\Models\BuyerModel;
use App\Models\RoleModel;

class AccountController extends BaseController
{
    protected $session;
    protected $userModel;

    public function __construct()
    {
        $this->session = session();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (session()->get('logged_in')) {
            $buyerModel = new BuyerModel();
            $roleModel = new RoleModel();
            $userId = session()->get('user_id');
            
            $data['title']  = 'Account Settings';
            $data['user']   = $this->userModel->getUserById($userId);
            $data['buyers'] = $buyerModel->findAll();
            $data['roles']  = $roleModel->findAll();
            
            return view('auth/account-settings', $data);
        }

        return view('auth/login');
    }

    public function update()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $rules = [
            'email'            => 'required|valid_email',
            'password'         => 'permit_empty|min_length[6]',
            'confirm_password' => 'matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'email' => $this->request->getPost('email'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->updateUser($userId, $data);

        $db = \Config\Database::connect();

        // Update relasi buyers
        $buyer_ids = $this->request->getPost('buyer_ids');
        $db->table('user_has_buyers')->where('user_id', $userId)->delete();
        if (!empty($buyer_ids)) {
            foreach ($buyer_ids as $buyer_id) {
                $db->table('user_has_buyers')->insert([
                    'user_id'  => $userId,
                    'buyer_id' => $buyer_id
                ]);
            }
        }

        // Update relasi roles
        $role_ids = $this->request->getPost('role_ids');
        $db->table('user_has_roles')->where('user_id', $userId)->delete();
        if (!empty($role_ids)) {
            foreach ($role_ids as $role_id) {
                $db->table('user_has_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $role_id
                ]);
            }
        }

        $this->session->set('email', $data['email']);

        return redirect()->to('/account-settings')->with('success', 'Account updated successfully.');
    }
}
