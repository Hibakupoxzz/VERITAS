@extends('layouts.app')

@section('title', 'Portal Lapor Pelanggaran - Wali Kelas')
@section('page_title', 'Portal Lapor Pelanggaran')

@section('content')

<div class="walas-container">

    {{-- =========================================================
         1. WELCOME BANNER KHUSUS WALI KELAS
    ========================================================= --}}
    <div class="walas-banner">

        <div class="walas-banner-content">

            <div class="walas-banner-badge">
                <i class="fa-solid fa-user-tie"></i>
                <span>Portal Khusus Guru Wali Kelas</span>
            </div>

            <h1 class="walas-banner-title">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>

            <p class="walas-banner-subtitle">
                Sebagai Wali Kelas, tugas Anda adalah melaporkan temuan pelanggaran siswa.
                Laporan yang Anda kirim akan masuk ke antrean verifikasi untuk ditinjau
                dan ditentukan sanksi/poin oleh tim <strong>Guru PDS</strong>.
            </p>

        </div>

        <div class="walas-banner-actions">

            <a href="#form-lapor" class="btn-banner-primary">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Buat Laporan Baru</span>
            </a>

            <a href="#riwayat-lapor" class="btn-banner-secondary">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Lihat Riwayat Laporan</span>
            </a>

        </div>

    </div>


    {{-- =========================================================
         2. STATISTIK LAPORAN SAYA
    ========================================================= --}}
    <div class="walas-stats-grid">

        {{-- TOTAL --}}
        <div class="walas-stat-card stat-total">

            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-folder-open"></i>
            </div>

            <div class="stat-data">

                <span class="stat-number">
                    {{ $stats['total'] ?? 0 }}
                </span>

                <span class="stat-label">
                    Total Laporan Dikirim
                </span>

            </div>

        </div>


        {{-- PENDING --}}
        <div class="walas-stat-card stat-pending">

            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>

            <div class="stat-data">

                <span class="stat-number">
                    {{ $stats['pending'] ?? 0 }}
                </span>

                <span class="stat-label">
                    Menunggu Verifikasi PDS
                </span>

            </div>

        </div>


        {{-- VERIFIED --}}
        <div class="walas-stat-card stat-verified">

            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <div class="stat-data">

                <span class="stat-number">
                    {{ $stats['verified'] ?? 0 }}
                </span>

                <span class="stat-label">
                    Diverifikasi & Diproses
                </span>

            </div>

        </div>


        {{-- REJECTED --}}
        <div class="walas-stat-card stat-rejected">

            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>

            <div class="stat-data">

                <span class="stat-number">
                    {{ $stats['rejected'] ?? 0 }}
                </span>

                <span class="stat-label">
                    Laporan Ditolak
                </span>

            </div>

        </div>

    </div>


    {{-- =========================================================
         3. FORMULIR LAPOR PELANGGARAN
    ========================================================= --}}
    <div class="walas-card" id="form-lapor">

        <div class="walas-card-header">

            <div class="walas-card-icon">
                <i class="fa-solid fa-bullhorn"></i>
            </div>

            <div>

                <h2>
                    Formulir Lapor Pelanggaran
                </h2>

                <p>
                    Isi rincian temuan pelanggaran siswa di bawah ini secara objektif dan lengkap.
                </p>

            </div>

        </div>


        {{-- =====================================================
             ALERT SUCCESS
        ====================================================== --}}
        @if(session('success'))

            <div class="walas-alert walas-alert-success">

                <i class="fa-solid fa-circle-check"></i>

                <div>

                    <strong>
                        Berhasil Terkirim!
                    </strong>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>

            </div>

        @endif


        {{-- =====================================================
             ALERT ERROR
        ====================================================== --}}
        @if(session('error'))

            <div class="walas-alert walas-alert-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <div>

                    <strong>
                        Pemberitahuan:
                    </strong>

                    <span>
                        {{ session('error') }}
                    </span>

                </div>

            </div>

        @endif


        {{-- =====================================================
             VALIDATION ERROR
        ====================================================== --}}
        @if($errors->any())

            <div class="walas-alert walas-alert-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <div>

                    <strong>
                        Mohon periksa kembali form berikut:
                    </strong>

                    <ul>

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        @endif


        {{-- =====================================================
             FORM
        ====================================================== --}}
        <form
            action="{{ route('lapor.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="walas-form"
        >

            @csrf


            {{-- =================================================
                 SISWA + TANGGAL
            ================================================== --}}
            <div class="walas-form-row">

                {{-- SISWA --}}
                <div class="walas-form-group">

                    <label
                        class="walas-label"
                        for="siswa_id"
                    >
                        Pilih Siswa Pelanggar
                        <span class="optional">
                            (Opsional)
                        </span>
                    </label>

                    <select
                        name="siswa_id"
                        id="siswa_id"
                        class="walas-input walas-select searchable-select"
                    >

                        <option value="">
                            -- Tidak memilih siswa --
                        </option>

                        @foreach($siswas as $siswa)

                            <option
                                value="{{ $siswa->id }}"
                                {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}
                            >

                                {{ $siswa->nama }}

                                @if($siswa->kelas)
                                    — {{ $siswa->kelas }}
                                @endif

                                @if(isset($siswa->nisn))
                                    (NISN: {{ $siswa->nisn }})
                                @endif

                            </option>

                        @endforeach

                    </select>

                    <span class="walas-help">
                        Pilih siswa jika laporan ditujukan kepada siswa tertentu.
                    </span>

                </div>


                {{-- TANGGAL --}}
                <div class="walas-form-group">

                    <label
                        class="walas-label"
                        for="tanggal"
                    >
                        Tanggal Kejadian
                        <span class="required">*</span>
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        id="tanggal"
                        class="walas-input"
                        value="{{ old('tanggal', date('Y-m-d')) }}"
                        required
                    >

                    <span class="walas-help">
                        Tanggal saat pelanggaran terjadi atau ditemukan.
                    </span>

                </div>

            </div>


            {{-- =================================================
                 JENIS / MODE PELANGGARAN
            ================================================== --}}
            <div class="walas-form-group">

                <label class="walas-label">
                    Nama / Jenis Pelanggaran
                    <span class="required">*</span>
                </label>


                {{-- MODE SELECT / MANUAL --}}
                <div class="pelanggaran-mode">

                    {{-- PILIH DARI ATURAN --}}
                    <label class="mode-option">

                        <input
                            type="radio"
                            name="pelanggaran_mode"
                            value="aturan"
                            {{ old('pelanggaran_mode', 'aturan') === 'aturan' ? 'checked' : '' }}
                        >

                        <div class="mode-option-icon">
                            <i class="fa-solid fa-list-check"></i>
                        </div>

                        <div class="mode-option-text">

                            <strong>
                                Pilih dari aturan
                            </strong>

                            <small>
                                Gunakan aturan pelanggaran yang tersedia
                            </small>

                        </div>

                    </label>


                    {{-- ISI MANUAL --}}
                    <label class="mode-option">

                        <input
                            type="radio"
                            name="pelanggaran_mode"
                            value="manual"
                            {{ old('pelanggaran_mode') === 'manual' ? 'checked' : '' }}
                        >

                        <div class="mode-option-icon">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>

                        <div class="mode-option-text">

                            <strong>
                                Isi secara manual
                            </strong>

                            <small>
                                Tulis nama pelanggaran sendiri
                            </small>

                        </div>

                    </label>

                </div>

            </div>


            {{-- =================================================
                 MODE ATURAN
            ================================================== --}}
            <div
                id="modeAturan"
                class="walas-form-group"
            >

                <label
                    class="walas-label"
                    for="aturan_pelanggaran_id"
                >
                    Aturan Pelanggaran
                    <span class="required">*</span>
                </label>

                <select
                    name="aturan_pelanggaran_id"
                    id="aturan_pelanggaran_id"
                    class="walas-input"
                >

                    <option value="">
                        -- Pilih Aturan Pelanggaran --
                    </option>

                    @foreach($aturanPelanggarans as $aturan)

                        <option
                            value="{{ $aturan->id }}"
                            data-kategori="{{ $aturan->kategori }}"
                            data-poin="{{ $aturan->poin }}"
                            {{ old('aturan_pelanggaran_id') == $aturan->id ? 'selected' : '' }}
                        >

                            {{ $aturan->kode }}
                            —
                            {{ $aturan->nama }}
                            ({{ $aturan->poin }} poin)

                        </option>

                    @endforeach

                </select>

                {{-- TIDAK ADA KOTAK INFO DI SINI --}}

            </div>


            {{-- =================================================
                 MODE MANUAL
            ================================================== --}}
            <div
                id="modeManual"
                style="display: none;"
            >

                <div class="walas-form-group">

                    <label
                        class="walas-label"
                        for="jenis_pelanggaran_manual"
                    >
                        Nama Pelanggaran
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        name="jenis_pelanggaran_manual"
                        id="jenis_pelanggaran_manual"
                        class="walas-input"
                        value="{{ old('jenis_pelanggaran_manual') }}"
                        placeholder="Contoh: Merokok di area toilet lantai 2"
                    >

                    <span class="walas-help">
                        Tuliskan nama atau jenis pelanggaran secara singkat dan jelas.
                    </span>

                </div>

            </div>


            {{-- =================================================
                 KATEGORI + POIN
            ================================================== --}}
            <div class="walas-detail-row">

                {{-- KATEGORI --}}
                <div class="walas-form-group">

                    <label
                        class="walas-label"
                        for="kategori_manual"
                    >
                        Kategori
                        <span class="required">*</span>
                    </label>

                    <select
                        name="kategori_manual"
                        id="kategori_manual"
                        class="walas-input"
                    >

                        <option value="">
                            -- Pilih Kategori --
                        </option>

                        <option
                            value="Ringan"
                            {{ old('kategori_manual') === 'Ringan' ? 'selected' : '' }}
                        >
                            Ringan
                        </option>

                        <option
                            value="Sedang"
                            {{ old('kategori_manual') === 'Sedang' ? 'selected' : '' }}
                        >
                            Sedang
                        </option>

                        <option
                            value="Berat"
                            {{ old('kategori_manual') === 'Berat' ? 'selected' : '' }}
                        >
                            Berat
                        </option>

                        <option
                            value="Luar Biasa"
                            {{ old('kategori_manual') === 'Luar Biasa' ? 'selected' : '' }}
                        >
                            Luar Biasa
                        </option>

                    </select>

                </div>


                {{-- POIN --}}
                <div class="walas-form-group">

                    <label
                        class="walas-label"
                        for="poin_manual"
                    >
                        Poin
                        <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        name="poin_manual"
                        id="poin_manual"
                        class="walas-input"
                        value="{{ old('poin_manual', 0) }}"
                        min="0"
                        placeholder="0"
                    >

                </div>

            </div>


            {{-- =================================================
                 KETERANGAN
            ================================================== --}}
            <div class="walas-form-group">

                <label
                    class="walas-label"
                    for="keterangan"
                >
                    Kronologi & Deskripsi Kejadian

                    <span class="optional">
                        (Opsional)
                    </span>

                </label>

                <textarea
                    name="keterangan"
                    id="keterangan"
                    class="walas-textarea"
                    rows="4"
                    placeholder="Jelaskan secara rinci kronologi temuan, saksi yang melihat, atau keterangan lain yang memperjelas laporan..."
                >{{ old('keterangan') }}</textarea>

            </div>


            {{-- =================================================
                 FOTO BUKTI
            ================================================== --}}
            <div class="walas-form-group">

                <label
                    class="walas-label"
                    for="foto_bukti"
                >
                    Unggah Foto Bukti

                    <span class="optional">
                        (Opsional)
                    </span>

                </label>

                <div class="walas-file-wrapper">

                    <input
                        type="file"
                        name="foto_bukti"
                        id="foto_bukti"
                        class="walas-file-input"
                        accept="image/*"
                        onchange="previewImage(event)"
                    >

                    <div class="walas-file-dummy">

                        <i class="fa-solid fa-cloud-arrow-up"></i>

                        <span>
                            Klik untuk memilih file foto bukti kejadian
                        </span>

                        <small>
                            Format yang didukung: JPG, PNG, WEBP (Maksimal 5 MB)
                        </small>

                    </div>

                </div>


                {{-- PREVIEW --}}
                <div
                    id="imagePreviewContainer"
                    style="display:none; margin-top:12px;"
                >

                    <img
                        id="imagePreview"
                        src=""
                        alt="Preview Bukti"
                        style="max-width:200px; max-height:160px; border-radius:10px; border:1px solid #e5e7eb;"
                    >

                </div>

            </div>


            {{-- =================================================
                 TOMBOL SUBMIT
            ================================================== --}}
            <div class="walas-form-actions">

                <button
                    type="submit"
                    class="btn-submit-laporan"
                >

                    <i class="fa-solid fa-paper-plane"></i>

                    <span>
                        Kirim Laporan ke PDS
                    </span>

                </button>

            </div>

        </form>

    </div>


    {{-- =========================================================
         4. RIWAYAT LAPORAN
    ========================================================= --}}
    <div
        class="walas-card"
        id="riwayat-lapor"
    >

        <div class="walas-card-header">

            <div class="walas-card-icon">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>

            <div>

                <h2>
                    Riwayat Laporan Saya
                </h2>

                <p>
                    Daftar laporan pelanggaran yang telah Anda kirim.
                </p>

            </div>

        </div>


        @if($laporans->count() > 0)

            <div class="walas-reports-list">

                @foreach($laporans as $laporan)

                    <div class="report-item">

                        {{-- HEADER --}}
                        <div class="report-header">

                            <div class="report-student">

                                <div class="student-avatar">
                                    <i class="fa-solid fa-user"></i>
                                </div>

                                <div>

                                    <h3 class="student-name">

                                        @if($laporan->siswa)

                                            {{ $laporan->siswa->nama }}

                                        @else

                                            Siswa tidak dipilih

                                        @endif

                                    </h3>

                                    <span class="student-class">

                                        @if($laporan->siswa)

                                            {{ $laporan->siswa->kelas ?? '-' }}

                                        @else

                                            -

                                        @endif

                                    </span>

                                </div>

                            </div>


                            {{-- STATUS --}}
                            @if($laporan->status === 'pending')

                                <span class="badge-status badge-pending">

                                    <i class="fa-solid fa-hourglass-half"></i>

                                    Menunggu Verifikasi

                                </span>

                            @elseif($laporan->status === 'diverifikasi')

                                <span class="badge-status badge-verified">

                                    <i class="fa-solid fa-circle-check"></i>

                                    Diverifikasi

                                </span>

                            @elseif($laporan->status === 'ditolak')

                                <span class="badge-status badge-rejected">

                                    <i class="fa-solid fa-circle-xmark"></i>

                                    Ditolak

                                </span>

                            @else

                                <span class="badge-status">

                                    {{ ucfirst($laporan->status) }}

                                </span>

                            @endif

                        </div>


                        {{-- BODY --}}
                        <div class="report-body">

                            <div class="report-meta-tags">

                                <span class="meta-tag meta-violation">

                                    <i class="fa-solid fa-triangle-exclamation"></i>

                                    {{ $laporan->jenis_pelanggaran }}

                                </span>


                                <span class="meta-tag">

                                    <i class="fa-regular fa-calendar"></i>

                                    {{ \Carbon\Carbon::parse($laporan->tanggal)->format('d M Y') }}

                                </span>


                                <span class="meta-tag">

                                    <i class="fa-solid fa-star"></i>

                                    {{ $laporan->poin ?? 0 }} poin

                                </span>


                                @if(!empty($laporan->kategori))

                                    <span class="meta-tag">

                                        <i class="fa-solid fa-layer-group"></i>

                                        {{ $laporan->kategori }}

                                    </span>

                                @endif

                            </div>


                            {{-- DESKRIPSI --}}
                            @if($laporan->keterangan)

                                <p class="report-description">

                                    {{ $laporan->keterangan }}

                                </p>

                            @endif


                            {{-- FOTO --}}
                            @if($laporan->foto_bukti)

                                <div class="report-photo">

                                    <a
                                        href="{{ asset('storage/' . $laporan->foto_bukti) }}"
                                        target="_blank"
                                    >

                                        <img
                                            src="{{ asset('storage/' . $laporan->foto_bukti) }}"
                                            alt="Foto bukti"
                                        >

                                    </a>

                                    <span class="photo-hint">
                                        Klik foto untuk melihat ukuran penuh.
                                    </span>

                                </div>

                            @endif

                        </div>


                        {{-- =================================================
                             FEEDBACK
                        ================================================== --}}

                        @if($laporan->status === 'diverifikasi')

                            <div class="report-feedback feedback-verified">

                                <i class="fa-solid fa-circle-check feedback-icon"></i>

                                <div class="feedback-text">

                                    <strong>
                                        Laporan telah diverifikasi.
                                    </strong>

                                    <span>
                                        Laporan telah diproses oleh PDS.
                                    </span>

                                    @if(isset($laporan->poin))

                                        <span class="points-badge">
                                            {{ $laporan->poin }} Poin
                                        </span>

                                    @endif

                                </div>

                            </div>

                        @elseif($laporan->status === 'ditolak')

                            <div class="report-feedback feedback-rejected">

                                <i class="fa-solid fa-circle-xmark feedback-icon"></i>

                                <div class="feedback-text">

                                    <strong>
                                        Laporan ditolak.
                                    </strong>

                                    @if(!empty($laporan->catatan_verifikator))

                                        <span>
                                            {{ $laporan->catatan_verifikator }}
                                        </span>

                                    @else

                                        <span>
                                            Laporan tidak disetujui oleh PDS.
                                        </span>

                                    @endif

                                </div>

                            </div>

                        @else

                            <div class="report-feedback feedback-pending">

                                <i class="fa-solid fa-hourglass-half feedback-icon"></i>

                                <div class="feedback-text">

                                    <strong>
                                        Menunggu verifikasi.
                                    </strong>

                                    <span class="feedback-note">
                                        Laporan akan ditinjau oleh PDS sebelum diproses.
                                    </span>

                                </div>

                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        @else

            <div class="walas-empty-state">

                <div class="empty-icon">

                    <i class="fa-solid fa-inbox"></i>

                </div>

                <h3>
                    Belum Ada Laporan
                </h3>

                <p>
                    Belum ada laporan pelanggaran yang Anda kirim.
                    Silakan gunakan formulir di atas untuk membuat laporan baru.
                </p>

            </div>

        @endif

    </div>

