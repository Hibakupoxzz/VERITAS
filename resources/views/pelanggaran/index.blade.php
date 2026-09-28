@extends('layouts.app')

@section('title', 'Data Pelanggaran')
@section('page_title', 'Data Pelanggaran')

@section('content')

<div class="pelanggaran-page">

    {{-- HEADER --}}
    <div class="page-header">

        <div class="page-heading">
            @if(auth()->user()->isWalas())
                <h1>Pelanggaran Kelas {{ auth()->user()->kelas }}</h1>
                <p>Daftar pelanggaran siswa di kelas Anda</p>
            @else
                <h1>Data Pelanggaran</h1>
                <p>Daftar seluruh pelanggaran siswa</p>
            @endif
        </div>

        @if(auth()->user()->canDirectAddPelanggaran())
        <a href="{{ route('pelanggaran.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Pelanggaran</span>
        </a>
        @elseif(auth()->user()->canReportPelanggaran())
        <a href="{{ route('lapor.index') }}" class="btn-primary">
            <i class="fa-solid fa-bullhorn"></i>
            <span>Lapor Pelanggaran</span>
        </a>
        @endif

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>

    @endif


    {{-- EXPORT --}}
    @if(!auth()->user()->isWalas())
    <div class="export-buttons">

        <a href="{{ route('pelanggaran.export.harian') }}"
           class="btn-export">

            <i class="fa-solid fa-file-export"></i>
            <span>Export Hari Ini</span>

        </a>

        <a href="{{ route('pelanggaran.export.mingguan') }}"
           class="btn-export">

            <i class="fa-solid fa-file-export"></i>
            <span>Export Mingguan</span>

        </a>

    </div>
    @endif


    {{-- =========================
         DESKTOP TABLE
    ========================== --}}

    <div class="table-card desktop-table">

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Pelanggaran</th>
                        <th>Poin</th>
                        <th>Foto</th>
                        @if(!auth()->user()->isWalas())
                        <th>Aksi</th>
                        @endif
                    </tr>

                </thead>

                <tbody>

                    @forelse($pelanggarans as $pelanggaran)

                        <tr>

                            {{-- NO --}}
                            <td>
                                {{ $loop->iteration }}
                            </td>


                            {{-- TANGGAL --}}
                            <td class="date-cell">

                                {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y') }}

                            </td>


                            {{-- SISWA --}}
                            <td>

                                <div class="student-info">

                                    <strong>
                                        {{ $pelanggaran->siswa?->nama ?? 'Laporan Umum (Tanpa Siswa)' }}
                                    </strong>

                                    <small>
                                        {{ $pelanggaran->siswa?->kelas ?? '-' }}
                                    </small>

                                </div>

                            </td>


                            {{-- PELANGGARAN --}}
                            <td>

                                <div class="violation-wrapper">

                                    <span class="badge-danger">
                                        {{ $pelanggaran->jenis_pelanggaran }}
                                    </span>

                                    @if($pelanggaran->keterangan)

                                        <div class="description">
                                            {{ $pelanggaran->keterangan }}
                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- POIN --}}
                            <td>

                                <span class="badge-point">
                                    -{{ $pelanggaran->poin }}
                                </span>

                            </td>


                            {{-- FOTO --}}
                            <td>

                                @if($pelanggaran->foto_bukti)

                                    <img
                                        src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
                                        alt="Foto Bukti"
                                        class="table-image"
                                    >

                                @else

                                    <span class="no-image">
                                        Tidak Ada
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            @if(!auth()->user()->isWalas())
                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="{{ route('pelanggaran.show', $pelanggaran->id) }}"
                                        class="btn-detail"
                                    >
                                        Detail
                                    </a>

                                    @if(auth()->user()->canDirectAddPelanggaran())
                                    <a
                                        href="{{ route('pelanggaran.edit', $pelanggaran->id) }}"
                                        class="btn-edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('pelanggaran.destroy', $pelanggaran->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Hapus data ini?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn-delete"
                                        >
                                            Hapus
                                        </button>

                                    </form>
                                    @endif

                                </div>

                            </td>
                            @endif

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="empty">

                                <i class="fa-solid fa-inbox"></i>

                                <span>
                                    Belum ada data pelanggaran
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================
         MOBILE CARD
    ========================== --}}

    <div class="mobile-list">

        @forelse($pelanggarans as $pelanggaran)

            <div class="violation-card">

                {{-- CARD HEADER --}}
                <div class="mobile-card-header">

                    <div class="mobile-date">

                        <i class="fa-regular fa-calendar"></i>

                        {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y') }}

                    </div>

                    <span class="mobile-point">
                        -{{ $pelanggaran->poin }}
                    </span>

                </div>


                {{-- SISWA --}}
                <div class="mobile-student">

                    <strong>
                        {{ $pelanggaran->siswa?->nama ?? 'Laporan Umum (Tanpa Siswa)' }}
                    </strong>

                    <small>
                        {{ $pelanggaran->siswa?->kelas ?? '-' }}
                    </small>

                </div>


                {{-- PELANGGARAN --}}
                <div class="mobile-violation">

                    <span class="badge-danger">

                        {{ $pelanggaran->jenis_pelanggaran }}

                    </span>

                </div>


                {{-- KETERANGAN --}}
                @if($pelanggaran->keterangan)

                    <div class="mobile-description">

                        {{ $pelanggaran->keterangan }}

                    </div>

                @endif


                {{-- FOTO --}}
                @if($pelanggaran->foto_bukti)

                    <div class="mobile-photo">

                        <img
                            src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
                            alt="Foto Bukti"
                        >

                    </div>

                @endif


                {{-- AKSI --}}
                @if(!auth()->user()->isWalas())
                <div class="mobile-actions">

                    <a
                        href="{{ route('pelanggaran.show', $pelanggaran->id) }}"
                        class="btn-detail"
                    >
                        <i class="fa-solid fa-eye"></i>
                        Detail
                    </a>

                    @if(auth()->user()->canDirectAddPelanggaran())
                    <a
                        href="{{ route('pelanggaran.edit', $pelanggaran->id) }}"
                        class="btn-edit"
                    >
                        <i class="fa-solid fa-pen"></i>
                        Edit
                    </a>

                    <form
                        action="{{ route('pelanggaran.destroy', $pelanggaran->id) }}"
                        method="POST"
                        onsubmit="return confirm('Hapus data ini?')"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn-delete"
                        >
                            <i class="fa-solid fa-trash"></i>
                            Hapus
                        </button>

                    </form>
                    @endif

                </div>
                @endif

            </div>

        @empty

            <div class="mobile-empty">

                <i class="fa-solid fa-inbox"></i>

                <p>
                    Belum ada data pelanggaran
                </p>

            </div>

        @endforelse

    </div>

    <!-- APPROVE MODAL -->
    <div class="modal-overlay" id="approveModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Verifikasi Laporan Walas</h3>
                <button type="button" onclick="closeModal('approveModal')" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="approveForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p style="font-size:13px; margin-bottom:5px;">Siswa: <strong id="approveSiswaName"></strong></p>
                    <p style="font-size:13px; margin-bottom:15px; color:#6D1408;">Laporan: <strong id="approvePelanggaranText"></strong></p>
                    
                    <div class="form-group">
                        <label class="form-label">Aturan Pelanggaran Terkait <span style="color:red">*</span></label>
                        <select name="aturan_pelanggaran_id" class="form-control" onchange="toggleCustomApprove(this.value)" required>
                            <option value="">-- Pilih Sesuai Pedoman Tata Tertib --</option>
                            @foreach($aturanPelanggarans as $aturan)
                                <option value="{{ $aturan->id }}">{{ $aturan->kode }} - {{ $aturan->nama }} (-{{ $aturan->poin }} poin)</option>
                            @endforeach
                            <option value="custom">-- Pelanggaran Lainnya (Custom) --</option>
                        </select>
                    </div>

                    <div id="customApproveDiv" style="display:none; margin-top:10px;">
                        <div class="form-group">
                            <label class="form-label">Nama Pelanggaran</label>
                            <input type="text" name="jenis_pelanggaran_custom" class="form-control" placeholder="Contoh: Bermain judi online di kelas">
                        </div>
                        <div class="form-group" style="margin-top:10px;">
                            <label class="form-label">Poin Sanksi</label>
                            <input type="number" name="poin_custom" class="form-control" min="0" placeholder="Misal: 20">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Catatan Verifikasi (Opsional)</label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Pesan kepada siswa atau wali kelas terkait keputusan ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('approveModal')">Batal</button>
                    <button type="submit" class="btn-primary">Verifikasi & Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- REJECT MODAL -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tolak Laporan Walas</h3>
                <button type="button" onclick="closeModal('rejectModal')" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p style="font-size:13px; margin-bottom:15px;">Anda akan menolak laporan untuk siswa: <strong id="rejectSiswaName"></strong>.</p>
                    <div class="form-group">
                        <label class="form-label">Alasan Penolakan <span style="color:red">*</span></label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan (misal: Bukti tidak cukup kuat, laporan ganda, dll)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('rejectModal')">Batal</button>
                    <button type="submit" class="btn-danger">Tolak Laporan</button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection


@section('styles')

/* =====================================================
   DATA PELANGGARAN
===================================================== */

.pelanggaran-page{
    width:100%;
    max-width:100%;
    overflow:hidden;
}


/* =====================================================
   HEADER
===================================================== */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:20px;
    margin-bottom:20px;
}

