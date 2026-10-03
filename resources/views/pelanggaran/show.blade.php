@extends('layouts.app')

@section('title', 'Rincian Laporan Pelanggaran')
@section('page_title', 'Rincian Laporan Pelanggaran')

@section('styles')

@include('partials.ui')

/* =========================================================
   RINCIAN LAPORAN PELANGGARAN
   Primitif bersama (.pv-card, .pv-section-label, .pv-page-*,
   .pv-footer) berasal dari partials/ui.blade.
========================================================= */

.pv-body {
    width: 100%;
}

.pv-inner {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
}

/* .pv-card di partial tidak punya padding, jadi diberikan di sini */
.pv-inner .pv-card {
    padding: 24px 28px;
}


/* ── Definisi / nilai ── */
.pv-grid {
    display: grid;
    grid-template-columns: 210px minmax(0, 1fr);
    gap: 0;
}

.pv-item {
    display: contents;
}

.pv-key {
    min-width: 0;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
    color: #6b7280;
    font-size: .85rem;
    font-weight: 500;
    overflow-wrap: anywhere;
}

.pv-value {
    min-width: 0;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
    color: #111827;
    font-size: .9rem;
    font-weight: 600;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

/*
 * pre-line hanya untuk teks bebas (kronologi, catatan) supaya baris
 * baru dari pengguna tetap terlihat. Jika dipasang di semua nilai,
 * spasi/newline dari template Blade ikut tampil sebagai baris kosong.
 * Isi .pv-value-text harus ditulis dalam satu baris di template.
 */
.pv-value-text {
    white-space: pre-line;
    font-weight: 500;
}

.pv-item:last-child .pv-key,
.pv-item:last-child .pv-value {
    border-bottom: none;
}


/* ── Badge ── */
.pv-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    max-width: 100%;
    padding: 5px 11px;
    border-radius: 999px;
    background: #dbeafe;
    color: #1d4ed8;
    font-size: .78rem;
    font-weight: 700;
    overflow-wrap: anywhere;
}

