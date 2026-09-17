<?php

namespace Database\Factories;

use App\Enums\GuarantorType;
use App\Models\Guarantor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guarantor>
 */
class GuarantorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('G-####'),
            'name' => $this->faker->company(),
            'type' => GuarantorType::Umum->value,
            'is_active' => true,
        ];
    }
}