</div>

@endsection


@section('styles')

/* =========================================================
   CONTAINER & LAYOUT
========================================================= */

.walas-container {
    width: 100%;
    max-width: 100%;
    display: flex;
    flex-direction: column;
    gap: 24px;
}


/* =========================================================
   WELCOME BANNER
========================================================= */

.walas-banner {
    background: linear-gradient(
        135deg,
        #6D1408 0%,
        #901C0D 50%,
        #4A0D05 100%
    );

    border-radius: 20px;
    padding: 32px 36px;

    color: #ffffff;

    box-shadow: 0 10px 30px rgba(109, 20, 8, 0.2);

    display: flex;
    justify-content: space-between;
    align-items: center;

    gap: 28px;

    position: relative;
    overflow: hidden;
}

.walas-banner::after {
    content: '';

    position: absolute;

    right: -40px;
    bottom: -40px;

    width: 220px;
    height: 220px;

    border-radius: 50%;

    background: rgba(255, 255, 255, 0.05);

    pointer-events: none;
}

.walas-banner-content {
    max-width: 680px;
}

.walas-banner-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    background: rgba(255, 255, 255, 0.15);

    backdrop-filter: blur(10px);

    padding: 6px 14px;

    border-radius: 20px;

    font-size: 12px;
    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: 0.5px;

    margin-bottom: 12px;
}

