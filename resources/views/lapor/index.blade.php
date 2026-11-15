@extends('layouts.app')

@section('title', 'Portal Lapor Pelanggaran - ' . (auth()->user()->role_label ?? 'Guru'))
@section('page_title', 'Portal Lapor Pelanggaran')

@section('content')

@php
    $user     = auth()->user();
    $roleIcon = $user->isBk()
        ? 'fa-user-nurse'
        : ($user->isPimpinan()
            ? 'fa-user-shield'
            : ($user->isGuru()
                ? 'fa-graduation-cap'
                : ($user->isWalas() ? 'fa-user-tie' : 'fa-shield-halved')));

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
                @if($user->isPimpinan())
                    Sebagai unsur pimpinan sekolah, Anda dapat memantau seluruh kelas dan melaporkan temuan pelanggaran siswa. Laporan Anda akan masuk ke antrean verifikasi untuk ditinjau oleh tim <strong>Guru PDS</strong>.
                @elseif($user->isGuru())
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

         Markup & perilaku form sengaja dibuat sama dengan
         /pelanggaran/create (Tambah Pelanggaran) supaya konsisten:
         kartu pv-card, combobox cari siswa, cari jenis pelanggaran,
         poin otomatis dari master, dan dua tombol foto.

         Catatan: flash session('success') / 'error' dan ringkasan
         $errors sudah otomatis ditampilkan oleh layouts/app.blade.php,
         jadi tidak diulang di sini (sebelumnya tampil dua kali).
    ========================================================= --}}
    <div class="pv-body" id="form-lapor">

        <div class="pv-inner">

            <div class="pv-page-header">

                <div class="pv-page-icon">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>

                <div>
                    <h1 class="pv-page-title">Formulir Lapor Pelanggaran</h1>
                    <p class="pv-page-sub">Isi rincian temuan pelanggaran siswa secara objektif dan lengkap.</p>
                </div>

            </div>

            <form
                action="{{ route('lapor.store') }}"
                method="POST"
                enctype="multipart/form-data"
                id="laporForm"
            >
                @csrf

                {{-- =====================================================
                     PILIH SISWA
                ====================================================== --}}
                <div class="pv-card pv-card-search">

                    <div class="pv-section-label">Pilih Siswa</div>

                    <input type="hidden"
                           name="siswa_id"
                           id="siswa_id"
                           value="{{ old('siswa_id', $selectedSiswaId ?? '') }}">

                    <div class="pv-search-box" id="searchBox">

                        <div class="pv-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>

                            <input
                                type="text"
                                id="searchSiswa"
                                class="pv-input"
                                placeholder="Cari nama atau NISN siswa"
                                autocomplete="off"
                                aria-controls="hasilSiswa">
                        </div>

                        <div id="hasilSiswa" class="is-hidden" role="listbox"></div>

                        <span class="pv-hint">
                            Ketik minimal 2 huruf. Kosongkan jika laporan tidak
                            ditujukan ke siswa tertentu.
                        </span>

                    </div>

                    <div id="selectedSiswa" class="pv-selected is-hidden">
                        <div class="pv-selected-avatar" id="selectedAvatar"></div>
                        <div class="pv-selected-text">
                            <strong id="selectedNama"></strong>
                            <small id="selectedMeta"></small>
                        </div>
                        <button type="button" class="pv-selected-change" id="gantiSiswa">Ganti</button>
                    </div>

                </div>


                {{-- =====================================================
                     DETAIL PELANGGARAN
                ====================================================== --}}
                <div class="pv-card">

                    <div class="pv-section-label">Detail Pelanggaran</div>

                    <div class="pv-grid-2">

                        <div class="pv-field">
                            <label class="pv-label" for="tanggal">
                                Tanggal Kejadian <span class="pv-req">*</span>
                            </label>
                            <input
                                type="date"
                                name="tanggal"
                                id="tanggal"
                                class="pv-input"
                                value="{{ old('tanggal', $today) }}"
                                max="{{ $today }}"
                                required>
                        </div>

                        <div class="pv-field">
                            <label class="pv-label" for="kategoriPelanggaran">Kategori</label>
                            <select id="kategoriPelanggaran" class="pv-select">
                                <option value="">-- Semua Kategori --</option>
                                <option value="Ringan">Ringan</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Berat">Berat</option>
                                <option value="Luar Biasa">Luar Biasa</option>
                            </select>
                            <span class="pv-hint">Menyaring daftar jenis pelanggaran di bawah.</span>
                        </div>

                    </div>


                    <div class="pv-field">

                        <label class="pv-label" for="aturan_pelanggaran_id">
                            Aturan Pelanggaran <span class="pv-req">*</span>
                        </label>

                        <input
                            type="search"
                            id="cariAturan"
                            class="pv-input"
                            placeholder="Cari jenis pelanggaran (kode atau nama)..."
                            autocomplete="off">

                        <select name="aturan_pelanggaran_id" id="aturan_pelanggaran_id" class="pv-select" required>

                            <option value="">-- Pilih jenis pelanggaran --</option>

                            @foreach($aturanPelanggarans as $aturan)
                                <option
                                    value="{{ $aturan->id }}"
                                    data-kategori="{{ $aturan->kategori }}"
                                    data-poin="{{ $aturan->poin }}"
                                    {{ (string) old('aturan_pelanggaran_id') === (string) $aturan->id ? 'selected' : '' }}
                                >{{ $aturan->kode }} — {{ $aturan->nama }} ({{ $aturan->poin }} poin)</option>
                            @endforeach

                        </select>

                        <span class="pv-hint" id="cariAturanInfo"></span>

                    </div>


                    <div class="pv-field">

                        <label class="pv-label" for="poinOtomatis">Poin</label>

                        {{-- Poin selalu mengikuti master aturan, jadi field ini
                             hanya untuk ditampilkan dan tidak pernah dikirim. --}}
                        <input
                            type="number"
                            id="poinOtomatis"
                            class="pv-input"
                            min="0"
                            value=""
                            placeholder="Poin otomatis"
                            readonly>

                        <small id="infoPoin" class="pv-hint">
                            Pilih aturan pelanggaran untuk mendapatkan poin.
                        </small>

                        <div id="poinBadge" class="poin-badge"></div>

                    </div>


                    <div class="pv-field">

                        <label class="pv-label" for="keterangan">
                            Kronologi &amp; Deskripsi Kejadian
                        </label>

                        <textarea
                            name="keterangan"
                            id="keterangan"
                            class="pv-textarea"
                            rows="4"
                            placeholder="Jelaskan secara rinci kronologi temuan, saksi yang melihat, atau keterangan lain yang memperjelas laporan..."
                        >{{ old('keterangan') }}</textarea>

                    </div>

                </div>


                {{-- =====================================================
                     FOTO BUKTI
                ====================================================== --}}
                <div class="pv-card">

                    <div class="pv-section-label">Foto Bukti</div>

                    {{-- Dua tombol terpisah: satu memaksa kamera (capture),
                         satu membuka galeri (tanpa capture). Atribut capture
                         dipasang/dilepas sebelum input diklik, jadi tetap satu
                         input agar name="foto_bukti" hanya terkirim satu kali. --}}
                    <div class="upload-box">

                        <input
                            type="file"
                            id="foto_bukti"
                            name="foto_bukti"
                            accept="image/jpeg,image/png,image/webp"
                            hidden>

                        <div class="upload-icon">
                            <i class="fa-solid fa-camera"></i>
                        </div>

                        <div class="upload-title">Upload Foto Bukti</div>

                        <div class="upload-desc">
                            Ambil foto langsung dengan kamera, atau pilih foto yang
                            sudah ada di galeri.
                            <br>
                            Format JPG, PNG, atau WEBP. Maksimal 5 MB.
                        </div>

                        <div class="upload-actions">

                            <button type="button" class="upload-btn" id="btnAmbilFoto">
                                <i class="fa-solid fa-camera-retro"></i>
                                Ambil Foto (Kamera)
                            </button>

                            <button type="button" class="upload-btn upload-btn-secondary" id="btnPilihGaleri">
                                <i class="fa-solid fa-image"></i>
                                Pilih dari Galeri
                            </button>

                        </div>

                    </div>

                    <div class="upload-error is-hidden" id="uploadError" role="alert"></div>

                    <div class="upload-result is-hidden" id="uploadResult">
                        <img id="previewFoto" alt="Preview foto">
                        <div class="file-info">
                            <strong id="fileName"></strong>
                            <small id="fileSize"></small>
                            <button type="button" class="upload-remove" id="hapusFoto">
                                <i class="fa-solid fa-trash"></i> Hapus foto
                            </button>
                        </div>
                    </div>

                </div>


                {{-- TOMBOL --}}
                <div class="pv-footer">

                    <a href="{{ route('lapor.riwayat') }}" class="pv-btn-secondary">Riwayat Laporan</a>

                    <button type="submit" class="pv-btn-primary" id="btnSimpan">
                        Kirim Laporan ke BK / PDS
                    </button>

                </div>

            </form>

        </div>

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
                                            data-nama="{{ $siswa->nama }}"
                                            data-nisn="{{ $siswa->nisn }}"
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
   FORMULIR LAPOR (prefix pv-)
   Styling form ini sengaja sama dengan
   /pelanggaran/create supaya keduanya terasa satu produk.
   Primitive pv-* berasal dari partials/ui; yang di sini
   hanya penyesuaian yang memang spesifik halaman.
