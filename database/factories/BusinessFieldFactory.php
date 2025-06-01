<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessField>
 */
class BusinessFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fields = [
            'tech',
            'construction',
            'manufacturing',
            'education',
            'health',
            'finance',
            'retail',
            'hospitality',
            'agriculture',
            'real_estate',
            'transportation',
            'energy',
            'telecommunications',
            'entertainment',
            'media',
            'fashion',
            'pharmaceuticals',
            'logistics',
            'aerospace',
            'insurance',
            'legal',
            'environmental_services',
            'non_profit',
            'consulting',
            'automotive',
            'food_beverage',
            'sports',
            'ecommerce',
            'cybersecurity',
            'biotechnology',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($fields),
        ];
    }
}
