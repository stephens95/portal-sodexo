<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;
use App\Models\ProgramUpdatesModel;

class LatestVersionCell extends Cell
{
    public function version()
    {
        $model = new ProgramUpdatesModel();
        $last  = $model->orderBy('created_at', 'DESC')->first();
        $data  = [
            'latestVersion' => $last['version'] ?? 'v1.0.0'
        ];

        return view('components/version_footer', $data);
    }
}
