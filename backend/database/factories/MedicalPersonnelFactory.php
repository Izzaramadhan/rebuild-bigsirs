<?php

namespace Database\Factories;

use App\Models\MedicalPersonnel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalPersonnel>
 */
class MedicalPersonnelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'str_number' => $this->faker->unique()->bothify('STR-#######'),
            'sip_number' => $this->faker->bothify('SIP-##-####'),
            'dpjp_code' => $this->faker->unique()->bothify('DPJP-#####'),
            'is_active' => true,
        ];
    }
}
