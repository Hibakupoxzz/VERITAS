@extends('layouts.app')

@section('title', 'Edit Prestasi - VERITAS')
@section('page-title', 'Edit Prestasi')

@section('content')

<div class="page-heading">
    <div>
        <h1>Edit Prestasi</h1>
        <p>Perbarui informasi prestasi siswa.</p>
    </div>

    <a href="{{ route('prestasi.show', $prestasi->id) }}" class="btn btn-secondary">
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


@if(session('error'))
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif


<div class="form-card">

    {{-- HEADER --}}

    <div class="form-card-header">

        <div class="header-icon">
            <i class="fa-solid fa-pen"></i>
        </div>

        <div>
            <h2>Edit Data Prestasi</h2>
            <p>Perbarui data prestasi yang telah dicatat.</p>
        </div>

    </div>


    <form action="{{ route('prestasi.update', $prestasi->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')


        {{-- =====================================
             DATA SISWA
        ====================================== --}}

        <div class="form-section">

            <div class="section-title">
                <i class="fa-solid fa-user-graduate"></i>
                Data Siswa
            </div>


            <div class="selected-student selected-always">

                <div class="student-icon">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="student-info">

                    <strong>
                        {{ $prestasi->siswa->nama ?? '-' }}
                    </strong>

                    <span>
                        NISN: {{ $prestasi->siswa->nisn ?? '-' }}
                    </span>

                </div>

                <div class="student-locked">
                    <i class="fa-solid fa-lock"></i>
                    Tidak dapat diubah
                </div>

            </div>


            <input type="hidden"
                   name="siswa_id"
                   value="{{ $prestasi->siswa_id }}">

            <small class="form-help">
                <i class="fa-solid fa-circle-info"></i>
                Siswa tidak dapat diganti saat mengedit data prestasi.
            </small>

        </div>


        {{-- =====================================
             INFORMASI PRESTASI
        ====================================== --}}

        <div class="form-section">

            <div class="section-title">
                <i class="fa-solid fa-medal"></i>
                Informasi Prestasi
            </div>


            <div class="form-grid">


                {{-- TANGGAL --}}

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
                            value="{{ old('tanggal', $prestasi->tanggal?->format('Y-m-d')) }}"
                            required
                        >

                    </div>

                </div>


                {{-- JENIS PRESTASI --}}

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
                            value="{{ old('jenis_prestasi', $prestasi->jenis_prestasi) }}"
                            placeholder="Contoh: Juara 1 Lomba Coding"
                            required
                        >

                    </div>

                </div>


                {{-- TINGKAT --}}

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

                            <option value="">
                                -- Pilih Tingkat --
                            </option>

                            <option value="Sekolah"
                                {{ old('tingkat', $prestasi->tingkat) == 'Sekolah' ? 'selected' : '' }}>
                                Sekolah
                            </option>

                            <option value="Kecamatan"
                                {{ old('tingkat', $prestasi->tingkat) == 'Kecamatan' ? 'selected' : '' }}>
                                Kecamatan
                            </option>

                            <option value="Kabupaten/Kota"
                                {{ old('tingkat', $prestasi->tingkat) == 'Kabupaten/Kota' ? 'selected' : '' }}>
                                Kabupaten/Kota
                            </option>

                            <option value="Provinsi"
                                {{ old('tingkat', $prestasi->tingkat) == 'Provinsi' ? 'selected' : '' }}>
                                Provinsi
                            </option>

                            <option value="Nasional"
                                {{ old('tingkat', $prestasi->tingkat) == 'Nasional' ? 'selected' : '' }}>
                                Nasional
                            </option>

                            <option value="Internasional"
                                {{ old('tingkat', $prestasi->tingkat) == 'Internasional' ? 'selected' : '' }}>
                                Internasional
                            </option>

                        </select>

                    </div>

                </div>


                {{-- POIN --}}

                <div class="form-group">

                    <label for="poin">
                        Poin Prestasi
                    </label>

                    <div class="input-icon">

                        <i class="fa-solid fa-star"></i>

                        <input
                            type="number"
                            id="poin"
                            class="form-control poin-disabled"
                            value="{{ $prestasi->poin }}"
                            disabled
                        >

                    </div>

                    <small class="form-help">
                        <i class="fa-solid fa-lock"></i>
                        Poin tidak dapat diubah setelah prestasi dicatat.
                    </small>

                </div>

            </div>

        </div>


        {{-- =====================================
             KETERANGAN & BUKTI
        ====================================== --}}

        <div class="form-section">

            <div class="section-title">
                <i class="fa-solid fa-file-lines"></i>
                Keterangan & Bukti
            </div>


            {{-- KETERANGAN --}}

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
                >{{ old('keterangan', $prestasi->keterangan) }}</textarea>

            </div>


            {{-- BUKTI LAMA --}}

            @if($prestasi->bukti)

                <div class="current-evidence">

                    <div class="current-evidence-header">

                        <div>
                            <strong>
                                <i class="fa-solid fa-image"></i>
                                Bukti Saat Ini
                            </strong>

                            <span>
                                Upload gambar baru jika ingin menggantinya.
                            </span>
                        </div>

                    </div>


                    <div class="current-evidence-image">

                        <img
                            src="{{ asset('storage/' . $prestasi->bukti) }}"
                            alt="Bukti {{ $prestasi->jenis_prestasi }}"
                        >

                    </div>

                </div>

            @endif


            {{-- UPLOAD BARU --}}

            <div class="form-group upload-group">

                <label for="bukti">
                    {{ $prestasi->bukti ? 'Ganti Bukti Prestasi' : 'Bukti Prestasi' }}
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

                        <strong>
                            Pilih foto bukti prestasi
                        </strong>

                        <span>
                            JPG, JPEG, PNG maksimal 2 MB
                        </span>

                    </div>

                </div>


                <div id="file-name" class="file-name"></div>

            </div>

        </div>


        {{-- =====================================
             ACTION
        ====================================== --}}

        <div class="form-footer">

            <a
                href="{{ route('prestasi.show', $prestasi->id) }}"
                class="btn btn-secondary"
            >
                <i class="fa-solid fa-xmark"></i>
                Batal
            </a>


            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Perubahan
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
   SISWA
======================================== */

.selected-student {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px;
    border: 1px solid #D8E8DC;
    border-radius: 10px;
    background: #F5FBF6;
}

.student-icon {
    width: 40px;
    height: 40px;
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

.student-locked {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #6B7280;
    font-size: 11px;
    white-space: nowrap;
}


/* ========================================
   POIN LOCKED
======================================== */

.poin-disabled {
    background: #F3F4F6;
    color: #6B7280;
    cursor: not-allowed;
}

.poin-disabled:focus {
    border-color: #D1D5DB;
    box-shadow: none;
}


/* ========================================
   HELP
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
   CURRENT EVIDENCE
======================================== */

.current-evidence {
    margin-bottom: 20px;
    border: 1px solid #E5E7EB;
    border-radius: 11px;
    overflow: hidden;
    background: #FAFAFA;
}

.current-evidence-header {
    padding: 13px 15px;
    border-bottom: 1px solid #E5E7EB;
}

.current-evidence-header strong {
    display: block;
    color: #374151;
    font-size: 12px;
    margin-bottom: 3px;
}

.current-evidence-header strong i {
    color: #6D1408;
    margin-right: 5px;
}

.current-evidence-header span {
    color: #9CA3AF;
    font-size: 11px;
}

.current-evidence-image {
    padding: 15px;
    display: flex;
    justify-content: center;
}

.current-evidence-image img {
    display: block;
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
    object-fit: contain;
}


/* ========================================
   UPLOAD
======================================== */

.upload-group {
    margin-bottom: 0;
}

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
    min-height: 140px;
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
   FOOTER
======================================== */

.form-footer {
    padding: 20px 24px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #FAFAFA;
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

    .student-locked {
        display: none;
    }

}


@media (max-width: 480px) {

    .page-heading {
        flex-direction: column;
        gap: 12px;
    }

    .page-heading > .btn {
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

    const fileInput = document.getElementById('bukti');
    const fileName = document.getElementById('file-name');


    if (fileInput) {

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

    }


    function escapeHtml(value) {

        const div = document.createElement('div');

        div.textContent = value ?? '';

        return div.innerHTML;

    }

});

</script>

@endsection
