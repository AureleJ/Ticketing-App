@extends('layouts.app')

@section('title', 'Détail #' . $ticket->id . ' | Ticketing')

@if ($canManageTicket)
    @section('popups')
        <div class="popup-overlay hidden" id="ticket-popup">
            <div class="glass-panel popup-card">

                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Modifier le ticket</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('ticket-popup')">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <form id="ticket-form" data-mode="edit" data-ticket-id="{{ $ticket->id }}">
                    @csrf
                    <div class="popup-body">
                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-text-t"></i> Sujet du ticket
                            </div>
                            <input type="text" name="title" value="{{ $ticket->title }}" required>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-article"></i> Description
                            </div>
                            <textarea name="description" rows="3">{{ $ticket->description }}</textarea>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-folder-notch"></i> Projet
                            </div>
                            <select name="project_id" required>
                                @foreach ($allProjects as $p)
                                    <option value="{{ $p->id }}" {{ $p->id === $ticket->project_id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group mb-md">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-user"></i> Assigné à
                            </div>
                            <select name="assigned_id">
                                <option value="0">Non assigné</option>
                                @foreach ($members as $m)
                                    <option value="{{ $m->id }}" {{ $m->id === $ticket->assigned_id ? 'selected' : '' }}>
                                        {{ $m->getFullName() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-md mb-md">
                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-flag"></i> Statut
                                </div>
                                <select name="status">
                                    @foreach (['open', 'pending', 'in_progress', 'closed'] as $s)
                                        <option value="{{ $s }}" {{ $s === $ticket->status ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group">
                                <div class="input-group-label mb-xs">
                                    <i class="ph ph-warning-circle"></i> Priorité
                                </div>
                                <select name="priority">
                                    @foreach (['low', 'medium', 'high'] as $pr)
                                        <option value="{{ $pr }}" {{ $pr === $ticket->priority ? 'selected' : '' }}>
                                            {{ $pr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="input-group">
                            <div class="input-group-label mb-xs">
                                <i class="ph ph-tag"></i> Type
                            </div>
                            <select name="type">
                                @foreach (['facturable', 'non_facturable'] as $tp)
                                    <option value="{{ $tp }}" {{ $tp === $ticket->type ? 'selected' : '' }}>
                                        {{ $tp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="popup-footer">
                        <button type="button" class="btn btn-secondary" onclick="togglePopup('ticket-popup')">
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
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary no-border">
            <i class="ph-bold ph-arrow-left"></i>Retour
        </a>

        @if ($canManageTicket)
            <div class="flex gap-sm">
                <button class="btn btn-secondary" onclick="togglePopup('ticket-popup')">
                    <i class="ph ph-pencil-simple"></i><span>Modifier</span>
                </button>

                <form method="POST" action="{{ route('tickets.close', $ticket->id) }}"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir clôturer ce ticket ?');">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-primary">
                        <i class="ph ph-check-circle"></i> <span>Clôturer</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('tickets.destroy', $ticket->id) }}"
                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce ticket ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary btn-danger">
                        <i class="ph ph-trash"></i> <span>Supprimer</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="glass-panel pannel animate-item delay-1">
        <div class="flex-col gap-md">
            <div class="flex gap-sm">
                <span id="status-badge" class="badge {{ $ticket->status_class }}">{{ $ticket->status_label }}</span>
                <span id="label-badge" class="badge {{ $ticket->type_class }}">{{ $ticket->type_label }}</span>
                <span id="date" class="text-muted text-sm flex-center-y ml-auto">
                    {{ $date }}
                </span>
            </div>
            <h1 id="title" class="text-xl font-bold">{{ $ticket->title }}</h1>
        </div>
    </div>

    <div class="ticket-layout animate-item delay-2">
        <div class="flex-col gap-lg">
            <div class="glass-panel pannel">
                <h3 class="text-sm font-bold uppercase mb-md">Description</h3>
                <p id="description" class="text-muted" style="line-height: 1.6;">{{ $ticket->description }}</p>
            </div>
        </div>

        <div class="flex-col gap-lg">
            <div class="glass-panel pannel">
                <h3 class="text-sm font-bold text-muted uppercase mb-md">Détails</h3>
                <ul class="flex-col gap-md text-sm">
                    <li class="flex-center-y gap-xs">
                        <span class="text-muted">Client</span>
                        <div class="flex-center-y gap-xs font-bold">
                            <div id="client-avatar" class="user-avatar small {{ $client->avatar_color }}">{{ $client->getInitials() }}</div>
                            <span id="client-company">{{ $client->company }}</span>
                        </div>
                    </li>
                    <li class="flex-center-y gap-xs">
                        <span class="text-muted">Projet</span>
                        <span id="project-name">{{ $project->name }}</span>
                    </li>
                    <li class="flex-center-y gap-xs">
                        <span class="text-muted">Priorité</span>
                        <span id="priority" class="font-bold {{ $ticket->priority_class }}">{{ $ticket->priority_label }}</span>
                    </li>
                    <li class="flex-center-y gap-xs">
                        <span class="text-muted">Assigné à</span>
                        @if ($assigned)
                            <div class="flex-center-y gap-xs">
                                <div id="assigned-avatar" class="user-avatar small {{ $assigned->avatar_color }}">{{ $assigned->getInitials() }}</div>
                                <span id="assigned-name">{{ $assigned->getFullName() }}</span>
                            </div>
                        @else
                            <span class="text-muted">Non assigné</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection