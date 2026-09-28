@extends('layouts.app')

@section('title', 'Leaderboard - VERITAS')
@section('page-title', 'Leaderboard')

@section('content')

<div class="page-heading">
    <div>
        <h1>Leaderboard</h1>
        <p>Peringkat siswa berdasarkan prestasi, pelanggaran, dan saldo poin.</p>
    </div>
</div>


{{-- =========================================
     TABS
========================================= --}}

<div class="leaderboard-tabs">

    <button
        type="button"
        class="leaderboard-tab active"
        data-tab="prestasi"
    >
        <i class="fa-solid fa-trophy"></i>
        Top Prestasi
    </button>

    <button
        type="button"
        class="leaderboard-tab"
        data-tab="pelanggaran"
    >
        <i class="fa-solid fa-triangle-exclamation"></i>
        Top Pelanggaran
    </button>

    <button
        type="button"
        class="leaderboard-tab"
        data-tab="saldo"
    >
        <i class="fa-solid fa-ranking-star"></i>
        Saldo Poin
    </button>

</div>


{{-- =========================================
     TOOLBAR: SEARCH & KELAS FILTER
========================================= --}}

<div class="leaderboard-toolbar">

    <div class="lb-search-wrap">
        <i class="fa-solid fa-magnifying-glass lb-search-icon"></i>
        <input
            type="text"
            id="leaderboardSearch"
            class="lb-search-input"
            placeholder="Cari nama, NISN, atau kelas siswa..."
            value="{{ request('search') }}"
            autocomplete="off"
        >
        <button type="button" id="clearSearchBtn" class="lb-clear-btn" style="display: none;" title="Hapus pencarian">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="lb-filter-wrap">
        <div class="lb-class-pills">
            <button
                type="button"
                class="lb-pill {{ !request('kelas') ? 'active' : '' }}"
                data-kelas=""
            >
                Semua Kelas
            </button>
            <button
                type="button"
                class="lb-pill {{ strtoupper(request('kelas') ?? '') === 'X' ? 'active' : '' }}"
                data-kelas="X"
            >
                Kelas X
            </button>
            <button
                type="button"
                class="lb-pill {{ strtoupper(request('kelas') ?? '') === 'XI' ? 'active' : '' }}"
                data-kelas="XI"
            >
                Kelas XI
            </button>
            <button
                type="button"
                class="lb-pill {{ strtoupper(request('kelas') ?? '') === 'XII' ? 'active' : '' }}"
                data-kelas="XII"
            >
                Kelas XII
            </button>
        </div>

        @if(isset($kelasList) && $kelasList->count() > 0)
            <div class="lb-select-wrap">
                <select id="leaderboardSelectKelas" class="lb-select-kelas">
                    <option value="">Semua Rombel</option>
                    @foreach($kelasList as $kelasOption)
                        <option
                            value="{{ $kelasOption }}"
                            {{ request('kelas') == $kelasOption ? 'selected' : '' }}
                        >
                            {{ $kelasOption }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

</div>


{{-- =========================================
     TOP PRESTASI
========================================= --}}

<div class="leaderboard-panel active" id="panel-prestasi">

    <div class="leaderboard-card">

        <div class="leaderboard-header">

            <div class="leaderboard-title">

                <div class="header-icon">
                    <i class="fa-solid fa-trophy"></i>
                </div>

                <div>
                    <h2>Top Prestasi</h2>
                    <p>Siswa dengan prestasi terbanyak.</p>
                </div>

            </div>

            <div class="header-badge">
                <i class="fa-solid fa-medal"></i>
                Prestasi
            </div>

        </div>


        @if($topPrestasi->count())

            <div class="ranking-list">

                @foreach($topPrestasi as $index => $siswa)

                    <div class="ranking-item"
                        data-nama="{{ strtolower($siswa->nama) }}"
                        data-nisn="{{ strtolower($siswa->nisn ?? '') }}"
                        data-kelas="{{ strtolower($siswa->kelas ?? '') }}">

                        <div class="rank-number
                            {{ $index === 0 ? 'rank-first' : '' }}
                            {{ $index === 1 ? 'rank-second' : '' }}
                            {{ $index === 2 ? 'rank-third' : '' }}
                        ">
                            {{ $index + 1 }}
                        </div>


                        <div class="student-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>


                        <div class="student-info">

                            <div class="student-name-row">
                                <strong>
                                    {{ $siswa->nama }}
                                </strong>
                                @if($siswa->kelas)
                                    <span class="student-kelas-badge">{{ $siswa->kelas }}</span>
                                @else
                                    <span class="student-kelas-badge empty">Tanpa Kelas</span>
                                @endif
                            </div>

                            <span>
                                NISN: {{ $siswa->nisn ?? '-' }}
                            </span>

                        </div>


                        <div class="ranking-stat">

                            <strong>
                                {{ $siswa->prestasis_count }}
                            </strong>

                            <span>
                                prestasi
                            </span>

                        </div>


                        <div class="ranking-points">

                            <strong>
                                +{{ $siswa->prestasis_sum_poin ?? 0 }}
                            </strong>

                            <span>
                                total poin
                            </span>

                        </div>


                        <a
                            href="{{ route('siswa.show', $siswa->id) }}"
                            class="view-student"
                            title="Lihat siswa"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                    </div>

                @endforeach

            </div>

            <div class="empty-state empty-filter-state" style="display: none;">
                <div class="empty-icon" style="background: #F3F4F6; color: #6B7280;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <strong>Tidak Ada Siswa Ditemukan</strong>
                <span>Tidak ada siswa yang sesuai dengan filter atau kata kunci pencarian.</span>
            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon">
                    <i class="fa-solid fa-trophy"></i>
                </div>

                <strong>Belum ada data prestasi</strong>

                <span>
                    Data siswa yang memiliki prestasi akan muncul di sini.
                </span>

            </div>

        @endif

    </div>

</div>


{{-- =========================================
     TOP PELANGGARAN
========================================= --}}

<div class="leaderboard-panel" id="panel-pelanggaran">

    <div class="leaderboard-card">

        <div class="leaderboard-header">

            <div class="leaderboard-title">

                <div class="header-icon violation-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div>
                    <h2>Top Pelanggaran</h2>
                    <p>Siswa dengan pelanggaran terbanyak.</p>
                </div>

            </div>

            <div class="header-badge violation-badge">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Pelanggaran
            </div>

        </div>


        @if($topPelanggaran->count())

            <div class="ranking-list">

                @foreach($topPelanggaran as $index => $siswa)

                    <div class="ranking-item"
                        data-nama="{{ strtolower($siswa->nama) }}"
                        data-nisn="{{ strtolower($siswa->nisn ?? '') }}"
                        data-kelas="{{ strtolower($siswa->kelas ?? '') }}">

                        <div class="rank-number
                            {{ $index === 0 ? 'rank-first' : '' }}
                            {{ $index === 1 ? 'rank-second' : '' }}
                            {{ $index === 2 ? 'rank-third' : '' }}
                        ">
                            {{ $index + 1 }}
                        </div>


                        <div class="student-avatar violation-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>


                        <div class="student-info">

                            <div class="student-name-row">
                                <strong>
                                    {{ $siswa->nama }}
                                </strong>
                                @if($siswa->kelas)
                                    <span class="student-kelas-badge">{{ $siswa->kelas }}</span>
                                @else
                                    <span class="student-kelas-badge empty">Tanpa Kelas</span>
                                @endif
                            </div>

                            <span>
                                NISN: {{ $siswa->nisn ?? '-' }}
                            </span>

                        </div>


                        <div class="ranking-stat">

                            <strong>
                                {{ $siswa->pelanggarans_count }}
                            </strong>

                            <span>
                                pelanggaran
                            </span>

                        </div>


                        <div class="ranking-points violation-points">

                            <strong>
                                -{{ $siswa->pelanggarans_sum_poin ?? 0 }}
                            </strong>

                            <span>
                                total poin
                            </span>

                        </div>


                        <a
                            href="{{ route('siswa.show', $siswa->id) }}"
                            class="view-student"
                            title="Lihat siswa"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                    </div>

                @endforeach

            </div>

            <div class="empty-state empty-filter-state" style="display: none;">
                <div class="empty-icon" style="background: #F3F4F6; color: #6B7280;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <strong>Tidak Ada Siswa Ditemukan</strong>
                <span>Tidak ada siswa yang sesuai dengan filter atau kata kunci pencarian.</span>
            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon violation-empty">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <strong>Belum ada data pelanggaran</strong>

                <span>
                    Data siswa yang memiliki pelanggaran akan muncul di sini.
                </span>

            </div>

        @endif

    </div>

</div>


{{-- =========================================
     SALDO POIN
========================================= --}}

<div class="leaderboard-panel" id="panel-saldo">

    <div class="leaderboard-card">

        <div class="leaderboard-header">

            <div class="leaderboard-title">

                <div class="header-icon">
                    <i class="fa-solid fa-ranking-star"></i>
                </div>

                <div>
                    <h2>Saldo Poin</h2>
                    <p>Peringkat siswa berdasarkan saldo poin.</p>
                </div>

            </div>

            <div class="header-badge">
                <i class="fa-solid fa-star"></i>
                Saldo
            </div>

        </div>


        @if($saldoSiswa->count())

            <div class="ranking-list">

                @foreach($saldoSiswa as $index => $siswa)

                    <div class="ranking-item"
                        data-nama="{{ strtolower($siswa->nama) }}"
                        data-nisn="{{ strtolower($siswa->nisn ?? '') }}"
                        data-kelas="{{ strtolower($siswa->kelas ?? '') }}">

                        <div class="rank-number
                            {{ $index === 0 ? 'rank-first' : '' }}
                            {{ $index === 1 ? 'rank-second' : '' }}
                            {{ $index === 2 ? 'rank-third' : '' }}
                        ">
                            {{ $index + 1 }}
                        </div>


                        <div class="student-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>


                        <div class="student-info">

                            <div class="student-name-row">
                                <strong>
                                    {{ $siswa->nama }}
                                </strong>
                                @if($siswa->kelas)
                                    <span class="student-kelas-badge">{{ $siswa->kelas }}</span>
                                @else
                                    <span class="student-kelas-badge empty">Tanpa Kelas</span>
                                @endif
                            </div>

                            <span>
                                NISN: {{ $siswa->nisn ?? '-' }}
                            </span>

                        </div>


                        <div class="saldo-detail">

                            <span>
                                <i class="fa-solid fa-trophy"></i>
                                {{ $siswa->prestasis_sum_poin ?? 0 }}
                            </span>

                            <span class="saldo-minus">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                {{ $siswa->pelanggarans_sum_poin ?? 0 }}
                            </span>

                        </div>


                        <div class="saldo-points">

                            <strong>
                                {{ $siswa->saldo_poin }}
                            </strong>

                            <span>
                                saldo poin
                            </span>

                        </div>


                        <a
                            href="{{ route('siswa.show', $siswa->id) }}"
                            class="view-student"
                            title="Lihat siswa"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                    </div>

                @endforeach

            </div>

            <div class="empty-state empty-filter-state" style="display: none;">
                <div class="empty-icon" style="background: #F3F4F6; color: #6B7280;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <strong>Tidak Ada Siswa Ditemukan</strong>
                <span>Tidak ada siswa yang sesuai dengan filter atau kata kunci pencarian.</span>
            </div>


            <div class="formula-box">

                <i class="fa-solid fa-calculator"></i>

                <span>
                    Saldo poin dihitung berdasarkan:
                    <strong>
                        100 - total pelanggaran + total prestasi
                    </strong>
                </span>

            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon">
                    <i class="fa-solid fa-ranking-star"></i>
                </div>

                <strong>Belum ada data siswa</strong>

                <span>
                    Data saldo poin siswa akan muncul di sini.
                </span>

            </div>

        @endif

    </div>

</div>


<style>

/* ========================================
   PAGE HEADING
======================================== */

.page-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}

