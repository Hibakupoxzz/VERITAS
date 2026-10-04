@extends('layouts.app')

@section('title', 'Tambah Prestasi - VERITAS')
@section('page_title', 'Tambah Prestasi')

@section('styles')
@include('partials.ui')

/* =========================================================
   TAMBAH PRESTASI
   Primitif bersama (.pv-card, .pv-field, .pv-label, .pv-input,
   .pv-select, .pv-textarea, .pv-footer, .pv-btn-*, .upload-box,
   .upload-icon, .pv-page-*) berasal dari partials/ui.blade.
========================================================= */

.pv-body,
.pv-body *,
.pv-body *::before,
.pv-body *::after {
    box-sizing: border-box;
}

.pv-body {
    width: 100%;
}

.pv-body .is-hidden {
    display: none !important;
}

.pv-inner {
    width: 100%;
    margin: 0 auto;
}

/* .pv-card di partial tidak punya padding */
.pv-inner .pv-card {
    padding: 24px 28px;
}

.pv-page-header,
.pv-page-header > div {
    min-width: 0;
}

.pv-page-header h1,
.pv-page-header p {
    overflow-wrap: anywhere;
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

.pv-error {
    margin-top: 8px;
    color: #b91c1c;
    font-size: .8rem;
    font-weight: 600;
}

/* Dua kolom (tanggal + jenis, tingkat + poin) */
.pv-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0 18px;
}


/* ── Pencarian siswa ── */
.pv-search-box {
    position: relative;
}

.pv-search {
    position: relative;
    width: 100%;
}

.pv-search input {
    padding-left: 42px;
}

.pv-search svg {
    position: absolute;
    left: 14px;
    top: 50%;
    width: 18px;
    height: 18px;
    transform: translateY(-50%);
    stroke: #9ca3af;
    pointer-events: none;
}

/* Daftar hasil tampil inline di dalam card (mendorong konten di bawahnya),
   bukan popup melayang. */
#hasilSiswa {
    margin-top: 8px;
    max-height: 340px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
}

