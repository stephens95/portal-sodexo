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
            ['buyer_id' => '1000000038', 'buyer_name' => 'Sodexo Indonesia, PT', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '1000002046', 'buyer_name' => 'Sodexo Sinergi Indonesia, PT', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000000', 'buyer_name' => 'Kelvin Catering Services (Emirates)', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000002', 'buyer_name' => 'Sodexo Services Asia Pte. Ltd.', 'group_name' => 'SSA'],
            ['buyer_id' => '2000000003', 'buyer_name' => 'Sodexo Singapore Pte. Ltd.', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000004', 'buyer_name' => 'Sodexo On-Site Services Israel Ltd.', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000006', 'buyer_name' => 'Safe S.R.L.', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000019', 'buyer_name' => 'Sodexo Security Guard Service', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000020', 'buyer_name' => 'Initial SAS EIM660', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000021', 'buyer_name' => 'Sodexo Vietnam Co., Ltd.', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000131', 'buyer_name' => 'Laara Venture LLC', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000225', 'buyer_name' => 'Sodexo On-Site Services Philippines', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000242', 'buyer_name' => 'Teyseer Services Company W.L.L.', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000244', 'buyer_name' => 'Elis Services', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000245', 'buyer_name' => 'Sodexo La Reunion', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000254', 'buyer_name' => 'Tariq Alghanim Ltd - Kuwait', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000269', 'buyer_name' => "Sodex'Net", 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000271', 'buyer_name' => 'Sodexo Malaysia Sdn. Bhd.', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000276', 'buyer_name' => 'Sodexo Amarit (Thailand) Limited', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000280', 'buyer_name' => 'Sodexo AB', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000306', 'buyer_name' => 'Jatu Clothing & PPE Pty Ltd.', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000309', 'buyer_name' => 'Sodexo SA', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000313', 'buyer_name' => 'Mirfil, S.L', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000317', 'buyer_name' => 'CWS Supply GmbH', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000319', 'buyer_name' => 'Arndt GmbH', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000325', 'buyer_name' => 'Sodexo Costa Rica S.A', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000326', 'buyer_name' => 'Chef Works, Inc', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000335', 'buyer_name' => 'Chef Works Canada', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000341', 'buyer_name' => 'Direct Corporate Clothing Plc', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000370', 'buyer_name' => 'Sodexo Justice Services – FR701394', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000383', 'buyer_name' => 'Direct Corporate Clothing B.V', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000395', 'buyer_name' => 'Sodexo United Kingdom', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000408', 'buyer_name' => 'Sodexo Suisse SA', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000412', 'buyer_name' => 'Johnsons Workwear', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000417', 'buyer_name' => 'Elis UK Limited', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000418', 'buyer_name' => 'Sodexo Luxembourg', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000421', 'buyer_name' => 'Sodexo Operations LLC', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000424', 'buyer_name' => 'Workwear Uniform Group, Ltd.', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000427', 'buyer_name' => 'Olympique Gaulois LLP', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000434', 'buyer_name' => 'Workwear Group BV', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000436', 'buyer_name' => 'Sodexo Services (Thailand) Co., Ltd', 'group_name' => 'Sodexo Global'],
            ['buyer_id' => '2000000454', 'buyer_name' => 'Sodexo Sports et Loisirs, SO575801', 'group_name' => 'Sodexo Global'],
        ];
        $this->db->table('buyers')->insertBatch($data);
    }
}
