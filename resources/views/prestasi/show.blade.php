@extends('layouts.app')

@section('title', 'Detail Prestasi - VERITAS')
@section('page-title', 'Detail Prestasi')

@section('content')

<div class="page-heading">
    <div>
        <h1>Detail Prestasi</h1>
        <p>Informasi lengkap mengenai prestasi siswa.</p>
    </div>

    <div class="heading-actions">
        <a href="{{ route('prestasi.edit', $prestasi->id) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i>
            Edit
        </a>

        <a href="{{ route('prestasi.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>
</div>


<div class="detail-grid">

    {{-- =========================================
         INFORMASI PRESTASI
    ========================================== --}}

    <div class="detail-card">

        <div class="detail-card-header">
            <div class="header-icon">
                <i class="fa-solid fa-trophy"></i>
            </div>

            <div>
                <h2>Informasi Prestasi</h2>
                <p>Data prestasi yang telah dicatat.</p>
            </div>
        </div>


        <div class="detail-body">

            <div class="prestasi-highlight">

                <div class="prestasi-icon">
                    <i class="fa-solid fa-medal"></i>
                </div>

                <div class="prestasi-main">
                    <span class="label">Jenis Prestasi</span>

                    <h3>
                        {{ $prestasi->jenis_prestasi }}
                    </h3>

                    @if($prestasi->tingkat)
                        <span class="tingkat-badge">
                            <i class="fa-solid fa-ranking-star"></i>
                            {{ $prestasi->tingkat }}
                        </span>
                    @endif
                </div>

                <div class="point-box">
                    <span>Poin</span>

                    <strong>
                        +{{ $prestasi->poin }}
                    </strong>
                </div>

            </div>


            <div class="info-list">

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa-regular fa-calendar"></i>
                    </div>

                    <div>
                        <span>Tanggal</span>

                        <strong>
                            {{ $prestasi->tanggal?->format('d F Y') ?? '-' }}
                        </strong>
                    </div>
                </div>


                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa-solid fa-ranking-star"></i>
                    </div>

                    <div>
                        <span>Tingkat</span>

                        <strong>
                            {{ $prestasi->tingkat ?? 'Tidak ditentukan' }}
                        </strong>
                    </div>
                </div>


                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa-solid fa-star"></i>
                    </div>

                    <div>
                        <span>Poin Prestasi</span>

                        <strong class="point-positive">
                            +{{ $prestasi->poin }} poin
                        </strong>
                    </div>
                </div>


                <div class="info-item">
                    <div class="info-icon">
                        <i class="fa-regular fa-clock"></i>
                    </div>

                    <div>
                        <span>Dicatat Pada</span>

                        <strong>
                            {{ $prestasi->created_at?->format('d F Y, H:i') ?? '-' }}
                        </strong>
                    </div>
                </div>

            </div>


            {{-- KETERANGAN --}}

            @if($prestasi->keterangan)

                <div class="description-box">

                    <div class="description-title">
                        <i class="fa-solid fa-file-lines"></i>
                        Keterangan
                    </div>

                    <p>
                        {{ $prestasi->keterangan }}
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================
         DATA SISWA
    ========================================== --}}

    <div class="detail-card">

        <div class="detail-card-header">
            <div class="header-icon">
                <i class="fa-solid fa-user-graduate"></i>
            </div>

            <div>
                <h2>Data Siswa</h2>
                <p>Siswa yang mendapatkan prestasi.</p>
            </div>
        </div>


        <div class="detail-body">

            <div class="student-profile">

                <div class="student-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="student-profile-info">

                    <span class="student-label">
                        Nama Siswa
                    </span>

                    <h3>
                        {{ $prestasi->siswa->nama ?? '-' }}
                    </h3>

                    <span class="student-nisn">
                        NISN:
                        {{ $prestasi->siswa->nisn ?? '-' }}
                    </span>

                </div>

            </div>


            <div class="student-data">

                <div class="data-row">

                    <span>
                        <i class="fa-solid fa-id-card"></i>
                        NISN
                    </span>

                    <strong>
                        {{ $prestasi->siswa->nisn ?? '-' }}
                    </strong>

                </div>


                @if(isset($prestasi->siswa->kelas))

                    <div class="data-row">

                        <span>
                            <i class="fa-solid fa-school"></i>
                            Kelas
                        </span>

                        <strong>
                            {{ $prestasi->siswa->kelas }}
                        </strong>

                    </div>

                @endif


                @if(isset($prestasi->siswa->jurusan))

                    <div class="data-row">

                        <span>
                            <i class="fa-solid fa-book-open"></i>
                            Jurusan
                        </span>

                        <strong>
                            {{ $prestasi->siswa->jurusan }}
                        </strong>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================
         PERUBAHAN POIN
    ========================================== --}}

    <div class="detail-card point-card">

        <div class="detail-card-header">

            <div class="header-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>

            <div>
                <h2>Perubahan Poin</h2>
                <p>Informasi saldo poin saat prestasi dicatat.</p>
            </div>

        </div>


        <div class="point-flow">

            <div class="point-step">

                <span>
                    Poin Sebelum
                </span>

                <strong>
                    {{ $prestasi->poin_sebelum }}
                </strong>

            </div>


            <div class="point-arrow">
                <i class="fa-solid fa-arrow-right"></i>
            </div>


            <div class="point-step point-add">

                <span>
                    Prestasi
                </span>

                <strong>
                    +{{ $prestasi->poin }}
                </strong>

            </div>


            <div class="point-arrow">
                <i class="fa-solid fa-arrow-right"></i>
            </div>


            <div class="point-step point-final">

                <span>
                    Poin Sesudah
                </span>

                <strong>
                    {{ $prestasi->poin_sesudah }}
                </strong>

            </div>

        </div>


        <div class="point-info">

            <i class="fa-solid fa-circle-info"></i>

            <span>
                Prestasi menambahkan
                <strong>+{{ $prestasi->poin }} poin</strong>
                ke saldo poin siswa.
            </span>

        </div>

    </div>


    {{-- =========================================
         BUKTI PRESTASI
    ========================================== --}}

    @if($prestasi->bukti)

        <div class="detail-card">

            <div class="detail-card-header">

                <div class="header-icon">
                    <i class="fa-solid fa-image"></i>
                </div>

                <div>
                    <h2>Bukti Prestasi</h2>
                    <p>Dokumentasi atau bukti prestasi siswa.</p>
                </div>

            </div>


            <div class="evidence-body">

                <div class="evidence-image-wrapper">

                    <img
                        src="{{ asset('storage/' . $prestasi->bukti) }}"
                        alt="Bukti prestasi {{ $prestasi->jenis_prestasi }}"
                        class="evidence-image"
                    >

                </div>


                <div class="evidence-actions">

                    <a
                        href="{{ asset('storage/' . $prestasi->bukti) }}"
                        target="_blank"
                        class="btn btn-secondary"
                    >
                        <i class="fa-solid fa-up-right-from-square"></i>
                        Lihat Gambar
                    </a>

                </div>

            </div>

        </div>

    @else

        <div class="detail-card">

            <div class="detail-card-header">

                <div class="header-icon">
                    <i class="fa-solid fa-image"></i>
                </div>

                <div>
                    <h2>Bukti Prestasi</h2>
                    <p>Dokumentasi atau bukti prestasi siswa.</p>
                </div>

            </div>


            <div class="no-evidence">

                <div class="no-evidence-icon">
                    <i class="fa-regular fa-image"></i>
                </div>

                <strong>
                    Tidak ada bukti
                </strong>

                <span>
                    Belum ada foto atau dokumentasi yang diunggah.
                </span>

            </div>

        </div>

    @endif