.page-heading h1 {
    margin: 0 0 6px;
    font-size: clamp(21px, 3.2vw, 26px);
    font-weight: 700;
    color: #1F2937;
    overflow-wrap: anywhere;
}

.page-heading p {
    margin: 0;
    color: #6B7280;
    font-size: 14px;
    overflow-wrap: anywhere;
}


/* ========================================
   TABS
======================================== */

.leaderboard-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    padding: 5px;
    background: #F3F4F6;
    border-radius: 11px;
    margin-bottom: 20px;
    width: fit-content;
    max-width: 100%;
}

.leaderboard-tab {
    height: 40px;
    padding: 0 15px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #6B7280;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: .2s;
}

.leaderboard-tab:hover {
    color: #6D1408;
}

.leaderboard-tab.active {
    background: #fff;
    color: #6D1408;
    box-shadow: 0 2px 7px rgba(0,0,0,.06);
}

.leaderboard-tab i {
    font-size: 13px;
}


/* ========================================
   PANEL
======================================== */

.leaderboard-panel {
    display: none;
}

.leaderboard-panel.active {
    display: block;
}


/* ========================================
   CARD
======================================== */

.leaderboard-card {
    background: #fff;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,.04);
}


/* ========================================
   HEADER
======================================== */

.leaderboard-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 12px 15px;
    padding: 20px 22px;
    background: #FFFCFA;
    border-bottom: 1px solid #E5E7EB;
}

