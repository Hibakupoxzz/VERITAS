@extends('layouts.app')

@section('title', 'Tambah Siswa')
@section('page_title', 'Tambah Siswa')

@section('styles')
<style>
.pv-wrapper{
    max-width:700px;
    width:100%;
    margin:auto;
}

.pv-header{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:25px;
    min-width:0;
}

.pv-header > div:last-child{ min-width:0; }

.pv-icon{
    width:55px;
    height:55px;
    min-width:55px;
    flex-shrink:0;
    border-radius:16px;
    background:#FBEAE8;
    color:#6D1408;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
}

.pv-title{
    font-size:clamp(1.15rem, 1rem + 1.4vw, 1.5rem);
    font-weight:700;
    color:#111827;
    overflow-wrap:anywhere;
}

.pv-subtitle{
    color:#6B7280;
    font-size:14px;
    overflow-wrap:anywhere;
}

.pv-card{
    background:white;
    border-radius:20px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    border:1px solid #E5E7EB;
    max-width:100%;
    overflow:hidden;
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
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:20px;
}

.pv-field{
    margin-bottom:20px;
    min-width:0;
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
    max-width:100%;
    padding:13px 15px;
    border:1px solid #D1D5DB;
    border-radius:12px;
    font-size:14px;
    outline:none;
    transition:.2s;
    background:#F9FAFB;
}

select.pv-input,
textarea.pv-input{
    width:100%;
    max-width:100%;
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
    flex-wrap:wrap;
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

.upload-wrapper{

    display:flex;
    flex-direction:column;
    gap:15px;

    align-items:center;

}

.camera-btn{

    width:100%;

    background:#6D1408;

    color:white;

    border:none;

    padding:16px;

    border-radius:12px;

    cursor:pointer;

    font-size:15px;

    font-weight:600;

    transition:.2s;

}

.camera-btn:hover{

    background:#521006;

}

.upload-text{

    color:#6B7280;

    font-size:13px;

}

#previewFoto{

    display:none;

    width:100%;

    max-width:420px;

    height:auto;

    border-radius:15px;

    border:2px dashed #D1D5DB;

    object-fit:cover;

}

/* ── Page footer note ── */
.pv-page-note{
    padding:120px 10px 0;
    text-align:center;
    color:#9CA3AF;
    font-size:13px;
    overflow-wrap:anywhere;
}

.pv-page-note a{
    color:#6D1408;
    font-weight:600;
    text-decoration:none;
}

/* ── Tom Select containment ── */
.searchable-select,
.ts-wrapper{
    width:100% !important;
    max-width:100%;
}

.ts-control{
    width:100%;
    max-width:100%;
}

.ts-dropdown{
    max-width:100vw;
}

/* =========================
   RESPONSIVE
   ========================= */

@media(max-width:768px){

    .pv-grid{
        grid-template-columns:1fr;
    }

    .pv-card{
        padding:22px 18px;
        border-radius:16px;
    }

    .pv-footer{
        flex-direction:column-reverse;
    }

    .btn-primary,
    .btn-secondary{
        width:100%;
        text-align:center;
        justify-content:center;
        min-height:40px;
    }

    .pv-btn-primary,
    .pv-btn-secondary{
        width:100%;
        justify-content:center;
    }

    .pv-page-note{
        padding:48px 8px 0;
    }
}

@media(max-width:640px){

    .pv-input{
        font-size:16px; /* anti-zoom on iOS */
    }

    .pv-header{
        gap:11px;
        margin-bottom:18px;
    }

    .pv-icon{
        width:44px;
        height:44px;
        min-width:44px;
        border-radius:13px;
        font-size:19px;
    }

    .pv-grid{
        gap:14px;
    }

    .camera-btn{
        padding:14px;
        min-height:40px;
    }
}

@media(max-width:576px){

    .pv-card{
        padding:18px 14px;
    }

    .pv-section-title{
        font-size:12px;
        letter-spacing:.6px;
        margin-bottom:14px;
    }

    .pv-alert{
        padding:12px;
    }
}

@media(max-width:480px){

    .pv-card{
        padding:15px 12px;
    }

    .pv-alert ul{
        margin-left:14px;
    }

    .upload-text{
        font-size:12px;
        text-align:center;
    }

    .pv-page-note{
        padding:32px 6px 0;
        font-size:12px;
    }
}

@media(max-width:400px){

    .pv-card{
        padding:13px 10px;
    }

    .btn-primary,
    .btn-secondary{
        padding:11px 14px;
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
<footer class="pv-page-note">
    © {{ date('Y') }} VERITAS — Sistem Monitoring Pelanggaran Siswa.
    <br>
    Developed by
    <a href="https://kicauorgspark.my.id"
       target="_blank">
        KicawOrgspark
    </a>
</footer>
</div>


@endsection
