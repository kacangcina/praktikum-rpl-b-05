<x-layouts.app title="Video {{ $recipe->title }} - CuBu">
    <section class="form-page-heading">
        <span class="eyebrow">Video resep</span>
        <h1>{{ $recipe->video ? 'Ganti' : 'Tambahkan' }} video {{ $recipe->title }}</h1>
        <p>Video akan tampil langsung pada halaman detail resep. Format MP4, maksimal 500 MB.</p>
    </section>

    <form method="POST" action="{{ route('recipes.video.store', $recipe) }}" enctype="multipart/form-data" class="form-panel verification-form">
        @csrf
        <label class="field">
            <span>Judul video</span>
            <input name="title" value="{{ old('title', $recipe->video?->title) }}" required>
            @error('title') <small class="field-error">{{ $message }}</small> @enderror
        </label>
        <label class="field">
            <span>Deskripsi</span>
            <textarea name="description" rows="5">{{ old('description', $recipe->video?->description) }}</textarea>
            @error('description') <small class="field-error">{{ $message }}</small> @enderror
        </label>
        <label class="field">
            <span>Tingkat kesulitan</span>
            <select name="difficulty" required>
                <option value="">Pilih kesulitan</option>
                @foreach (['mudah', 'sedang', 'sulit'] as $difficulty)
                    <option value="{{ $difficulty }}" @selected(old('difficulty', $recipe->video?->difficulty) === $difficulty)>{{ ucfirst($difficulty) }}</option>
                @endforeach
            </select>
            @error('difficulty') <small class="field-error">{{ $message }}</small> @enderror
        </label>
        <label class="field">
            <span>File video MP4</span>
            <input type="file" name="video" accept="video/mp4" @required(! $recipe->video)>
            @if ($recipe->video)
                <small>Kosongkan jika hanya ingin mengubah judul atau deskripsi video.</small>
            @endif
            @error('video') <small class="field-error">{{ $message }}</small> @enderror
        </label>
        <div class="form-actions">
            <a href="{{ route('recipes.show', $recipe) }}" class="button button-ghost">Batal</a>
            <button class="button button-primary"><i data-lucide="upload"></i>Simpan video</button>
        </div>
    </form>
</x-layouts.app>