.page-heading{
    min-width:0;
}

.page-heading h1{
    font-size:clamp(20px, 4.5vw, 34px);
    color:var(--color-primary-gray);
    margin-bottom:5px;
    line-height:1.2;
    overflow-wrap:anywhere;
}

.page-heading p{
    color:#6b7280;
    font-size:14px;
    overflow-wrap:anywhere;
}


/* =====================================================
   BUTTON PRIMARY
===================================================== */

.btn-primary{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;

    background:var(--color-secondary-red);
    color:white;

    text-decoration:none;

    padding:12px 18px;

    border-radius:12px;

    font-weight:600;
    font-size:14px;

    white-space:nowrap;

    transition:.2s;
}

.btn-primary:hover{
    transform:translateY(-2px);
    opacity:.95;
}


/* =====================================================
   ALERT
===================================================== */

.alert-success{
    display:flex;
    align-items:flex-start;
    gap:10px;

    background:#dcfce7;
    color:#166534;

    padding:14px 16px;

    border-radius:12px;

    margin-bottom:18px;

    font-size:14px;

    overflow-wrap:anywhere;
}


/* =====================================================
   EXPORT
===================================================== */

.export-buttons{
    display:flex;
    align-items:center;
    gap:10px;

    margin-bottom:20px;

    flex-wrap:wrap;
}

