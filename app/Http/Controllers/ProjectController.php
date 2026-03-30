<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;
use App\Models\Client;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private function isInTeam(Project $project): bool
    {
        return $project->getTeam()->contains('id', auth()->id()) && auth()->user()->type !== 'client';
    }

    private function canManage(Project $project): bool
    {
        $user = auth()->user();
        return $user->type === 'admin' || $this->isInTeam($project);
    }

    private function canDelete(Project $project): bool
    {
        return auth()->user()->type === 'admin' || auth()->user()->id === $project->owner_id;
    }

    public function index()
    {
        $user = auth()->user();
        $clientId = $user->type === 'client' ? $user->client_id : null;

        $allProjects = Project::with(['client', 'owner'])
            ->when($clientId, fn($q) => $q->whereHas(
                'client',
                fn($q) => $q->where('id', $clientId)
            ))
            ->latest()
            ->get();

        $data = compact('allProjects');

        if (!$clientId) {
            $data += [
                'allClients' => Client::all(),
                'members' => User::where('type', 'member')->get(),
            ];
        }

        return view('projects.index', $data);
    }

    public function show($id)
    {
        $user = auth()->user();
        $project = Project::with('client')->findOrFail($id);

        if ($user->type === 'client' && $project->client_id !== $user->client_id) {
            return redirect()->route('projects.index');
        }

        $tickets = Ticket::where('project_id', $id)->with(['project.client', 'assignee'])->latest()->get();
        $budgetPercent = $project->total_h > 0 ? ($project->budget_h / $project->total_h * 100) : 0;
        $teamMembers = $project->getTeam();

        $ticketsStats = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'pending' => $tickets->where('status', 'pending')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
        ];

        $data = [
            'project' => $project,
            'tickets' => $tickets,
            'ticketsStats' => $ticketsStats,
            'budgetPercent' => $budgetPercent,
            'teamMembers' => $teamMembers,
            'canManageProject' => $this->canManage($project),
            'canDeleteProject' => $this->canDelete($project),
        ];

        if ($user->type !== 'client') {
            $data['allClients'] = Client::all();
        }

        return view('projects.show', $data);
    }

    public function store(Request $request)
    {
        if (auth()->user()->type === 'client') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:planning,in_progress,on_hold,completed'],
            'budget_h' => ['nullable', 'integer', 'min:0'],
            'total_h' => ['nullable', 'integer', 'min:0'],
            'progress' => ['nullable', 'integer', 'between:0,100'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'team' => ['nullable', 'array'],
            'team.*' => ['integer', 'exists:users,id'],
        ]);

        $project = Project::create($validated);

        if (!empty($validated['team'])) {
            $project->users()->sync($validated['team']);
        }

        $project->load(['client', 'owner']);

        return response()->json([
            'message' => 'Projet créé avec succès.',
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'progress' => $project->progress ?? 0,
                'progress_color' => $project->getProgressClassAttribute(),
                'status_label' => $project->status_label,
                'status_class' => $project->status_class,
                'show_url' => route('projects.show', $project->id),
                'created_at' => $project->created_at->format('M y'),
                'client' => $project->client ? [
                    'name' => $project->client->name,
                    'company' => $project->client->company,
                    'initials' => $project->client->getInitials(),
                    'avatar_color' => $project->client->avatar_color,
                ] : null,
                'owner' => $project->owner ? [
                    'full_name' => $project->owner->getFullName(),
                    'initials' => $project->owner->getInitials(),
                    'avatar_color' => $project->owner->avatar_color,
                ] : null,
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        if (!$this->canManage($project)) {
            return redirect()->route('projects.show', $id);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:planning,in_progress,on_hold,completed'],
            'budget_h' => ['nullable', 'integer', 'min:0'],
            'total_h' => ['nullable', 'integer', 'min:0'],
            'progress' => ['nullable', 'integer', 'between:0,100'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project->id);
    }

    public function destroy(Request $request, $id)
    {
        if (!$this->canDelete(Project::findOrFail($id))) {
            return redirect()->route('projects.show', $id);
        }

        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('projects.index');
    }
}