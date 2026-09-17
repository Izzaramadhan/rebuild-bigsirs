<?php

namespace Database\Factories;

use App\Models\OutpatientQueue;
use App\Models\Polyclinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutpatientQueue>
 */
class OutpatientQueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admission_id' => null,
            'polyclinic_id' => Polyclinic::factory(),
            'queue_number' => $this->faker->bothify('Q-####'),
            'queue_date' => $this->faker->date(),
            'status' => 0,
        ];
    }
}
