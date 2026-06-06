<x-layouts.app title="Daftar - CuBu">
    <section class="auth-shell">
        <div class="auth-story">
            <span class="eyebrow">Mulai dari dapurmu</span>
            <h1>Satu akun untuk semua inspirasi masak.</h1>
            <p>Buat akun gratis untuk membagikan resep dan menikmati seluruh pengalaman CuBu.</p>
            <div class="auth-story-mark">CuBu</div>
        </div>

        <div class="auth-panel">
            <span class="eyebrow">Bergabung dengan CuBu</span>
            <h2>Buat akun baru</h2>
            <p>Setelah mendaftar, kamu bisa langsung membagikan resep dalam bentuk foto.</p>

            <form method="POST" action="{{ route('register') }}" class="field-stack">
                @csrf
                <label class="field">
                    <span>Username</span>
                    <input name="username" value="{{ old('username') }}" placeholder="Nama yang tampil di CuBu" required autofocus>
                    @error('username') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                    @error('email') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Kata sandi</span>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                    @error('password') <small class="field-error">{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Konfirmasi kata sandi</span>
                    <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" required>
                </label>

                <button class="button button-primary w-full">Daftar</button>
            </form>

            <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
        </div>
    </section>
</x-layouts.app>
