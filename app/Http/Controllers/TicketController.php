<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\User;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    private function canManage(Ticket $ticket): bool
    {
        return auth()->user()->type === 'admin' || (auth()->user()->type === 'member' && $ticket->assigned_id === auth()->user()->id);
    }

    public function index()
    {
        $user = auth()->user();
        $clientId = $user->type === 'client' ? $user->client_id : null;

        $tickets = Ticket::with(['project.client', 'assignee'])
            ->when($clientId, fn($q) => $q->whereHas(
                'project.client',
                fn($q) => $q->where('id', $clientId)
            ))
            ->latest()
            ->get();

        $data = compact('tickets');

        if ($user->type !== 'client') {
            $data += [
                'allProjects' => Project::all(),
                'members' => User::where('type', 'member')->get(),
            ];
        }

        return view('tickets.index', $data);
    }

    public function store(Request $request)
    {
        if (auth()->user()->type === 'client') {
            return redirect()->route('tickets.index');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['required', 'exists:projects,id'],
            'assigned_id' => ['nullable', 'exists:users,id'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'type' => ['required', 'in:facturable,non_facturable'],
            'description' => ['nullable', 'string'],
        ]);

        $ticket = Ticket::create([...$validated, 'status' => 'open']);

        $ticket->load(['project.client', 'assignee']);

        return response()->json([
            'message' => 'Ticket ajouté avec succès.',
            'ticket' => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'created_at' => $ticket->created_at,

                'status_label' => $ticket->status_label,
                'status_class' => $ticket->status_class,

                'type_label' => $ticket->type_label,
                'type_class' => $ticket->type_class,

                'priority_label' => $ticket->priority_label,
                'priority_class' => $ticket->priority_class,

                'client' => $ticket->project?->client ? [
                    'company' => $ticket->project->client->company,
                    'initials' => $ticket->project->client->getInitials(),
                    'avatar_color' => $ticket->project->client->avatar_color,
                ] : null,

                'assignee' => $ticket->assignee ? [
                    'full_name' => $ticket->assignee->getFullName(),
                    'initials' => $ticket->assignee->getInitials(),
                    'avatar_color' => $ticket->assignee->getAvatarColor(),
                ] : null,
            ],
        ], 201);
    }

    public function show($id)
    {
        $user = auth()->user();
        $ticket = Ticket::with(['project.client', 'assignee'])->findOrFail($id);

        if ($user->type === 'client' && $ticket->project->client_id !== $user->client_id) {
            return redirect()->route('tickets.index');
        }

        $data = [
            'ticket' => $ticket,
            'project' => $ticket->project,
            'client' => $ticket->project->client,
            'assigned' => $ticket->assignee,
            'canManageTicket' => $this->canManage($ticket),
        ];

        if ($user->type !== 'client') {
            $data += [
                'allProjects' => Project::all(),
                'members' => User::whereIn('type', ['admin', 'member'])->get(),
            ];
        }

        return view('tickets.show', $data);
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if (!$this->canManage($ticket)) {
            return redirect()->route('tickets.show', $id);
        }

        $validated = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'project_id' => ['required', 'exists:projects,id'],
            'assigned_id' => ['nullable', 'exists:users,id'],
            'priority' => ['required', 'in:low,medium,high'],
            'type' => ['required', 'in:facturable,non_facturable'],
            'status' => ['required', 'in:open,pending,in_progress,closed'],
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket->id);
    }

    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if (!$this->canManage($ticket)) {
            return redirect()->route('tickets.show', $id);
        }

        $ticket->delete();

        return redirect()->route('tickets.index');
    }

    public function close($id)
    {
        $ticket = Ticket::findOrFail($id);

        if (!$this->canManage($ticket)) {
            return redirect()->route('tickets.show', $id);
        }

        $ticket->update(['status' => 'closed']);

        return redirect()->route('tickets.show', $ticket->id);
    }
}