@extends('layouts.app')

@section('title', 'Data Pelanggaran')
@section('page_title', 'Data Pelanggaran')

@section('content')

<div class="page-header">

    <div>
        <h1>Data Pelanggaran</h1>
        <p>Daftar seluruh pelanggaran siswa</p>
    </div>

    <a href="{{ route('pelanggaran.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i>⠀Tambah Pelanggaran
    </a>

</div>

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

<div style="display:flex; gap:10px; margin-bottom:20px;">

    <a href="{{ route('pelanggaran.export.harian') }}"
       class="btn-export">
        <i class="fa-solid fa-file-export"></i> Export Hari Ini
    </a>

    <a href="{{ route('pelanggaran.export.mingguan') }}"
       class="btn-export">
        <i class="fa-solid fa-file-export"></i> Export Mingguan
    </a>

</div>

<div class="table-card">
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
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($pelanggarans as $pelanggaran)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>
                    {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y') }}
                </td>

                <td>
                    <div class="student-info">
                        <strong>{{ $pelanggaran->siswa->nama }}</strong>
                        <small>{{ $pelanggaran->siswa->kelas }}</small>
                    </div>
                </td>

                <td>

                    <span class="badge-danger">
                        {{ $pelanggaran->jenis_pelanggaran }}
                    </span>

                    @if($pelanggaran->keterangan)
                    <div class="description">
                        {{ $pelanggaran->keterangan }}
                    </div>
                    @endif

                </td>

                <td>

                    <span class="badge-point">
                        {{ $pelanggaran->poin }}
                    </span>

                </td>

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

                <td>

                    <div class="action-buttons">

                        <a
                        href="{{ route('pelanggaran.show', $pelanggaran->id) }}"
                        class="btn-detail">
                        Detail
                    </a>

                    <a
                    href="{{ route('pelanggaran.edit', $pelanggaran->id) }}"
                    class="btn-edit">
                    Edit
                </a>

                <form
                action="{{ route('pelanggaran.destroy', $pelanggaran->id) }}"
                method="POST"
                onsubmit="return confirm('Hapus data ini?')">

                @csrf
                @method('DELETE')

                <button
                type="submit"
                class="btn-delete">
                Hapus
            </button>

        </form>

    </div>

</td>

</tr>

@empty

<tr>
    <td colspan="7" class="empty">
        Belum ada data pelanggaran
    </td>
</tr>

@endforelse

</tbody>

</table>
</div>

</div>

@endsection


@section('styles')

    .page-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:25px;
    }

    .page-header h1{
        font-size:34px;
        color:var(--color-primary-gray);
        margin-bottom:5px;
    }

    .page-header p{
        color:#6b7280;
    }

    .btn-primary{
        background:var(--color-secondary-red);
        color:white;
        text-decoration:none;
        padding:12px 20px;
        border-radius:12px;
        font-weight:600;
        transition:.2s;
    }

    .btn-primary:hover{
        transform:translateY(-2px);
        opacity:.95;
    }

    .alert-success{
        background:#dcfce7;
        color:#166534;
        padding:15px;
        border-radius:12px;
        margin-bottom:20px;
    }

    .table-card{
        background:white;
        border-radius:20px;
        overflow:hidden;
        border:1px solid #e5e7eb;
        box-shadow:0 10px 30px rgba(0,0,0,.05);
    }

    table{
        width:100%;
        border-collapse:collapse;
    }

    thead{
        background:var(--color-primary-gray);
    }

    th{
        color:white;
        padding:18px;
        text-align:left;
        font-weight:600;
    }

    td{
        padding:18px;
        border-bottom:1px solid #eeeeee;
    }

    tbody tr{
        transition:.2s;
    }

    tbody tr:hover{
        background:#f9fafb;
    }

    .student-info{
        display:flex;
        flex-direction:column;
    }

    .student-info strong{
        color:#111827;
    }

    .student-info small{
        color:#6b7280;
        margin-top:4px;
    }

    .badge-danger{
        display:inline-block;
        background:#fee2e2;
        color:#991b1b;
        padding:6px 12px;
        border-radius:999px;
        font-size:12px;
        font-weight:600;
    }

    .badge-point{
        background:var(--color-secondary-red);
        color:white;
        padding:6px 12px;
        border-radius:999px;
        font-weight:600;
    }

    .description{
        margin-top:8px;
        color:#6b7280;
        font-size:13px;
    }

    .table-image{
        width:70px;
        height:70px;
        border-radius:12px;
        object-fit:cover;
        border:1px solid #e5e7eb;
    }

    .no-image{
        color:#9ca3af;
        font-size:13px;
    }

    .action-buttons{
        display:flex;
        gap:8px;
        flex-wrap:wrap;
    }

    .action-buttons form{
        margin:0;
    }

    .btn-detail,
    .btn-edit,
    .btn-delete{
        border:none;
        padding:8px 12px;
        border-radius:10px;
        text-decoration:none;
        cursor:pointer;
        font-size:13px;
        font-weight:600;
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

    .empty{
        text-align:center;
        color:#6b7280;
        padding:35px;
    }

    @media(max-width:1000px){

        .page-header{
            flex-direction:column;
            align-items:flex-start;
            gap:15px;
        }

        .table-card{
            overflow-x:auto;
        }

        table{
            min-width:900px;
        }

    }

    .btn-export{
        background:#6D1408;
        color:white;
        text-decoration:none;
        padding:10px 16px;
        border-radius:10px;
        font-weight:600;
        transition:.2s;
    }

    .btn-export:hover{
        background: #6D1408;
    }

    /* =========================
        TABLE RESPONSIVE
    ========================= */

    .table-responsive{
        width:100%;
        overflow-x:auto;
    }

    table{
        min-width:700px;
    }

        @endsection

