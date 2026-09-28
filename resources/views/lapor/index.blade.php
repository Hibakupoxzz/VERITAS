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

        <div class="walas-stat-card stat-total">
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div class="stat-data">
                <span class="stat-number">{{ $stats['total'] ?? 0 }}</span>
                <span class="stat-label">Total Laporan Dikirim</span>
            </div>
        </div>

        <div class="walas-stat-card stat-pending">
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="stat-data">
                <span class="stat-number">{{ $stats['pending'] ?? 0 }}</span>
                <span class="stat-label">Menunggu Verifikasi PDS</span>
            </div>
        </div>

        <div class="walas-stat-card stat-verified">
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-data">
                <span class="stat-number">{{ $stats['verified'] ?? 0 }}</span>
                <span class="stat-label">Diverifikasi & Diproses</span>
            </div>
        </div>

        <div class="walas-stat-card stat-rejected">
            <div class="stat-icon-wrapper">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="stat-data">
                <span class="stat-number">{{ $stats['rejected'] ?? 0 }}</span>
                <span class="stat-label">Laporan Ditolak</span>
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
                <h2>Formulir Lapor Pelanggaran</h2>
                <p>Isi rincian temuan pelanggaran siswa di bawah ini secara objektif dan lengkap.</p>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="walas-alert walas-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <strong>Berhasil Terkirim!</strong>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="walas-alert walas-alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    <strong>Pemberitahuan:</strong>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="walas-alert walas-alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    <strong>Mohon periksa kembali form berikut:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('lapor.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="walas-form">

            @csrf

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

                {{-- Siswa --}}
                <div class="walas-form-group">
                    <label class="walas-label" for="siswa_id">
                        Pilih Siswa Pelanggar <span class="required">*</span>
                    </label>
                    <select name="siswa_id"
                            id="siswa_id"
                            class="walas-input walas-select searchable-select"
                            required>
                        <option value="">-- Cari atau Pilih Siswa --</option>
                        @foreach($siswas as $siswa)
                            <option value="{{ $siswa->id }}"
                                data-kelas="{{ $siswa->kelas }}"
                                data-nama="{{ strtolower($siswa->nama) }}"
                                {{ (old('siswa_id', $selectedSiswaId ?? null) == $siswa->id) ? 'selected' : '' }}>
                                {{ $siswa->nama }} — {{ $siswa->kelas }} (NISN: {{ $siswa->nisn ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <span class="walas-help">Pilih siswa yang terbukti atau dilaporkan melakukan pelanggaran.</span>
                </div>

                {{-- Tanggal Kejadian --}}
                <div class="walas-form-group">
                    <label class="walas-label" for="tanggal">
                        Tanggal Kejadian <span class="required">*</span>
                    </label>
                    <input type="date"
                           name="tanggal"
                           id="tanggal"
                           class="walas-input"
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           required>
                    <span class="walas-help">Tanggal saat pelanggaran terjadi atau ditemukan.</span>
                </div>

            </div>

            {{-- Jenis / Indikasi Pelanggaran --}}
            <div class="walas-form-group">
                <label class="walas-label" for="jenis_pelanggaran">
                    Nama / Jenis Pelanggaran <span class="required">*</span>
                </label>
                <input type="text"
                       name="jenis_pelanggaran"
                       id="jenis_pelanggaran"
                       class="walas-input"
                       placeholder="Contoh: Merokok di area toilet lantai 2, Terlambat lebih dari 30 menit, dll."
                       value="{{ old('jenis_pelanggaran') }}"
                       required>
                <span class="walas-help">Tuliskan ringkasan pelanggaran yang dilakukan siswa.</span>
            </div>

            {{-- Kronologi / Deskripsi --}}
            <div class="walas-form-group">
                <label class="walas-label" for="keterangan">
                    Kronologi & Deskripsi Kejadian <span class="optional">(Opsional)</span>
                </label>
                <textarea name="keterangan"
                          id="keterangan"
                          class="walas-textarea"
                          rows="4"
                          placeholder="Jelaskan secara rinci kronologi temuan, saksi yang melihat, atau keterangan lain yang memperjelas laporan...">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Foto Bukti --}}
            <div class="walas-form-group">
                <label class="walas-label" for="foto_bukti">
                    Unggah Foto Bukti <span class="optional">(Opsional)</span>
                </label>
                <div class="walas-file-wrapper">
                    <input type="file"
                           name="foto_bukti"
                           id="foto_bukti"
                           class="walas-file-input"
                           accept="image/*"
                           onchange="previewImage(event)">
                    <div class="walas-file-dummy">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Klik untuk memilih file foto bukti kejadian</span>
                        <small>Format yang didukung: JPG, PNG, WEBP (Maksimal 5 MB)</small>
                    </div>
                </div>
                <div id="imagePreviewContainer" style="display:none; margin-top:12px;">
                    <img id="imagePreview" src="" alt="Preview Bukti" style="max-width:200px; max-height:160px; border-radius:10px; border:1px solid #e5e7eb;">
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="walas-form-actions">
                <button type="submit" class="btn-submit-laporan">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Laporan Pelanggaran</span>
                </button>
            </div>

        </form>

    </div>


    {{-- =========================================================
         DIREKTORI DATA KELAS & SISWA UNTUK PELAPORAN
    ========================================================= --}}
    <div class="walas-card" id="direktori-siswa">
        <div class="walas-card-header" style="justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div class="walas-card-icon" style="background:#fef2f2; color:#b91c1c;">
                    <i class="fa-solid fa-users-viewfinder"></i>
                </div>
                <div>
                    <h2>Direktori Data Kelas &amp; Siswa</h2>
                    <p>Cari siswa dari kelas manapun untuk langsung mengisi formulir pelaporan pelanggaran.</p>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div style="position:relative; min-width:220px;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:13px;"></i>
                    <input type="text"
                           id="searchSiswaInput"
                           onkeyup="filterDirectoryTable()"
                           placeholder="Cari nama atau NISN..."
                           style="width:100%; padding:8px 12px 8px 34px; border:1px solid #d1d5db; border-radius:10px; font-size:13px; outline:none;">
                </div>

                @if(isset($kelasList) && count($kelasList) > 1)
                <select id="selectDirectoryKelas"
                        onchange="filterDirectoryTable()"
                        style="padding:8px 14px; border:1px solid #d1d5db; border-radius:10px; font-size:13px; outline:none; background:#fff; font-weight:600; color:#374151;">
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
        <div style="padding: 12px 24px 0; display:flex; flex-wrap:wrap; gap:6px; align-items:center;">
            <span style="font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; margin-right:4px;">Filter Cepat Kelas:</span>
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
        <div style="padding:18px 24px 24px;">
            <div style="max-height: 480px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 12px;">
                <table style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
                    <thead style="background:#f9fafb; position:sticky; top:0; z-index:5; border-bottom:1px solid #e5e7eb;">
                        <tr>
                            <th style="padding:12px 16px; color:#4b5563; font-weight:700; width:60px;">No</th>
                            <th style="padding:12px 16px; color:#4b5563; font-weight:700;">Nama Siswa</th>
                            <th style="padding:12px 16px; color:#4b5563; font-weight:700;">NISN</th>
                            <th style="padding:12px 16px; color:#4b5563; font-weight:700;">Kelas</th>
                            <th style="padding:12px 16px; color:#4b5563; font-weight:700; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="directoryTableBody">
                        @forelse($siswas as $siswa)
                            <tr class="dir-siswa-row"
                                data-nama="{{ strtolower($siswa->nama) }}"
                                data-nisn="{{ $siswa->nisn }}"
                                data-kelas="{{ strtolower($siswa->kelas) }}"
                                style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:12px 16px; color:#9ca3af;">{{ $loop->iteration }}</td>
                                <td style="padding:12px 16px; font-weight:600; color:#111827;">
                                    {{ $siswa->nama }}
                                </td>
                                <td style="padding:12px 16px; color:#6b7280; font-family:monospace;">
                                    {{ $siswa->nisn ?? '-' }}
                                </td>
                                <td style="padding:12px 16px;">
                                    <span style="background:rgba(109,20,8,0.1); color:#6D1408; font-weight:700; font-size:11px; padding:3px 8px; border-radius:6px; border:1px solid rgba(109,20,8,0.2);">
                                        {{ $siswa->kelas }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px; text-align:right;">
                                    <button type="button"
                                            onclick="selectStudentForReport({{ $siswa->id }}, '{{ addslashes($siswa->nama) }}', '{{ $siswa->kelas }}')"
                                            style="background:#6D1408; color:#fff; border:none; padding:7px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                                        <i class="fa-solid fa-bullhorn"></i>
                                        Pilih untuk Melapor
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:30px; color:#9ca3af;">
                                    Belum ada data siswa ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="dirEmptyMessage" style="display:none; text-align:center; padding:30px; color:#9ca3af;">
                <i class="fa-solid fa-user-slash" style="font-size:28px; margin-bottom:8px; display:block;"></i>
                Tidak ada siswa yang sesuai dengan filter pencarian.
            </div>
        </div>
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
    background: linear-gradient(135deg, #6D1408 0%, #901C0D 50%, #4A0D05 100%);
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
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.walas-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
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
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.04);
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
    transition: border-color 0.15s, box-shadow 0.15s;
    font-family: inherit;
    box-sizing: border-box;
}

.walas-input:focus,
.walas-textarea:focus {
    outline: none;
    border-color: #6D1408;
    box-shadow: 0 0 0 3px rgba(109, 20, 8, 0.12);
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

/* Custom file upload */
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

/* Submit Button */
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
    box-shadow: 0 4px 14px rgba(109, 20, 8, 0.25);
    transition: all 0.2s ease;
}

.btn-submit-laporan:hover {
    background: #550f06;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(109, 20, 8, 0.35);
}

/* =========================================================
   TABS FILTER
========================================================= */
.walas-filter-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f3f4f6;
    padding: 4px;
    border-radius: 12px;
}

