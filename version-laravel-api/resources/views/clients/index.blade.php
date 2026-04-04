@extends('layouts.app')

@section('title', 'Clients | Ticketing')

@section('popups')
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
                        <select name="status">
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

@section('floating')
    <button class="btn btn-primary btn-floating" onclick="togglePopup('client-popup')">
        <i class="ph-bold ph-plus"></i> <span>Nouveau</span>
    </button>
@endsection

@section('content')
    <div class="top-bar glass-panel animate-item">
        <h2>Clients</h2>
    </div>

    <div class="clients-grid animate-item delay-2" id="clients-grid">
        @forelse ($clients as $client)
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
        @empty
            <div class="text-muted text-center w-full" style="grid-column: 1/-1; padding: 40px;">Aucun client trouvé.</div>
        @endforelse
    </div>
@endsection