.walas-banner-title {
    font-size: 26px;
    font-weight: 700;

    margin: 0 0 10px;

    line-height: 1.3;
}

.walas-banner-subtitle {
    font-size: 14px;

    line-height: 1.6;

    color: rgba(255, 255, 255, 0.88);

    margin: 0;
}

.walas-banner-actions {
    display: flex;

    flex-direction: column;

    gap: 10px;

    flex-shrink: 0;
}

.btn-banner-primary,
.btn-banner-secondary {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 10px;

    padding: 12px 20px;

    border-radius: 12px;

    font-size: 13px;
    font-weight: 600;

    text-decoration: none;

    transition: all 0.2s ease;

    white-space: nowrap;
}

.btn-banner-primary {
    background: #ffffff;

    color: #6D1408;

    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
}

.btn-banner-primary:hover {
    background: #f8fafc;

    transform: translateY(-2px);

    color: #550f06;
}

.btn-banner-secondary {
    background: rgba(255, 255, 255, 0.15);

    color: #ffffff;

    border: 1px solid rgba(255, 255, 255, 0.25);
}

.btn-banner-secondary:hover {
    background: rgba(255, 255, 255, 0.25);

    transform: translateY(-2px);
}


/* =========================================================
   STAT CARDS GRID
========================================================= */