.pv-siswa-item {
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

.pv-siswa-item:last-child {
    border-bottom: none;
}

.pv-siswa-item:hover,
.pv-siswa-item:focus-visible {
    background: #f8fafc;
    outline: none;
}

.pv-siswa-item strong {
    display: block;
}

.pv-siswa-item small {
    color: #6b7280;
}

.pv-siswa-status {
    padding: 14px;
    color: #9ca3af;
    font-size: .8rem;
    text-align: center;
}

.pv-selected {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    background: #eff6ff;
    color: #1e40af;
}

.pv-selected-avatar {
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

.pv-selected-text {
    flex: 1 1 auto;
    min-width: 0;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

.pv-selected-text strong {
    display: block;
    font-size: .9rem;
}

.pv-selected-text small {
    font-size: .75rem;
    opacity: .85;
}

.pv-selected-change {
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

.pv-selected-change:hover {
    background: #dbeafe;
}


/* ── Upload bukti ── */
.pv-inner .pv-card.pv-card-search {
    position: relative;
    z-index: 5;
    overflow: visible;
}

.upload-box:hover,
.upload-box:focus-within {
    border-color: var(--primary);
    background: #FFF8F6;
}

.upload-title {
    margin-bottom: 8px;
    color: #1F2937;
    font-size: 20px;
    font-weight: 700;
}

.upload-desc {
    max-width: 450px;
    margin-bottom: 22px;
    color: #6B7280;
    font-size: .9rem;
    line-height: 1.7;
}

.upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 13px 26px;
    border-radius: 14px;
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    transition: transform .25s ease, box-shadow .25s ease;
}

.upload-box:hover .upload-btn {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(109, 20, 8, .25);
}

.upload-error {
    margin-top: 10px;
    color: #b91c1c;
    font-size: .8rem;
    font-weight: 600;
}

.upload-result {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-top: 16px;
    padding: 14px;
    border: 1px solid #E5E7EB;
    border-radius: 14px;
    background: #fafafa;
}

#previewFoto {
    display: block;
    width: 140px;
    height: 140px;
    flex: 0 0 140px;
    object-fit: cover;
    border: 3px solid #fff;
    border-radius: 14px;
    box-shadow: 0 8px 22px rgba(0, 0, 0, .15);
}

.file-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
    min-width: 0;
}

.file-info strong {
    color: #111827;
    font-size: .9rem;
    overflow-wrap: anywhere;
}

.file-info small {
    color: #6B7280;
}

.upload-remove {
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

.upload-remove:hover {
    text-decoration: underline;
}


/* ── Footer ── */
.pv-inner .pv-footer {
    align-items: center;
}

.pv-bottom-footer {
    padding: 48px 10px 15px;
    color: #9CA3AF;
    font-size: 13px;
    line-height: 1.6;
    text-align: center;
}

.pv-bottom-footer a {
    color: var(--primary);
    font-weight: 600;
}


/* ── Responsive ── */
@media (max-width: 768px) {
    .pv-grid-2 { grid-template-columns: 1fr; }

    .pv-inner .pv-card { padding: 18px; border-radius: 16px; }

    .pv-inner .pv-footer { flex-direction: column-reverse; }

    .pv-inner .pv-btn-primary,
    .pv-inner .pv-btn-secondary {
        width: 100%;
        min-height: 46px;
    }

    .upload-box { padding: 28px 18px; }
    .upload-title { font-size: 18px; }
    .upload-desc { font-size: .85rem; }
}

@media (max-width: 576px) {
    .pv-page-header { margin-bottom: 1rem; gap: 10px; }

    .pv-page-icon { width: 40px; height: 40px; flex-basis: 40px; }
    .pv-page-icon svg { width: 20px; height: 20px; }
    .pv-page-title { font-size: 1.1rem; }
    .pv-page-sub { font-size: .72rem; }

    /* 16px mencegah iOS zoom otomatis saat input difokus */
    .pv-input,
    .pv-select,
    .pv-textarea { font-size: 16px; }

    .pv-search svg { left: 12px; width: 16px; height: 16px; }
    .pv-search input { padding-left: 38px; }

    .upload-icon { width: 62px; height: 62px; font-size: 26px; }
    .upload-box { padding: 22px 14px; }
    .upload-btn { width: 100%; justify-content: center; }

    .upload-result { flex-direction: column; align-items: center; text-align: center; }
    .file-info { align-items: center; }
    #previewFoto { width: 100%; max-width: 260px; height: auto; max-height: 220px; flex-basis: auto; }

    .pv-selected { flex-wrap: wrap; }
    .pv-selected-change { width: 100%; }

    .pv-bottom-footer { padding-top: 36px; font-size: 12px; }
}

@media (max-width: 400px) {
    .pv-inner .pv-card { padding: 14px 12px; }
    .pv-page-title { font-size: 1rem; }
    .pv-page-sub { font-size: .68rem; }
    .upload-title { font-size: 16px; }
}
@endsection


@section('content')

@php
    // Pulihkan siswa yang dipilih jika validasi gagal. Sebelumnya dicari lewat
    // /search-siswa?q=<id>, padahal pencarian memakai nama/NISN sehingga
    // siswa tidak pernah ketemu.
    $oldSiswa = null;

    if (old('siswa_id') && class_exists(\App\Models\Siswa::class)) {
        $found = \App\Models\Siswa::find(old('siswa_id'));

        if ($found) {
            $oldSiswa = [
                'id'    => $found->id,
                'nama'  => $found->nama,
                'nisn'  => $found->nisn,
                'kelas' => $found->kelas ?? '',
            ];
        }
    }

    $tingkatList = ['Sekolah', 'Kecamatan', 'Kabupaten/Kota', 'Provinsi', 'Nasional', 'Internasional'];
@endphp

<div class="pv-body">

<div class="pv-inner">

    {{-- HEADER --}}
    <div class="pv-page-header">

        <div class="pv-page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/>
                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                <path d="M4 22h16"/>
                <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
            </svg>
        </div>

        <div>
            <h1 class="pv-page-title">Tambah Prestasi</h1>
            <p class="pv-page-sub">Catat prestasi siswa dan tambahkan poin penghargaan</p>
        </div>

    </div>

    {{-- Catatan: ringkasan $errors sudah otomatis ditampilkan oleh
         layouts/app.blade.php, jadi tidak diulang di sini. --}}


    <form
        action="{{ route('prestasi.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="prestasiForm">

        @csrf


        {{-- =====================================================
             DATA SISWA
        ====================================================== --}}
        <div class="pv-card pv-card-search">

            <div class="pv-section-label">Data Siswa <span class="pv-req">*</span></div>

            <input type="hidden" name="siswa_id" id="siswa_id" value="{{ old('siswa_id') }}">

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
                        placeholder="Cari berdasarkan nama atau NISN..."
                        autocomplete="off"
                        aria-controls="hasilSiswa">
                </div>

                <div id="hasilSiswa" class="is-hidden" role="listbox"></div>

                <span class="pv-hint">Ketik minimal 2 huruf, lalu pilih siswa dari hasil pencarian.</span>

            </div>

            <div id="selectedSiswa" class="pv-selected is-hidden">
                <div class="pv-selected-avatar" id="selectedAvatar"></div>
                <div class="pv-selected-text">
                    <strong id="selectedNama"></strong>
                    <small id="selectedMeta"></small>
                </div>
                <button type="button" class="pv-selected-change" id="gantiSiswa">Ganti</button>
            </div>

            <div class="pv-error is-hidden" id="siswaError" role="alert">
                Pilih siswa terlebih dahulu.
            </div>

        </div>


        {{-- =====================================================
             INFORMASI PRESTASI
        ====================================================== --}}
        <div class="pv-card">

            <div class="pv-section-label">Informasi Prestasi</div>

            <div class="pv-grid-2">

                <div class="pv-field">
                    <label class="pv-label" for="tanggal">
                        Tanggal <span class="pv-req">*</span>
                    </label>
                    <input
                        type="date"
                        name="tanggal"
                        id="tanggal"
                        class="pv-input"
                        value="{{ old('tanggal', date('Y-m-d')) }}"
                        required>
                </div>

                <div class="pv-field">
                    <label class="pv-label" for="jenis_prestasi">
                        Jenis Prestasi <span class="pv-req">*</span>
                    </label>
                    <input
                        type="text"
                        name="jenis_prestasi"
                        id="jenis_prestasi"
                        class="pv-input"
                        value="{{ old('jenis_prestasi') }}"
                        placeholder="Contoh: Juara 1 Lomba Coding"
                        required>
                </div>

                <div class="pv-field">
                    <label class="pv-label" for="tingkat">Tingkat Prestasi</label>
                    <select name="tingkat" id="tingkat" class="pv-select">
                        <option value="">-- Pilih Tingkat --</option>
                        @foreach($tingkatList as $tingkat)
                            <option value="{{ $tingkat }}" {{ old('tingkat') === $tingkat ? 'selected' : '' }}>{{ $tingkat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pv-field">
                    <label class="pv-label" for="poin">
                        Poin Prestasi <span class="pv-req">*</span>
                    </label>
                    <input
                        type="number"
                        name="poin"
                        id="poin"
                        class="pv-input"
                        value="{{ old('poin', 0) }}"
                        min="0"
                        inputmode="numeric"
                        placeholder="Contoh: 10"
                        required>
                    <span class="pv-hint">Poin akan otomatis menambah saldo poin siswa.</span>
                </div>

            </div>

        </div>


        {{-- =====================================================
             KETERANGAN & BUKTI
        ====================================================== --}}
        <div class="pv-card">

            <div class="pv-section-label">Keterangan &amp; Bukti</div>

            <div class="pv-field">
                <label class="pv-label" for="keterangan">Keterangan</label>
                <textarea
                    name="keterangan"
                    id="keterangan"
                    class="pv-textarea"
                    rows="4"
                    placeholder="Tambahkan keterangan mengenai prestasi siswa...">{{ old('keterangan') }}</textarea>
            </div>

            <div class="pv-field">

                <label class="pv-label" for="bukti">Bukti Prestasi</label>

                <label class="upload-box" for="bukti">

                    <input
                        type="file"
                        id="bukti"
                        name="bukti"
                        accept="image/jpeg,image/png"
                        hidden>

                    <div class="upload-icon">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>

                    <div class="upload-title">Upload Bukti Prestasi</div>

                    <div class="upload-desc">
                        Pilih foto sertifikat, piala, atau dokumentasi lomba.
                        <br>
                        Format JPG atau PNG. Maksimal 2 MB.
                    </div>

                    <span class="upload-btn">
                        <i class="fa-solid fa-image"></i>
                        Pilih Foto
                    </span>

                </label>

                <div class="upload-error is-hidden" id="uploadError" role="alert"></div>

                <div class="upload-result is-hidden" id="uploadResult">
                    <img id="previewFoto" alt="Preview bukti">
                    <div class="file-info">
                        <strong id="fileName"></strong>
                        <small id="fileSize"></small>
                        <button type="button" class="upload-remove" id="hapusFoto">
                            <i class="fa-solid fa-trash"></i> Hapus foto
                        </button>
                    </div>
                </div>

            </div>

        </div>


        {{-- TOMBOL --}}
        <div class="pv-footer">

            <a href="{{ route('prestasi.index') }}" class="pv-btn-secondary">Batal</a>

            <button type="submit" class="pv-btn-primary" id="btnSimpan">Simpan Prestasi</button>

        </div>

    </form>

</div>

</div>


{{-- FOOTER --}}
<footer class="pv-bottom-footer">
    © {{ date('Y') }} VERITAS — Sistem Monitoring Pelanggaran Siswa.
    <br>
    Developed by
    <a href="https://kicauorgspark.my.id" target="_blank" rel="noopener">KicawOrgspark</a>
</footer>

@endsection


@section('scripts')
<script>
(function () {
    'use strict';

    var $ = function (id) { return document.getElementById(id); };

    var form = $('prestasiForm');


    /* =====================================================
       CARI SISWA
       Hasil dirender lewat textContent (bukan innerHTML) agar
       nama siswa yang berisi tanda kutip / tag HTML aman.
    ===================================================== */

    var SEARCH_URL = @json(url('/search-siswa'));
    var OLD_SISWA  = @json($oldSiswa);

    var searchInput = $('searchSiswa');
    var results     = $('hasilSiswa');
    var searchBox   = $('searchBox');
    var siswaId     = $('siswa_id');
    var selected    = $('selectedSiswa');
    var siswaError  = $('siswaError');

    var timer = null;
    var lastRequest = 0;

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

    function metaText(item, withLabel) {
        var nisn  = item.nisn || '-';
        var kelas = item.kelas || '';

        if (withLabel) {
            return 'NISN: ' + nisn + (kelas ? ' • Kelas: ' + kelas : '');
        }

        return nisn + (kelas ? ' • ' + kelas : '');
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
            meta.textContent = metaText(item, false);

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
        $('selectedMeta').textContent = metaText(item, true);

        selected.classList.remove('is-hidden');
        searchBox.classList.add('is-hidden');
        siswaError.classList.add('is-hidden');
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
        clearTimeout(timer);

        var q = searchInput.value.trim();

        if (q.length < 2) {
            lastRequest++; // batalkan respons yang masih berjalan
            hideResults();
            return;
        }

        timer = setTimeout(function () {
            var current = ++lastRequest;
            showStatus('Mencari...');

            fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(function (res) {
                if (!res.ok) { throw new Error('HTTP ' + res.status); }
                return res.json();
            })
            .then(function (data) {
                // Abaikan respons lama yang datang terlambat.
                if (current !== lastRequest) { return; }
                renderResults(Array.isArray(data) ? data : []);
            })
            .catch(function () {
                if (current !== lastRequest) { return; }
                showStatus('Gagal memuat data siswa. Coba lagi.');
            });
        }, 300);
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

    // Pulihkan siswa terpilih setelah validasi server gagal.
    if (OLD_SISWA) { pickSiswa(OLD_SISWA); }


    /* =====================================================
       FOTO BUKTI
    ===================================================== */

    var MAX_SIZE  = 2 * 1024 * 1024; // 2 MB
    var TIPE_OK   = ['image/jpeg', 'image/png'];
    var fotoInput = $('bukti');
    var preview   = $('previewFoto');
    var result    = $('uploadResult');
    var errorBox  = $('uploadError');
    var objectUrl = null;

    function resetFoto() {
        if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
        fotoInput.value = '';
        preview.removeAttribute('src');
        result.classList.add('is-hidden');
    }

    function showError(msg) {
        errorBox.textContent = msg;
        errorBox.classList.toggle('is-hidden', !msg);
    }

    fotoInput.addEventListener('change', function () {
        showError('');

        var file = fotoInput.files && fotoInput.files[0];
        if (!file) { resetFoto(); return; }

        if (TIPE_OK.indexOf(file.type) === -1) {
            resetFoto();
            showError('File harus berupa gambar JPG atau PNG.');
            return;
        }

        if (file.size > MAX_SIZE) {
            resetFoto();
            showError('Ukuran foto melebihi 2 MB. Pilih foto yang lebih kecil.');
            return;
        }

        if (objectUrl) { URL.revokeObjectURL(objectUrl); }
        objectUrl = URL.createObjectURL(file);

        preview.src = objectUrl;
        $('fileName').textContent = file.name;
        $('fileSize').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        result.classList.remove('is-hidden');
    });

    $('hapusFoto').addEventListener('click', function () {
        resetFoto();
        showError('');
    });


    /* =====================================================
       VALIDASI SISWA + MENCEGAH KIRIM GANDA
    ===================================================== */

    var btnSimpan = $('btnSimpan');
    var labelAwal = btnSimpan.textContent;

    form.addEventListener('submit', function (e) {
        if (!siswaId.value) {
            e.preventDefault();
            siswaError.classList.remove('is-hidden');
            searchBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            searchInput.focus();
            return;
        }

        btnSimpan.disabled = true;
        btnSimpan.textContent = 'Menyimpan...';
    });

    // Pulihkan tombol jika pengguna kembali lewat tombol Back.
    window.addEventListener('pageshow', function () {
        btnSimpan.disabled = false;
        btnSimpan.textContent = labelAwal;
    });
})();
</script>
@endsection
