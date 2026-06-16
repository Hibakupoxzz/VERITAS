@extends('layouts.app')

@section('title', 'Tambah Pelanggaran')
@section('page_title', 'Tambah Pelanggaran')

@section('styles')
<style>
    *, *::before, *::after {
        box-sizing: border-box;
    }

    .pv-body{
        background:#f5f6fa;
        min-height:90vh;
        font-family:'Inter',sans-serif;
    }

    .pv-page-header{
        display:flex;
        align-items:center;
        gap:14px;
        margin-bottom:1.75rem;
    }

    .pv-page-icon{
        width:44px;
        height:44px;
        border-radius:12px;
        background:#FBEAE8;
        color:white;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .pv-page-icon svg{
        width:22px;
        height:22px;
        stroke:#6D1408;
    }

    .pv-page-title{
        font-size:1.25rem;
        font-weight:600;
        color:#1a1a2e;
        margin:0;
    }

    .pv-page-sub{
        font-size:.8rem;
        color:#6b7280;
        margin:2px 0 0;
    }

    .pv-card{
        background:#fff;
        border:1px solid #E5E7EB;
        border-radius:20px;
        padding:1.25rem 1.5rem;
        margin-bottom:1rem;
         box-shadow:0 10px 25px rgba(0,0,0,.08);
    }

    .pv-section-label{
        font-size:.7rem;
        font-weight:600;
        letter-spacing:.06em;
        text-transform:uppercase;
        color:#9ca3af;
        display:flex;
        align-items:center;
        gap:6px;
        margin-bottom:1rem;
    }

    .pv-grid-2{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:14px;
    }

    .pv-field{
        margin-bottom:14px;
    }

    .pv-label{
        display:block;
        font-size:.8rem;
        font-weight:500;
        color:#374151;
        margin-bottom:6px;
    }

    .pv-label span{
        color:#2563eb;
    }

    .pv-input,
    .pv-select,
    .pv-textarea{
        width:100%;
        padding:9px 12px;
        border:1px solid #e2e5ea;
        border-radius:9px;
        background:#fafbfc;
        font-size:.875rem;
        outline:none;
        transition:.15s;
    }

    .pv-input:focus,
    .pv-select:focus,
    .pv-textarea:focus{
        border-color:#2563eb;
        box-shadow:0 0 0 3px rgba(37,99,235,.1);
        background:#fff;
    }

    .pv-textarea{
        resize:vertical;
        min-height:100px;
    }

    .pv-search{
        position:relative;
    }

    .pv-search input{
        padding-left:42px;
    }

    .pv-search svg{
        position:absolute;
        left:14px;
        top:50%;
        transform:translateY(-50%);
        width:18px;
        height:18px;
        stroke:#9ca3af;
    }

    #hasilSiswa{
        margin-top:10px;
        border:1px solid #e5e7eb;
        border-radius:10px;
        overflow:hidden;
        display:none;
    }

    .pv-siswa-item{
        padding:12px;
        cursor:pointer;
        background:white;
        border-bottom:1px solid #f3f4f6;
    }

    .pv-siswa-item:hover{
        background:#f8fafc;
    }

    .pv-selected{
        margin-top:12px;
        padding:12px;
        background:#eff6ff;
        border-radius:10px;
        color:#1e40af;
        display:none;
    }

    .pv-alert-error{
        background:#fff1f1;
        border:1px solid #fecaca;
        border-left:4px solid #ef4444;
        border-radius:10px;
        padding:12px 16px;
        margin-bottom:1rem;
        color:#991b1b;
    }

    .pv-footer{
        display:flex;
        justify-content:flex-end;
        gap:10px;
    }

    .pv-btn-primary{
        border:none;
        background:#6D1408;
        color:white;
        padding:12px 22px;
        border-radius:12px;
        font-weight:600;
        cursor:pointer;
        transition:.2s;
    }

    .pv-btn-primary:hover{
        background:#1d4ed8;
    }

    .pv-btn-secondary{
        background:white;
        color:#374151;
        border:1px solid #e5e7eb;
        padding:10px 20px;
        border-radius:9px;
        text-decoration:none;
    }

    .pv-btn-secondary:hover{
        background:#f9fafb;
    }

    @media(max-width:576px){
        .pv-grid-2{
            grid-template-columns:1fr;
        }

        .pv-footer{
            flex-direction:column-reverse;
        }

        .pv-btn-primary,
        .pv-btn-secondary{
            width:100%;
            text-align:center;
        }
    }

    @media(max-width:768px){

    .pv-grid-2{
        grid-template-columns:1fr;
    }

    .pv-footer{
        flex-direction:column-reverse;
    }

    .pv-btn-primary,
    .pv-btn-secondary{
        width:100%;
        justify-content:center;
    }

}
</style>
@endsection

