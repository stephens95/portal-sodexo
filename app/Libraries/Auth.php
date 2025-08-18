<?php

namespace App\Libraries;

use App\Models\UserModel;

class Auth
{
    protected $user = null;
    protected $roles = null;
    protected $buyers = null;

    public function __construct()
    {
        if (session()->get('logged_in')) {
            $this->loadUser();
        }
    }

    protected function loadUser()
    {
        if (!$this->user) {
            $userModel = new UserModel();
            $userId = session()->get('user_id');

            if ($userId) {
                $this->user = $userModel->find($userId);
                $this->loadRoles();
                $this->loadBuyers();
            }
        }
    }

    protected function loadRoles()
    {
        if ($this->user && !$this->roles) {
            $db = \Config\Database::connect();
            $this->roles = $db->table('user_has_roles uhr')
                             ->select('r.role_id, r.role_name')
                             ->join('roles r', 'r.role_id = uhr.role_id')
                             ->where('uhr.user_id', $this->user['user_id'])
                             ->get()
                             ->getResultArray();
        }
    }

    protected function loadBuyers()
    {
        if ($this->user && !$this->buyers) {
            $db = \Config\Database::connect();
            $this->buyers = $db->table('user_has_buyers uhb')
                              ->select('b.buyer_id, b.buyer_name, b.group_name')
                              ->join('buyers b', 'b.buyer_id = uhb.buyer_id')
                              ->where('uhb.user_id', $this->user['user_id'])
                              ->get()
                              ->getResultArray();
        }
    }

    public function check()
    {
        return session()->get('logged_in') && $this->user;
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user ? $this->user['user_id'] : null;
    }

    public function hasRole($roleName)
    {
        if (!$this->roles) return false;
        
        foreach ($this->roles as $role) {
            if ($role['role_name'] === $roleName) {
                return true;
            }
        }
        return false;
    }

    public function hasRoles($roleNames)
    {
        if (!is_array($roleNames)) {
            $roleNames = [$roleNames];
        }

        foreach ($roleNames as $roleName) {
            if ($this->hasRole($roleName)) {
                return true;
            }
        }
        return false;
    }

    public function hasRoleId($roleId)
    {
        if (!$this->roles) return false;
        
        foreach ($this->roles as $role) {
            if ($role['role_id'] == $roleId) {
                return true;
            }
        }
        return false;
    }

    public function hasBuyer($buyerId)
    {
        if (!$this->buyers) return false;
        
        foreach ($this->buyers as $buyer) {
            if ($buyer['buyer_id'] === $buyerId) {
                return true;
            }
        }
        return false;
    }

    public function hasBuyers($buyerIds)
    {
        if (!is_array(value: $buyerIds)) {
            $buyerIds = [$buyerIds];
        }

        foreach ($buyerIds as $buyerId) {
            if ($this->hasBuyer($buyerId)) {
                return true;
            }
        }
        return false;
    }

    public function roles()
    {
        return $this->roles ?: [];
    }

    public function buyers()
    {
        return $this->buyers ?: [];
    }

    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    public function isVerified()
    {
        return $this->user && $this->user['verified'] == 1;
    }

    public function roleNames()
    {
        return array_column($this->roles(), 'role_name');
    }

    public function buyerNames()
    {
        return array_column($this->buyers(), 'buyer_name');
    }
}