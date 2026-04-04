@extends('layouts.app')

@section('title', 'Mon Profil | Ticketing')

@section('content')
                <div class="glass-panel pannel animate-item">
                    <div class="flex-row gap-lg flex-center-y">
                        <div class="user-avatar large {{ auth()->user()->getAvatarColor() }}"
                            style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ auth()->user()->getInitials() }}
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">{{ auth()->user()->getFullName() }}</h1>
                            <div class="text-muted">{{ auth()->user()->role }} &bull; {{ auth()->user()->status }}</div>
                        </div>
                    </div>
                </div>

                <div class="glass-panel pannel mt-lg animate-item delay-1">
                    <h3 class="text-sm font-bold text-muted uppercase mb-md">Informations Personnelles</h3>
                    <div class="flex-col gap-md">
                        <div class="flex gap-md">
                            <div class="input-group w-full">
                                <label class="text-xs text-muted mb-xs block">Prénom</label>
                                <input type="text" value="{{ auth()->user()->firstname }}">
                            </div>
                            <div class="input-group w-full">
                                <label class="text-xs text-muted mb-xs block">Nom</label>
                                <input type="text" value="{{ auth()->user()->lastname }}">
                            </div>
                        </div>
                        <div class="input-group">
                            <label class="text-xs text-muted mb-xs block">Email professionnel</label>
                            <input type="email" value="{{ auth()->user()->email }}" disabled style="opacity: 0.5;">
                        </div>
                        <div class="input-group">
                            <label class="text-xs text-muted mb-xs block">Rôle</label>
                            <input type="text" value="{{ auth()->user()->role }}" disabled style="opacity: 0.5;">
                        </div>
                        <div class="flex justify-end mt-sm">
                            <button class="btn btn-primary" onclick="showToast('Modifications enregistrées')">Enregistrer les modifications</button>
                        </div>
                    </div>
                </div>

                <div class="glass-panel pannel mt-lg animate-item delay-2">
                    <h3 class="text-sm font-bold text-muted uppercase mb-md">Sécurité</h3>
                    <div class="flex-col gap-md">
                        <div class="input-group">
                            <label class="text-xs text-muted mb-xs block">Mot de passe actuel</label>
                            <input type="password" placeholder="***********">
                        </div>
                        <div class="flex gap-md">
                            <div class="input-group w-full">
                                <label class="text-xs text-muted mb-xs block">Nouveau mot de passe</label>
                                <input type="password">
                            </div>
                            <div class="input-group w-full">
                                <label class="text-xs text-muted mb-xs block">Confirmer</label>
                                <input type="password">
                            </div>
                        </div>
                        <div class="flex justify-end mt-sm">
                            <button class="btn btn-secondary text-danger border-danger"
                                onclick="showToast('Mot de passe modifié avec succès')">Mettre à jour le mot de
                                passe</button>
                        </div>
                    </div>
                </div>
@endsection
