<x-layouts.app title="{{ $user->username }} - CuBu">
    <section class="profile-header">
        <div class="profile-avatar-shell">
            <div class="profile-avatar profile-avatar-large">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="Foto profil {{ $user->username }}">
                @else
                    <span>{{ $user->initials }}</span>
                @endif
            </div>

            @if ($user->canUploadVideos())
                <span class="profile-role-badge creator" title="Creator terverifikasi" aria-label="Creator terverifikasi">
                    <i data-lucide="chef-hat"></i>
                </span>
            @elseif ($user->isAdmin())
                <span class="profile-role-badge admin" title="Administrator" aria-label="Administrator">
                    <i data-lucide="shield-check"></i>
                </span>
            @else
                @auth
                    @if (auth()->id() === $user->id)
                        <a href="{{ route('creator.apply') }}" class="profile-role-badge verification" title="Ajukan verifikasi creator" aria-label="Ajukan verifikasi creator">?</a>
                    @endif
                @endauth
            @endif
        </div>

        <div class="profile-identity">
            <div class="profile-title-row">
                <h1>{{ $user->name }}</h1>
                <span class="profile-name-divider" aria-hidden="true"></span>
                <p class="profile-username">{{ '@'.$user->username }}</p>
            </div>

            <div class="profile-stats">
                @unless ($user->isAdmin())
                    <div><strong>{{ $recipes->total() }}</strong><span>Resep</span></div>
                @endunless
                @if ($user->canUploadVideos())
                    <div><strong>{{ $user->videos()->count() }}</strong><span>Video</span></div>
                @endif
                <div>
                    <strong>{{ $user->role_label }}</strong>
                    <span>Status akun</span>
                </div>
            </div>

            @auth
                @if (auth()->id() === $user->id)
                    <a href="{{ route('profile.edit') }}" class="button button-secondary profile-edit-button">
                        <i data-lucide="settings"></i>
                        Edit profil
                    </a>
                @endif
            @endauth

            <p class="profile-bio">
                {{ $user->bio ?: ($user->isAdmin() ? 'Akun administrator untuk mengelola verifikasi creator dan operasional CuBu.' : 'Belum ada bio. Ceritakan sedikit tentang gaya memasakmu.') }}
            </p>

            @auth
                @if (auth()->id() === $user->id)
                    @if ($user->isAdmin())
                        <div class="creator-status-actions">
                            <a href="{{ route('admin.creator-verifications.index') }}" class="button button-primary">
                                <i data-lucide="shield-check"></i>
                                Buka dashboard admin
                            </a>
                        </div>
                    @endif
                @endif
            @endauth
        </div>
    </section>

    @if ($notifications->isNotEmpty())
        <section class="notification-panel">
            <div class="panel-heading">
                <div><span class="eyebrow">Pembaruan akun</span><h2>Notifikasi</h2></div>
                @if ($notifications->contains(fn ($notification) => is_null($notification->read_at)))
                    <form method="POST" action="{{ route('notifications.read') }}">
                        @csrf
                        <button class="button button-ghost button-small">Tandai dibaca</button>
                    </form>
                @endif
            </div>
            <div class="notification-list">
                @foreach ($notifications as $notification)
                    <article class="{{ $notification->read_at ? '' : 'unread' }}">
                        <i data-lucide="{{ ($notification->data['status'] ?? '') === 'approved' ? 'badge-check' : 'circle-x' }}"></i>
                        <div>
                            <h3>{{ $notification->data['title'] ?? 'Notifikasi' }}</h3>
                            <p>{{ $notification->data['message'] ?? '' }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @unless ($user->isAdmin())
        <section class="profile-recipes-heading">
            <div>
                <span class="eyebrow">Karya dapur</span>
                <h2>Resep {{ $user->name }}</h2>
            </div>

            @auth
                @if (auth()->id() === $user->id)
                    @if (auth()->user()->canPublishRecipes())
                        <a href="{{ route('recipes.create') }}" class="button button-primary">
                            <i data-lucide="plus"></i>
                            Buat resep
                        </a>
                    @endif
                @endif
            @endauth
        </section>

        <section class="recipe-grid">
            @forelse ($recipes as $recipe)
                <article class="recipe-card">
                    <a href="{{ route('recipes.show', $recipe) }}" class="recipe-image">
                        @if ($recipe->thumbnail_url)
                            <img src="{{ $recipe->thumbnail_url }}" alt="{{ $recipe->title }}">
                        @else
                            <span class="recipe-placeholder">CuBu</span>
                        @endif
                        <span class="difficulty difficulty-{{ $recipe->difficulty }}">{{ ucfirst($recipe->difficulty) }}</span>
                    </a>
                    <div class="recipe-card-body">
                        <p class="recipe-author">{{ $recipe->estimated_time }} menit</p>
                        <h3><a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a></h3>
                        <p>{{ \Illuminate\Support\Str::limit($recipe->description, 92) }}</p>
                        <div class="recipe-meta">
                            <span>{{ $recipe->ingredients->count() }} bahan</span>
                            <span>{{ ucfirst($recipe->difficulty) }}</span>
                        </div>
                        @auth
                            @if (auth()->id() === $user->id && $user->canUploadVideos())
                                <a href="{{ route('recipes.video.create', $recipe) }}" class="button {{ $recipe->video ? 'button-secondary' : 'button-primary' }} button-small profile-video-action">
                                    <i data-lucide="{{ $recipe->video ? 'replace' : 'video' }}"></i>
                                    {{ $recipe->video ? 'Ganti video' : 'Tambah video' }}
                                </a>
                            @endif
                        @endauth
                    </div>
                </article>
            @empty
                <div class="empty-state full-grid">
                    <i data-lucide="book-open"></i>
                    <h2>Belum ada resep</h2>
                    <p>{{ $user->canUploadVideos() ? 'Buat resep terlebih dahulu sebelum menambahkan video.' : 'Resep yang dipublikasikan akan tampil di sini.' }}</p>
                    @auth
                        @if (auth()->id() === $user->id && $user->canPublishRecipes())
                            <a href="{{ route('recipes.create') }}" class="button button-primary">Buat resep pertama</a>
                        @endif
                    @endauth
                </div>
            @endforelse
        </section>

        <div class="pagination-wrap">{{ $recipes->links() }}</div>
    @endunless
</x-layouts.app>
