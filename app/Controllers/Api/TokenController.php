<?php

namespace App\Controllers\Api;

use App\Models\ApiTokenModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class TokenController extends BaseController
{
    public function generate()
    {
        $model = new ApiTokenModel();

        // Generate string random sebagai token
        $token = bin2hex(random_bytes(32));

        $data = [
            'token'      => $token,
            'expired_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
        ];

        // Simpan token baru
        $model->insert($data);

        return $this->response->setJSON([
            'success' => true,
            'token'   => $token,
            'expired_at' => $data['expired_at']
        ]);
    }
}
