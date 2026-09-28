@extends('layouts.app')

@section('title', 'Portal Lapor Pelanggaran - ' . (auth()->user()->role_label ?? 'Guru'))
@section('page_title', 'Portal Lapor Pelanggaran')

@section('content')

<div class="walas-container">

    {{-- =========================================================
         1. WELCOME BANNER
    ========================================================= --}}
    <div class="walas-banner">

        <div class="walas-banner-content">

            <div class="walas-banner-badge">
                <i class="fa-solid {{ auth()->user()->isBk() ? 'fa-user-nurse' : (auth()->user()->isGuru() ? 'fa-graduation-cap' : (auth()->user()->isWalas() ? 'fa-user-tie' : 'fa-shield-halved')) }}"></i>
                <span>Portal Pelaporan {{ auth()->user()->role_label }}</span>
            </div>

            <h1 class="walas-banner-title">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>

            <p class="walas-banner-subtitle">
                @if(auth()->user()->isGuru())
                    Sebagai Guru Khusus, Anda memiliki akses penuh ke seluruh kelas ({{ count($kelasList ?? []) }} Kelas) dan seluruh siswa untuk melaporkan temuan pelanggaran secara langsung. Laporan Anda akan masuk ke antrean verifikasi untuk ditinjau oleh tim <strong>Guru PDS</strong>.
                @elseif(auth()->user()->isBk())
                    Sebagai Guru BK, Anda dapat melaporkan temuan pelanggaran siswa. Laporan yang Anda kirim akan masuk ke antrean verifikasi untuk ditinjau dan ditentukan sanksi/poin oleh tim <strong>Guru PDS</strong>.
                @elseif(auth()->user()->isWalas())
                    Sebagai Wali Kelas, tugas Anda adalah melaporkan temuan pelanggaran siswa di kelas Anda. Laporan yang Anda kirim akan masuk ke antrean verifikasi untuk ditinjau dan diverifikasi oleh tim <strong>Guru PDS</strong>.
                @else
                    Laporan yang Anda kirim akan masuk ke antrean verifikasi untuk ditinjau dan diverifikasi oleh tim <strong>Guru PDS</strong>.
                @endif
            </p>

        </div>

        <div class="walas-banner-actions">

            <a href="#form-lapor" class="btn-banner-primary">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Buat Laporan Baru</span>
            </a>
            <a href="#direktori-siswa" class="btn-banner-secondary">
                <i class="fa-solid fa-users"></i>
                <span>Lihat Data Siswa &amp; Kelas</span>
            </a>
            <a href="#riwayat-lapor" class="btn-banner-secondary">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Riwayat Laporan</span>
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

            <div class="walas-card-header-main">

                <div class="walas-card-icon">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>

                <div class="walas-card-header-text">

                    <h2>
                        Formulir Lapor Pelanggaran
                    </h2>

                    <p>
                        Isi rincian temuan pelanggaran siswa di bawah ini secara objektif dan lengkap.
                    </p>

                </div>

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

                @if(isset($kelasList) && count($kelasList) > 1)
                {{-- Filter Kelas untuk Mempersempit Pilihan --}}
                <div class="walas-form-group">
                    <label class="walas-label" for="form_filter_kelas">
                        <i class="fa-solid fa-filter"></i> Saring Kelas Siswa <span class="optional">(Opsional)</span>
                    </label>
                    <select id="form_filter_kelas"
                            class="walas-input walas-select"
                            onchange="filterSiswaDropdown(this.value)">
                        <option value="">-- Semua Kelas ({{ count($kelasList) }} Kelas) --</option>
                        @foreach($kelasList as $kelasOpt)
                            <option value="{{ $kelasOpt }}">{{ $kelasOpt }}</option>
                        @endforeach
                    </select>
                    <span class="walas-help">Pilih kelas jika ingin menyaring daftar siswa di samping.</span>
                </div>
                @endif

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
                                data-kelas="{{ $siswa->kelas }}"
                                data-nama="{{ \Illuminate\Support\Str::lower($siswa->nama) }}"
                                @if($siswa->kelas) data-nisn="{{ $siswa->nisn }}" @endif
                                {{ (string) old('siswa_id', $selectedSiswaId ?? '') === (string) $siswa->id ? 'selected' : '' }}
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
                        class="walas-preview-img"
                        src=""
                        alt="Preview Bukti"
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
                    <span>Kirim Laporan ke BK / PDS</span>
                </button>

            </div>

        </form>

    </div>


    {{-- =========================================================
         4. DIREKTORI DATA KELAS & SISWA UNTUK PELAPORAN
    ========================================================= --}}
    <div class="walas-card" id="direktori-siswa">
        <div class="walas-card-header" style="justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <div class="walas-card-header-main">
                <div class="walas-card-icon" style="background:#fef2f2; color:#b91c1c;">
                    <i class="fa-solid fa-users-viewfinder"></i>
                </div>
                <div class="walas-card-header-text">
                    <h2>Direktori Data Kelas &amp; Siswa</h2>
                    <p>Cari siswa dari kelas manapun untuk langsung mengisi formulir pelaporan pelanggaran.</p>
                </div>
            </div>

            <div class="dir-controls">
                <div class="dir-search">
                    <i class="fa-solid fa-magnifying-glass dir-search-icon"></i>
                    <input type="text"
                           id="searchSiswaInput"
                           class="dir-search-input"
                           onkeyup="filterDirectoryTable()"
                           placeholder="Cari nama atau NISN..."
                           style="width:100%; padding:8px 12px 8px 34px; border:1px solid #d1d5db; border-radius:10px; outline:none;">
                </div>

                @if(isset($kelasList) && count($kelasList) > 1)
                <select id="selectDirectoryKelas"
                        class="dir-kelas-select"
                        onchange="filterDirectoryTable()"
                        style="padding:8px 14px; border:1px solid #d1d5db; border-radius:10px; outline:none; background:#fff; font-weight:600; color:#374151;">
                    <option value="">Semua Kelas ({{ count($kelasList) }})</option>
                    @foreach($kelasList as $kelasItem)
                        <option value="{{ strtolower($kelasItem) }}">{{ $kelasItem }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>

        {{-- Class Pill Filters --}}
        @if(isset($kelasList) && count($kelasList) > 1)
        <div class="dir-pill-row">
            <span class="dir-pill-label">Filter Cepat Kelas:</span>
            <button type="button"
                    class="class-pill-btn active"
                    onclick="setQuickClassFilter('', this)">
                Semua ({{ $siswas->count() }})
            </button>
            @foreach($kelasList as $kelasItem)
                <button type="button"
                        class="class-pill-btn"
                        onclick="setQuickClassFilter('{{ strtolower($kelasItem) }}', this)">
                    {{ $kelasItem }}
                </button>
            @endforeach
        </div>
        @endif

        {{-- Table Container --}}
        <div class="dir-table-area">
            <div class="dir-table-scroll">
                <table class="dir-table" style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
                    <thead style="background:#f9fafb; position:sticky; top:0; z-index:5; border-bottom:1px solid #e5e7eb;">
                        <tr>
                            <th class="dir-th dir-th-no" style="color:#4b5563; font-weight:700;">No</th>
                            <th class="dir-th" style="color:#4b5563; font-weight:700;">Nama Siswa</th>
                            <th class="dir-th" style="color:#4b5563; font-weight:700;">NISN</th>
                            <th class="dir-th" style="color:#4b5563; font-weight:700;">Kelas</th>
                            <th class="dir-th" style="color:#4b5563; font-weight:700; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="directoryTableBody">
                        @forelse($siswas as $siswa)
                            <tr class="dir-siswa-row"
                                data-nama="{{ strtolower($siswa->nama) }}"
                                data-nisn="{{ $siswa->nisn }}"
                                data-kelas="{{ strtolower($siswa->kelas) }}"
                                style="border-bottom:1px solid #f3f4f6;">
                                <td class="dir-td dir-td-num" style="color:#9ca3af;">{{ $loop->iteration }}</td>
                                <td class="dir-td dir-td-name" style="font-weight:600; color:#111827;">
                                    {{ $siswa->nama }}
                                </td>
                                <td class="dir-td" style="color:#6b7280; font-family:monospace;">
                                    {{ $siswa->nisn ?? '-' }}
                                </td>
                                <td class="dir-td">
                                    <span style="background:rgba(109,20,8,0.1); color:#6D1408; font-weight:700; font-size:11px; padding:3px 8px; border-radius:6px; border:1px solid rgba(109,20,8,0.2);">
                                        {{ $siswa->kelas }}
                                    </span>
                                </td>
                                <td class="dir-td" style="text-align:right;">
                                    <button type="button"
                                            class="dir-pick-btn"
                                            onclick="selectStudentForReport({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}', '{{ $siswa->kelas }}')"
                                            style="background:#6D1408; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                                        <i class="fa-solid fa-bullhorn"></i>
                                        Pilih untuk Melapor
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="dir-td dir-td-empty">
                                    Belum ada data siswa ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="dirEmptyMessage" class="dir-empty-msg" style="display:none;">
                <i class="fa-solid fa-user-slash" style="font-size:28px; margin-bottom:8px; display:block;"></i>
                Tidak ada siswa yang sesuai dengan filter pencarian.
            </div>
        </div>
    </div>



    {{-- =========================================================
         5. RIWAYAT LAPORAN
    ========================================================= --}}
    <div
        class="walas-card"
        id="riwayat-lapor"
    >

        <div class="walas-card-header">

            <div class="walas-card-header-main">

                <div class="walas-card-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>

                <div class="walas-card-header-text">

                    <h2>
                        Riwayat Laporan Saya
                    </h2>

                    <p>
                        Daftar laporan pelanggaran yang telah Anda kirim.
                    </p>

                </div>

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

                                <div class="student-info">

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

                                    @if(!empty($laporan->catatan_verifikasi))

                                        <span>
                                            {{ $laporan->catatan_verifikasi }}
                                        </span>

                                    @else

                                        <span>
                                            Laporan tidak disetujui oleh BK / PDS.
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
    overflow-wrap: anywhere;
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
    min-width: 0;
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
    font-size: clamp(19px, 5.2vw, 26px);
    font-weight: 700;

    margin: 0 0 10px;

    line-height: 1.3;

    overflow-wrap: anywhere;
}

