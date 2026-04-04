@extends('layouts.app')

@section('title', '#' . $project->id . ' | ' . $project->name)

@if ($canManageProject)
    @section('popups')
        <div class="popup-overlay hidden" id="edit-project-popup">
            <div class="glass-panel popup-card">
                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Modifier le projet</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('edit-project-popup')">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('projects.update', $project->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="popup-body">
                        <div class="input-group mb-md">
                            <i class="ph ph-folder"></i>
                            <input type="text" name="name" value="{{ $project->name }}" required>
                        </div>
                        <div class="input-group mb-md">
                            <i class="ph ph-article"></i>
                            <textarea name="description" rows="3">{{ $project->description }}</textarea>
                        </div>
                        @if (isset($allClients))
                            <div class="input-group mb-md">
                                <i class="ph ph-buildings"></i>
                                <select name="client_id" required>
                                    @foreach ($allClients as $c)
                                        <option value="{{ $c->id }}" {{ $c->id === $project->client_id ? 'selected' : '' }}>
                                            {{ $c->company }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="flex gap-md mb-md">
                            <div class="input-group w-full">
                                <i class="ph ph-flag"></i>
                                <select name="status">
                                    @foreach (['planning', 'in_progress', 'on_hold', 'completed'] as $s)
                                        <option value="{{ $s }}" {{ $s === $project->status ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-md mb-md">
                            <div class="input-group w-full">
                                <i class="ph ph-chart-line-up"></i>
                                <input type="number" name="progress" value="{{ $project->progress }}" min="0" max="100" placeholder="Avancement %">
                            </div>
                            <div class="input-group w-full">
                                <i class="ph ph-clock"></i>
                                <input type="number" name="budget_h" value="{{ $project->budget_h }}" min="0" placeholder="Budget h">
                            </div>
                            <div class="input-group w-full">
                                <i class="ph ph-hourglass"></i>
                                <input type="number" name="total_h" value="{{ $project->total_h }}" min="0" placeholder="Total h">
                            </div>
                        </div>
                    </div>
                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('edit-project-popup')">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
@endif

@section('content')
    <div class="flex-between top-bar glass-panel animate-item">
        <a href="{{ route('projects.index') }}" class="btn btn-secondary no-border">
            <i class="ph-bold ph-arrow-left"></i>Retour
        </a>

        @if ($canManageProject || $canDeleteProject)
            <div class="flex gap-sm">
                @if ($canManageProject)
                    <button class="btn btn-secondary" onclick="togglePopup('edit-project-popup')">
                        <i class="ph ph-pencil-simple"></i> <span>Modifier</span>
                    </button>
                @endif

                @if ($canDeleteProject)
                    <form method="POST" action="{{ route('projects.destroy', $project->id) }}"
                        onsubmit="return confirm('Êtes-vous sûr ? Cela supprimera tous les tickets liés au projet.');">
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

    <div class="glass-panel pannel flex-col gap-sm animate-item delay-1">
        <div class="flex-center-y gap-sm mb-xs">
            <span class="badge {{ $project->status_class }}">{{ $project->status_label }}</span>
            <span class="badge text-muted text-sm">Contrat : {{ $project->total_h }}h</span>
        </div>
        <h1 class="text-title text-2xl">{{ $project->name }}</h1>
        <div class="text-muted flex-center-y gap-sm">
            <div class="user-avatar small {{ $project->client->avatar_color }}">{{ $project->client->getInitials() }}</div>
            {{ $project->client->company }}
        </div>
    </div>

    <div class="flex gap-lg animate-item delay-2 responsive-stack">
        <div class="glass-panel pannel flex-col gap-sm flex-1">
            <h3 class="text-lg font-bold">À propos du projet</h3>
            <p class="text-muted" style="line-height: 1.6;">
                {{ !empty($project->description) ? $project->description : 'Aucune description.' }}
            </p>
        </div>

        <div class="glass-panel pannel flex-col justify-between gap-lg flex-1">
            <div class="flex-col gap-md">
                <h3 class="text-sm font-bold text-muted uppercase">Responsable du projet</h3>
                @if ($project->owner)
                    <div class="flex-center-y gap-sm mb-lg">
                        <div class="user-avatar {{ $project->owner->getAvatarColor() }}">{{ $project->owner->getInitials() }}</div>
                        <div>
                            <div class="font-bold text-sm">{{ $project->owner->getFullName() }}</div>
                            <div class="text-muted text-xs">{{ $project->owner->role }}</div>
                        </div>
                    </div>
                @else
                    <span class="text-muted text-sm mb-lg">Aucun responsable assigné.</span>
                @endif
            </div>

            <div class="flex-col gap-md">
                <h3 class="text-sm font-bold text-muted uppercase">Client</h3>
                <div class="flex-center-y gap-sm mb-lg">
                    <div class="user-avatar {{ $project->client->avatar_color }}">{{ $project->client->getInitials() }}</div>
                    <div>
                        <div class="font-bold text-sm">{{ $project->client->company }}</div>
                        <div class="text-muted text-xs">{{ $project->client->name }}</div>
                    </div>
                </div>
            </div>

            <div class="flex-col gap-md">
                <h3 class="text-sm font-bold text-muted uppercase">Équipe Projet</h3>
                <div class="flex gap-md flex-wrap">
                    @forelse ($teamMembers as $member)
                        <div class="flex-center-y gap-sm">
                            <div class="user-avatar {{ $member->avatar_color }}">{{ $member->getInitials() }}</div>
                            <div class="flex-1">
                                <div class="font-bold text-sm">{{ $member->getFullName() }}</div>
                                <div class="text-muted text-xs">{{ $member->role }}</div>
                            </div>
                        </div>
                    @empty
                        <span class="text-muted text-sm">Aucun membre assigné.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="flex-col gap-lg animate-item delay-2">
        <div class="flex-row gap-lg responsive-stack">
            <div class="glass-panel pannel flex-col item-between w-full">
                <span class="text-muted text-sm font-bold uppercase">Avancement</span>
                <div>
                    <div class="flex-between mb-xs">
                        <span class="text-2xl font-bold">{{ $project->progress }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: {{ $project->progress }}%; background: var(--accent-color);"></div>
                    </div>
                </div>
            </div>

            <div class="glass-panel pannel flex-col item-between w-full">
                <span class="text-muted text-sm font-bold uppercase">Budget Heures</span>
                <div>
                    <div class="flex-between mb-xs">
                        <span class="text-2xl font-bold text-warning">{{ $project->budget_h }}h</span>
                        <span class="text-xs text-muted">sur {{ $project->total_h }}h</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill"
                            style="width: {{ min($budgetPercent, 100) }}%; background: var(--warning-color);"></div>
                    </div>
                </div>
            </div>

            <div class="glass-panel pannel flex-col item-between w-full">
                <span class="text-muted text-sm font-bold uppercase">Tickets ({{ $ticketsStats['total'] }})</span>
                <div class="flex-between gap-lg mb-xs">
                    <div class="flex-col flex-center">
                        <div class="text-xl font-bold text-success">{{ $ticketsStats['open'] }}</div>
                        <div class="text-xs text-muted">Ouverts</div>
                    </div>
                    <div class="flex-col flex-center">
                        <div class="text-xl font-bold text-info">{{ $ticketsStats['in_progress'] }}</div>
                        <div class="text-xs text-muted">En cours</div>
                    </div>
                    <div class="flex-col flex-center">
                        <div class="text-xl font-bold text-warning">{{ $ticketsStats['pending'] }}</div>
                        <div class="text-xs text-muted">En attente</div>
                    </div>
                    <div class="flex-col flex-center">
                        <div class="text-xl font-bold">{{ $ticketsStats['closed'] }}</div>
                        <div class="text-xs text-muted">Terminé</div>
                    </div>
                </div>
                <div class="progress-track">
                    @if ($ticketsStats['total'] > 0)
                        @php $total = $ticketsStats['total']; @endphp
                        <div class="progress-fill" style="width: {{ $ticketsStats['open'] / $total * 100 }}%; background: var(--success-color);"></div>
                        <div class="progress-fill" style="width: {{ $ticketsStats['in_progress'] / $total * 100 }}%; background: var(--info-color);"></div>
                        <div class="progress-fill" style="width: {{ $ticketsStats['pending'] / $total * 100 }}%; background: var(--warning-color);"></div>
                        <div class="progress-fill" style="width: {{ $ticketsStats['closed'] / $total * 100 }}%; background: var(--text-primary);"></div>
                    @else
                        <div class="progress-fill" style="width: 0;"></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="pannel glass-panel animate-item delay-2">
            <h3 class="text-lg font-bold mb-md">Tickets liés au projet</h3>
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
                        @forelse ($tickets as $ticket)
                            @php
                                $ticketClient   = $ticket->project?->client;
                                $assignee = $ticket->assignee;
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
                                <td colspan="9" class="text-center text-muted p-md">Aucun ticket pour ce projet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection