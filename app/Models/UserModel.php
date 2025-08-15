<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email', 'password', 'name', 'last_login', 'created_at', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getUser()
    {
        $users = $this->findAll();
        $db = \Config\Database::connect();
        
        foreach ($users as &$user) {
            // Get roles
            $roles = $db->table('user_has_roles uhr')
                       ->select('r.role_name, r.role_id')
                       ->join('roles r', 'r.role_id = uhr.role_id')
                       ->where('uhr.user_id', $user['user_id'])
                       ->get()
                       ->getResultArray();
            
            // Get buyers
            $buyers = $db->table('user_has_buyers uhb')
                        ->select('b.buyer_name, b.buyer_id, b.group_name')
                        ->join('buyers b', 'b.buyer_id = uhb.buyer_id')
                        ->where('uhb.user_id', $user['user_id'])
                        ->get()
                        ->getResultArray();
            
            // Format data
            $user['role_name'] = implode(', ', array_column($roles, 'role_name'));
            $user['role_ids'] = implode(',', array_column($roles, 'role_id'));
            $user['buyer_name'] = implode(', ', array_column($buyers, 'buyer_name'));
            $user['buyer_ids'] = implode(',', array_column($buyers, 'buyer_id'));
            $user['group_name'] = implode(', ', array_unique(array_column($buyers, 'group_name')));
        }
        
        return $users;
    }

    public function getUserWithRolesAndBuyers($email)
    {
        $user = $this->where('email', $email)->first();
        if (!$user) return null;
        
        $db = \Config\Database::connect();
        
        // Get roles
        $roles = $db->table('user_has_roles uhr')
                   ->select('r.role_name')
                   ->join('roles r', 'r.role_id = uhr.role_id')
                   ->where('uhr.user_id', $user['user_id'])
                   ->get()
                   ->getResultArray();
        
        // Get buyers
        $buyers = $db->table('user_has_buyers uhb')
                    ->select('b.buyer_name, b.group_name')
                    ->join('buyers b', 'b.buyer_id = uhb.buyer_id')
                    ->where('uhb.user_id', $user['user_id'])
                    ->get()
                    ->getResultArray();
        
        // Format data
        $user['role_name'] = implode(', ', array_column($roles, 'role_name'));
        $user['buyer_name'] = implode(', ', array_column($buyers, 'buyer_name'));
        $user['group_name'] = implode(', ', array_unique(array_column($buyers, 'group_name')));
        
        return $user;
    }

    public function getUserById($id)
    {
        $user = $this->find($id);
        if (!$user) return null;
        
        $db = \Config\Database::connect();
        
        // Get roles
        $roles = $db->table('user_has_roles uhr')
                   ->select('r.role_id')
                   ->join('roles r', 'r.role_id = uhr.role_id')
                   ->where('uhr.user_id', $user['user_id'])
                   ->get()
                   ->getResultArray();
        
        // Get buyers
        $buyers = $db->table('user_has_buyers uhb')
                    ->select('b.buyer_id')
                    ->join('buyers b', 'b.buyer_id = uhb.buyer_id')
                    ->where('uhb.user_id', $user['user_id'])
                    ->get()
                    ->getResultArray();
        
        // Format data
        $user['role_ids'] = implode(',', array_column($roles, 'role_id'));
        $user['buyer_ids'] = implode(',', array_column($buyers, 'buyer_id'));
        
        return $user;
    }

    public function updateUser($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteUser($id)
    {
        $db = \Config\Database::connect();
        
        // Delete relations first
        $db->table('user_has_roles')->where('user_id', $id)->delete();
        $db->table('user_has_buyers')->where('user_id', $id)->delete();
        
        // Delete user
        return $this->delete($id);
    }
}