.btn-export{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;

    background:#6D1408;
    color:white;

    text-decoration:none;

    padding:10px 15px;

    border-radius:10px;

    font-weight:600;
    font-size:13px;

    min-height:40px;

    flex:1 1 150px;

    max-width:100%;

    transition:.2s;
}

.btn-export:hover{
    background:#5a1006;
    transform:translateY(-1px);
}


/* =====================================================
   TABLE
===================================================== */

.table-card{
    width:100%;

    background:white;

    border-radius:20px;

    overflow:hidden;

    border:1px solid #e5e7eb;

    box-shadow:0 10px 30px rgba(0,0,0,.05);
}

.table-responsive{
    width:100%;
    max-width:100%;
    overflow-x:auto;
    overflow-y:hidden;
    -webkit-overflow-scrolling:touch;
}

table{
    width:100%;
    min-width:900px;

    border-collapse:collapse;
}

thead{
    background:var(--color-primary-gray);
}

th{
    color:white;

    padding:16px 14px;

    text-align:left;

    font-weight:600;
    font-size:13px;

    white-space:nowrap;
}

td{
    padding:16px 14px;

    border-bottom:1px solid #eeeeee;

    vertical-align:middle;

    font-size:13px;
}

tbody tr{
    transition:.2s;
}

tbody tr:hover{
    background:#f9fafb;
}


