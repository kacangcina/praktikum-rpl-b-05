<x-layouts.app title="Resep - CuBu">
    @if (! $search)
        <section class="home-hero featured-hero">
            @if ($featuredRecipe)
                <div class="hero-content">
                    <span class="eyebrow">Highlighted recipe</span>
                    <p class="featured-author">
                        Oleh {{ $featuredRecipe->creator?->username ?? $featuredRecipe->creator?->name ?? 'Koki CuBu' }}
                    </p>
                    <h1>{{ $featuredRecipe->title }}</h1>
                    <p>{{ \Illuminate\Support\Str::limit($featuredRecipe->description, 180) }}</p>
                    <div class="featured-meta">
                        <span>{{ $featuredRecipe->estimated_time }} menit</span>
                        <span>{{ ucfirst($featuredRecipe->difficulty) }}</span>
                        <span>{{ $featuredRecipe->ingredients->count() }} bahan</span>
                        @if ($featuredRecipe->video)
                            <span><i data-lucide="play"></i> Video</span>
                        @endif
                    </div>
                    <a href="{{ route('recipes.show', $featuredRecipe) }}" class="button button-primary">
                        Lihat resep
                        <i data-lucide="arrow-right"></i>
                    </a>
                </div>
                <a href="{{ route('recipes.show', $featuredRecipe) }}" class="hero-featured-media">
                    @if ($featuredRecipe->thumbnail_url)
                        <img src="{{ $featuredRecipe->thumbnail_url }}" alt="{{ $featuredRecipe->title }}">
                    @else
                        <span class="recipe-placeholder">CuBu</span>
                    @endif
                    @if ($featuredRecipe->video)
                        <span class="featured-play"><i data-lucide="play"></i></span>
                    @endif
                </a>
            @else
                <div class="hero-content">
                    <span class="eyebrow">Highlighted recipe</span>
                    <h1>Resep pilihan akan tampil di sini.</h1>
                    <p>Publikasikan resep pertama untuk menjadikannya sorotan di beranda CuBu.</p>
                </div>
                <div class="hero-featured-media"><span class="recipe-placeholder">CuBu</span></div>
            @endif
        </section>
    @endif

    <section class="ingredient-strip">
        <div>
            <span class="eyebrow">Cari dari bahan</span>
            <h2>Punya bahan apa di dapur?</h2>
        </div>
        <div class="ingredient-chips">
            @foreach (['Ayam', 'Daging sapi', 'Telur', 'Tahu', 'Tempe', 'Cabai', 'Bawang merah'] as $ingredient)
                <a href="{{ route('recipes.index', ['q' => $ingredient]) }}">{{ $ingredient }}</a>
            @endforeach
        </div>
    </section>

    <section class="section-heading">
        <div>
            <span class="eyebrow">{{ $search ? 'Hasil pencarian' : 'Rekomendasi terkini' }}</span>
            <h2>{{ $search ? 'Resep untuk "'.$search.'"' : 'Pilihan untuk dimasak hari ini' }}</h2>
        </div>
        @auth
            @if (auth()->user()->canPublishRecipes())
                <a href="{{ route('recipes.create') }}" class="button button-secondary">Buat resep</a>
            @endif
        @endauth
    </section>

    @if ($search && $recipes->isEmpty())
        <div class="empty-state">
            <h2>Tidak ada resep yang ditemukan untuk "{{ $search }}"</h2>
            <p>Coba kata kunci lain. Sementara itu, ini beberapa resep terbaru yang bisa kamu coba.</p>
        </div>
    @endif

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
                    <p class="recipe-author">Oleh {{ $recipe->creator?->username ?? $recipe->creator?->name ?? 'Koki CuBu' }}</p>
                    <h3><a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a></h3>
                    <p>{{ \Illuminate\Support\Str::limit($recipe->description, 92) }}</p>
                    <div class="recipe-meta">
                        <span>{{ $recipe->estimated_time }} menit</span>
                        <span>{{ $recipe->ingredients->count() }} bahan</span>
                    </div>
                </div>
            </article>
        @empty
            @unless ($search)
                <div class="empty-state full-grid">
                    <h2>Belum ada resep</h2>
                    <p>Jadilah yang pertama membagikan resep andalanmu di CuBu.</p>
                </div>
            @endunless
        @endforelse
    </section>

    @if ($suggestions->isNotEmpty())
        <section class="recipe-grid suggestions">
            @foreach ($suggestions as $recipe)
                <article class="recipe-card compact-card">
                    <a href="{{ route('recipes.show', $recipe) }}" class="recipe-image">
                        @if ($recipe->thumbnail_url)
                            <img src="{{ $recipe->thumbnail_url }}" alt="{{ $recipe->title }}">
                        @else
                            <span class="recipe-placeholder">CuBu</span>
                        @endif
                    </a>
                    <div class="recipe-card-body">
                        <p class="recipe-author">Saran resep</p>
                        <h3><a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a></h3>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    <div class="pagination-wrap">{{ $recipes->links() }}</div>
</x-layouts.app>
