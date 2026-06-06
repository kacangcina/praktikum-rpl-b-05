<x-layouts.app title="{{ $recipe->title }} - CuBu">
    <a href="{{ route('recipes.index') }}" class="back-link">Kembali ke beranda</a>

    <article class="recipe-detail">
        <div class="detail-main">
            <div class="detail-media">
                @if ($recipe->video)
                    <video controls preload="metadata" poster="{{ $recipe->thumbnail_url }}">
                        <source src="{{ $recipe->video->file_url }}" type="video/mp4">
                    </video>
                @elseif ($recipe->thumbnail_url)
                    <img src="{{ $recipe->thumbnail_url }}" alt="{{ $recipe->title }}">
                @else
                    <span class="recipe-placeholder">CuBu</span>
                @endif
            </div>

            <div class="detail-title">
                <div>
                    <span class="eyebrow">Resep {{ ucfirst($recipe->difficulty) }}</span>
                    <h1>{{ $recipe->title }}</h1>
                    <p>{{ $recipe->description }}</p>
                </div>
                <div class="detail-actions">
                    @auth
                        @if ($isSaved)
                            <form method="POST" action="{{ route('collections.destroy', $recipe) }}">
                                @csrf
                                @method('DELETE')
                                <button class="button button-secondary"><i data-lucide="bookmark-check"></i>Tersimpan</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('collections.store', $recipe) }}">
                                @csrf
                                <button class="button button-secondary"><i data-lucide="bookmark"></i>Simpan</button>
                            </form>
                        @endif

                        @if (auth()->user()->canUploadVideos() && auth()->id() === $recipe->user_id)
                            <a href="{{ route('recipes.video.create', $recipe) }}" class="button button-primary">
                                <i data-lucide="video"></i>
                                {{ $recipe->video ? 'Ganti video' : 'Tambah video' }}
                            </a>
                        @endif

                        @if (auth()->id() === $recipe->user_id)
                            <form method="POST" action="{{ route('recipes.destroy', $recipe) }}" onsubmit="return confirm('Hapus resep {{ addslashes($recipe->title) }}? Resep yang dihapus tidak dapat dikembalikan.')">
                                @csrf
                                @method('DELETE')
                                <button class="button button-danger" type="submit">
                                    <i data-lucide="trash-2"></i>
                                    Hapus resep
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            <section class="steps-section">
                <span class="eyebrow">Ikuti urutannya</span>
                <h2>Langkah memasak</h2>
                <ol class="steps-list">
                    @foreach ($recipe->steps as $step)
                        <li>
                            <span class="step-number">{{ $step->step_number }}</span>
                            <div class="step-copy">
                                <h3>{{ $step->title ?: 'Langkah '.$step->step_number }}</h3>
                                <p>{{ $step->description }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>

        <aside class="detail-sidebar">
            <section class="creator-panel">
                <div class="avatar">{{ strtoupper(substr($recipe->creator?->username ?? 'C', 0, 1)) }}</div>
                <div>
                    <span class="eyebrow">Dibuat oleh</span>
                    <h2>{{ $recipe->creator?->username ?? $recipe->creator?->name ?? 'Koki CuBu' }}</h2>
                    @if ($recipe->creator?->role === 'creator' && $recipe->creator?->is_verified)
                        <span class="verified-badge">Creator terverifikasi</span>
                    @else
                        <span class="member-badge">Anggota CuBu</span>
                    @endif
                </div>
                <div class="stat-grid">
                    <div><strong>{{ $recipe->estimated_time }}</strong><span>Menit</span></div>
                    <div><strong>{{ ucfirst($recipe->difficulty) }}</strong><span>Kesulitan</span></div>
                </div>
            </section>

            <section class="detail-panel">
                <h2>Alat masak</h2>
                <ul class="clean-list">
                    @foreach ($recipe->tools as $tool)
                        <li>{{ $tool->tool_name }}</li>
                    @endforeach
                </ul>
            </section>

            <section class="detail-panel">
                <h2>Bahan-bahan</h2>
                <ul class="ingredient-list">
                    @foreach ($recipe->ingredients as $ingredient)
                        <li><span>{{ $ingredient->ingredient_name }}</span><strong>{{ $ingredient->quantity }}</strong></li>
                    @endforeach
                </ul>
            </section>
        </aside>
    </article>
</x-layouts.app>