/* =====================================================
   STUDENT
===================================================== */

.student-info{
    display:flex;
    flex-direction:column;

    min-width:130px;
}

.student-info strong{
    color:#111827;

    font-size:13px;

    line-height:1.4;

    overflow-wrap:anywhere;
}

.student-info small{
    color:#6b7280;

    margin-top:3px;

    font-size:11px;
}

.date-cell{
    white-space:nowrap;
}


/* =====================================================
   PELANGGARAN
===================================================== */

.violation-wrapper{
    max-width:min(420px, 100%);
}

.badge-danger{
    display:inline-block;

    background:#fee2e2;

    color:#991b1b;

    padding:6px 10px;

    border-radius:999px;

    font-size:11px;

    font-weight:600;

    line-height:1.4;

    max-width:100%;

    word-break:break-word;
}

.description{
    margin-top:7px;

    color:#6b7280;

    font-size:11px;

    line-height:1.4;

    word-break:break-word;
}


/* =====================================================
   POIN
===================================================== */

.badge-point{
    display:inline-flex;

    align-items:center;
    justify-content:center;

    min-width:42px;
    height:32px;

    padding:0 10px;

    border-radius:999px;

    background:var(--color-secondary-red);

    color:white;

    font-weight:700;

    font-size:13px;

    white-space:nowrap;
}


/* =====================================================
   FOTO
===================================================== */

.table-image{
    width:58px;
    max-width:100%;
    height:58px;

    border-radius:10px;

    object-fit:cover;

    border:1px solid #e5e7eb;

    display:block;
}

.no-image{
    color:#9ca3af;

    font-size:11px;

    white-space:nowrap;
}


/* =====================================================
   ACTION
===================================================== */

.action-buttons{
    display:flex;
    align-items:center;

    gap:6px;

    flex-wrap:wrap;

    min-width:170px;
}

.action-buttons form{
    margin:0;
}

.btn-detail,
.btn-edit,
.btn-delete{
    display:inline-flex;

    align-items:center;
    justify-content:center;

    gap:5px;

    border:none;

    padding:7px 10px;

    border-radius:8px;

    text-decoration:none;

    cursor:pointer;

    font-size:11px;

    font-weight:600;

    white-space:nowrap;

    transition:.15s;
}

.btn-detail{
    background:#dbeafe;
    color:#1d4ed8;
}

.btn-edit{
    background:#fef3c7;
    color:#92400e;
}

.btn-delete{
    background:#fee2e2;
    color:#b91c1c;
}

.btn-detail:hover,
.btn-edit:hover,
.btn-delete:hover{
    transform:translateY(-1px);
}


/* =====================================================
   EMPTY
===================================================== */

.empty{
    text-align:center;

    color:#6b7280;

    padding:45px !important;
}

.empty i{
    display:block;

    font-size:28px;

    margin-bottom:10px;
}


/* =====================================================
   MOBILE
===================================================== */

.mobile-list{
    display:none;
}

.mobile-empty{
    width:100%;

    background:white;

    border:1px solid #e5e7eb;

    border-radius:15px;

    padding:40px 20px;

    text-align:center;

    color:#6b7280;
}

.mobile-empty i{
    display:block;

    font-size:26px;

    margin-bottom:10px;
}

.mobile-empty p{
    margin:0;

    font-size:12px;

    overflow-wrap:anywhere;
}


/* =====================================================
   TABLET
===================================================== */

@media(max-width:1000px){

    .page-header{
        align-items:flex-start;
    }

    .page-heading h1{
        font-size:28px;
    }

}


/* =====================================================
   MOBILE
===================================================== */