========================================================= */

.walas-container .pv-body {
    width: 100%;
}

.walas-container .pv-inner {
    width: 100%;
    margin: 0 auto;
}

/* .pv-card di partial tidak punya padding */
.walas-container .pv-card {
    padding: 24px 28px;
}

.walas-container .pv-req {
    color: #dc2626;
}

.walas-container .pv-hint {
    display: block;
    margin-top: 6px;
    color: #6b7280;
    font-size: .75rem;
    line-height: 1.5;
}

/* Dua kolom (tanggal + kategori) */
.walas-container .pv-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0 18px;
}

.walas-container .pv-page-header,
.walas-container .pv-page-header > div {
    min-width: 0;
}

.walas-container .pv-page-header h1,
.walas-container .pv-page-header p {
    overflow-wrap: anywhere;
}

/* Ikon header pakai Font Awesome, bukan inline SVG seperti
   di /pelanggaran/create, jadi ukuran hurufnya perlu diatur. */
.walas-container .pv-page-icon {
    font-size: 20px;
}

/* Hanya input readonly yang tampil "terkunci", supaya form
   tidak terlihat seperti nonaktif. */
.walas-container .pv-input[readonly] {
    background: #f3f4f6;
    color: #374151;
    cursor: not-allowed;
}

.walas-container .pv-input[readonly]:focus {
    border-color: var(--c-border);
    box-shadow: none;
}