.walas-banner-subtitle {
    font-size: clamp(12px, 3.4vw, 14px);

    line-height: 1.6;

    color: rgba(255, 255, 255, 0.88);

    margin: 0;

    overflow-wrap: anywhere;
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

    grid-template-columns: repeat(4, minmax(0, 1fr));

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

    min-width: 0;

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

    min-width: 0;
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

    min-width: 0;
}

.walas-card-header {
    display: flex;

    align-items: flex-start;

    flex-wrap: wrap;

    gap: 16px;

    margin-bottom: 24px;

    padding-bottom: 18px;

    border-bottom: 1px solid #f3f4f6;
}

.walas-card-header-main {
    display: flex;

    align-items: flex-start;

    gap: 14px;

    min-width: 0;

    flex: 1 1 320px;
}

.walas-card-header-text {
    min-width: 0;
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

.walas-alert > div {
    min-width: 0;

    overflow-wrap: anywhere;
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

    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);

    gap: 20px;
}

.walas-form-group {
    display: flex;

    flex-direction: column;

    min-width: 0;
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

    max-width: 100%;

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

    overflow-wrap: anywhere;
}


/* =========================================================
   TOM SELECT (ENHANCED SELECT)
   ========================================================= */

.searchable-select,
.searchable-select.ts-wrapper,
.ts-wrapper,
.ts-control {
    width: 100%;

    max-width: 100%;
}

