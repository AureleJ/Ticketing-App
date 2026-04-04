<?php

namespace App\Http\Controllers;

use App\Models\Client;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    private function canDelete(): bool
    {
        return auth()->user()->type === 'admin';
    }

    private function canManage(): bool
    {
        return auth()->user()->type !== 'client';
    }

    public function index()
    {
        if (auth()->user()->type === 'client') {
            return redirect()->route('dashboard');
        }

        $clients = Client::all();

        return view('clients.index', [
            'clients' => $clients
        ]);
    }

    public function show($id)
    {
        if (auth()->user()->type === 'client') {
            return redirect()->route('dashboard');
        }

        $client = Client::findOrFail($id);

        return view('clients.show', [
            'client' => $client,
            'clientProjects' => $client->projects,
            'clientTickets' => $client->projects->flatMap->tickets,
            'canManage' => $this->canManage(),
            'canDelete' => $this->canDelete(),
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->canManage()) {
            return redirect()->route('clients.index');
        }

        $validated = $request->validate([
            'company' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive,prospect'],
        ]);

        $colors = ['blue', 'yellow', 'green', 'red', 'purple', 'cyan'];

        $client = Client::create([
            ...$validated,
            'avatar_color' => $colors[array_rand($colors)],
        ]);

        return response()->json([
            'message' => 'Client créé avec succès.',
            'client' => [
                'id' => $client->id,
                'company' => $client->company,
                'name' => $client->name,
                'email' => $client->email,
                'initials' => $client->getInitials(),
                'avatar_color' => $client->avatar_color,
                'status_label' => $client->status_label,
                'status_class' => $client->status_class,
                'show_url' => route('clients.show', $client->id),
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (!$this->canManage()) {
            return redirect()->route('clients.show', $id);
        }

        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'company' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive,prospect'],
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client->id);
    }

    public function destroy(Request $request, $id)
    {
        if (!$this->canDelete()) {
            return redirect()->route('clients.show', $id);
        }

        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index');
    }
}