/* ── Pencarian siswa ── */
.walas-container .pv-search-box {
    position: relative;
}

.walas-container .pv-search {
    position: relative;
    width: 100%;
}

.walas-container .pv-search input {
    padding-left: 42px;
}

.walas-container .pv-search svg {
    position: absolute;
    left: 14px;
    top: 50%;
    width: 18px;
    height: 18px;
    transform: translateY(-50%);
    stroke: #9ca3af;
    pointer-events: none;
}

/* Daftar hasil tampil inline di dalam kartu, bukan popup melayang. */
.walas-container #hasilSiswa {
    margin-top: 8px;
    max-height: 340px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
}

.walas-container .pv-siswa-item {
    display: block;
    width: 100%;
    padding: 12px 14px;
    border: none;
    border-bottom: 1px solid #f3f4f6;
    background: #fff;
    color: #111827;
    font-family: inherit;
    font-size: .875rem;
    text-align: left;
    cursor: pointer;
    overflow-wrap: anywhere;
}

.walas-container .pv-siswa-item:last-child {
    border-bottom: none;
}

.walas-container .pv-siswa-item:hover,
.walas-container .pv-siswa-item:focus-visible {
    background: #f8fafc;
    outline: none;
}

.walas-container .pv-siswa-item strong {
    display: block;
}

.walas-container .pv-siswa-item small {
    color: #6b7280;
}

.walas-container .pv-siswa-status {
    padding: 14px;
    color: #9ca3af;
    font-size: .8rem;
    text-align: center;
}

.walas-container .pv-selected {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    background: #eff6ff;
    color: #1e40af;
}

.walas-container .pv-selected-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    border-radius: 50%;
    background: #dbeafe;
    font-weight: 700;
}

.walas-container .pv-selected-text {
    flex: 1 1 auto;
    min-width: 0;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

.walas-container .pv-selected-text strong {
    display: block;
    font-size: .9rem;
}

.walas-container .pv-selected-text small {
    font-size: .75rem;
    opacity: .85;
}

.walas-container .pv-selected-change {
    flex: 0 0 auto;
    padding: 7px 12px;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    background: #fff;
    color: #1d4ed8;
    font-family: inherit;
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
}

.walas-container .pv-selected-change:hover {
    background: #dbeafe;
}


/* ── Jenis Pelanggaran: input cari + select ── */
.walas-container #cariAturan,
.walas-container #aturan_pelanggaran_id {
    display: block;
    width: 100%;
    min-height: 42px;            /* samakan tinggi keduanya */
}

