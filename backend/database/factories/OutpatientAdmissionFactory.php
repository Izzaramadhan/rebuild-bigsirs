<?php

namespace Database\Factories;

use App\Enums\AdmissionStatus;
use App\Models\OutpatientAdmission;
use App\Models\OutpatientRegistration;
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
        $serviceDate = $this->faker->dateTimeBetween('-1 years', 'now');

        return [
            'admission_no' => 'RJ-'.$serviceDate->format('Ymd').'-'.$this->faker->unique()->numerify('####'),
            'registration_id' => OutpatientRegistration::factory(),
            'patient_id' => Patient::factory(),
            'polyclinic_id' => Polyclinic::factory(),
            'doctor_id' => null,
            'guarantor_id' => null,
            'admission_time' => $this->faker->dateTime(),
            'discharge_time' => null,
            'service_date' => $serviceDate->format('Y-m-d'),
            'entry_mode' => $this->faker->randomElement(['manual', 'online', 'telemedicine']),
            'status' => AdmissionStatus::Waiting->value,
        ];
    }
}
