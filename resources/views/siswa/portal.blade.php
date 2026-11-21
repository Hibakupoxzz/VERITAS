@extends('layouts.app')

@section('title', 'Portal Siswa')
@section('page_title', 'Beranda Saya')

@section('styles')
@include('partials.ui')

/* =========================================================
   PORTAL SISWA
========================================================= */

.portal-siswa {
    width: 100%;
}

.portal-siswa-hero {
    position: relative;
    overflow: hidden;
    padding: 26px 28px;
    margin-bottom: 22px;
    border-radius: var(--r-xl);
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: var(--c-point-text);
    box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
}

.portal-siswa-hero::after {
    content: '';
    position: absolute;
    top: -70px;
    right: -40px;
    width: 190px;
    height: 190px;
    border-radius: var(--r-full);
    background: rgba(255, 255, 255, .07);
}

.portal-siswa-hero-label {
    position: relative;
    z-index: 1;
    margin-bottom: 8px;
    font-size: var(--fs-xs);
    font-weight: var(--fw-bold);
    letter-spacing: .08em;
    opacity: .85;
}

.portal-siswa-hero-title {
    position: relative;
    z-index: 1;
    margin: 0;
    font-size: clamp(20px, 4vw, 28px);
    font-weight: var(--fw-bold);
    line-height: var(--lh-tight);
    overflow-wrap: anywhere;
}

.portal-siswa-hero-meta {
    position: relative;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    gap: var(--sp-4);
    margin: 14px 0 0;
}

.portal-siswa-chip {
    display: inline-flex;
    align-items: center;
    gap: var(--sp-3);
    padding: var(--sp-2) var(--sp-5);
    border-radius: var(--r-full);
    background: rgba(255, 255, 255, .14);
    font-size: var(--fs-sm);
    font-weight: var(--fw-semibold);
}

.portal-siswa-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(190px, 100%), 1fr));
    gap: var(--sp-7);
    margin-bottom: var(--sp-9);
}

.portal-siswa-stat {
    display: flex;
    align-items: center;
    gap: var(--sp-6);
    min-width: 0;
    padding: var(--sp-8);
    border: 1px solid var(--c-border);
    border-radius: var(--r-xl);
    background: var(--c-surface);
    box-shadow: 0 4px 12px rgba(0, 0, 0, .03);
}

.portal-siswa-stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 44px;
    width: 44px;
    height: 44px;
    border-radius: var(--r-lg);
    background: rgba(109, 20, 8, .08);
    color: var(--primary);
    font-size: var(--fs-lg);
}

.portal-siswa-stat-value {
    color: var(--c-text);
    font-size: var(--fs-xl);
    font-weight: var(--fw-bold);
    line-height: var(--lh-tight);
}

.portal-siswa-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(420px, 100%), 1fr));
    gap: var(--sp-8);
    align-items: start;
}

.portal-siswa-section {
    margin-bottom: 0;
}

.portal-siswa-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--sp-4);
    padding: var(--sp-7) var(--sp-8);
    border-bottom: 1px solid var(--c-border);
}

.portal-siswa-section-title {
    display: flex;
    align-items: center;
    gap: var(--sp-4);
    margin: 0;
    color: var(--c-text);
    font-size: var(--fs-md);
    font-weight: var(--fw-bold);
}

.portal-siswa-section-title i {
    color: var(--primary);
}

.portal-siswa-count {
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
}

.portal-siswa-note {
    margin: var(--sp-7) 0 0;
    color: var(--c-text-muted);
    font-size: var(--fs-sm);
    line-height: var(--lh-normal);
}

@media (max-width: 480px) {
    .portal-siswa-hero {
        padding: var(--sp-8) var(--sp-7);
    }
}
@endsection


@section('content')