.walas-container #cariAturan {
    margin-bottom: 8px;          /* jarak ke dropdown */
    -webkit-appearance: none;    /* hilangkan gaya bawaan type="search" */
    appearance: none;
}

.walas-container #cariAturan::-webkit-search-cancel-button {
    cursor: pointer;
}


/* ── Poin ── */
.walas-container .poin-badge {
    display: none;
    margin-top: 8px;
    padding: 9px 12px;
    border-radius: 9px;
    background: #fef3c7;
    color: #92400e;
    font-size: .78rem;
    line-height: 1.5;
}

.walas-container .poin-badge.active {
    display: block;
}


/* ── Upload foto ── */
.walas-container .pv-card.pv-card-search {
    position: relative;
    z-index: 5;
    overflow: visible;
}

.walas-container .upload-box:hover,
.walas-container .upload-box:focus-within {
    border-color: var(--primary);
    background: #FFF8F6;
}

.walas-container .upload-title {
    margin-bottom: 8px;
    color: #1F2937;
    font-size: 20px;
    font-weight: 700;
}

.walas-container .upload-desc {
    max-width: 450px;
    margin-bottom: 22px;
    color: #6B7280;
    font-size: .9rem;
    line-height: 1.7;
}

.walas-container .upload-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px;
}

.walas-container .upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 13px 26px;
    border: none;
    border-radius: 14px;
    background: var(--primary);
    color: #fff;
    font-family: inherit;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform .25s ease, box-shadow .25s ease;
}

.walas-container .upload-box:hover .upload-btn {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(109, 20, 8, .25);
}

/* Tombol galeri dibuat netral supaya pilihan kedua jelas berbeda
   dari tombol kamera yang memakai warna utama. */
.walas-container .upload-btn-secondary {
    border: 2px solid var(--primary);
    background: #fff;
    color: var(--primary);
}

.walas-container .upload-box:hover .upload-btn-secondary {
    box-shadow: 0 12px 25px rgba(109, 20, 8, .12);
}

.walas-container .upload-error {
    margin-top: 10px;
    color: #b91c1c;
    font-size: .8rem;
    font-weight: 600;
}

.walas-container .upload-result {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-top: 16px;
    padding: 14px;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    background: #fafafa;
}

.walas-container #previewFoto {
    display: block;
    width: 140px;
    height: 140px;
    flex: 0 0 140px;
    object-fit: cover;
    border: 3px solid #fff;
    border-radius: 14px;
    box-shadow: 0 8px 22px rgba(0, 0, 0, .15);
}

.walas-container .file-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
    min-width: 0;
}

.walas-container .file-info strong {
    color: #111827;
    font-size: .9rem;
    overflow-wrap: anywhere;
}

.walas-container .file-info small {
    color: #6B7280;
}

.walas-container .upload-remove {
    margin-top: 6px;
    padding: 0;
    border: none;
    background: none;
    color: #b91c1c;
    font-family: inherit;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
}

.walas-container .upload-remove:hover {
    text-decoration: underline;
}