.tab-btn {
    border: none;
    background: transparent;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.15s ease;
}

.tab-btn.active {
    background: #ffffff;
    color: #111827;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

/* =========================================================
   REPORTS LIST & ITEMS
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
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
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

/* Badges */
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

/* Body */
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

/* Feedback / Response Footer */
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

/* Empty State */
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

/* Responsive */
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
        flex-wrap: wrap;
    }
    .btn-banner-primary, .btn-banner-secondary {
        flex: 1;
        min-width: 140px;
    }
    .walas-form-row {
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
    .walas-filter-tabs {
        width: 100%;
        overflow-x: auto;
    }
}
@endsection


@section('scripts')
<script>
function previewImage(event) {
    const input = event.target;
    const container = document.getElementById('imagePreviewContainer');
    const preview = document.getElementById('imagePreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        container.style.display = 'none';
    }
}

// Filter siswa dropdown inside the form by class
function filterSiswaDropdown(kelas) {
    const select = document.getElementById('siswa_id');
    if (!select) return;
    const options = select.querySelectorAll('option');

    options.forEach(opt => {
        if (!opt.value) return; // Skip placeholder
        const optKelas = opt.getAttribute('data-kelas');
        if (!kelas || optKelas === kelas) {
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

// Quick select student from directory table and pre-fill form
function selectStudentForReport(id, nama, kelas) {
    const select = document.getElementById('siswa_id');
    const filterKelas = document.getElementById('form_filter_kelas');

    if (filterKelas) {
        filterKelas.value = kelas;
        filterSiswaDropdown(kelas);
    }

    if (select) {
        select.value = id;
    }

    const formCard = document.getElementById('form-lapor');
    if (formCard) {
        formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        formCard.style.transition = 'box-shadow 0.3s, border-color 0.3s';
        formCard.style.borderColor = '#6D1408';
        formCard.style.boxShadow = '0 0 0 4px rgba(109, 20, 8, 0.2)';
        setTimeout(() => {
            formCard.style.borderColor = '';
            formCard.style.boxShadow = '';
        }, 1800);
    }
}

// Filter the student directory table
function filterDirectoryTable() {
    const searchVal = (document.getElementById('searchSiswaInput')?.value || '').toLowerCase().trim();
    const kelasVal = (document.getElementById('selectDirectoryKelas')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.dir-siswa-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const nama = row.getAttribute('data-nama') || '';
        const nisn = row.getAttribute('data-nisn') || '';
        const kelas = row.getAttribute('data-kelas') || '';

        const matchSearch = !searchVal || nama.includes(searchVal) || nisn.includes(searchVal);
        const matchKelas = !kelasVal || kelas === kelasVal;

        if (matchSearch && matchKelas) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const emptyMsg = document.getElementById('dirEmptyMessage');
    if (emptyMsg) {
        emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

function setQuickClassFilter(kelas, btn) {
    document.querySelectorAll('.class-pill-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const select = document.getElementById('selectDirectoryKelas');
    if (select) {
        select.value = kelas;
    }
    filterDirectoryTable();
}

// Auto filter on page load if student pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('siswa_id');
    const filterKelas = document.getElementById('form_filter_kelas');
    if (select && select.value && filterKelas) {
        const selectedOpt = select.querySelector('option[value="' + select.value + '"]');
        if (selectedOpt) {
            const k = selectedOpt.getAttribute('data-kelas');
            if (k) {
                filterKelas.value = k;
                filterSiswaDropdown(k);
            }
        }
    }
});
</script>
@endsection
