<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageContent = file_get_contents('https://picsum.photos/300/300');
        $imageName = 'user_' . $this->faker->uuid() . '.jpg';

        Storage::disk('public')->put('user-images/' . $imageName, $imageContent);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'address' => $this->faker->address,
            'phone' => $this->faker->unique()->numerify('08##########'),
            'image' => 'user-images/' . $imageName,
            'email_verified_at' => now(),
            // 'password' => static::$password ??= Hash::make(str_repeat('1', $this->faker->numberBetween(1, 8))),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