@section('content')
<div class="pv-body">

<div class="container">

    <div class="pv-page-header">
        <div class="pv-page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                <path d="M12 8v4l3 3"/>
                <circle cx="12" cy="12" r="10"/>
            </svg>
        </div>
        <div>
            <h1 class="pv-page-title">Tambah Pelanggaran</h1>
            <p class="pv-page-sub">Catat pelanggaran siswa ke dalam sistem</p>
        </div>
    </div>

    @if($errors->any())
    <div class="pv-alert-error">
        <strong>Data belum lengkap :</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('pelanggaran.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <input type="hidden" name="siswa_id" id="siswa_id">

        <div class="pv-card">

            <div class="pv-section-label">
                Pilih Siswa
            </div>

            <div class="pv-search">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>

                <input
                    type="text"
                    id="searchSiswa"
                    class="pv-input"
                    placeholder="Cari nama atau NISN siswa">
            </div>

            <div id="hasilSiswa"></div>

            <div id="selectedSiswa" class="pv-selected"></div>

        </div>

        <div class="pv-card">

            <div class="pv-section-label">
                Detail Pelanggaran
            </div>

            <div class="pv-grid-2">

                <div class="pv-field">
                    <label class="pv-label">
                        Tanggal <span>*</span>
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        class="pv-input"
                        value="{{ old('tanggal', date('Y-m-d')) }}"
                        required>
                </div>

                <div class="pv-field">
                    <label class="pv-label">
                        Poin <span>*</span>
                    </label>

                    <input
                        type="number"
                        name="poin"
                        class="pv-input"
                        value="{{ old('poin') }}"
                        required>
                </div>

            </div>

            <div class="pv-field">
                <label class="pv-label">
                    Jenis Pelanggaran <span>*</span>
                </label>

                <input
                    type="text"
                    name="jenis_pelanggaran"
                    class="pv-input"
                    value="{{ old('jenis_pelanggaran') }}"
                    placeholder="Contoh: Terlambat masuk sekolah"
                    required>
            </div>

            <div class="pv-field">
                <label class="pv-label">
                    Keterangan
                </label>

                <textarea
                    name="keterangan"
                    class="pv-textarea"
                    placeholder="Tambahkan keterangan...">{{ old('keterangan') }}</textarea>
            </div>

        </div>

        <div class="pv-card">

            <div class="pv-section-label">
                Foto Bukti
            </div>

            <input
                type="file"
                name="foto_bukti"
                class="pv-input"
                accept="image/*">

        </div>

        <div class="pv-footer">

            <a href="{{ route('pelanggaran.index') }}"
               class="pv-btn-secondary">
                Kembali
            </a>

            <button type="submit"
                    class="pv-btn-primary">
                Simpan Pelanggaran
            </button>

        </div>

    </form>

</div>
</div>
<footer style="
    padding: 120px 10px 10px 10px;
    text-align:center;
    color:#9CA3AF;
    font-size:13px; ">
    © {{ date('Y') }} VERITAS — Sistem Monitoring Pelanggaran Siswa.
    <br>
    Developed by
    <a href="https://kicauorgspark.my.id"
       target="_blank"
       style="color:#6D1408;font-weight:600;text-decoration:none;">
        KicawOrgspark
    </a>
</footer>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
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
                    <small>${item.nisn} • ${item.kelas}</small>
                </div>`;
            });

            $('#hasilSiswa').html(html).show();

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
</script>
@endsection