.leaderboard-title {
    display: flex;
    align-items: center;
    gap: 13px;
    flex: 1 1 220px;
    min-width: 0;
}

.leaderboard-title > div:last-child {
    min-width: 0;
}

.header-icon {
    width: 44px;
    height: 44px;
    border-radius: 11px;
    background: #F9E9E6;
    color: #6D1408;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.leaderboard-title h2 {
    margin: 0 0 4px;
    font-size: 17px;
    color: #1F2937;
}

.leaderboard-title p {
    margin: 0;
    font-size: 12px;
    color: #6B7280;
}

.header-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 10px;
    border-radius: 7px;
    background: #F9E9E6;
    color: #6D1408;
    font-size: 11px;
    font-weight: 600;
}

.violation-icon {
    background: #FEF2F2;
    color: #B42318;
}

.violation-badge {
    background: #FEF2F2;
    color: #B42318;
}


/* ========================================
   RANKING LIST
======================================== */

.ranking-list {
    padding: 8px 22px 14px;
}

.ranking-item {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 14px 0;
    border-bottom: 1px solid #F0F0F0;
}

.ranking-item:last-child {
    border-bottom: none;
}


/* ========================================
   RANK
======================================== */

.rank-number {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #F3F4F6;
    color: #6B7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
}

.rank-first {
    background: #F9E9E6;
    color: #6D1408;
}

