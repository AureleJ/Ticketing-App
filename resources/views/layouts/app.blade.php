<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Ticketing'))</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utils.css') }}">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @yield('head')
</head>
<body>
    <div class="app-container">
        @if (auth()->user()->type !== 'client')
            @yield('popups')

            @yield('floating')
        @endif

        @include('layouts.navigation')

        <main class="main-content">
            <div class="content-scroll">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="{{ asset('scripts/script.js') }}"></script>
    @yield('scripts')
</body>
</html>