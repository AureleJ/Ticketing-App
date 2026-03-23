<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'type'         => 'admin',
            'firstname'    => 'Admin',
            'lastname'     => 'Admin',
            'username'     => 'adminuser',
            'email'        => 'admin@example.com',
            'role'         => 'Director',
            'avatar_color' => 'purple',
            'client_id'    => null,
        ]);

        for ($i = 0; $i < 5; $i++) {
            User::factory()->create([
                'type'         => 'member',
                'firstname'    => fake()->firstName(),
                'lastname'     => fake()->lastName(),
                'username'     => fake()->unique()->userName(),
                'email'        => fake()->unique()->safeEmail(),
                'role'         => fake()->randomElement(['Director', 'Manager', 'Developer', 'Tester']),
                'avatar_color' => fake()->randomElement(['blue', 'green', 'red', 'yellow', 'purple', 'orange']),
                'client_id'    => null,  
            ]);
        }
    }
}