.rank-second {
    background: #F3F4F6;
    color: #4B5563;
}

.rank-third {
    background: #F6F1EC;
    color: #8A5A3B;
}


/* ========================================
   STUDENT
======================================== */

.student-avatar {
    width: 39px;
    height: 39px;
    border-radius: 10px;
    background: #F9E9E6;
    color: #6D1408;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.violation-avatar {
    background: #FEF2F2;
    color: #B42318;
}

.student-info {
    flex: 1;
    min-width: 0;
}

.student-info strong {
    display: block;
    margin-bottom: 3px;
    color: #1F2937;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.student-info span {
    display: block;
    color: #9CA3AF;
    font-size: 11px;
}


/* ========================================
   STAT
======================================== */

.ranking-stat {
    min-width: 88px;
    text-align: center;
}

.ranking-stat strong {
    display: block;
    color: #374151;
    font-size: 15px;
    font-weight: 700;
}

.ranking-stat span {
    color: #9CA3AF;
    font-size: 10px;
}


/* ========================================
   POINTS
======================================== */

.ranking-points {
    min-width: 96px;
    text-align: right;
}

.ranking-points strong {
    display: block;
    color: #287A3D;
    font-size: 15px;
    font-weight: 700;
}

.ranking-points span {
    color: #9CA3AF;
    font-size: 10px;
}

