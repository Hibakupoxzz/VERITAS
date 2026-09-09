@extends('layouts.app')

@section('title', 'Tambah Prestasi - VERITAS')
@section('page-title', 'Tambah Prestasi')

@section('content')

<div class="page-heading">
    <div>
        <h1>Tambah Prestasi</h1>
        <p>Catat prestasi siswa dan tambahkan poin penghargaan.</p>
    </div>

    <a href="{{ route('prestasi.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>

        <div>
            <strong>Terjadi kesalahan.</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="form-card">

    <div class="form-card-header">
        <div class="header-icon">
            <i class="fa-solid fa-trophy"></i>
        </div>

        <div>
            <h2>Data Prestasi</h2>
            <p>Lengkapi informasi prestasi siswa di bawah ini.</p>
        </div>
    </div>

    <form action="{{ route('prestasi.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        {{-- =========================
             DATA SISWA
        ========================== --}}

        <div class="form-section">
            <div class="section-title">
                <i class="fa-solid fa-user-graduate"></i>
                Data Siswa
            </div>

            <div class="form-group">
                <label for="search_siswa">
                    Cari Siswa
                    <span>*</span>
                </label>

                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>

                    <input
                        type="text"
                        id="search_siswa"
                        class="form-control"
                        placeholder="Cari berdasarkan nama atau NISN..."
                        autocomplete="off"
                    >
                </div>

                <div id="search-results" class="search-results"></div>

                <input type="hidden"
                       name="siswa_id"
                       id="siswa_id"
                       value="{{ old('siswa_id') }}">

                <div id="selected-student" class="selected-student" style="display:none;">
                    <div class="student-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div class="student-info">
                        <strong id="selected-name"></strong>
                        <span id="selected-nisn"></span>
                    </div>

                    <button type="button"
                            id="remove-student"
                            class="remove-student"
                            title="Hapus siswa">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <small class="form-help">
                    <i class="fa-solid fa-circle-info"></i>
                    Pilih siswa dari hasil pencarian.
                </small>
            </div>
        </div>


        {{-- =========================
             DATA PRESTASI
        ========================== --}}

        <div class="form-section">

            <div class="section-title">
                <i class="fa-solid fa-medal"></i>
                Informasi Prestasi
            </div>

            <div class="form-grid">

                <div class="form-group">
                    <label for="tanggal">
                        Tanggal
                        <span>*</span>
                    </label>

                    <div class="input-icon">
                        <i class="fa-regular fa-calendar"></i>

                        <input
                            type="date"
                            name="tanggal"
                            id="tanggal"
                            class="form-control"
                            value="{{ old('tanggal', date('Y-m-d')) }}"
                            required
                        >
                    </div>
                </div>


                <div class="form-group">
                    <label for="jenis_prestasi">
                        Jenis Prestasi
                        <span>*</span>
                    </label>

                    <div class="input-icon">
                        <i class="fa-solid fa-trophy"></i>

                        <input
                            type="text"
                            name="jenis_prestasi"
                            id="jenis_prestasi"
                            class="form-control"
                            value="{{ old('jenis_prestasi') }}"
                            placeholder="Contoh: Juara 1 Lomba Coding"
                            required
                        >
                    </div>
                </div>


                <div class="form-group">
                    <label for="tingkat">
                        Tingkat Prestasi
                    </label>

                    <div class="input-icon">
                        <i class="fa-solid fa-ranking-star"></i>

                        <select
                            name="tingkat"
                            id="tingkat"
                            class="form-control"
                        >
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="Sekolah" {{ old('tingkat') == 'Sekolah' ? 'selected' : '' }}>
                                Sekolah
                            </option>
                            <option value="Kecamatan" {{ old('tingkat') == 'Kecamatan' ? 'selected' : '' }}>
                                Kecamatan
                            </option>
                            <option value="Kabupaten/Kota" {{ old('tingkat') == 'Kabupaten/Kota' ? 'selected' : '' }}>
                                Kabupaten/Kota
                            </option>
                            <option value="Provinsi" {{ old('tingkat') == 'Provinsi' ? 'selected' : '' }}>
                                Provinsi
                            </option>
                            <option value="Nasional" {{ old('tingkat') == 'Nasional' ? 'selected' : '' }}>
                                Nasional
                            </option>
                            <option value="Internasional" {{ old('tingkat') == 'Internasional' ? 'selected' : '' }}>
                                Internasional
                            </option>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for="poin">
                        Poin Prestasi
                        <span>*</span>
                    </label>

                    <div class="input-icon">
                        <i class="fa-solid fa-star"></i>

                        <input
                            type="number"
                            name="poin"
                            id="poin"
                            class="form-control"
                            value="{{ old('poin', 0) }}"
                            min="0"
                            placeholder="Contoh: 10"
                            required
                        >
                    </div>

                    <small class="form-help">
                        <i class="fa-solid fa-circle-info"></i>
                        Poin akan otomatis menambah saldo poin siswa.
                    </small>
                </div>

            </div>
        </div>


        {{-- =========================
             KETERANGAN & BUKTI
        ========================== --}}

        <div class="form-section">

            <div class="section-title">
                <i class="fa-solid fa-file-lines"></i>
                Keterangan & Bukti
            </div>

            <div class="form-group">

                <label for="keterangan">
                    Keterangan
                </label>

                <textarea
                    name="keterangan"
                    id="keterangan"
                    class="form-control textarea"
                    rows="4"
                    placeholder="Tambahkan keterangan mengenai prestasi siswa..."
                >{{ old('keterangan') }}</textarea>

            </div>


            <div class="form-group">

                <label for="bukti">
                    Bukti Prestasi
                </label>

                <div class="upload-box">

                    <input
                        type="file"
                        name="bukti"
                        id="bukti"
                        accept="image/*"
                    >

                    <div class="upload-content">
                        <div class="upload-icon">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>

                        <strong>Pilih foto bukti prestasi</strong>

                        <span>
                            JPG, JPEG, PNG maksimal 2 MB
                        </span>
                    </div>

                </div>

                <div id="file-name" class="file-name"></div>

            </div>

        </div>


        {{-- =========================
             ACTION
        ========================== --}}

        <div class="form-footer">

            <a href="{{ route('prestasi.index') }}"
               class="btn btn-secondary">

                <i class="fa-solid fa-xmark"></i>
                Batal

            </a>

            <button type="submit"
                    class="btn btn-primary">

                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Prestasi

            </button>

        </div>

    </form>

