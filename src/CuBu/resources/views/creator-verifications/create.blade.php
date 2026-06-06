<x-layouts.app title="Pengajuan Creator - CuBu">
    <section class="form-page-heading">
        <span class="eyebrow">Program Creator CuBu</span>
        <h1>Ajukan verifikasi creator</h1>
        <p>Creator terverifikasi dapat mengunggah video kelas memasak. Kirim dokumen dan informasi yang membantu admin menilai pengalamanmu.</p>
    </section>

    @if ($latestVerification?->status === 'pending')
        <div class="status-panel status-pending">
            <i data-lucide="clock"></i>
            <div>
                <h2>Pengajuan sedang ditinjau</h2>
                <p>Dikirim {{ $latestVerification->submitted_at->diffForHumans() }}. Kamu akan menerima notifikasi setelah admin memberi keputusan.</p>
            </div>
        </div>
    @else
        @if ($latestVerification?->status === 'rejected')
            <div class="status-panel status-rejected">
                <i data-lucide="circle-x"></i>
                <div>
                    <h2>Pengajuan sebelumnya ditolak</h2>
                    <p>{{ $latestVerification->rejection_reason }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('creator.apply.store') }}" enctype="multipart/form-data" class="form-panel verification-form">
            @csrf

            <label class="field">
                <span>Dokumen pendukung</span>
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                <small>KTP, sertifikat, atau portofolio dalam PDF/JPG/PNG, maksimal 10 MB. Dokumen disimpan privat.</small>
                @error('document') <small class="field-error">{{ $message }}</small> @enderror
            </label>

            <label class="field">
                <span>Link portofolio <small>(opsional)</small></span>
                <input type="url" name="portfolio_url" value="{{ old('portfolio_url') }}" placeholder="https://instagram.com/...">
                @error('portfolio_url') <small class="field-error">{{ $message }}</small> @enderror
            </label>

            <label class="field">
                <span>Pengalaman dan alasan mendaftar</span>
                <textarea name="notes" rows="7" required placeholder="Ceritakan pengalaman memasak, jenis konten, dan rencana kelas video...">{{ old('notes') }}</textarea>
                @error('notes') <small class="field-error">{{ $message }}</small> @enderror
            </label>

            <div class="form-actions">
                <a href="{{ route('profile.me') }}" class="button button-ghost">Batal</a>
                <button class="button button-primary"><i data-lucide="send"></i>Kirim pengajuan</button>
            </div>
        </form>
    @endif
</x-layouts.app>
