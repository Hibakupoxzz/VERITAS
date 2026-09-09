@extends('layouts.app')

@section('title', 'Data Prestasi')
@section('page_title', 'Data Prestasi')

@section('content')

<div class="prestasi-page">

    {{-- HEADER --}}
    <div class="page-header">

        <div class="page-heading">
            <h1>Data Prestasi</h1>
            <p>Daftar seluruh prestasi siswa</p>
        </div>

        <a href="{{ route('prestasi.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Prestasi</span>
        </a>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
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
                        <th>Prestasi</th>
                        <th>Tingkat</th>
                        <th>Poin</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($prestasis as $prestasi)

                        <tr>

                            {{-- NO --}}
                            <td>
                                {{ $loop->iteration }}
                            </td>


                            {{-- TANGGAL --}}
                            <td class="date-cell">

                                {{ \Carbon\Carbon::parse($prestasi->tanggal)->format('d/m/Y') }}

                            </td>


                            {{-- SISWA --}}
                            <td>

                                <div class="student-info">

                                    <strong>
                                        {{ $prestasi->siswa->nama }}
                                    </strong>

                                    <small>
                                        {{ $prestasi->siswa->kelas }}
                                    </small>

                                </div>

                            </td>


                            {{-- PRESTASI --}}
                            <td>

                                <div class="prestasi-wrapper">

                                    <span class="badge-prestasi">
                                        <i class="fa-solid fa-trophy"></i>
                                        {{ $prestasi->jenis_prestasi }}
                                    </span>

                                    @if($prestasi->keterangan)

                                        <div class="description">
                                            {{ $prestasi->keterangan }}
                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- TINGKAT --}}
                            <td>

                                @if($prestasi->tingkat)

                                    <span class="badge-tingkat">
                                        {{ $prestasi->tingkat }}
                                    </span>

                                @else

                                    <span class="no-data">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- POIN --}}
                            <td>

                                <span class="badge-point">
                                    +{{ $prestasi->poin }}
                                </span>

                            </td>


                            {{-- BUKTI --}}
                            <td>

                                @if($prestasi->bukti)

                                    <img
                                        src="{{ asset('storage/' . $prestasi->bukti) }}"
                                        alt="Bukti Prestasi"
                                        class="table-image"
                                    >

                                @else

                                    <span class="no-image">
                                        Tidak Ada
                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="{{ route('prestasi.show', $prestasi->id) }}"
                                        class="btn-detail"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                        Detail
                                    </a>

                                    <a
                                        href="{{ route('prestasi.edit', $prestasi->id) }}"
                                        class="btn-edit"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('prestasi.destroy', $prestasi->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Hapus data prestasi ini?')"
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

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="empty">

                                <i class="fa-solid fa-trophy"></i>

                                <span>
                                    Belum ada data prestasi
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

        @forelse($prestasis as $prestasi)

            <div class="prestasi-card">

                {{-- CARD HEADER --}}
                <div class="mobile-card-header">

                    <div class="mobile-date">

                        <i class="fa-regular fa-calendar"></i>

                        {{ \Carbon\Carbon::parse($prestasi->tanggal)->format('d/m/Y') }}

                    </div>

                    <span class="mobile-point">
                        +{{ $prestasi->poin }}
                    </span>

                </div>


                {{-- SISWA --}}
                <div class="mobile-student">

                    <strong>
                        {{ $prestasi->siswa->nama }}
                    </strong>

                    <small>
                        {{ $prestasi->siswa->kelas }}
                    </small>

                </div>


                {{-- PRESTASI --}}
                <div class="mobile-prestasi">

                    <span class="badge-prestasi">

                        <i class="fa-solid fa-trophy"></i>

                        {{ $prestasi->jenis_prestasi }}

                    </span>

                </div>


                {{-- TINGKAT --}}
                @if($prestasi->tingkat)

                    <div class="mobile-tingkat">

                        <i class="fa-solid fa-ranking-star"></i>

                        <span>
                            {{ $prestasi->tingkat }}
                        </span>

                    </div>

                @endif


                {{-- KETERANGAN --}}
                @if($prestasi->keterangan)

                    <div class="mobile-description">

                        {{ $prestasi->keterangan }}

                    </div>

                @endif


                {{-- BUKTI --}}
                @if($prestasi->bukti)

                    <div class="mobile-photo">

                        <img
                            src="{{ asset('storage/' . $prestasi->bukti) }}"
                            alt="Bukti Prestasi"
                        >

                    </div>

                @endif


                {{-- AKSI --}}
                <div class="mobile-actions">

                    <a
                        href="{{ route('prestasi.show', $prestasi->id) }}"
                        class="btn-detail"
                    >
                        <i class="fa-solid fa-eye"></i>
                        Detail
                    </a>

                    <a
                        href="{{ route('prestasi.edit', $prestasi->id) }}"
                        class="btn-edit"
                    >
                        <i class="fa-solid fa-pen"></i>
                        Edit
                    </a>

                    <form
                        action="{{ route('prestasi.destroy', $prestasi->id) }}"
                        method="POST"
                        onsubmit="return confirm('Hapus data prestasi ini?')"
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

                </div>

            </div>

        @empty

            <div class="mobile-empty">

                <i class="fa-solid fa-trophy"></i>

                <p>
                    Belum ada data prestasi
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection


@section('styles')

<style>

/* =====================================================
   DATA PRESTASI
===================================================== */

.prestasi-page{
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
    gap:20px;
    margin-bottom:20px;
}

.page-heading h1{
    font-size:34px;
    color:var(--color-primary-gray);
    margin-bottom:5px;
    line-height:1.2;
}

.page-heading p{
    color:#6b7280;
    font-size:14px;
}


/* =====================================================
   BUTTON PRIMARY
===================================================== */

.btn-primary{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;

    background:#6D1408;
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
    background:#5a1006;
}


/* =====================================================
   ALERT
===================================================== */

.alert-success{
    display:flex;
    align-items:center;
    gap:10px;

    background:#dcfce7;
    color:#166534;

    padding:14px 16px;

    border-radius:12px;

    margin-bottom:18px;

    font-size:14px;
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
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}

table{
    width:100%;
    min-width:1050px;

    border-collapse:collapse;
}

thead{
    background:#1F2937;
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
   PRESTASI
===================================================== */

.prestasi-wrapper{
    max-width:350px;
}

.badge-prestasi{
    display:inline-flex;

    align-items:center;

    gap:6px;

    background:#fef3c7;

    color:#92400e;

    padding:7px 10px;

    border-radius:999px;

    font-size:11px;

    font-weight:600;

    line-height:1.4;

    max-width:100%;

    word-break:break-word;
}

.badge-prestasi i{
    font-size:10px;

    flex-shrink:0;
}

.description{
    margin-top:7px;

    color:#6b7280;

    font-size:11px;

    line-height:1.4;

    word-break:break-word;
}


/* =====================================================
   TINGKAT
===================================================== */

.badge-tingkat{
    display:inline-flex;

    align-items:center;

    padding:6px 10px;

    border-radius:999px;

    background:#ede9fe;

    color:#6d28d9;

    font-size:11px;

    font-weight:600;

    white-space:nowrap;
}

.no-data{
    color:#9ca3af;
}


/* =====================================================
   POIN
===================================================== */

.badge-point{
    display:inline-flex;

    align-items:center;
    justify-content:center;

    min-width:45px;
    height:32px;

    padding:0 10px;

    border-radius:999px;

    background:#166534;

    color:white;

    font-weight:700;

    font-size:13px;

    white-space:nowrap;
}


/* =====================================================
   BUKTI
===================================================== */

.table-image{
    width:58px;
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

    min-width:180px;
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

    .prestasi-page{
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


    /* CARD */

    .prestasi-card{
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

        min-width:42px;

        height:32px;

        padding:0 9px;

        background:#166534;

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


    /* PRESTASI */

    .mobile-prestasi{
        width:100%;

        margin-bottom:8px;
    }

    .mobile-prestasi .badge-prestasi{
        display:flex;

        width:100%;

        max-width:100%;

        border-radius:10px;

        padding:8px 9px;

        font-size:10px;

        line-height:1.4;

        overflow-wrap:anywhere;
    }


    /* TINGKAT */

    .mobile-tingkat{
        display:flex;

        align-items:center;

        gap:7px;

        color:#6d28d9;

        font-size:11px;

        font-weight:600;

        margin-bottom:8px;
    }

    .mobile-tingkat i{
        font-size:11px;
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

</style>

@endsection
