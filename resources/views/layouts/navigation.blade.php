<header class="mobile-header">
    <a href="{{ route('dashboard') }}" class="text-logo">Ticketing.</a>
    <div class="user-infos">
        <div class="user-avatar {{ auth()->user()->getAvatarColor() }}">
            {{ auth()->user()->getInitials() }}
        </div>
        <div class="user-info">
            <div class="user-name">{{ auth()->user()->getFullName() }}</div>
            <div class="user-role">{{ auth()->user()->role }}</div>
        </div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-icon"><i class="ph ph-sign-out"></i></button>
        </form>
    </div>
</header>

<nav class="sidebar glass-panel">
    <a href="{{ route('dashboard') }}" class="text-logo">Ticketing.</a>
    <ul class="nav-links">
        <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="ph ph-squares-four"></i> Tableau de bord</a></li>
        @if (auth()->user()->type !== 'client')
            <li><a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients*') ? 'active' : '' }}"><i class="ph ph-users"></i> Clients</a></li>
        @endif
        <li><a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects*') ? 'active' : '' }}"><i class="ph ph-folder-notch"></i> Projets</a></li>
        <li><a href="{{ route('tickets.index') }}" class="{{ request()->routeIs('tickets*') ? 'active' : '' }}"><i class="ph ph-ticket"></i> Tickets</a></li>
        <li><a href="{{ route('account.profile') }}" class="{{ request()->routeIs('account.profile') ? 'active' : '' }}"><i class="ph ph-user"></i> Mon Profil</a></li>
        <li><a href="{{ route('account.settings') }}" class="{{ request()->routeIs('account.settings') ? 'active' : '' }}"><i class="ph ph-gear"></i> Parametres</a></li>
    </ul>
    <div class="sidebar-footer">
        <div class="user-infos">
            <div class="user-avatar {{ auth()->user()->getAvatarColor() }}">
                {{ auth()->user()->getInitials() }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->getFullName() }}</div>
                <div class="user-role">
                    {{ auth()->user()->role }}
                    {{ auth()->user()->type === 'client' ? '(' . auth()->user()->client->company . ')' : '' }}
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-icon"><i class="ph ph-sign-out"></i></button>
        </form>
    </div>
</nav>