.violation-points strong {
    color: #B42318;
}


/* ========================================
   SALDO
======================================== */

.saldo-detail {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 112px;
}

.saldo-detail span {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #287A3D;
    font-size: 11px;
    font-weight: 600;
}

.saldo-detail span i {
    font-size: 9px;
}

.saldo-detail .saldo-minus {
    color: #B42318;
}

.saldo-points {
    min-width: 96px;
    text-align: right;
}

.saldo-points strong {
    display: block;
    color: #6D1408;
    font-size: 18px;
    font-weight: 700;
}

.saldo-points span {
    color: #9CA3AF;
    font-size: 10px;
}


/* ========================================
   VIEW
======================================== */

.view-student {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #F3F4F6;
    color: #6B7280;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    flex-shrink: 0;
    transition: .2s;
}

.view-student:hover {
    background: #F9E9E6;
    color: #6D1408;
}


/* ========================================
   FORMULA
======================================== */

.formula-box {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 8px;
    margin: 4px 22px 20px;
    padding: 12px;
    border-radius: 8px;
    background: #F9FAFB;
    color: #6B7280;
    font-size: 11px;
    text-align: center;
    overflow-wrap: anywhere;
}

.formula-box i {
    color: #6D1408;
}

.formula-box strong {
    color: #374151;
}


/* ========================================
   EMPTY
======================================== */

.empty-state {
    min-height: 260px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 30px 20px;
}

.empty-icon {
    width: 55px;
    height: 55px;
    border-radius: 13px;
    background: #F9E9E6;
    color: #6D1408;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
    margin-bottom: 13px;
}

.violation-empty {
    background: #FEF2F2;
    color: #B42318;
}

.empty-state strong {
    color: #374151;
    font-size: 14px;
    margin-bottom: 5px;
}

.empty-state span {
    color: #9CA3AF;
    font-size: 12px;
    max-width: 42ch;
    overflow-wrap: anywhere;
}


/* ========================================
   RESPONSIVE
======================================== */

@media (max-width: 800px) {

    .ranking-stat {
        width: 75px;
    }

    .ranking-points {
        width: 80px;
    }

    .saldo-detail {
        width: 95px;
        gap: 7px;
    }

}


@media (max-width: 650px) {

    .leaderboard-tabs {
        width: 100%;
        box-sizing: border-box;
    }

    .leaderboard-tab {
        flex: 1 1 auto;
        white-space: nowrap;
        padding: 0 12px;
    }

    .ranking-list {
        padding-left: 16px;
        padding-right: 16px;
    }

    .ranking-item {
        gap: 9px;
    }

    .ranking-stat {
        width: auto;
        min-width: 65px;
    }

    .ranking-points {
        width: auto;
        min-width: 70px;
    }

    .saldo-detail {
        width: auto;
        min-width: 75px;
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
    }

    .saldo-points {
        width: auto;
        min-width: 65px;
    }

}


@media (max-width: 500px) {

    .leaderboard-header {
        align-items: flex-start;
        padding: 17px;
    }

    .header-badge {
        display: none;
    }

    .leaderboard-title h2 {
        font-size: 15px;
    }

    .leaderboard-title p {
        font-size: 11px;
    }

    .ranking-item {
        flex-wrap: wrap;
        padding: 13px 0;
    }

    .rank-number {
        width: 28px;
        height: 28px;
    }

    .student-avatar {
        width: 36px;
        height: 36px;
    }

    .student-info {
        width: calc(100% - 90px);
    }

    .ranking-stat {
        margin-left: 37px;
        text-align: left;
    }

    .ranking-points {
        text-align: left;
    }

    .saldo-detail {
        margin-left: 37px;
    }

    .saldo-points {
        text-align: left;
    }

    .view-student {
        margin-left: auto;
    }

    .formula-box {
        margin-left: 16px;
        margin-right: 16px;
    }

    .leaderboard-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .lb-filter-wrap {
        flex-direction: column;
        align-items: stretch;
    }

    .lb-class-pills {
        width: 100%;
    }

    .lb-select-kelas {
        width: 100%;
    }

}