.ts-wrapper .ts-control {
    min-height: 42px;
    border-radius: 10px;
}

.ts-wrapper .ts-dropdown {
    max-width: 100%;

    max-height: 260px;

    overflow-y: auto;
}

.ts-wrapper .ts-dropdown .active {
    overflow-wrap: anywhere;
}


/* =========================================================
   MODE PELANGGARAN
========================================================= */

.pelanggaran-mode {
    display: grid;

    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);

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

    min-width: 0;

    overflow-wrap: anywhere;
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

    grid-template-columns: minmax(0, 1fr) 160px;

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

.walas-preview-img {
    max-width: min(200px, 100%);
    width: auto;
    height: auto;
    max-height: 160px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    display: block;
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

    min-width: 0;

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

    gap: 10px;

    margin-bottom: 14px;
}

.report-student {
    display: flex;

    align-items: center;

    gap: 12px;

    min-width: 0;
}

.student-info {
    min-width: 0;

    overflow-wrap: anywhere;
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

    overflow-wrap: anywhere;
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

    max-width: 100%;

    overflow-wrap: anywhere;
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

    overflow-wrap: anywhere;
}

.report-photo img {
    max-width: min(140px, 100%);

    width: auto;

    height: auto;

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

    min-width: 0;

    overflow-wrap: anywhere;
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

/* Class Pill Filter */
.class-pill-btn {
    border: 1px solid #d1d5db;
    background: #fff;
    color: #4b5563;
    padding: 5px 12px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
}

.class-pill-btn:hover {
    background: #f3f4f6;
    color: #111827;
}

.class-pill-btn.active {
    background: #6D1408;
    color: #fff;
    border-color: #6D1408;
}

.class-pill-btn {
    white-space: nowrap;
}


/* =========================================================
   DIREKTORI SISWA (SEARCH + TABLE)
   ========================================================= */

.dir-controls {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    min-width: 0;
    flex: 1 1 260px;
}

.dir-search {
    position: relative;
    min-width: 0;
    flex: 1 1 220px;
    max-width: 100%;
}

.dir-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 13px;
    pointer-events: none;
}