/* ── Footer ── */
.walas-container .pv-inner .pv-footer {
    align-items: center;
}


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

    .walas-container .pv-card { padding: 18px; border-radius: 16px; }
    .walas-container .pv-grid-2 { grid-template-columns: 1fr; }
    .walas-container .pv-inner .pv-footer { flex-direction: column-reverse; }
    .walas-container .pv-btn-primary,
    .walas-container .pv-btn-secondary { width: 100%; min-height: 46px; }

    .walas-container .upload-box { padding: 28px 18px; }
    .walas-container .upload-title { font-size: 18px; }
    .walas-container .upload-desc { font-size: .85rem; }

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
    .walas-container .pv-input,
    .walas-container .pv-select,
    .walas-container .pv-textarea,
    .walas-container .dir-search-input,
    .walas-container .dir-kelas-select { font-size: 16px; }

    .walas-container .pv-search svg { left: 12px; width: 16px; height: 16px; }
    .walas-container .pv-search input { padding-left: 38px; }

    .walas-container .walas-card-icon { width: 40px; height: 40px; border-radius: 11px; font-size: 16px; }

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

    .walas-container .pv-page-header { margin-bottom: 1rem; gap: 10px; }
    .walas-container .pv-page-icon { width: 40px; height: 40px; flex-basis: 40px; font-size: 17px; }

    .walas-container .upload-icon { width: 62px; height: 62px; font-size: 26px; }
    .walas-container .upload-box { padding: 22px 14px; }
    .walas-container .upload-actions { flex-direction: column; }
    .walas-container .upload-btn { width: 100%; justify-content: center; }

    .walas-container .upload-result { flex-direction: column; align-items: center; text-align: center; }
    .walas-container .file-info { align-items: center; }
    .walas-container #previewFoto { width: 100%; max-width: 260px; height: auto; max-height: 220px; flex-basis: auto; }

    .walas-container .pv-selected { flex-wrap: wrap; }
    .walas-container .pv-selected-change { width: 100%; }
}

