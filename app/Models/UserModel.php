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

    // public function getUserByUsername($email)
    // {
    //     return $this->where('email', $email)->first();
    // }

    // public function getUserWithRole($email)
    // {
    //     return $this->select('users.*, role_name, buyers.buyer_name')
    //         ->join('roles', 'roles.rolesid = users.role_id')
    //         ->join('buyers', 'buyers.id = users.user_id')
    //         ->where('users.email', $email)
    //         ->first();
    // }

    public function getUserWithRolesAndBuyers($email)
    {
        return $this->select('users.*, roles.role_name, buyers.buyer_name, buyers.group_name')
            ->join('user_has_roles', 'user_has_roles.user_id = users.user_id', 'left')
            ->join('roles', 'roles.role_id = user_has_roles.role_id', 'left')
            ->join('user_has_buyers', 'user_has_buyers.user_id = users.user_id', 'left')
            ->join('buyers', 'buyers.buyer_id = user_has_buyers.buyer_id', 'left')
            ->where('users.email', $email)
            ->first();
    }

    public function updateUser($id, $data)
    {
        return $this->update($id, $data);
    }
}
