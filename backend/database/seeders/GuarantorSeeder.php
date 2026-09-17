<?php

namespace Database\Seeders;

use App\Models\Guarantor;
use Illuminate\Database\Seeder;

class GuarantorSeeder extends Seeder
{
    public function run(): void
    {
        Guarantor::create(['code' => 'G-001', 'name' => 'Jaminan Utama', 'type' => 'UMUM', 'is_active' => true]);
        Guarantor::create(['code' => 'G-002', 'name' => 'Jaminan BPJS', 'type' => 'BPJS', 'is_active' => true]);
        Guarantor::create(['code' => 'G-003', 'name' => 'Jaminan Mandiri', 'type' => 'PRIVATE', 'is_active' => true]);
    }
}