.dir-search-input,
.dir-kelas-select {
    max-width: 100%;
    min-width: 0;
    font-size: 13px;
}

.dir-pill-row {
    padding: 12px 24px 0;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
}

.dir-pill-label {
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    margin-right: 4px;
}

.dir-table-area {
    padding: 18px 24px 24px;
    min-width: 0;
}

.dir-table-scroll {
    max-height: 480px;
    overflow: auto;
    -webkit-overflow-scrolling: touch;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.dir-table {
    min-width: 560px;
}

.dir-th {
    padding: 12px 16px;
    white-space: nowrap;
}

.dir-th-no {
    width: 60px;
}

.dir-td {
    padding: 12px 16px;
    vertical-align: middle;
}

.dir-td-name {
    overflow-wrap: anywhere;
}

.dir-td-num {
    white-space: nowrap;
}

.dir-td-empty {
    text-align: center;
    padding: 30px;
    color: #9ca3af;
}

.dir-empty-msg {
    text-align: center;
    padding: 30px;
    color: #9ca3af;
}

.dir-pick-btn {
    padding: 7px 14px;
    font-size: 12px;
    max-width: 100%;
    white-space: nowrap;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1024px) {

    .walas-card {
        padding: 24px 24px;
    }

    .walas-banner {
        padding: 26px 26px;
    }

    .walas-stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}


@media (max-width: 900px) {

    .walas-banner {
        flex-direction: column;

        align-items: flex-start;
    }

    .walas-banner-actions {
        width: 100%;

        flex-direction: row;
        flex-wrap: wrap;
    }

    .btn-banner-primary,
    .btn-banner-secondary {
        flex: 1;
        min-width: 140px;
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

    .walas-card-header {
        flex-direction: column;

        align-items: flex-start;
    }

    .walas-card-header-main,
    .dir-controls {
        width: 100%;
    }
}


@media (max-width: 768px) {

    .walas-container {
        gap: 16px;
    }

    .walas-card {
        padding: 20px 16px;

        border-radius: 16px;
    }

    .walas-banner {
        padding: 22px 18px;

        border-radius: 16px;
    }

    .walas-banner-actions {
        flex-direction: column;
    }

    .btn-banner-primary,
    .btn-banner-secondary {
        width: 100%;
        min-width: 0;
    }

    .walas-stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .report-item {
        padding: 16px;
    }

    .report-header {
        align-items: flex-start;

        flex-direction: column;

        gap: 12px;
    }

    .report-feedback {
        padding: 12px;
    }

    .walas-form-actions {
        justify-content: stretch;
    }

    .btn-submit-laporan {
        width: 100%;

        justify-content: center;
    }

    /* Directory table: tighter cells + narrower min width so it
       stays usable with horizontal scroll on phones. */
    .dir-table {
        min-width: 460px;
    }

    .dir-table-area {
        padding: 14px 16px 18px;
    }

    .dir-pill-row {
        padding: 12px 16px 0;
    }

    .dir-table-scroll {
        max-height: 60vh;
    }

    .class-pill-btn {
        min-height: 36px;
    }
}


@media (max-width: 640px) {

    .walas-stats-grid {
        grid-template-columns: minmax(0, 1fr);
    }

    .walas-stat-card {
        padding: 16px 16px;
    }

    .stat-icon-wrapper {
        width: 42px;
        height: 42px;
        font-size: 18px;
    }

    .dir-controls {
        flex-direction: column;

        align-items: stretch;
    }

    .dir-search,
    .dir-kelas-select {
        width: 100%;
        max-width: 100%;
    }
}


@media (max-width: 576px) {

    .walas-input,
    .walas-textarea,
    .dir-search-input,
    .dir-kelas-select {
        font-size: 16px;
    }

    .walas-form-row,
    .walas-detail-row,
    .pelanggaran-mode {
        gap: 14px;
    }

    .mode-option {
        padding: 12px;
    }

    .walas-card-header-main {
        flex-wrap: wrap;
    }

    .walas-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        font-size: 16px;
    }

    .walas-alert {
        padding: 12px 14px;
    }

    .ts-wrapper .ts-dropdown {
        position: static !important;

        width: 100% !important;
        max-width: 100% !important;

        max-height: 260px;
        overflow-y: auto;
    }

    .ts-wrapper.dropdown-open .ts-control {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .walas-empty-state {
        padding: 30px 10px;
    }
}


@media (max-width: 480px) {

    .walas-card {
        padding: 16px 12px;

        border-radius: 14px;
    }

    .walas-banner {
        padding: 20px 15px;
    }

    .walas-banner-badge {
        font-size: 10px;
        padding: 5px 11px;
    }

    .report-item {
        padding: 14px;
        border-radius: 12px;
    }

    .student-avatar {
        width: 34px;
        height: 34px;
        font-size: 15px;
    }

    .dir-table {
        min-width: 400px;
    }

    .dir-th,
    .dir-td {
        padding: 9px 10px;
    }

    .class-pill-btn {
        padding: 6px 10px;
    }

    .dir-pick-btn {
        padding: 6px 10px;

        font-size: 11px;
    }

    .walas-file-wrapper {
        padding: 18px 12px;
    }
}


@media (max-width: 400px) {

    .walas-card {
        padding: 14px 10px;
    }

    .walas-banner {
        padding: 18px 12px;
    }

    .walas-card-header h2 {
        font-size: 16px;
    }

    .btn-submit-laporan {
        padding: 12px 14px;

        font-size: 13px;
    }

    .badge-status {
        font-size: 11px;
        padding: 5px 10px;
    }

    .dir-table {
        min-width: 360px;
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
| FILTER KELAS UNTUK DROPDOWN SISWA
|--------------------------------------------------------------------------
*/

function filterSiswaDropdown(kelas) {

    const select =
        document.getElementById('siswa_id');

    if (!select) {
        return;
    }

    const target =
        (kelas || '').toString().toLowerCase().trim();

    const options =
        select.querySelectorAll('option');


    options.forEach(function (opt) {

        if (!opt.value) {
            return;
        }

        const optKelas =
            (opt.getAttribute('data-kelas') || '')
                .toLowerCase()
                .trim();

        if (!target || optKelas === target) {

            opt.style.display = '';

        } else {

            opt.style.display = 'none';

            if (opt.selected) {
                opt.selected = false;
                select.value = '';
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| PILIH SISWA DARI TABEL DIREKTORI
|--------------------------------------------------------------------------
*/

function selectStudentForReport(id, nama, kelas) {

    const select =
        document.getElementById('siswa_id');

    const filterKelas =
        document.getElementById('form_filter_kelas');


    if (filterKelas) {
        filterKelas.value = kelas;
        filterSiswaDropdown(kelas);
    }

    if (select) {
        select.value = id;
    }


    /*
     * Scroll ke form lalu beri highlight singkat
     * supaya pengguna tahu form sudah terisi.
     */

    const formCard =
        document.getElementById('form-lapor');

    if (formCard) {

        formCard.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

        formCard.style.transition =
            'box-shadow 0.3s, border-color 0.3s';

        formCard.style.borderColor = '#6D1408';

        formCard.style.boxShadow =
            '0 0 0 4px rgba(109, 20, 8, 0.2)';

        setTimeout(function () {

            formCard.style.borderColor = '';

            formCard.style.boxShadow = '';

        }, 1800);
    }
}


/*
|--------------------------------------------------------------------------
| FILTER TABEL DIREKTORI SISWA
|--------------------------------------------------------------------------
*/

function filterDirectoryTable() {

    const searchInput =
        document.getElementById('searchSiswaInput');

    const kelasInput =
        document.getElementById('selectDirectoryKelas');

    const searchVal =
        (searchInput ? searchInput.value : '')
            .toLowerCase()
            .trim();

    const kelasVal =
        (kelasInput ? kelasInput.value : '')
            .toLowerCase()
            .trim();

    const rows =
        document.querySelectorAll('.dir-siswa-row');

    let visibleCount = 0;


    rows.forEach(function (row) {

        const nama =
            (row.getAttribute('data-nama') || '')
                .toLowerCase();

        const nisn =
            (row.getAttribute('data-nisn') || '')
                .toLowerCase();

        const kelas =
            (row.getAttribute('data-kelas') || '')
                .toLowerCase()
                .trim();

        const matchSearch =
            !searchVal ||
            nama.includes(searchVal) ||
            nisn.includes(searchVal);

        const matchKelas =
            !kelasVal ||
            kelas === kelasVal;

        if (matchSearch && matchKelas) {

            row.style.display = '';

            visibleCount++;

        } else {

            row.style.display = 'none';
        }
    });


    const emptyMsg =
        document.getElementById('dirEmptyMessage');

    if (emptyMsg) {
        emptyMsg.style.display =
            visibleCount === 0 ? 'block' : 'none';
    }
}


/*
|--------------------------------------------------------------------------
| FILTER CEPAT KELAS (CLASS PILL)
|--------------------------------------------------------------------------
*/

function setQuickClassFilter(kelas, btn) {

    document
        .querySelectorAll('.class-pill-btn')
        .forEach(function (b) {
            b.classList.remove('active');
        });

    if (btn) {
        btn.classList.add('active');
    }

    const select =
        document.getElementById('selectDirectoryKelas');

    if (select) {
        select.value = kelas;
    }

    filterDirectoryTable();
}


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
         * Terapkan mode pelaporan yang aktif.
         */

        updatePelanggaranMode();


        /*
         * Jika siswa sudah terpilih (misalnya laporan
         * dibuat dari halaman daftar siswa), saring
         * dropdown dan direktori ke kelas siswa tersebut.
         */

        const select =
            document.getElementById('siswa_id');

        const filterKelas =
            document.getElementById('form_filter_kelas');

        if (select && select.value && filterKelas) {

            const selectedOpt =
                select.querySelector(
                    'option[value="' + select.value + '"]'
                );

            if (selectedOpt) {

                const kelas =
                    selectedOpt.getAttribute('data-kelas');

                if (kelas) {

                    filterKelas.value = kelas;

                    filterSiswaDropdown(kelas);
                }
            }
        }
    }
);
</script>

@endsection
