<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $internalUsers = User::where('type', 'member')->get();
        $clients = Client::with('users')->get();

        if ($internalUsers->isEmpty() || $clients->isEmpty()) {
            return;
        }

        $statuses = ['planning', 'in_progress', 'on_hold', 'completed'];

        for ($i = 0; $i < 12; $i++) {
            $client = $clients->random();
            $owner = $internalUsers->random();
            $budgetHours = fake()->numberBetween(40, 300);
            $progress = fake()->numberBetween(10, 100);
            $totalHours = (int) round($budgetHours * ($progress / 100));

            $project = Project::create([
                'name' => fake()->catchPhrase(),
                'description' => fake()->text(180),
                'progress' => $progress,
                'budget_h' => $budgetHours,
                'total_h' => $totalHours,
                'status' => fake()->randomElement($statuses),
                'client_id' => $client->id,
                'owner_id' => $owner->id,
            ]);

            $teamSize = fake()->numberBetween(1, 2);
            $teamMembers = $internalUsers->random($teamSize);

            foreach ($teamMembers as $member) {
                $project->users()->syncWithoutDetaching([
                    $member->id => ['role' => 'member'],
                ]);
            }

            foreach ($client->users as $clientUser) {
                $project->users()->syncWithoutDetaching([
                    $clientUser->id => ['role' => 'client'],
                ]);
            }
        }
    }
}