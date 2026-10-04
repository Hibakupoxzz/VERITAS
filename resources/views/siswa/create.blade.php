@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page_title', 'Tambah Siswa')

@section('styles')
@include('partials.ui')

/* =========================================================
   TAMBAH SISWA
   Primitif bersama (.pv-page-header, .pv-page-icon, .pv-card,
   .pv-section-label, .pv-field, .pv-label, .pv-input,
   .pv-footer, .pv-btn-*) berasal dari partials/ui.blade.
========================================================= */

.pv-inner {
    width: 100%;
    margin: 0 auto;
}

.pv-inner,
.pv-inner *,
.pv-inner *::before,
.pv-inner *::after {
    box-sizing: border-box;
}

/* .pv-card di partial tidak punya padding */
.pv-inner .pv-card {
    padding: 24px 28px;
}

.pv-page-header,
.pv-page-header > div {
    min-width: 0;
}

/* Dua kolom (NISN + Kelas) */
.pv-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0 20px;
}

.pv-req {
    color: #dc2626;
}

.pv-hint {
    display: block;
    margin-top: 6px;
    color: #6b7280;
    font-size: .75rem;
    line-height: 1.5;
}

.pv-inner .pv-footer {
    align-items: center;
}

/* Catatan kaki halaman */
.pv-page-note {
    padding: 48px 10px 0;
    color: #9CA3AF;
    font-size: 13px;
    line-height: 1.6;
    text-align: center;
    overflow-wrap: anywhere;
}

.pv-page-note a {
    color: var(--primary);
    font-weight: 600;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {
    .pv-inner .pv-card {
        padding: 20px 16px;
        border-radius: 16px;
    }

    .pv-form-grid {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .pv-inner .pv-footer {
        flex-direction: column-reverse;
    }

    .pv-inner .pv-btn-primary,
    .pv-inner .pv-btn-secondary {
        width: 100%;
        min-height: 46px;
    }
}

@media (max-width: 576px) {
    /* 16px mencegah iOS zoom otomatis saat input difokus */
    .pv-input {
        font-size: 16px;
    }

    .pv-page-header {
        gap: 10px;
        margin-bottom: 1rem;
    }

    .pv-page-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;
    }

    .pv-page-icon svg {
        width: 20px;
        height: 20px;
    }

    .pv-page-title {
        font-size: 1.1rem;
    }

    .pv-page-sub {
        font-size: .72rem;
    }

    .pv-page-note {
        padding-top: 36px;
        font-size: 12px;
    }
}

@media (max-width: 400px) {
    .pv-inner .pv-card {
        padding: 16px 12px;
    }
}
@endsection


@section('content')

<div class="pv-inner">

    {{-- HEADER --}}
    <div class="pv-page-header">

        <div class="pv-page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/>
                <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
        </div>

        <div class="pv-page-headtext">
            <h1 class="pv-page-title">Tambah Siswa</h1>
            <p class="pv-page-sub">Tambahkan data siswa baru ke dalam sistem</p>
        </div>

    </div>

    {{-- Catatan: ringkasan $errors sudah otomatis ditampilkan oleh
         layouts/app.blade.php, jadi tidak diulang di sini. --}}


    <form action="{{ route('siswa.store') }}" method="POST" id="siswaForm">

        @csrf

        <div class="pv-card">

            <div class="pv-section-label">Data Siswa</div>

            <div class="pv-field">
                <label class="pv-label" for="nama">
                    Nama Lengkap <span class="pv-req">*</span>
                </label>

                <input
                    type="text"
                    name="nama"
                    id="nama"
                    class="pv-input"
                    placeholder="Masukkan nama siswa"
                    value="{{ old('nama') }}"
                    autocomplete="off"
                    autofocus
                    required>
            </div>

            <div class="pv-form-grid">

                <div class="pv-field">
                    <label class="pv-label" for="nisn">
                        NISN <span class="pv-req">*</span>
                    </label>

                    <input
                        type="text"
                        name="nisn"
                        id="nisn"
                        class="pv-input"
                        placeholder="Masukkan NISN"
                        value="{{ old('nisn') }}"
                        inputmode="numeric"
                        autocomplete="off"
                        required>

                    <span class="pv-hint">Hanya angka. NISN umumnya terdiri dari 9 digit.</span>
                </div>

                <div class="pv-field">
                    <label class="pv-label" for="kelas">
                        Kelas <span class="pv-req">*</span>
                    </label>

                    <input
                        type="text"
                        name="kelas"
                        id="kelas"
                        class="pv-input"
                        placeholder="Contoh: XI RPL 1"
                        value="{{ old('kelas') }}"
                        @if(isset($kelasList) && count($kelasList) > 0) list="kelasOptions" @endif
                        autocomplete="off"
                        required>

                    @if(isset($kelasList) && count($kelasList) > 0)
                        <datalist id="kelasOptions">
                            @foreach($kelasList as $kelasOption)
                                <option value="{{ $kelasOption }}"></option>
                            @endforeach
                        </datalist>
                    @endif

                    <span class="pv-hint">Tingkat, jurusan, lalu nomor kelas. Huruf otomatis dibuat kapital.</span>
                </div>

            </div>

        </div>

        <div class="pv-footer">

            <a href="{{ route('siswa.index') }}" class="pv-btn-secondary">Kembali</a>

            <button type="submit" class="pv-btn-primary" id="btnSimpan">Simpan Siswa</button>

        </div>

    </form>

    <footer class="pv-page-note">
        © {{ date('Y') }} VERITAS — Sistem Monitoring Pelanggaran Siswa.
        <br>
        Developed by
        <a href="https://kicauorgspark.my.id" target="_blank" rel="noopener">KicawOrgspark</a>
    </footer>

</div>

@endsection


@section('scripts')
<script>
(function () {
    'use strict';

    var form  = document.getElementById('siswaForm');
    var nama  = document.getElementById('nama');
    var nisn  = document.getElementById('nisn');
    var kelas = document.getElementById('kelas');
    var btn   = document.getElementById('btnSimpan');

    // NISN hanya angka: spasi / huruf dari hasil tempel dibuang.
    nisn.addEventListener('input', function () {
        var bersih = nisn.value.replace(/\D+/g, '');
        if (bersih !== nisn.value) { nisn.value = bersih; }
    });

    // Kelas diseragamkan ("xi  rpl 1" -> "XI RPL 1") agar konsisten
    // dengan filter kelas di Leaderboard dan Daftar Siswa.
    function rapikanKelas() {
        kelas.value = kelas.value.replace(/\s+/g, ' ').trim().toUpperCase();
    }

    function rapikanNama() {
        nama.value = nama.value.replace(/\s+/g, ' ').trim();
    }

    kelas.addEventListener('blur', rapikanKelas);
    nama.addEventListener('blur', rapikanNama);

    var labelAwal = btn.textContent;

    form.addEventListener('submit', function () {
        rapikanNama();
        rapikanKelas();

        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
    });

    // Pulihkan tombol jika pengguna kembali lewat tombol Back.
    window.addEventListener('pageshow', function () {
        btn.disabled = false;
        btn.textContent = labelAwal;
    });
})();
</script>
@endsection
