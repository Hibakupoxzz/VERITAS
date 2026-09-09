@extends('layouts.app')

@section('title', 'Detail Siswa - VERITAS')
@section('page-title', 'Detail Siswa')

@section('content')

@php
    $totalPelanggaran = $siswa->pelanggarans->sum('poin');
    $totalPrestasi = $siswa->prestasis->sum('poin');

    $saldoPoin = 100 - $totalPelanggaran + $totalPrestasi;
@endphp

<div class="page-heading">

    <div>
        <h1>
            <i class="fa-solid fa-user-graduate"></i>
            Detail Siswa
        </h1>

        <p>
            Informasi lengkap siswa dan riwayat poin
        </p>
    </div>

    <div class="heading-actions">

        <a href="{{ route('siswa.edit', $siswa->id) }}"
           class="btn btn-primary">
            <i class="fa-solid fa-pen"></i>
            Edit Siswa
        </a>

        <a href="{{ route('siswa.index') }}"
           class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>

    </div>

</div>


{{-- =========================================================
     DATA SISWA
========================================================= --}}

<div class="student-profile">

    <div class="profile-icon">
        <i class="fa-solid fa-user-graduate"></i>
    </div>

    <div class="profile-info">

        <h2>{{ $siswa->nama }}</h2>

        <div class="profile-meta">

            <span>
                <i class="fa-solid fa-id-card"></i>
                NISN: {{ $siswa->nisn }}
            </span>

            @if($siswa->kelas)
                <span>
                    <i class="fa-solid fa-school"></i>
                    Kelas: {{ $siswa->kelas }}
                </span>
            @endif

            @if($siswa->jurusan)
                <span>
                    <i class="fa-solid fa-book"></i>
                    {{ $siswa->jurusan }}
                </span>
            @endif

        </div>

    </div>

</div>


{{-- =========================================================
     STATISTIK POIN
========================================================= --}}

<div class="stats-grid">

    {{-- POIN AWAL --}}
    <div class="stat-card">

        <div class="stat-icon neutral">
            <i class="fa-solid fa-star"></i>
        </div>

        <div class="stat-content">

            <span class="stat-label">
                Poin Awal
            </span>

            <strong>
                100
            </strong>

        </div>

    </div>


    {{-- PELANGGARAN --}}
    <div class="stat-card">

        <div class="stat-icon danger">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <div class="stat-content">

            <span class="stat-label">
                Poin Pelanggaran
            </span>

            <strong class="text-danger">
                -{{ $totalPelanggaran }}
            </strong>

        </div>

    </div>


    {{-- PRESTASI --}}
    <div class="stat-card">

        <div class="stat-icon success">
            <i class="fa-solid fa-trophy"></i>
        </div>

        <div class="stat-content">

            <span class="stat-label">
                Poin Prestasi
            </span>

            <strong class="text-success">
                +{{ $totalPrestasi }}
            </strong>

        </div>

    </div>


    {{-- SALDO --}}
    <div class="stat-card saldo-card">

        <div class="stat-icon saldo">
            <i class="fa-solid fa-chart-line"></i>
        </div>

        <div class="stat-content">

            <span class="stat-label">
                Saldo Poin
            </span>

            <strong class="
                {{ $saldoPoin >= 80
                    ? 'text-success'
                    : ($saldoPoin >= 50
                        ? 'text-warning'
                        : 'text-danger') }}
            ">
                {{ $saldoPoin }}
            </strong>

        </div>

    </div>

</div>


{{-- =========================================================
     RIWAYAT
========================================================= --}}

