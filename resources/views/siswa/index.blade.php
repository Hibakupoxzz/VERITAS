@extends('layouts.app')

{{-- dipakai untuk urut kelas pada filter dropdown --}}
@use(App\Models\Siswa)

@section('title', 'Data Siswa')
@section('page_title', 'Data Siswa')

@section('styles')
@include('partials.ui')

/* =========================================================
   DATA SISWA
   Primitif bersama (.pv-page-*, .pv-card, dst.) berasal dari
   partials/ui.blade.
========================================================= */

/* Dibatasi ke halaman ini saja. Sebelumnya "*" global ikut
   mengubah sidebar, navbar, dan seluruh layout. */
.container-page,
.container-page *,
.container-page *::before,
.container-page *::after {
    box-sizing: border-box;
}

.container-page {
    width: 100%;
    margin: 0 auto;
}


/* ── Header ── */
.container-page .pv-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
}

.pv-page-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.pv-page-left > div:last-child {
    min-width: 0;
}

.pv-page-header h1,
.pv-page-header p {
    overflow-wrap: anywhere;
}

/* Variabel --color-secondary-red tidak ada di tema, sehingga
   tombol sebelumnya tidak punya latar. Pakai --primary. */
.pv-btn-add {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s;
}

.pv-btn-add:hover {
    background: var(--primary-dark, #581006);
    color: #fff;
}


/* ── Alert ── */
.pv-alert-success {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 1.25rem;
    padding: 12px 16px;
    border: 1px solid #bbf7d0;
    border-left: 4px solid #16a34a;
    border-radius: 10px;
    background: #f0fdf4;
    color: #15803d;
    font-size: .875rem;
}

.pv-alert-success svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    stroke: #16a34a;
}


/* ── Stats ── */
.pv-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 1.25rem;
}

.pv-stat-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 1rem 1.25rem;
    border: 1px solid #e8eaed;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
}

.pv-stat-card > div:last-child {
    min-width: 0;
}

.pv-stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    border-radius: 10px;
}

.pv-stat-icon svg { width: 18px; height: 18px; }

