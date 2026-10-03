@extends('layouts.app')

@section('title', 'Portal Lapor Pelanggaran - ' . (auth()->user()->role_label ?? 'Guru'))
@section('page_title', 'Portal Lapor Pelanggaran')

@section('content')

@php
    $user     = auth()->user();
    $roleIcon = $user->isBk()
        ? 'fa-user-nurse'
        : ($user->isGuru()
            ? 'fa-graduation-cap'
            : ($user->isWalas() ? 'fa-user-tie' : 'fa-shield-halved'));

    $today      = date('Y-m-d');
    $multiKelas = isset($kelasList) && count($kelasList) > 1;

    $histCount = [
        'all'          => $laporans->count(),
        'pending'      => $laporans->where('status', 'pending')->count(),
        'diverifikasi' => $laporans->where('status', 'diverifikasi')->count(),
        'ditolak'      => $laporans->where('status', 'ditolak')->count(),
    ];
@endphp

<div class="walas-container">

    {{-- =========================================================
         1. WELCOME BANNER
    ========================================================= --}}
    <div class="walas-banner">

        <div class="walas-banner-content">

            <div class="walas-banner-badge">
                <i class="fa-solid {{ $roleIcon }}"></i>
                <span>Portal Pelaporan {{ $user->role_label }}</span>
            </div>

            <h1 class="walas-banner-title">Selamat Datang, {{ $user->name }}</h1>

            <p class="walas-banner-subtitle">
                @if($user->isGuru())
                    Sebagai Guru Khusus, Anda memiliki akses penuh ke seluruh kelas ({{ count($kelasList ?? []) }} Kelas) dan seluruh siswa untuk melaporkan temuan pelanggaran secara langsung. Laporan Anda akan masuk ke antrean verifikasi untuk ditinjau oleh tim <strong>Guru PDS</strong>.
                @elseif($user->isBk())
                    Sebagai Guru BK, Anda dapat melaporkan temuan pelanggaran siswa. Laporan yang Anda kirim akan masuk ke antrean verifikasi untuk ditinjau dan ditentukan sanksi/poin oleh tim <strong>Guru PDS</strong>.
                @elseif($user->isWalas())
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

        <div class="walas-stat-card stat-total">
            <div class="stat-icon-wrapper"><i class="fa-solid fa-folder-open"></i></div>
            <div class="stat-data">
                <span class="stat-number">{{ $stats['total'] ?? 0 }}</span>
                <span class="stat-label">Total Laporan Dikirim</span>
            </div>
        </div>

        <div class="walas-stat-card stat-pending">
            <div class="stat-icon-wrapper"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="stat-data">
                <span class="stat-number">{{ $stats['pending'] ?? 0 }}</span>
                <span class="stat-label">Menunggu Verifikasi PDS</span>
            </div>
        </div>

        <div class="walas-stat-card stat-verified">
            <div class="stat-icon-wrapper"><i class="fa-solid fa-circle-check"></i></div>
            <div class="stat-data">
                <span class="stat-number">{{ $stats['verified'] ?? 0 }}</span>
                <span class="stat-label">Diverifikasi &amp; Diproses</span>
            </div>
        </div>

        <div class="walas-stat-card stat-rejected">
            <div class="stat-icon-wrapper"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="stat-data">
                <span class="stat-number">{{ $stats['rejected'] ?? 0 }}</span>
                <span class="stat-label">Laporan Ditolak</span>
            </div>
        </div>

    </div>


    {{-- =========================================================
         3. FORMULIR LAPOR PELANGGARAN

         Catatan: flash session('success') / 'error' dan ringkasan
         $errors sudah otomatis ditampilkan oleh layouts/app.blade.php,
         jadi tidak diulang di sini (sebelumnya tampil dua kali).
    ========================================================= --}}
    <div class="walas-card" id="form-lapor">

        <div class="walas-card-header">
            <div class="walas-card-header-main">
                <div class="walas-card-icon"><i class="fa-solid fa-bullhorn"></i></div>
                <div class="walas-card-header-text">
                    <h2>Formulir Lapor Pelanggaran</h2>
                    <p>Isi rincian temuan pelanggaran siswa di bawah ini secara objektif dan lengkap.</p>
                </div>
            </div>
        </div>

        <form
            action="{{ route('lapor.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="walas-form"
            id="laporForm"
        >
            @csrf

            {{-- SISWA + TANGGAL --}}
            <div class="walas-form-row">

                @if($multiKelas)
                <div class="walas-form-group">
                    <label class="walas-label" for="form_filter_kelas">
                        <i class="fa-solid fa-filter"></i> Saring Kelas Siswa
                        <span class="optional">(Opsional)</span>
                    </label>
                    <select id="form_filter_kelas" class="walas-input">
                        <option value="">-- Semua Kelas ({{ count($kelasList) }} Kelas) --</option>
                        @foreach($kelasList as $kelasOpt)
                            <option value="{{ $kelasOpt }}">{{ $kelasOpt }}</option>
                        @endforeach
                    </select>
                    <span class="walas-help">Pilih kelas jika ingin menyaring daftar siswa.</span>
                </div>
                @endif

                <div class="walas-form-group">
                    <label class="walas-label" for="siswa_id">
                        Pilih Siswa Pelanggar
                        <span class="optional">(Opsional)</span>
                    </label>

                    <select name="siswa_id" id="siswa_id" class="walas-input searchable-select">
                        <option value="">-- Tidak memilih siswa --</option>

                        @foreach($siswas as $siswa)
                            <option
                                value="{{ $siswa->id }}"
                                data-kelas="{{ $siswa->kelas }}"
                                data-nisn="{{ $siswa->nisn }}"
                                {{ (string) old('siswa_id', $selectedSiswaId ?? '') === (string) $siswa->id ? 'selected' : '' }}
                            >
                                {{ $siswa->nama }}@if($siswa->kelas) — {{ $siswa->kelas }}@endif @if(!empty($siswa->nisn)) (NISN: {{ $siswa->nisn }})@endif
                            </option>
                        @endforeach
                    </select>

                    <span class="walas-help">Pilih siswa jika laporan ditujukan kepada siswa tertentu.</span>
                </div>

                <div class="walas-form-group">
                    <label class="walas-label" for="tanggal">
                        Tanggal Kejadian <span class="required">*</span>
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        id="tanggal"
                        class="walas-input"
                        value="{{ old('tanggal', $today) }}"
                        max="{{ $today }}"
                        required
                    >
                    <span class="walas-help">Tanggal saat pelanggaran terjadi atau ditemukan.</span>
                </div>

            </div>


            {{-- JENIS / MODE PELANGGARAN --}}
            <div class="walas-form-group">

                <span class="walas-label" id="modeLabel">
                    Nama / Jenis Pelanggaran <span class="required">*</span>
                </span>

                <div class="pelanggaran-mode" role="radiogroup" aria-labelledby="modeLabel">

                    <label class="mode-option">
                        <input type="radio" name="pelanggaran_mode" value="aturan"
                               {{ old('pelanggaran_mode', 'aturan') === 'aturan' ? 'checked' : '' }}>
                        <div class="mode-option-icon"><i class="fa-solid fa-list-check"></i></div>
                        <div class="mode-option-text">
                            <strong>Pilih dari aturan</strong>
                            <small>Gunakan aturan pelanggaran yang tersedia</small>
                        </div>
                    </label>

                    <label class="mode-option">
                        <input type="radio" name="pelanggaran_mode" value="manual"
                               {{ old('pelanggaran_mode') === 'manual' ? 'checked' : '' }}>
                        <div class="mode-option-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                        <div class="mode-option-text">
                            <strong>Isi secara manual</strong>
                            <small>Tulis nama pelanggaran sendiri</small>
                        </div>
                    </label>

                </div>

            </div>


            {{-- MODE ATURAN --}}
            <div id="modeAturan" class="walas-form-group">

                <label class="walas-label" for="aturan_pelanggaran_id">
                    Aturan Pelanggaran <span class="required">*</span>
                </label>

                <select name="aturan_pelanggaran_id" id="aturan_pelanggaran_id" class="walas-input">
                    <option value="">-- Pilih Aturan Pelanggaran --</option>

                    @foreach($aturanPelanggarans as $aturan)
                        <option
                            value="{{ $aturan->id }}"
                            data-kategori="{{ $aturan->kategori }}"
                            data-poin="{{ $aturan->poin }}"
                            {{ (string) old('aturan_pelanggaran_id') === (string) $aturan->id ? 'selected' : '' }}
                        >
                            {{ $aturan->kode }} — {{ $aturan->nama }} ({{ $aturan->poin }} poin)
                        </option>
                    @endforeach
                </select>

            </div>


            {{-- MODE MANUAL --}}
            <div id="modeManual" class="is-hidden">

                <div class="walas-form-group">
                    <label class="walas-label" for="jenis_pelanggaran_manual">
                        Nama Pelanggaran <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="jenis_pelanggaran_manual"
                        id="jenis_pelanggaran_manual"
                        class="walas-input"
                        value="{{ old('jenis_pelanggaran_manual') }}"
                        placeholder="Contoh: Merokok di area toilet lantai 2"
                    >
                    <span class="walas-help">Tuliskan nama atau jenis pelanggaran secara singkat dan jelas.</span>
                </div>

            </div>


            {{-- KATEGORI + POIN --}}
            <div class="walas-detail-row">

                <div class="walas-form-group">
                    <label class="walas-label" for="kategori_manual">
                        Kategori <span class="required">*</span>
                    </label>
                    <select name="kategori_manual" id="kategori_manual" class="walas-input">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(['Ringan', 'Sedang', 'Berat', 'Luar Biasa'] as $kat)
                            <option value="{{ $kat }}" {{ old('kategori_manual') === $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="walas-form-group">
                    <label class="walas-label" for="poin_manual">
                        Poin <span class="required">*</span>
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

                <span class="walas-help walas-detail-hint" id="autoHint">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Kategori dan poin terisi otomatis sesuai aturan yang dipilih.
                </span>

            </div>


            {{-- KETERANGAN --}}
            <div class="walas-form-group">
                <label class="walas-label" for="keterangan">
                    Kronologi &amp; Deskripsi Kejadian
                    <span class="optional">(Opsional)</span>
                </label>
                <textarea
                    name="keterangan"
                    id="keterangan"
                    class="walas-textarea"
                    rows="4"
                    placeholder="Jelaskan secara rinci kronologi temuan, saksi yang melihat, atau keterangan lain yang memperjelas laporan..."
                >{{ old('keterangan') }}</textarea>
            </div>


            {{-- FOTO BUKTI --}}
            <div class="walas-form-group">

                <label class="walas-label" for="foto_bukti">
                    Unggah Foto Bukti
                    <span class="optional">(Opsional)</span>
                </label>

                <div class="walas-file-wrapper" id="fileWrapper">
                    <input
                        type="file"
                        name="foto_bukti"
                        id="foto_bukti"
                        class="walas-file-input"
                        accept="image/jpeg,image/png,image/webp"
                    >
                    <div class="walas-file-dummy">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Klik untuk memilih file foto bukti kejadian</span>
                        <small>Format yang didukung: JPG, PNG, WEBP (Maksimal 5 MB)</small>
                    </div>
                </div>

                <span class="walas-help walas-error is-hidden" id="fotoError"></span>

                <div id="imagePreviewContainer" class="walas-preview is-hidden">
                    <img id="imagePreview" class="walas-preview-img" src="" alt="Preview Bukti">
                    <div class="walas-preview-info">
                        <strong id="fotoName"></strong>
                        <small id="fotoSize"></small>
                        <button type="button" id="removeFoto" class="walas-link-danger">
                            <i class="fa-solid fa-trash"></i> Hapus foto
                        </button>
                    </div>
                </div>

            </div>


            {{-- SUBMIT --}}
            <div class="walas-form-actions">
                <button type="submit" class="btn-submit-laporan" id="btnSubmit">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Laporan ke BK / PDS</span>
                </button>
            </div>

        </form>

    </div>


    {{-- =========================================================
         4. DIREKTORI DATA KELAS & SISWA
    ========================================================= --}}
    <div class="walas-card walas-card-flush" id="direktori-siswa">

        <div class="walas-card-header walas-card-header-split">

            <div class="walas-card-header-main">
                <div class="walas-card-icon walas-card-icon-red">
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
                    <input type="search"
                           id="searchSiswaInput"
                           class="dir-search-input"
                           placeholder="Cari nama atau NISN..."
                           autocomplete="off">
                </div>

                @if($multiKelas)
                <select id="selectDirectoryKelas" class="dir-kelas-select" aria-label="Filter kelas">
                    <option value="">Semua Kelas ({{ count($kelasList) }})</option>
                    @foreach($kelasList as $kelasItem)
                        <option value="{{ \Illuminate\Support\Str::lower($kelasItem) }}">{{ $kelasItem }}</option>
                    @endforeach
                </select>
                @endif
            </div>

        </div>

        @if($multiKelas)
        <div class="dir-pill-row" id="dirPillRow">
            <span class="dir-pill-label">Filter Cepat Kelas:</span>
            <button type="button" class="pill-btn active" data-kelas="">
                Semua ({{ $siswas->count() }})
            </button>
            @foreach($kelasList as $kelasItem)
                <button type="button" class="pill-btn"
                        data-kelas="{{ \Illuminate\Support\Str::lower($kelasItem) }}">
                    {{ $kelasItem }}
                </button>
            @endforeach
        </div>
        @endif

        <div class="dir-table-area">

            <div class="dir-result-info">
                Menampilkan <strong id="dirCount">{{ $siswas->count() }}</strong> siswa
            </div>

            <div class="dir-table-scroll">
                <table class="dir-table">
                    <thead>
                        <tr>
                            <th class="dir-th dir-th-no">No</th>
                            <th class="dir-th">Nama Siswa</th>
                            <th class="dir-th">NISN</th>
                            <th class="dir-th">Kelas</th>
                            <th class="dir-th dir-th-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="directoryTableBody">
                        @forelse($siswas as $siswa)
                            <tr class="dir-siswa-row"
                                data-nama="{{ \Illuminate\Support\Str::lower($siswa->nama) }}"
                                data-nisn="{{ $siswa->nisn }}"
                                data-kelas="{{ \Illuminate\Support\Str::lower($siswa->kelas ?? '') }}">
                                <td class="dir-td dir-td-num">{{ $loop->iteration }}</td>
                                <td class="dir-td dir-td-name">{{ $siswa->nama }}</td>
                                <td class="dir-td dir-td-nisn">{{ $siswa->nisn ?? '-' }}</td>
                                <td class="dir-td">
                                    <span class="dir-kelas-badge">{{ $siswa->kelas ?? '-' }}</span>
                                </td>
                                <td class="dir-td dir-td-right">
                                    <button type="button"
                                            class="dir-pick-btn js-pick"
                                            data-id="{{ $siswa->id }}"
                                            data-kelas="{{ $siswa->kelas }}">
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

            <div id="dirEmptyMessage" class="dir-empty-msg is-hidden">
                <i class="fa-solid fa-user-slash"></i>
                Tidak ada siswa yang sesuai dengan filter pencarian.
            </div>

        </div>

    </div>


    {{-- =========================================================
         5. RIWAYAT LAPORAN
    ========================================================= --}}
    <div class="walas-card" id="riwayat-lapor">

        <div class="walas-card-header">
            <div class="walas-card-header-main">
                <div class="walas-card-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div class="walas-card-header-text">
                    <h2>Riwayat Laporan Saya</h2>
                    <p>Daftar laporan pelanggaran yang telah Anda kirim.</p>
                </div>
            </div>
        </div>

        @if($laporans->count() > 0)

            <div class="hist-tabs" id="histTabs">
                <button type="button" class="pill-btn active" data-status="all">
                    Semua ({{ $histCount['all'] }})
                </button>
                <button type="button" class="pill-btn" data-status="pending">
                    Menunggu ({{ $histCount['pending'] }})
                </button>
                <button type="button" class="pill-btn" data-status="diverifikasi">
                    Diverifikasi ({{ $histCount['diverifikasi'] }})
                </button>
                <button type="button" class="pill-btn" data-status="ditolak">
                    Ditolak ({{ $histCount['ditolak'] }})
                </button>
            </div>

            <div class="walas-reports-list">

                @foreach($laporans as $laporan)

                    <div class="report-item" data-status="{{ $laporan->status }}">

                        {{-- HEADER --}}
                        <div class="report-header">

                            <div class="report-student">
                                <div class="student-avatar"><i class="fa-solid fa-user"></i></div>
                                <div class="student-info">
                                    <h3 class="student-name">
                                        {{ $laporan->siswa?->nama ?? 'Siswa tidak dipilih' }}
                                    </h3>
                                    <span class="student-class">
                                        {{ $laporan->siswa ? ($laporan->siswa->kelas ?? '-') : '-' }}
                                    </span>
                                </div>
                            </div>

                            @if($laporan->status === 'pending')
                                <span class="badge-status badge-pending">
                                    <i class="fa-solid fa-hourglass-half"></i> Menunggu Verifikasi
                                </span>
                            @elseif($laporan->status === 'diverifikasi')
                                <span class="badge-status badge-verified">
                                    <i class="fa-solid fa-circle-check"></i> Diverifikasi
                                </span>
                            @elseif($laporan->status === 'ditolak')
                                <span class="badge-status badge-rejected">
                                    <i class="fa-solid fa-circle-xmark"></i> Ditolak
                                </span>
                            @else
                                <span class="badge-status">{{ ucfirst($laporan->status) }}</span>
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

                            @if($laporan->keterangan)
                                <p class="report-description">{{ $laporan->keterangan }}</p>
                            @endif

                            @if($laporan->foto_bukti)
                                <div class="report-photo">
                                    <a href="{{ asset('storage/' . $laporan->foto_bukti) }}"
                                       target="_blank" rel="noopener">
                                        <img src="{{ asset('storage/' . $laporan->foto_bukti) }}"
                                             alt="Foto bukti" loading="lazy">
                                    </a>
                                    <span class="photo-hint">Klik foto untuk melihat ukuran penuh.</span>
                                </div>
                            @endif

                        </div>

                        {{-- FEEDBACK --}}
                        @if($laporan->status === 'diverifikasi')

                            <div class="report-feedback feedback-verified">
                                <i class="fa-solid fa-circle-check feedback-icon"></i>
                                <div class="feedback-text">
                                    <strong>Laporan telah diverifikasi.</strong>
                                    <span>Laporan telah diproses oleh PDS.</span>
                                    @if(isset($laporan->poin))
                                        <span class="points-badge">{{ $laporan->poin }} Poin</span>
                                    @endif
                                </div>
                            </div>

                        @elseif($laporan->status === 'ditolak')

                            <div class="report-feedback feedback-rejected">
                                <i class="fa-solid fa-circle-xmark feedback-icon"></i>
                                <div class="feedback-text">
                                    <strong>Laporan ditolak.</strong>
                                    <span>
                                        {{ !empty($laporan->catatan_verifikasi)
                                            ? $laporan->catatan_verifikasi
                                            : 'Laporan tidak disetujui oleh BK / PDS.' }}
                                    </span>
                                </div>
                            </div>

                        @else

                            <div class="report-feedback feedback-pending">
                                <i class="fa-solid fa-hourglass-half feedback-icon"></i>
                                <div class="feedback-text">
                                    <strong>Menunggu verifikasi.</strong>
                                    <span class="feedback-note">Laporan akan ditinjau oleh PDS sebelum diproses.</span>
                                </div>
                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

            <div class="walas-empty-state is-hidden" id="histNoResult">
                <div class="empty-icon"><i class="fa-solid fa-filter-circle-xmark"></i></div>
                <h3>Tidak Ada Laporan</h3>
                <p>Tidak ada laporan dengan status tersebut.</p>
            </div>

            @if(method_exists($laporans, 'hasPages') && $laporans->hasPages())
                <div class="walas-pagination">
                    {{ $laporans->links() }}
                </div>
            @endif

        @else

            <div class="walas-empty-state">
                <div class="empty-icon"><i class="fa-solid fa-inbox"></i></div>
                <h3>Belum Ada Laporan</h3>
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

@include('partials.ui')

/* =========================================================
   PORTAL LAPOR PELANGGARAN
   Semua aturan di-scope ke .walas-container supaya tidak
   bentrok dengan class generik di partials/ui.blade
   (.stat-label, .student-avatar, .badge-status, dll).
========================================================= */

html {
    scroll-behavior: smooth;
}

.walas-container {
    width: 100%;
    max-width: 100%;
    display: flex;
    flex-direction: column;
    gap: 24px;
    overflow-wrap: break-word;
}

.walas-container .is-hidden {
    display: none !important;
}

.walas-container [id] {
    scroll-margin-top: 16px;
}


/* ---------- BANNER ---------- */

.walas-container .walas-banner {
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 28px;
    padding: 32px 36px;
    border-radius: 20px;
    color: #fff;
    background: linear-gradient(135deg, var(--primary) 0%, #901C0D 50%, #4A0D05 100%);
    box-shadow: 0 10px 30px rgba(109, 20, 8, 0.2);
}

.walas-container .walas-banner::after {
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

.walas-container .walas-banner-content {
    max-width: 680px;
    min-width: 0;
}

.walas-container .walas-banner-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    padding: 6px 14px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.walas-container .walas-banner-title {
    margin: 0 0 10px;
    font-size: clamp(19px, 5.2vw, 26px);
    font-weight: 700;
    line-height: 1.3;
    overflow-wrap: anywhere;
}

.walas-container .walas-banner-subtitle {
    margin: 0;
    font-size: clamp(12px, 3.4vw, 14px);
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.88);
}

.walas-container .walas-banner-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex-shrink: 0;
}

.walas-container .btn-banner-primary,
.walas-container .btn-banner-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    transition: all 0.2s ease;
}

.walas-container .btn-banner-primary {
    background: #fff;
    color: var(--primary);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
}

.walas-container .btn-banner-primary:hover {
    background: #f8fafc;
    color: #550f06;
    transform: translateY(-2px);
}

.walas-container .btn-banner-secondary {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.walas-container .btn-banner-secondary:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}


/* ---------- STAT CARDS ---------- */

.walas-container .walas-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
}

