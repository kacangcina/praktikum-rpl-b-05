<x-layouts.app title="Masuk - CuBu">
    <section class="auth-shell">
        <div class="auth-story">
            <span class="eyebrow">Selamat datang kembali</span>
            <h1>Masak lebih percaya diri bersama CuBu.</h1>
            <p>Simpan inspirasi, tulis resep andalan, dan temukan masakan baru dari bahan yang ada di rumah.</p>
            <div class="auth-story-mark">CuBu</div>
        </div>

        <div class="auth-panel">
            <span class="eyebrow">Masuk ke akun</span>
            <h2>Halo, koki!</h2>
            <p>Gunakan email dan kata sandi yang sudah didaftarkan.</p>

            <form method="POST" action="{{ route('login') }}" class="field-stack">
                @csrf
                <label class="field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                    @error('email') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Kata sandi</span>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                    @error('password') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label class="checkbox-field">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ingat saya</span>
                </label>

                <button class="button button-primary w-full">Masuk ke CuBu</button>
            </form>

            <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
        </div>
    </section>
</x-layouts.app>
