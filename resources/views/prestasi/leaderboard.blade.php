@extends('layouts.app')

@section('title', 'Leaderboard - VERITAS')
@section('page_title', 'Leaderboard')

@section('content')

@php
    $Str = \Illuminate\Support\Str::class;

    $kelasList = $kelasList ?? collect();

    // Satu konfigurasi, tiga papan peringkat.
    $boards = [
        'prestasi' => [
            'title' => 'Top Prestasi',
            'desc'  => 'Siswa dengan prestasi terbanyak.',
            'icon'  => 'fa-trophy',
            'tone'  => 'ok',
            'rows'  => $topPrestasi,
            'value' => 'prestasis_count',
            'empty' => ['Belum ada data prestasi', 'Data siswa yang memiliki prestasi akan muncul di sini.'],
        ],
        'pelanggaran' => [
            'title' => 'Top Pelanggaran',
            'desc'  => 'Siswa dengan pelanggaran terbanyak.',
            'icon'  => 'fa-triangle-exclamation',
            'tone'  => 'bad',
            'rows'  => $topPelanggaran,
            'value' => 'pelanggarans_count',
            'empty' => ['Belum ada data pelanggaran', 'Data siswa yang memiliki pelanggaran akan muncul di sini.'],
        ],
        'saldo' => [
            'title' => 'Saldo Poin',
            'desc'  => 'Peringkat siswa berdasarkan saldo poin.',
            'icon'  => 'fa-ranking-star',
            'tone'  => 'brand',
            'rows'  => $saldoSiswa,
            'value' => 'saldo_poin',
            'empty' => ['Belum ada data siswa', 'Data saldo poin siswa akan muncul di sini.'],
        ],
    ];
@endphp

