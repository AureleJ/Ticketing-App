@extends('layouts.guest')

@section('title', 'Inscription | Ticketing')

@section('content')
    <main class="login-wrapper">
        <div class="login-card glass-panel animate-item">
            <div>
                <h1 class="text-logo">Ticketing.</h1>
                <p class="text-muted">Créez votre compte.</p>
            </div>

            @if ($errors->any())
                <div class="text-danger text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="flex gap-md mb-md">
                    <div class="input-group w-full">
                        <div class="input-group-label mb-xs">
                            <i class="ph ph-user"></i> Prénom
                        </div>
                        <input type="text" name="firstname" placeholder="Jean" required>
                    </div>
                    <div class="input-group w-full">
                        <div class="input-group-label mb-xs">
                            <i class="ph ph-user"></i> Nom
                        </div>
                        <input type="text" name="lastname" placeholder="Dupont" required>
                    </div>
                </div>
                <div class="input-group mb-md">
                    <div class="input-group-label mb-xs">
                        <i class="ph ph-user"></i> Nom d'utilisateur
                    </div>
                    <input type="text" name="username" placeholder="jean44" required>
                </div>
                <div class="input-group mb-md">
                    <div class="input-group-label mb-xs">
                        <i class="ph ph-envelope"></i> Email
                    </div>
                    <input type="email" name="email" placeholder="jean.dupont@example.com" required>
                </div>
                <div class="input-group mb-md">
                    <div class="input-group-label mb-xs">
                        <i class="ph ph-lock-key"></i> Mot de passe
                    </div>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="input-group mb-md">
                    <div class="input-group-label mb-xs">
                        <i class="ph ph-lock-key"></i> Confirmer le mot de passe
                    </div>
                    <input type="password" name="password_confirmation" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary w-full flex-center p-md mt-md">
                    Créer mon compte <i class="ph-bold ph-arrow-right"></i>
                </button>
            </form>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-muted">
                    <i class="ph-bold ph-arrow-left"></i> Déjà un compte ? Se connecter
                </a>
            </div>
        </div>
    </main>
@endsection