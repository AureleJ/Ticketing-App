<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;

class DashboardController extends Controller
{
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
            ->take(5)
            ->get();

        $projects = Project::with('client')
            ->when($clientId, fn($q) => $q->whereHas(
                'client',
                fn($q) => $q->where('id', $clientId)
            ))
            ->latest()
            ->take(5)
            ->get();

        $data = [
            'tickets' => $tickets,
            'projects' => $projects,
        ];

        if ($user->type !== 'client') {
            $data += [
                'clients' => Client::latest()->take(3)->get(),
                'allProjects' => Project::all(),
                'allClients' => Client::all(),
                'allUsers' => User::all(),
                'members' => User::where('type', 'member')->get(),
                'openTickets' => Ticket::where('status', 'open')->get(),
                'pendingTickets' => Ticket::where('status', 'pending')->get(),
                'inProgressTickets' => Ticket::where('status', 'in_progress')->get(),
                'closedTickets' => Ticket::where('status', 'closed')->get(),
                'urgentTickets' => Ticket::where('priority', 'high')->get(),
                'totalTime' => Project::sum('total_h') . '/' . Project::sum('budget_h') . 'h',
            ];
        }

        return view('dashboard.index', $data);
    }
}