/* ========================================
   LEADERBOARD TOOLBAR & FILTER
======================================== */

.leaderboard-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 20px;
    background: #ffffff;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    padding: 12px 16px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
}

.lb-search-wrap {
    position: relative;
    flex: 1 1 220px;
    min-width: min(250px, 100%);
}

.lb-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
    font-size: 13px;
    pointer-events: none;
}

.lb-search-input {
    width: 100%;
    height: 40px;
    padding: 0 36px 0 38px;
    border: 1px solid #E5E7EB;
    border-radius: 9px;
    background: #F9FAFB;
    font-size: 13px;
    font-family: inherit;
    color: #1F2937;
    outline: none;
    transition: all 0.2s ease;
}

.lb-search-input:focus {
    background: #ffffff;
    border-color: #6D1408;
    box-shadow: 0 0 0 3px rgba(109, 20, 8, 0.12);
}

.lb-clear-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9CA3AF;
    font-size: 13px;
    cursor: pointer;
    padding: 4px 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.lb-clear-btn:hover {
    color: #6D1408;
    background: #F3F4F6;
}

.lb-filter-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    flex: 1 1 320px;
    min-width: 0;
}

.lb-class-pills {
    display: flex;
    flex-wrap: wrap;
    background: #F3F4F6;
    padding: 4px;
    border-radius: 9px;
    gap: 4px;
    max-width: 100%;
}

.lb-pill {
    border: none;
    background: transparent;
    padding: 6px 13px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    color: #4B5563;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
    white-space: nowrap;
}

.lb-pill:hover {
    color: #6D1408;
}

.lb-pill.active {
    background: #6D1408;
    color: #ffffff;
    box-shadow: 0 2px 5px rgba(109, 20, 8, 0.2);
}

.lb-select-wrap {
    position: relative;
    min-width: 0;
    max-width: 100%;
}

.lb-select-kelas {
    height: 38px;
    max-width: 100%;
    padding: 0 12px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    background: #F9FAFB;
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    font-family: inherit;
    cursor: pointer;
    outline: none;
    transition: border-color 0.2s;
}

.lb-select-kelas:focus {
    border-color: #6D1408;
}

.student-name-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 2px;
    flex-wrap: wrap;
}

.student-name-row strong {
    margin-bottom: 0 !important;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}

.student-kelas-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 7px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    background: #FBEAE8;
    color: #6D1408;
    border: 1px solid #E8C2BD;
    white-space: nowrap;
}

.student-kelas-badge.empty {
    background: #F3F4F6;
    color: #6B7280;
    border: 1px solid #E5E7EB;
}


/* ========================================
   RESPONSIVE REFINEMENTS
   ======================================== */

@media (max-width: 1024px) {

    .lb-search-wrap {
        flex: 1 1 100%;
    }

    .ranking-list {
        padding: 8px 18px 14px;
    }

    .ranking-item {
        gap: 11px;
    }

}

@media (max-width: 768px) {

    .leaderboard-toolbar {
        padding: 12px;
    }

    .lb-search-input {
        font-size: 16px;
        height: 44px;
    }

    .lb-select-kelas {
        font-size: 16px;
        height: 44px;
    }

    .lb-pill {
        flex: 1 1 auto;
        text-align: center;
        font-size: 13px;
        padding: 8px 12px;
    }

}

@media (max-width: 640px) {

    .page-heading h1 {
        font-size: clamp(19px, 3vw, 22px);
    }

    .page-heading p {
        font-size: 13px;
    }

    .leaderboard-card {
        border-radius: 12px;
    }

    .leaderboard-header {
        padding: 15px 14px;
    }

    .ranking-list {
        padding: 6px 14px 12px;
    }

    .ranking-stat,
    .ranking-points,
    .saldo-detail,
    .saldo-points {
        min-width: 0;
    }

    .formula-box {
        margin: 4px 14px 16px;
    }

    .empty-state {
        min-height: 200px;
        padding: 24px 16px;
    }

}

