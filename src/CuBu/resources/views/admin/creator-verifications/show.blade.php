<x-layouts.app title="Tinjau {{ $verification->user->username }} - CuBu">
    <a href="{{ route('admin.creator-verifications.index') }}" class="back-link">Kembali ke antrean</a>

    <section class="admin-review-grid">
        <article class="form-panel">
            <span class="eyebrow">Pemohon creator</span>
            <div class="review-user">
                <span class="profile-avatar profile-avatar-large">
                    @if ($verification->user->avatar_url)
                        <img src="{{ $verification->user->avatar_url }}" alt="">
                    @else
                        <span>{{ $verification->user->initials }}</span>
                    @endif
                </span>
                <div>
                    <h1>{{ $verification->user->name }}</h1>
                    <p>{{ '@'.$verification->user->username }}</p>
                    <a href="{{ route('profile.show', $verification->user) }}">Lihat profil publik</a>
                </div>
            </div>

            <dl class="review-details">
                <div><dt>Dikirim</dt><dd>{{ $verification->submitted_at->format('d M Y, H:i') }}</dd></div>
                <div><dt>Pengalaman</dt><dd>{{ $verification->notes }}</dd></div>
                @if ($verification->portfolio_url)
                    <div><dt>Portofolio</dt><dd><a href="{{ $verification->portfolio_url }}" target="_blank" rel="noopener">{{ $verification->portfolio_url }}</a></dd></div>
                @endif
                <div><dt>Dokumen</dt><dd><a href="{{ route('creator.verifications.document', $verification) }}" class="button button-secondary button-small"><i data-lucide="download"></i>Unduh dokumen</a></dd></div>
            </dl>
        </article>

        <aside class="form-panel review-action">
            <span class="eyebrow">Keputusan</span>
            <h2>Status: {{ ucfirst($verification->status) }}</h2>

            @if ($verification->status === 'pending')
                <form method="POST" action="{{ route('admin.creator-verifications.approve', $verification) }}">
                    @csrf
                    @method('PATCH')
                    <button class="button button-success w-full"><i data-lucide="badge-check"></i>Setujui creator</button>
                </form>

                <form method="POST" action="{{ route('admin.creator-verifications.reject', $verification) }}" class="field-stack">
                    @csrf
                    @method('PATCH')
                    <label class="field">
                        <span>Alasan penolakan</span>
                        <textarea name="rejection_reason" rows="5" required placeholder="Jelaskan dokumen yang kurang atau alasan lainnya...">{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason') <small class="field-error">{{ $message }}</small> @enderror
                    </label>
                    <button class="button button-danger w-full"><i data-lucide="circle-x"></i>Tolak pengajuan</button>
                </form>
            @else
                <p>Ditinjau oleh {{ $verification->reviewer?->username ?? 'admin' }} pada {{ $verification->reviewed_at?->format('d M Y, H:i') }}.</p>
                @if ($verification->rejection_reason)
                    <div class="status-panel status-rejected"><p>{{ $verification->rejection_reason }}</p></div>
                @endif
            @endif
        </aside>
    </section>
</x-layouts.app>
