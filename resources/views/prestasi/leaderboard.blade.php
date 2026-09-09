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

                    <div class="ranking-item">

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

                            <strong>
                                {{ $siswa->nama }}
                            </strong>

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

                    <div class="ranking-item">

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

                            <strong>
                                {{ $siswa->nama }}
                            </strong>

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

                    <div class="ranking-item">

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

                            <strong>
                                {{ $siswa->nama }}
                            </strong>

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
    font-size: 26px;
    font-weight: 700;
    color: #1F2937;
}

.page-heading p {
    margin: 0;
    color: #6B7280;
    font-size: 14px;
}


/* ========================================
   TABS
======================================== */

.leaderboard-tabs {
    display: flex;
    gap: 7px;
    padding: 5px;
    background: #F3F4F6;
    border-radius: 11px;
    margin-bottom: 20px;
    width: fit-content;
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
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding: 20px 22px;
    background: #FFFCFA;
    border-bottom: 1px solid #E5E7EB;
}

.leaderboard-title {
    display: flex;
    align-items: center;
    gap: 13px;
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
    width: 90px;
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
    width: 100px;
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
    width: 115px;
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
    width: 100px;
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
    gap: 8px;
    margin: 4px 22px 20px;
    padding: 12px;
    border-radius: 8px;
    background: #F9FAFB;
    color: #6B7280;
    font-size: 11px;
    text-align: center;
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
        overflow-x: auto;
        box-sizing: border-box;
    }

    .leaderboard-tab {
        flex: 1;
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

}

</style>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const tabs = document.querySelectorAll('.leaderboard-tab');
    const panels = document.querySelectorAll('.leaderboard-panel');


    tabs.forEach(tab => {

        tab.addEventListener('click', function () {

            const target = this.dataset.tab;


            /* Hapus active dari semua tab */

            tabs.forEach(item => {
                item.classList.remove('active');
            });


            /* Hapus active dari semua panel */

            panels.forEach(panel => {
                panel.classList.remove('active');
            });


            /* Aktifkan tab */

            this.classList.add('active');


            /* Aktifkan panel */

            const panel = document.getElementById(
                `panel-${target}`
            );

            if (panel) {
                panel.classList.add('active');
            }

        });

    });

});

</script>

@endsection
