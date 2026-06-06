<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CuBu' }}</title>
    <link rel="icon" href="{{ asset('images/cubu-logo.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="site-nav">
            <a href="{{ route('recipes.index') }}" class="brand" aria-label="Beranda CuBu">
                <img src="{{ asset('images/cubu-logo.svg') }}" class="brand-mark" alt="">
                <span>CuBu</span>
            </a>

            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="{{ route('recipes.index') }}" class="{{ request()->routeIs('recipes.*') && ! request()->routeIs('recipes.create') ? 'active' : '' }}">
                    <i data-lucide="house"></i>
                    Beranda
                </a>
                @auth
                    <a href="{{ route('collections.index') }}" class="{{ request()->routeIs('collections.*') ? 'active' : '' }}">
                        <i data-lucide="bookmark"></i>
                        Koleksi 
                    </a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.creator-verifications.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                            <i data-lucide="shield-check"></i>
                            Admin
                        </a>
                    @endif
                @endauth
            </nav>

            <form action="{{ route('recipes.index') }}" method="GET" class="nav-search">
                <label class="sr-only" for="global-search">Cari resep</label>
                <input id="global-search" name="q" value="{{ request('q') }}" placeholder="Cari resep atau bahan...">
                <button aria-label="Cari"><i data-lucide="search"></i></button>
            </form>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('profile.show', auth()->user()) }}" class="nav-profile {{ request()->routeIs('profile.*') ? 'active' : '' }}" aria-label="Profil {{ auth()->user()->username }}">
                        <span class="profile-avatar profile-avatar-nav">
                            @if (auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="">
                            @else
                                <span>{{ auth()->user()->initials }}</span>
                            @endif
                        </span>
                        <span class="user-chip">{{ auth()->user()->username }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="button button-ghost button-small" title="Keluar">
                            <i data-lucide="log-out"></i>
                            <span class="desktop-label">Keluar</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="button button-ghost button-small">Masuk</a>
                    <a href="{{ route('register') }}" class="button button-primary button-small">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="page-shell">
        @if (session('status'))
            <div class="flash-message" role="status">
                {{ session('status') }}
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="site-footer">
        <span class="brand brand-footer"><img src="{{ asset('images/cubu-logo.svg') }}" class="brand-mark" alt=""><span>CuBu</span></span>
        <p>Temukan, masak, dan bagikan resep favoritmu.</p>
    </footer>
</body>
</html>
