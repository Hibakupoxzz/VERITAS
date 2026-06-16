@extends('layouts.app')

@section('title', 'Detail Pelanggaran')
@section('page_title', 'Detail Pelanggaran')

@section('styles')

<style>
.pv-body{
    background:#f5f6fa;
    min-height:100vh;
    padding:2rem 1rem;
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
    background:#eff6ff;
    display:flex;
    align-items:center;
    justify-content:center;
}

.pv-page-icon svg{
    width:22px;
    height:22px;
    stroke:#2563eb;
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
}

.pv-card{
    background:#fff;
    border:1px solid #e8eaed;
    border-radius:14px;
    padding:1.5rem;
    margin-bottom:1rem;
}

.pv-section-label{
    font-size:.7rem;
    font-weight:600;
    letter-spacing:.06em;
    text-transform:uppercase;
    color:#9ca3af;
    margin-bottom:1rem;
}

.pv-grid{
    display:grid;
    grid-template-columns:180px 1fr;
    gap:12px;
}

.pv-item{
    padding:12px 0;
    border-bottom:1px solid #f1f5f9;
}

.pv-item:last-child{
    border-bottom:none;
}

.pv-key{
    color:#6b7280;
    font-size:.85rem;
    font-weight:500;
}

.pv-value{
    color:#111827;
    font-size:.9rem;
    font-weight:600;
}

.pv-badge{
    display:inline-flex;
    align-items:center;
    background:#dbeafe;
    color:#1d4ed8;
    padding:6px 12px;
    border-radius:999px;
    font-size:.8rem;
    font-weight:600;
}

.pv-img{
    width:100%;
    max-width:450px;
    border-radius:12px;
    border:1px solid #e5e7eb;
}

.pv-footer{
    display:flex;
    justify-content:flex-end;
    margin-top:1rem;
}

.pv-btn{
    background:#2563eb;
    color:#fff;
    text-decoration:none;
    border:none;
    padding:10px 22px;
    border-radius:10px;
    font-size:.875rem;
    font-weight:600;
}

.pv-btn:hover{
    background:#1d4ed8;
}
</style>

@endsection

@section('content')

<div class="pv-body">

<div class="container" style="max-width:800px">


<div class="pv-page-header">
    <div class="pv-page-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
            <path d="M9 11l3 3L22 4"/>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
        </svg>
    </div>

    <div>
        <h1 class="pv-page-title">Detail Pelanggaran</h1>
        <p class="pv-page-sub">
            Informasi lengkap data pelanggaran siswa
        </p>
    </div>
</div>

<div class="pv-card">

    <div class="pv-section-label">
        Data Pelanggaran
    </div>

    <div class="pv-item pv-grid">
        <div class="pv-key">Nama Siswa</div>
        <div class="pv-value">
            {{ $pelanggaran->siswa->nama }}
        </div>
    </div>

    <div class="pv-item pv-grid">
        <div class="pv-key">NISN</div>
        <div class="pv-value">
            {{ $pelanggaran->siswa->nisn }}
        </div>
    </div>

    <div class="pv-item pv-grid">
        <div class="pv-key">Kelas</div>
        <div class="pv-value">
            {{ $pelanggaran->siswa->kelas }}
        </div>
    </div>

    <div class="pv-item pv-grid">
        <div class="pv-key">Tanggal</div>
        <div class="pv-value">
            {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d M Y') }}
        </div>
    </div>

    <div class="pv-item pv-grid">
        <div class="pv-key">Jenis Pelanggaran</div>
        <div class="pv-value">
            {{ $pelanggaran->jenis_pelanggaran }}
        </div>
    </div>

    <div class="pv-item pv-grid">
        <div class="pv-key">Poin</div>
        <div class="pv-value">
            <span class="pv-badge">
                -{{ $pelanggaran->poin }} Poin
            </span>
        </div>
    </div>

    <div class="pv-item pv-grid">
        <div class="pv-key">Keterangan</div>
        <div class="pv-value">
            {{ $pelanggaran->keterangan ?: '-' }}
        </div>
    </div>

</div>

@if($pelanggaran->foto_bukti)
<div class="pv-card">

    <div class="pv-section-label">
        Foto Bukti
    </div>

    <img
        src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
        alt="Foto Bukti"
        class="pv-img">

</div>
@endif

<div class="pv-footer">
    <a href="{{ route('pelanggaran.index') }}" class="pv-btn">
        Kembali
    </a>
</div>


</div>
</div>
@endsection
