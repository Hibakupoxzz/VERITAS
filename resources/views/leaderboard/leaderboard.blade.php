@extends('layouts.app')

@section('title', 'Leaderboard')
@section('page_title', 'Leaderboard')

@section('styles')


@include('partials.ui')

/* =====================================================
       LEADERBOARD PAGE
    ====================================================== */

    .leaderboard-page {
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }


    /* =====================================================
       HEADER
    ====================================================== */

    .leaderboard-hero {
        background: linear-gradient(
            135deg,
            var(--primary) 0%,
            #8f1d0d 100%
        );

        border-radius: 20px;

        padding: clamp(18px, 2.4vw, 30px);

        color: #fff;

        margin-bottom: 24px;

        position: relative;

        overflow: hidden;

        box-shadow:
            0 8px 25px rgba(109,20,8,.15);
    }


    .leaderboard-hero::before {
        content: '';

        position: absolute;

        width: 200px;
        height: 200px;

        border-radius: 50%;

        background: rgba(255,255,255,.05);

        right: -60px;
        top: -80px;
    }


    .leaderboard-hero::after {
        content: '';

        position: absolute;

        width: 130px;
        height: 130px;

        border-radius: 50%;

        background: rgba(255,255,255,.04);

        right: 100px;
        bottom: -90px;
    }


    .hero-content {
        position: relative;

        z-index: 2;
    }


    .hero-icon {
        width: clamp(38px, 3.4vw, 48px);
        height: clamp(38px, 3.4vw, 48px);

        border-radius: 14px;

        background: rgba(255,255,255,.12);

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: clamp(15px, 1.5vw, 21px);

        margin-bottom: 14px;
    }


    .hero-title {
        margin: 0;

        font-size: clamp(19px, 2.2vw, 27px);

        font-weight: 800;

        overflow-wrap: anywhere;
    }


    .hero-description {
        margin: 7px 0 0;

        font-size: 13px;

        opacity: .88;

        line-height: 1.6;

        max-width: 70ch;
    }


    /* =====================================================
       TAB
    ====================================================== */

    .leaderboard-tabs {
        display: flex;
        flex-wrap: wrap;

        gap: 8px;

        margin-bottom: 18px;

        background: #fff;

        border: 1px solid #E5E7EB;

        border-radius: 14px;

        padding: 6px;

        width: fit-content;

        max-width: 100%;
    }


    .leaderboard-tab {
        border: none;

        background: transparent;

        color: #6B7280;

        padding: 10px 15px;

        border-radius: 9px;

        font-size: 12px;

        font-weight: 700;

        cursor: pointer;

        transition: .2s ease;
    }


    .leaderboard-tab:hover {
        background: #F3F4F6;

        color: #1F2937;
    }


    .leaderboard-tab.active {
        background: var(--primary);

        color: #fff;
    }


    /* =====================================================
       CONTENT
    ====================================================== */

    .leaderboard-panel {
        display: none;
    }


    .leaderboard-panel.active {
        display: block;
    }


    /* =====================================================
       TOP 3
    ====================================================== */

    .top-three {
        display: grid;

        grid-template-columns:
            repeat(auto-fit, minmax(min(230px, 100%), 1fr));

        gap: 18px;

        margin-bottom: 20px;
    }


    .top-card {
        background: #fff;

        border: 1px solid #E5E7EB;

        border-radius: 18px;

        padding: clamp(14px, 1.8vw, 22px);

        text-align: center;

        box-shadow:
            0 5px 20px rgba(0,0,0,.04);

        position: relative;

        min-width: 0;
    }


    .top-card.first {
        border-color: #E5C66B;
    }


    .top-card.second {
        border-color: #CBD0D6;
    }


    .top-card.third {
        border-color: #D4A98C;
    }


    .top-medal {
        font-size: clamp(22px, 2.4vw, 27px);

        margin-bottom: 9px;
    }


    .top-rank {
        font-size: 11px;

        color: #9CA3AF;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: .5px;
    }


    .top-name {
        margin-top: 8px;

        font-size: 15px;

        font-weight: 800;

        color: #1F2937;

        min-width: 0;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;
    }


    .top-class {
        color: #9CA3AF;

        font-size: 11px;

        margin-top: 3px;

        overflow-wrap: anywhere;
    }


    .top-points {
        margin-top: 13px;

        font-size: clamp(20px, 2.1vw, 24px);

        font-weight: 800;

        color: #15803D;

        overflow-wrap: anywhere;
    }


    .top-points-label {
        color: #9CA3AF;

        font-size: 10px;

        margin-top: 2px;
    }


    /* =====================================================
       TABLE CARD
    ====================================================== */

    .leaderboard-table-card {
        background: #fff;

        border: 1px solid #E5E7EB;

        border-radius: 18px;

        box-shadow:
            0 5px 20px rgba(0,0,0,.04);

        overflow: hidden;
    }


    .table-header {
        padding: 18px 20px;

        border-bottom: 1px solid #F0F1F3;

        display: flex;
        flex-wrap: wrap;

        justify-content: space-between;

        align-items: center;

        gap: 10px;
    }


    .table-title {
        color: #1F2937;

        font-size: 15px;

        font-weight: 800;

        overflow-wrap: anywhere;
    }


    .table-subtitle {
        color: #9CA3AF;

        font-size: 11px;

        margin-top: 3px;

        overflow-wrap: anywhere;
    }


    .table-wrapper {
        width: 100%;
        max-width: 100%;

        overflow-x: auto;

        -webkit-overflow-scrolling: touch;
    }


    .leaderboard-table {
        width: 100%;

        border-collapse: collapse;

        min-width: 650px;
    }


    .leaderboard-table th {
        background: #F9FAFB;

        color: #6B7280;

        font-size: 10px;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: .4px;

        padding: 12px 18px;

        text-align: left;

        white-space: nowrap;
    }


    .leaderboard-table td {
        padding: 13px 18px;

        border-top: 1px solid #F3F4F6;

        color: #374151;

        font-size: 12px;
    }


    .rank-box {
        width: 31px;
        height: 31px;

        display: flex;

        align-items: center;
        justify-content: center;

        border-radius: 9px;

        background: #F3F4F6;

        color: #6B7280;

        font-size: 11px;

        font-weight: 800;
    }


    .rank-box.rank-1 {
        background: #FEF3C7;

        color: #B45309;
    }


    .rank-box.rank-2 {
        background: #E5E7EB;

        color: #4B5563;
    }


    .rank-box.rank-3 {
        background: #F3E8E1;

        color: #92400E;
    }


    .student-cell {
        display: flex;

        align-items: center;

        gap: 11px;

        min-width: 0;
    }