.pv-badge-poin     { background: #fff7ed; color: #9a3412; }
.pv-badge-pending  { background: #fef9c3; color: #854d0e; }
.pv-badge-verified { background: #dcfce7; color: #166534; }
.pv-badge-rejected { background: #fee2e2; color: #991b1b; }

.pv-kat-ringan { background: #dcfce7; color: #166534; }
.pv-kat-sedang { background: #fef3c7; color: #92400e; }
.pv-kat-berat  { background: #fee2e2; color: #991b1b; }
.pv-kat-luar-biasa { background: #1f2937; color: #fff; }

.pv-muted {
    color: #9ca3af;
    font-style: italic;
    font-weight: 500;
}


/* ── Ringkasan siswa ── */
.pv-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 18px;
}

.pv-summary-item {
    min-width: 0;
    padding: 14px 16px;
    border: 1px solid #e8eaed;
    border-radius: 12px;
    background: #f9fafb;
}

.pv-summary-label {
    display: block;
    margin-bottom: 4px;
    color: #9ca3af;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
}

.pv-summary-value {
    display: block;
    color: #1a1a2e;
    font-size: 1.05rem;
    font-weight: 700;
    overflow-wrap: anywhere;
}


/* ── Riwayat teguran ── */
.pv-history {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.pv-history li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border: 1px solid #f1f5f9;
    border-radius: 10px;
    background: #f9fafb;
}

.pv-history-date {
    flex: 0 0 auto;
    color: #6b7280;
    font-size: .78rem;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
}

.pv-history-jenis {
    flex: 1 1 auto;
    min-width: 0;
    color: #1f2937;
    font-size: .85rem;
    font-weight: 600;
    overflow-wrap: anywhere;
}

.pv-history-poin {
    flex: 0 0 auto;
    padding: 3px 8px;
    border-radius: 6px;
    background: #fff7ed;
    color: #9a3412;
    font-size: .72rem;
    font-weight: 700;
    white-space: nowrap;
}


/* ── Foto bukti ── */
.pv-photo-link {
    position: relative;
    display: block;
    max-width: 520px;
    overflow: hidden;
    border-radius: 12px;
}

.pv-img {
    display: block;
    width: 100%;
    height: auto;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.pv-photo-hint {
    position: absolute;
    inset: auto 0 0 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px;
    background: rgba(17, 24, 39, .72);
    color: #fff;
    font-size: .72rem;
    font-weight: 600;
    opacity: 0;
    transition: opacity .18s ease;
}

.pv-photo-link:hover .pv-photo-hint,
.pv-photo-link:focus-visible .pv-photo-hint {
    opacity: 1;
}

/* Layar sentuh tidak punya hover, jadi petunjuk selalu terlihat */
@media (hover: none) {
    .pv-photo-hint { opacity: 1; }
}


/* ── Footer / tombol ── */
.pv-inner .pv-footer {
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-top: 4px;
}

.pv-note {
    flex: 1 1 240px;
    min-width: 0;
    margin: 0;
    color: #9ca3af;
    font-size: .78rem;
    line-height: 1.5;
}

.pv-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-left: auto;
}

.pv-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 42px;
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    font-family: inherit;
    font-size: .875rem;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
    transition: background .18s ease, transform .18s ease;
}

.pv-btn-primary {
    background: var(--primary);
    color: #fff;
}

.pv-btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.pv-btn-muted {
    background: #f3f4f6;
    color: #4b5563;
}

.pv-btn-muted:hover {
    background: #e5e7eb;
    color: #1f2937;
}

.pv-btn:focus-visible,
.pv-photo-link:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}


/* ── Responsive ── */
@media (max-width: 768px) {
    .pv-inner .pv-card { padding: 18px; }
    .pv-page-header { gap: 10px; }
    .pv-summary { grid-template-columns: 1fr; }
}

@media (max-width: 576px) {
    .pv-inner .pv-card { padding: 16px 14px; }

    .pv-grid { grid-template-columns: 1fr; }

    .pv-key {
        padding: 10px 0 0;
        border-bottom: none;
        font-size: .72rem;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .pv-value { padding: 2px 0 10px; }

    .pv-page-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
    }

    .pv-page-title { font-size: 1.1rem; }

    .pv-actions { width: 100%; margin-left: 0; }
    .pv-btn { width: 100%; }

    .pv-photo-link { max-width: 100%; }

    .pv-history li { flex-wrap: wrap; }
}
@endsection


@section('content')

@php
    $statusMeta = match ($pelanggaran->status) {
        'pending'      => ['label' => 'Menunggu Verifikasi', 'class' => 'pv-badge-pending'],
        'diverifikasi' => ['label' => 'Terverifikasi',       'class' => 'pv-badge-verified'],
        'ditolak'      => ['label' => 'Ditolak',             'class' => 'pv-badge-rejected'],
        default        => ['label' => ucfirst($pelanggaran->status), 'class' => ''],
    };

    // Halaman ini jadi tujuan dari "Pending Laporan",
    // supaya tombol kembali mengarah ke antrean verifikasi.
    $fromPending = request()->query('from') === 'pending';
    $backUrl     = $fromPending ? route('pelanggaran.pending') : route('pelanggaran.index');

    $canVerify  = auth()->user()?->canVerify();
    $isPending  = $pelanggaran->status === 'pending';
    $katSlug    = \Illuminate\Support\Str::slug($pelanggaran->kategori ?? '');

    $ringkasan = $ringkasan ?? null;
    $riwayat   = $riwayat ?? collect();
@endphp

<div class="pv-body">

<div class="pv-inner">

    {{-- HEADER --}}
    <div class="pv-page-header">
        <div class="pv-page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
        </div>

        <div class="pv-page-headtext">
            <h1 class="pv-page-title">Rincian Laporan Pelanggaran</h1>
            <p class="pv-page-sub">
                Laporan #{{ $pelanggaran->id }} &middot;
                <span class="pv-badge {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
            </p>
        </div>
    </div>


    {{-- SISWA --}}
    <div class="pv-card">

        <div class="pv-section-label">Siswa yang Dilaporkan</div>

        <div class="pv-grid">
            <div class="pv-item">
                <div class="pv-key">Nama</div>
                <div class="pv-value">{{ $pelanggaran->siswa?->nama ?? 'Laporan Umum (Tanpa Siswa)' }}</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">NISN</div>
                <div class="pv-value">{{ $pelanggaran->siswa?->nisn ?? '—' }}</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Kelas</div>
                <div class="pv-value">{{ $pelanggaran->siswa?->kelas ?? '—' }}</div>
            </div>
        </div>

        @if(!empty($ringkasan))
            <div class="pv-summary">
                <div class="pv-summary-item">
                    <span class="pv-summary-label">Saldo Poin</span>
                    <span class="pv-summary-value">{{ $ringkasan->saldo_poin }} Poin</span>
                </div>

                <div class="pv-summary-item">
                    <span class="pv-summary-label">Pelanggaran Terverifikasi</span>
                    <span class="pv-summary-value">{{ $ringkasan->jumlah_pelanggaran }} kali</span>
                </div>

                <div class="pv-summary-item">
                    <span class="pv-summary-label">Total Poin Pelanggaran</span>
                    <span class="pv-summary-value">{{ (int) ($ringkasan->total_poin_pelanggaran ?? 0) }} Poin</span>
                </div>
            </div>
        @endif

    </div>


    {{-- ISI LAPORAN --}}
    <div class="pv-card">

        <div class="pv-section-label">Isi Laporan</div>

        <div class="pv-grid">

            <div class="pv-item">
                <div class="pv-key">Tanggal Kejadian</div>
                <div class="pv-value">{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->locale('id')->translatedFormat('d F Y') }}</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Waktu Dilaporkan</div>
                <div class="pv-value">{{ \Carbon\Carbon::parse($pelanggaran->created_at)->locale('id')->translatedFormat('d F Y, H:i') }}</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Pelapor</div>
                <div class="pv-value">@if($pelanggaran->pelapor){{ $pelanggaran->pelapor->name }} <span class="pv-muted">({{ $pelanggaran->pelapor->role_label }})</span>@else<span class="pv-muted">Tidak diketahui</span>@endif</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Aturan Master</div>
                <div class="pv-value">@if($pelanggaran->aturanPelanggaran){{ $pelanggaran->aturanPelanggaran->kode }} — {{ $pelanggaran->aturanPelanggaran->nama }} <span class="pv-muted">({{ $pelanggaran->aturanPelanggaran->kategori }}, {{ $pelanggaran->aturanPelanggaran->poin }} poin)</span>@else<span class="pv-muted">Tidak terkait aturan master</span>@endif</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Jenis Pelanggaran</div>
                <div class="pv-value">{{ $pelanggaran->jenis_pelanggaran ?: '—' }}</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Kategori</div>
                <div class="pv-value">@if($pelanggaran->kategori)<span class="pv-badge pv-kat-{{ $katSlug }}">{{ $pelanggaran->kategori }}</span>@else—@endif</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Poin</div>
                <div class="pv-value">@if($pelanggaran->poin)<span class="pv-badge pv-badge-poin">−{{ $pelanggaran->poin }} Poin</span>@else—@endif</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Saldo Poin Tercatat</div>
                <div class="pv-value">@if($pelanggaran->poin_sebelum !== null){{ $pelanggaran->poin_sebelum }} → {{ $pelanggaran->poin_sesudah }} Poin@elseif($isPending)<span class="pv-muted">Belum dihitung (menunggu verifikasi)</span>@else<span class="pv-muted">Tidak dihitung (tanpa siswa)</span>@endif</div>
            </div>

            <div class="pv-item">
                <div class="pv-key">Kronologi / Keterangan</div>
                <div class="pv-value pv-value-text">@if($pelanggaran->keterangan){{ $pelanggaran->keterangan }}@else<span class="pv-muted">Tidak ada keterangan tambahan.</span>@endif</div>
            </div>

        </div>

    </div>


    {{-- VERIFIKASI --}}
    @if(!$isPending || $pelanggaran->catatan_verifikasi)
        <div class="pv-card">

            <div class="pv-section-label">Verifikasi</div>

            <div class="pv-grid">

                <div class="pv-item">
                    <div class="pv-key">Verifikator</div>
                    <div class="pv-value">@if($pelanggaran->verifikator){{ $pelanggaran->verifikator->name }} <span class="pv-muted">({{ $pelanggaran->verifikator->role_label }})</span>@else<span class="pv-muted">Belum diverifikasi</span>@endif</div>
                </div>

                @if(!$isPending)
                    <div class="pv-item">
                        <div class="pv-key">Waktu Verifikasi</div>
                        <div class="pv-value">{{ $pelanggaran->updated_at->locale('id')->translatedFormat('d F Y, H:i') }}</div>
                    </div>
                @endif

                <div class="pv-item">
                    <div class="pv-key">Catatan Verifikasi</div>
                    <div class="pv-value pv-value-text">@if($pelanggaran->catatan_verifikasi){{ $pelanggaran->catatan_verifikasi }}@else<span class="pv-muted">Tidak ada catatan.</span>@endif</div>
                </div>

            </div>

        </div>
    @endif


    {{-- RIWAYAT TEGURAN --}}
    @if($riwayat->isNotEmpty())
        <div class="pv-card">

            <div class="pv-section-label">Riwayat Teguran 90 Hari Terakhir</div>

            <ul class="pv-history">
                @foreach($riwayat as $item)
                    <li>
                        <span class="pv-history-date">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                        </span>
                        <span class="pv-history-jenis">{{ $item->jenis_pelanggaran }}</span>
                        <span class="pv-history-poin">−{{ $item->poin }}</span>
                    </li>
                @endforeach
            </ul>

        </div>
    @endif


    {{-- FOTO BUKTI --}}
    @if($pelanggaran->foto_bukti)
        <div class="pv-card">

            <div class="pv-section-label">Foto Bukti</div>

            <a href="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
               target="_blank"
               rel="noopener"
               class="pv-photo-link"
               title="Buka foto di ukuran penuh">
                <img src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
                     alt="Bukti foto pelanggaran {{ $pelanggaran->jenis_pelanggaran ?? '' }}"
                     class="pv-img"
                     loading="lazy">
                <span class="pv-photo-hint">
                    <i class="fa-solid fa-expand"></i>
                    Klik untuk melihat ukuran penuh
                </span>
            </a>

        </div>
    @endif


    {{-- AKSI --}}
    <div class="pv-footer">

        @if($isPending && $canVerify)
            <p class="pv-note">
                Laporan ini masih menunggu verifikasi. Kembali ke antrean
                untuk menyetujui atau menolak.
            </p>
        @endif

        <div class="pv-actions">
            <a href="{{ $backUrl }}" class="pv-btn pv-btn-muted">
                <i class="fa-solid fa-arrow-left"></i>
                {{ $fromPending ? 'Kembali ke Antrean' : 'Kembali' }}
            </a>

            @if($isPending && $canVerify)
                <a href="{{ route('pelanggaran.pending') }}" class="pv-btn pv-btn-primary">
                    <i class="fa-solid fa-list-check"></i>
                    Verifikasi Laporan
                </a>
            @endif
        </div>

    </div>

</div>

</div>

@endsection