.walas-stats-grid {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 18px;
}

.walas-stat-card {
    background: #ffffff;

    border-radius: 16px;

    padding: 20px 22px;

    border: 1px solid #e5e7eb;

    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);

    display: flex;

    align-items: center;

    gap: 16px;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.walas-stat-card:hover {
    transform: translateY(-2px);

    box-shadow:
        0 8px 24px rgba(0, 0, 0, 0.06);
}

.stat-icon-wrapper {
    width: 48px;
    height: 48px;

    border-radius: 12px;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 20px;

    flex-shrink: 0;
}

.stat-total .stat-icon-wrapper {
    background: #eff6ff;
    color: #2563eb;
}

.stat-pending .stat-icon-wrapper {
    background: #fef3c7;
    color: #d97706;
}

.stat-verified .stat-icon-wrapper {
    background: #dcfce7;
    color: #16a34a;
}

.stat-rejected .stat-icon-wrapper {
    background: #fee2e2;
    color: #dc2626;
}

.stat-data {
    display: flex;
    flex-direction: column;
}

.stat-number {
    font-size: 24px;

    font-weight: 700;

    color: #111827;

    line-height: 1.1;
}

.stat-label {
    font-size: 12px;

    color: #6b7280;

    margin-top: 4px;

    font-weight: 500;
}


