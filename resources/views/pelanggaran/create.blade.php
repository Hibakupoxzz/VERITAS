@extends('layouts.app')

@section('title', 'Tambah Pelanggaran')

@section('page_title', 'Tambah Pelanggaran')

@section('styles')
@include('partials.ui')

/* =========================================================
   TAMBAH PELANGGARAN
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

/* Dua kolom (tanggal + kategori). Belum didefinisikan di partial. */
.pv-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0 18px;
}

/*
 * Hanya input readonly yang tampil "terkunci".
 * Sebelumnya selector ini ikut mengenai SEMUA .pv-input dan
 * .pv-select sehingga seluruh form terlihat nonaktif.
 */
.pv-input[readonly] {
    background: #f3f4f6;
    color: #374151;
    cursor: not-allowed;
}

.pv-input[readonly]:focus {
    border-color: var(--c-border);
    box-shadow: none;
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


/* ── Jenis Pelanggaran: input cari + select ── */
#cariAturan,
#aturan_pelanggaran_id {
    display: block;
    width: 100%;
    min-height: 42px;            /* samakan tinggi keduanya */
}

#cariAturan {
    margin-bottom: 8px;          /* jarak ke dropdown */
    -webkit-appearance: none;    /* hilangkan gaya bawaan type="search" */
    appearance: none;
}

#cariAturan::-webkit-search-cancel-button {
    cursor: pointer;
}


/* ── Poin ── */
.poin-badge {
    display: none;
    margin-top: 8px;
    padding: 9px 12px;
    border-radius: 9px;
    background: #fef3c7;
    color: #92400e;
    font-size: .78rem;
    line-height: 1.5;
}

.poin-badge.active {
    display: block;
}


/* ── Upload foto ── */
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

.upload-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px;
}

.upload-box:hover .upload-btn {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(109, 20, 8, .25);
}

.upload-btn {
    border: none;
    font-family: inherit;
    font-size: 1rem;
    cursor: pointer;
}

/* Tombol galeri dibuat netral supaya pilihan kedua jelas berbeda
   dari tombol kamera yang memakai warna utama. */
.upload-btn-secondary {
    border: 2px solid var(--primary);
    background: #fff;
    color: var(--primary);
}

.upload-box:hover .upload-btn-secondary {
    box-shadow: 0 12px 25px rgba(109, 20, 8, .12);
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
    .upload-actions { flex-direction: column; }
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
    $today = date('Y-m-d');
@endphp

<div class="pv-body">

<div class="pv-inner">

    {{-- HEADER --}}
    <div class="pv-page-header">

        <div class="pv-page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 8v4l3 3"/>
                <circle cx="12" cy="12" r="10"/>
            </svg>
        </div>

        <div>
            <h1 class="pv-page-title">Tambah Pelanggaran</h1>
            <p class="pv-page-sub">Catat pelanggaran siswa ke dalam sistem</p>
        </div>

    </div>

    {{-- Catatan: ringkasan $errors sudah otomatis ditampilkan oleh
         layouts/app.blade.php, jadi tidak diulang di sini. --}}


    <form
        action="{{ route('pelanggaran.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="pelanggaranForm">

        @csrf


        {{-- =====================================================
             PILIH SISWA
        ====================================================== --}}
        <div class="pv-card pv-card-search">

            <div class="pv-section-label">Pilih Siswa</div>

            <input type="hidden" name="siswa_id" id="siswa_id">

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

                <span class="pv-hint">Ketik minimal 2 huruf. Kosongkan jika laporan tidak ditujukan ke siswa tertentu.</span>

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
                        Tanggal <span class="pv-req">*</span>
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


            {{-- JENIS PELANGGARAN --}}
            <div class="pv-field">

                <label class="pv-label" for="aturan_pelanggaran_id">Jenis Pelanggaran</label>

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
                            {{ (string) old('aturan_pelanggaran_id') === (string) $aturan->id ? 'selected' : '' }}>{{ $aturan->kode }} - {{ $aturan->nama }} ({{ $aturan->poin }} poin)</option>
                    @endforeach

                </select>

                <span class="pv-hint" id="cariAturanInfo"></span>

            </div>


            {{-- POIN --}}
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
                    Pilih jenis pelanggaran untuk mendapatkan poin.
                </small>

                <div id="poinBadge" class="poin-badge"></div>

            </div>


            {{-- KETERANGAN --}}
            <div class="pv-field">

                <label class="pv-label" for="keterangan">Keterangan</label>

                <textarea
                    name="keterangan"
                    id="keterangan"
                    class="pv-textarea"
                    placeholder="Tambahkan keterangan...">{{ old('keterangan') }}</textarea>

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

            <a href="{{ route('pelanggaran.index') }}" class="pv-btn-secondary">Kembali</a>

            <button type="submit" class="pv-btn-primary" id="btnSimpan">Simpan Pelanggaran</button>

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

    var form = $('pelanggaranForm');


    /* =====================================================
       FOTO BUKTI
    ===================================================== */

    var MAX_SIZE   = 5 * 1024 * 1024; // 5 MB
    var fotoInput  = $('foto_bukti');
    var preview    = $('previewFoto');
    var result     = $('uploadResult');
    var errorBox   = $('uploadError');
    var objectUrl  = null;

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

        if (!file.type.startsWith('image/')) {
            resetFoto();
            showError('File harus berupa gambar (JPG, PNG, atau WEBP).');
            return;
        }

        if (file.size > MAX_SIZE) {
            resetFoto();
            showError('Ukuran foto melebihi 5 MB. Pilih foto yang lebih kecil.');
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

    /* Dua tombol untuk satu input file:
       - "Ambil Foto" memasang capture="environment" supaya HP membuka
         kamera belakang langsung.
       - "Pilih dari Galeri" melepas capture supaya file chooser
         menampilkan daftar foto.
       capture harus sudah terpasang saat dialog dibuka, makanya diubah
       sebelum input.click(), bukan sesudahnya. */
    function openFileDialog(capture) {
        if (capture) {
            fotoInput.setAttribute('capture', 'environment');
        } else {
            fotoInput.removeAttribute('capture');
        }
        fotoInput.click();
    }

    $('btnAmbilFoto').addEventListener('click', function () {
        openFileDialog(true);
    });

    $('btnPilihGaleri').addEventListener('click', function () {
        openFileDialog(false);
    });


    /* =====================================================
       CARI SISWA
       Endpoint dan format respons sama seperti sebelumnya
       (GET /search-siswa?q=...). Hasil dirender lewat
       textContent, bukan innerHTML, agar nama siswa yang
       berisi tanda kutip / tag HTML tidak merusak halaman.
    ===================================================== */

    var SEARCH_URL = @json(url('/search-siswa'));

    var searchInput = $('searchSiswa');
    var results     = $('hasilSiswa');
    var searchBox   = $('searchBox');
    var siswaId     = $('siswa_id');
    var selected    = $('selectedSiswa');

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
            meta.textContent = item.nisn + ' • ' + item.kelas;

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
        $('selectedMeta').textContent = 'NISN: ' + item.nisn + ' • Kelas: ' + item.kelas;

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


    /* =====================================================
       KATEGORI + JENIS PELANGGARAN + POIN
    ===================================================== */

    var kategoriSelect = $('kategoriPelanggaran');
    var cariAturan     = $('cariAturan');
    var aturanSelect   = $('aturan_pelanggaran_id');
    var poinInput      = $('poinOtomatis');
    var infoPoin       = $('infoPoin');
    var poinBadge      = $('poinBadge');
    var infoCari       = $('cariAturanInfo');

    // Simpan daftar aturan asli sekali, lalu bangun ulang opsi saat
    // kategori / kata kunci berubah (cara ini juga andal di Safari/iOS).
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
       MENCEGAH KIRIM GANDA
    ===================================================== */

    var btnSimpan = $('btnSimpan');
    var labelAwal = btnSimpan.textContent;

    form.addEventListener('submit', function () {
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
