@extends('layouts.app')

@section('title', 'Data Prestasi')
@section('page_title', 'Data Prestasi')

@section('styles')
@include('partials.ui')

/* =========================================================
   DATA PRESTASI
   Primitif bersama (.pv-page-*) berasal dari partials/ui.blade.
========================================================= */

.prestasi-page,
.prestasi-page *,
.prestasi-page *::before,
.prestasi-page *::after {
    box-sizing: border-box;
}

.prestasi-page {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}


/* ── Header ── */
.prestasi-page .pv-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
}

.pv-page-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.pv-page-left > div:last-child {
    min-width: 0;
}

.pv-page-header h1,
.pv-page-header p {
    overflow-wrap: anywhere;
}

.pv-btn-add {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: background .2s, transform .2s;
}

.pv-btn-add:hover {
    background: var(--primary-dark, #5a1006);
    color: #fff;
    transform: translateY(-2px);
}


/* ── Tabel (desktop) ── */
.table-card {
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Sebelumnya min-width: min(1050px, 100%) sehingga tabel tidak pernah
   scroll dan kolomnya terhimpit. */
.table-responsive table {
    width: 100%;
    min-width: 960px;
    border-collapse: collapse;
}

.table-responsive thead {
    background: #1F2937;
}

.table-responsive th {
    padding: 14px;
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .04em;
    text-align: left;
    text-transform: uppercase;
    white-space: nowrap;
}

.table-responsive td {
    padding: 14px;
    border-bottom: 1px solid #f0f1f3;
    color: #374151;
    font-size: 13px;
    vertical-align: middle;
    overflow-wrap: anywhere;
}

.table-responsive tbody tr {
    transition: background .15s;
}

.table-responsive tbody tr:last-child td {
    border-bottom: none;
}

.table-responsive tbody tr:hover {
    background: #f9fafb;
}

.date-cell {
    white-space: nowrap;
}

.student-info,
.mobile-student {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.student-info {
    min-width: 120px;
}

.student-info strong,
.mobile-student strong {
    color: #111827;
    font-weight: 600;
    line-height: 1.4;
}

.student-info small,
.mobile-student small {
    margin-top: 2px;
    color: #6b7280;
    font-size: 11px;
}

.prestasi-wrapper {
    max-width: 340px;
}

.badge-prestasi {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
    padding: 7px 10px;
    border-radius: 999px;
    background: #fef3c7;
    color: #92400e;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.4;
    overflow-wrap: anywhere;
}

.badge-prestasi i {
    flex-shrink: 0;
    font-size: 10px;
}

.description {
    margin-top: 7px;
    color: #6b7280;
    font-size: 11px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.badge-tingkat {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: #ede9fe;
    color: #6d28d9;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.no-data,
.no-image {
    color: #9ca3af;
    font-size: 11px;
    white-space: nowrap;
}

.badge-point {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 999px;
    background: var(--c-point-bg);
    color: var(--c-point-text);
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

.table-image {
    display: block;
    width: 58px;
    height: 58px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    object-fit: cover;
    transition: transform .15s;
}

.table-image:hover {
    transform: scale(1.06);
}

.empty {
    padding: 48px 16px !important;
    color: #9ca3af;
    text-align: center;
}

.empty i {
    display: block;
    margin-bottom: 10px;
    font-size: 28px;
}


/* ── Tombol aksi ── */
.action-buttons {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    min-width: 180px;
}

.action-buttons form,
.mobile-actions form {
    margin: 0;
}

.btn-detail,
.btn-edit,
.btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 7px 10px;
    border: none;
    border-radius: 8px;
    font-family: inherit;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: transform .15s, background .15s;
}

.btn-detail { background: #dbeafe; color: #1d4ed8; }
.btn-edit   { background: #fef3c7; color: #92400e; }
.btn-delete { background: #fee2e2; color: #b91c1c; }

.btn-detail:hover,
.btn-edit:hover,
.btn-delete:hover {
    transform: translateY(-1px);
}


/* ── Daftar kartu (mobile) ──
   Disembunyikan di desktop. Sebelumnya aturan ini hilang sehingga
   data tampil dobel (tabel + kartu). */
.mobile-list {
    display: none;
}

.prestasi-card {
    width: 100%;
    overflow: hidden;
    padding: 13px;
    border: 1px solid #e5e7eb;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 5px 18px rgba(0, 0, 0, .05);
}

.mobile-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.mobile-date {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #9ca3af;
    font-size: 11px;
    white-space: nowrap;
}

.mobile-point {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    min-width: 42px;
    height: 32px;
    padding: 0 9px;
    border-radius: 999px;
    background: var(--c-point-bg);
    color: var(--c-point-text);
    font-size: 13px;
    font-weight: 700;
}

.mobile-student {
    margin-bottom: 10px;
}

.mobile-student strong {
    font-size: 13px;
    overflow-wrap: anywhere;
}

.mobile-prestasi {
    margin-bottom: 8px;
}

.mobile-prestasi .badge-prestasi {
    display: flex;
    width: 100%;
    padding: 8px 10px;
    border-radius: 10px;
    font-size: 11px;
}

.mobile-tingkat {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 8px;
    color: #6d28d9;
    font-size: 11px;
    font-weight: 600;
}

.mobile-description {
    margin-bottom: 10px;
    color: #6b7280;
    font-size: 11px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.mobile-photo {
    margin: 8px 0 12px;
}

.mobile-photo img {
    display: block;
    width: 64px;
    height: 64px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    object-fit: cover;
}

.mobile-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(110px, 100%), 1fr));
    gap: 6px;
    width: 100%;
    padding-top: 10px;
    border-top: 1px solid #f0f0f0;
}

.mobile-actions form {
    width: 100%;
}

.mobile-actions .btn-detail,
.mobile-actions .btn-edit,
.mobile-actions .btn-delete {
    width: 100%;
    min-height: 40px;
    padding: 7px 4px;
    font-size: 11px;
}

.mobile-empty {
    padding: 40px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 15px;
    background: #fff;
    color: #9ca3af;
    text-align: center;
}

.mobile-empty i {
    display: block;
    margin-bottom: 10px;
    font-size: 28px;
}


/* ── Responsive ── */
@media (max-width: 1024px) {
    .table-responsive table { min-width: 900px; }
    .table-responsive th,
    .table-responsive td { padding: 12px 10px; }
    .prestasi-wrapper { max-width: 260px; }
}

@media (max-width: 768px) {
    .desktop-table { display: none; }

    .mobile-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
    }

    .pv-btn-add {
        width: 100%;
        min-height: 44px;
    }
}

@media (max-width: 576px) {
    .pv-page-header { margin-bottom: 1rem; gap: 10px; }

    .pv-page-icon { width: 40px; height: 40px; flex-basis: 40px; }
    .pv-page-icon svg { width: 20px; height: 20px; }
    .pv-page-title { font-size: 1.1rem; }
    .pv-page-sub { font-size: .72rem; }
}

@media (max-width: 380px) {
    .mobile-actions { grid-template-columns: 1fr; }
    .pv-page-title { font-size: 1rem; }
}
@endsection


@section('content')

@php
    $user      = auth()->user();
    $isWalas   = $user->isWalas();
    $canManage = $user->canManagePrestasi();
    $kolom     = $isWalas ? 7 : 8;
@endphp

<div class="prestasi-page">

    {{-- HEADER --}}
    <div class="pv-page-header">

        <div class="pv-page-left">
            <div class="pv-page-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/>
                    <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                    <path d="M4 22h16"/>
                    <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
                    <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
                    <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
                </svg>
            </div>

            <div>
                @if($isWalas)
                    <h1 class="pv-page-title">Prestasi Kelas {{ $user->kelas }}</h1>
                    <p class="pv-page-sub">Daftar prestasi siswa di kelas Anda</p>
                @else
                    <h1 class="pv-page-title">Data Prestasi</h1>
                    <p class="pv-page-sub">Daftar seluruh prestasi siswa</p>
                @endif
            </div>
        </div>

        @if($canManage)
            <a href="{{ route('prestasi.create') }}" class="pv-btn-add">
                <i class="fa-solid fa-plus"></i>
                Tambah Prestasi
            </a>
        @endif

    </div>


    {{-- Catatan: pesan sukses (session 'success') sudah otomatis ditampilkan
         oleh layouts/app.blade.php, jadi tidak diulang di sini. --}}


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
                        @unless($isWalas)
                            <th>Aksi</th>
                        @endunless
                    </tr>
                </thead>

                <tbody>

                    @forelse($prestasis as $prestasi)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td class="date-cell">
                                {{ \Carbon\Carbon::parse($prestasi->tanggal)->format('d/m/Y') }}
                            </td>

                            <td>
                                <div class="student-info">
                                    <strong>{{ optional($prestasi->siswa)->nama ?? '-' }}</strong>
                                    <small>{{ optional($prestasi->siswa)->kelas ?? '-' }}</small>
                                </div>
                            </td>

                            <td>
                                <div class="prestasi-wrapper">
                                    <span class="badge-prestasi">
                                        <i class="fa-solid fa-trophy"></i>
                                        {{ $prestasi->jenis_prestasi }}
                                    </span>

                                    @if($prestasi->keterangan)
                                        <div class="description">{{ $prestasi->keterangan }}</div>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if($prestasi->tingkat)
                                    <span class="badge-tingkat">{{ $prestasi->tingkat }}</span>
                                @else
                                    <span class="no-data">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-point">+{{ $prestasi->poin }}</span>
                            </td>

                            <td>
                                @if($prestasi->bukti)
                                    <a href="{{ asset('storage/' . $prestasi->bukti) }}" target="_blank" rel="noopener">
                                        <img
                                            src="{{ asset('storage/' . $prestasi->bukti) }}"
                                            alt="Bukti Prestasi"
                                            class="table-image"
                                            loading="lazy">
                                    </a>
                                @else
                                    <span class="no-image">Tidak Ada</span>
                                @endif
                            </td>

                            @unless($isWalas)
                                <td>
                                    <div class="action-buttons">

                                        <a href="{{ route('prestasi.show', $prestasi->id) }}" class="btn-detail">
                                            <i class="fa-solid fa-eye"></i>
                                            Detail
                                        </a>

                                        @if($canManage)
                                            <a href="{{ route('prestasi.edit', $prestasi->id) }}" class="btn-edit">
                                                <i class="fa-solid fa-pen"></i>
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route('prestasi.destroy', $prestasi->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus data prestasi ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn-delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            @endunless

                        </tr>

                    @empty

                        <tr>
                            <td colspan="{{ $kolom }}" class="empty">
                                <i class="fa-solid fa-trophy"></i>
                                <span>Belum ada data prestasi</span>
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

                <div class="mobile-card-header">
                    <div class="mobile-date">
                        <i class="fa-regular fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($prestasi->tanggal)->format('d/m/Y') }}
                    </div>

                    <span class="mobile-point">+{{ $prestasi->poin }}</span>
                </div>

                <div class="mobile-student">
                    <strong>{{ optional($prestasi->siswa)->nama ?? '-' }}</strong>
                    <small>{{ optional($prestasi->siswa)->kelas ?? '-' }}</small>
                </div>

                <div class="mobile-prestasi">
                    <span class="badge-prestasi">
                        <i class="fa-solid fa-trophy"></i>
                        {{ $prestasi->jenis_prestasi }}
                    </span>
                </div>

                @if($prestasi->tingkat)
                    <div class="mobile-tingkat">
                        <i class="fa-solid fa-ranking-star"></i>
                        <span>{{ $prestasi->tingkat }}</span>
                    </div>
                @endif

                @if($prestasi->keterangan)
                    <div class="mobile-description">{{ $prestasi->keterangan }}</div>
                @endif

                @if($prestasi->bukti)
                    <div class="mobile-photo">
                        <a href="{{ asset('storage/' . $prestasi->bukti) }}" target="_blank" rel="noopener">
                            <img
                                src="{{ asset('storage/' . $prestasi->bukti) }}"
                                alt="Bukti Prestasi"
                                loading="lazy">
                        </a>
                    </div>
                @endif

                @unless($isWalas)
                    <div class="mobile-actions">

                        <a href="{{ route('prestasi.show', $prestasi->id) }}" class="btn-detail">
                            <i class="fa-solid fa-eye"></i>
                            Detail
                        </a>

                        @if($canManage)
                            <a href="{{ route('prestasi.edit', $prestasi->id) }}" class="btn-edit">
                                <i class="fa-solid fa-pen"></i>
                                Edit
                            </a>

                            <form
                                action="{{ route('prestasi.destroy', $prestasi->id) }}"
                                method="POST"
                                onsubmit="return confirm('Hapus data prestasi ini?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn-delete">
                                    <i class="fa-solid fa-trash"></i>
                                    Hapus
                                </button>
                            </form>
                        @endif

                    </div>
                @endunless

            </div>

        @empty

            <div class="mobile-empty">
                <i class="fa-solid fa-trophy"></i>
                <p>Belum ada data prestasi</p>
            </div>

        @endforelse

    </div>

</div>

@endsection