/* =========================================================
   CARDS GENERAL
========================================================= */

.walas-card {
    background: #ffffff;

    border-radius: 20px;

    padding: 28px 32px;

    border: 1px solid #e5e7eb;

    box-shadow:
        0 6px 24px rgba(0, 0, 0, 0.04);
}

.walas-card-header {
    display: flex;

    align-items: flex-start;

    gap: 16px;

    margin-bottom: 24px;

    padding-bottom: 18px;

    border-bottom: 1px solid #f3f4f6;
}

.walas-card-icon {
    width: 44px;
    height: 44px;

    border-radius: 12px;

    background: #FBEAE8;

    color: #6D1408;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 18px;

    flex-shrink: 0;
}

.walas-card-header h2 {
    font-size: 18px;

    font-weight: 700;

    color: #111827;

    margin: 0 0 4px;
}

.walas-card-header p {
    font-size: 13px;

    color: #6b7280;

    margin: 0;
}


/* =========================================================
   ALERTS
========================================================= */

.walas-alert {
    display: flex;

    align-items: flex-start;

    gap: 12px;

    padding: 14px 18px;

    border-radius: 12px;

    font-size: 13px;

    line-height: 1.5;

    margin-bottom: 20px;
}

.walas-alert-success {
    background: #dcfce7;

    color: #15803d;

    border: 1px solid #bbf7d0;
}

.walas-alert-error {
    background: #fee2e2;

    color: #b91c1c;

    border: 1px solid #fecaca;
}

.walas-alert ul {
    margin: 6px 0 0 16px;

    padding: 0;
}


/* =========================================================
   FORM STYLING
========================================================= */

