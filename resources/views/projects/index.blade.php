@extends('layouts.app')

@section('title', 'Projets | Ticketing')

@if (auth()->user()->type !== 'client')
    @section('popups')
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
    @endsection

    @section('floating')
        <button class="btn btn-primary btn-floating" onclick="togglePopup('project-popup')">
            <i class="ph-bold ph-plus"></i> <span>Nouveau</span>
        </button>
    @endsection
@endif

@section('content')
    <div class="top-bar glass-panel animate-item">
        <h2>Projets</h2>
    </div>

    <div class="projects-grid animate-item delay-3" id="projects-grid">
        @forelse ($allProjects as $project)
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
                        <div class="progress-fill" style="width: {{ $project->progress }}%; background: {{ $project->getProgressClassAttribute() }};"></div>
                    </div>
                    <div class="card-footer">
                        <div class="user-infos">
                            <div class="user-avatar small {{ $project->owner->avatar_color }}">
                                {{ $project->owner->getInitials() }}
                            </div>
                            <span class="text-xs text-muted">Resp: {{ $project->owner->getFullName() }}</span>
                        </div>
                        <div class="text-xs text-muted flex-center-y gap-xs">
                            <i class="ph ph-calendar-blank"></i> {{ $project->created_at->format('d M y') }}
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-muted text-center w-full" style="grid-column: 1/-1; padding: 40px;">Aucun projet trouvé.</div>
        @endforelse
    </div>
@endsection