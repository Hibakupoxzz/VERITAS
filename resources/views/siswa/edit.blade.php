@extends('layouts.app')

@section('page_title', 'Edit Siswa')

@section('styles')
<style>
    *, *::before, *::after { box-sizing: border-box; }

    .pv-body {
        background: #f5f6fa;
        padding: 2rem 1rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .pv-page-header { display: flex; align-items: center; gap: 14px; margin-bottom: 1.75rem; }
    .pv-page-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: #fffbeb;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .pv-page-icon svg { width: 22px; height: 22px; stroke: #d97706; }
    .pv-page-title { font-size: 1.25rem; font-weight: 600; color: #1a1a2e; margin: 0; }
    .pv-page-sub   { font-size: 0.8rem; color: #6b7280; margin: 2px 0 0; }

    .pv-card {
        background: #fff; border: 1px solid #e8eaed;
        border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1rem;
    }
    .pv-section-label {
        font-size: 0.7rem; font-weight: 600; letter-spacing: 0.06em;
        text-transform: uppercase; color: #9ca3af;
        display: flex; align-items: center; gap: 6px; margin-bottom: 1rem;
    }
    .pv-section-label svg { width: 14px; height: 14px; }

    .pv-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .pv-field { margin-bottom: 14px; }
    .pv-field:last-child { margin-bottom: 0; }
    .pv-label { display: block; font-size: 0.8rem; font-weight: 500; color: #374151; margin-bottom: 6px; }
    .pv-label span { color: #d97706; }
    .pv-input {
        width: 100%; padding: 9px 12px;
        border: 1px solid #e2e5ea; border-radius: 9px;
        font-size: 0.875rem; color: #1a1a2e;
        background: #fafbfc; outline: none; font-family: inherit;
        transition: border-color .15s, box-shadow .15s;
    }
    .pv-input:focus {
        border-color: #d97706; box-shadow: 0 0 0 3px rgba(217,119,6,.1); background: #fff;
    }

    .pv-alert-error {
        background: #fff1f1; border: 1px solid #fecaca;
        border-left: 4px solid #e53e3e; border-radius: 10px;
        padding: 12px 16px; margin-bottom: 1rem;
        font-size: 0.85rem; color: #991b1b;
    }
    .pv-alert-error ul { margin: 6px 0 0 16px; }

    /* Riwayat pelanggaran mini */
    .pv-riwayat-list { display: flex; flex-direction: column; gap: 8px; }
    .pv-riwayat-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px;
        background: #fafbfc; border: 1px solid #f0f0f0;
        border-radius: 9px;
    }
    .pv-riwayat-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #e53e3e; flex-shrink: 0;
    }
    .pv-riwayat-jenis { font-size: 0.85rem; font-weight: 500; color: #1a1a2e; flex: 1; }
    .pv-riwayat-tgl   { font-size: 0.75rem; color: #9ca3af; }
    .pv-riwayat-poin  {
        background: #fff1f1; color: #e53e3e;
        font-size: 0.72rem; font-weight: 700;
        padding: 2px 8px; border-radius: 20px;
        border: 1px solid #fecaca;
    }
    .pv-riwayat-empty { font-size: 0.85rem; color: #9ca3af; text-align: center; padding: 12px 0; }

    .pv-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 0.5rem; }
    .pv-btn-primary {
        background: #d97706; color: #fff; border: none;
        padding: 10px 24px; border-radius: 9px;
        font-size: 0.875rem; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 7px;
        transition: background .15s;
    }
    .pv-btn-primary:hover { background: #b45309; }
    .pv-btn-primary svg { width: 16px; height: 16px; stroke: #fff; }
    .pv-btn-secondary {
        background: #fff; color: #374151; border: 1px solid #e2e5ea;
        padding: 10px 20px; border-radius: 9px;
        font-size: 0.875rem; font-weight: 500; cursor: pointer;
        text-decoration: none; display: inline-flex; align-items: center; gap: 7px;
        transition: background .15s;
    }
    .pv-btn-secondary:hover { background: #f9fafb; color: #374151; }
    .pv-btn-secondary svg { width: 15px; height: 15px; stroke: #6b7280; }

    @media (max-width: 576px) {
        .pv-grid-2 { grid-template-columns: 1fr; }
        .pv-footer { flex-direction: column-reverse; }
        .pv-btn-primary, .pv-btn-secondary { width: 100%; justify-content: center; }
    }
    @media(max-width:768px){

    .pv-grid-2{
        grid-template-columns:1fr;
    }

    .pv-footer{
        flex-direction:column-reverse;
    }

    .pv-btn-primary,
    .pv-btn-secondary{
        width:100%;
        justify-content:center;
    }

}
</style>
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
<footer style="
    padding: 120px 10px 0px 10px;
    text-align:center;
    color:#9CA3AF;
    font-size:13px; ">
    © {{ date('Y') }} VERITAS — Sistem Monitoring Pelanggaran Siswa.
    <br>
    Developed by
    <a href="https://kicauorgspark.my.id"
       target="_blank"
       style="color:#6D1408;font-weight:600;text-decoration:none;">
        KicawOrgspark
    </a>
</footer>
@endsection
