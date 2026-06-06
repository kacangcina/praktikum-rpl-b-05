<x-layouts.app title="Buat Resep - CuBu">
    <div class="form-page-heading">
        <span class="eyebrow">Bagikan masakanmu</span>
        <h1>Buat resep baru</h1>
        <p>Lengkapi setiap bagian agar resep mudah dipahami dan berhasil dicoba oleh orang lain.</p>
    </div>

    <form method="POST" action="{{ route('recipes.store') }}" enctype="multipart/form-data" class="recipe-form" data-recipe-form>
        @csrf

        <section class="form-panel form-intro-grid">
            <div>
                <h2>Informasi utama</h2>
                <p class="form-help">Foto yang terang dan judul yang spesifik akan membuat resepmu lebih mudah ditemukan.</p>

                <label class="upload-dropzone" data-image-dropzone>
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" data-image-input>
                    <span class="upload-icon">+</span>
                    <strong>Unggah foto masakan</strong>
                    <small>JPG, PNG, atau WebP, maksimal 5 MB</small>
                    <img data-image-preview alt="Pratinjau thumbnail">
                </label>
                @error('thumbnail') <span class="field-error">{{ $message }}</span> @enderror

                @if (auth()->user()->canUploadVideos())
                    <label class="upload-dropzone video-upload-dropzone" data-video-dropzone>
                        <input type="file" name="video" accept="video/mp4" data-video-input>
                        <span class="upload-icon"><i data-lucide="video"></i></span>
                        <strong>Unggah video memasak</strong>
                        <small data-video-file-name>MP4, opsional, maksimal 500 MB</small>
                    </label>
                    @error('video') <span class="field-error">{{ $message }}</span> @enderror
                @endif
            </div>

            <div class="field-stack">
                <label class="field">
                    <span>Judul resep</span>
                    <input name="title" value="{{ old('title') }}" placeholder="Contoh: Soto Ayam Lamongan" required>
                    @error('title') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Deskripsi singkat</span>
                    <textarea name="description" rows="4" placeholder="Ceritakan rasa dan ciri khas masakan ini..." required>{{ old('description') }}</textarea>
                    @error('description') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <div class="two-fields">
                    <label class="field">
                        <span>Tingkat kesulitan</span>
                        <select name="difficulty" required>
                            <option value="">Pilih kesulitan</option>
                            <option value="mudah" @selected(old('difficulty') === 'mudah')>Mudah</option>
                            <option value="sedang" @selected(old('difficulty') === 'sedang')>Sedang</option>
                            <option value="sulit" @selected(old('difficulty') === 'sulit')>Sulit</option>
                        </select>
                        @error('difficulty') <small class="field-error">{{ $message }}</small> @enderror
                    </label>

                    <label class="field">
                        <span>Estimasi waktu</span>
                        <div class="input-suffix">
                            <input type="number" name="estimated_time" min="1" max="1440" value="{{ old('estimated_time', 30) }}" required>
                            <span>menit</span>
                        </div>
                        @error('estimated_time') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                </div>
            </div>
        </section>

        <div class="form-columns">
            <section class="form-panel">
                <div class="panel-heading">
                    <div>
                        <span class="eyebrow">Persiapan</span>
                        <h2>Alat masak</h2>
                    </div>
                    <button type="button" class="icon-button" data-add-row="tools-list" title="Tambah alat" aria-label="Tambah alat">+</button>
                </div>
                <div id="tools-list" class="dynamic-list" data-row-list>
                    @foreach (old('tools', ['']) as $tool)
                        <div class="dynamic-row">
                            <input name="tools[]" value="{{ $tool }}" placeholder="Nama alat masak" required>
                            <button type="button" class="remove-button" data-remove-row aria-label="Hapus alat">&times;</button>
                        </div>
                    @endforeach
                </div>
                @error('tools') <span class="field-error">{{ $message }}</span> @enderror
                @error('tools.*') <span class="field-error">{{ $message }}</span> @enderror
            </section>

            <section class="form-panel">
                <div class="panel-heading">
                    <div>
                        <span class="eyebrow">Takaran lengkap</span>
                        <h2>Bahan</h2>
                    </div>
                    <button type="button" class="icon-button" data-add-row="ingredients-list" title="Tambah bahan" aria-label="Tambah bahan">+</button>
                </div>
                <div id="ingredients-list" class="dynamic-list" data-row-list>
                    @php
                        $oldNames = old('ingredient_names', ['']);
                        $oldQuantities = old('ingredient_quantities', ['']);
                    @endphp
                    @foreach ($oldNames as $index => $name)
                        <div class="dynamic-row ingredient-row">
                            <input name="ingredient_names[]" value="{{ $name }}" placeholder="Nama bahan" required>
                            <input name="ingredient_quantities[]" value="{{ $oldQuantities[$index] ?? '' }}" placeholder="Takaran" required>
                            <button type="button" class="remove-button" data-remove-row aria-label="Hapus bahan">&times;</button>
                        </div>
                    @endforeach
                </div>
                @error('ingredient_names') <span class="field-error">{{ $message }}</span> @enderror
                @error('ingredient_names.*') <span class="field-error">{{ $message }}</span> @enderror
                @error('ingredient_quantities.*') <span class="field-error">{{ $message }}</span> @enderror
            </section>
        </div>

        <section class="form-panel">
            <div class="panel-heading">
                <div>
                    <span class="eyebrow">Urut dan jelas</span>
                    <h2>Langkah memasak</h2>
                </div>
                <button type="button" class="button button-secondary button-small" data-add-row="steps-list">+ Tambah langkah</button>
            </div>
            <div id="steps-list" class="steps-editor" data-row-list data-numbered>
                @php
                    $oldStepTitles = old('step_titles', ['']);
                    $oldSteps = old('steps', ['']);
                @endphp
                @foreach ($oldSteps as $index => $step)
                    <div class="step-editor-row">
                        <span class="step-editor-number"></span>
                        <div class="step-editor-fields">
                            <input name="step_titles[]" value="{{ $oldStepTitles[$index] ?? '' }}" placeholder="Contoh: Rebus ayam" required>
                            <textarea name="steps[]" rows="3" placeholder="Jelaskan cara, waktu, dan tanda kematangannya..." required>{{ $step }}</textarea>
                        </div>
                        <button type="button" class="remove-button" data-remove-row aria-label="Hapus langkah">&times;</button>
                    </div>
                @endforeach
            </div>
            @error('step_titles') <span class="field-error">{{ $message }}</span> @enderror
            @error('step_titles.*') <span class="field-error">{{ $message }}</span> @enderror
            @error('steps') <span class="field-error">{{ $message }}</span> @enderror
            @error('steps.*') <span class="field-error">{{ $message }}</span> @enderror
        </section>

        <div class="form-actions">
            <a href="{{ route('recipes.index') }}" class="button button-ghost">Batal</a>
            <button class="button button-primary">Publikasikan resep</button>
        </div>
    </form>

    <template id="tools-list-template">
        <div class="dynamic-row">
            <input name="tools[]" placeholder="Nama alat masak" required>
            <button type="button" class="remove-button" data-remove-row aria-label="Hapus alat">&times;</button>
        </div>
    </template>
    <template id="ingredients-list-template">
        <div class="dynamic-row ingredient-row">
            <input name="ingredient_names[]" placeholder="Nama bahan" required>
            <input name="ingredient_quantities[]" placeholder="Takaran" required>
            <button type="button" class="remove-button" data-remove-row aria-label="Hapus bahan">&times;</button>
        </div>
    </template>
    <template id="steps-list-template">
        <div class="step-editor-row">
            <span class="step-editor-number"></span>
            <div class="step-editor-fields">
                <input name="step_titles[]" placeholder="Contoh: Rebus ayam" required>
                <textarea name="steps[]" rows="3" placeholder="Jelaskan cara, waktu, dan tanda kematangannya..." required></textarea>
            </div>
            <button type="button" class="remove-button" data-remove-row aria-label="Hapus langkah">&times;</button>
        </div>
    </template>
</x-layouts.app>
