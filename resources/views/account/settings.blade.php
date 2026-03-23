@extends('layouts.app')

@section('title', 'Paramètres | Ticketing')

@section('content')
    <div class="flex-between top-bar glass-panel animate-item">
        <h1 class="text-xl font-bold">Paramètres</h1>
        <button class="btn btn-primary" onclick="showToast('Paramètres enregistrés !')">
            <i class="ph-bold ph-floppy-disk"></i> Enregistrer
        </button>
    </div>

    <div class="flex-col gap-lg animate-item delay-1">
        <div class="glass-panel pannel">
            <div class="flex-center-y gap-sm mb-md pb-sm">
                <h3 class="text-sm font-bold text-muted uppercase">Interface & Apparence</h3>
            </div>
            <div class="flex-col">
                <div class="setting-item">
                    <div>
                        <div class="font-bold text-sm">Mode Sombre</div>
                        <div class="text-xs text-muted">Thème sombre par défaut pour réduire la fatigue visuelle.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="setting-item">
                    <div>
                        <div class="font-bold text-sm">Animations Fluides</div>
                        <div class="text-xs text-muted">Activer les transitions et les effets de verre.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
@endsection