@media (max-width: 576px) {

    .leaderboard-tabs {
        gap: 4px;
    }

    .leaderboard-tab {
        flex: 1 1 100%;
        height: 38px;
    }

    /* Ranking item -> 2 baris: identitas di atas, angka di bawah.
       Memakai flex + pseudo-element pemutus baris agar tetap bekerja
       meski JS memasang display:flex inline saat filter berjalan. */

    .ranking-item {
        flex-wrap: wrap;
        align-items: center;
        column-gap: 9px;
        row-gap: 6px;
        padding: 12px 0;
    }

    .ranking-item::before {
        content: "";
        flex: 0 0 100%;
        height: 0;
        order: 1;
    }

    .rank-number,
    .student-avatar,
    .student-info,
    .view-student {
        order: 0;
    }

    .ranking-stat,
    .saldo-detail,
    .ranking-points,
    .saldo-points {
        order: 2;
    }

    .rank-number {
        width: 28px;
        height: 28px;
    }

    .student-avatar {
        width: 34px;
        height: 34px;
    }

    .student-info {
        width: auto;
        flex: 1 1 0;
        min-width: 0;
    }

    .ranking-stat,
    .saldo-detail {
        min-width: 0;
        margin-left: 0;
        flex: 1 1 auto;
        text-align: left;
    }

    .ranking-points,
    .saldo-points {
        min-width: 66px;
        text-align: left;
    }

    .view-student {
        margin-left: auto;
    }

    .saldo-detail {
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
    }

}

@media (max-width: 480px) {

    .leaderboard-title h2 {
        font-size: 15px;
    }

    .leaderboard-title p {
        font-size: 11px;
    }

    .header-icon {
        width: 38px;
        height: 38px;
        font-size: 16px;
    }

    .ranking-item {
        column-gap: 8px;
    }

    .rank-number {
        width: 26px;
        height: 26px;
        font-size: 11px;
    }

    .student-avatar {
        width: 32px;
        height: 32px;
    }

    .view-student {
        width: 30px;
        height: 30px;
    }

    .saldo-points strong {
        font-size: 16px;
    }

    .empty-state {
        min-height: 170px;
    }

}

@media (max-width: 400px) {

    .page-heading h1 {
        font-size: 19px;
    }

    .lb-pill {
        font-size: 12px;
        padding: 8px 8px;
    }

    .ranking-list {
        padding: 6px 10px 10px;
    }

    .ranking-item {
        column-gap: 7px;
    }

    .rank-number {
        width: 24px;
        height: 24px;
    }

    .student-avatar {
        width: 30px;
        height: 30px;
    }

    .view-student {
        width: 28px;
        height: 28px;
    }

    .formula-box {
        margin: 4px 10px 14px;
        text-align: left;
    }

}

