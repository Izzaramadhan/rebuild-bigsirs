<?php

namespace Database\Seeders;

use App\Models\Polyclinic;
use Illuminate\Database\Seeder;

class PolyclinicSeeder extends Seeder
{
    public function run(): void
    {
        Polyclinic::create(['code' => 'KL-01', 'name' => 'Poliklinik Umum', 'type' => 'rawat-jalan', 'bpjs_code' => 'BPJS-KL-001', 'is_active' => true]);
        Polyclinic::create(['code' => 'KL-02', 'name' => 'Poliklinik Spesialis', 'type' => 'rawat-jalan', 'bpjs_code' => 'BPJS-KL-002', 'is_active' => true]);
        Polyclinic::create(['code' => 'KL-03', 'name' => 'Poliklinik Gigi', 'type' => 'rawat-jalan', 'bpjs_code' => 'BPJS-KL-003', 'is_active' => true]);
    }
}