@media(max-width:768px){

    .desktop-table{
        display:none;
    }

    .mobile-list{
        display:flex;

        flex-direction:column;

        gap:12px;

        width:100%;
    }

    .pelanggaran-page{
        width:100%;
        max-width:100%;
    }


    /* HEADER */

    .page-header{
        display:flex;

        flex-direction:column;

        align-items:stretch;

        gap:12px;

        margin-bottom:15px;
    }

    .page-heading h1{
        font-size:24px;

        line-height:1.25;
    }

    .page-heading p{
        font-size:12px;

        margin-top:4px;
    }


    /* TAMBAH */

    .btn-primary{
        width:100%;

        min-height:44px;

        padding:11px 15px;

        font-size:13px;
    }


    /* EXPORT */

    .export-buttons{
        display:flex;

        flex-direction:column;

        width:100%;

        gap:8px;

        margin-bottom:15px;
    }

    .btn-export{
        width:100%;

        min-height:42px;

        padding:10px 12px;

        font-size:13px;
    }


    /* CARD */

    .violation-card{
        width:100%;

        background:white;

        border:1px solid #e5e7eb;

        border-radius:15px;

        padding:13px;

        box-shadow:0 5px 18px rgba(0,0,0,.05);

        overflow:hidden;
    }


    /* CARD HEADER */

    .mobile-card-header{
        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:10px;

        padding-bottom:10px;

        margin-bottom:10px;

        border-bottom:1px solid #f0f0f0;
    }

    .mobile-date{
        display:flex;

        align-items:center;

        gap:6px;

        color:#9ca3af;

        font-size:11px;

        white-space:nowrap;
    }

    .mobile-point{
        display:inline-flex;

        align-items:center;

        justify-content:center;

        min-width:40px;

        height:32px;

        padding:0 9px;

        background:#6D1408;

        color:white;

        border-radius:999px;

        font-weight:700;

        font-size:13px;

        flex-shrink:0;
    }


    /* STUDENT */

    .mobile-student{
        display:flex;

        flex-direction:column;

        margin-bottom:10px;

        min-width:0;
    }

    .mobile-student strong{
        color:#111827;

        font-size:13px;

        line-height:1.4;

        word-break:break-word;
    }

    .mobile-student small{
        color:#6b7280;

        font-size:11px;

        margin-top:2px;
    }


    /* VIOLATION */

    .mobile-violation{
        width:100%;

        margin-bottom:7px;
    }

    .mobile-violation .badge-danger{
        display:block;

        width:100%;

        max-width:100%;

        border-radius:10px;

        padding:7px 9px;

        font-size:10px;

        line-height:1.4;

        overflow-wrap:anywhere;
    }


    /* DESCRIPTION */

    .mobile-description{
        color:#6b7280;

        font-size:11px;

        line-height:1.5;

        margin-bottom:10px;

        overflow-wrap:anywhere;
    }


    /* PHOTO */

    .mobile-photo{
        margin-top:8px;

        margin-bottom:12px;
    }

    .mobile-photo img{
        display:block;

        width:64px;

        height:64px;

        object-fit:cover;

        border-radius:10px;

        border:1px solid #e5e7eb;
    }


    /* ACTIONS */

    .mobile-actions{
        display:grid;

        grid-template-columns:1fr 1fr 1fr;

        gap:6px;

        width:100%;

        padding-top:10px;

        border-top:1px solid #f0f0f0;
    }

    .mobile-actions form{
        width:100%;

        margin:0;
    }

    .mobile-actions .btn-detail,
    .mobile-actions .btn-edit,
    .mobile-actions .btn-delete{
        width:100%;

        min-height:38px;

        padding:7px 4px;

        font-size:10px;

        border-radius:8px;
    }


    /* HARDENING */

    .violation-card{
        overflow:hidden;
    }

    .mobile-card-header{
        flex-wrap:wrap;
    }

    .mobile-date{
        min-width:0;

        white-space:normal;
    }

    .mobile-empty{
        padding:28px 14px;
    }

    .empty{
        padding:28px 14px !important;
    }

}