<div class="portal-siswa">

    {{-- =====================================================
         SAMBUTAN
    ====================================================== --}}
    <div class="portal-siswa-hero">
        <div class="portal-siswa-hero-label">
            SISTEM INFORMASI KESISWAAN
        </div>

        <h1 class="portal-siswa-hero-title">
            Halo, {{ $siswa->nama }}!
        </h1>

        <p class="portal-siswa-hero-meta">
            <span class="portal-siswa-chip">
                <i class="fa-solid fa-school"></i>
                {{ $siswa->kelas }}
            </span>
            <span class="portal-siswa-chip">
                <i class="fa-solid fa-id-card"></i>
                NIS {{ $siswa->nisn }}
            </span>
        </p>
    </div>


    {{-- =====================================================
         RINGKASAN POIN
    ====================================================== --}}
    <div class="portal-siswa-stats">

        <div class="portal-siswa-stat">
            <div class="portal-siswa-stat-icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <span class="stat-label">Saldo Poin</span>
                <div class="portal-siswa-stat-value">{{ $saldoPoin }}</div>
            </div>
        </div>

        <div class="portal-siswa-stat">
            <div class="portal-siswa-stat-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="stat-label">Pelanggaran Saya</span>
                <div class="portal-siswa-stat-value">{{ $totalPelanggaran }}</div>
            </div>
        </div>

        <div class="portal-siswa-stat">
            <div class="portal-siswa-stat-icon">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <span class="stat-label">Prestasi Saya</span>
                <div class="portal-siswa-stat-value">{{ $totalPrestasi }}</div>
            </div>
        </div>

    </div>


    {{-- =====================================================
         RIWAYAT
    ====================================================== --}}
    <div class="portal-siswa-grid">

        {{-- PELANGGARAN --}}
        <section class="table-card portal-siswa-section">
            <div class="portal-siswa-section-head">
                <h2 class="portal-siswa-section-title">
                    <i class="fa-solid fa-shield-halved"></i>
                    Pelanggaran Saya
                </h2>
                <span class="portal-siswa-count">{{ $pelanggarans->count() }} data</span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Status</th>
                            <th>Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pelanggarans as $pelanggaran)
                            @php
                                $statusKelas = match ($pelanggaran->status) {
                                    'diverifikasi' => 'badge-verified',
                                    'pending' => 'badge-pending',
                                    'ditolak' => 'badge-rejected',
                                    default => '',
                                };

                                $statusTeks = match ($pelanggaran->status) {
                                    'diverifikasi' => 'Diverifikasi',
                                    'pending' => 'Menunggu',
                                    'ditolak' => 'Ditolak',
                                    default => $pelanggaran->status,
                                };
                            @endphp
                            <tr>
                                <td>{{ $pelanggaran->tanggal?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td>
                                    <div class="student-info">
                                        <strong>
                                            {{ $pelanggaran->aturanPelanggaran?->nama
                                                ?? $pelanggaran->jenis_pelanggaran
                                                ?? '-' }}
                                        </strong>
                                        <small>{{ $pelanggaran->kategori ?: 'Pelanggaran' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-status {{ $statusKelas }}">
                                        {{ $statusTeks }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-point">{{ $pelanggaran->poin }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Belum ada catatan pelanggaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>


        {{-- PRESTASI --}}
        <section class="table-card portal-siswa-section">
            <div class="portal-siswa-section-head">
                <h2 class="portal-siswa-section-title">
                    <i class="fa-solid fa-award"></i>
                    Prestasi Saya
                </h2>
                <span class="portal-siswa-count">{{ $prestasis->count() }} data</span>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Prestasi</th>
                            <th>Tingkat</th>
                            <th>Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prestasis as $prestasi)
                            <tr>
                                <td>{{ $prestasi->tanggal?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td>
                                    <div class="student-info">
                                        <strong>{{ $prestasi->jenis_prestasi }}</strong>
                                        <small>{{ $prestasi->keterangan ?: 'Prestasi' }}</small>
                                    </div>
                                </td>
                                <td>{{ $prestasi->tingkat ?: '-' }}</td>
                                <td>
                                    <span class="badge-point point-positive">+{{ $prestasi->poin }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">
                                    <i class="fa-solid fa-trophy"></i>
                                    Belum ada catatan prestasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>

    <p class="portal-siswa-note">
        Saldo poin = 100 − poin pelanggaran yang sudah diverifikasi + poin prestasi.
    </p>

</div>

@endsection
