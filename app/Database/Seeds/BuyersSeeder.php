<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Database\Seeds\UserSeeder;

class BuyersSeeder extends Seeder
{
    public function run()
    {
        // $this->db->table('buyers')->truncate();
        $data = [
            ['buyer_id' => '1000002046', 'buyer_name' => 'Sodexo Sinergi Indonesia, PT', 'group_name' => ''],
            ['buyer_id' => '2000000002', 'buyer_name' => 'Sodexo Services Asia Pte. Ltd.', 'group_name' => 'SSA'],
            ['buyer_id' => '2000000003', 'buyer_name' => 'Sodexo Singapore Pte. Ltd.', 'group_name' => ''],
            ['buyer_id' => '2000000006', 'buyer_name' => 'Safe S.R.L.', 'group_name' => ''],
            ['buyer_id' => '2000000019', 'buyer_name' => 'Sodexo Security Guard Service', 'group_name' => ''],
            ['buyer_id' => '2000000020', 'buyer_name' => 'Initial SAS EIM660', 'group_name' => ''],
            ['buyer_id' => '2000000021', 'buyer_name' => 'Sodexo Vietnam Co., Ltd.', 'group_name' => ''],
            ['buyer_id' => '2000000131', 'buyer_name' => 'Laara Venture LLC', 'group_name' => ''],
            ['buyer_id' => '2000000244', 'buyer_name' => 'Elis Services', 'group_name' => ''],
            ['buyer_id' => '2000000245', 'buyer_name' => 'Sodexo La Reunion', 'group_name' => ''],
            ['buyer_id' => '2000000271', 'buyer_name' => 'Sodexo Malaysia Sdn. Bhd.', 'group_name' => ''],
            ['buyer_id' => '2000000276', 'buyer_name' => 'Sodexo Amarit (Thailand) Limited', 'group_name' => ''],
            ['buyer_id' => '2000000306', 'buyer_name' => 'Jatu Clothing & PPE Pty Ltd.', 'group_name' => ''],
            ['buyer_id' => '2000000313', 'buyer_name' => 'Mirfil, S.L', 'group_name' => ''],
            ['buyer_id' => '2000000317', 'buyer_name' => 'CWS Supply GmbH', 'group_name' => ''],
            ['buyer_id' => '2000000325', 'buyer_name' => 'Sodexo Costa Rica S.A', 'group_name' => ''],
            ['buyer_id' => '2000000326', 'buyer_name' => 'Chef Works, Inc', 'group_name' => ''],
            ['buyer_id' => '2000000335', 'buyer_name' => 'Chef Works Canada', 'group_name' => ''],
            ['buyer_id' => '2000000370', 'buyer_name' => 'Sodexo Justice Services – FR701394', 'group_name' => ''],
            ['buyer_id' => '2000000383', 'buyer_name' => 'Direct Corporate Clothing B.V', 'group_name' => ''],
            ['buyer_id' => '2000000417', 'buyer_name' => 'Elis UK Limited', 'group_name' => ''],
            ['buyer_id' => '2000000418', 'buyer_name' => 'Sodexo Luxembourg', 'group_name' => ''],
            ['buyer_id' => '2000000424', 'buyer_name' => 'Workwear Uniform Group, Ltd.', 'group_name' => ''],
            ['buyer_id' => '2000000436', 'buyer_name' => 'Sodexo Services (Thailand) Co., Ltd', 'group_name' => ''],
            ['buyer_id' => '2000000454', 'buyer_name' => 'Sodexo Sports et Loisirs, SO575801', 'group_name' => ''],
        ];
        $this->db->table('buyers')->insertBatch($data);
    }
}