/* =====================================================
   EXTRA SMALL PHONE
===================================================== */

@media(max-width:380px){

    .page-heading h1{
        font-size:21px;
    }

    .mobile-actions{
        grid-template-columns:1fr;
    }

    .mobile-actions .btn-detail,
    .mobile-actions .btn-edit,
    .mobile-actions .btn-delete{
        min-height:38px;

        font-size:11px;
    }

}

/* =====================================================
   MODAL STYLES
===================================================== */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
    backdrop-filter: blur(2px);
}
.modal-overlay.show {
    opacity: 1;
    pointer-events: auto;
}
.modal-content {
    background: white;
    width: 90%;
    max-width: 450px;
    max-height: 92vh;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 14px;
    transform: translateY(-20px) scale(0.95);
    transition: all 0.2s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.modal-overlay.show .modal-content {
    transform: translateY(0) scale(1);
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}
.modal-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    min-width: 0;
    overflow-wrap: anywhere;
}
.close-btn {
    background: none; border: none; font-size: 18px; cursor: pointer; color: #9ca3af;
    transition: color 0.15s;
}
.close-btn:hover {
    color: #ef4444;
}
.modal-body {
    padding: 20px;
}
.modal-footer {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 10px;
    background: #f9fafb;
    position: sticky;
    bottom: 0;
}


/* =====================================================
   RESPONSIVE OVERRIDES (MOBILE-FIRST HARDENING)
   ===================================================== */

@media(max-width:1200px){

    table{
        min-width:820px;
    }

    th,
    td{
        padding:13px 12px;
    }

}

@media(max-width:1024px){

    table{
        min-width:760px;
    }

    th,
    td{
        padding:11px 10px;

        font-size:12px;
    }

    .student-info{
        min-width:110px;
    }

    .action-buttons{
        min-width:150px;
    }

    .table-image{
        width:46px;
        height:46px;
    }

    .btn-primary{
        font-size:13px;

        padding:11px 15px;
    }

}

@media(max-width:640px){

    .export-buttons{
        gap:8px;
    }

    .btn-export{
        font-size:12px;
    }

}

@media(max-width:576px){

    .modal-overlay{
        align-items:flex-end;
    }

    .modal-content{
        width:100%;
        max-width:100%;
        max-height:92vh;

        border-radius:16px 16px 0 0;
    }

    .modal-header,
    .modal-body,
    .modal-footer{
        padding:14px 16px;
    }

    .modal-footer{
        flex-direction:column;
    }

    .modal-footer .btn-secondary,
    .modal-footer .btn-primary,
    .modal-footer .btn-danger{
        width:100%;

        min-height:40px;
    }

    .form-control,
    .modal-body select,
    .modal-body input,
    .modal-body textarea{
        width:100%;
        max-width:100%;

        font-size:16px;
    }

}

@media(max-width:480px){

    .violation-card{
        padding:11px;

        border-radius:13px;
    }

    .mobile-violation .badge-danger{
        font-size:11px;
    }

    .mobile-photo img{
        width:56px;
        height:56px;
    }

}

@endsection

@section('scripts')
<script>
    function openApproveModal(id, siswa, pelanggaran) {
        document.getElementById('approveForm').action = '/pelanggaran/' + id + '/approve';
        document.getElementById('approveSiswaName').innerText = siswa;
        document.getElementById('approvePelanggaranText').innerText = pelanggaran;
        document.getElementById('approveModal').classList.add('show');
    }

    function openRejectModal(id, siswa) {
        document.getElementById('rejectForm').action = '/pelanggaran/' + id + '/reject';
        document.getElementById('rejectSiswaName').innerText = siswa;
        document.getElementById('rejectModal').classList.add('show');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    function toggleCustomApprove(val) {
        if(val === 'custom') {
            document.getElementById('customApproveDiv').style.display = 'block';
        } else {
            document.getElementById('customApproveDiv').style.display = 'none';
        }
    }
</script>
@endsection
