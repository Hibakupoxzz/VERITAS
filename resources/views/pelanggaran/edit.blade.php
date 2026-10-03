@extends('layouts.app')

@section('title', 'Edit Pelanggaran - VERITAS')
@section('page_title', 'Edit Pelanggaran')

@section('content')

<div class="page-heading">
    <div>
        <h1>Edit Pelanggaran</h1>
        <p>Perbarui data pelanggaran siswa di bawah ini.</p>
    </div>

    <a href="{{ route('pelanggaran.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>

        <div>
            <strong>Data belum lengkap.</strong>

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
            <i class="fa-solid fa-file-circle-exclamation"></i>
        </div>

        <div>
            <h2>Data Pelanggaran</h2>
            <p>Periksa kembali laporan sebelum menyimpan perubahan.</p>
        </div>
    </div>

    <form action="{{ route('pelanggaran.update', $pelanggaran->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{-- =========================
             DATA SISWA
        ========================== --}}

        <div class="form-section">
            <div class="section-title">
                <i class="fa-solid fa-user-graduate"></i>
                Data Siswa
            </div>

            <div class="form-group">
                <label for="siswa_id">
                    Siswa
                    <span>*</span>
                </label>

                <div class="input-icon">
                    <i class="fa-solid fa-user"></i>

                    <select
                        name="siswa_id"
                        id="siswa_id"
                        class="form-control"
                        required
                    >
                        <option value="">-- Pilih Siswa --</option>

                        @foreach ($siswas as $siswa)
                            <option
                                value="{{ $siswa->id }}"
                                {{ old('siswa_id', $pelanggaran->siswa_id) == $siswa->id ? 'selected' : '' }}
                            >
                                {{ $siswa->nama }} — {{ $siswa->kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <small class="form-help">
                    <i class="fa-solid fa-circle-info"></i>
                    Laporan umum tanpa siswa tetap bisa dipilih lewat halaman pelaporan.
                </small>
            </div>
        </div>


        {{-- =========================
             INFORMASI PELANGGARAN
        ========================== --}}

        <div class="form-section">

            <div class="section-title">
                <i class="fa-solid fa-clipboard-list"></i>
                Informasi Pelanggaran
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
                            value="{{ old('tanggal', $pelanggaran->tanggal ? \Carbon\Carbon::parse($pelanggaran->tanggal)->format('Y-m-d') : '') }}"
                            required
                        >
                    </div>
                </div>


                <div class="form-group">
                    <label for="jenis_pelanggaran">
                        Jenis Pelanggaran
                        <span>*</span>
                    </label>

                    <div class="input-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>

                        <input
                            type="text"
                            name="jenis_pelanggaran"
                            id="jenis_pelanggaran"
                            class="form-control"
                            value="{{ old('jenis_pelanggaran', $pelanggaran->jenis_pelanggaran) }}"
                            placeholder="Contoh: Terlambat, Tidak Memakai Dasi"
                            required
                        >
                    </div>
                </div>


                <div class="form-group">
                    <label for="poin">
                        Poin Pelanggaran
                        <span>*</span>
                    </label>

                    <div class="input-icon">
                        <i class="fa-solid fa-star"></i>

                        <input
                            type="number"
                            name="poin"
                            id="poin"
                            class="form-control"
                            value="{{ old('poin', $pelanggaran->poin) }}"
                            min="0"
                            max="100"
                            placeholder="Contoh: 5"
                            required
                        >
                    </div>

                    <small class="form-help">
                        <i class="fa-solid fa-circle-info"></i>
                        Mengubah poin tidak menghitung ulang saldo siswa otomatis.
                    </small>
                </div>


                <div class="form-group">
                    <label for="kategori">
                        Kategori
                    </label>

                    <div class="input-icon">
                        <i class="fa-solid fa-tags"></i>

                        <select
                            name="kategori"
                            id="kategori"
                            class="form-control"
                        >
                            <option value="">-- Pilih Kategori --</option>

                            @foreach (['Ringan', 'Sedang', 'Berat', 'Luar Biasa'] as $kategori)
                                <option
                                    value="{{ $kategori }}"
                                    {{ old('kategori', $pelanggaran->kategori) === $kategori ? 'selected' : '' }}
                                >
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            @if ($pelanggaran->aturanPelanggaran)
                <div class="form-group">
                    <label>Aturan Master</label>

                    <div class="selected-student" style="display:flex;">
                        <div class="student-icon">
                            <i class="fa-solid fa-book"></i>
                        </div>

                        <div class="student-info">
                            <strong>
                                {{ $pelanggaran->aturanPelanggaran->kode }}
                                — {{ $pelanggaran->aturanPelanggaran->nama }}
                            </strong>
                            <span>
                                {{ $pelanggaran->aturanPelanggaran->kategori }}
                                · {{ $pelanggaran->aturanPelanggaran->poin }} poin
                            </span>
                        </div>
                    </div>
                </div>
            @endif

        </div>


        {{-- =========================
             KETERANGAN & BUKTI
        ========================== --}}

        <div class="form-section">

            <div class="section-title">
                <i class="fa-solid fa-file-lines"></i>
                Keterangan &amp; Bukti
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
                    placeholder="Tambahkan kronologi atau keterangan mengenai pelanggaran ini..."
                >{{ old('keterangan', $pelanggaran->keterangan) }}</textarea>

            </div>


            @if ($pelanggaran->foto_bukti)
                <div class="form-group">

                    <label>Foto Tersimpan</label>

                    <div class="upload-box" style="padding:0;">
                        <img
                            src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
                            alt="Foto bukti pelanggaran"
                            style="display:block; width:100%; max-height:260px; object-fit:contain; background:#F9FAFB;"
                        >
                    </div>

                    <small class="form-help">
                        <i class="fa-solid fa-circle-info"></i>
                        Mengunggah foto baru akan menggantikan foto di atas.
                    </small>

                </div>
            @endif


            <div class="form-group">

                <label for="foto_bukti">
                    {{ $pelanggaran->foto_bukti ? 'Ganti Foto Bukti' : 'Upload Foto Bukti' }}
                </label>

                <div class="upload-box">

                    <input
                        type="file"
                        name="foto_bukti"
                        id="foto_bukti"
                        accept="image/*"
                    >

                    <div class="upload-content">
                        <div class="upload-icon">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>

                        <strong>Pilih foto bukti pelanggaran</strong>

                        <span>
                            JPG, JPEG, PNG maksimal 5 MB
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

            <a href="{{ route('pelanggaran.index') }}"
               class="btn btn-secondary">

                <i class="fa-solid fa-xmark"></i>
                Batal

            </a>

            <button type="submit"
                    class="btn btn-primary">

                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Perubahan

            </button>

        </div>

    </form>

</div>

@endsection


@section('styles')
@include('partials.ui')

/* ========================================
   FORM CARD HEADER
======================================== */

.form-card-header {
    display: flex;

    align-items: center;

    gap: var(--sp-5);

    padding: 22px var(--sp-9);

    border-bottom: 1px solid var(--c-border);

    background: #FFFCFA;
}

.form-card-header h2 {
    margin: 0 0 var(--sp-1);

    color: var(--c-text);

    font-size: var(--fs-lg);

    font-weight: var(--fw-bold);
}

.form-card-header p {
    margin: 0;

    color: var(--c-text-muted);

    font-size: var(--fs-base);
}


/* ========================================
   FORM SECTION
======================================== */

.form-section {
    padding: 26px var(--sp-9);

    border-bottom: 1px solid var(--c-border);
}

.section-title {
    display: flex;

    align-items: center;

    gap: 9px;

    margin-bottom: var(--sp-8);

    color: var(--c-text);

    font-size: var(--fs-md);

    font-weight: var(--fw-bold);
}

.section-title i {
    width: 20px;

    color: var(--primary);

    text-align: center;
}


/* ========================================
   FORM GRID
======================================== */

.form-grid {
    display: grid;

    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: var(--sp-8);
}

.form-grid > *,
.form-group > *,
.input-icon,
.selected-student,
.form-group:last-child {
    margin-bottom: 0;
}

.form-grid .form-group {
    margin-bottom: 0;
}

.form-group label span {
    color: var(--c-badge-danger-text);
}

select.form-control {
    cursor: pointer;
}


/* ========================================
   INPUT ICON
======================================== */

.input-icon {
    position: relative;
}

.input-icon > i {
    position: absolute;

    left: 13px;
    top: 50%;

    transform: translateY(-50%);

    color: var(--c-text-soft);

    font-size: var(--fs-sm);

    pointer-events: none;
}

.input-icon .form-control {
    padding-left: 38px;
}


/* ========================================
   ATURAN MASTER (KARTU INFO)
======================================== */

.selected-student {
    display: flex;

    align-items: center;

    gap: var(--sp-4);

    padding: var(--sp-5);

    border: 1px solid #D8E8DC;

    border-radius: var(--r-lg);

    background: #F5FBF6;
}

.student-icon {
    display: flex;

    align-items: center;

    justify-content: center;

    width: 38px;

    height: 38px;

    flex: 0 0 38px;

    border-radius: var(--r-md);

    background: #E1F0E4;

    color: var(--c-badge-success-text);
}

.student-info {
    min-width: 0;
}

.student-info strong {
    display: block;

    color: var(--c-text);

    font-size: var(--fs-md);

    font-weight: var(--fw-semibold);

    overflow-wrap: anywhere;
}

.student-info span {
    color: var(--c-text-muted);

    font-size: var(--fs-sm);
}


/* ========================================
   HELP TEXT
======================================== */

.form-help {
    display: flex;

    align-items: center;

    gap: var(--sp-2);

    margin-top: 7px;

    color: var(--c-text-muted);

    font-size: var(--fs-xs);

    flex-wrap: wrap;

    overflow-wrap: anywhere;
}

.form-help i {
    color: var(--c-text-soft);
}


/* ========================================
   UPLOAD
======================================== */

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

    padding: var(--sp-9);

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    text-align: center;

    gap: 7px;
}

.upload-content strong {
    color: var(--dark-2);

    font-size: var(--fs-base);
}

.upload-content span {
    color: var(--c-text-soft);

    font-size: var(--fs-xs);
}

.file-name {
    display: none;

    margin-top: var(--sp-3);

    padding: 9px var(--sp-5);

    border-radius: var(--r-md);

    background: var(--c-badge-neutral-bg);

    color: var(--c-badge-neutral-text);

    font-size: var(--fs-sm);

    max-width: 100%;

    overflow-wrap: anywhere;
}

.file-name.is-visible {
    display: block;
}


/* ========================================
   RESPONSIVE
======================================== */

@media (max-width: 768px) {

    .page-heading {
        align-items: flex-start;

        flex-wrap: wrap;
    }

    .page-heading > div {
        flex: 1 1 240px;

        min-width: 0;
    }

    .page-heading .btn {
        flex-shrink: 0;
    }

    .form-grid {
        grid-template-columns: 1fr;

        gap: 0;
    }

    .form-grid .form-group {
        margin-bottom: var(--sp-8);
    }

    .form-grid .form-group:last-child {
        margin-bottom: 0;
    }

    .form-section {
        padding: 22px var(--sp-7);
    }

    .form-card-header {
        padding: var(--sp-7);
    }

    .form-footer {
        padding: var(--sp-7);

        flex-direction: column-reverse;
    }
}

@endsection


@section('scripts')
<script>
    document.getElementById('foto_bukti').addEventListener('change', function () {
        const file = this.files[0];
        const label = document.getElementById('file-name');

        if (!file) {
            label.classList.remove('is-visible');
            label.textContent = '';

            return;
        }

        const maxBytes = 5 * 1024 * 1024;

        if (file.size > maxBytes) {
            this.value = '';
            label.textContent = 'Ukuran file melebihi 5 MB.';
            label.classList.add('is-visible');

            return;
        }

        label.textContent = file.name + ' · ' + (file.size / 1024).toFixed(0) + ' KB';
        label.classList.add('is-visible');
    });
</script>
@endsection