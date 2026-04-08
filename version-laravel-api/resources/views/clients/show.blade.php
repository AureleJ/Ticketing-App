@extends('layouts.app')

@section('title', $client->company . ' | Clients')

@if ($canManage)
    @section('popups')
        <div class="popup-overlay hidden" id="edit-client-popup">
            <div class="glass-panel popup-card">
                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Modifier le client</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('edit-client-popup')">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>
                <form id="client-form" method="POST" action="{{ route('clients.update', $client->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="popup-body">
                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-building"></i> Entreprise
                            </div>
                            <input type="text" name="company" value="{{ $client->company }}" placeholder="Entreprise" required>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-envelope"></i> Mail
                            </div>
                            <input type="email" name="email" value="{{ $client->email }}" placeholder="Mail" required>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-user"></i> Contact
                            </div>
                            <input type="text" name="name" value="{{ $client->name }}" placeholder="Contact" required>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-phone"></i> Téléphone
                            </div>
                            <input type="text" name="phone" value="{{ $client->phone }}" placeholder="Téléphone">
                        </div>

                        <div class="input-group">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-info"></i> Statut
                            </div>
                            <select name="status">
                                @foreach (['active' => 'Actif', 'inactive' => 'Inactif', 'prospect' => 'Prospect'] as $val => $label)
                                    <option value="{{ $val }}" {{ $client->status === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('edit-client-popup')">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
@endif

@section('content')
    <div class="flex-between top-bar glass-panel animate-item">
        <a class="btn btn-secondary no-border" href="{{ route('clients.index') }}">
            <i class="ph-bold ph-arrow-left"></i> Retour
        </a>

        @if ($canManage || $canDelete)
            <div class="flex gap-sm">
                @if ($canManage)
                    <button class="btn btn-secondary" onclick="togglePopup('edit-client-popup')">
                        <i class="ph ph-pencil-simple"></i> <span>Modifier</span>
                    </button>
                @endif

                @if ($canDelete)
                    <form method="POST" action="{{ route('clients.destroy', $client->id) }}"
                        onsubmit="return confirm('Êtes-vous sûr ? Cela supprimera tous les projets et tickets liés à ce client.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-primary btn-danger">
                            <i class="ph ph-trash"></i> <span>Supprimer</span>
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    <div class="glass-panel pannel animate-item delay-1">
        <div class="flex-between flex-wrap gap-lg">
            <div class="flex-row gap-md flex-center-y">
                <div class="user-avatar large {{ $client->avatar_color }}">{{ $client->getInitials() }}</div>
                <div>
                    <h1 class="text-xl font-bold mb-xs">{{ $client->company }}</h1>
                    <span class="badge {{ $client->status_class }}">{{ $client->status_label }}</span>
                </div>
            </div>
            <div class="flex gap-xl text-center">
                <div>
                    <div class="text-muted text-xs uppercase font-bold mb-xs">Projets</div>
                    <div class="text-xl font-bold">{{ count($clientProjects) }}</div>
                </div>
                <div style="border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">
                    <div class="text-muted text-xs uppercase font-bold mb-xs">Tickets</div>
                    <div class="text-xl font-bold">{{ count($clientTickets) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-panel pannel animate-item delay-2">
        <h3 class="text-sm font-bold uppercase text-muted mb-md">Projets associés au client</h3>
        <div class="projects-grid" id="projects-grid">
            @forelse ($clientProjects as $project)
                <a href="{{ route('projects.show', $project->id) }}" class="glass-panel project-card">
                    <div>
                        <div class="card-header">
                            <div class="badge {{ $project->status_class }}">{{ $project->status_label }}</div>
                        </div>
                        <h3 class="project-title">{{ $project->name }}</h3>
                        <div class="user-infos mb-xs">
                            <div class="user-avatar small {{ $project->client->avatar_color }}">
                                {{ $project->client->getInitials() }}
                            </div>
                            <span class="text-sm text-muted">{{ $project->client->company }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex-between text-xs text-muted mb-xs">
                            <span>Progression</span>
                            <span style="font-weight: 600;">{{ $project->progress }}%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill"
                                style="width: {{ $project->progress }}%; background: {{ $project->getProgressClassAttribute() }};"></div>
                        </div>
                        <div class="card-footer">
                            <div class="user-infos">
                                <div class="user-avatar small {{ $project->owner->avatar_color }}">
                                    {{ $project->owner->getInitials() }}
                                </div>
                                <span class="text-xs text-muted">Resp: {{ $project->owner->getFullName() }}</span>
                            </div>
                            <div class="text-xs text-muted flex-center-y gap-xs">
                                <i class="ph ph-calendar-blank"></i> {{ $project->created_at->format('M y') }}
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-muted text-sm">Aucun projet associé.</div>
            @endforelse
        </div>
    </div>

    <div class="glass-panel pannel animate-item delay-3">
        <h3 class="text-sm font-bold uppercase text-muted mb-md">Tickets associés au client</h3>
        <div class="table-container">
            <table class="table" id="ticket-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sujet</th>
                        <th>Client</th>
                        <th>Assigné à</th>
                        <th>Créé</th>
                        <th>Statut</th>
                        <th>Type</th>
                        <th>Priorité</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientTickets as $ticket)
                        @php
                            $ticketClient = $ticket->project?->client;
                            $assignee     = $ticket->assignee;
                        @endphp
                        <tr onclick="window.location='{{ route('tickets.show', $ticket->id) }}'" class="ticket-row">
                            <td class="font-mono text-muted">#{{ $ticket->id }}</td>
                            <td><div class="text-title line-text">{{ $ticket->title }}</div></td>
                            <td>
                                @if ($ticketClient)
                                    <div class="flex-center-y gap-sm">
                                        <div class="user-avatar small {{ $ticketClient->avatar_color }}">{{ $ticketClient->getInitials() }}</div>
                                        <span class="text-sm line-text">{{ $ticketClient->company }}</span>
                                    </div>
                                @else
                                    <span class="text-muted text-sm">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($assignee)
                                    <div class="flex-center-y gap-sm">
                                        <div class="user-avatar small {{ $assignee->getAvatarColor() }}">{{ $assignee->getInitials() }}</div>
                                        <span class="text-sm line-text">{{ $assignee->getFullName() }}</span>
                                    </div>
                                @else
                                    <span class="text-muted text-sm">Non assigné</span>
                                @endif
                            </td>
                            <td><span class="text-sm line-text">{{ $ticket->created_at->format('d/m/y') }}</span></td>
                            <td><span class="badge line-text {{ $ticket->status_class }}">{{ $ticket->status_label }}</span></td>
                            <td><span class="badge line-text {{ $ticket->type_class }}">{{ $ticket->type_label }}</span></td>
                            <td class="font-bold text-sm {{ $ticket->priority_class }}">{{ $ticket->priority_label }}</td>
                            <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted p-md">Aucun ticket trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection