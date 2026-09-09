@extends('layouts.app')

@section('title', 'Tambah Pelanggaran')

@section('page_title', 'Tambah Pelanggaran')

@section('styles')
<style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    .pv-body {
        background: #f5f6fa;
        min-height: 90vh;
        font-family: 'Inter', sans-serif;
        width: 100%;
    }

    .pv-body .container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
    }

    .pv-page-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 1.75rem;
    }

    .pv-page-icon {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        border-radius: 12px;
        background: #FBEAE8;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pv-page-icon svg {
        width: 22px;
        height: 22px;
        stroke: #6D1408;
    }

    .pv-page-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a2e;
        margin: 0;
    }

    .pv-page-sub {
        font-size: .8rem;
        color: #6b7280;
        margin: 2px 0 0;
    }

    .pv-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,.08);
        width: 100%;
    }

    .pv-section-label {
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 1rem;
    }

    .pv-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .pv-field {
        margin-bottom: 14px;
        min-width: 0;
    }

    .pv-label {
        display: block;
        font-size: .8rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }

    .pv-input,
    .pv-select,
    .pv-textarea {
        display: block;
        width: 100%;
        max-width: 100%;
        padding: 9px 12px;
        border: 1px solid #e2e5ea;
        border-radius: 9px;
        background: #fafbfc;
        font-size: .875rem;
        outline: none;
        transition: .15s;
    }

    .pv-input:focus,
    .pv-select:focus,
    .pv-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        background: #fff;
    }

    .pv-input[readonly] {
        background: #f3f4f6;
        color: #374151;
        cursor: not-allowed;
    }

    .pv-textarea {
        resize: vertical;
        min-height: 100px;
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
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        stroke: #9ca3af;
        pointer-events: none;
    }

    #hasilSiswa {
        margin-top: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        display: none;
        width: 100%;
    }

    .pv-siswa-item {
        padding: 12px;
        cursor: pointer;
        background: white;
        border-bottom: 1px solid #f3f4f6;
        word-break: break-word;
    }

    .pv-siswa-item:hover {
        background: #f8fafc;
    }

    .pv-siswa-item:last-child {
        border-bottom: none;
    }

    .pv-selected {
        margin-top: 12px;
        padding: 12px;
        background: #eff6ff;
        border-radius: 10px;
        color: #1e40af;
        display: none;
        word-break: break-word;
    }

    .pv-alert-error {
        background: #fff1f1;
        border: 1px solid #fecaca;
        border-left: 4px solid #ef4444;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 1rem;
        color: #991b1b;
    }

    .pv-alert-error ul {
        margin: 6px 0 0 20px;
    }

    .pv-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 20px;
    }

    .pv-btn-primary {
        border: none;
        background: #6D1408;
        color: white;
        padding: 12px 22px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
    }

    .pv-btn-primary:hover {
        background: #531006;
    }

    .pv-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: #374151;
        border: 1px solid #e5e7eb;
        padding: 10px 20px;
        border-radius: 9px;
        text-decoration: none;
    }

    .pv-btn-secondary:hover {
        background: #f9fafb;
    }

    /*
    |--------------------------------------------------------------------------
    | POIN
    |--------------------------------------------------------------------------
    */

    .poin-wrapper {
        position: relative;
    }

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

    /*
    |--------------------------------------------------------------------------
    | UPLOAD FOTO
    |--------------------------------------------------------------------------
    */

    .upload-box {
        width: 100%;
        border: 2px dashed #D7D7D7;
        border-radius: 22px;
        background: #FAFAFA;
        padding: 45px;
        cursor: pointer;
        transition: .3s;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .upload-box:hover {
        border-color: #6D1408;
        background: #FFF8F6;
    }

    .upload-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #FBEAE8;
        color: #6D1408;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        margin-bottom: 22px;
    }

    .upload-title {
        font-size: 24px;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 10px;
    }

    .upload-desc {
        max-width: 450px;
        color: #6B7280;
        line-height: 1.7;
        margin-bottom: 28px;
    }

    .upload-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        border-radius: 14px;
        background: #6D1408;
        color: #fff;
        font-weight: 600;
        transition: .3s;
    }

    .upload-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(109,20,8,.25);
    }

    #previewFoto {
        display: none;
        width: 100%;
        max-width: 650px;
        max-height: 420px;
        margin-top: 30px;
        border-radius: 18px;
        object-fit: cover;
        border: 6px solid white;
        box-shadow: 0 20px 45px rgba(0,0,0,.18);
    }

    .file-info {
        display: none;
        width: 100%;
        max-width: 650px;
        margin-top: 20px;
        padding: 18px;
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        text-align: left;
        word-break: break-word;
    }

    .file-info strong {
        display: block;
        font-size: 15px;
        color: #111827;
    }

    .file-info small {
        color: #6B7280;
    }

    #infoPoin {
        display: block;
        margin-top: 6px;
        color: #6b7280;
        font-size: .75rem;
        line-height: 1.5;
    }

    /*
    |--------------------------------------------------------------------------
    | FOOTER
    |--------------------------------------------------------------------------
    */

    .pv-bottom-footer {
        padding: 60px 10px 15px;
        text-align: center;
        color: #9CA3AF;
        font-size: 13px;
        line-height: 1.6;
    }

    .pv-bottom-footer a {
        color: #6D1408;
        font-weight: 600;
        text-decoration: none;
    }

    /*
    |--------------------------------------------------------------------------
    | MOBILE
    |--------------------------------------------------------------------------
    */

    @media (max-width: 768px) {

        .pv-grid-2 {
            grid-template-columns: 1fr;
        }

        .pv-card {
            padding: 1rem;
            border-radius: 16px;
        }

        .pv-footer {
            flex-direction: column-reverse;
        }

        .pv-btn-primary,
        .pv-btn-secondary {
            width: 100%;
            min-height: 46px;
        }

        .upload-box {
            padding: 30px 20px;
        }

        .upload-title {
            font-size: 20px;
        }

        .upload-desc {
            font-size: 14px;
        }
    }

    @media (max-width: 576px) {

        .pv-page-header {
            margin-bottom: 1rem;
            gap: 10px;
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

        .pv-input,
        .pv-select,
        .pv-textarea {
            font-size: 16px;
        }

        .upload-icon {
            width: 70px;
            height: 70px;
            font-size: 30px;
        }

        .pv-bottom-footer {
            padding-top: 40px;
            font-size: 12px;
        }
    }

    @media (max-width: 380px) {

        .pv-card {
            padding: 12px;
        }

        .pv-page-title {
            font-size: 1rem;
        }

        .pv-page-sub {
            font-size: .68rem;
        }

        .upload-title {
            font-size: 18px;
        }

        .upload-desc {
            font-size: 13px;
        }
    }
</style>
@endsection


@section('content')

<div class="pv-body">

    <div class="container">

        {{-- HEADER --}}
        <div class="pv-page-header">

            <div class="pv-page-icon">
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">
                    <path d="M12 8v4l3 3"/>
                    <circle
                        cx="12"
                        cy="12"
                        r="10"/>
                </svg>
            </div>

            <div>
                <h1 class="pv-page-title">
                    Tambah Pelanggaran
                </h1>

                <p class="pv-page-sub">
                    Catat pelanggaran siswa ke dalam sistem
                </p>
            </div>

        </div>


        {{-- ERROR --}}
        @if($errors->any())

            <div class="pv-alert-error">

                <strong>
                    Data belum lengkap:
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif


        <form
            action="{{ route('pelanggaran.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf


            {{-- =========================================================
                 PILIH SISWA
                 JANGAN DIUBAH
            ========================================================== --}}

            <input
                type="hidden"
                name="siswa_id"
                id="siswa_id">

            <div class="pv-card">

                <div class="pv-section-label">
                    Pilih Siswa
                </div>

                <div class="pv-search">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">

                        <circle
                            cx="11"
                            cy="11"
                            r="8"/>

                        <line
                            x1="21"
                            y1="21"
                            x2="16.65"
                            y2="16.65"/>

                    </svg>

                    <input
                        type="text"
                        id="searchSiswa"
                        class="pv-input"
                        placeholder="Cari nama atau NISN siswa">

                </div>

                <div id="hasilSiswa"></div>

                <div
                    id="selectedSiswa"
                    class="pv-selected">
                </div>

            </div>


            {{-- =========================================================
                 DETAIL PELANGGARAN
            ========================================================== --}}

            <div class="pv-card">

                <div class="pv-section-label">
                    Detail Pelanggaran
                </div>


                {{-- TANGGAL + KATEGORI --}}

                <div class="pv-grid-2">

                    {{-- TANGGAL --}}

                    <div class="pv-field">

                        <label class="pv-label">
                            Tanggal
                            <span style="color:#2563eb;">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="pv-input"
                            value="{{ old('tanggal', date('Y-m-d')) }}"
                            required>

                    </div>


                    {{-- KATEGORI --}}

                    <div class="pv-field">

                        <label class="pv-label">
                            Kategori
                        </label>

                        <select
                            id="kategoriPelanggaran"
                            class="pv-select">

                            <option value="">
                                -- Semua Kategori --
                            </option>

                            <option value="Ringan">
                                Ringan
                            </option>

                            <option value="Sedang">
                                Sedang
                            </option>

                            <option value="Berat">
                                Berat
                            </option>

                            <option value="Luar Biasa">
                                Luar Biasa
                            </option>

                        </select>

                    </div>

                </div>


                {{-- =====================================================
                     JENIS PELANGGARAN
                ====================================================== --}}

                <div class="pv-field">

                    <label
                        class="pv-label"
                        for="aturan_pelanggaran_id">

                        Jenis Pelanggaran

                    </label>

                    <select
                        name="aturan_pelanggaran_id"
                        id="aturan_pelanggaran_id"
                        class="pv-select">

                        <option value="">
                            -- Tidak memilih pelanggaran --
                        </option>

                        @foreach($aturanPelanggarans as $aturan)

                            <option
                                value="{{ $aturan->id }}"
                                data-kategori="{{ $aturan->kategori }}"
                                data-poin="{{ $aturan->poin }}"
                                data-nama="{{ $aturan->nama }}">

                                {{ $aturan->kode }} -
                                {{ $aturan->nama }}
                                ({{ $aturan->poin }} poin)

                            </option>

                        @endforeach

                        <option value="custom">
                            Pelanggaran lainnya
                        </option>

                    </select>

                </div>


                {{-- =====================================================
                     PELANGGARAN LAINNYA
                ====================================================== --}}

                <div
                    class="pv-field"
                    id="customPelanggaran"
                    style="display:none;">

                    <label
                        class="pv-label"
                        for="jenis_pelanggaran_custom">

                        Nama Pelanggaran

                    </label>

                    <input
                        type="text"
                        name="jenis_pelanggaran_custom"
                        id="jenis_pelanggaran_custom"
                        class="pv-input"
                        value="{{ old('jenis_pelanggaran_custom') }}"
                        placeholder="Masukkan nama pelanggaran">

                </div>


                {{-- =====================================================
                     POIN
                ====================================================== --}}

                <div class="pv-field">

                    <label
                        class="pv-label"
                        for="poin_custom">

                        Poin

                    </label>

                    <div class="poin-wrapper">

                        <input
                            type="number"
                            name="poin_custom"
                            id="poin_custom"
                            class="pv-input"
                            min="0"
                            value="{{ old('poin_custom') }}"
                            placeholder="Poin otomatis"
                            readonly>

                    </div>

                    <small id="infoPoin">
                        Pilih jenis pelanggaran untuk mendapatkan poin.
                    </small>

                    <div
                        id="poinBadge"
                        class="poin-badge">

                        Poin aturan resmi tidak dapat diubah.

                    </div>

                </div>


                {{-- =====================================================
                     KETERANGAN
                ====================================================== --}}

                <div class="pv-field">

                    <label
                        class="pv-label"
                        for="keterangan">

                        Keterangan

                    </label>

                    <textarea
                        name="keterangan"
                        id="keterangan"
                        class="pv-textarea"
                        placeholder="Tambahkan keterangan...">{{ old('keterangan') }}</textarea>

                </div>

            </div>


            {{-- =========================================================
                 FOTO BUKTI
            ========================================================== --}}

            <div class="pv-card">

                <div class="pv-section-label">
                    Foto Bukti
                </div>

                <label class="upload-box">

                    <input
                        type="file"
                        id="foto_bukti"
                        name="foto_bukti"
                        accept="image/*"
                        capture="environment"
                        hidden>

                    <div class="upload-icon">
                        <i class="fa-solid fa-camera"></i>
                    </div>

                    <div class="upload-title">
                        Upload Foto Bukti
                    </div>

                    <div class="upload-desc">

                        Ambil foto menggunakan kamera
                        atau pilih dari galeri.

                        <br>

                        Format JPG, PNG, JPEG.

                    </div>

                    <span class="upload-btn">
                        <i class="fa-solid fa-image"></i>
                        Pilih Foto
                    </span>

                    <img
                        id="previewFoto"
                        alt="Preview foto">

                    <div
                        id="fileInfo"
                        class="file-info">

                        <strong id="fileName"></strong>

                        <small id="fileSize"></small>

                    </div>

                </label>

            </div>


            {{-- =========================================================
                 BUTTON
            ========================================================== --}}

            <div class="pv-footer">

                <a
                    href="{{ route('pelanggaran.index') }}"
                    class="pv-btn-secondary">

                    Kembali

                </a>

                <button
                    type="submit"
                    class="pv-btn-primary">

                    Simpan Pelanggaran

                </button>

            </div>

        </form>

    </div>

</div>


{{-- FOOTER --}}

<footer class="pv-bottom-footer">

    © {{ date('Y') }} VERITAS — Sistem Monitoring Pelanggaran Siswa.

    <br>

    Developed by

    <a
        href="https://kicauorgspark.my.id"
        target="_blank">

        KicawOrgspark

    </a>

</footer>


{{-- =========================================================
     JQUERY
========================================================= --}}

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<script>

/*
|--------------------------------------------------------------------------
| FOTO BUKTI
|--------------------------------------------------------------------------
*/

const fotoInput = document.getElementById("foto_bukti");
const preview = document.getElementById("previewFoto");
const fileInfo = document.getElementById("fileInfo");
const fileName = document.getElementById("fileName");
const fileSize = document.getElementById("fileSize");

if (fotoInput) {

    fotoInput.addEventListener("change", function () {

        if (!this.files.length) {
            return;
        }

        const file = this.files[0];

        preview.src = URL.createObjectURL(file);

        preview.style.display = "block";

        fileInfo.style.display = "block";

        fileName.textContent = file.name;

        fileSize.textContent =
            (file.size / 1024 / 1024).toFixed(2) + " MB";

    });

}


/*
|--------------------------------------------------------------------------
| SEARCH SISWA
|
| KODE SEARCH TETAP SEPERTI PUNYA KAMU
|--------------------------------------------------------------------------
*/

let timer;

$('#searchSiswa').on('keyup', function(){

    clearTimeout(timer);

    let q = $(this).val();

    if(q.length < 2){

        $('#hasilSiswa').hide();

        return;
    }

    timer = setTimeout(function(){

        $.get('/search-siswa',{q:q},function(data){

            let html = '';

            data.forEach(item => {

                html += `
                <div class="pv-siswa-item pilihSiswa"
                    data-id="${item.id}"
                    data-nama="${item.nama}"
                    data-nisn="${item.nisn}"
                    data-kelas="${item.kelas}">

                    <strong>${item.nama}</strong><br>

                    <small>
                        ${item.nisn} • ${item.kelas}
                    </small>

                </div>
                `;

            });

            $('#hasilSiswa')
                .html(html)
                .show();

        });

    },300);

});


$(document).on('click','.pilihSiswa',function(){

    let id = $(this).data('id');

    let nama = $(this).data('nama');

    let nisn = $(this).data('nisn');

    let kelas = $(this).data('kelas');

    $('#siswa_id').val(id);

    $('#searchSiswa').val(nama);

    $('#selectedSiswa')
        .html(`
            <strong>${nama}</strong><br>
            NISN : ${nisn}<br>
            Kelas : ${kelas}
        `)
        .show();

    $('#hasilSiswa').hide();

});


/*
|--------------------------------------------------------------------------
| KATEGORI + JENIS PELANGGARAN + POIN
|--------------------------------------------------------------------------
*/

$(document).ready(function(){

    const kategoriSelect =
        $('#kategoriPelanggaran');

    const aturanSelect =
        $('#aturan_pelanggaran_id');

    const customPelanggaran =
        $('#customPelanggaran');

    const namaCustom =
        $('#jenis_pelanggaran_custom');

    const poinCustom =
        $('#poin_custom');

    const infoPoin =
        $('#infoPoin');

    const poinBadge =
        $('#poinBadge');


    /*
    |--------------------------------------------------------------------------
    | Simpan seluruh option aturan
    |--------------------------------------------------------------------------
    */

    const semuaAturan = aturanSelect
        .find('option')
        .filter(function(){

            return $(this).val() !== '';

        })
        .clone();


    /*
    |--------------------------------------------------------------------------
    | Filter kategori
    |--------------------------------------------------------------------------
    */

    kategoriSelect.on('change', function(){

        const kategori = $(this).val();

        const pilihanSaatIni =
            aturanSelect.val();

        aturanSelect.empty();


        /*
         * Option kosong
         */

        aturanSelect.append(`
            <option value="">
                -- Tidak memilih pelanggaran --
            </option>
        `);


        /*
         * Tambahkan aturan sesuai kategori
         */

        semuaAturan.each(function(){

            const option = $(this);

            const value = option.val();


            /*
             * Pelanggaran lainnya selalu tampil
             */

            if(value === 'custom'){

                return;

            }


            /*
             * Jika kategori kosong,
             * tampilkan semua aturan.
             */

            if(
                kategori === '' ||
                option.attr('data-kategori') === kategori
            ){

                aturanSelect.append(
                    option.clone()
                );

            }

        });


        /*
         * Pelanggaran lainnya
         */

        const customOption =
            semuaAturan.filter(function(){

                return $(this).val() === 'custom';

            });

        if(customOption.length){

            aturanSelect.append(
                customOption.clone()
            );

        }


        /*
         * Pertahankan pilihan jika masih tersedia
         */

        const masihAda =
            aturanSelect.find(
                'option[value="' +
                pilihanSaatIni +
                '"]'
            ).length > 0;

        if(masihAda){

            aturanSelect.val(
                pilihanSaatIni
            );

        } else {

            aturanSelect.val('');

        }


        updatePoin();

    });


    /*
    |--------------------------------------------------------------------------
    | Pilih jenis pelanggaran
    |--------------------------------------------------------------------------
    */

    aturanSelect.on('change', function(){

        updatePoin();

    });


    /*
    |--------------------------------------------------------------------------
    | Fungsi update poin
    |--------------------------------------------------------------------------
    */

    function updatePoin(){

        const value =
            aturanSelect.val();


        /*
         * RESET
         */

        customPelanggaran.hide();

        namaCustom
            .prop('required', false);

        poinCustom
            .prop('required', false)
            .prop('readonly', true)
            .val('')
            .attr(
                'placeholder',
                'Poin otomatis'
            );

        poinBadge.removeClass('active');


        /*
         * TIDAK MEMILIH
         */

        if(value === ''){

            infoPoin.text(
                'Pilih jenis pelanggaran untuk mendapatkan poin.'
            );

            return;

        }


        /*
         * PELANGGARAN LAINNYA
         */

        if(value === 'custom'){

            customPelanggaran.show();

            namaCustom
                .prop('required', true);

            poinCustom
                .prop('required', true)
                .prop('readonly', false)
                .attr(
                    'placeholder',
                    'Masukkan poin manual'
                );

            infoPoin.text(
                'Poin dapat diisi manual untuk pelanggaran lainnya.'
            );

            return;

        }


        /*
         * ATURAN RESMI
         */

        const selected =
            aturanSelect.find(
                'option:selected'
            );

        const poin =
            selected.attr('data-poin');


        poinCustom
            .val(poin)
            .prop('readonly', true)
            .prop('required', false)
            .attr(
                'placeholder',
                'Poin otomatis'
            );


        infoPoin.text(
            'Poin otomatis berdasarkan aturan pelanggaran.'
        );


        poinBadge
            .addClass('active')
            .text(
                'Poin aturan resmi: ' +
                poin +
                '. Tidak dapat diubah.'
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Jalankan saat halaman pertama dibuka
    |--------------------------------------------------------------------------
    */

    updatePoin();

});


</script>

@endsection