.walas-form {
    display: flex;

    flex-direction: column;

    gap: 18px;
}

.walas-form-row {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 20px;
}

.walas-form-group {
    display: flex;

    flex-direction: column;
}

.walas-label {
    font-size: 13px;

    font-weight: 600;

    color: #374151;

    margin-bottom: 6px;
}

.walas-label .required {
    color: #dc2626;
}

.walas-label .optional {
    color: #9ca3af;

    font-weight: 400;

    font-size: 11px;
}

.walas-input,
.walas-textarea {
    width: 100%;

    padding: 11px 14px;

    border: 1px solid #d1d5db;

    border-radius: 10px;

    font-size: 13px;

    color: #111827;

    background: #ffffff;

    transition:
        border-color 0.15s,
        box-shadow 0.15s;

    font-family: inherit;

    box-sizing: border-box;
}

.walas-input:focus,
.walas-textarea:focus {
    outline: none;

    border-color: #6D1408;

    box-shadow:
        0 0 0 3px rgba(109, 20, 8, 0.12);
}

.walas-input:disabled {
    background: #f9fafb;

    color: #6b7280;

    cursor: not-allowed;
}

.walas-textarea {
    resize: vertical;

    min-height: 90px;
}

.walas-help {
    font-size: 11px;

    color: #6b7280;

    margin-top: 5px;
}


/* =========================================================
   MODE PELANGGARAN
========================================================= */

.pelanggaran-mode {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 12px;

    margin-top: 2px;
}

.mode-option {
    position: relative;

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 13px 15px;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    background: #ffffff;

    cursor: pointer;

    transition:
        border-color 0.15s ease,
        background 0.15s ease,
        box-shadow 0.15s ease;
}

.mode-option:hover {
    border-color: #cbd5e1;
}

.mode-option input {
    position: absolute;

    opacity: 0;

    pointer-events: none;
}

.mode-option-icon {
    width: 38px;
    height: 38px;

    border-radius: 10px;

    background: #f9fafb;

    color: #6b7280;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 16px;

    flex-shrink: 0;

    transition: all 0.15s ease;
}

.mode-option-text {
    display: flex;

    flex-direction: column;

    gap: 2px;
}

.mode-option-text strong {
    font-size: 13px;

    color: #374151;

    font-weight: 600;
}

.mode-option-text small {
    font-size: 11px;

    color: #9ca3af;
}


/* Checked state */

.mode-option:has(input:checked) {
    border-color: #6D1408;

    background: #fff8f7;

    box-shadow:
        0 0 0 1px rgba(109, 20, 8, 0.08);
}

.mode-option:has(input:checked)
.mode-option-icon {
    background: #6D1408;

    color: #ffffff;
}

.mode-option:has(input:checked)
.mode-option-text strong {
    color: #6D1408;
}


/* =========================================================
   KATEGORI + POIN
========================================================= */

.walas-detail-row {
    display: grid;

    grid-template-columns: 1fr 160px;

    gap: 20px;
}


/* =========================================================
   CUSTOM FILE UPLOAD
========================================================= */

.walas-file-wrapper {
    position: relative;

    border: 2px dashed #d1d5db;

    border-radius: 12px;

    background: #fafafa;

    text-align: center;

    padding: 24px 16px;

    cursor: pointer;

    transition: all 0.2s ease;
}

.walas-file-wrapper:hover {
    border-color: #6D1408;

    background: #fff8f8;
}

.walas-file-input {
    position: absolute;

    top: 0;
    left: 0;

    width: 100%;
    height: 100%;

    opacity: 0;

    cursor: pointer;
}

.walas-file-dummy {
    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 6px;

    color: #4b5563;

    pointer-events: none;
}

.walas-file-dummy i {
    font-size: 28px;

    color: #6D1408;
}

.walas-file-dummy span {
    font-size: 13px;

    font-weight: 600;
}

.walas-file-dummy small {
    font-size: 11px;

    color: #9ca3af;
}


/* =========================================================
   SUBMIT BUTTON
========================================================= */

.walas-form-actions {
    display: flex;

    justify-content: flex-end;

    margin-top: 6px;
}

.btn-submit-laporan {
    display: inline-flex;

    align-items: center;

    gap: 10px;

    background: #6D1408;

    color: #ffffff;

    padding: 13px 28px;

    border-radius: 12px;

    font-size: 14px;

    font-weight: 600;

    border: none;

    cursor: pointer;

    box-shadow:
        0 4px 14px rgba(109, 20, 8, 0.25);

    transition: all 0.2s ease;
}

.btn-submit-laporan:hover {
    background: #550f06;

    transform: translateY(-2px);

    box-shadow:
        0 6px 20px rgba(109, 20, 8, 0.35);
}


/* =========================================================
   REPORTS LIST
========================================================= */

.walas-reports-list {
    display: flex;

    flex-direction: column;

    gap: 16px;
}

