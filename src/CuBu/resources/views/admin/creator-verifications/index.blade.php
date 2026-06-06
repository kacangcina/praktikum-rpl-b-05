<x-layouts.app title="Verifikasi Creator - Admin CuBu">
    <section class="section-heading admin-heading">
        <div>
            <span class="eyebrow">Dashboard admin</span>
            <h1>Verifikasi creator</h1>
            <p>Tinjau dokumen dan pengalaman pengguna sebelum memberikan hak upload video.</p>
        </div>
    </section>

    <nav class="status-tabs" aria-label="Filter status">
        @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $value => $label)
            <a href="{{ route('admin.creator-verifications.index', ['status' => $value]) }}" class="{{ $status === $value ? 'active' : '' }}">
                {{ $label }} <span>{{ $counts[$value] ?? 0 }}</span>
            </a>
        @endforeach
    </nav>

    <div class="admin-list">
        @forelse ($verifications as $verification)
            <article class="admin-list-row">
                <span class="profile-avatar profile-avatar-nav">
                    @if ($verification->user->avatar_url)
                        <img src="{{ $verification->user->avatar_url }}" alt="">
                    @else
                        <span>{{ $verification->user->initials }}</span>
                    @endif
                </span>
                <div>
                    <h2>{{ $verification->user->name }}</h2>
                    <p>{{ '@'.$verification->user->username }} · {{ $verification->submitted_at->format('d M Y, H:i') }}</p>
                </div>
                <span class="status-badge status-{{ $verification->status }}">{{ ucfirst($verification->status) }}</span>
                <a href="{{ route('admin.creator-verifications.show', $verification) }}" class="button button-secondary button-small">Tinjau</a>
            </article>
        @empty
            <div class="empty-state">
                <h2>Tidak ada pengajuan</h2>
                <p>Belum ada data dengan status ini.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrap">{{ $verifications->links() }}</div>
</x-layouts.app>