<div class="lb-page">

    {{-- HEADER --}}
    <header class="lb-head">
        <h1>Leaderboard</h1>
        <p>Peringkat siswa berdasarkan prestasi, pelanggaran, dan saldo poin.</p>
    </header>


    {{-- TAB --}}
    <div class="lb-tabs" role="tablist" aria-label="Jenis peringkat">
        @foreach($boards as $key => $board)
            <button
                type="button"
                role="tab"
                id="tab-{{ $key }}"
                class="lb-tab {{ $loop->first ? 'active' : '' }}"
                data-tab="{{ $key }}"
                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                aria-controls="panel-{{ $key }}"
                tabindex="{{ $loop->first ? '0' : '-1' }}"
            >
                <i class="fa-solid {{ $board['icon'] }}"></i>
                {{ $board['title'] }}
            </button>
        @endforeach
    </div>


    {{-- TOOLBAR: CARI & KELAS --}}
    <div class="lb-toolbar">

        <div class="lb-search">
            <i class="fa-solid fa-magnifying-glass lb-search-icon"></i>
            <input
                type="text"
                id="lbSearch"
                class="lb-input"
                placeholder="Cari nama, NISN, atau kelas siswa"
                value="{{ request('search') }}"
                autocomplete="off"
                aria-label="Cari siswa"
            >
            <button type="button" id="lbClear" class="lb-clear is-hidden" title="Hapus pencarian" aria-label="Hapus pencarian">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="lb-seg" id="lbGrades" role="group" aria-label="Tingkat kelas">
            <button type="button" class="lb-pill active" data-grade="">Semua</button>
            <button type="button" class="lb-pill" data-grade="X">Kelas X</button>
            <button type="button" class="lb-pill" data-grade="XI">Kelas XI</button>
            <button type="button" class="lb-pill" data-grade="XII">Kelas XII</button>
        </div>

        @if(count($kelasList) > 0)
            <select id="lbKelas" class="lb-input lb-select" aria-label="Filter kelas">
                <option value="">Semua kelas</option>
                @foreach($kelasList as $kelasOption)
                    <option value="{{ $kelasOption }}">{{ $kelasOption }}</option>
                @endforeach
            </select>
        @endif

    </div>

    <p class="lb-count">Menampilkan <strong id="lbCount">0</strong> siswa</p>


    {{-- PAPAN PERINGKAT --}}
    @foreach($boards as $key => $board)

        @php
            $rows = $board['rows'];
            $max  = max(1, (float) $rows->max($board['value']));
        @endphp

        <section
            class="lb-panel lb-tone-{{ $board['tone'] }} {{ $loop->first ? 'active' : '' }}"
            id="panel-{{ $key }}"
            role="tabpanel"
            aria-labelledby="tab-{{ $key }}"
            {{ $loop->first ? '' : 'hidden' }}
        >

            <div class="lb-card">

                <div class="lb-card-head">
                    <span class="lb-card-icon"><i class="fa-solid {{ $board['icon'] }}"></i></span>
                    <div>
                        <h2>{{ $board['title'] }}</h2>
                        <p>{{ $board['desc'] }}</p>
                    </div>
                </div>

                @if($rows->count())

                    <ol class="lb-list">

                        @foreach($rows as $index => $siswa)

                            @php
                                $nilai = max(0, (float) data_get($siswa, $board['value'], 0));
                                $lebar = min(100, round($nilai / $max * 100));
                            @endphp

                            <li class="lb-item"
                                data-nama="{{ $Str::lower($siswa->nama) }}"
                                data-nisn="{{ $Str::lower($siswa->nisn ?? '') }}"
                                data-kelas="{{ $Str::lower($siswa->kelas ?? '') }}">

                                <span class="lb-rank {{ $index === 0 ? 'lb-rank-1' : '' }} {{ $index === 1 ? 'lb-rank-2' : '' }} {{ $index === 2 ? 'lb-rank-3' : '' }}">
                                    {{ $index + 1 }}
                                </span>

                                <span class="lb-avatar">{{ $Str::upper($Str::substr($siswa->nama, 0, 1)) }}</span>

                                <div class="lb-info">
                                    <div class="lb-name">
                                        <strong>{{ $siswa->nama }}</strong>
                                        @if($siswa->kelas)
                                            <span class="lb-chip">{{ $siswa->kelas }}</span>
                                        @else
                                            <span class="lb-chip lb-chip-empty">Tanpa kelas</span>
                                        @endif
                                    </div>

                                    <span class="lb-nisn">NISN: {{ $siswa->nisn ?? '-' }}</span>

                                    <span class="lb-bar" aria-hidden="true"><i style="width: {{ $lebar }}%"></i></span>
                                </div>

                                <div class="lb-metrics">

                                    @if($key === 'saldo')

                                        <div class="lb-metric lb-metric-split">
                                            <span class="lb-up" title="Total poin prestasi">
                                                <i class="fa-solid fa-trophy"></i> {{ $siswa->prestasis_sum_poin ?? 0 }}
                                            </span>
                                            <span class="lb-down" title="Total poin pelanggaran">
                                                <i class="fa-solid fa-triangle-exclamation"></i> {{ $siswa->pelanggarans_sum_poin ?? 0 }}
                                            </span>
                                        </div>

                                        <div class="lb-metric lb-metric-main lb-metric-big">
                                            <strong>{{ $siswa->saldo_poin }}</strong>
                                            <span>saldo poin</span>
                                        </div>

                                    @elseif($key === 'prestasi')

                                        <div class="lb-metric">
                                            <strong>{{ $siswa->prestasis_count }}</strong>
                                            <span>prestasi</span>
                                        </div>

                                        <div class="lb-metric lb-metric-main">
                                            <strong>+{{ $siswa->prestasis_sum_poin ?? 0 }}</strong>
                                            <span>total poin</span>
                                        </div>

                                    @else

                                        <div class="lb-metric">
                                            <strong>{{ $siswa->pelanggarans_count }}</strong>
                                            <span>pelanggaran</span>
                                        </div>

                                        <div class="lb-metric lb-metric-main">
                                            <strong>-{{ $siswa->pelanggarans_sum_poin ?? 0 }}</strong>
                                            <span>total poin</span>
                                        </div>

                                    @endif

                                </div>

                                <a href="{{ route('siswa.show', $siswa->id) }}"
                                   class="lb-go"
                                   title="Lihat siswa"
                                   aria-label="Lihat {{ $siswa->nama }}">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>

                            </li>

                        @endforeach

                    </ol>

                    {{-- Muncul saat pencarian/filter tidak menemukan hasil --}}
                    <div class="lb-empty lb-empty-filter is-hidden">
                        <span class="lb-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <strong>Tidak ada siswa ditemukan</strong>
                        <p>Tidak ada siswa yang sesuai dengan filter atau kata kunci pencarian.</p>
                    </div>

                    @if($key === 'saldo')
                        <div class="lb-formula">
                            <i class="fa-solid fa-calculator"></i>
                            <span>
                                Saldo poin dihitung dari
                                <strong>100 &minus; total pelanggaran + total prestasi</strong>.
                            </span>
                        </div>
                    @endif

                @else

                    <div class="lb-empty">
                        <span class="lb-empty-icon"><i class="fa-solid {{ $board['icon'] }}"></i></span>
                        <strong>{{ $board['empty'][0] }}</strong>
                        <p>{{ $board['empty'][1] }}</p>
                    </div>

                @endif

            </div>

        </section>

    @endforeach

