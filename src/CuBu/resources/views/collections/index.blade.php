<x-layouts.app title="Koleksi Saya - CuBu">
    <section class="section-heading">
        <div>
            <span class="eyebrow">Resep tersimpan</span>
            <h1>Koleksi</h1>
            <p>Semua resep yang ingin kamu masak lagi tersimpan di satu tempat.</p>
        </div>
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
                    @if ($recipe->video)
                        <span class="video-badge"><i data-lucide="play"></i>Video</span>
                    @endif
                    <span class="difficulty difficulty-{{ $recipe->difficulty }}">{{ ucfirst($recipe->difficulty) }}</span>
                </a>
                <div class="recipe-card-body">
                    <p class="recipe-author">Oleh {{ $recipe->creator?->username }}</p>
                    <h3><a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a></h3>
                    <p>{{ \Illuminate\Support\Str::limit($recipe->description, 92) }}</p>
                    <form method="POST" action="{{ route('collections.destroy', $recipe) }}">
                        @csrf
                        @method('DELETE')
                        <button class="button button-ghost button-small"><i data-lucide="bookmark-x"></i>Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="empty-state full-grid">
                <i data-lucide="bookmark"></i>
                <h2>Koleksi masih kosong</h2>
                <p>Simpan resep dari halaman detail agar mudah ditemukan kembali.</p>
            </div>
        @endforelse
    </section>

    <div class="pagination-wrap">{{ $recipes->links() }}</div>
</x-layouts.app>
