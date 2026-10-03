@extends('layouts.app')

@section('page_title', 'Edit Siswa')

@section('styles')
@include('partials.ui')

*, *::before, *::after { box-sizing: border-box; }

.pv-page-header > div:last-child { min-width: 0; }

.pv-section-label svg { width: 14px; height: 14px; }

.pv-field:last-child { margin-bottom: 0; }    select.pv-input,
textarea.pv-input { width: 100%; max-width: 100%; }

.pv-alert-error {
        background: #fff1f1; border: 1px solid #fecaca;
        border-left: 4px solid var(--c-badge-danger-text); border-radius: 10px;
        padding: 12px 16px; margin-bottom: 1rem;
        font-size: 0.85rem; color: #991b1b;
    }
    .pv-alert-error ul { margin: 6px 0 0 16px; }

    /* Riwayat pelanggaran mini */
    .pv-riwayat-list { display: flex; flex-direction: column; gap: 8px; }
    .pv-riwayat-item {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        padding: 10px 12px;
        background: #fafbfc; border: 1px solid #f0f0f0;
        border-radius: 9px;
    }
    .pv-riwayat-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #e53e3e; flex-shrink: 0;
    }
    .pv-riwayat-jenis { font-size: 0.85rem; font-weight: 500; color: #1a1a2e; flex: 1 1 160px; min-width: 0; overflow-wrap: anywhere; }
    .pv-riwayat-tgl   { font-size: 0.75rem; color: #9ca3af; white-space: nowrap; }
    .pv-riwayat-poin  {
        background: #fff1f1; color: #e53e3e;
        font-size: 0.72rem; font-weight: 700;
        padding: 2px 8px; border-radius: 20px;
        border: 1px solid #fecaca;
    }
    .pv-riwayat-empty { font-size: 0.85rem; color: #9ca3af; text-align: center; padding: 12px 0; }

.pv-btn-primary:hover { background: var(--primary-dark); }
    .pv-btn-primary svg { width: 16px; height: 16px; stroke: #fff; flex-shrink: 0; }

.pv-btn-secondary:hover { background: #f9fafb; color: #374151; }
    .pv-btn-secondary svg { width: 15px; height: 15px; stroke: #6b7280; flex-shrink: 0; }

    /* ── Page footer note ── */
    .pv-page-note {
        padding: 120px 10px 0;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
        overflow-wrap: anywhere;
    }
    .pv-page-note a {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
    }

    /* ── Tom Select containment ── */
    .searchable-select,
    .ts-wrapper { width: 100% !important; max-width: 100%; }
    .ts-control { width: 100%; max-width: 100%; }
    .ts-dropdown { max-width: 100vw; }

    /* =====================================================
       RESPONSIVE
    ===================================================== */

    @media (max-width: 1024px) {
        .pv-body { padding: 1.5rem 1rem; }
    }

    @media (max-width: 900px) {
        .container { width: 100%; max-width: 100%; }
    }

    @media (max-width: 768px) {
        .pv-grid-2 { grid-template-columns: 1fr; }
        .pv-card { padding: 1.1rem 1.15rem; }
        .pv-footer { flex-direction: column-reverse; }
        .pv-btn-primary,
        .pv-btn-secondary {
            width: 100%;
            justify-content: center;
            min-height: 40px;
        }
        .pv-page-note { padding: 48px 8px 0; }
    }

    @media (max-width: 640px) {
        .pv-input { font-size: 16px; /* anti-zoom on iOS */ }
        .pv-body { padding: 1.25rem 0.85rem; }
        .pv-page-icon { width: 40px; height: 40px; }
        .pv-page-icon svg { width: 19px; height: 19px; }
    }

    @media (max-width: 576px) {
        .pv-card { padding: 1rem 0.9rem; }
        .pv-riwayat-item { padding: 9px 10px; gap: 8px; }
    }

    @media (max-width: 480px) {
        .pv-card { padding: 0.85rem 0.75rem; }
        .pv-alert-error { padding: 11px 12px; font-size: 0.8rem; }
        .pv-alert-error ul { margin-left: 14px; }
        .pv-page-note { padding: 32px 6px 0; font-size: 12px; }
    }

    @media (max-width: 400px) {
        .pv-card { padding: 0.75rem 0.6rem; }
        .pv-btn-primary,
        .pv-btn-secondary { padding: 10px 12px; font-size: 0.82rem; }
    }
@endsection

@section('content')
<div class="pv-body">
<div class="container" >

    <div class="pv-page-header">
        <div class="pv-page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </div>
        <div>
            <h1 class="pv-page-title">Edit Siswa</h1>
            <p class="pv-page-sub">Perbarui data siswa</p>
        </div>
    </div>

    @if($errors->any())
    <div class="pv-alert-error">
        <strong>Data belum lengkap:</strong>
        <ul>
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="pv-card">
            <div class="pv-section-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="7" r="4"/><path d="M5.5 21a7 7 0 0 1 13 0"/>
                </svg>
                Identitas siswa
            </div>

            <div class="pv-field">
                <label class="pv-label" for="nama">Nama lengkap <span>*</span></label>
                <input type="text" name="nama" id="nama" class="pv-input"
                    placeholder="Nama lengkap siswa"
                    value="{{ old('nama', $siswa->nama) }}" required>
            </div>

            <div class="pv-grid-2">
                <div class="pv-field">
                    <label class="pv-label" for="nisn">NISN <span>*</span></label>
                    <input type="text" name="nisn" id="nisn" class="pv-input"
                        placeholder="10 digit NISN"
                        value="{{ old('nisn', $siswa->nisn) }}" required>
                </div>
                <div class="pv-field">
                    <label class="pv-label" for="kelas">Kelas <span>*</span></label>
                    <input type="text" name="kelas" id="kelas" class="pv-input"
                        placeholder="Contoh: X IPA 1"
                        value="{{ old('kelas', $siswa->kelas) }}" required>
                </div>
            </div>
        </div>

        {{-- Riwayat pelanggaran singkat --}}
        @if($siswa->pelanggarans && $siswa->pelanggarans->count() > 0)
        <div class="pv-card">
            <div class="pv-section-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Riwayat pelanggaran ({{ $siswa->pelanggarans->count() }} catatan)
            </div>
            <div class="pv-riwayat-list">
                @foreach($siswa->pelanggarans->take(5) as $p)
                <div class="pv-riwayat-item">
                    <div class="pv-riwayat-dot"></div>
                    <div class="pv-riwayat-jenis">{{ $p->jenis_pelanggaran }}</div>
                    <div class="pv-riwayat-tgl">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</div>
                    <span class="pv-riwayat-poin">−{{ $p->poin }}</span>
                </div>
                @endforeach
                @if($siswa->pelanggarans->count() > 5)
                <div style="font-size:.78rem; color:#9ca3af; text-align:center; padding:4px 0;">
                    +{{ $siswa->pelanggarans->count() - 5 }} pelanggaran lainnya
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="pv-footer">
            <a href="{{ route('siswa.index') }}" class="pv-btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Kembali
            </a>
            <button type="submit" class="pv-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Simpan perubahan
            </button>
        </div>
    </form>
</div>
</div>
<footer class="pv-page-note">
    © {{ date('Y') }} VERITAS — Sistem Monitoring Pelanggaran Siswa.
    <br>
    Developed by
    <a href="https://kicauorgspark.my.id"
       target="_blank">
        KicawOrgspark
    </a>
</footer>
@endsection
