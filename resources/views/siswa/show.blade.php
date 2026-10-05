@extends('layouts.app')

@section('title', 'Detail Siswa - VERITAS')
@section('page_title', 'Detail Siswa')

@section('content')

@php
    $pelanggarans     = $siswa->pelanggarans;
    $prestasis        = $siswa->prestasis;

    $totalPelanggaran = $pelanggarans->sum('poin');
    $totalPrestasi    = $prestasis->sum('poin');
    $saldoPoin        = 100 - $totalPelanggaran + $totalPrestasi;

    $saldoClass = $saldoPoin >= 80
        ? 'text-success'
        : ($saldoPoin >= 50 ? 'text-warning' : 'text-danger');
@endphp

<div class="siswa-detail">

    {{-- =========================================================
         HEADING
    ========================================================= --}}
    <div class="sd-heading">

        <div>
            <h1>
                <i class="fa-solid fa-user-graduate"></i>
                Detail Siswa
            </h1>
            <p>Informasi lengkap siswa dan riwayat poin</p>
        </div>

        <div class="sd-actions">
            <a href="{{ route('siswa.edit', $siswa->id) }}" class="btn btn-primary">
                <i class="fa-solid fa-pen"></i>
                Edit Siswa
            </a>

            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>

    </div>


    {{-- =========================================================
         DATA SISWA
    ========================================================= --}}
    <div class="student-profile">

        <div class="profile-icon">
            <i class="fa-solid fa-user-graduate"></i>
        </div>

        <div class="profile-info">

            <h2>{{ $siswa->nama }}</h2>

            <div class="profile-meta">
                <span>
                    <i class="fa-solid fa-id-card"></i>
                    NISN: {{ $siswa->nisn }}
                </span>

                @if($siswa->kelas)
                    <span>
                        <i class="fa-solid fa-school"></i>
                        Kelas: {{ $siswa->kelas }}
                    </span>
                @endif

                @if($siswa->jurusan)
                    <span>
                        <i class="fa-solid fa-book"></i>
                        {{ $siswa->jurusan }}
                    </span>
                @endif
            </div>

        </div>

    </div>


    {{-- =========================================================
         STATISTIK POIN
    ========================================================= --}}
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stat-icon neutral"><i class="fa-solid fa-star"></i></div>
            <div class="stat-content">
                <span class="stat-label">Poin Awal</span>
                <strong>100</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="stat-content">
                <span class="stat-label">Poin Pelanggaran</span>
                <strong class="text-danger">-{{ $totalPelanggaran }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success"><i class="fa-solid fa-trophy"></i></div>
            <div class="stat-content">
                <span class="stat-label">Poin Prestasi</span>
                <strong class="text-success">+{{ $totalPrestasi }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon saldo"><i class="fa-solid fa-chart-line"></i></div>
            <div class="stat-content">
                <span class="stat-label">Saldo Poin</span>
                <strong class="{{ $saldoClass }}">{{ $saldoPoin }}</strong>
            </div>
        </div>

    </div>


    {{-- =========================================================
         RIWAYAT
    ========================================================= --}}
    <div class="history-grid">

        {{-- ================= PELANGGARAN ================= --}}
        <div class="table-card">

            <div class="card-header">
                <div>
                    <h3>
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Riwayat Pelanggaran
                    </h3>
                    <p>Daftar pelanggaran siswa</p>
                </div>

                <span class="count-badge danger">{{ $pelanggarans->count() }}</span>
            </div>

            @if($pelanggarans->isNotEmpty())

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pelanggaran</th>
                                <th>Kategori</th>
                                <th>Poin</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($pelanggarans->sortByDesc('tanggal') as $pelanggaran)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="nowrap">
                                        {{ $pelanggaran->tanggal
                                            ? \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y')
                                            : '-' }}
                                    </td>

                                    <td>
                                        <div class="item-name violation-name">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            <span>{{ $pelanggaran->jenis_pelanggaran }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        @if($pelanggaran->kategori)
                                            <span class="badge category">{{ $pelanggaran->kategori }}</span>
                                        @else
                                            <span class="muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="point-badge negative">
                                            <i class="fa-solid fa-minus"></i>
                                            {{ $pelanggaran->poin }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else

                <div class="empty-state">
                    <div class="empty-icon success">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h4>Tidak ada pelanggaran</h4>
                    <p>Siswa ini belum memiliki riwayat pelanggaran.</p>
                </div>

            @endif

        </div>


        {{-- ================= PRESTASI ================= --}}
        <div class="table-card">

            <div class="card-header">
                <div>
                    <h3>
                        <i class="fa-solid fa-trophy"></i>
                        Riwayat Prestasi
                    </h3>
                    <p>Daftar prestasi siswa</p>
                </div>

                <span class="count-badge success">{{ $prestasis->count() }}</span>
            </div>

            @if($prestasis->isNotEmpty())

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Prestasi</th>
                                <th>Tingkat</th>
                                <th>Poin</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($prestasis->sortByDesc('tanggal') as $prestasi)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="nowrap">
                                        {{ $prestasi->tanggal
                                            ? \Carbon\Carbon::parse($prestasi->tanggal)->format('d/m/Y')
                                            : '-' }}
                                    </td>

                                    <td>
                                        <div class="item-name achievement-name">
                                            <i class="fa-solid fa-trophy"></i>
                                            <span>{{ $prestasi->jenis_prestasi }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        @if($prestasi->tingkat)
                                            <span class="badge achievement">{{ $prestasi->tingkat }}</span>
                                        @else
                                            <span class="muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="point-badge positive">
                                            <i class="fa-solid fa-plus"></i>
                                            {{ $prestasi->poin }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else

                <div class="empty-state">
                    <div class="empty-icon neutral">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <h4>Belum ada prestasi</h4>
                    <p>Siswa ini belum memiliki data prestasi.</p>
                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
         RINGKASAN PERHITUNGAN
    ========================================================= --}}
    <div class="calculation-card">

        <div class="calculation-header">
            <h3>
                <i class="fa-solid fa-calculator"></i>
                Perhitungan Saldo Poin
            </h3>
            <p>Ringkasan perhitungan poin siswa</p>
        </div>

        <div class="calculation">

            <div class="calc-item">
                <span>Poin Awal</span>
                <strong>100</strong>
            </div>

            <div class="calc-symbol negative"><i class="fa-solid fa-minus"></i></div>

            <div class="calc-item">
                <span>Pelanggaran</span>
                <strong class="text-danger">{{ $totalPelanggaran }}</strong>
            </div>

            <div class="calc-symbol"><i class="fa-solid fa-plus"></i></div>

            <div class="calc-item">
                <span>Prestasi</span>
                <strong class="text-success">{{ $totalPrestasi }}</strong>
            </div>

            <div class="calc-symbol"><i class="fa-solid fa-equals"></i></div>

            <div class="calc-item result">
                <span>Saldo Akhir</span>
                <strong class="{{ $saldoClass }}">{{ $saldoPoin }}</strong>
            </div>

        </div>

    </div>

</div>{{-- /.siswa-detail --}}

@endsection


@section('styles')

@include('partials.ui')

/* =====================================================
   FIX GLOBAL: hilangkan underline di sidebar, breadcrumb,
   dan semua link. (Idealnya dipindah ke layouts/app.blade.php
   supaya berlaku di semua halaman.)
===================================================== */

a,
a:hover,
a:focus,
a:active{
    text-decoration:none !important;
}

/* =====================================================
   DETAIL SISWA (di-scope ke .siswa-detail)
===================================================== */

.siswa-detail{
    width:100%;
    max-width:100%;
}

.siswa-detail a,
.siswa-detail a:hover,
.siswa-detail .btn,
.siswa-detail .btn:hover{
    text-decoration:none !important;
}

.siswa-detail img{
    max-width:100%;
    height:auto;
}

.siswa-detail .nowrap{ white-space:nowrap; }
.siswa-detail .muted{ color:#9ca3af; }


/* ---------- HEADING ---------- */

.siswa-detail .sd-heading{
    display:flex;
    flex-direction:row;
    align-items:center;
    justify-content:space-between;
    text-align:left;
    gap:20px;
    flex-wrap:wrap;
    margin-bottom:24px;
}

.siswa-detail .sd-heading > div:first-child{
    min-width:0;
}

.siswa-detail .sd-heading h1{
    margin:0;
    font-size:28px;
    font-weight:800;
    color:#111827;
    letter-spacing:-.3px;
}

.siswa-detail .sd-heading h1 i{
    color:var(--primary);
    margin-right:8px;
}

.siswa-detail .sd-heading p{
    margin:4px 0 0;
    font-size:14px;
    color:#6b7280;
}

.siswa-detail .sd-actions{
    display:flex;
    justify-content:flex-end;
    gap:10px;
    flex-wrap:wrap;
}

.siswa-detail .sd-actions .btn{
    min-height:40px;
}


/* ---------- PROFIL ---------- */

.siswa-detail .student-profile{
    display:flex;
    align-items:center;
    gap:18px;
    flex-wrap:wrap;

    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;

    padding:22px;
    margin-bottom:20px;

    box-shadow:0 4px 12px rgba(0,0,0,.03);
}

.siswa-detail .profile-icon{
    flex-shrink:0;

    display:flex;
    align-items:center;
    justify-content:center;

    width:64px;
    height:64px;
    min-width:64px;

    border-radius:16px;

    background:var(--primary);
    color:#fff;

    font-size:26px;
}

.siswa-detail .profile-info{
    min-width:0;
    flex:1 1 240px;
}

.siswa-detail .profile-info h2{
    margin:0 0 10px;
    font-size:clamp(1.05rem, .9rem + .9vw, 1.375rem);
    font-weight:800;
    color:#1f2937;
    overflow-wrap:anywhere;
}

.siswa-detail .profile-meta{
    display:flex;
    gap:18px;
    flex-wrap:wrap;
    color:#6b7280;
    font-size:13px;
}

.siswa-detail .profile-meta span{
    display:flex;
    align-items:center;
    gap:7px;
    min-width:0;
    overflow-wrap:anywhere;
}

.siswa-detail .profile-meta i{
    flex-shrink:0;
    color:var(--primary);
}


/* ---------- STATISTIK ---------- */

.siswa-detail .stats-grid{
    display:grid;
    grid-template-columns:repeat(4, minmax(0, 1fr));
    gap:16px;
    margin-bottom:20px;
}

.siswa-detail .stat-card{
    display:flex;
    align-items:center;
    gap:14px;
    min-width:0;

    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:15px;

    padding:18px;

    box-shadow:0 4px 12px rgba(0,0,0,.03);
}

.siswa-detail .stat-icon{
    flex-shrink:0;

    display:flex;
    align-items:center;
    justify-content:center;

    width:48px;
    height:48px;
    min-width:48px;

    border-radius:12px;
    font-size:19px;
}

.siswa-detail .stat-icon.neutral{ background:#f3f4f6; color:#374151; }
.siswa-detail .stat-icon.danger { background:#fef2f2; color:#b91c1c; }
.siswa-detail .stat-icon.success{ background:#ecfdf5; color:#047857; }
.siswa-detail .stat-icon.saldo  { background:#fdf2f8; color:var(--primary); }

.siswa-detail .stat-content{
    min-width:0;
    flex:1;
}

.siswa-detail .stat-label{
    display:block;
    margin-bottom:2px;
    color:#6b7280;
    font-size:12px;
}

.siswa-detail .stat-content strong{
    display:block;
    font-size:22px;
    font-weight:800;
    line-height:1.1;
    color:#1f2937;
}


/* ---------- RIWAYAT ---------- */

.siswa-detail .history-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:20px;
    margin-bottom:20px;
}

.siswa-detail .table-card{
    min-width:0;

    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;

    overflow:hidden;
    box-shadow:0 4px 12px rgba(0,0,0,.03);
}

.siswa-detail .card-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;

    padding:18px 20px;
    border-bottom:1px solid #e5e7eb;
}

.siswa-detail .card-header > div:first-child{
    min-width:0;
}

.siswa-detail .card-header h3{
    margin:0 0 4px;
    font-size:16px;
    color:#1f2937;
}

.siswa-detail .card-header h3 i{
    color:var(--primary);
    margin-right:6px;
}

.siswa-detail .card-header p{
    margin:0;
    color:#9ca3af;
    font-size:12px;
}


/* ---------- TABEL ---------- */

.siswa-detail .table-wrapper{
    width:100%;
    max-width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}

.siswa-detail table{
    width:100%;
    min-width:460px;
    border-collapse:collapse;
}

.siswa-detail th{
    background:#faf7f6;
    color:#6b7280;

    font-size:11px;
    font-weight:700;
    letter-spacing:.5px;
    text-transform:uppercase;
    text-align:left;

    padding:12px 14px;

    border-bottom:1px solid #eee7e5;
    white-space:nowrap;
}

.siswa-detail td{
    padding:13px 14px;
    border-bottom:1px solid #f3eeec;

    font-size:13px;
    color:#374151;
    vertical-align:middle;
}

.siswa-detail tbody tr:last-child td{
    border-bottom:none;
}

.siswa-detail tbody tr:hover{
    background:#fffaf9;
}

.siswa-detail .item-name{
    display:flex;
    align-items:center;
    gap:8px;
    min-width:0;

    font-weight:600;
    color:#374151;
}

.siswa-detail .item-name i{ flex-shrink:0; }
.siswa-detail .item-name span{ min-width:0; overflow-wrap:anywhere; }

.siswa-detail .violation-name i{ color:#b91c1c; }
.siswa-detail .achievement-name i{ color:var(--primary); }


/* ---------- BADGE ---------- */

.siswa-detail .badge{
    display:inline-flex;
    align-items:center;
    padding:5px 9px;
    border-radius:7px;
    font-size:11px;
    font-weight:700;
}

.siswa-detail .badge.category   { background:#f3f4f6; color:#4b5563; }
.siswa-detail .badge.achievement{ background:#ecfdf5; color:#047857; }

.siswa-detail .point-badge{
    display:inline-flex;
    align-items:center;
    gap:3px;
    padding:5px 8px;
    border-radius:7px;
    font-size:12px;
    font-weight:800;
}

.siswa-detail .point-badge.negative{ background:#fef2f2; color:#b91c1c; }
.siswa-detail .point-badge.positive{ background:#ecfdf5; color:#047857; }

.siswa-detail .count-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;

    min-width:28px;
    height:28px;
    padding:0 8px;

    border-radius:8px;
    font-size:12px;
    font-weight:800;
}

.siswa-detail .count-badge.danger { background:#fef2f2; color:#b91c1c; }
.siswa-detail .count-badge.success{ background:#ecfdf5; color:#047857; }


/* ---------- EMPTY STATE ---------- */

.siswa-detail .empty-state{
    text-align:center;
    padding:40px 20px;
}

.siswa-detail .empty-icon{
    display:inline-flex;
    align-items:center;
    justify-content:center;

    width:52px;
    height:52px;
    margin-bottom:12px;

    border-radius:50%;
    font-size:22px;
}

.siswa-detail .empty-icon.success{ background:#ecfdf5; color:#047857; }
.siswa-detail .empty-icon.neutral{ background:#f3f4f6; color:#6b7280; }

.siswa-detail .empty-state h4{
    margin:0 0 5px;
    font-size:14px;
    color:#374151;
}

.siswa-detail .empty-state p{
    margin:0;
    color:#9ca3af;
    font-size:12px;
}


/* ---------- PERHITUNGAN ---------- */

.siswa-detail .calculation-card{
    width:100%;
    max-width:100%;

    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;

    overflow:hidden;
    box-shadow:0 4px 12px rgba(0,0,0,.03);
}

.siswa-detail .calculation-header{
    padding:18px 20px;
    border-bottom:1px solid #e5e7eb;
}

.siswa-detail .calculation-header h3{
    margin:0 0 4px;
    font-size:16px;
    color:#1f2937;
    overflow-wrap:anywhere;
}

.siswa-detail .calculation-header h3 i{
    color:var(--primary);
    margin-right:6px;
}

.siswa-detail .calculation-header p{
    margin:0;
    color:#9ca3af;
    font-size:12px;
}

.siswa-detail .calculation{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:20px;
    flex-wrap:wrap;
    padding:22px;
}

.siswa-detail .calc-item{
    min-width:110px;
    max-width:100%;
    text-align:center;
}

.siswa-detail .calc-item span{
    display:block;
    margin-bottom:5px;
    color:#6b7280;
    font-size:11px;
    overflow-wrap:anywhere;
}

.siswa-detail .calc-item strong{
    display:block;
    font-size:22px;
    font-weight:800;
    color:#1f2937;
}

.siswa-detail .calc-item.result{
    background:#fafafa;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:12px 18px;
}

.siswa-detail .calc-symbol{
    color:#9ca3af;
    font-size:14px;
}

.siswa-detail .calc-symbol.negative{
    color:#b91c1c;
}


/* ---------- WARNA TEKS ---------- */

.siswa-detail .text-danger { color:#b91c1c !important; }
.siswa-detail .text-success{ color:#047857 !important; }
.siswa-detail .text-warning{ color:#b45309 !important; }


/* =====================================================
   RESPONSIVE
===================================================== */

@media(max-width:1200px){

    .siswa-detail .stats-grid{
        grid-template-columns:repeat(2, minmax(0, 1fr));
    }

}

@media(max-width:1024px){

    .siswa-detail .history-grid{
        grid-template-columns:minmax(0, 1fr);
    }

}

@media(max-width:900px){

    .siswa-detail .sd-heading{
        flex-direction:column;
        align-items:flex-start;
    }

    .siswa-detail .sd-actions{
        justify-content:flex-start;
    }

    .siswa-detail .sd-heading h1{
        font-size:22px;
    }

    .siswa-detail .sd-actions{
        width:100%;
    }

    .siswa-detail .sd-actions .btn{
        flex:1 1 140px;
        justify-content:center;
    }

    .siswa-detail .student-profile{
        padding:18px;
        gap:14px;
    }

    .siswa-detail .profile-icon{
        width:55px;
        height:55px;
        min-width:55px;
        font-size:22px;
    }

    .siswa-detail .profile-meta{
        flex-direction:column;
        gap:7px;
    }

    .siswa-detail .stats-grid{ gap:10px; }

    .siswa-detail .stat-card{
        padding:14px;
        gap:10px;
    }

    .siswa-detail .stat-icon{
        width:40px;
        height:40px;
        min-width:40px;
        font-size:16px;
    }

    .siswa-detail .stat-content strong{ font-size:18px; }

    .siswa-detail .history-grid{ gap:14px; }

    .siswa-detail .card-header{ padding:15px; }

    .siswa-detail th,
    .siswa-detail td{
        padding:11px 10px;
    }

    .siswa-detail table{ min-width:440px; }

    .siswa-detail .calculation{
        gap:10px;
        padding:18px 12px;
    }

    .siswa-detail .calc-item{ min-width:85px; }
    .siswa-detail .calc-item strong{ font-size:18px; }
    .siswa-detail .calc-symbol{ font-size:11px; }

}

@media(max-width:768px){

    .siswa-detail .calculation-card,
    .siswa-detail .table-card,
    .siswa-detail .student-profile{
        border-radius:13px;
    }

    .siswa-detail table{ min-width:400px; }

}

@media(max-width:576px){

    .siswa-detail .sd-heading{ margin-bottom:18px; }

    .siswa-detail .sd-actions{ flex-direction:column; }

    .siswa-detail .sd-actions .btn{ width:100%; }

    .siswa-detail .student-profile{
        align-items:flex-start;
        flex-wrap:nowrap;
    }

    .siswa-detail .empty-state{ padding:32px 16px; }

    .siswa-detail table{ min-width:360px; }

    .siswa-detail th,
    .siswa-detail td{
        padding:10px 8px;
    }

    .siswa-detail .calculation{
        gap:8px 6px;
        padding:16px 10px;
    }

    .siswa-detail .calc-item{ min-width:0; }

    .siswa-detail .calc-item.result{ padding:10px 8px; }

}

@media(max-width:480px){

    .siswa-detail .stats-grid{
        grid-template-columns:minmax(0, 1fr);
    }

    .siswa-detail .stat-card{ padding:13px; }

    .siswa-detail .empty-state{ padding:28px 14px; }

    .siswa-detail .empty-icon{
        width:44px;
        height:44px;
        font-size:18px;
    }

    .siswa-detail .card-header h3{ font-size:15px; }

    .siswa-detail table{ min-width:330px; }

    .siswa-detail th{
        font-size:10px;
        padding:9px 7px;
    }

    .siswa-detail td{
        font-size:12px;
        padding:9px 7px;
    }

    .siswa-detail .calculation{ padding:14px 8px; }
    .siswa-detail .calc-item strong{ font-size:16px; }

}

@media(max-width:400px){

    .siswa-detail .student-profile{
        padding:14px;
        gap:11px;
    }

    .siswa-detail .profile-icon{
        width:46px;
        height:46px;
        min-width:46px;
        font-size:19px;
        border-radius:13px;
    }

    .siswa-detail .stat-card{ padding:11px; }

    .siswa-detail .badge,
    .siswa-detail .point-badge,
    .siswa-detail .count-badge{
        font-size:10px;
    }

    .siswa-detail table{ min-width:300px; }

    .siswa-detail th{
        font-size:9px;
        letter-spacing:.02em;
        padding:8px 6px;
    }

    .siswa-detail td{ padding:8px 6px; }

    .siswa-detail .calculation{ padding:12px 6px; }

}
@endsection
