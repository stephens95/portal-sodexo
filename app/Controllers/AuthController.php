<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;

class AuthController extends BaseController
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
            return redirect()->to('/home');
        }

        $data['title'] = 'Login';
        return view('auth/login', $data);
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember_me');

        $user = $this->userModel->getUserWithRolesAndBuyers($username);

        if ($user && password_verify($password, $user['password'])) {
            $this->userModel->update($user['user_id'], [
                'last_login' => date('Y-m-d H:i:s')
            ]);

            $db = \Config\Database::connect();
            $roles = $db->table('user_has_roles uhr')
                       ->select('r.role_id, r.role_name')
                       ->join('roles r', 'r.role_id = uhr.role_id')
                       ->where('uhr.user_id', $user['user_id'])
                       ->get()
                       ->getResultArray();

            $this->session->set([
                'user_id'   => $user['user_id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
                'role'      => $user['role_name'],
                'role_ids'  => array_column($roles, 'role_id'),
                'buyer'     => $user['buyer_name'],
                'group'     => $user['group_name'],
                'logged_in' => true,
            ]);

            if ($remember) {
                set_cookie('remember_username', $username, 604800);
                set_cookie('remember_token', hash('sha256', $user['password']), 604800);
            }

            return redirect()->to('/home');
        } else {
            return redirect()->back()->with('error', 'Incorrect Email or Password.');
        }
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/home');
        }

        $data['title'] = 'Register';
        return view('auth/register', $data);
    }

    public function processRegister()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/')->with('success', 'Registration successful! Please login with your credentials.');
        } else {
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        }
    }

    public function forgotPassword()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/home');
        }

        $data['title'] = 'Forgot Password';
        return view('auth/forgot-password', $data);
    }

    public function processForgotPassword()
    {
        $email = $this->request->getPost('email');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $rules = [
            'email'            => 'required|valid_email',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->where('email', $email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Email not found in our records.');
        }

        $updateData = [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ];

        if ($this->userModel->update($user['user_id'], $updateData)) {
            return redirect()->to('/')->with('success', 'Password has been reset successfully! Please login with your new password.');
        } else {
            return redirect()->back()->with('error', 'Failed to reset password. Please try again.');
        }
    }

    public function logout()
    {
        $this->session->destroy();
        delete_cookie('remember_username');
        delete_cookie('remember_token');
        return redirect()->to('/');
    }
}
