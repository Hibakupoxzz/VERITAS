@extends('layouts.app')

@section('page_title', 'Data Siswa')

@section('styles')
<style>
    *, *::before, *::after { box-sizing: border-box; }

    .pv-body {
        background: #f5f6fa;
        min-height: 100vh;
        /* padding: 2rem 1rem; */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* ── Page header ── */
    .pv-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        gap: 12px;
        flex-wrap: wrap;
    }
    .pv-page-left { display: flex; align-items: center; gap: 14px; }

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
    .pv-page-title { font-size: 1.25rem; font-weight: 600; color: #1a1a2e; margin: 0; }
    .pv-page-sub   { font-size: 0.8rem; color: #6b7280; margin: 2px 0 0; }

    .pv-btn-add {
        background:var(--color-secondary-red);
        color:white;
        text-decoration:none;
        padding:12px 20px;
        border-radius:12px;
        font-weight:600;
        transition:.2s;
    }
    .pv-btn-add:hover {background:#581006; color: #fff; }
    .pv-btn-add svg { width: 16px; height: 16px; stroke: #fff; }

    /* ── Alert ── */
    .pv-alert-success {
        background: #f0fdf4; border: 1px solid #bbf7d0;
        border-left: 4px solid #16a34a; border-radius: 10px;
        padding: 12px 16px; margin-bottom: 1.25rem;
        font-size: 0.875rem; color: #15803d;
        display: flex; align-items: center; gap: 8px;
    }
    .pv-alert-success svg { width: 16px; height: 16px; stroke: #16a34a; flex-shrink: 0; }

    /* ── Stats ── */
    .pv-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px; margin-bottom: 1.25rem;
    }
    .pv-stat-card {
        background: #fff; border: 1px solid #e8eaed;
        border-radius: 12px; padding: 1rem 1.25rem;
        display: flex; align-items: center; gap: 12px;
        transition:.25s;
        box-shadow:0 4px 15px rgba(0,0,0,.05);
    }
    .pv-stat-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .pv-stat-icon svg { width: 18px; height: 18px; }
    .pv-stat-icon.blue  { background:#FBEAE8; } .pv-stat-icon.blue  svg { stroke:#6D1408; }
    .pv-stat-icon.green { background: #f0fdf4; } .pv-stat-icon.green svg { stroke: #16a34a; }
    .pv-stat-icon.red   { background: #fff1f1; } .pv-stat-icon.red   svg { stroke: #e53e3e; }
    .pv-stat-val { font-size: 1.35rem; font-weight: 700; color: #1a1a2e; line-height: 1; }
    .pv-stat-lbl { font-size: 0.72rem; color: #6b7280; margin-top: 3px; }

    /* ── Toolbar ── */
    .pv-toolbar {
        background: #fff; border: 1px solid #e8eaed;
        border-radius: 12px; padding: 14px 1.25rem;
        margin-bottom: 1rem;
        display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
    }
    .pv-search-wrap { position: relative; flex: 1; min-width: 200px; }
    .pv-search-wrap svg {
        position: absolute; left: 10px; top: 50%;
        transform: translateY(-50%);
        width: 15px; height: 15px; stroke: #9ca3af; pointer-events: none;
    }
    .pv-search-wrap input {
        width: 100%; padding: 8px 12px 8px 34px;
        border: 1px solid #e2e5ea; border-radius: 8px;
        font-size: 0.85rem; outline: none;
        background: #fafbfc; transition: border-color .15s, box-shadow .15s;
        font-family: inherit;
    }
    .pv-search-wrap input:focus{
        border-color:#6D1408;
        box-shadow:0 0 0 3px rgba(109,20,8,.15);
    }
    .pv-filter-select {
        padding: 8px 10px; border: 1px solid #e2e5ea;
        border-radius: 8px; font-size: 0.85rem;
        background: #fafbfc; outline: none;
        cursor: pointer; font-family: inherit; color: #374151;
    }
    .pv-filter-select:focus { border-color: #2563eb; }

    /* ── Table ── */
    .pv-table-card{
        background:white;
        border:1px solid #e5e7eb;
        border-radius:20px;
        box-shadow:0 10px 30px rgba(0,0,0,.05);
    }
    .pv-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .pv-table thead tr {
        background:#1F2937; border-bottom: 1px solid #e8eaed;
    }
    .pv-table th {
        padding: 11px 14px; text-align: left;
        font-size: 0.72rem; font-weight: 600;
        letter-spacing: 0.05em; text-transform: uppercase;
        color: white; white-space: nowrap;
    }
    .pv-table tbody tr { border-bottom: 1px solid #f3f4f6; transition: background .1s; }
    .pv-table tbody tr:last-child { border-bottom: none; }
    .pv-table tbody tr:hover { background: #fafbfc; }
    .pv-table td { padding: 12px 14px; color: #374151; vertical-align: middle; }

    /* ── Siswa cell ── */
    .pv-siswa-cell { display: flex; align-items: center; gap: 10px; }
    .pv-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background:#6D1408;
        color:white;
        font-size: 0.72rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .pv-siswa-name  { font-weight: 500; color: #1a1a2e; font-size: 0.875rem; }
    .pv-siswa-nisn  { font-size: 0.72rem; color: #6b7280; margin-top: 2px; }

    /* ── Kelas badge ── */
    .pv-kelas-badge {
        background:#FBEAE8;
        color:#6D1408;
        border:1px solid #E8C2BD;
        font-size: 0.75rem; font-weight: 600;
        padding: 4px 10px; border-radius: 20px;
        display: inline-block; white-space: nowrap;
    }

    /* ── Poin total badge ── */
    .pv-poin-badge {
        font-size: 0.78rem; font-weight: 700;
        padding: 4px 10px; border-radius: 20px;
        display: inline-block; white-space: nowrap;
    }
    .pv-poin-badge.aman   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .pv-poin-badge.sedang { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .pv-poin-badge.tinggi { background: #fff1f1; color: #e53e3e; border: 1px solid #fecaca; }

    /* ── Action buttons ── */
    .pv-action-wrap{
        display:flex;
        gap:8px;
        align-items:center;
        flex-wrap:nowrap;
    }

    .pv-btn-edit,
    .pv-btn-delete{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:5px;

        padding:8px 14px;

        border-radius:8px;
        font-size:13px;
        font-weight:600;

        text-decoration:none;
        white-space:nowrap;

        cursor:pointer;
        transition:.2s;
    }
    .pv-btn-edit{
        background:#FEF3C7;
        color:#92400E;
        border:1px solid #FCD34D;
    }
    .pv-btn-edit:hover {background:#FDE68A; color:#6D1408; }
    .pv-btn-edit svg { width: 13px; height: 13px; stroke: currentColor; }

    .pv-btn-delete{
        background:#FEE2E2;
        color:#B91C1C;
        border:1px solid #FCA5A5;
    }
    .pv-btn-delete:hover { background:#FECACA; color: #c53030; }
    .pv-btn-delete svg { width: 13px; height: 13px; stroke: currentColor; }

    /* ── Empty state ── */
    .pv-empty { padding: 3rem 1.5rem; text-align: center; }
    .pv-empty svg { width: 48px; height: 48px; stroke: #d1d5db; margin-bottom: 12px; }
    .pv-empty p { color: #9ca3af; font-size: 0.9rem; }
    .pv-empty a {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 12px; font-size: 0.875rem; font-weight: 500;
        color: #2563eb; text-decoration: none;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .pv-stats { grid-template-columns: 1fr 1fr; }
        .pv-table th:nth-child(5),
        .pv-table td:nth-child(5) { display: none; }
    }
    @media (max-width: 576px) {
        .pv-stats { grid-template-columns: 1fr; }
        .pv-table-card { overflow-x: auto; }
        .pv-table { min-width: 520px; }
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
</style>
@endsection

@section('content')
<div class="pv-body">
<div class="container" style="max-width: 1100px;">

    {{-- Header --}}
    <div class="pv-page-header">
        <div class="pv-page-left">
            <div class="pv-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <div>
                <h1 class="pv-page-title">Data Siswa</h1>
                <p class="pv-page-sub">Kelola data siswa terdaftar</p>
            </div>
        </div>
        <a href="{{ route('siswa.create') }}" class="pv-btn-add">
        <i class="fa-solid fa-plus"> </i>⠀ Tambah Siswa
        </a>
    </div>

    {{-- Session success --}}
    @if(session('success'))
    <div class="pv-alert-success">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    @php
        $totalSiswa  = $siswas->count();
        $totalKelas  = $siswas->pluck('kelas')->unique()->count();
        $totalPoin   = $siswas->sum(fn($s) => $s->pelanggarans->sum('poin') ?? 0);
    @endphp
    <div class="pv-stats">
        <div class="pv-stat-card">
            <div class="pv-stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="pv-stat-val">{{ $totalSiswa }}</div>
                <div class="pv-stat-lbl">Total siswa</div>
            </div>
        </div>
        <div class="pv-stat-card">
            <div class="pv-stat-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div>
                <div class="pv-stat-val">{{ $totalKelas }}</div>
                <div class="pv-stat-lbl">Jumlah kelas</div>
            </div>
        </div>
        <div class="pv-stat-card">
            <div class="pv-stat-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <div class="pv-stat-val">{{ $totalPoin }}</div>
                <div class="pv-stat-lbl">Total poin pelanggaran</div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="pv-toolbar">
        <div class="pv-search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" id="filterSearch" placeholder="Cari nama atau NISN siswa...">
        </div>
        <select class="pv-filter-select" id="filterKelas">
            <option value="">Semua kelas</option>
            @foreach($siswas->pluck('kelas')->unique()->sort() as $kelas)
                <option value="{{ strtolower($kelas) }}">{{ $kelas }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="pv-table-card">
        <div class="table-responsive">

            <table class="pv-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NISN</th>
                        <th>Kelas</th>
                        <th>Total Poin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($siswas as $siswa)
                    @php
                $inits      = collect(explode(' ', $siswa->nama))->map(fn($w) => strtoupper($w[0]))->take(2)->join('');
                $totalPoinSiswa = $siswa->pelanggarans->sum('poin') ?? 0;
                $poinClass  = $totalPoinSiswa >= 20 ? 'tinggi' : ($totalPoinSiswa >= 10 ? 'sedang' : 'aman');
                @endphp
            <tr data-nama="{{ strtolower($siswa->nama) }}"
                data-nisn="{{ $siswa->nisn }}"
                data-kelas="{{ strtolower($siswa->kelas) }}">
                <td style="color:#9ca3af; font-size:.8rem;">{{ $loop->iteration }}</td>
                <td>
                    <div class="pv-siswa-cell">
                        <div class="pv-avatar">{{ $inits }}</div>
                        <div>
                            <div class="pv-siswa-name">{{ $siswa->nama }}</div>
                            <div class="pv-siswa-nisn">NISN: {{ $siswa->nisn }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:.85rem; color:#6b7280; font-family:monospace;">{{ $siswa->nisn }}</td>
                <td><span class="pv-kelas-badge">{{ $siswa->kelas }}</span></td>
                <td>
                    <span class="pv-poin-badge {{ $poinClass }}">
                        {{ $totalPoinSiswa }} poin
                    </span>
                </td>
                <td>
                    <div class="pv-action-wrap">
                        <a href="{{ route('siswa.edit', $siswa->id) }}" class="pv-btn-edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pv-btn-delete"
                            onclick="return confirm('Yakin ingin menghapus data siswa ini?')">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">
                <div class="pv-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    <p>Belum ada data siswa terdaftar.</p>
                    <a href="{{ route('siswa.create') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" style="width:15px;height:15px;stroke:#2563eb">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Tambah siswa pertama
                    </a>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
</div>

</div>
</div>

<script>
    const filterSearch = document.getElementById('filterSearch');
const filterKelas  = document.getElementById('filterKelas');
const rows = document.querySelectorAll('#tableBody tr[data-nama]');

function applyFilter() {
    const q     = filterSearch.value.toLowerCase();
    const kelas = filterKelas.value.toLowerCase();
    rows.forEach(row => {
        const matchQ     = row.dataset.nama.includes(q) || row.dataset.nisn.includes(q);
        const matchKelas = !kelas || row.dataset.kelas === kelas;
        row.style.display = matchQ && matchKelas ? '' : 'none';
    });
}
filterSearch.addEventListener('input', applyFilter);
filterKelas.addEventListener('change', applyFilter);
</script>
@endsection
