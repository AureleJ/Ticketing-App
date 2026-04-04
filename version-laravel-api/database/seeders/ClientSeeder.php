<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['active', 'inactive', 'prospect'];
        $colors = ['blue', 'green', 'red', 'yellow', 'purple', 'orange'];

        for ($i = 0; $i < 5; $i++) {
            $client = Client::create([
                'company' => fake()->company(),
                'name' => fake()->name(),
                'email' => fake()->unique()->companyEmail(),
                'phone' => fake()->phoneNumber(),
                'status' => fake()->randomElement($statuses),
                'avatar_color' => fake()->randomElement($colors),
            ]);

            User::factory()->create([
                'type' => 'client',
                'firstname' => fake()->firstName(),
                'lastname' => fake()->lastName(),
                'username' => fake()->unique()->userName(),
                'email' => fake()->unique()->safeEmail(),
                'role' => fake()->randomElement(['Manager', 'Director']),
                'avatar_color' => fake()->randomElement($colors),
                'client_id' => $client->id,
            ]);
        }
    }
}