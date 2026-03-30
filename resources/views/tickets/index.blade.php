@extends('layouts.app')

@section('title', 'Tickets | Ticketing')

@if (auth()->user()->type !== 'client')
    @section('popups')
        <div class="popup-overlay hidden" id="ticket-popup">
            <div class="glass-panel popup-card">

                <div class="popup-header">
                    <h3 class="text-lg font-semibold">Nouveau Ticket</h3>
                    <button type="button" class="btn-icon" onclick="togglePopup('ticket-popup')">
                        <i class="ph-bold ph-x"></i>
                    </button>
                </div>

                <form id="ticket-form">
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
                                    <option value="{{ $p->id }}">{{ $p->name }} - {{ $p->client?->company }}</option>
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
    @endsection

    @section('floating')
        <button class="btn btn-primary btn-floating" onclick="togglePopup('ticket-popup')">
            <i class="ph-bold ph-plus"></i> <span>Nouveau</span>
        </button>
    @endsection
@endif

@section('content')
    <div class="top-bar glass-panel animate-item">
        <h2>Tickets</h2>
    </div>

    <div class="pannel glass-panel animate-item delay-2">
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
                        <tr onclick="window.location='{{ route('tickets.show', $ticket->id) }}'" class="ticket-row">
                            <td class="font-mono text-muted">#{{ $ticket->id }}</td>
                            <td><div class="text-title line-text">{{ $ticket->title }}</div></td>
                            <td>
                                @if ($ticket->project->client)
                                    <div class="flex-center-y gap-sm">
                                        <div class="user-avatar small {{ $ticket->project->client->avatar_color }}">{{ $ticket->project->client->getInitials() }}</div>
                                        <span class="text-sm line-text">{{ $ticket->project->client->company }}</span>
                                    </div>
                                @else
                                    <span class="text-muted text-sm">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($ticket->assignee)
                                    <div class="flex-center-y gap-sm">
                                        <div class="user-avatar small {{ $ticket->assignee->getAvatarColor() }}">{{ $ticket->assignee->getInitials() }}</div>
                                        <span class="text-sm line-text">{{ $ticket->assignee->getFullName() }}</span>
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