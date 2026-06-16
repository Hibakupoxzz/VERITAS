@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page_title', 'Tambah Siswa')

@section('styles')
<style>
.pv-wrapper{
    max-width:700px;
    margin:auto;
}

.pv-header{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:25px;
}

.pv-icon{
    width:55px;
    height:55px;
    border-radius:16px;
    background:#FBEAE8;
    color:#6D1408;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
}

.pv-title{
    font-size:24px;
    font-weight:700;
    color:#111827;
}

.pv-subtitle{
    color:#6B7280;
    font-size:14px;
}

.pv-card{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    border:1px solid #E5E7EB;
}

.pv-section-title{
    font-size:13px;
    font-weight:700;
    color:#6B7280;
    text-transform:uppercase;
    letter-spacing:1px;
    margin-bottom:20px;
}

.pv-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.pv-field{
    margin-bottom:20px;
}

.pv-label{
    display:block;
    margin-bottom:8px;
    font-size:14px;
    font-weight:600;
    color:#374151;
}

.pv-label span{
    color:#DC2626;
}

.pv-input{
    width:100%;
    padding:13px 15px;
    border:1px solid #D1D5DB;
    border-radius:12px;
    font-size:14px;
    outline:none;
    transition:.2s;
    background:#F9FAFB;
}

.pv-input:focus{
    border-color:#6D1408;
    box-shadow:0 0 0 4px rgba(109,20,8,.15);
    background:white;
}

.pv-alert{
    background:#FEF2F2;
    border:1px solid #FECACA;
    color:#991B1B;
    padding:15px;
    border-radius:12px;
    margin-bottom:20px;
}

.pv-alert ul{
    margin-left:18px;
    margin-top:8px;
}

.pv-footer{
    margin-top:25px;
    display:flex;
    justify-content:flex-end;
    gap:12px;
}

.btn-secondary{
    text-decoration:none;
    background:white;
    border:1px solid #D1D5DB;
    color:#374151;
    padding:12px 20px;
    border-radius:12px;
    font-weight:600;
    transition:.2s;
}

.btn-secondary:hover{
    background:#F3F4F6;
}

.btn-primary{
    border:none;
    background:#6D1408;
    color:white;
    padding:12px 22px;
    border-radius:12px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
}

.btn-primary:hover{
    background:#581108;
}

@media(max-width:768px){

    .pv-grid{
        grid-template-columns:1fr;
    }

    .pv-footer{
        flex-direction:column-reverse;
    }

    .btn-primary,
    .btn-secondary{
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

<div class="pv-wrapper">

    <div class="pv-header">
        <div class="pv-icon">
            <i class="fa-regular fa-user"></i>
        </div>

        <div>
            <div class="pv-title">
                Tambah Siswa
            </div>

            <div class="pv-subtitle">
                Tambahkan data siswa baru ke dalam sistem
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="pv-alert">
            <strong>Terdapat kesalahan:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('siswa.store') }}" method="POST">

        @csrf

        <div class="pv-card">

            <div class="pv-section-title">
                Data Siswa
            </div>

            <div class="pv-field">
                <label class="pv-label">
                    Nama Lengkap <span>*</span>
                </label>

                <input
                    type="text"
                    name="nama"
                    class="pv-input"
                    placeholder="Masukkan nama siswa"
                    value="{{ old('nama') }}"
                    required>
            </div>

            <div class="pv-grid">

                <div class="pv-field">
                    <label class="pv-label">
                        NISN <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="nisn"
                        class="pv-input"
                        placeholder="Masukkan NISN"
                        value="{{ old('nisn') }}"
                        required>
                </div>

                <div class="pv-field">
                    <label class="pv-label">
                        Kelas <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="kelas"
                        class="pv-input"
                        placeholder="Contoh: XI RPL 1"
                        value="{{ old('kelas') }}"
                        required>
                </div>

            </div>

        </div>

        <div class="pv-footer">

            <a href="{{ route('siswa.index') }}" class="btn-secondary">
                Kembali
            </a>

            <button type="submit" class="btn-primary">
                Simpan Siswa
            </button>

        </div>

    </form>
<footer style="
    padding: 120px 10px 0px 10px;
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
</div>

@endsection