</div>


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


    /* ========================================
       FORM CARD
    ======================================== */

    .form-card {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,.04);
    }

    .form-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 22px 24px;
        border-bottom: 1px solid #E5E7EB;
        background: #FFFCFA;
    }

    .header-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #F9E9E6;
        color: #6D1408;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .form-card-header h2 {
        margin: 0 0 4px;
        color: #1F2937;
        font-size: 18px;
        font-weight: 700;
    }

    .form-card-header p {
        margin: 0;
        color: #6B7280;
        font-size: 13px;
    }


    /* ========================================
       FORM SECTION
    ======================================== */

    .form-section {
        padding: 26px 24px;
        border-bottom: 1px solid #E5E7EB;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 9px;
        color: #1F2937;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .section-title i {
        color: #6D1408;
        width: 20px;
        text-align: center;
    }


    /* ========================================
       FORM
    ======================================== */

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-grid .form-group {
        margin-bottom: 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .form-group label span {
        color: #B42318;
    }

    .form-control {
        width: 100%;
        height: 44px;
        padding: 0 13px;
        border: 1px solid #D1D5DB;
        border-radius: 9px;
        background: #fff;
        color: #1F2937;
        font-family: inherit;
        font-size: 14px;
        outline: none;
        transition: .2s;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #6D1408;
        box-shadow: 0 0 0 3px rgba(109, 20, 8, .08);
    }

    .textarea {
        height: auto;
        padding: 12px 13px;
        resize: vertical;
        min-height: 100px;
    }

    select.form-control {
        cursor: pointer;
    }

    .input-icon {
        position: relative;
    }

    .input-icon > i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 14px;
        pointer-events: none;
    }

    .input-icon .form-control {
        padding-left: 38px;
    }


    /* ========================================
       SEARCH SISWA
    ======================================== */

    .search-wrapper {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 14px;
        z-index: 2;
    }

    .search-wrapper .form-control {
        padding-left: 39px;
    }

    .search-results {
        position: relative;
        z-index: 20;
    }

    .search-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid #E5E7EB;
        border-top: none;
        background: #fff;
        cursor: pointer;
        transition: .15s;
    }

    .search-item:first-child {
        border-top: 1px solid #E5E7EB;
        border-radius: 9px 9px 0 0;
        margin-top: 5px;
    }

    .search-item:last-child {
        border-radius: 0 0 9px 9px;
    }

    .search-item:hover {
        background: #FFF7F5;
    }

    .search-item-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: #F9E9E6;
        color: #6D1408;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .search-item-info {
        min-width: 0;
    }

    .search-item-info strong {
        display: block;
        font-size: 14px;
        color: #1F2937;
        margin-bottom: 3px;
    }

    .search-item-info span {
        display: block;
        color: #6B7280;
        font-size: 12px;
    }

    .search-empty {
        padding: 14px;
        margin-top: 5px;
        border: 1px solid #E5E7EB;
        border-radius: 9px;
        background: #fff;
        color: #6B7280;
        text-align: center;
        font-size: 13px;
    }


    /* ========================================
       SELECTED STUDENT
    ======================================== */

    .selected-student {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 10px;
        padding: 12px;
        border: 1px solid #D8E8DC;
        border-radius: 10px;
        background: #F5FBF6;
    }

    .student-icon {
        width: 38px;
        height: 38px;
        border-radius: 9px;
        background: #E1F0E4;
        color: #287A3D;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .student-info {
        flex: 1;
        min-width: 0;
    }

    .student-info strong {
        display: block;
        color: #1F2937;
        font-size: 14px;
        margin-bottom: 3px;
    }

    .student-info span {
        display: block;
        color: #6B7280;
        font-size: 12px;
    }

    .remove-student {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 8px;
        background: #FEECEC;
        color: #B42318;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .2s;
    }

    .remove-student:hover {
        background: #FCDADA;
    }


    /* ========================================
       HELP TEXT
    ======================================== */

    .form-help {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 7px;
        color: #6B7280;
        font-size: 11px;
    }

    .form-help i {
        color: #9CA3AF;
    }


    /* ========================================
       UPLOAD
    ======================================== */

    .upload-box {
        position: relative;
        border: 1.5px dashed #D1D5DB;
        border-radius: 11px;
        background: #FAFAFA;
        transition: .2s;
        overflow: hidden;
    }

    .upload-box:hover {
        border-color: #6D1408;
        background: #FFFCFA;
    }

    .upload-box input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .upload-content {
        min-height: 145px;
        padding: 25px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 7px;
    }

    .upload-icon {
        width: 45px;
        height: 45px;
        border-radius: 11px;
        background: #F9E9E6;
        color: #6D1408;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 4px;
    }

    .upload-content strong {
        font-size: 13px;
        color: #374151;
    }

    .upload-content span {
        color: #9CA3AF;
        font-size: 11px;
    }

    .file-name {
        display: none;
        margin-top: 8px;
        padding: 9px 12px;
        border-radius: 8px;
        background: #F3F4F6;
        color: #4B5563;
        font-size: 12px;
    }


    /* ========================================
       BUTTON
    ======================================== */

    .btn {
        height: 42px;
        padding: 0 16px;
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
       FORM FOOTER
    ======================================== */

    .form-footer {
        padding: 20px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #FAFAFA;
    }


    /* ========================================
       ALERT
    ======================================== */

    .alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 13px 15px;
        margin-bottom: 20px;
        border-radius: 9px;
        font-size: 13px;
    }

    .alert-danger {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
    }

    .alert ul {
        margin: 5px 0 0 17px;
        padding: 0;
    }

    .alert li {
        margin-bottom: 2px;
    }


    /* ========================================
       RESPONSIVE
    ======================================== */

    @media (max-width: 768px) {

        .page-heading {
            align-items: flex-start;
        }

        .page-heading h1 {
            font-size: 22px;
        }

        .page-heading p {
            font-size: 13px;
        }

        .page-heading .btn {
            flex-shrink: 0;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .form-grid .form-group {
            margin-bottom: 20px;
        }

        .form-section {
            padding: 22px 18px;
        }

        .form-card-header {
            padding: 18px;
        }

        .form-footer {
            padding: 16px 18px;
            flex-direction: column-reverse;
        }

        .form-footer .btn {
            width: 100%;
        }

    }


    @media (max-width: 480px) {

        .page-heading {
            flex-direction: column;
            gap: 12px;
        }

        .page-heading .btn {
            width: 100%;
        }

        .form-card-header {
            align-items: flex-start;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            font-size: 17px;
        }

        .form-card-header h2 {
            font-size: 16px;
        }

        .section-title {
            font-size: 14px;
        }

        .upload-content {
            min-height: 130px;
        }

    }

</style>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('search_siswa');
    const searchResults = document.getElementById('search-results');

    const siswaId = document.getElementById('siswa_id');

    const selectedStudent = document.getElementById('selected-student');
    const selectedName = document.getElementById('selected-name');
    const selectedNisn = document.getElementById('selected-nisn');

    const removeStudent = document.getElementById('remove-student');

    let searchTimeout;


    /* ========================================
       SEARCH SISWA
    ======================================== */

    searchInput.addEventListener('input', function () {

        const keyword = this.value.trim();

        clearTimeout(searchTimeout);

        if (keyword.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {

            fetch(`/search-siswa?q=${encodeURIComponent(keyword)}`)
                .then(response => response.json())
                .then(data => {

                    searchResults.innerHTML = '';

                    if (!data.length) {

                        searchResults.innerHTML = `
                            <div class="search-empty">
                                <i class="fa-solid fa-user-slash"></i>
                                Siswa tidak ditemukan.
                            </div>
                        `;

                        return;
                    }


                    data.forEach(siswa => {

                        const item = document.createElement('div');

                        item.className = 'search-item';

                        item.innerHTML = `
                            <div class="search-item-icon">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>

                            <div class="search-item-info">
                                <strong>${escapeHtml(siswa.nama)}</strong>
                                <span>NISN: ${escapeHtml(siswa.nisn ?? '-')}</span>
                            </div>
                        `;


                        item.addEventListener('click', function () {

                            pilihSiswa(siswa);

                        });


                        searchResults.appendChild(item);

                    });

                })
                .catch(error => {

                    console.error(error);

                    searchResults.innerHTML = `
                        <div class="search-empty">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Gagal mengambil data siswa.
                        </div>
                    `;

                });

        }, 300);

    });


    /* ========================================
       PILIH SISWA
    ======================================== */

    function pilihSiswa(siswa) {

        siswaId.value = siswa.id;

        searchInput.value = '';

        searchResults.innerHTML = '';

        selectedName.textContent = siswa.nama;

        selectedNisn.textContent = `NISN: ${siswa.nisn ?? '-'}`;

        selectedStudent.style.display = 'flex';

    }


    /* ========================================
       HAPUS SISWA
    ======================================== */

    removeStudent.addEventListener('click', function () {

        siswaId.value = '';

        selectedName.textContent = '';

        selectedNisn.textContent = '';

        selectedStudent.style.display = 'none';

        searchInput.focus();

    });


    /* ========================================
       FILE NAME
    ======================================== */

    const fileInput = document.getElementById('bukti');
    const fileName = document.getElementById('file-name');

    fileInput.addEventListener('change', function () {

        if (this.files.length > 0) {

            fileName.style.display = 'block';

            fileName.innerHTML = `
                <i class="fa-solid fa-paperclip"></i>
                ${escapeHtml(this.files[0].name)}
            `;

        } else {

            fileName.style.display = 'none';

            fileName.innerHTML = '';

        }

    });


    /* ========================================
       ESCAPE HTML
    ======================================== */

    function escapeHtml(value) {

        const div = document.createElement('div');

        div.textContent = value ?? '';

        return div.innerHTML;

    }


    /* ========================================
       RESTORE OLD SELECTED STUDENT
    ======================================== */

    @if(old('siswa_id'))

        fetch(`/search-siswa?q={{ old('siswa_id') }}`)
            .then(response => response.json())
            .then(data => {

                const siswa = data.find(
                    item => String(item.id) === String({{ old('siswa_id') }})
                );

                if (siswa) {
                    pilihSiswa(siswa);
                }

            })
            .catch(() => {});

    @endif

});

</script>

@endsection