@media (max-width: 400px) {
    .walas-container .walas-banner { padding: 18px 12px; }
    .walas-container .walas-card-header h2 { font-size: 16px; }
    .walas-container .pv-card { padding: 14px 12px; }
    .walas-container .pv-page-title { font-size: 1rem; }
    .walas-container .pv-page-sub { font-size: .68rem; }
    .walas-container .upload-title { font-size: 16px; }
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
    var kategoriSelect = $('kategoriPelanggaran');
    var cariAturan     = $('cariAturan');
    var aturanSelect   = $('aturan_pelanggaran_id');
    var poinInput      = $('poinOtomatis');
    var infoPoin       = $('infoPoin');
    var poinBadge      = $('poinBadge');
    var infoCari       = $('cariAturanInfo');


    /* =====================================================
       KATEGORI + JENIS PELANGGARAN + POIN

       Menyalin perilaku /pelanggaran/create: daftar aturan
       disimpan sekali lalu opsi dibangun ulang saat kategori
       atau kata kunci berubah (andalan di Safari/iOS).
    ===================================================== */

    var placeholderText = aturanSelect.options[0].textContent.trim();

    var semuaAturan = [];

    Array.prototype.forEach.call(aturanSelect.options, function (o) {
        if (o.value === '') { return; }

        semuaAturan.push({
            value: o.value,
            text: o.textContent.replace(/\s+/g, ' ').trim(),
            kategori: o.dataset.kategori || '',
            poin: o.dataset.poin || '0'
        });
    });

    function renderAturan(kategori) {
        var pilihan = aturanSelect.value;
        var keyword = (cariAturan.value || '').toLowerCase().trim();

        aturanSelect.innerHTML = '';
        aturanSelect.appendChild(new Option(placeholderText, ''));

        var jumlahCocok = 0;

        semuaAturan.forEach(function (r) {
            if (kategori && r.kategori !== kategori) { return; }
            if (keyword && r.text.toLowerCase().indexOf(keyword) === -1) { return; }

            jumlahCocok++;

            var opt = new Option(r.text, r.value);
            opt.dataset.kategori = r.kategori;
            opt.dataset.poin = r.poin;
            aturanSelect.appendChild(opt);
        });

        // Pertahankan pilihan jika masih ada di daftar.
        var masihAda = Array.prototype.some.call(aturanSelect.options, function (o) {
            return o.value === pilihan;
        });

        aturanSelect.value = masihAda ? pilihan : '';

        // Info jumlah hasil + pesan bila tidak ada yang cocok.
        if (jumlahCocok === 0) {
            infoCari.textContent = keyword
                ? 'Tidak ada jenis pelanggaran yang cocok dengan "' + (cariAturan.value || '').trim() + '".'
                : 'Tidak ada jenis pelanggaran pada kategori ini.';
        } else if (keyword) {
            infoCari.textContent = jumlahCocok + ' jenis pelanggaran ditemukan.';
        } else {
            infoCari.textContent = '';
        }
    }

    function updatePoin() {
        poinBadge.classList.remove('active');
        poinBadge.textContent = '';

        if (! aturanSelect.value) {
            poinInput.value = '';
            infoPoin.textContent = 'Pilih jenis pelanggaran untuk mendapatkan poin.';
            return;
        }

        // Poin selalu mengikuti master aturan dan tidak bisa diubah.
        var opt  = aturanSelect.options[aturanSelect.selectedIndex];
        var poin = opt.dataset.poin;

        poinInput.value = poin;
        infoPoin.textContent = 'Poin otomatis berdasarkan aturan pelanggaran.';

        poinBadge.textContent = 'Poin aturan resmi: ' + poin + '. Tidak dapat diubah.';
        poinBadge.classList.add('active');
    }

    kategoriSelect.addEventListener('change', function () {
        renderAturan(kategoriSelect.value);
        updatePoin();
    });

    cariAturan.addEventListener('input', function () {
        renderAturan(kategoriSelect.value);
        updatePoin();
    });

    aturanSelect.addEventListener('change', updatePoin);

    // Nilai select sudah dibawa dari old() oleh Blade, jadi cukup
    // refresh tampilan poin tanpa perlu menyimpan apa pun.
    updatePoin();


    /* =====================================================
       PILIH SISWA (combobox)

       Daftar siswa sudah dibatasi server sesuai role
       (walas hanya kelasnya), jadi pencarian cukup di sisi
       klien. Hasil dirender lewat textContent, bukan innerHTML,
       agar nama yang berisi tanda kutip / tag HTML aman.
    ===================================================== */

    var SISWAS = @json($siswaOptions);

    var searchInput = $('searchSiswa');
    var results     = $('hasilSiswa');
    var searchBox   = $('searchBox');
    var siswaId     = $('siswa_id');
    var selected    = $('selectedSiswa');

    function hideResults() {
        results.classList.add('is-hidden');
        results.innerHTML = '';
    }

    function showStatus(text) {
        results.innerHTML = '';
        var div = document.createElement('div');
        div.className = 'pv-siswa-status';
        div.textContent = text;
        results.appendChild(div);
        results.classList.remove('is-hidden');
    }

    function renderResults(list) {
        results.innerHTML = '';

        if (!list.length) {
            showStatus('Siswa tidak ditemukan.');
            return;
        }

        list.forEach(function (item) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pv-siswa-item';
            btn.setAttribute('role', 'option');

            var name = document.createElement('strong');
            name.textContent = item.nama;

            var meta = document.createElement('small');
            meta.textContent = (item.nisn ? item.nisn + ' • ' : '') + (item.kelas || '-');

            btn.appendChild(name);
            btn.appendChild(meta);

            btn.addEventListener('click', function () { pickSiswa(item); });

            results.appendChild(btn);
        });

        results.classList.remove('is-hidden');
    }

    function pickSiswa(item) {
        siswaId.value = item.id;

        $('selectedAvatar').textContent = (item.nama || '?').charAt(0).toUpperCase();
        $('selectedNama').textContent = item.nama;
        $('selectedMeta').textContent = 'NISN: ' + (item.nisn || '-') + ' • Kelas: ' + (item.kelas || '-');

        selected.classList.remove('is-hidden');
        searchBox.classList.add('is-hidden');
        hideResults();
    }

    function clearSiswa() {
        siswaId.value = '';
        searchInput.value = '';
        selected.classList.add('is-hidden');
        searchBox.classList.remove('is-hidden');
        searchInput.focus();
    }

    $('gantiSiswa').addEventListener('click', clearSiswa);

    searchInput.addEventListener('input', function () {
        var q = searchInput.value.trim().toLowerCase();

        if (q.length < 2) {
            hideResults();
            return;
        }

        var list = SISWAS.filter(function (s) {
            var nama  = (s.nama || '').toLowerCase();
            var nisn  = (s.nisn || '').toLowerCase();
            return nama.indexOf(q) !== -1 || nisn.indexOf(q) !== -1;
        });

        renderResults(list.slice(0, 20));
    });

    // Navigasi keyboard: panah bawah/atas pindah antar hasil, Esc menutup.
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') {
            var first = results.querySelector('.pv-siswa-item');
            if (first) { e.preventDefault(); first.focus(); }
        } else if (e.key === 'Escape') {
            hideResults();
        }
    });

    results.addEventListener('keydown', function (e) {
        var items = Array.prototype.slice.call(results.querySelectorAll('.pv-siswa-item'));
        var i = items.indexOf(document.activeElement);

        if (e.key === 'ArrowDown' && i < items.length - 1) {
            e.preventDefault();
            items[i + 1].focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (i > 0) { items[i - 1].focus(); } else { searchInput.focus(); }
        } else if (e.key === 'Escape') {
            hideResults();
            searchInput.focus();
        }
    });

    // Klik di luar area pencarian menutup daftar hasil.
    document.addEventListener('click', function (e) {
        if (!searchBox.contains(e.target)) { hideResults(); }
    });

    // Siswa yang sudah terpilih (old() atau ?siswa_id= dari halaman
    // daftar siswa) langsung tampil sebagai chip, tanpa perlu
    // mengetik ulang namanya.
    (function restoreSiswa() {
        var awal = String(siswaId.value || '');
        if (!awal) { return; }

        var item = SISWAS.filter(function (s) { return String(s.id) === awal; })[0];
        if (!item) { return; }

        searchInput.value = '';
        pickSiswa(item);
    })();


    /* =====================================================
       FOTO BUKTI: validasi, preview, hapus
    ===================================================== */

    var MAX_SIZE = 5 * 1024 * 1024; // 5 MB, sesuai teks di form
    var fotoInput   = $('foto_bukti');
    var fotoBox     = $('uploadResult');
    var fotoImg     = $('previewFoto');
    var fotoError   = $('uploadError');
    var fotoName    = $('fileName');
    var fotoSize    = $('fileSize');
    var objectUrl   = null;

    function resetFoto() {
        if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
        fotoInput.value = '';
        fotoImg.removeAttribute('src');
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

        // Object URL lebih ringan daripada FileReader untuk foto
        // besar, karena tidak mengubah file jadi base64 di memori.
        if (objectUrl) { URL.revokeObjectURL(objectUrl); }
        objectUrl = URL.createObjectURL(file);

        fotoImg.src = objectUrl;
        fotoName.textContent = file.name;
        fotoSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        fotoBox.classList.remove('is-hidden');
    });

    $('hapusFoto').addEventListener('click', function () {
        resetFoto();
        showFotoError('');
    });

    /* Dua tombol untuk satu input file:
       - "Ambil Foto" memasang capture="environment" supaya kamera
         langsung dibuka.
       - "Pilih dari Galeri" melepas capture supaya file chooser
         menampilkan daftar foto.
       Atribut harus terpasang sebelum dialog dibuka, makanya diubah
       sebelum input.click(). */
    function openFotoDialog(capture) {
        if (capture) {
            fotoInput.setAttribute('capture', 'environment');
        } else {
            fotoInput.removeAttribute('capture');
        }
        fotoInput.click();
    }

    $('btnAmbilFoto').addEventListener('click', function () {
        openFotoDialog(true);
    });

    $('btnPilihGaleri').addEventListener('click', function () {
        openFotoDialog(false);
    });


    /* =====================================================
       MENCEGAH KIRIM GANDA
    ===================================================== */

    var btnSubmit = $('btnSimpan');
    var labelAwal = btnSubmit.textContent.trim();

    form.addEventListener('submit', function () {
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Mengirim...';
    });

    // Tombol dipulihkan jika pengguna kembali lewat tombol Back.
    window.addEventListener('pageshow', function () {
        btnSubmit.disabled = false;
        btnSubmit.textContent = labelAwal;
    });


    /* =====================================================
       DIREKTORI SISWA: cari, filter kelas, pilih
    ===================================================== */

    var dirSearch = $('searchSiswaInput');
    var dirKelas  = $('selectDirectoryKelas');
    var dirCount  = $('dirCount');
    var dirEmpty  = $('dirEmptyMessage');
    var pillRow   = $('dirPillRow');
    var dirRows   = document.querySelectorAll('.dir-siswa-row');

    function syncPills(kelasVal) {
        if (!pillRow) { return; }
        pillRow.querySelectorAll('.pill-btn').forEach(function (b) {
            b.classList.toggle('active', (b.dataset.kelas || '') === kelasVal);
        });
    }

    function filterDirectory() {
        var q = (dirSearch.value || '').toLowerCase().trim();
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

    dirSearch.addEventListener('input', filterDirectory);

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

        // Form memakai combobox, jadi siswa langsung dipasang
        // sebagai chip terpilih tanpa perlu menyaring dropdown.
        pickSiswa({
            id: btn.dataset.id,
            nama: btn.dataset.nama,
            nisn: btn.dataset.nisn,
            kelas: btn.dataset.kelas
        });

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


    })();
</script>

@endsection
