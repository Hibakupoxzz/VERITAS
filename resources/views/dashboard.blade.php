@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('styles')
<style>
    /* =========================================================
       DASHBOARD
    ========================================================= */

    .dashboard {
        width: 100%;
    }

    /* =========================
       WELCOME
    ========================= */

    .dashboard-welcome {
        background: linear-gradient(135deg, #6D1408 0%, #8f1d0d 100%);
        color: #fff;
        border-radius: 20px;
        padding: 28px 30px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(109, 20, 8, .15);
    }

    .dashboard-welcome::after {
        content: '';
        position: absolute;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,.06);
        right: -50px;
        top: -70px;
    }

    .dashboard-welcome::before {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,.04);
        right: 100px;
        bottom: -80px;
    }

    .welcome-content {
        position: relative;
        z-index: 2;
    }

    .welcome-label {
        font-size: 13px;
        font-weight: 600;
        opacity: .85;
        margin-bottom: 6px;
    }

    .welcome-title {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .welcome-description {
        margin: 8px 0 0;
        font-size: 14px;
        opacity: .9;
        max-width: 650px;
        line-height: 1.6;
    }


    /* =========================
       STAT CARDS
    ========================= */

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 26px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,.04);
        display: flex;
        align-items: center;
        gap: 15px;
        min-width: 0;
        transition: .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,.07);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-icon.red {
        background: rgba(109,20,8,.10);
        color: #6D1408;
    }

    .stat-icon.green {
        background: rgba(21,128,61,.10);
        color: #15803D;
    }

    .stat-icon.blue {
        background: rgba(37,99,235,.10);
        color: #2563EB;
    }

    .stat-icon.orange {
        background: rgba(234,88,12,.10);
        color: #EA580C;
    }

    .stat-info {
        min-width: 0;
    }

    .stat-label {
        color: #6B7280;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .stat-value {
        color: #1F2937;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
    }


    /* =========================
       LEADERBOARD
    ========================= */

    .dashboard-leaderboard {
        margin-top: 25px;
    }

    .leaderboard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 15px;
    }

    .leaderboard-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .leaderboard-heading-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(109,20,8,.10);
        color: #6D1408;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .leaderboard-title {
        margin: 0;
        color: #1F2937;
        font-size: 20px;
        font-weight: 800;
    }

    .leaderboard-subtitle {
        margin: 3px 0 0;
        color: #6B7280;
        font-size: 12px;
    }

    .leaderboard-link {
        text-decoration: none;
        color: #6D1408;
        background: rgba(109,20,8,.08);
        border-radius: 10px;
        padding: 9px 13px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        transition: .2s ease;
    }

    .leaderboard-link:hover {
        background: #6D1408;
        color: #fff;
    }


    /* =========================
       LEADERBOARD GRID
    ========================= */

    .leaderboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
    }

    .leaderboard-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,.04);
        min-width: 0;
    }

    .leaderboard-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 1px solid #F0F1F3;
        margin-bottom: 5px;
    }

    .leaderboard-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #1F2937;
        font-size: 15px;
        font-weight: 800;
    }

    .leaderboard-card-title i {
        color: #6D1408;
    }

    .leaderboard-card-title.violation i {
        color: #B91C1C;
    }

    .leaderboard-card-count {
        color: #9CA3AF;
        font-size: 11px;
        font-weight: 600;
    }


    /* =========================
       LEADERBOARD ITEM
    ========================= */

    .leaderboard-list {
        display: flex;
        flex-direction: column;
    }

    .leaderboard-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #F3F4F6;
        min-width: 0;
    }

    .leaderboard-item:last-child {
        border-bottom: none;
    }

    .leaderboard-rank {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 10px;
        background: #F3F4F6;
        color: #6B7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
    }

    .leaderboard-rank.rank-1 {
        background: #FEF3C7;
        color: #B45309;
    }

    .leaderboard-rank.rank-2 {
        background: #E5E7EB;
        color: #4B5563;
    }

    .leaderboard-rank.rank-3 {
        background: #F3E8E1;
        color: #92400E;
    }

    .leaderboard-student {
        flex: 1;
        min-width: 0;
    }

    .student-name {
        color: #1F2937;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .student-class {
        color: #9CA3AF;
        font-size: 11px;
        margin-top: 2px;
    }

    .leaderboard-score {
        text-align: right;
        white-space: nowrap;
    }

    .score-value {
        font-size: 15px;
        font-weight: 800;
    }

    .score-value.achievement {
        color: #15803D;
    }

    .score-value.violation {
        color: #B91C1C;
    }

    .score-label {
        color: #9CA3AF;
        font-size: 10px;
        margin-top: 2px;
    }


    /* =========================
       SALDO POIN
    ========================= */

    .saldo-section {
        margin-top: 22px;
    }

    .saldo-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,.04);
    }

    .saldo-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .saldo-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #1F2937;
    }

    .saldo-description {
        margin: 4px 0 0;
        font-size: 11px;
        color: #9CA3AF;
    }

    .saldo-list {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }

    .saldo-item {
        border: 1px solid #F0F1F3;
        border-radius: 13px;
        padding: 13px;
        min-width: 0;
    }

    .saldo-name {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .saldo-class {
        color: #9CA3AF;
        font-size: 10px;
        margin-top: 3px;
    }

    .saldo-poin {
        margin-top: 8px;
        font-size: 19px;
        font-weight: 800;
        color: #6D1408;
    }

    .saldo-poin small {
        font-size: 9px;
        font-weight: 600;
        color: #9CA3AF;
    }


    /* =========================
       EMPTY STATE
    ========================= */

    .leaderboard-empty {
        text-align: center;
        padding: 28px 10px;
        color: #9CA3AF;
    }

    .leaderboard-empty i {
        font-size: 25px;
        margin-bottom: 8px;
        display: block;
    }

    .leaderboard-empty p {
        margin: 0;
        font-size: 12px;
    }


    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 1100px) {

        .dashboard-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .saldo-list {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 800px) {

        .leaderboard-grid {
            grid-template-columns: 1fr;
        }

        .saldo-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {

        .dashboard-welcome {
            padding: 22px 20px;
            border-radius: 16px;
        }

        .welcome-title {
            font-size: 22px;
        }

        .welcome-description {
            font-size: 12px;
        }

        .dashboard-stats {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat-card {
            padding: 14px;
            border-radius: 14px;
            gap: 10px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            font-size: 16px;
            border-radius: 11px;
        }

        .stat-value {
            font-size: 19px;
        }

        .stat-label {
            font-size: 10px;
        }

        .leaderboard-header {
            align-items: flex-start;
        }

        .leaderboard-title {
            font-size: 17px;
        }

        .leaderboard-subtitle {
            font-size: 10px;
        }

        .leaderboard-link {
            padding: 8px 10px;
            font-size: 10px;
        }

        .leaderboard-card {
            padding: 15px;
            border-radius: 15px;
        }

        .saldo-list {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 400px) {

        .dashboard-stats {
            grid-template-columns: 1fr;
        }

        .saldo-list {
            grid-template-columns: 1fr;
        }

        .leaderboard-heading-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
        }

        .leaderboard-title {
            font-size: 16px;
        }
    }
</style>
@endsection


@section('content')

<div class="dashboard">

    {{-- =====================================================
         WELCOME
    ====================================================== --}}

    <div class="dashboard-welcome">
        <div class="welcome-content">

            <div class="welcome-label">
                SISTEM INFORMASI SEKOLAH
            </div>

            <h1 class="welcome-title">
                Selamat Datang di VERITAS
            </h1>

            <p class="welcome-description">
                Kelola data siswa, pelanggaran, prestasi, pembinaan,
                dan perkembangan poin siswa secara terintegrasi.
            </p>

        </div>
    </div>


    {{-- =====================================================
         STATISTIK
    ====================================================== --}}

    <div class="dashboard-stats">

        <div class="stat-card">

            <div class="stat-icon red">
                <i class="fa-solid fa-user-graduate"></i>
            </div>

            <div class="stat-info">

                <div class="stat-label">
                    TOTAL SISWA
                </div>

                <div class="stat-value">
                    {{ $totalSiswa ?? 0 }}
                </div>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon orange">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="stat-info">

                <div class="stat-label">
                    TOTAL PELANGGARAN
                </div>

                <div class="stat-value">
                    {{ $totalPelanggaran ?? 0 }}
                </div>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon green">
                <i class="fa-solid fa-trophy"></i>
            </div>

            <div class="stat-info">

                <div class="stat-label">
                    TOTAL PRESTASI
                </div>

                <div class="stat-value">
                    {{ $totalPrestasi ?? 0 }}
                </div>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon blue">
                <i class="fa-solid fa-star"></i>
            </div>

            <div class="stat-info">

                <div class="stat-label">
                    POIN DASAR
                </div>

                <div class="stat-value">
                    100
                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
         LEADERBOARD
    ====================================================== --}}

    <div class="dashboard-leaderboard">

        <div class="leaderboard-header">

            <div class="leaderboard-heading">

                <div class="leaderboard-heading-icon">
                    <i class="fa-solid fa-ranking-star"></i>
                </div>

                <div>

                    <h2 class="leaderboard-title">
                        Leaderboard Siswa
                    </h2>

                    <p class="leaderboard-subtitle">
                        Peringkat berdasarkan aktivitas siswa
                    </p>

                </div>

            </div>

            <a href="{{ route('leaderboard') }}"
               class="leaderboard-link">

                Lihat Semua
                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <div class="leaderboard-grid">


            {{-- =================================================
                 TOP PRESTASI
            ================================================== --}}

            <div class="leaderboard-card">

                <div class="leaderboard-card-header">

                    <div class="leaderboard-card-title">

                        <i class="fa-solid fa-trophy"></i>

                        Top Prestasi

                    </div>

                    <div class="leaderboard-card-count">
                        5 TERATAS
                    </div>

                </div>


                <div class="leaderboard-list">

                    @forelse($topPrestasi ?? [] as $index => $siswa)

                        <div class="leaderboard-item">

                            <div class="leaderboard-rank
                                {{ $index < 3 ? 'rank-' . ($index + 1) : '' }}">

                                {{ $index + 1 }}

                            </div>


                            <div class="leaderboard-student">

                                <div class="student-name">
                                    {{ $siswa->nama }}
                                </div>

                                <div class="student-class">

                                    {{ $siswa->kelas ?? '-' }}

                                </div>

                            </div>


                            <div class="leaderboard-score">

                                <div class="score-value achievement">

                                    +{{ $siswa->prestasis_sum_poin ?? 0 }}

                                </div>

                                <div class="score-label">
                                    poin prestasi
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="leaderboard-empty">

                            <i class="fa-solid fa-trophy"></i>

                            <p>
                                Belum ada data prestasi.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>



            {{-- =================================================
                 TOP PELANGGARAN
            ================================================== --}}

            <div class="leaderboard-card">

                <div class="leaderboard-card-header">

                    <div class="leaderboard-card-title violation">

                        <i class="fa-solid fa-triangle-exclamation"></i>

                        Top Pelanggaran

                    </div>

                    <div class="leaderboard-card-count">
                        5 TERATAS
                    </div>

                </div>


                <div class="leaderboard-list">

                    @forelse($topPelanggaran ?? [] as $index => $siswa)

                        <div class="leaderboard-item">

                            <div class="leaderboard-rank
                                {{ $index < 3 ? 'rank-' . ($index + 1) : '' }}">

                                {{ $index + 1 }}

                            </div>


                            <div class="leaderboard-student">

                                <div class="student-name">
                                    {{ $siswa->nama }}
                                </div>

                                <div class="student-class">

                                    {{ $siswa->kelas ?? '-' }}

                                </div>

                            </div>


                            <div class="leaderboard-score">

                                <div class="score-value violation">

                                    -{{ $siswa->pelanggarans_sum_poin ?? 0 }}

                                </div>

                                <div class="score-label">
                                    poin pelanggaran
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="leaderboard-empty">

                            <i class="fa-solid fa-shield-halved"></i>

                            <p>
                                Belum ada data pelanggaran.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- =================================================
             SALDO POIN TERATAS
        ================================================== --}}

        <div class="saldo-section">

            <div class="saldo-card">

                <div class="saldo-header">

                    <div>

                        <h3 class="saldo-title">
                            Saldo Poin Siswa
                        </h3>

                        <p class="saldo-description">
                            Perhitungan: 100 − pelanggaran + prestasi
                        </p>

                    </div>

                </div>


                <div class="saldo-list">

                    @forelse($saldoSiswa ?? [] as $siswa)

                        <div class="saldo-item">

                            <div class="saldo-name">
                                {{ $siswa->nama }}
                            </div>

                            <div class="saldo-class">
                                {{ $siswa->kelas ?? '-' }}
                            </div>

                            <div class="saldo-poin">

                                {{ $siswa->saldo_poin ?? 100 }}

                                <small>poin</small>

                            </div>

                        </div>

                    @empty

                        <div class="leaderboard-empty"
                             style="grid-column:1/-1;">

                            <i class="fa-solid fa-user-graduate"></i>

                            <p>
                                Belum ada data siswa.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
