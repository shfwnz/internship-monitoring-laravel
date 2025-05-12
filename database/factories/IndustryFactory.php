<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Industry>
 */
class IndustryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessFields = [
            'Teknologi Informasi',
            'Konstruksi',
            'Manufaktur',
            'Pendidikan',
            'Kesehatan',
            'Keuangan',
            'Retail',
            'Hospitality'
        ];

        return [
            'name' => $this->faker->company,
            'business_field' => $this->faker->randomElement($businessFields),
            'address' => $this->faker->address,
            'phone' => $this->faker->unique()->numerify('08##########'),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
