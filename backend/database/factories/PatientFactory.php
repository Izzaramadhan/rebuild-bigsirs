<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medical_record_number' => 'RM-'.$this->faker->unique()->numberBetween(1000000, 9999999),
            'nik' => $this->faker->unique()->numerify('################'),
            'full_name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'birth_date' => $this->faker->dateTimeBetween('-80 years', '-20 years')->format('Y-m-d'),
            'birth_place' => $this->faker->city(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'religion' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']),
            'blood_type' => $this->faker->randomElement(['A', 'B', 'AB', 'O', '']),
            'marital_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'default_guarantor_id' => null,
            'ihs_id' => 'ID-NIK-'.$this->faker->unique()->numerify('##############'),
        ];
    }
}