</style>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const tabs = document.querySelectorAll('.leaderboard-tab');
    const panels = document.querySelectorAll('.leaderboard-panel');
    const searchInput = document.getElementById('leaderboardSearch');
    const clearBtn = document.getElementById('clearSearchBtn');
    const pillButtons = document.querySelectorAll('.lb-pill');
    const selectKelas = document.getElementById('leaderboardSelectKelas');

    let activeGradeFilter = '';
    let activeSpecificClass = '';

    // Inisialisasi awal nilai dari tombol active
    const defaultActivePill = document.querySelector('.lb-pill.active');
    if (defaultActivePill) {
        activeGradeFilter = defaultActivePill.dataset.kelas || '';
    }
    if (selectKelas && selectKelas.value) {
        activeSpecificClass = selectKelas.value;
    }

    // Tab navigasi
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const target = this.dataset.tab;

            tabs.forEach(item => item.classList.remove('active'));
            panels.forEach(panel => panel.classList.remove('active'));

            this.classList.add('active');

            const panel = document.getElementById(`panel-${target}`);
            if (panel) {
                panel.classList.add('active');
            }

            applyFilters();
        });
    });

    // Helper: Ekstraksi tingkatan kelas (XII, XI, X)
    function extractGrade(kelasStr) {
        if (!kelasStr) return '';
        let k = kelasStr.trim().toLowerCase();
        if (k.startsWith('kelas ')) {
            k = k.substring(6).trim();
        }
        if (k.startsWith('xii') || k.startsWith('12')) return 'XII';
        if (k.startsWith('xi') || k.startsWith('11')) return 'XI';
        if (k.startsWith('x') || k.startsWith('10')) return 'X';
        return '';
    }

    // Helper: Cek kecocokan kelas siswa dengan filter
    function checkClassMatch(studentKelas, filterGrade, specificClass) {
        if (!studentKelas) studentKelas = '';
        const lowerK = studentKelas.trim().toLowerCase();

        // 1. Jika rombel spesifik dipilih dari dropdown
        if (specificClass) {
            return lowerK === specificClass.trim().toLowerCase();
        }

        // 2. Jika filter pil tingkatan kelas dipilih ('X', 'XI', 'XII')
        if (filterGrade) {
            const studentGrade = extractGrade(studentKelas);
            return studentGrade === filterGrade;
        }

        // 3. 'Semua Kelas'
        return true;
    }

    // Fungsi utama filter real-time
    function applyFilters() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';

        // Tampilkan/sembunyikan tombol clear search
        if (clearBtn) {
            clearBtn.style.display = query ? 'flex' : 'none';
        }

        panels.forEach(panel => {
            const items = panel.querySelectorAll('.ranking-item');
            const rankingList = panel.querySelector('.ranking-list');
            const emptyFilterState = panel.querySelector('.empty-filter-state');

            if (items.length === 0) {
                return;
            }

            let visibleCount = 0;

            items.forEach(item => {
                const nama = item.dataset.nama || '';
                const nisn = item.dataset.nisn || '';
                const kelas = item.dataset.kelas || '';

                const matchesSearch = !query ||
                    nama.includes(query) ||
                    nisn.includes(query) ||
                    kelas.includes(query);

                const matchesClass = checkClassMatch(kelas, activeGradeFilter, activeSpecificClass);

                if (matchesSearch && matchesClass) {
                    item.style.display = 'flex';
                    visibleCount++;

                    // Update nomor peringkat dinamis sesuai hasil filter
                    const rankEl = item.querySelector('.rank-number');
                    if (rankEl) {
                        rankEl.textContent = visibleCount;
                        rankEl.classList.remove('rank-first', 'rank-second', 'rank-third');
                        if (visibleCount === 1) rankEl.classList.add('rank-first');
                        else if (visibleCount === 2) rankEl.classList.add('rank-second');
                        else if (visibleCount === 3) rankEl.classList.add('rank-third');
                    }
                } else {
                    item.style.display = 'none';
                }
            });

            // Tampilkan empty state jika pencarian tidak menemukan hasil
            if (emptyFilterState) {
                if (visibleCount === 0) {
                    emptyFilterState.style.display = 'flex';
                    if (rankingList) rankingList.style.display = 'none';
                } else {
                    emptyFilterState.style.display = 'none';
                    if (rankingList) rankingList.style.display = '';
                }
            }
        });
    }

    // Event listener search input
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    // Event listener clear search button
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        });
    }

    // Event listener pil kelas (Semua, X, XI, XII)
    pillButtons.forEach(pill => {
        pill.addEventListener('click', function () {
            pillButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            activeGradeFilter = this.dataset.kelas || '';
            activeSpecificClass = '';

            if (selectKelas) {
                selectKelas.value = '';
            }

            applyFilters();
        });
    });

    // Event listener select dropdown rombel spesifik
    if (selectKelas) {
        selectKelas.addEventListener('change', function () {
            activeSpecificClass = this.value;

            if (activeSpecificClass) {
                pillButtons.forEach(b => b.classList.remove('active'));
                activeGradeFilter = '';
            } else {
                // Kembalikan ke pil pertama (Semua)
                const firstPill = pillButtons[0];
                if (firstPill) {
                    firstPill.classList.add('active');
                    activeGradeFilter = firstPill.dataset.kelas || '';
                }
            }

            applyFilters();
        });
    }

    // Jalankan filter saat halaman selesai dimuat
    applyFilters();

});

</script>

@endsection