<div class="history-grid">


    {{-- =====================================================
         RIWAYAT PELANGGARAN
    ====================================================== --}}

    <div class="table-card">

        <div class="card-header">

            <div>

                <h3>
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Riwayat Pelanggaran
                </h3>

                <p>
                    Daftar pelanggaran siswa
                </p>

            </div>

            <span class="count-badge danger">
                {{ $siswa->pelanggarans->count() }}
            </span>

        </div>


        @if($siswa->pelanggarans->count() > 0)

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Tanggal</th>

                            <th>Pelanggaran</th>

                            <th>Kategori</th>

                            <th>Poin</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($siswa->pelanggarans->sortByDesc('tanggal') as $pelanggaran)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $pelanggaran->tanggal
                                        ? $pelanggaran->tanggal->format('d/m/Y')
                                        : '-' }}
                                </td>

                                <td>

                                    <div class="violation-name">

                                        <i class="fa-solid fa-circle-exclamation"></i>

                                        <span>
                                            {{ $pelanggaran->jenis_pelanggaran }}
                                        </span>

                                    </div>

                                </td>

                                <td>

                                    @if($pelanggaran->kategori)

                                        <span class="badge category">
                                            {{ $pelanggaran->kategori }}
                                        </span>

                                    @else

                                        <span class="muted">
                                            -
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <span class="point-badge negative">

                                        <i class="fa-solid fa-minus"></i>

                                        {{ $pelanggaran->poin }}

                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon success">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <h4>
                    Tidak ada pelanggaran
                </h4>

                <p>
                    Siswa ini belum memiliki riwayat pelanggaran.
                </p>

            </div>

        @endif

    </div>



    {{-- =====================================================
         RIWAYAT PRESTASI
    ====================================================== --}}

    <div class="table-card">

        <div class="card-header">

            <div>

                <h3>
                    <i class="fa-solid fa-trophy"></i>
                    Riwayat Prestasi
                </h3>

                <p>
                    Daftar prestasi siswa
                </p>

            </div>

            <span class="count-badge success">
                {{ $siswa->prestasis->count() }}
            </span>

        </div>


        @if($siswa->prestasis->count() > 0)

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Tanggal</th>

                            <th>Prestasi</th>

                            <th>Tingkat</th>

                            <th>Poin</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($siswa->prestasis->sortByDesc('tanggal') as $prestasi)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $prestasi->tanggal
                                        ? $prestasi->tanggal->format('d/m/Y')
                                        : '-' }}
                                </td>

                                <td>

                                    <div class="achievement-name">

                                        <i class="fa-solid fa-trophy"></i>

                                        <span>
                                            {{ $prestasi->jenis_prestasi }}
                                        </span>

                                    </div>

                                </td>

                                <td>

                                    @if($prestasi->tingkat)

                                        <span class="badge achievement">
                                            {{ $prestasi->tingkat }}
                                        </span>

                                    @else

                                        <span class="muted">
                                            -
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <span class="point-badge positive">

                                        <i class="fa-solid fa-plus"></i>

                                        {{ $prestasi->poin }}

                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty-state">

                <div class="empty-icon neutral">
                    <i class="fa-solid fa-trophy"></i>
                </div>

                <h4>
                    Belum ada prestasi
                </h4>

                <p>
                    Siswa ini belum memiliki data prestasi.
                </p>

            </div>

        @endif

    </div>

</div>


{{-- =========================================================
     RINGKASAN PERHITUNGAN
========================================================= --}}

<div class="calculation-card">

    <div class="calculation-header">

        <div>

            <h3>
                <i class="fa-solid fa-calculator"></i>
                Perhitungan Saldo Poin
            </h3>

            <p>
                Ringkasan perhitungan poin siswa
            </p>

        </div>

    </div>


    <div class="calculation">

        <div class="calc-item">

            <span>
                Poin Awal
            </span>

            <strong>
                100
            </strong>

        </div>


        <div class="calc-symbol negative">
            <i class="fa-solid fa-minus"></i>
        </div>


        <div class="calc-item">

            <span>
                Pelanggaran
            </span>

            <strong class="text-danger">
                {{ $totalPelanggaran }}
            </strong>

        </div>


        <div class="calc-symbol">
            <i class="fa-solid fa-plus"></i>
        </div>


        <div class="calc-item">

            <span>
                Prestasi
            </span>

            <strong class="text-success">
                {{ $totalPrestasi }}
            </strong>

        </div>


        <div class="calc-symbol">
            <i class="fa-solid fa-equals"></i>
        </div>


        <div class="calc-item result">

            <span>
                Saldo Akhir
            </span>

            <strong
                class="
                    {{ $saldoPoin >= 80
                        ? 'text-success'
                        : ($saldoPoin >= 50
                            ? 'text-warning'
                            : 'text-danger') }}
                "
            >
                {{ $saldoPoin }}
            </strong>

        </div>

    </div>

</div>


@endsection


@section('styles')