</div>


{{-- =========================================
     CSS
========================================== --}}

<style>

    /* ========================================
       PAGE HEADING
    ======================================== */

    .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
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

    .heading-actions {
        display: flex;
        gap: 10px;
    }


    /* ========================================
       GRID
    ======================================== */

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .detail-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,.04);
    }

    .point-card {
        grid-column: 1 / -1;
    }


    /* ========================================
       CARD HEADER
    ======================================== */

    .detail-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 22px;
        background: #FFFCFA;
        border-bottom: 1px solid #E5E7EB;
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
        flex-shrink: 0;
        font-size: 18px;
    }

    .detail-card-header h2 {
        margin: 0 0 4px;
        font-size: 17px;
        color: #1F2937;
    }

    .detail-card-header p {
        margin: 0;
        color: #6B7280;
        font-size: 12px;
    }


    /* ========================================
       BODY
    ======================================== */

    .detail-body {
        padding: 22px;
    }


    /* ========================================
       PRESTASI HIGHLIGHT
    ======================================== */

    .prestasi-highlight {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 17px;
        border: 1px solid #E8D5D1;
        background: #FFF9F7;
        border-radius: 11px;
        margin-bottom: 22px;
    }

    .prestasi-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: #F9E9E6;
        color: #6D1408;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        flex-shrink: 0;
    }

    .prestasi-main {
        flex: 1;
        min-width: 0;
    }

    .prestasi-main .label {
        display: block;
        color: #9CA3AF;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .prestasi-main h3 {
        margin: 0 0 7px;
        font-size: 16px;
        color: #1F2937;
        word-break: break-word;
    }

    .tingkat-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 6px;
        background: #F3F4F6;
        color: #4B5563;
        font-size: 11px;
        font-weight: 600;
    }

    .point-box {
        text-align: right;
        flex-shrink: 0;
    }

    .point-box span {
        display: block;
        color: #9CA3AF;
        font-size: 11px;
        margin-bottom: 3px;
    }

    .point-box strong {
        color: #287A3D;
        font-size: 23px;
        font-weight: 700;
    }


    /* ========================================
       INFO LIST
    ======================================== */

    .info-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 12px;
        border: 1px solid #E5E7EB;
        border-radius: 9px;
    }

    .info-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        background: #F3F4F6;
        color: #6D1408;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 13px;
    }

    .info-item span {
        display: block;
        color: #9CA3AF;
        font-size: 10px;
        margin-bottom: 3px;
    }

    .info-item strong {
        display: block;
        color: #374151;
        font-size: 12px;
    }

    .point-positive {
        color: #287A3D !important;
    }


    /* ========================================
       KETERANGAN
    ======================================== */

    .description-box {
        margin-top: 18px;
        padding: 15px;
        background: #F9FAFB;
        border-radius: 9px;
        border: 1px solid #E5E7EB;
    }

    .description-title {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
    }

    .description-title i {
        color: #6D1408;
    }

    .description-box p {
        margin: 0;
        color: #6B7280;
        font-size: 13px;
        line-height: 1.7;
        white-space: pre-line;
    }


    /* ========================================
       STUDENT PROFILE
    ======================================== */

    .student-profile {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 11px;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        margin-bottom: 18px;
    }

    .student-avatar {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: #F9E9E6;
        color: #6D1408;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        flex-shrink: 0;
    }

    .student-profile-info {
        min-width: 0;
    }

    .student-label {
        display: block;
        color: #9CA3AF;
        font-size: 10px;
        margin-bottom: 3px;
    }

    .student-profile-info h3 {
        margin: 0 0 4px;
        color: #1F2937;
        font-size: 16px;
    }

    .student-nisn {
        color: #6B7280;
        font-size: 12px;
    }


    /* ========================================
       STUDENT DATA
    ======================================== */

    .student-data {
        border-top: 1px solid #E5E7EB;
    }

    .data-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 13px 0;
        border-bottom: 1px solid #F0F0F0;
    }

    .data-row:last-child {
        border-bottom: none;
    }

    .data-row span {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6B7280;
        font-size: 12px;
    }

    .data-row span i {
        width: 16px;
        color: #9CA3AF;
    }

    .data-row strong {
        color: #374151;
        font-size: 12px;
        text-align: right;
    }


    /* ========================================
       POINT FLOW
    ======================================== */

    .point-flow {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 25px;
        padding: 28px 20px;
    }

    .point-step {
        min-width: 150px;
        text-align: center;
        padding: 17px;
        border: 1px solid #E5E7EB;
        border-radius: 11px;
        background: #FAFAFA;
    }

    .point-step span {
        display: block;
        color: #9CA3AF;
        font-size: 11px;
        margin-bottom: 7px;
    }

    .point-step strong {
        display: block;
        font-size: 25px;
        color: #374151;
    }

    .point-step.point-add {
        background: #F5FBF6;
        border-color: #D8E8DC;
    }

    .point-step.point-add strong {
        color: #287A3D;
    }

    .point-step.point-final {
        background: #FFF9F7;
        border-color: #E8D5D1;
    }

    .point-step.point-final strong {
        color: #6D1408;
    }

    .point-arrow {
        color: #9CA3AF;
        font-size: 17px;
    }

    .point-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        margin: 0 22px 22px;
        background: #F9FAFB;
        border-radius: 8px;
        color: #6B7280;
        font-size: 12px;
    }

    .point-info i {
        color: #9CA3AF;
    }

    .point-info strong {
        color: #287A3D;
    }


    /* ========================================
       BUKTI
    ======================================== */

    .evidence-body {
        padding: 22px;
    }

    .evidence-image-wrapper {
        display: flex;
        justify-content: center;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 11px;
        padding: 15px;
    }

    .evidence-image {
        display: block;
        max-width: 100%;
        max-height: 500px;
        border-radius: 8px;
        object-fit: contain;
    }

    .evidence-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 12px;
    }


    /* ========================================
       NO EVIDENCE
    ======================================== */

    .no-evidence {
        min-height: 200px;
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .no-evidence-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: #F3F4F6;
        color: #9CA3AF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        margin-bottom: 12px;
    }

    .no-evidence strong {
        color: #4B5563;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .no-evidence span {
        color: #9CA3AF;
        font-size: 12px;
    }


    /* ========================================
       BUTTON
    ======================================== */

    .btn {
        height: 42px;
        padding: 0 15px;
        border-radius: 9px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: .2s;
        box-sizing: border-box;
    }

    .btn-primary {
        background: #6D1408;
        color: #fff;
    }

    .btn-primary:hover {
        background: #551006;
    }

    .btn-secondary {
        background: #F3F4F6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #E5E7EB;
    }


    /* ========================================
       RESPONSIVE
    ======================================== */

    @media (max-width: 900px) {

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .point-card {
            grid-column: auto;
        }

    }


    @media (max-width: 768px) {

        .page-heading {
            align-items: flex-start;
        }

        .page-heading h1 {
            font-size: 22px;
        }

        .heading-actions {
            flex-shrink: 0;
        }

        .info-list {
            grid-template-columns: 1fr;
        }

        .point-flow {
            gap: 10px;
        }

        .point-step {
            min-width: 0;
            flex: 1;
        }

    }


    @media (max-width: 560px) {

        .page-heading {
            flex-direction: column;
        }

        .heading-actions {
            width: 100%;
        }

        .heading-actions .btn {
            flex: 1;
        }

        .prestasi-highlight {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .point-box {
            width: 100%;
            padding-top: 10px;
            border-top: 1px solid #E8D5D1;
            text-align: left;
        }

        .point-flow {
            flex-direction: column;
            padding: 20px 18px;
        }

        .point-step {
            width: 100%;
            box-sizing: border-box;
        }

        .point-arrow {
            transform: rotate(90deg);
        }

        .point-info {
            margin-left: 18px;
            margin-right: 18px;
            text-align: center;
        }

        .detail-card-header {
            padding: 17px;
        }

        .detail-body,
        .evidence-body {
            padding: 17px;
        }

    }

</style>

@endsection
