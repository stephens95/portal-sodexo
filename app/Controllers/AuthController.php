<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;
use App\Models\BuyerModel;

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
        if (auth()->check()) {
            return redirect()->to('/home');
        }

        $intendedUrl = $this->request->getGet('redirect');
        if ($intendedUrl) {
            $this->session->set('intended_url', $intendedUrl);
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
            // Check if user is verified
            if (!$user['verified']) {
                return redirect()->back()->with('error', 'Your account is pending verification. Please contact administrator.');
            }

            $this->userModel->update($user['user_id'], [
                'last_login' => date('Y-m-d H:i:s')
            ]);

            $this->session->set([
                'user_id'   => $user['user_id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
                'verified'  => $user['verified'],
                'logged_in' => true,
            ]);

            if ($remember) {
                set_cookie('remember_username', $username, 604800);
                set_cookie('remember_token', hash('sha256', $user['password']), 604800);
            }

            $intendedUrl = $this->session->get('intended_url');
            if ($intendedUrl) {
                $this->session->remove('intended_url');
                return redirect()->to($intendedUrl);
            }

            return redirect()->to('/home');
        } else {
            return redirect()->back()->with('error', 'Incorrect Email or Password.');
        }
    }

    public function register()
    {
        if (auth()->check()) {
            return redirect()->to('/home');
        }

        $buyerModel = new BuyerModel();
        $data['title'] = 'Register';
        $data['buyers'] = $buyerModel->findAll();
        return view('auth/register', $data);
    }

    public function processRegister()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'buyer_ids'        => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'verified' => 0 // Default not verified
        ];

        $userId = $this->userModel->insert($userData);

        if ($userId) {
            $db = \Config\Database::connect();
            
            // Insert buyer relations
            $buyerIds = $this->request->getPost('buyer_ids');
            if (!empty($buyerIds)) {
                foreach ($buyerIds as $buyerId) {
                    $db->table('user_has_buyers')->insert([
                        'user_id'  => $userId,
                        'buyer_id' => $buyerId
                    ]);
                }
            }

            // Assign default "User" role
            $userRole = $db->table('roles')->where('role_name', 'User')->get()->getRow();
            if ($userRole) {
                $db->table('user_has_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $userRole->role_id
                ]);
            }

            return redirect()->to('/')->with('success', 'Registration successful! Please wait for admin verification before login.');
        } else {
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        }
    }

    public function forgotPassword()
    {
        if (auth()->check()) {
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
