<x-layouts.app title="Edit Profil - CuBu">
    <section class="profile-edit-shell">
        <div class="form-page-heading profile-edit-heading">
            <span class="eyebrow">Pengaturan akun</span>
            <h1>Edit profil</h1>
            <p>Perbarui identitas yang akan dilihat pengguna lain saat menemukan resepmu.</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="form-panel profile-edit-form">
            @csrf
            @method('PUT')

            <div class="avatar-editor">
                <label class="avatar-upload-control" data-avatar-dropzone>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" data-avatar-input>
                    <span class="profile-avatar profile-avatar-editor">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="Foto profil saat ini" data-avatar-preview>
                        @else
                            <span data-avatar-initials>{{ $user->initials }}</span>
                            <img alt="Pratinjau foto profil" data-avatar-preview>
                        @endif
                    </span>
                    <span class="avatar-edit-icon"><i data-lucide="pencil"></i></span>
                </label>
                <div>
                    <h2>Foto profil</h2>
                    <p>Gunakan JPG, PNG, atau WebP maksimal 2 MB.</p>
                    @error('avatar') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="profile-fields">
                <label class="field">
                    <span>Nama</span>
                    <input name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Username</span>
                    <input name="username" value="{{ old('username', $user->username) }}" required>
                    @error('username') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Bio</span>
                    <textarea name="bio" rows="6" maxlength="500" placeholder="Ceritakan minat dan gaya memasakmu...">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio') <small class="field-error">{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="form-actions">
                <a href="{{ route('profile.show', $user) }}" class="button button-ghost">Batal</a>
                <button class="button button-primary">
                    <i data-lucide="save"></i>
                    Simpan profil
                </button>
            </div>
        </form>
    </section>
</x-layouts.app>