.pv-stat-icon.blue  { background: #FBEAE8; }
.pv-stat-icon.blue svg { stroke: var(--primary); }
.pv-stat-icon.green { background: #f0fdf4; }
.pv-stat-icon.green svg { stroke: #16a34a; }
.pv-stat-icon.red   { background: #fff1f1; }
.pv-stat-icon.red svg { stroke: #e53e3e; }

.pv-stat-val {
    color: #1a1a2e;
    font-size: 1.35rem;
    font-weight: 700;
    line-height: 1;
}

.pv-stat-lbl {
    margin-top: 3px;
    color: #6b7280;
    font-size: .72rem;
}


/* ── Toolbar ── */
.pv-toolbar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 1rem;
    padding: 14px 1.25rem;
    border: 1px solid #e8eaed;
    border-radius: 12px;
    background: #fff;
}

.pv-search-wrap {
    position: relative;
    flex: 1 1 220px;
    min-width: 0;
}

.pv-search-wrap svg {
    position: absolute;
    left: 10px;
    top: 50%;
    width: 15px;
    height: 15px;
    transform: translateY(-50%);
    stroke: #9ca3af;
    pointer-events: none;
}

.pv-search-wrap input {
    width: 100%;
    padding: 8px 12px 8px 34px;
    border: 1px solid #e2e5ea;
    border-radius: 8px;
    background: #fafbfc;
    font-family: inherit;
    font-size: .85rem;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}

.pv-search-wrap input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(109, 20, 8, .15);
}

.pv-filter-select {
    flex: 0 1 auto;
    min-width: 0;
    max-width: 100%;
    padding: 8px 10px;
    border: 1px solid #e2e5ea;
    border-radius: 8px;
    background: #fafbfc;
    color: #374151;
    font-family: inherit;
    font-size: .85rem;
    outline: none;
    cursor: pointer;
}

.pv-filter-select:focus {
    border-color: var(--primary);
}


/* ── Tabel ── */
.pv-table-card {
    overflow: hidden;                 /* sudut membulat ikut memotong header tabel */
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
}

.pv-table-scroll {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.pv-table {
    width: 100%;
    min-width: 700px;
    border-collapse: collapse;
    font-size: .85rem;
}

.pv-table thead tr {
    background: #1F2937;
}

.pv-table th {
    padding: 11px 14px;
    color: #fff;
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: .05em;
    text-align: left;
    text-transform: uppercase;
    white-space: nowrap;
}

.pv-table tbody tr {
    border-bottom: 1px solid #f3f4f6;
    transition: background .1s;
}

.pv-table tbody tr:last-child { border-bottom: none; }
.pv-table tbody tr:hover { background: #fafbfc; }

.pv-table td {
    padding: 12px 14px;
    color: #374151;
    vertical-align: middle;
}

.pv-td-no {
    color: #9ca3af;
    font-size: .8rem;
}

.pv-td-nisn {
    color: #6b7280;
    font-family: monospace;
    font-size: .85rem;
}


/* ── Sel siswa ── */
.pv-siswa-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pv-siswa-cell > div:last-child {
    min-width: 0;
}

.pv-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    font-size: .72rem;
    font-weight: 700;
}

.pv-siswa-name {
    color: #1a1a2e;
    font-size: .875rem;
    font-weight: 500;
    overflow-wrap: anywhere;
}

/* NISN sudah punya kolom sendiri di desktop; baris ini hanya
   tampil di layar kecil saat kolom NISN disembunyikan. */
.pv-siswa-nisn {
    display: none;
    margin-top: 2px;
    color: #6b7280;
    font-size: .72rem;
    overflow-wrap: anywhere;
}


/* ── Badge ── */
.pv-kelas-badge {
    display: inline-block;
    padding: 4px 10px;
    border: 1px solid #E8C2BD;
    border-radius: 20px;
    background: #FBEAE8;
    color: var(--primary);
    font-size: .75rem;
    font-weight: 600;
    white-space: nowrap;
}

.pv-poin-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
}

.pv-poin-badge.aman   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.pv-poin-badge.sedang { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.pv-poin-badge.tinggi { background: #fff1f1; color: #e53e3e; border: 1px solid #fecaca; }


/* ── Tombol aksi ── */
.pv-action-wrap {
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
    gap: 8px;
}

.pv-action-wrap form {
    margin: 0;
}

.pv-btn-edit,
.pv-btn-delete,
.pv-btn-report {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: .2s;
}

.pv-btn-edit svg,
.pv-btn-delete svg {
    width: 13px;
    height: 13px;
    stroke: currentColor;
}

.pv-btn-edit {
    border: 1px solid #FCD34D;
    background: #FEF3C7;
    color: #92400E;
}

.pv-btn-edit:hover {
    background: #FDE68A;
    color: var(--primary);
}

.pv-btn-delete {
    border: 1px solid #FCA5A5;
    background: #FEE2E2;
    color: #B91C1C;
}

.pv-btn-delete:hover {
    background: #FECACA;
    color: #c53030;
}

.pv-btn-report {
    border: 1px solid var(--primary);
    background: var(--primary);
    color: #fff;
}

.pv-btn-report:hover {
    background: var(--primary-dark, #581006);
    color: #fff;
}


/* ── Kosong ── */
.pv-empty {
    padding: 3rem 1.5rem;
    text-align: center;
}

.pv-empty svg {
    width: 48px;
    height: 48px;
    margin-bottom: 12px;
    stroke: #d1d5db;
}

.pv-empty p {
    color: #9ca3af;
    font-size: .9rem;
}

.pv-empty a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 12px;
    color: var(--primary);
    font-size: .875rem;
    font-weight: 500;
    text-decoration: none;
}


/* ── Responsive ── */
@media (max-width: 1024px) {
    .pv-table { min-width: 640px; }
}

@media (max-width: 768px) {
    .pv-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .pv-toolbar { padding: 12px 1rem; }

    /* Sembunyikan kolom NISN (kolom ke-3), bukan Total Poin.
       NISN dipindah ke bawah nama siswa. */
    .pv-table th:nth-child(3),
    .pv-table td:nth-child(3) { display: none; }
    .pv-siswa-nisn { display: block; }

    .pv-table { min-width: 520px; }
    .pv-table th { padding: 9px 10px; font-size: .66rem; }
    .pv-table td { padding: 10px; }
    .pv-siswa-cell { gap: 8px; }
}

@media (max-width: 640px) {
    .container-page .pv-page-header { gap: 10px; }
    .pv-page-left { gap: 10px; }

    .pv-btn-add { width: 100%; min-height: 44px; }

    .pv-toolbar { flex-direction: column; align-items: stretch; }
    .pv-search-wrap { flex: 1 1 auto; width: 100%; }

    .pv-search-wrap input,
    .pv-filter-select {
        width: 100%;
        min-height: 40px;
        font-size: 16px;               /* mencegah iOS zoom otomatis */
    }

    .pv-btn-edit,
    .pv-btn-delete,
    .pv-btn-report {
        min-height: 40px;
        padding: 9px 12px;
    }

    .pv-table { min-width: 460px; }
}

@media (max-width: 576px) {
    .pv-stats { grid-template-columns: 1fr; }
    .pv-table { min-width: 420px; }
    .pv-empty { padding: 2.25rem 1rem; }
    .pv-empty svg { width: 40px; height: 40px; }
}

@media (max-width: 480px) {
    .pv-alert-success { padding: 11px 13px; font-size: .82rem; }
    .pv-stat-card { padding: .9rem 1rem; }
    .pv-table { min-width: 380px; }
    .pv-table th { padding: 8px; }
    .pv-table td { padding: 9px 8px; }
    .pv-avatar { width: 32px; height: 32px; font-size: .66rem; }
}

@media (max-width: 400px) {
    .pv-table { min-width: 340px; }
    .pv-table th { font-size: .6rem; letter-spacing: .03em; }
    .pv-table td { padding: 8px 6px; }
    .pv-btn-edit,
    .pv-btn-delete,
    .pv-btn-report { padding: 8px 10px; font-size: 12px; }
    .pv-kelas-badge,
    .pv-poin-badge { padding: 3px 8px; }
}
@endsection


@section('content')

@php
    $isAdmin    = auth()->user()->isAdmin();
    $isGuru     = auth()->user()->isGuru();
    $totalSiswa = $siswas->count();
    $totalKelas = $siswas->pluck('kelas')->unique()->count();
    $totalPoin  = $siswas->sum(fn ($s) => $s->pelanggarans->sum('poin'));
@endphp

<div class="container-page">

    {{-- Header --}}
    <div class="pv-page-header">

        <div class="pv-page-left">
            <div class="pv-page-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>

            <div>
                <h1 class="pv-page-title">Data Siswa</h1>
                <p class="pv-page-sub">Kelola data siswa terdaftar</p>
            </div>
        </div>

        @if($isAdmin)
            <a href="{{ route('siswa.create') }}" class="pv-btn-add">
                <i class="fa-solid fa-plus"></i>
                Tambah Siswa
            </a>
        @endif

    </div>

    {{-- Session success --}}
    @if(session('success'))
        <div class="pv-alert-success">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="pv-stats">

        <div class="pv-stat-card">
            <div class="pv-stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="pv-stat-val">{{ $totalSiswa }}</div>
                <div class="pv-stat-lbl">Total siswa</div>
            </div>
        </div>

        <div class="pv-stat-card">
            <div class="pv-stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div>
                <div class="pv-stat-val">{{ $totalKelas }}</div>
                <div class="pv-stat-lbl">Jumlah kelas</div>
            </div>
        </div>

        <div class="pv-stat-card">
            <div class="pv-stat-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <div class="pv-stat-val">{{ $totalPoin }}</div>
                <div class="pv-stat-lbl">Total poin pelanggaran</div>
            </div>
        </div>

    </div>

    {{-- Toolbar --}}
    <div class="pv-toolbar">

        <div class="pv-search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" id="filterSearch" placeholder="Cari nama atau NISN siswa..." autocomplete="off">
        </div>

        <select class="pv-filter-select" id="filterKelas" aria-label="Filter kelas">
            <option value="">Semua kelas</option>
            @foreach(Siswa::urutKelasList($siswas->pluck('kelas')) as $kelas)
                <option value="{{ strtolower($kelas) }}">{{ $kelas }}</option>
            @endforeach
        </select>

    </div>

    {{-- Table --}}
    <div class="pv-table-card">
        <div class="pv-table-scroll">

            <table class="pv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Total Poin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="tableBody">

                    @forelse($siswas as $siswa)
                        @php
                            // filter() membuang spasi ganda agar $w[0] tidak error pada string kosong
                            $inits = collect(explode(' ', $siswa->nama))
                                ->filter()
                                ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                ->take(2)
                                ->join('');

                            $totalPoinSiswa = $siswa->pelanggarans->sum('poin');
                            $poinClass = $totalPoinSiswa >= 20 ? 'tinggi' : ($totalPoinSiswa >= 10 ? 'sedang' : 'aman');
                        @endphp

                        <tr data-nama="{{ strtolower($siswa->nama) }}"
                            data-nisn="{{ strtolower($siswa->nisn) }}"
                            data-kelas="{{ strtolower($siswa->kelas) }}">

                            <td class="pv-td-no">{{ $loop->iteration }}</td>

                            <td>
                                <div class="pv-siswa-cell">
                                    <div class="pv-avatar">{{ $inits }}</div>
                                    <div>
                                        <div class="pv-siswa-name">{{ $siswa->nama }}</div>
                                        <div class="pv-siswa-nisn">NISN: {{ $siswa->nisn }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="pv-td-nisn">{{ $siswa->nisn }}</td>

                            <td><span class="pv-kelas-badge">{{ $siswa->kelas }}</span></td>

                            <td>
                                <span class="pv-poin-badge {{ $poinClass }}">{{ $totalPoinSiswa }} poin</span>
                            </td>

                            <td>
                                <div class="pv-action-wrap">

                                    @if($isGuru)

                                        <a href="{{ route('lapor.index', ['siswa_id' => $siswa->id]) }}"
                                           class="pv-btn-report"
                                           title="Laporkan pelanggaran siswa ini">
                                            <i class="fa-solid fa-bullhorn"></i>
                                            Laporkan
                                        </a>

                                    @else

                                        <a href="{{ route('siswa.edit', $siswa->id) }}" class="pv-btn-edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="pv-btn-delete"
                                                    onclick="return confirm('Yakin ingin menghapus data siswa ini?')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>

                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6">
                                <div class="pv-empty">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <line x1="22" y1="11" x2="16" y2="11"/>
                                    </svg>
                                    <p>Belum ada data siswa terdaftar.</p>

                                    @if($isAdmin)
                                        <a href="{{ route('siswa.create') }}">
                                            <i class="fa-solid fa-plus"></i>
                                            Tambah siswa pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    @endforelse

                    {{-- Ditampilkan oleh filter jika tidak ada baris yang cocok --}}
                    <tr id="noResult" style="display:none">
                        <td colspan="6">
                            <div class="pv-empty">
                                <p>Tidak ada siswa yang cocok dengan pencarian.</p>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection


@section('scripts')
<script>
(function () {
    'use strict';

    var filterSearch = document.getElementById('filterSearch');
    var filterKelas  = document.getElementById('filterKelas');
    var noResult     = document.getElementById('noResult');
    var rows         = document.querySelectorAll('#tableBody tr[data-nama]');

    function applyFilter() {
        var q     = filterSearch.value.toLowerCase().trim();
        var kelas = filterKelas.value.toLowerCase();
        var tampil = 0;

        rows.forEach(function (row) {
            var cocokQ     = row.dataset.nama.indexOf(q) !== -1 || row.dataset.nisn.indexOf(q) !== -1;
            var cocokKelas = !kelas || row.dataset.kelas === kelas;
            var show       = cocokQ && cocokKelas;

            row.style.display = show ? '' : 'none';
            if (show) { tampil++; }
        });

        noResult.style.display = (rows.length && tampil === 0) ? '' : 'none';
    }

    filterSearch.addEventListener('input', applyFilter);
    filterKelas.addEventListener('change', applyFilter);
})();
</script>
@endsection