.report-item {
    border: 1px solid #e5e7eb;

    border-radius: 14px;

    padding: 20px 22px;

    background: #ffffff;

    transition: all 0.2s ease;
}

.report-item:hover {
    border-color: #d1d5db;

    box-shadow:
        0 4px 16px rgba(0, 0, 0, 0.03);
}

.report-header {
    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 14px;
}

.report-student {
    display: flex;

    align-items: center;

    gap: 12px;
}

.student-avatar {
    width: 38px;
    height: 38px;

    border-radius: 10px;

    background: #f3f4f6;

    color: #4b5563;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 16px;
}

.student-name {
    font-size: 15px;

    font-weight: 700;

    color: #111827;

    margin: 0;
}

.student-class {
    font-size: 12px;

    color: #6b7280;
}


/* =========================================================
   BADGES
========================================================= */

.badge-status {
    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 5px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 600;
}

.badge-pending {
    background: #fef3c7;

    color: #92400e;

    border: 1px solid #fde68a;
}

.badge-verified {
    background: #dcfce7;

    color: #15803d;

    border: 1px solid #bbf7d0;
}

.badge-rejected {
    background: #fee2e2;

    color: #b91c1c;

    border: 1px solid #fecaca;
}


/* =========================================================
   REPORT BODY
========================================================= */

.report-body {
    display: flex;

    flex-direction: column;

    gap: 10px;

    margin-bottom: 14px;
}

.report-meta-tags {
    display: flex;

    align-items: center;

    gap: 12px;

    flex-wrap: wrap;
}

.meta-tag {
    display: inline-flex;

    align-items: center;

    gap: 6px;

    font-size: 12px;

    color: #6b7280;
}

.meta-violation {
    background: #FBEAE8;

    color: #6D1408;

    font-weight: 600;

    padding: 3px 10px;

    border-radius: 6px;
}

.report-description {
    font-size: 13px;

    color: #374151;

    line-height: 1.5;

    margin: 0;

    background: #f9fafb;

    padding: 10px 14px;

    border-radius: 8px;

    border-left: 3px solid #6D1408;
}

.report-photo img {
    max-width: 140px;

    max-height: 90px;

    border-radius: 8px;

    object-fit: cover;

    border: 1px solid #e5e7eb;

    display: block;
}

.report-photo a {
    display: inline-block;

    text-decoration: none;

    position: relative;
}

.photo-hint {
    display: block;

    font-size: 10px;

    color: #6b7280;

    margin-top: 4px;
}


/* =========================================================
   FEEDBACK
========================================================= */

.report-feedback {
    display: flex;

    align-items: flex-start;

    gap: 12px;

    padding: 12px 16px;

    border-radius: 10px;

    font-size: 12px;
}

.feedback-verified {
    background: #f0fdf4;

    border: 1px solid #bbf7d0;

    color: #166534;
}

.feedback-rejected {
    background: #fef2f2;

    border: 1px solid #fecaca;

    color: #991b1b;
}

.feedback-pending {
    background: #fefce8;

    border: 1px solid #fef08a;

    color: #854d0e;
}

.feedback-icon {
    font-size: 16px;

    margin-top: 2px;
}

.feedback-text {
    display: flex;

    flex-direction: column;

    gap: 2px;
}

.points-badge {
    display: inline-block;

    background: #dc2626;

    color: #ffffff;

    font-weight: 700;

    padding: 2px 8px;

    border-radius: 4px;

    font-size: 11px;

    margin-top: 4px;

    width: fit-content;
}

