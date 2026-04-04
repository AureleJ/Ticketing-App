<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
        $firstname = fake()->firstName();
        $lastname = fake()->lastName();

        return [
            'type' => fake()->randomElement(['Member', 'Technicien', 'Admin']),
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('123'),
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['User', 'Manager', 'Admin']),
            'status' => fake()->randomElement(['Active', 'Inactive']),
            'avatar_color' => fake()->randomElement(['blue', 'yellow', 'green', 'red', 'purple', 'cyan']),
            'client_id' => null,
            'email_verified_at' => now(),
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
