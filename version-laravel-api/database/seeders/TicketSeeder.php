<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $internalUsers = User::where('type', 'member')->get();

        if ($projects->isEmpty() || $internalUsers->isEmpty()) {
            return;
        }

        $statuses = ['open', 'in_progress', 'pending', 'closed'];
        $priorities = ['low', 'medium', 'high'];
        $types = ['facturable', 'non_facturable'];

        for ($i = 0; $i < 30; $i++) {
            Ticket::create([
                'project_id' => $projects->random()->id,
                'assigned_id' => $internalUsers->random()->id,
                'title' => fake()->sentence(fake()->numberBetween(3, 6)),
                'description' => fake()->paragraphs(2, true),
                'status' => fake()->randomElement($statuses),
                'priority' => fake()->randomElement($priorities),
                'type' => fake()->randomElement($types),
            ]);
        }
    }
}