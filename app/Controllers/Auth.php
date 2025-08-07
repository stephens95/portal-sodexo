<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
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
        // Kalau sudah login, jangan tampilkan form login lagi
        if (session()->get('logged_in')) {
            return redirect()->to('/home');
        }

        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember_me'); // cek checkbox

        // $user = $this->userModel->getUserByUsername($username);
        $user = $this->userModel->getUserWithRole($username);

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $this->session->set([
                'user_id'   => $user['id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
                'role'      => $user['role_name'],
                'logged_in' => true,
            ]);

            // Jika remember me dicentang, buat cookie login
            if ($remember) {
                // Simpan di cookie selama 7 hari (604800 detik)
                set_cookie('remember_username', $username, 604800);
                set_cookie('remember_token', hash('sha256', $user['password']), 604800);
            }

            return redirect()->to('/home');
        } else {
            return redirect()->back()->with('error', 'Incorrect Email or Password.');
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