.feedback-note {
    font-style: italic;

    margin: 4px 0 0;

    font-size: 11px;

    opacity: 0.9;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.walas-empty-state {
    text-align: center;

    padding: 40px 20px;
}

.empty-icon {
    width: 60px;
    height: 60px;

    border-radius: 50%;

    background: #f3f4f6;

    color: #9ca3af;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 24px;

    margin: 0 auto 14px;
}

.walas-empty-state h3 {
    font-size: 16px;

    color: #111827;

    margin: 0 0 6px;
}

.walas-empty-state p {
    font-size: 13px;

    color: #6b7280;

    max-width: 400px;

    margin: 0 auto;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 900px) {

    .walas-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .walas-banner {
        flex-direction: column;

        align-items: flex-start;
    }

    .walas-banner-actions {
        width: 100%;

        flex-direction: row;
    }

    .btn-banner-primary,
    .btn-banner-secondary {
        flex: 1;
    }

    .walas-form-row {
        grid-template-columns: 1fr;
    }

    .walas-detail-row {
        grid-template-columns: 1fr;
    }

    .pelanggaran-mode {
        grid-template-columns: 1fr;
    }
}


@media (max-width: 600px) {

    .walas-stats-grid {
        grid-template-columns: 1fr;
    }

    .walas-banner-actions {
        flex-direction: column;
    }

    .walas-card-header {
        flex-direction: column;

        align-items: flex-start;
    }

    .walas-card {
        padding: 22px 18px;
    }

    .report-header {
        align-items: flex-start;

        flex-direction: column;

        gap: 12px;
    }

    .walas-form-actions {
        justify-content: stretch;
    }

    .btn-submit-laporan {
        width: 100%;

        justify-content: center;
    }

}

@endsection


@section('scripts')

<script>

/*
|--------------------------------------------------------------------------
| MODE PELANGGARAN
|--------------------------------------------------------------------------
*/

const modeAturan =
    document.getElementById('modeAturan');

const modeManual =
    document.getElementById('modeManual');

const aturanSelect =
    document.getElementById('aturan_pelanggaran_id');

const kategoriInput =
    document.getElementById('kategori_manual');

const poinInput =
    document.getElementById('poin_manual');

const namaManualInput =
    document.getElementById('jenis_pelanggaran_manual');


/*
|--------------------------------------------------------------------------
| UPDATE NILAI ATURAN
|--------------------------------------------------------------------------
*/

function updateAturanValues() {

    if (!aturanSelect) {
        return;
    }

    const selectedOption =
        aturanSelect.options[
            aturanSelect.selectedIndex
        ];


    if (
        !selectedOption ||
        !selectedOption.value
    ) {

        if (kategoriInput) {
            kategoriInput.value = '';
        }

        if (poinInput) {
            poinInput.value = 0;
        }

        return;
    }


    const kategori =
        selectedOption.dataset.kategori || '';

    const poin =
        selectedOption.dataset.poin || 0;


    /*
     * Kategori dan poin ditampilkan
     * otomatis berdasarkan aturan yang dipilih.
     */

    if (kategoriInput) {
        kategoriInput.value = kategori;
    }

    if (poinInput) {
        poinInput.value = poin;
    }

}


/*
|--------------------------------------------------------------------------
| UPDATE MODE
|--------------------------------------------------------------------------
*/

function updatePelanggaranMode() {

    const checked =
        document.querySelector(
            'input[name="pelanggaran_mode"]:checked'
        );

    if (!checked) {
        return;
    }


    const mode =
        checked.value;


    /*
     * MODE ATURAN
     */

    if (mode === 'aturan') {

        modeAturan.style.display = 'block';

        modeManual.style.display = 'none';


        /*
         * Aturan wajib dipilih.
         */

        aturanSelect.disabled = false;

        aturanSelect.required = true;


        /*
         * Field manual tidak digunakan.
         */

        namaManualInput.disabled = true;

        namaManualInput.required = false;


        /*
         * Kategori dan poin hanya
         * sebagai tampilan otomatis.
         */

        kategoriInput.disabled = true;

        poinInput.disabled = true;

        kategoriInput.required = false;

        poinInput.required = false;


        updateAturanValues();

    }


    /*
     * MODE MANUAL
     */

    else {

        modeAturan.style.display = 'none';

        modeManual.style.display = 'block';


        /*
         * Aturan tidak digunakan.
         */

        aturanSelect.disabled = true;

        aturanSelect.required = false;


        /*
         * Nama manual wajib diisi.
         */

        namaManualInput.disabled = false;

        namaManualInput.required = true;


        /*
         * Kategori dan poin bisa diisi.
         */

        kategoriInput.disabled = false;

        poinInput.disabled = false;

        kategoriInput.required = true;

        poinInput.required = true;

    }

}


/*
|--------------------------------------------------------------------------
| RADIO MODE
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll(
        'input[name="pelanggaran_mode"]'
    )
    .forEach(function (radio) {

        radio.addEventListener(
            'change',
            updatePelanggaranMode
        );

    });


/*
|--------------------------------------------------------------------------
| SELECT ATURAN
|--------------------------------------------------------------------------
*/

if (aturanSelect) {

    aturanSelect.addEventListener(
        'change',
        updateAturanValues
    );

}


/*
|--------------------------------------------------------------------------
| PREVIEW IMAGE
|--------------------------------------------------------------------------
*/

function previewImage(event) {

    const input =
        event.target;

    const container =
        document.getElementById(
            'imagePreviewContainer'
        );

    const preview =
        document.getElementById(
            'imagePreview'
        );


    if (
        input.files &&
        input.files[0]
    ) {

        const file =
            input.files[0];


        /*
         * Pastikan file adalah gambar.
         */

        if (
            !file.type.startsWith('image/')
        ) {

            input.value = '';

            container.style.display =
                'none';

            preview.src = '';

            return;
        }


        /*
         * Preview.
         */

        const reader =
            new FileReader();


        reader.onload =
            function (e) {

                preview.src =
                    e.target.result;

                container.style.display =
                    'block';

            };


        reader.readAsDataURL(file);

    }

    else {

        container.style.display =
            'none';

        preview.src = '';

    }

}


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        updatePelanggaranMode();

    }
);

</script>

@endsection