.student-name-table {
        font-weight: 700;

        color: #1F2937;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;
    }


    .student-class-table {
        color: #9CA3AF;

        font-size: 10px;

        margin-top: 2px;
    }

.point-negative {
        color: #B91C1C;

        font-weight: 800;
    }


    .activity-count {
        color: #6B7280;

        font-weight: 600;
    }


    /* =====================================================
       SALDO
    ====================================================== */

    .saldo-value {
        color: var(--primary);

        font-size: 15px;

        font-weight: 800;
    }


    .saldo-positive {
        color: #15803D;
    }


    .saldo-negative {
        color: #B91C1C;
    }


    /* =====================================================
       EMPTY
    ====================================================== */    .empty-state i {
        display: block;

        font-size: 30px;

        margin-bottom: 10px;
    }


    .empty-state p {
        margin: 0;

        font-size: 12px;
    }


    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 800px) {

        .top-three {
            grid-template-columns: 1fr;
        }

        .top-card {
            padding: 18px;
        }

    }


    @media (max-width: 768px) {

        .leaderboard-table {
            min-width: 520px;
        }

        .leaderboard-table th {
            padding: 10px 12px;
        }

        .leaderboard-table td {
            padding: 11px 12px;
        }

        .leaderboard-tabs {
            width: 100%;
        }

        .leaderboard-tab {
            white-space: nowrap;

            flex: 1 1 auto;
        }

    }


    @media (max-width: 600px) {

        .leaderboard-hero {
            padding: 20px 18px;

            border-radius: 16px;
        }


        .hero-title {
            font-size: 21px;
        }


        .hero-description {
            font-size: 12px;
        }


        .leaderboard-table-card {
            border-radius: 15px;
        }


        .table-header {
            padding: 14px;
        }

    }


    @media (max-width: 480px) {

        .leaderboard-hero {
            padding: 16px 14px;

            margin-bottom: 18px;
        }

        .hero-icon {
            width: 38px;
            height: 38px;

            font-size: 15px;

            margin-bottom: 10px;
        }

        .hero-title {
            font-size: 19px;
        }

        .top-three {
            gap: 12px;
        }

        .top-card {
            padding: 14px 12px;
        }

        .top-points {
            margin-top: 10px;
        }

        .leaderboard-table {
            min-width: 440px;
        }

        .leaderboard-table th {
            padding: 9px 9px;
        }

        .leaderboard-table td {
            padding: 10px 9px;
        }

        .rank-box {
            width: 27px;
            height: 27px;
        }

        .empty-state {
            padding: 34px 14px;
        }

    }


    @media (max-width: 400px) {

        .hero-title {
            font-size: 18px;
        }

        .leaderboard-tab {
            font-size: 11px;
        }

        .leaderboard-table {
            min-width: 400px;
        }

        .leaderboard-table th {
            padding: 8px 7px;
        }

        .leaderboard-table td {
            padding: 9px 7px;
        }

        .top-points-label {
            font-size: 9px;
        }

    }