.walas-container .walas-stat-card {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
    padding: 20px 22px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.walas-container .walas-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.walas-container .stat-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    border-radius: 12px;
    font-size: 20px;
}

.walas-container .stat-total .stat-icon-wrapper    { background: #eff6ff; color: #2563eb; }
.walas-container .stat-pending .stat-icon-wrapper  { background: #fef3c7; color: #d97706; }
.walas-container .stat-verified .stat-icon-wrapper { background: #dcfce7; color: #16a34a; }
.walas-container .stat-rejected .stat-icon-wrapper { background: #fee2e2; color: #dc2626; }

.walas-container .stat-data {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.walas-container .stat-number {
    color: #111827;
    font-size: 24px;
    font-weight: 700;
    line-height: 1.1;
}

.walas-container .stat-label {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0;
    text-transform: none;
}


/* ---------- CARD UMUM ---------- */

.walas-container .walas-card {
    min-width: 0;
    padding: 28px 32px;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.04);
}

/* Kartu yang isinya (tabel) punya padding sendiri */
.walas-container .walas-card-flush {
    padding: 0;
    overflow: hidden;
}

.walas-container .walas-card-flush .walas-card-header {
    margin: 0;
    padding: 24px 28px 18px;
}

.walas-container .walas-card-header {
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
    padding-bottom: 18px;
    border-bottom: 1px solid #f3f4f6;
}

.walas-container .walas-card-header-split {
    justify-content: space-between;
}

.walas-container .walas-card-header-main {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    min-width: 0;
    flex: 1 1 320px;
}

.walas-container .walas-card-header-text {
    min-width: 0;
}

.walas-container .walas-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    border-radius: 12px;
    background: #FBEAE8;
    color: var(--primary);
    font-size: 18px;
}

.walas-container .walas-card-icon-red {
    background: #fef2f2;
    color: #b91c1c;
}

.walas-container .walas-card-header h2 {
    margin: 0 0 4px;
    color: #111827;
    font-size: 18px;
    font-weight: 700;
}

.walas-container .walas-card-header p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
}


/* ---------- FORM ---------- */

.walas-container .walas-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.walas-container .walas-form-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.walas-container .walas-form-group {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.walas-container .walas-label {
    display: block;
    margin-bottom: 6px;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
}

.walas-container .walas-label .required { color: #dc2626; }

.walas-container .walas-label .optional {
    color: #9ca3af;
    font-size: 11px;
    font-weight: 400;
}

.walas-container .walas-input,
.walas-container .walas-textarea {
    box-sizing: border-box;
    width: 100%;
    max-width: 100%;
    padding: 11px 14px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    background: #fff;
    color: #111827;
    font-family: inherit;
    font-size: 13px;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.walas-container .walas-input:focus,
.walas-container .walas-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(109, 20, 8, 0.12);
}

.walas-container .walas-input:disabled {
    background: #f3f4f6;
    color: #4b5563;
    -webkit-text-fill-color: #4b5563;
    opacity: 1;
    cursor: not-allowed;
}

.walas-container .walas-textarea {
    min-height: 90px;
    resize: vertical;
}

.walas-container .walas-help {
    margin-top: 5px;
    color: #6b7280;
    font-size: 11px;
    overflow-wrap: anywhere;
}

.walas-container .walas-error {
    color: #b91c1c;
    font-weight: 600;
}

/* Tom Select (jika diaktifkan di layout lewat .searchable-select) */
.walas-container .searchable-select,
.walas-container .ts-wrapper,
.walas-container .ts-control {
    width: 100%;
    max-width: 100%;
}

.walas-container .ts-wrapper .ts-control {
    min-height: 42px;
    border-radius: 10px;
}

.walas-container .ts-wrapper .ts-dropdown {
    max-width: 100%;
    max-height: 260px;
    overflow-y: auto;
}


/* ---------- MODE PELANGGARAN ---------- */

.walas-container .pelanggaran-mode {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.walas-container .mode-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
}

.walas-container .mode-option:hover {
    border-color: #cbd5e1;
}

.walas-container .mode-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.walas-container .mode-option-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    border-radius: 10px;
    background: #f9fafb;
    color: #6b7280;
    font-size: 16px;
    transition: all 0.15s ease;
}

.walas-container .mode-option-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.walas-container .mode-option-text strong {
    color: #374151;
    font-size: 13px;
    font-weight: 600;
}

.walas-container .mode-option-text small {
    color: #9ca3af;
    font-size: 11px;
}

.walas-container .mode-option:has(input:checked) {
    border-color: var(--primary);
    background: #fff8f7;
    box-shadow: 0 0 0 1px rgba(109, 20, 8, 0.08);
}

.walas-container .mode-option:has(input:checked) .mode-option-icon {
    background: var(--primary);
    color: #fff;
}

.walas-container .mode-option:has(input:checked) .mode-option-text strong {
    color: var(--primary);
}

/* Fokus keyboard terlihat walau radio disembunyikan */
.walas-container .mode-option:has(input:focus-visible) {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}


/* ---------- KATEGORI + POIN ---------- */

.walas-container .walas-detail-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 160px;
    gap: 20px;
}

.walas-container .walas-detail-hint {
    grid-column: 1 / -1;
    margin-top: -10px;
}

.walas-container .walas-detail-hint i {
    color: var(--primary);
}


/* ---------- UPLOAD FOTO ---------- */

.walas-container .walas-file-wrapper {
    position: relative;
    padding: 24px 16px;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    background: #fafafa;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.walas-container .walas-file-wrapper:hover,
.walas-container .walas-file-wrapper:focus-within {
    border-color: var(--primary);
    background: #fff8f8;
}

.walas-container .walas-file-input {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.walas-container .walas-file-dummy {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: #4b5563;
    pointer-events: none;
}

.walas-container .walas-file-dummy i {
    color: var(--primary);
    font-size: 28px;
}

.walas-container .walas-file-dummy span {
    font-size: 13px;
    font-weight: 600;
}

.walas-container .walas-file-dummy small {
    color: #9ca3af;
    font-size: 11px;
}

.walas-container .walas-preview {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 12px;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fafafa;
}

.walas-container .walas-preview-img {
    display: block;
    width: 84px;
    height: 84px;
    flex-shrink: 0;
    object-fit: cover;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
}

.walas-container .walas-preview-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 3px;
    min-width: 0;
}

.walas-container .walas-preview-info strong {
    max-width: 100%;
    color: #111827;
    font-size: 13px;
    overflow-wrap: anywhere;
}

.walas-container .walas-preview-info small {
    color: #6b7280;
    font-size: 11px;
}

.walas-container .walas-link-danger {
    margin-top: 4px;
    padding: 0;
    border: none;
    background: none;
    color: #b91c1c;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}

.walas-container .walas-link-danger:hover {
    text-decoration: underline;
}


/* ---------- SUBMIT ---------- */

.walas-container .walas-form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 6px;
}

.walas-container .btn-submit-laporan {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 13px 28px;
    border: none;
    border-radius: 12px;
    background: var(--primary);
    color: #fff;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(109, 20, 8, 0.25);
    transition: all 0.2s ease;
}

.walas-container .btn-submit-laporan:hover {
    background: #550f06;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(109, 20, 8, 0.35);
}

.walas-container .btn-submit-laporan:disabled {
    opacity: 0.7;
    cursor: wait;
    transform: none;
}


/* ---------- PILL FILTER (kelas & status) ---------- */

.walas-container .pill-btn {
    padding: 6px 13px;
    border: 1px solid #d1d5db;
    border-radius: 99px;
    background: #fff;
    color: #4b5563;
    font-family: inherit;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.15s ease;
}

.walas-container .pill-btn:hover {
    background: #f3f4f6;
    color: #111827;
}

.walas-container .pill-btn.active {
    border-color: var(--primary);
    background: var(--primary);
    color: #fff;
}

.walas-container .pill-btn:focus-visible,
.walas-container .dir-pick-btn:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}


/* ---------- DIREKTORI SISWA ---------- */

.walas-container .dir-controls {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    min-width: 0;
    flex: 1 1 260px;
    justify-content: flex-end;
}

.walas-container .dir-search {
    position: relative;
    min-width: 0;
    max-width: 100%;
    flex: 1 1 220px;
}

.walas-container .dir-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 13px;
    pointer-events: none;
}

.walas-container .dir-search-input,
.walas-container .dir-kelas-select {
    box-sizing: border-box;
    min-width: 0;
    max-width: 100%;
    height: 40px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    background: #fff;
    color: #374151;
    font-family: inherit;
    font-size: 13px;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.walas-container .dir-search-input {
    width: 100%;
    padding: 0 12px 0 34px;
}

.walas-container .dir-kelas-select {
    padding: 0 14px;
    font-weight: 600;
}

.walas-container .dir-search-input:focus,
.walas-container .dir-kelas-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(109, 20, 8, 0.12);
}

.walas-container .dir-pill-row,
.walas-container .hist-tabs {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}

.walas-container .dir-pill-row {
    padding: 0 28px;
}

.walas-container .hist-tabs {
    margin-bottom: 18px;
}

.walas-container .dir-pill-label {
    margin-right: 4px;
    color: #6b7280;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.walas-container .dir-table-area {
    min-width: 0;
    padding: 14px 28px 24px;
}

.walas-container .dir-result-info {
    margin-bottom: 8px;
    color: #6b7280;
    font-size: 12px;
}

.walas-container .dir-result-info strong {
    color: #111827;
}

.walas-container .dir-table-scroll {
    max-height: 480px;
    overflow: auto;
    -webkit-overflow-scrolling: touch;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.walas-container .dir-table {
    width: 100%;
    min-width: 560px;
    border-collapse: collapse;
    font-size: 13px;
    text-align: left;
}

.walas-container .dir-table thead {
    position: sticky;
    top: 0;
    z-index: 5;
    background: #f9fafb;
    box-shadow: 0 1px 0 #e5e7eb;
}

.walas-container .dir-th {
    padding: 12px 16px;
    color: #4b5563;
    font-weight: 700;
    white-space: nowrap;
}

.walas-container .dir-th-no { width: 60px; }
.walas-container .dir-th-right,
.walas-container .dir-td-right { text-align: right; }

.walas-container .dir-td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.walas-container .dir-siswa-row:last-child .dir-td {
    border-bottom: none;
}

.walas-container .dir-td-num  { color: #9ca3af; white-space: nowrap; }
.walas-container .dir-td-name { min-width: 140px; color: #111827; font-weight: 600; overflow-wrap: anywhere; }
.walas-container .dir-td-nisn { color: #6b7280; font-family: ui-monospace, monospace; white-space: nowrap; }

.walas-container .dir-td-empty,
.walas-container .dir-empty-msg {
    padding: 30px;
    color: #9ca3af;
    text-align: center;
}

.walas-container .dir-empty-msg i {
    display: block;
    margin-bottom: 8px;
    font-size: 28px;
}

.walas-container .dir-kelas-badge {
    display: inline-block;
    padding: 3px 8px;
    border: 1px solid rgba(109, 20, 8, 0.2);
    border-radius: 6px;
    background: rgba(109, 20, 8, 0.1);
    color: var(--primary);
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.walas-container .dir-pick-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
    padding: 7px 14px;
    border: none;
    border-radius: 8px;
    background: var(--primary);
    color: #fff;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: background 0.15s, transform 0.15s;
}

.walas-container .dir-pick-btn:hover {
    background: #550f06;
    transform: translateY(-1px);
}


/* ---------- RIWAYAT LAPORAN ---------- */

.walas-container .walas-reports-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.walas-container .report-item {
    min-width: 0;
    padding: 20px 22px;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #fff;
    transition: all 0.2s ease;
}

.walas-container .report-item[hidden] {
    display: none !important;
}

.walas-container .report-item:hover {
    border-color: #d1d5db;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
}

.walas-container .report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
}

.walas-container .report-student {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.walas-container .student-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    border-radius: 10px;
    background: #f3f4f6;
    color: #4b5563;
    font-size: 16px;
}

.walas-container .student-info {
    min-width: 0;
}

.walas-container .student-name {
    margin: 0;
    color: #111827;
    font-size: 15px;
    font-weight: 700;
    overflow-wrap: anywhere;
}

.walas-container .student-class {
    color: #6b7280;
    font-size: 12px;
}

.walas-container .badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border: 1px solid transparent;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.walas-container .badge-pending  { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.walas-container .badge-verified { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
.walas-container .badge-rejected { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

.walas-container .report-body {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 14px;
}

.walas-container .report-meta-tags {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.walas-container .meta-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
    color: #6b7280;
    font-size: 12px;
    overflow-wrap: anywhere;
}

.walas-container .meta-violation {
    padding: 3px 10px;
    border-radius: 6px;
    background: #FBEAE8;
    color: var(--primary);
    font-weight: 600;
}

.walas-container .report-description {
    margin: 0;
    padding: 10px 14px;
    border-left: 3px solid var(--primary);
    border-radius: 8px;
    background: #f9fafb;
    color: #374151;
    font-size: 13px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.walas-container .report-photo a {
    display: inline-block;
}

.walas-container .report-photo img {
    display: block;
    width: auto;
    max-width: min(140px, 100%);
    height: auto;
    max-height: 90px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.walas-container .photo-hint {
    display: block;
    margin-top: 4px;
    color: #6b7280;
    font-size: 10px;
}

.walas-container .report-feedback {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-size: 12px;
}

.walas-container .feedback-verified { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.walas-container .feedback-rejected { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
.walas-container .feedback-pending  { background: #fefce8; border-color: #fef08a; color: #854d0e; }

.walas-container .feedback-icon {
    margin-top: 2px;
    font-size: 16px;
}

.walas-container .feedback-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
    overflow-wrap: anywhere;
}

/* Sebelumnya teks putih di atas latar pink muda (tidak terbaca) */
.walas-container .points-badge {
    display: inline-block;
    width: fit-content;
    margin-top: 4px;
    padding: 2px 9px;
    border-radius: 999px;
    background: var(--c-point-bg);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
}

.walas-container .feedback-note {
    margin-top: 4px;
    font-size: 11px;
    font-style: italic;
    opacity: 0.9;
}

.walas-container .walas-pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}


/* ---------- EMPTY STATE ---------- */

.walas-container .walas-empty-state {
    padding: 40px 20px;
    text-align: center;
}

.walas-container .empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    margin: 0 auto 14px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #9ca3af;
    font-size: 24px;
}

.walas-container .walas-empty-state h3 {
    margin: 0 0 6px;
    color: #111827;
    font-size: 16px;
}

.walas-container .walas-empty-state p {
    max-width: 400px;
    margin: 0 auto;
    color: #6b7280;
    font-size: 13px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1024px) {
    .walas-container .walas-card { padding: 24px; }
    .walas-container .walas-card-flush { padding: 0; }
    .walas-container .walas-banner { padding: 26px; }
    .walas-container .walas-stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 900px) {
    .walas-container .walas-banner { flex-direction: column; align-items: flex-start; }

    .walas-container .walas-banner-actions {
        width: 100%;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .walas-container .btn-banner-primary,
    .walas-container .btn-banner-secondary { flex: 1; min-width: 140px; }

    .walas-container .walas-form-row,
    .walas-container .walas-detail-row,
    .walas-container .pelanggaran-mode { grid-template-columns: 1fr; }

    .walas-container .walas-card-header { flex-direction: column; align-items: flex-start; }

    .walas-container .walas-card-header-main,
    .walas-container .dir-controls { width: 100%; justify-content: flex-start; }
}

@media (max-width: 768px) {
    .walas-container { gap: 16px; }
    .walas-container .walas-card { padding: 20px 16px; border-radius: 16px; }
    .walas-container .walas-card-flush { padding: 0; }
    .walas-container .walas-card-flush .walas-card-header { padding: 18px 16px 14px; }
    .walas-container .walas-banner { padding: 22px 18px; border-radius: 16px; }
    .walas-container .walas-banner-actions { flex-direction: column; }

    .walas-container .btn-banner-primary,
    .walas-container .btn-banner-secondary { width: 100%; min-width: 0; }

    .walas-container .report-item { padding: 16px; }
    .walas-container .report-header { flex-direction: column; align-items: flex-start; gap: 12px; }
    .walas-container .report-feedback { padding: 12px; }

    .walas-container .walas-form-actions { justify-content: stretch; }
    .walas-container .btn-submit-laporan { width: 100%; }

    .walas-container .dir-table { min-width: 460px; }
    .walas-container .dir-table-area { padding: 12px 16px 18px; }
    .walas-container .dir-pill-row { padding: 0 16px; }
    .walas-container .dir-table-scroll { max-height: 60vh; }
    .walas-container .pill-btn { min-height: 36px; }
}

@media (max-width: 640px) {
    .walas-container .walas-stats-grid { grid-template-columns: minmax(0, 1fr); }
    .walas-container .walas-stat-card { padding: 16px; }
    .walas-container .stat-icon-wrapper { width: 42px; height: 42px; font-size: 18px; }

    .walas-container .dir-controls { flex-direction: column; align-items: stretch; }
    .walas-container .dir-search,
    .walas-container .dir-kelas-select { width: 100%; }
}

@media (max-width: 576px) {
    /* 16px mencegah iOS zoom otomatis saat input difokus */
    .walas-container .walas-input,
    .walas-container .walas-textarea,
    .walas-container .dir-search-input,
    .walas-container .dir-kelas-select { font-size: 16px; }

    .walas-container .mode-option { padding: 12px; }
    .walas-container .walas-card-icon { width: 40px; height: 40px; border-radius: 11px; font-size: 16px; }

    .walas-container .walas-preview { flex-direction: column; align-items: flex-start; }

    .walas-container .ts-wrapper .ts-dropdown {
        position: static !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .walas-container .walas-empty-state { padding: 30px 10px; }
}

@media (max-width: 480px) {
    .walas-container .walas-card { padding: 16px 12px; border-radius: 14px; }
    .walas-container .walas-card-flush { padding: 0; }
    .walas-container .walas-banner { padding: 20px 15px; }
    .walas-container .walas-banner-badge { padding: 5px 11px; font-size: 10px; }
    .walas-container .report-item { padding: 14px; border-radius: 12px; }
    .walas-container .student-avatar { width: 34px; height: 34px; flex-basis: 34px; font-size: 15px; }

    .walas-container .dir-table { min-width: 400px; }
    .walas-container .dir-th,
    .walas-container .dir-td { padding: 9px 10px; }
    .walas-container .dir-pick-btn { padding: 6px 10px; font-size: 11px; }
    .walas-container .walas-file-wrapper { padding: 18px 12px; }
}

@media (max-width: 400px) {
    .walas-container .walas-banner { padding: 18px 12px; }
    .walas-container .walas-card-header h2 { font-size: 16px; }
    .walas-container .btn-submit-laporan { padding: 12px 14px; font-size: 13px; }
    .walas-container .badge-status { padding: 5px 10px; font-size: 11px; }
    .walas-container .dir-table { min-width: 360px; }
}

@endsection


@section('scripts')

<script>
(function () {
    'use strict';

    var $ = function (id) { return document.getElementById(id); };

    var form          = $('laporForm');
    var modeAturan    = $('modeAturan');
    var modeManual    = $('modeManual');
    var aturanSelect  = $('aturan_pelanggaran_id');
    var kategoriInput = $('kategori_manual');
    var poinInput     = $('poin_manual');
    var namaManual    = $('jenis_pelanggaran_manual');
    var autoHint      = $('autoHint');
    var siswaSelect   = $('siswa_id');
    var filterKelas   = $('form_filter_kelas');


    /* =====================================================
       MODE PELANGGARAN (aturan / manual)
    ===================================================== */

    function syncAturan() {
        var opt = aturanSelect.options[aturanSelect.selectedIndex];

        if (!opt || !opt.value) {
            kategoriInput.value = '';
            poinInput.value = 0;
            return;
        }

        kategoriInput.value = opt.dataset.kategori || '';
        poinInput.value     = opt.dataset.poin || 0;
    }

    function updateMode() {
        var checked = form.querySelector('input[name="pelanggaran_mode"]:checked');
        if (!checked) { return; }

        var isAturan = checked.value === 'aturan';

        modeAturan.classList.toggle('is-hidden', !isAturan);
        modeManual.classList.toggle('is-hidden', isAturan);
        autoHint.classList.toggle('is-hidden', !isAturan);

        // Field yang disabled tidak ikut terkirim ke server.
        aturanSelect.disabled = !isAturan;
        aturanSelect.required = isAturan;

        namaManual.disabled = isAturan;
        namaManual.required = !isAturan;

        // Pada mode aturan, kategori & poin hanya tampilan otomatis.
        kategoriInput.disabled = isAturan;
        poinInput.disabled     = isAturan;
        kategoriInput.required = !isAturan;
        poinInput.required     = !isAturan;

        if (isAturan) { syncAturan(); }
    }

    form.querySelectorAll('input[name="pelanggaran_mode"]').forEach(function (r) {
        r.addEventListener('change', updateMode);
    });

    aturanSelect.addEventListener('change', syncAturan);


    /* =====================================================
       FOTO BUKTI: validasi, preview, hapus
    ===================================================== */

    var MAX_SIZE = 5 * 1024 * 1024; // 5 MB, sesuai teks di form
    var fotoInput   = $('foto_bukti');
    var fotoBox     = $('imagePreviewContainer');
    var fotoImg     = $('imagePreview');
    var fotoError   = $('fotoError');
    var fotoName    = $('fotoName');
    var fotoSize    = $('fotoSize');

    function resetFoto() {
        fotoInput.value = '';
        fotoImg.src = '';
        fotoBox.classList.add('is-hidden');
    }

    function showFotoError(msg) {
        fotoError.textContent = msg;
        fotoError.classList.toggle('is-hidden', !msg);
    }

    fotoInput.addEventListener('change', function () {
        showFotoError('');

        var file = fotoInput.files && fotoInput.files[0];
        if (!file) { resetFoto(); return; }

        if (!file.type.startsWith('image/')) {
            resetFoto();
            showFotoError('File harus berupa gambar (JPG, PNG, atau WEBP).');
            return;
        }

        if (file.size > MAX_SIZE) {
            resetFoto();
            showFotoError('Ukuran foto melebihi 5 MB. Pilih foto yang lebih kecil.');
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            fotoImg.src = e.target.result;
            fotoName.textContent = file.name;
            fotoSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            fotoBox.classList.remove('is-hidden');
        };
        reader.readAsDataURL(file);
    });

    $('removeFoto').addEventListener('click', function () {
        resetFoto();
        showFotoError('');
    });


    /* =====================================================
       MENCEGAH KIRIM GANDA
    ===================================================== */

    var btnSubmit = $('btnSubmit');
    var btnLabel  = btnSubmit.querySelector('span');
    var btnIcon   = btnSubmit.querySelector('i');
    var labelAwal = btnLabel.textContent;

    form.addEventListener('submit', function () {
        btnSubmit.disabled = true;
        btnLabel.textContent = 'Mengirim...';
        btnIcon.className = 'fa-solid fa-spinner fa-spin';
    });

    // Tombol dipulihkan jika pengguna kembali lewat tombol Back.
    window.addEventListener('pageshow', function () {
        btnSubmit.disabled = false;
        btnLabel.textContent = labelAwal;
        btnIcon.className = 'fa-solid fa-paper-plane';
    });


    /* =====================================================
       FILTER KELAS -> DROPDOWN SISWA

       Memakai daftar master + membangun ulang opsi, bukan
       option.style.display: Safari/iOS mengabaikan display:none
       pada <option>, dan Tom Select tidak ikut terpengaruh.
    ===================================================== */

    var placeholderText = siswaSelect.options[0] ? siswaSelect.options[0].textContent : '';

    var siswaMaster = Array.prototype.filter.call(siswaSelect.options, function (o) {
        return o.value !== '';
    }).map(function (o) {
        return {
            value: o.value,
            text: o.textContent.replace(/\s+/g, ' ').trim(),
            kelasRaw: o.dataset.kelas || '',
            kelas: (o.dataset.kelas || '').toLowerCase().trim(),
            nisn: o.dataset.nisn || ''
        };
    });

    function applySiswaFilter(kelas, selectedValue) {
        var key  = (kelas || '').toLowerCase().trim();
        var list = siswaMaster.filter(function (s) { return !key || s.kelas === key; });

        var wanted = selectedValue !== undefined ? String(selectedValue) : String(siswaSelect.value || '');
        var keep   = list.some(function (s) { return s.value === wanted; }) ? wanted : '';

        var ts = siswaSelect.tomselect; // ada jika Tom Select aktif

        if (ts) {
            ts.clear(true);
            ts.clearOptions();
            ts.addOptions(list.map(function (s) { return { value: s.value, text: s.text }; }));
            if (keep) { ts.setValue(keep, true); }
            ts.refreshOptions(false);
            return;
        }

        siswaSelect.innerHTML = '';
        siswaSelect.appendChild(new Option(placeholderText, ''));

        list.forEach(function (s) {
            var opt = new Option(s.text, s.value);
            opt.dataset.kelas = s.kelasRaw;
            opt.dataset.nisn  = s.nisn;
            siswaSelect.appendChild(opt);
        });

        siswaSelect.value = keep;
    }

    if (filterKelas) {
        filterKelas.addEventListener('change', function () {
            applySiswaFilter(filterKelas.value);
        });
    }


    /* =====================================================
       DIREKTORI SISWA: cari, filter kelas, pilih
    ===================================================== */

    var searchInput = $('searchSiswaInput');
    var dirKelas    = $('selectDirectoryKelas');
    var dirCount    = $('dirCount');
    var dirEmpty    = $('dirEmptyMessage');
    var pillRow     = $('dirPillRow');
    var dirRows     = document.querySelectorAll('.dir-siswa-row');

    function syncPills(kelasVal) {
        if (!pillRow) { return; }
        pillRow.querySelectorAll('.pill-btn').forEach(function (b) {
            b.classList.toggle('active', (b.dataset.kelas || '') === kelasVal);
        });
    }

    function filterDirectory() {
        var q = (searchInput.value || '').toLowerCase().trim();
        var k = dirKelas ? dirKelas.value.toLowerCase().trim() : '';
        var shown = 0;

        dirRows.forEach(function (row) {
            var okSearch = !q ||
                (row.dataset.nama || '').indexOf(q) !== -1 ||
                (row.dataset.nisn || '').toLowerCase().indexOf(q) !== -1;
            var okKelas = !k || (row.dataset.kelas || '').trim() === k;

            var show = okSearch && okKelas;
            row.hidden = !show;

            if (show) {
                shown++;
                var num = row.querySelector('.dir-td-num');
                if (num) { num.textContent = shown; } // nomor urut mengikuti hasil filter
            }
        });

        dirCount.textContent = shown;
        dirEmpty.classList.toggle('is-hidden', shown !== 0 || dirRows.length === 0);
        syncPills(k);
    }

    searchInput.addEventListener('input', filterDirectory);

    if (dirKelas) {
        dirKelas.addEventListener('change', filterDirectory);
    }

    if (pillRow) {
        pillRow.addEventListener('click', function (e) {
            var btn = e.target.closest('.pill-btn');
            if (!btn) { return; }
            if (dirKelas) { dirKelas.value = btn.dataset.kelas || ''; }
            filterDirectory();
            if (!dirKelas) { syncPills(btn.dataset.kelas || ''); }
        });
    }

    // Tombol "Pilih untuk Melapor" (delegasi event, data-* aman dari tanda kutip)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.js-pick');
        if (!btn) { return; }

        var kelas = btn.dataset.kelas || '';

        if (filterKelas) { filterKelas.value = kelas; }
        applySiswaFilter(filterKelas ? kelas : '', btn.dataset.id);

        var card = $('form-lapor');
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Highlight singkat supaya jelas bahwa form sudah terisi.
        card.style.transition = 'box-shadow 0.3s, border-color 0.3s';
        card.style.borderColor = getComputedStyle(document.documentElement)
            .getPropertyValue('--primary').trim();
        card.style.boxShadow = '0 0 0 4px rgba(109, 20, 8, 0.2)';

        setTimeout(function () {
            card.style.borderColor = '';
            card.style.boxShadow = '';
        }, 1800);
    });


    /* =====================================================
       RIWAYAT: filter status
    ===================================================== */

    var histTabs   = $('histTabs');
    var histItems  = document.querySelectorAll('.report-item');
    var histNoRes  = $('histNoResult');

    if (histTabs) {
        histTabs.addEventListener('click', function (e) {
            var btn = e.target.closest('.pill-btn');
            if (!btn) { return; }

            histTabs.querySelectorAll('.pill-btn').forEach(function (b) {
                b.classList.toggle('active', b === btn);
            });

            var status = btn.dataset.status;
            var shown = 0;

            histItems.forEach(function (item) {
                var show = status === 'all' || item.dataset.status === status;
                item.hidden = !show;
                if (show) { shown++; }
            });

            histNoRes.classList.toggle('is-hidden', shown !== 0);
        });
    }


    /* =====================================================
       INISIALISASI
    ===================================================== */

    document.addEventListener('DOMContentLoaded', function () {

        updateMode();

        // Jika siswa sudah terpilih (misalnya dari halaman daftar siswa),
        // saring dropdown ke kelas siswa tersebut.
        if (filterKelas && siswaSelect.value) {
            var picked = siswaMaster.filter(function (s) {
                return s.value === String(siswaSelect.value);
            })[0];

            if (picked && picked.kelasRaw) {
                filterKelas.value = picked.kelasRaw;
                applySiswaFilter(picked.kelasRaw, picked.value);
            }
        }
    });
})();
</script>

@endsection
