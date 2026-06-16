@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

<div class="dashboard">

    {{-- Hero --}}
    <div class="hero-card">

        <div>
            <span class="hero-badge">
                Sistem Monitoring
            </span>

            <h1>Dashboard Veritas</h1>

            <p>
                Kelola data siswa, pantau pelanggaran,
                dan analisis kedisiplinan secara realtime.
            </p>
        </div>

        <div class="hero-stats">

            <div>
                <strong>{{ $totalSiswa }}</strong>
                <span>Siswa</span>
            </div>

            <div>
                <strong>{{ $pelanggaranHariIni }}</strong>
                <span>Hari Ini</span>
            </div>

        </div>

    </div>

    {{-- Statistik --}}
    <div class="stats-grid">

        <div class="stat-card">
            <span>Total Siswa</span>
            <h2>{{ $totalSiswa }}</h2>
            <small>Siswa terdaftar</small>
        </div>

        <div class="stat-card danger">
            <span>Pelanggaran Hari Ini</span>
            <h2>{{ $pelanggaranHariIni }}</h2>
            <small>Kasus tercatat hari ini</small>
        </div>

        <div class="stat-card">
            <span>Total Poin</span>
            <h2>{{ $totalPoin }}</h2>
            <small>Akumulasi poin</small>
        </div>

    </div>

    <div class="dashboard-grid">

        {{-- Tabel --}}
        <div class="table-card">

            <div class="card-header">
                <h3>Siswa Melanggar Hari Ini</h3>
            </div>
        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Pelanggaran</th>
                        <th>Poin</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pelanggaranHariIniList as $pelanggaran)

                    <tr>

                        <td>{{ $pelanggaran->siswa->nama }}</td>

                        <td>{{ $pelanggaran->siswa->kelas }}</td>

                        <td>
                            <span class="badge">
                                {{ $pelanggaran->jenis_pelanggaran }}
                            </span>
                        </td>

                        <td>{{ $pelanggaran->poin }}</td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="4" class="empty">
                            Tidak ada pelanggaran hari ini
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        </div>

        {{-- Aktivitas --}}
        <div class="activity-card">

            <div class="card-header">
                <h3>Aktivitas Terbaru</h3>
            </div>

            @forelse($latestPelanggaran as $pelanggaran)

            <div class="activity-item">

                <div class="activity-dot"></div>

                <div>

                    <strong>
                        {{ $pelanggaran->siswa->nama }}
                    </strong>

                    <p>
                        {{ $pelanggaran->jenis_pelanggaran }}
                    </p>

                    <small>
                        {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}
                    </small>

                </div>

            </div>

            @empty

            <p>Tidak ada aktivitas.</p>

            @endforelse

        </div>

    </div>

</div>

<style>

.hero-card{
    background:linear-gradient(
        135deg,
        #1F2937,
        #111827
    );

    color:white;

    border-radius:24px;

    padding:35px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:25px;
}

.hero-badge{
    display:inline-block;

    background:rgba(255,255,255,.1);

    padding:8px 14px;

    border-radius:999px;

    margin-bottom:15px;

    font-size:13px;
}

.hero-card h1{
    font-size:38px;
    margin-bottom:10px;
}

.hero-card p{
    opacity:.85;
    max-width:600px;
}

.hero-icon{
    font-size:80px;
}

.stats-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    margin-bottom:25px;
}

.stat-card{
    background:white;

    border-radius:20px;

    padding:25px;

    border:1px solid #eee;

    transition:.2s;
}

.stat-card:hover{
    transform:translateY(-4px);
}

.stat-card span{
    color:#666;
    font-size:14px;
}

.stat-card h2{
    font-size:42px;
    color:#1F2937;
    margin:12px 0;
}

.stat-card small{
    color:#999;
}

.stat-card.danger h2{
    color:#6D1408;
}

.dashboard-grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:25px;
}

.table-card,
.activity-card{
    background:white;

    border-radius:20px;

    overflow:hidden;

    border:1px solid #eee;
}

.card-header{
    padding:20px 25px;

    border-bottom:1px solid #eee;
}

.card-header h3{
    font-size:18px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#F9FAFB;
    text-align:left;
    padding:15px;
}

td{
    padding:15px;
    border-bottom:1px solid #eee;
}

.badge{
    background:#FEE2E2;

    color:#991B1B;

    padding:6px 12px;

    border-radius:999px;

    font-size:13px;
    font-weight:600;
}

.activity-card{
    padding-bottom:20px;
}

.activity-item{
    display:flex;
    gap:15px;
    padding:20px;
}

.activity-dot{
    width:12px;
    height:12px;
    border-radius:50%;
    background:#6D1408;
    margin-top:6px;
}

.activity-item p{
    margin-top:5px;
    color:#666;
}

.activity-item small{
    color:#999;
}

.empty{
    text-align:center;
    color:#999;
}

@media(max-width:1000px){

    .stats-grid{
        grid-template-columns:1fr;
    }

    .dashboard-grid{
        grid-template-columns:1fr;
    }

    .hero-card{
        flex-direction:column;
        align-items:flex-start;
        gap:20px;
    }

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

@media(max-width:768px){

    .hero-card{
        flex-direction:column;
        align-items:flex-start;
        gap:20px;
    }

    .hero-card h1{
        font-size:28px;
    }

    .hero-stats{
        width:100%;
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:10px;
    }

    .stats-grid{
        grid-template-columns:1fr;
    }

    .dashboard-grid{
        grid-template-columns:1fr;
    }

}
</style>

@endsection