@endsection


@section('content')

<div class="leaderboard-page">


    {{-- =====================================================
         HERO
    ====================================================== --}}

    <div class="leaderboard-hero">

        <div class="hero-content">

            <div class="hero-icon">

                <i class="fa-solid fa-ranking-star"></i>

            </div>


            <h1 class="hero-title">
                Leaderboard Siswa
            </h1>


            <p class="hero-description">

                Lihat peringkat siswa berdasarkan prestasi,
                pelanggaran, dan saldo poin.

            </p>

        </div>

    </div>


    {{-- =====================================================
         TABS
    ====================================================== --}}

    <div class="leaderboard-tabs">

        <button
            type="button"
            class="leaderboard-tab active"
            data-tab="prestasi">

            <i class="fa-solid fa-trophy"></i>

            Top Prestasi

        </button>


        <button
            type="button"
            class="leaderboard-tab"
            data-tab="pelanggaran">

            <i class="fa-solid fa-triangle-exclamation"></i>

            Top Pelanggaran

        </button>


        <button
            type="button"
            class="leaderboard-tab"
            data-tab="saldo">

            <i class="fa-solid fa-star"></i>

            Saldo Poin

        </button>

    </div>


    {{-- =====================================================
         PRESTASI
    ====================================================== --}}

    <div
        class="leaderboard-panel active"
        id="panel-prestasi">


        @if($leaderboardPrestasi->count() > 0)

            <div class="top-three">

                @foreach($leaderboardPrestasi->take(3) as $index => $siswa)

                    <div class="top-card
                        {{ $index === 0 ? 'first' : '' }}
                        {{ $index === 1 ? 'second' : '' }}
                        {{ $index === 2 ? 'third' : '' }}">

                        <div class="top-medal">

                            @if($index === 0)
                                🥇
                            @elseif($index === 1)
                                🥈
                            @else
                                🥉
                            @endif

                        </div>


                        <div class="top-rank">
                            PERINGKAT {{ $index + 1 }}
                        </div>


                        <div class="top-name">
                            {{ $siswa->nama }}
                        </div>


                        <div class="top-class">
                            {{ $siswa->kelas ?? '-' }}
                        </div>


                        <div class="top-points">
                            +{{ $siswa->prestasis_sum_poin ?? 0 }}
                        </div>


                        <div class="top-points-label">
                            TOTAL POIN PRESTASI
                        </div>

                    </div>

                @endforeach

            </div>

        @endif


        <div class="leaderboard-table-card">

            <div class="table-header">

                <div>

                    <div class="table-title">
                        Peringkat Prestasi
                    </div>

                    <div class="table-subtitle">
                        Berdasarkan total poin prestasi siswa
                    </div>

                </div>

            </div>


            @if($leaderboardPrestasi->count() > 0)

                <div class="table-wrapper">

                    <table class="leaderboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Rank
                                </th>

                                <th>
                                    Siswa
                                </th>

                                <th>
                                    Jumlah Prestasi
                                </th>

                                <th>
                                    Total Poin
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($leaderboardPrestasi as $index => $siswa)

                                <tr>

                                    <td>

                                        <div class="rank-box
                                            {{ $index < 3 ? 'rank-' . ($index + 1) : '' }}">

                                            {{ $index + 1 }}

                                        </div>

                                    </td>


                                    <td>

                                        <div class="student-cell">

                                            <div class="student-avatar">

                                                <i class="fa-solid fa-user"></i>

                                            </div>


                                            <div class="student-info">

                                                <div class="student-name-table">

                                                    {{ $siswa->nama }}

                                                </div>

                                                <div class="student-class-table">

                                                    {{ $siswa->kelas ?? '-' }}

                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="activity-count">

                                            {{ $siswa->prestasis_count }}

                                            prestasi

                                        </span>

                                    </td>


                                    <td>

                                        <span class="point-positive">

                                            +{{ $siswa->prestasis_sum_poin ?? 0 }}

                                            poin

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <i class="fa-solid fa-trophy"></i>

                    <p>
                        Belum ada data prestasi.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- =====================================================
         PELANGGARAN
    ====================================================== --}}

    <div
        class="leaderboard-panel"
        id="panel-pelanggaran">


        <div class="leaderboard-table-card">

            <div class="table-header">

                <div>

                    <div class="table-title">
                        Peringkat Pelanggaran
                    </div>

                    <div class="table-subtitle">
                        Berdasarkan total poin pelanggaran siswa
                    </div>

                </div>

            </div>


            @if($leaderboardPelanggaran->count() > 0)

                <div class="table-wrapper">

                    <table class="leaderboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Rank
                                </th>

                                <th>
                                    Siswa
                                </th>

                                <th>
                                    Jumlah Pelanggaran
                                </th>

                                <th>
                                    Total Poin
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($leaderboardPelanggaran as $index => $siswa)

                                <tr>

                                    <td>

                                        <div class="rank-box
                                            {{ $index < 3 ? 'rank-' . ($index + 1) : '' }}">

                                            {{ $index + 1 }}

                                        </div>

                                    </td>


                                    <td>

                                        <div class="student-cell">

                                            <div class="student-avatar">

                                                <i class="fa-solid fa-user"></i>

                                            </div>


                                            <div class="student-info">

                                                <div class="student-name-table">

                                                    {{ $siswa->nama }}

                                                </div>

                                                <div class="student-class-table">

                                                    {{ $siswa->kelas ?? '-' }}

                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="activity-count">

                                            {{ $siswa->pelanggarans_count }}

                                            pelanggaran

                                        </span>

                                    </td>


                                    <td>

                                        <span class="point-negative">

                                            -{{ $siswa->pelanggarans_sum_poin ?? 0 }}

                                            poin

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <i class="fa-solid fa-shield-halved"></i>

                    <p>
                        Belum ada data pelanggaran.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- =====================================================
         SALDO
    ====================================================== --}}

    <div
        class="leaderboard-panel"
        id="panel-saldo">


        <div class="leaderboard-table-card">

            <div class="table-header">

                <div>

                    <div class="table-title">
                        Peringkat Saldo Poin
                    </div>

                    <div class="table-subtitle">
                        100 − pelanggaran + prestasi
                    </div>

                </div>

            </div>


            @if($saldoSiswa->count() > 0)

                <div class="table-wrapper">

                    <table class="leaderboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Rank
                                </th>

                                <th>
                                    Siswa
                                </th>

                                <th>
                                    Prestasi
                                </th>

                                <th>
                                    Pelanggaran
                                </th>

                                <th>
                                    Saldo
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($saldoSiswa as $index => $siswa)

                                <tr>

                                    <td>

                                        <div class="rank-box
                                            {{ $index < 3 ? 'rank-' . ($index + 1) : '' }}">

                                            {{ $index + 1 }}

                                        </div>

                                    </td>


                                    <td>

                                        <div class="student-cell">

                                            <div class="student-avatar">

                                                <i class="fa-solid fa-user"></i>

                                            </div>


                                            <div class="student-info">

                                                <div class="student-name-table">

                                                    {{ $siswa->nama }}

                                                </div>

                                                <div class="student-class-table">

                                                    {{ $siswa->kelas ?? '-' }}

                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="point-positive">

                                            +{{ $siswa->prestasis_sum_poin ?? 0 }}

                                        </span>

                                    </td>


                                    <td>

                                        <span class="point-negative">

                                            -{{ $siswa->pelanggarans_sum_poin ?? 0 }}

                                        </span>

                                    </td>


                                    <td>

                                        <span class="saldo-value
                                            {{ $siswa->saldo_poin >= 100
                                                ? 'saldo-positive'
                                                : ($siswa->saldo_poin < 50
                                                    ? 'saldo-negative'
                                                    : '') }}">

                                            {{ $siswa->saldo_poin }}

                                            poin

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <i class="fa-solid fa-user-graduate"></i>

                    <p>
                        Belum ada data siswa.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>


<script>

    document.addEventListener('DOMContentLoaded', function () {

        const tabs =
            document.querySelectorAll('.leaderboard-tab');

        const panels =
            document.querySelectorAll('.leaderboard-panel');


        tabs.forEach(function (tab) {

            tab.addEventListener('click', function () {

                const target =
                    this.dataset.tab;


                tabs.forEach(function (item) {

                    item.classList.remove('active');

                });


                panels.forEach(function (panel) {

                    panel.classList.remove('active');

                });


                this.classList.add('active');


                const targetPanel =
                    document.getElementById(
                        'panel-' + target
                    );


                if (targetPanel) {

                    targetPanel.classList.add('active');

                }

            });

        });

    });

</script>

@endsection
