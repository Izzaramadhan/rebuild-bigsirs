<?php

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Models\OutpatientRegistration;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutpatientRegistration>
 */
class OutpatientRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $registrationNumbers = ['REG-'.$this->faker->unique()->numerify('##############')];

        return [
            'registration_no' => $registrationNumbers[0],
            'patient_id' => Patient::factory(),
            'registration_date' => $this->faker->dateTime(),
            'guarantor_id' => null,
            'bpjs_number' => $this->faker->bothify('BPJS-####-####-####'),
            'channel' => $this->faker->randomElement(['offline', 'online']),
            'status' => RegistrationStatus::Registered->value,
        ];
    }
}
