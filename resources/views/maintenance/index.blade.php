@extends('layouts.app')

@section('title', 'Bersihkan Website')
@section('page_title', 'Bersihkan Website')

@section('styles')
@include('partials.ui')

.mt-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: var(--sp-6);
    margin-bottom: var(--sp-8);
}

.mt-card {
    padding: var(--sp-8);
    border: 1px solid var(--c-border);
    border-radius: var(--r-lg);
    background: var(--c-surface);
    box-shadow: 0 4px 12px rgba(0, 0, 0, .03);
}

.mt-card .num {
    color: var(--c-text);
    font-size: 18px;
    font-weight: var(--fw-bold);
    line-height: 1.2;
    overflow-wrap: anywhere;
}

.mt-card .lbl {
    margin-top: var(--sp-2);
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
    letter-spacing: .03em;
    text-transform: uppercase;
}

.mt-body { padding: var(--sp-9); }

.mt-list {
    margin: var(--sp-5) 0 var(--sp-8);
    padding-left: var(--sp-8);
    color: var(--c-text-muted);
    font-size: var(--fs-md);
    line-height: var(--lh-relaxed);
}

.mt-out {
    margin-top: var(--sp-7);
    padding: var(--sp-6);
    border-radius: var(--r-md);
    background: #0f172a;
    color: #d1fae5;
    font-family: monospace;
    font-size: var(--fs-sm);
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}
@endsection

@section('content')

<div class="pv-page-header">
    <div class="pv-page-icon">
        <i class="fa-solid fa-broom"></i>
    </div>
    <div class="pv-page-headtext">
        <h1 class="pv-page-title">Bersihkan Website</h1>
        <p class="pv-page-sub">Hapus cache dan file sementara agar aplikasi berjalan ringan.</p>
    </div>
</div>

<div class="mt-grid">
    <div class="mt-card">
        <div class="num">{{ $info['log_size'] }}</div>
        <div class="lbl">Ukuran Log</div>
    </div>
    <div class="mt-card">
        <div class="num">{{ $info['log_files'] }}</div>
        <div class="lbl">File Log</div>
    </div>
    <div class="mt-card">
        <div class="num">{{ $info['view_cache'] }}</div>
        <div class="lbl">Cache View</div>
    </div>
    <div class="mt-card">
        <div class="num">{{ strtoupper($info['cache_driver']) }}</div>
        <div class="lbl">Cache Driver</div>
    </div>
</div>

<div class="pv-card">
    <div class="mt-body">
        <div class="pv-section-label">
            <i class="fa-solid fa-trash-can"></i>
            Yang akan dibersihkan
        </div>

        <ul class="mt-list">
            <li>Cache aplikasi, cache config, cache route, dan cache event.</li>
            <li>File view yang sudah dikompilasi.</li>
            <li>Isi file log di <code>storage/logs</code>.</li>
        </ul>

        <p style="color: var(--c-text-muted); font-size: var(--fs-sm);">
            <i class="fa-solid fa-circle-info"></i>
            Data pelanggaran, prestasi, siswa, dan akun <strong>tidak</strong> dihapus.
            Anda yang sedang login juga tidak akan keluar.
        </p>

        <form action="{{ route('maintenance.clean') }}" method="POST"
              onsubmit="return confirm('Bersihkan cache dan file sementara sekarang?')">
            @csrf
            <div class="form-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-broom"></i>
                    Bersihkan Sekarang
                </button>
            </div>
        </form>

        @if(session('maintenance_output'))
            <div class="mt-out">{{ session('maintenance_output') }}</div>
        @endif
    </div>
</div>

@endsection
