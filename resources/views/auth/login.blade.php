@extends('layouts.guest')

@section('title', 'Connexion | Ticketing')

@section('content')
    <main class="login-wrapper">
        <div class="login-card glass-panel animate-item">
            <div>
                <h1 class="text-logo">Ticketing.</h1>
                <p class="text-muted">Connectez-vous à votre espace.</p>
            </div>

            @if ($errors->any())
                <div class="text-danger text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group mb-md">
                    <div class="input-group-label mb-xs">
                        <i class="ph ph-user"></i> Nom d'utilisateur
                    </div>
                    <input type="text" name="username" placeholder="jean44" required autofocus>
                </div>
                <div class="input-group mb-lg">
                    <div class="input-group-label mb-xs">
                        <i class="ph ph-lock-key"></i> Mot de passe (8 caractères minimum)
                    </div>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary w-full flex-center p-md mt-md">
                    Se connecter <i class="ph-bold ph-arrow-right"></i>
                </button>
            </form>

            <div>
                <a href="{{ route('register') }}" class="text-muted text-sm">Créer un compte</a>
            </div>
        </div>
    </main>
@endsection