<style>

    /* =====================================================
       PAGE HEADING
    ===================================================== */

    .page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .page-heading h1 {
        margin: 0 0 6px;
        font-size: 25px;
        font-weight: 800;
        color: #1f2937;
    }

    .page-heading h1 i {
        color: #6d1408;
        margin-right: 8px;
    }

    .page-heading p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .heading-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }


    /* =====================================================
       STUDENT PROFILE
    ===================================================== */

    .student-profile {
        display: flex;
        align-items: center;
        gap: 20px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .04);
    }

    .profile-icon {
        width: 70px;
        height: 70px;
        min-width: 70px;
        border-radius: 16px;
        background: #6d1408;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .profile-info h2 {
        margin: 0 0 10px;
        font-size: 22px;
        font-weight: 800;
        color: #1f2937;
    }

    .profile-meta {
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
        color: #6b7280;
        font-size: 13px;
    }

    .profile-meta span {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .profile-meta i {
        color: #6d1408;
    }


    /* =====================================================
       STATS
    ===================================================== */

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 15px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .03);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }

    .stat-icon.neutral {
        background: #f3f4f6;
        color: #374151;
    }

    .stat-icon.danger {
        background: #fef2f2;
        color: #b91c1c;
    }

    .stat-icon.success {
        background: #ecfdf5;
        color: #047857;
    }

    .stat-icon.saldo {
        background: #fdf2f8;
        color: #6d1408;
    }

    .stat-content {
        min-width: 0;
    }

    .stat-label {
        display: block;
        color: #6b7280;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .stat-content strong {
        font-size: 22px;
        font-weight: 800;
        color: #1f2937;
    }


    /* =====================================================
       HISTORY
    ===================================================== */

    .history-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .03);
    }

    .card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .card-header h3 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 800;
        color: #1f2937;
    }

    .card-header h3 i {
        color: #6d1408;
        margin-right: 6px;
    }

    .card-header p {
        margin: 0;
        color: #9ca3af;
        font-size: 12px;
    }


    /* =====================================================
       TABLE
    ===================================================== */

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: #fafafa;
        color: #6b7280;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 12px 14px;
        text-align: left;
        white-space: nowrap;
    }

    td {
        padding: 13px 14px;
        border-top: 1px solid #f0f0f0;
        color: #374151;
        font-size: 13px;
        vertical-align: middle;
    }

    tbody tr:hover {
        background: #fafafa;
    }


    /* =====================================================
       NAME
    ===================================================== */

    .violation-name,
    .achievement-name {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #374151;
    }

    .violation-name i {
        color: #b91c1c;
    }

    .achievement-name i {
        color: #d97706;
    }


    /* =====================================================
       BADGES
    ===================================================== */

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge.category {
        background: #f3f4f6;
        color: #4b5563;
    }

    .badge.achievement {
        background: #ecfdf5;
        color: #047857;
    }

    .point-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 5px 8px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 800;
    }

    .point-badge.negative {
        background: #fef2f2;
        color: #b91c1c;
    }

    .point-badge.positive {
        background: #ecfdf5;
        color: #047857;
    }

    .count-badge {
        min-width: 28px;
        height: 28px;
        padding: 0 8px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
    }

    .count-badge.danger {
        background: #fef2f2;
        color: #b91c1c;
    }

    .count-badge.success {
        background: #ecfdf5;
        color: #047857;
    }

    .muted {
        color: #9ca3af;
    }


    /* =====================================================
       EMPTY STATE
    ===================================================== */

    .empty-state {
        padding: 40px 20px;
        text-align: center;
    }

    .empty-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto 12px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .empty-icon.success {
        background: #ecfdf5;
        color: #047857;
    }

    .empty-icon.neutral {
        background: #f3f4f6;
        color: #6b7280;
    }

    .empty-state h4 {
        margin: 0 0 5px;
        font-size: 14px;
        color: #374151;
    }

    .empty-state p {
        margin: 0;
        color: #9ca3af;
        font-size: 12px;
    }


    /* =====================================================
       CALCULATION
    ===================================================== */

    .calculation-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .03);
    }

    .calculation-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .calculation-header h3 {
        margin: 0 0 4px;
        font-size: 16px;
        color: #1f2937;
    }

    .calculation-header h3 i {
        color: #6d1408;
        margin-right: 6px;
    }

    .calculation-header p {
        margin: 0;
        color: #9ca3af;
        font-size: 12px;
    }

    .calculation {
        padding: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .calc-item {
        min-width: 110px;
        text-align: center;
    }

    .calc-item span {
        display: block;
        color: #6b7280;
        font-size: 11px;
        margin-bottom: 5px;
    }

    .calc-item strong {
        display: block;
        font-size: 22px;
        font-weight: 800;
        color: #1f2937;
    }

    .calc-item.result {
        background: #fafafa;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 18px;
    }

    .calc-symbol {
        color: #9ca3af;
        font-size: 14px;
    }

    .calc-symbol.negative {
        color: #b91c1c;
    }


    /* =====================================================
       TEXT COLORS
    ===================================================== */

    .text-danger {
        color: #b91c1c !important;
    }

    .text-success {
        color: #047857 !important;
    }

    .text-warning {
        color: #b45309 !important;
    }


    /* =====================================================
       RESPONSIVE TABLET
    ===================================================== */

    @media (max-width: 1100px) {

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .history-grid {
            grid-template-columns: 1fr;
        }

    }


    /* =====================================================
       RESPONSIVE MOBILE
    ===================================================== */

    @media (max-width: 700px) {

        .page-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .page-heading h1 {
            font-size: 21px;
        }

        .heading-actions {
            width: 100%;
        }

        .heading-actions .btn {
            flex: 1;
            justify-content: center;
        }


        .student-profile {
            padding: 18px;
            gap: 14px;
        }

        .profile-icon {
            width: 55px;
            height: 55px;
            min-width: 55px;
            font-size: 22px;
        }

        .profile-info h2 {
            font-size: 18px;
        }

        .profile-meta {
            flex-direction: column;
            gap: 7px;
        }


        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat-card {
            padding: 14px;
            gap: 10px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            font-size: 16px;
        }

        .stat-content strong {
            font-size: 18px;
        }


        .history-grid {
            gap: 14px;
        }

        .card-header {
            padding: 15px;
        }

        th,
        td {
            padding: 11px 10px;
        }


        .calculation {
            gap: 10px;
            padding: 18px 12px;
        }

        .calc-item {
            min-width: 85px;
        }

        .calc-item strong {
            font-size: 18px;
        }

        .calc-symbol {
            font-size: 11px;
        }

    }


    /* =====================================================
       SMALL MOBILE
    ===================================================== */

    @media (max-width: 430px) {

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .heading-actions {
            flex-direction: column;
        }

        .heading-actions .btn {
            width: 100%;
        }

        .student-profile {
            align-items: flex-start;
        }

        .calculation {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
        }

    }

</style>

@endsection