</div>

@endsection


@section('styles')

@include('partials.ui')

/* =========================================================
   LEADERBOARD
   Satu warna aksen (--primary), bayangan tipis, tanpa gradien
   atau efek hover mengangkat. Hijau hanya untuk prestasi dan
   merah hanya untuk pelanggaran. Scope: .lb-page.
========================================================= */

.lb-page {
    --lb-ink: #1f1b18;
    --lb-muted: #6b625a;
    --lb-faint: #968d84;
    --lb-line: #e7e0d8;
    --lb-line-soft: #f0eae3;
    --lb-line-strong: #d6cdc3;
    --lb-soft: #f7f3ee;
    --lb-tint: #fcfaf7;
    --lb-accent: var(--primary, #6d1408);
    --lb-shadow: 0 1px 2px rgba(31, 27, 24, .04), 0 12px 28px -18px rgba(31, 27, 24, .22);

    width: 100%;
    max-width: 980px;
    margin: 0 auto;
    color: var(--lb-ink);
    font-size: 14px;
    line-height: 1.55;
    overflow-wrap: break-word;
}

.lb-page *,
.lb-page *::before,
.lb-page *::after { box-sizing: border-box; }

.lb-page .is-hidden { display: none !important; }

.lb-page a { text-decoration: none; }

/* Warna per papan */
.lb-tone-ok    { --tone: #2f855a; --tone-ink: #1f6b44; --tone-bg: #e3f3ea; }
.lb-tone-bad   { --tone: #c53030; --tone-ink: #9b2c2c; --tone-bg: #fbe3df; }
.lb-tone-brand { --tone: var(--lb-accent); --tone-ink: var(--lb-accent); --tone-bg: #f6e3de; }


/* ---------- HEADER ---------- */

.lb-head { margin-bottom: 20px; }

.lb-head h1 {
    margin: 0 0 4px;
    font-size: 28px;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.01em;
}

.lb-head p {
    margin: 0;
    color: var(--lb-muted);
    font-size: 14px;
}


/* ---------- TAB ---------- */

.lb-tabs {
    display: flex;
    gap: 24px;
    margin-bottom: 18px;
    border-bottom: 1px solid var(--lb-line);
    overflow-x: auto;
}

.lb-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 0;
    margin-bottom: -1px;
    border: none;
    border-bottom: 2px solid transparent;
    background: none;
    color: var(--lb-muted);
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: color .15s, border-color .15s;
}

.lb-tab i { font-size: 13px; }

.lb-tab:hover { color: var(--lb-ink); }

.lb-tab.active {
    border-bottom-color: var(--lb-accent);
    color: var(--lb-accent);
}

.lb-tab:focus-visible,
.lb-pill:focus-visible,
.lb-go:focus-visible,
.lb-clear:focus-visible {
    outline: 2px solid var(--lb-accent);
    outline-offset: 2px;
}


/* ---------- TOOLBAR ---------- */

.lb-toolbar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.lb-search {
    position: relative;
    flex: 1 1 280px;
    min-width: 0;
}

.lb-search-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--lb-faint);
    font-size: 13px;
    pointer-events: none;
}

.lb-input {
    display: block;
    width: 100%;
    height: 42px;
    padding: 0 12px;
    border: 1px solid var(--lb-line-strong);
    border-radius: 8px;
    background: #fff;
    color: var(--lb-ink);
    font-family: inherit;
    font-size: 14px;
    transition: border-color .15s, box-shadow .15s;
}

.lb-search .lb-input { padding: 0 38px 0 36px; }

.lb-input:hover { border-color: #c4b9ad; }

.lb-input:focus {
    outline: none;
    border-color: var(--lb-accent);
    box-shadow: 0 0 0 3px rgba(109, 20, 8, .14);
}

.lb-select {
    width: 190px;
    padding-right: 36px;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%236b625a' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 13px center;
}

.lb-clear {
    position: absolute;
    right: 6px;
    top: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    transform: translateY(-50%);
    border: none;
    border-radius: 6px;
    background: none;
    color: var(--lb-faint);
    font-size: 13px;
    cursor: pointer;
}

.lb-clear:hover { background: var(--lb-soft); color: var(--lb-ink); }

/* Pilihan tingkat kelas: kontrol bersegmen */
.lb-seg {
    display: inline-flex;
    padding: 3px;
    border: 1px solid var(--lb-line);
    border-radius: 10px;
    background: var(--lb-soft);
}

.lb-pill {
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    background: none;
    color: var(--lb-muted);
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: background .15s, color .15s;
}

.lb-pill:hover { color: var(--lb-ink); }

.lb-pill.active {
    background: #fff;
    color: var(--lb-accent);
    box-shadow: 0 1px 2px rgba(31, 27, 24, .1), 0 0 0 1px var(--lb-line);
}

.lb-count {
    margin: 12px 2px 14px;
    color: var(--lb-muted);
    font-size: 12px;
}

.lb-count strong { color: var(--lb-ink); }


/* ---------- PANEL & KARTU ---------- */

.lb-panel[hidden] { display: none; }

.lb-card {
    overflow: hidden;
    border: 1px solid var(--lb-line);
    border-radius: 14px;
    background: #fff;
    box-shadow: var(--lb-shadow);
}

.lb-card-head {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 24px;
    border-bottom: 1px solid var(--lb-line);
    background: var(--lb-tint);
}

.lb-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border-radius: 10px;
    background: var(--tone-bg);
    color: var(--tone-ink);
    font-size: 16px;
}

.lb-card-head h2 {
    margin: 0 0 2px;
    font-size: 17px;
    font-weight: 700;
}

.lb-card-head p {
    margin: 0;
    color: var(--lb-muted);
    font-size: 13px;
}


/* ---------- DAFTAR PERINGKAT ---------- */

.lb-list {
    margin: 0;
    padding: 0 24px;
    list-style: none;
}

.lb-item {
    display: grid;
    grid-template-columns: 34px 40px minmax(0, 1fr) auto 32px;
    align-items: center;
    gap: 14px;
    padding: 16px 0;
    border-bottom: 1px solid var(--lb-line-soft);
}

.lb-item:last-child { border-bottom: none; }

.lb-item[hidden] { display: none; }

.lb-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: var(--lb-soft);
    color: var(--lb-muted);
    font-size: 13px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

/* Medali emas / perak / perunggu, sengaja redup */
.lb-rank-1 { background: #fdf0d3; color: #8a5a0a; }
.lb-rank-2 { background: #eceff1; color: #4f5b66; }
.lb-rank-3 { background: #f7e6d6; color: #8a4f22; }

.lb-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--tone-bg);
    color: var(--tone-ink);
    font-size: 15px;
    font-weight: 700;
}

.lb-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.lb-name {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px 10px;
}

.lb-name strong {
    font-size: 15px;
    font-weight: 700;
    overflow-wrap: anywhere;
}

.lb-chip {
    padding: 1px 9px;
    border-radius: 6px;
    background: #f1ebe4;
    color: #4a423b;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.6;
    white-space: nowrap;
}

.lb-chip-empty {
    background: none;
    padding-left: 0;
    color: var(--lb-faint);
    font-weight: 500;
}

.lb-nisn {
    color: var(--lb-muted);
    font-size: 12px;
}

/* Batang tipis: nilai dibanding peringkat teratas */
.lb-bar {
    display: block;
    width: 100%;
    max-width: 280px;
    height: 4px;
    margin-top: 6px;
    border-radius: 999px;
    background: var(--lb-line-soft);
    overflow: hidden;
}

.lb-bar i {
    display: block;
    height: 100%;
    min-width: 3px;
    border-radius: 999px;
    background: var(--tone);
}


/* ---------- ANGKA ---------- */

.lb-metrics {
    display: flex;
    align-items: center;
    gap: 28px;
}

.lb-metric {
    min-width: 84px;
    text-align: right;
}

.lb-metric strong {
    display: block;
    color: var(--lb-ink);
    font-size: 17px;
    font-weight: 700;
    line-height: 1.2;
    font-variant-numeric: tabular-nums;
}

.lb-metric span {
    color: var(--lb-faint);
    font-size: 11px;
}

.lb-metric-main strong { color: var(--tone-ink); }

.lb-metric-big strong { font-size: 22px; }

.lb-metric-split {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 72px;
}

.lb-metric-split span {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}

.lb-metric-split i { font-size: 10px; }

.lb-up   { color: #1f6b44 !important; }
.lb-down { color: #9b2c2c !important; }

.lb-go {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    color: var(--lb-faint);
    font-size: 12px;
    transition: background .15s, color .15s;
}

.lb-go:hover {
    background: var(--lb-soft);
    color: var(--lb-accent);
}


/* ---------- KOSONG & CATATAN ---------- */

.lb-empty {
    padding: 44px 20px;
    text-align: center;
}

.lb-empty-filter { padding: 36px 20px; }

.lb-empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    margin-bottom: 12px;
    border-radius: 50%;
    background: var(--tone-bg);
    color: var(--tone-ink);
    font-size: 20px;
}

.lb-empty-filter .lb-empty-icon {
    background: var(--lb-soft);
    color: var(--lb-muted);
}

.lb-empty strong {
    display: block;
    font-size: 16px;
}

.lb-empty p {
    max-width: 44ch;
    margin: 4px auto 0;
    color: var(--lb-muted);
}

.lb-formula {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 24px;
    border-top: 1px solid var(--lb-line);
    background: var(--lb-tint);
    color: var(--lb-muted);
    font-size: 13px;
}

.lb-formula i { color: var(--lb-accent); }

.lb-formula strong { color: var(--lb-ink); }


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 760px) {
    .lb-head h1 { font-size: 24px; }

    .lb-card-head { padding: 16px; }
    .lb-list { padding: 0 16px; }
    .lb-formula { padding: 14px 16px; }

    /* Baris kedua: angka turun ke bawah, memenuhi lebar */
    .lb-item {
        grid-template-columns: 34px 40px minmax(0, 1fr) 32px;
        gap: 10px 12px;
        padding: 16px 0;
    }

    .lb-metrics {
        grid-column: 1 / -1;
        justify-content: flex-start;
        gap: 24px;
        padding: 10px 12px;
        border-radius: 8px;
        background: var(--lb-tint);
    }

    .lb-metric { min-width: 0; text-align: left; }

    .lb-metric-split { align-items: flex-start; }
    .lb-metric-split span { justify-content: flex-start; }

    .lb-metric-big { margin-left: auto; text-align: right; }

    .lb-bar { max-width: none; }
}

@media (max-width: 640px) {
    .lb-toolbar { gap: 10px; }

    .lb-search { flex-basis: 100%; }

    .lb-seg { flex: 1 1 100%; }
    .lb-pill { flex: 1; padding: 9px 4px; text-align: center; }

    .lb-select { width: 100%; }

    /* 16px mencegah iOS zoom otomatis saat input difokus */
    .lb-input { font-size: 16px; }
}

@media (max-width: 400px) {
    .lb-item { grid-template-columns: 30px 36px minmax(0, 1fr) 28px; gap: 10px 8px; }
    .lb-rank { width: 30px; height: 30px; }
    .lb-avatar { width: 36px; height: 36px; font-size: 14px; }
    .lb-name strong { font-size: 14px; }
}
@endsection


@section('scripts')

<script>
(function () {
    'use strict';

    var $ = function (id) { return document.getElementById(id); };

    var tabs     = Array.prototype.slice.call(document.querySelectorAll('.lb-tab'));
    var panels   = Array.prototype.slice.call(document.querySelectorAll('.lb-panel'));
    var search   = $('lbSearch');
    var clearBtn = $('lbClear');
    var select   = $('lbKelas');
    var pills    = Array.prototype.slice.call(document.querySelectorAll('.lb-pill'));
    var counter  = $('lbCount');

    var GRADES = ['X', 'XI', 'XII'];

    var state = {
        tab: 'prestasi',
        grade: '',   // '', 'X', 'XI', 'XII'
        kelas: ''    // nama kelas lengkap dari dropdown
    };


    /* =====================================================
       UTIL
    ===================================================== */

    // "XII RPL 2" -> "XII", "X TKJ 1" -> "X", "XIIRPL" -> "XII"
    function gradeOf(kelas) {
        var m = String(kelas || '').trim().toUpperCase().match(/^(XII|XI|X)/);
        return m ? m[1] : '';
    }


    /* =====================================================
       FILTER (cari + tingkat + kelas), berlaku di semua papan
    ===================================================== */

    var activeVisible = 0;

    function applyFilter() {
        var q = (search.value || '').trim().toLowerCase();
        var kelasSel = state.kelas.toLowerCase();

        panels.forEach(function (panel) {
            var items = panel.querySelectorAll('.lb-item');
            var visible = 0;

            items.forEach(function (item) {
                var nama  = item.dataset.nama  || '';
                var nisn  = item.dataset.nisn  || '';
                var kelas = item.dataset.kelas || '';

                var okQ = !q ||
                    nama.indexOf(q)  !== -1 ||
                    nisn.indexOf(q)  !== -1 ||
                    kelas.indexOf(q) !== -1;

                var okGrade = !state.grade || gradeOf(kelas) === state.grade;
                var okKelas = !kelasSel || kelas.trim() === kelasSel;

                var show = okQ && okGrade && okKelas;
                item.hidden = !show;
                if (show) { visible++; }
            });

            var emptyFilter = panel.querySelector('.lb-empty-filter');
            if (emptyFilter) {
                emptyFilter.classList.toggle('is-hidden', !(items.length > 0 && visible === 0));
            }

            if (panel.id === 'panel-' + state.tab) { activeVisible = visible; }
        });

        counter.textContent = activeVisible;
        clearBtn.classList.toggle('is-hidden', !search.value);

        syncUrl();
    }

    // Simpan filter di URL agar bisa dibagikan / dimuat ulang
    function syncUrl() {
        if (!window.history || !history.replaceState) { return; }

        var params = new URLSearchParams(window.location.search);
        var q = (search.value || '').trim();
        var kelas = state.kelas || state.grade;

        if (q) { params.set('search', q); } else { params.delete('search'); }
        if (kelas) { params.set('kelas', kelas); } else { params.delete('kelas'); }

        var qs = params.toString();
        history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : '') + window.location.hash);
    }


    /* =====================================================
       TINGKAT KELAS (pill) & KELAS (dropdown)
    ===================================================== */

    function paintPills() {
        pills.forEach(function (p) {
            p.classList.toggle('active', (p.dataset.grade || '') === state.grade);
        });
    }

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            state.grade = pill.dataset.grade || '';

            // Kelas di dropdown yang tidak sesuai tingkat dilepas.
            if (state.kelas && state.grade && gradeOf(state.kelas) !== state.grade) {
                state.kelas = '';
                if (select) { select.value = ''; }
            }

            paintPills();
            applyFilter();
        });
    });

    if (select) {
        select.addEventListener('change', function () {
            state.kelas = select.value;

            // Memilih kelas otomatis menyesuaikan tingkatnya.
            state.grade = state.kelas ? gradeOf(state.kelas) : '';

            paintPills();
            applyFilter();
        });
    }


    /* =====================================================
       PENCARIAN
    ===================================================== */

    search.addEventListener('input', applyFilter);

    search.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && search.value) {
            search.value = '';
            applyFilter();
        }
    });

    clearBtn.addEventListener('click', function () {
        search.value = '';
        applyFilter();
        search.focus();
    });


    /* =====================================================
       TAB
    ===================================================== */

    function showTab(key, focus) {
        var found = tabs.some(function (t) { return t.dataset.tab === key; });
        if (!found) { key = 'prestasi'; }

        state.tab = key;

        tabs.forEach(function (t) {
            var active = t.dataset.tab === key;
            t.classList.toggle('active', active);
            t.setAttribute('aria-selected', active ? 'true' : 'false');
            t.tabIndex = active ? 0 : -1;
            if (active && focus) { t.focus(); }
        });

        panels.forEach(function (p) {
            var active = p.id === 'panel-' + key;
            p.classList.toggle('active', active);
            p.hidden = !active;
        });

        if (window.history && history.replaceState) {
            history.replaceState(null, '', window.location.pathname + window.location.search + '#' + key);
        }

        applyFilter();
    }

    tabs.forEach(function (tab, i) {
        tab.addEventListener('click', function () { showTab(tab.dataset.tab); });

        // Panah kiri/kanan berpindah tab
        tab.addEventListener('keydown', function (e) {
            var next = null;

            if (e.key === 'ArrowRight') { next = tabs[(i + 1) % tabs.length]; }
            else if (e.key === 'ArrowLeft') { next = tabs[(i - 1 + tabs.length) % tabs.length]; }
            else if (e.key === 'Home') { next = tabs[0]; }
            else if (e.key === 'End') { next = tabs[tabs.length - 1]; }

            if (next) {
                e.preventDefault();
                showTab(next.dataset.tab, true);
            }
        });
    });


    /* =====================================================
       INISIALISASI
       Dijalankan langsung jika DOM sudah siap, karena
       layout bisa memuat script ini setelah DOMContentLoaded.
    ===================================================== */

    function init() {
        // Filter awal dari URL (?kelas=XII atau ?kelas=XII RPL 2)
        var awal = @json((string) request('kelas', ''));

        if (awal) {
            var up = awal.trim().toUpperCase();

            if (GRADES.indexOf(up) !== -1) {
                state.grade = up;
            } else if (select) {
                var cocok = Array.prototype.some.call(select.options, function (o) {
                    return o.value.toLowerCase() === awal.trim().toLowerCase();
                });

                if (cocok) {
                    select.value = Array.prototype.filter.call(select.options, function (o) {
                        return o.value.toLowerCase() === awal.trim().toLowerCase();
                    })[0].value;
                    state.kelas = select.value;
                    state.grade = gradeOf(state.kelas);
                }
            }
        }

        paintPills();

        var hash = window.location.hash.replace('#', '');
        showTab(hash || 'prestasi');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

@endsection
