<?php

namespace App\Models;

use CodeIgniter\Model;

class BuyerModel extends Model
{
    protected $table            = 'buyers';
    protected $primaryKey       = 'buyer_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'buyer_id', 'buyer_name', 'country', 'country_name', 'group_name', 'created_at', 'updated_at'
    ];

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

    public function updateOrInsert($data)
    {
        $existing = $this->find($data['buyer_id']);
        
        if ($existing) {
            return $this->update($data['buyer_id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    public function syncBuyersFromAPI($apiData)
    {
        $insertedCount = 0;
        $updatedCount = 0;
        $errors = [];

        foreach ($apiData as $buyer) {
            try {
                $buyerData = [
                    'buyer_id' => $buyer['CUSTOMER'],
                    'buyer_name' => $buyer['CUSTOMER_NAME'],
                    'country' => $buyer['COUNTRY'] ?? '',
                    'country_name' => $buyer['COUNTRY_NAME'] ?? '',
                    'group_name' => 'Sodexo Global' // Default value
                ];

                $existing = $this->find($buyerData['buyer_id']);
                
                if ($existing) {
                    $this->update($buyerData['buyer_id'], $buyerData);
                    $updatedCount++;
                } else {
                    $this->insert($buyerData);
                    $insertedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Error processing buyer {$buyer['CUSTOMER']}: " . $e->getMessage();
            }
        }

        return [
            'inserted' => $insertedCount,
            'updated' => $updatedCount,
            'errors' => $errors
        ];
    }
}
