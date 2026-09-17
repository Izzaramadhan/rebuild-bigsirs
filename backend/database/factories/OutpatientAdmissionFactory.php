<?php

namespace Database\Factories;

use App\Enums\AdmissionStatus;
use App\Models\OutpatientAdmission;
use App\Models\Patient;
use App\Models\Polyclinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutpatientAdmission>
 */
class OutpatientAdmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $admissionNumbers = ['ADM-'.$this->faker->unique()->numerify('##############')];

        return [
            'admission_no' => $admissionNumbers[0],
            'registration_id' => null,
            'patient_id' => Patient::factory(),
            'polyclinic_id' => Polyclinic::factory(),
            'doctor_id' => null,
            'guarantor_id' => null,
            'admission_time' => $this->faker->dateTime(),
            'discharge_time' => null,
            'entry_mode' => $this->faker->randomElement(['manual', 'online', 'telemedicine']),
            'status' => AdmissionStatus::Waiting->value,
        ];
    }
}
