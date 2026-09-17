<?php

namespace Database\Factories;

use App\Models\Polyclinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Polyclinic>
 */
class PolyclinicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('CL-###'),
            'name' => $this->faker->company().' Polyclinic',
            'type' => 'rawat-jalan',
            'bpjs_code' => $this->faker->bothify('BPJS-##-####'),
            'is_active' => true,
        ];
    }
}
