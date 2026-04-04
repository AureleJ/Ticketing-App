@extends('layouts.app')

@section('title', 'Tableau de bord | Ticketing')

@if (auth()->user()->type !== 'client')
    @section('popups')
        <!-- Tickets Popup -->
        <div class="popup-overlay hidden" id="ticket-popup">
            <div class="glass-panel popup-card">

                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Nouveau Ticket</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('ticket-popup')">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <form id="ticket-form" data-mode="create">
                    @csrf
                    <input type="hidden" name="back_route" value="tickets.index">

                    <div class="popup-body">
                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-text-t"></i> Sujet du ticket
                            </div>
                            <input type="text" name="title" placeholder="Sujet du ticket" required autofocus>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-folder-notch"></i> Projet
                            </div>
                            <select name="project_id" required>
                                <option value="" disabled selected>Projet</option>
                                @foreach ($allProjects as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} - {{ $p->client->company }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-user"></i> Assigné à
                            </div>
                            <select name="assigned_id">
                                <option value="" selected>Non assigné</option>
                                @foreach ($members as $m)
                                    <option value="{{ $m->id }}">{{ $m->getFullName() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-md mb-md">
                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-warning-circle"></i> Priorité
                                </div>
                                <select name="priority">
                                    <option value="low">Basse</option>
                                    <option value="medium" selected>Moyenne</option>
                                    <option value="high">Haute</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>

                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-tag"></i> Type
                                </div>
                                <select name="type">
                                    <option value="non_facturable">Non facturable</option>
                                    <option value="facturable">Facturable</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group textarea-group">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-article"></i> Description
                            </div>
                            <textarea name="description" placeholder="Description détaillée..." rows="4"></textarea>
                        </div>

                    </div>

                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('ticket-popup')">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Project Popup -->
        <div class="popup-overlay hidden" id="project-popup">
            <div class="glass-panel popup-card">

                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Nouveau projet</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('project-popup')">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <form id="project-form">
                    @csrf

                    <div class="popup-body">

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-folder"></i> Nom du projet
                            </div>
                            <input type="text" name="name" placeholder="Nom du projet" required>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-article"></i> Description
                            </div>
                            <textarea name="description" placeholder="Description courte..." style="min-height:80px"></textarea>
                        </div>

                        <div class="flex gap-md mb-md">
                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-building"></i> Client
                                </div>
                                <select name="client_id" required>
                                    <option value="" disabled selected>Client</option>
                                    @foreach ($allClients as $c)
                                        <option value="{{ $c->id }}">{{ $c->company }} | {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-user"></i> Responsable
                                </div>
                                <select name="owner_id" required>
                                    <option value="" disabled selected>Responsable</option>
                                    @foreach ($members as $m)
                                        <option value="{{ $m->id }}">
                                            {{ $m->firstname }} {{ mb_substr($m->lastname, 0, 1) }}.
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-flag"></i> Statut
                                </div>
                                <select name="status">
                                    @foreach (['planning', 'in_progress', 'on_hold', 'completed'] as $s)
                                        <option value="{{ $s }}">{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-users"></i> Équipe du projet
                            </div>
                            <div class="team-selection-grid">
                                @foreach ($members as $m)
                                    <label class="team-member-checkbox">
                                        <input type="checkbox" name="team[]" value="{{ $m->id }}">
                                        <div class="member-card glass-panel">
                                            <span class="member-name">
                                                {{ $m->firstname }} {{ mb_substr($m->lastname, 0, 1) }}.
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-md">
                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-chart-line-up"></i> Progression
                                </div>
                                <input type="number" name="progress" min="0" max="100" placeholder="Progression (%)">
                            </div>

                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-clock"></i> Budget (h)
                                </div>
                                <input type="number" name="budget_h" min="0" placeholder="Budget en heures">
                            </div>

                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-hourglass"></i> Heures consommées
                                </div>
                                <input type="number" name="total_h" min="0" placeholder="Total heures consommées">
                            </div>
                        </div>

                    </div>

                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('project-popup')">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Créer
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Client Popup -->
        <div class="popup-overlay hidden" id="client-popup">
            <div class="glass-panel popup-card">

                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Nouveau Client</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('client-popup')">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <form id="client-form">
                    @csrf
                    <div class="popup-body">
                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-building"></i> Entreprise
                            </div>
                            <input type="text" name="company" placeholder="Entreprise" required>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-envelope"></i> Mail
                            </div>
                            <input type="email" name="email" placeholder="Mail" required>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-user"></i> Contact
                            </div>
                            <input type="text" name="name" placeholder="Contact" required>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-phone"></i> Téléphone
                            </div>
                            <input type="text" name="phone" placeholder="Téléphone">
                        </div>

                        <div class="input-group">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-info"></i> Statut
                            </div>
                            <select name="status" class="form-select">
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="prospect">Prospect</option>
                            </select>
                        </div>

                    </div>

                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('client-popup')">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Créer
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endsection
@endif

@section('content')
    <div class="top-bar glass-panel animate-item">
        <h2>Tableau de bord</h2>
    </div>

    @if (auth()->user()->type !== 'client')
        <div class="flex gap-md animate-item delay-1 dashboard-stack">
            <div class="flex gap-md w-full stats-stack">
                <div class="flex-col gap-md w-full">
                    <div class="stat-item glass-panel h-full">
                        <i class="ph ph-ticket stat-icon-bg"></i>
                        <div class="stat-label">Tickets ouverts</div>
                        <div class="stat-number">{{ $openTickets->count() }}</div>
                    </div>
                    <div class="stat-item glass-panel h-full">
                        <i class="ph ph-check-circle stat-icon-bg"></i>
                        <div class="stat-label">Tickets en cours</div>
                        <div class="stat-number text-info">{{ $inProgressTickets->count() }}</div>
                    </div>
                </div>

                <div class="flex-col gap-md w-full">
                    <div class="stat-item glass-panel h-full">
                        <i class="ph ph-fire stat-icon-bg"></i>
                        <div class="stat-label">Ticket en attente</div>
                        <div class="stat-number text-warning">{{ $pendingTickets->count() }}</div>
                    </div>
                    <div class="stat-item glass-panel h-full">
                        <i class="ph ph-clock stat-icon-bg"></i>
                        <div class="stat-label">Ticket fermés</div>
                        <div class="stat-number">{{ $closedTickets->count() }}</div>
                    </div>
                </div>

                <div class="flex-col gap-md w-full">
                    <div class="stat-item glass-panel h-full">
                        <i class="ph ph-fire stat-icon-bg"></i>
                        <div class="stat-label">Urgents</div>
                        <div class="stat-number text-danger">{{ $urgentTickets->count() }}</div>
                    </div>
                    <div class="stat-item glass-panel h-full">
                        <i class="ph ph-clock stat-icon-bg"></i>
                        <div class="stat-label">Temps projets</div>
                        <div class="stat-number">{{ $totalTime }}</div>
                    </div>
                </div>
            </div>

            <div class="pannel glass-panel">
                <h3 class="text-lg mb-md">Actions Rapides</h3>
                <div class="flex-col gap-sm">
                    <button class="btn btn-secondary w-full line-text" onclick="togglePopup('ticket-popup')">
                        <i class="ph-bold ph-plus-circle text-info"></i>
                        Nouveau Ticket
                    </button>
                    <button class="btn btn-secondary w-full line-text" onclick="togglePopup('project-popup')">
                        <i class="ph-bold ph-folder-plus text-info"></i>
                        Nouveau Projet
                    </button>
                    <button class="btn btn-secondary w-full line-text" onclick="togglePopup('client-popup')">
                        <i class="ph-bold ph-user-plus text-info"></i>
                        Nouveau Client
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="pannel glass-panel animate-item delay-2">
        <div class="flex-between mb-md">
            <h3 class="text-lg">Tickets Récents</h3>
            <a href="{{ route('tickets.index') }}" class="text-muted text-sm">Voir tout</a>
        </div>
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
                    @foreach ($tickets as $ticket)
                        <tr onclick="window.location='{{ route('tickets.show', $ticket->id) }}'" class="ticket-row">
                            <td class="font-mono text-muted">#{{ $ticket->id }}</td>
                            <td>
                                <div class="text-title line-text">{{ $ticket->title }}</div>
                            </td>
                            <td>
                                @if ($ticket->project->client)
                                    <div class="flex-center-y gap-sm">
                                        <div class="user-avatar small {{ $ticket->project->client->avatar_color }}">
                                            {{ $ticket->project->client->getInitials() }}</div>
                                        <span class="text-sm line-text">{{ $ticket->project->client->company }}</span>
                                    </div>
                                @else
                                    <span class="text-muted text-sm">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($ticket->assignee)
                                    <div class="flex-center-y gap-sm">
                                        <div class="user-avatar small {{ $ticket->assignee->getAvatarColor() }}">
                                            {{ $ticket->assignee->getInitials() }}</div>
                                        <span class="text-sm line-text">{{ $ticket->assignee->getFullName() }}</span>
                                    </div>
                                @else
                                    <span class="text-muted text-sm">Non assigné</span>
                                @endif
                            </td>
                            <td><span class="text-sm line-text">{{ $ticket->created_at->format('d/m/y') }}</span></td>
                            <td><span class="badge line-text {{ $ticket->status_class }}">{{ $ticket->status_label }}</span>
                            </td>
                            <td><span class="badge line-text {{ $ticket->type_class }}">{{ $ticket->type_label }}</span></td>
                            <td class="font-bold text-sm {{ $ticket->priority_class }}">{{ $ticket->priority_label }}</td>
                            <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="pannel glass-panel animate-item delay-2">
        <div class="flex-between mb-md">
            <h3 class="text-lg">Projets Récents</h3>
            <a href="{{ route('projects.index') }}" class="text-muted text-sm">Voir tout</a>
        </div>
        <div class="table-container">
            <table class="table" id="project-table">
                <thead>
                    <tr>
                        <th>Projet</th>
                        <th>Client</th>
                        <th>Progression</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        <tr onclick="window.location='{{ route('projects.show', $project->id) }}'">
                            <td>
                                <div class="text-title line-text">{{ $project->name }}</div>
                            </td>
                            <td>
                                <div class="user-infos">
                                    <div class="user-avatar small {{ $project->client->avatar_color }}">
                                        {{ $project->client->getInitials() }}
                                    </div>
                                    <div class="user-name line-text">
                                        {{ $project->client->name }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="mt-auto">
                                    <div class="flex-between text-xs text-muted mb-sm">
                                        <span>{{ $project->progress }}%</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill"
                                            style="width: {{ $project->progress }}%; background: {{ $project->getProgressClassAttribute() }};">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right"><i class="ph-bold ph-caret-right text-muted"></i></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if (auth()->user()->type !== 'client')
        <div class="pannel glass-panel animate-item delay-2">
            <div class="flex-between mb-md">
                <h3 class="text-lg">Clients Récents</h3>
                <a href="{{ route('clients.index') }}" class="text-muted text-sm">Voir tout</a>
            </div>
            <div class="clients-grid animate-item delay-2" id="clients-grid">
                @foreach ($clients as $client)
                    <a class="glass-panel client-card" href="{{ route('clients.show', $client->id) }}">
                        <div class="client-header">
                            <div class="user-avatar large {{ $client->avatar_color }}">
                                {{ $client->getInitials() }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">{{ $client->company }}</h3>
                            </div>
                            <div class="ml-auto">
                                <span class="badge {{ $client->status_class }}">{{ $client->status_label }}</span>
                            </div>
                        </div>
                        <div class="client-body">
                            <div class="contact-row"><i class="ph-bold ph-user"></i> <span>{{ $client->name }}</span></div>
                            <div class="contact-row"><i class="ph-bold ph-envelope-simple"></i> <span>{{ $client->email }}</span></div>
                        </div>
                        <div class="client-footer">
                            <div class="client-stat"><span>{{ $client->projects->count() }}</span> Projets</div>
                            <div class="client-stat"><span>{{ $client->projects->flatMap->tickets->count() }}</span> Tickets</div>
                            <div class="btn-icon"><i class="ph-bold ph-caret-right"></i></div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection