<?php

namespace Database\Seeders;

use App\Models\MedicalPersonnel;
use Illuminate\Database\Seeder;

class MedicalPersonnelSeeder extends Seeder
{
    public function run(): void
    {
        MedicalPersonnel::create(['name' => 'Dr. Ahmad', 'str_number' => '123456789', 'sip_number' => 'SN-123456', 'dpjp_code' => 'DPJP-01', 'is_active' => true]);
        MedicalPersonnel::create(['name' => 'Dr. Budi', 'str_number' => '234567890', 'sip_number' => 'SN-234567', 'dpjp_code' => 'DPJP-02', 'is_active' => true]);
        MedicalPersonnel::create(['name' => 'Dr. Chandra', 'str_number' => '345678901', 'sip_number' => 'SN-345678', 'dpjp_code' => 'DPJP-03', 'is_active' => true]);
    }
}
