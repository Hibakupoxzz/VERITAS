@extends('layouts.app')

@section('title', 'Riwayat Laporan')
@section('page_title', 'Riwayat Laporan Saya')

@section('content')

@php
    $stats = $stats ?? [];
    $count = [
        'all'          => $stats['total']    ?? $laporans->count(),
        'pending'      => $stats['pending']  ?? $laporans->where('status', 'pending')->count(),
        'diverifikasi' => $stats['verified'] ?? $laporans->where('status', 'diverifikasi')->count(),
        'ditolak'      => $stats['rejected'] ?? $laporans->where('status', 'ditolak')->count(),
    ];
@endphp

<div class="walas-container">

    {{-- Catatan: flash session('success') / 'error' sudah otomatis
         ditampilkan oleh layouts/app.blade.php. --}}

    <div class="walas-card" id="riwayat-lapor">

        {{-- HEADER --}}
        <div class="walas-card-header">

            <div class="walas-card-header-main">
                <div class="walas-card-icon walas-card-icon-indigo">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="walas-card-header-text">
                    <h2>Riwayat Laporan Saya</h2>
                    <p>Daftar seluruh laporan yang telah Anda kirimkan beserta perkembangan status verifikasinya.</p>
                </div>
            </div>

            <a href="{{ route('lapor.index') }}" class="walas-btn-new">
                <i class="fa-solid fa-plus"></i>
                <span>Buat Laporan</span>
            </a>

        </div>


        @if($laporans->count() > 0)

            {{-- TOOLBAR: TAB STATUS + CARI --}}
            <div class="walas-toolbar">

                <div class="walas-filter-tabs" id="filterTabs" role="group" aria-label="Filter status">
                    <button type="button" class="tab-btn active" data-status="all" aria-pressed="true">
                        Semua ({{ $count['all'] }})
                    </button>
                    <button type="button" class="tab-btn" data-status="pending" aria-pressed="false">
                        Menunggu ({{ $count['pending'] }})
                    </button>
                    <button type="button" class="tab-btn" data-status="diverifikasi" aria-pressed="false">
                        Diverifikasi ({{ $count['diverifikasi'] }})
                    </button>
                    <button type="button" class="tab-btn" data-status="ditolak" aria-pressed="false">
                        Ditolak ({{ $count['ditolak'] }})
                    </button>
                </div>

                <div class="walas-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        type="search"
                        id="reportSearch"
                        placeholder="Cari siswa atau pelanggaran..."
                        autocomplete="off"
                    >
                </div>

            </div>

            <div class="walas-result-info">
                Menampilkan <strong id="reportCount">{{ $laporans->count() }}</strong> laporan
            </div>

        @endif


        {{-- DAFTAR LAPORAN --}}
        <div class="walas-reports-list" id="reportsList">

            @forelse($laporans as $laporan)

                @php
                    $namaSiswa = $laporan->siswa?->nama ?? 'Siswa Tidak Ditemukan';
                    $haystack  = \Illuminate\Support\Str::lower(
                        $namaSiswa . ' ' .
                        ($laporan->siswa?->kelas ?? '') . ' ' .
                        $laporan->jenis_pelanggaran . ' ' .
                        ($laporan->keterangan ?? '')
                    );
                @endphp

                <div class="report-item"
                     data-status="{{ $laporan->status }}"
                     data-search="{{ $haystack }}">

                    {{-- Header Kartu --}}
                    <div class="report-header">

                        <div class="report-student">
                            <div class="student-avatar">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div class="student-info">
                                <h3 class="student-name">{{ $namaSiswa }}</h3>
                                <span class="student-class">{{ $laporan->siswa?->kelas ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="report-status-badge">
                            @if($laporan->status === 'pending')
                                <span class="badge-status badge-pending">
                                    <i class="fa-solid fa-hourglass-half"></i> Menunggu Verifikasi
                                </span>
                            @elseif($laporan->status === 'diverifikasi')
                                <span class="badge-status badge-verified">
                                    <i class="fa-solid fa-circle-check"></i> Diverifikasi
                                </span>
                            @elseif($laporan->status === 'ditolak')
                                <span class="badge-status badge-rejected">
                                    <i class="fa-solid fa-circle-xmark"></i> Ditolak
                                </span>
                            @else
                                <span class="badge-status">{{ ucfirst($laporan->status) }}</span>
                            @endif
                        </div>

                    </div>

                    {{-- Body Kartu --}}
                    <div class="report-body">

                        <div class="report-meta-tags">
                            <span class="meta-tag">
                                <i class="fa-regular fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($laporan->tanggal)->locale('id')->translatedFormat('d F Y') }}
                            </span>
                            <span class="meta-tag meta-violation">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                {{ $laporan->jenis_pelanggaran }}
                            </span>
                        </div>

                        @if($laporan->keterangan)
                            <p class="report-description">{{ $laporan->keterangan }}</p>
                        @endif

                        @if($laporan->foto_bukti)
                            <div class="report-photo">
                                <a href="{{ asset('storage/' . $laporan->foto_bukti) }}"
                                   target="_blank" rel="noopener"
                                   title="Klik untuk memperbesar">
                                    <img src="{{ asset('storage/' . $laporan->foto_bukti) }}"
                                         alt="Foto Bukti" loading="lazy">
                                    <span class="photo-hint">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Bukti Penuh
                                    </span>
                                </a>
                            </div>
                        @endif

                    </div>

                    {{-- Info Verifikasi --}}
                    @if($laporan->status === 'diverifikasi')

                        <div class="report-feedback feedback-verified">
                            <div class="feedback-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                            <div class="feedback-text">
                                <strong>Laporan Diterima &amp; Disetujui</strong>
                                <span>Diverifikasi oleh: <strong>{{ $laporan->verifikator->name ?? 'Petugas PDS' }}</strong></span>
                                <span class="points-badge">Poin Pengurang Dikenakan: -{{ $laporan->poin }}</span>
                                @if($laporan->catatan_verifikasi)
                                    <p class="feedback-note">"{{ $laporan->catatan_verifikasi }}"</p>
                                @endif
                            </div>
                        </div>

                    @elseif($laporan->status === 'ditolak')

                        <div class="report-feedback feedback-rejected">
                            <div class="feedback-icon"><i class="fa-solid fa-ban"></i></div>
                            <div class="feedback-text">
                                <strong>Laporan Ditolak</strong>
                                <span>Ditinjau oleh: <strong>{{ $laporan->verifikator->name ?? 'Petugas PDS' }}</strong></span>
                                @if($laporan->catatan_verifikasi)
                                    <p class="feedback-note">Alasan Penolakan: "{{ $laporan->catatan_verifikasi }}"</p>
                                @endif
                            </div>
                        </div>

                    @else

                        <div class="report-feedback feedback-pending">
                            <div class="feedback-icon"><i class="fa-solid fa-clock"></i></div>
                            <div class="feedback-text">
                                <strong>Dalam Antrean Peninjauan</strong>
                                <span>Laporan ini belum diverifikasi. Petugas PDS akan segera meninjau bukti dan menentukan poin pelanggaran.</span>
                            </div>
                        </div>

                    @endif

                </div>

            @empty

                <div class="walas-empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-inbox"></i></div>
                    <h3>Belum Ada Laporan</h3>
                    <p>Anda belum pernah mengirim laporan pelanggaran. Gunakan formulir di halaman Lapor untuk melapor.</p>
                    <a href="{{ route('lapor.index') }}" class="walas-btn-new walas-btn-center">
                        <i class="fa-solid fa-plus"></i>
                        <span>Buat Laporan Pertama</span>
                    </a>
                </div>

            @endforelse

        </div>

        {{-- Muncul saat filter/pencarian tidak menemukan hasil --}}
        <div class="walas-empty-state is-hidden" id="noResult">
            <div class="empty-icon"><i class="fa-solid fa-filter-circle-xmark"></i></div>
            <h3>Tidak Ada Laporan</h3>
            <p>Tidak ada laporan yang cocok dengan filter atau pencarian Anda.</p>
        </div>

        {{-- Pagination (hanya jika $laporans berupa paginator) --}}
        @if(method_exists($laporans, 'hasPages') && $laporans->hasPages())
            <div class="walas-pagination">
                {{ $laporans->links() }}
            </div>
        @endif

    </div>

</div>

@endsection


@section('styles')
@include('partials.ui')

/* =========================================================
   RIWAYAT LAPORAN
   Di-scope ke .walas-container supaya tidak bentrok dengan
   class generik di partials/ui.blade.
========================================================= */

.walas-container {
    width: 100%;
    max-width: 100%;
    display: flex;
    flex-direction: column;
    gap: 24px;
    overflow-wrap: break-word;
}

.walas-container .is-hidden {
    display: none !important;
}


/* ---------- CARD & HEADER ---------- */

.walas-container .walas-card {
    min-width: 0;
    padding: 28px 32px;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.04);
}

.walas-container .walas-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 22px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f3f4f6;
}

.walas-container .walas-card-header-main {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
    flex: 1 1 320px;
}

.walas-container .walas-card-header-text {
    min-width: 0;
}

.walas-container .walas-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    border-radius: 14px;
    font-size: 20px;
}

.walas-container .walas-card-icon-indigo {
    background: #eef2ff;
    color: #4f46e5;
}

.walas-container .walas-card-header h2 {
    margin: 0 0 4px;
    color: #111827;
    font-size: 20px;
    font-weight: 700;
}

.walas-container .walas-card-header p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
}

.walas-container .walas-btn-new {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 0 18px;
    border-radius: 12px;
    background: var(--primary);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    box-shadow: 0 4px 14px rgba(109, 20, 8, 0.22);
    transition: all 0.2s ease;
}

.walas-container .walas-btn-new:hover {
    background: #550f06;
    transform: translateY(-2px);
}

.walas-container .walas-btn-center {
    margin-top: 18px;
}


/* ---------- TOOLBAR ---------- */

.walas-container .walas-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 10px;
}

.walas-container .walas-filter-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-width: 0;
}

.walas-container .tab-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    background: #f3f4f6;
    color: #4b5563;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s;
}

.walas-container .tab-btn:hover {
    background: #e5e7eb;
}

.walas-container .tab-btn.active {
    background: #111827;
    color: #fff;
}

.walas-container .tab-btn:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

.walas-container .walas-search {
    position: relative;
    flex: 0 1 320px;
    min-width: 200px;
}

.walas-container .walas-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 13px;
    pointer-events: none;
}

.walas-container .walas-search input {
    box-sizing: border-box;
    width: 100%;
    height: 40px;
    padding: 0 14px 0 38px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fff;
    color: #111827;
    font-family: inherit;
    font-size: 13px;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}

.walas-container .walas-search input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(109, 20, 8, 0.12);
}

.walas-container .walas-result-info {
    margin-bottom: 16px;
    color: #6b7280;
    font-size: 12px;
}

.walas-container .walas-result-info strong {
    color: #111827;
}


/* ---------- DAFTAR LAPORAN ---------- */

.walas-container .walas-reports-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.walas-container .report-item {
    min-width: 0;
    padding: 20px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #fff;
    transition: transform 0.2s, box-shadow 0.2s;
}

.walas-container .report-item[hidden] {
    display: none !important;
}

.walas-container .report-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}

.walas-container .report-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px dashed #e5e7eb;
}

.walas-container .report-student {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.walas-container .report-status-badge {
    flex-shrink: 0;
}

.walas-container .student-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    border-radius: 12px;
    background: #f3f4f6;
    color: #4b5563;
    font-size: 16px;
}

.walas-container .student-info {
    min-width: 0;
}

.walas-container .student-name {
    margin: 0 0 2px;
    color: #111827;
    font-size: 15px;
    font-weight: 700;
    overflow-wrap: anywhere;
}

.walas-container .student-class {
    color: #6b7280;
    font-size: 12px;
}

.walas-container .badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1px solid transparent;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.walas-container .badge-pending  { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.walas-container .badge-verified { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
.walas-container .badge-rejected { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

.walas-container .report-body {
    margin-bottom: 16px;
}

.walas-container .report-meta-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}

.walas-container .meta-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
    padding: 6px 10px;
    border-radius: 8px;
    background: #f3f4f6;
    color: #4b5563;
    font-size: 12px;
    overflow-wrap: anywhere;
}

.walas-container .meta-violation {
    background: #fef2f2;
    color: #991b1b;
    font-weight: 600;
}

.walas-container .report-description {
    margin: 0 0 16px;
    padding: 12px 16px;
    border-left: 3px solid #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    color: #4b5563;
    font-size: 14px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.walas-container .report-photo {
    margin-top: 10px;
}

.walas-container .report-photo a {
    display: inline-block;
}

.walas-container .report-photo img {
    display: block;
    width: auto;
    max-width: min(140px, 100%);
    height: auto;
    max-height: 140px;
    object-fit: cover;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    transition: transform 0.2s;
}

.walas-container .report-photo a:hover img {
    transform: scale(1.03);
}

.walas-container .photo-hint {
    display: block;
    margin-top: 4px;
    color: #6b7280;
    font-size: 10px;
}


/* ---------- FEEDBACK VERIFIKASI ---------- */

.walas-container .report-feedback {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-size: 12px;
}

.walas-container .feedback-verified { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.walas-container .feedback-rejected { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
.walas-container .feedback-pending  { background: #fefce8; border-color: #fef08a; color: #854d0e; }

.walas-container .feedback-icon {
    margin-top: 2px;
    font-size: 16px;
}

.walas-container .feedback-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
    overflow-wrap: anywhere;
}

.walas-container .points-badge {
    display: inline-block;
    width: fit-content;
    margin-top: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    background: var(--c-point-bg);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
}

.walas-container .feedback-note {
    margin: 4px 0 0;
    font-size: 11px;
    font-style: italic;
    opacity: 0.9;
}

.walas-container .walas-pagination {
    display: flex;
    justify-content: center;
    margin-top: 22px;
}


/* ---------- EMPTY STATE ---------- */

.walas-container .walas-empty-state {
    padding: 40px 20px;
    text-align: center;
}

.walas-container .empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    margin: 0 auto 14px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #9ca3af;
    font-size: 24px;
}

.walas-container .walas-empty-state h3 {
    margin: 0 0 6px;
    color: #111827;
    font-size: 16px;
}

.walas-container .walas-empty-state p {
    max-width: 400px;
    margin: 0 auto;
    color: #6b7280;
    font-size: 13px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1024px) {
    .walas-container .walas-card { padding: 24px; }
}

@media (max-width: 900px) {
    .walas-container .walas-card-header { flex-direction: column; align-items: flex-start; }
    .walas-container .walas-card-header-main { width: 100%; }
    .walas-container .walas-btn-new { width: 100%; }
    .walas-container .walas-btn-center { width: auto; }
    .walas-container .walas-search { flex: 1 1 100%; }
}

@media (max-width: 768px) {
    .walas-container { gap: 16px; }
    .walas-container .walas-card { padding: 20px 16px; border-radius: 16px; }
    .walas-container .report-item { padding: 16px; }
    .walas-container .report-header { flex-wrap: wrap; align-items: flex-start; }
    .walas-container .report-student,
    .walas-container .report-status-badge { flex: 1 1 100%; }
}

@media (max-width: 640px) {
    /* Tab menggulir horizontal agar tidak menumpuk berbaris-baris */
    .walas-container .walas-filter-tabs {
        flex-wrap: nowrap;
        width: 100%;
        padding-bottom: 4px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .walas-container .tab-btn {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        padding: 9px 14px;
    }
}

@media (max-width: 576px) {
    /* 16px mencegah iOS zoom otomatis saat input difokus */
    .walas-container .walas-search input { font-size: 16px; }

    .walas-container .walas-card-header h2 { font-size: 17px; }
    .walas-container .walas-card-icon { width: 40px; height: 40px; border-radius: 11px; font-size: 17px; }
    .walas-container .report-feedback { padding: 12px; gap: 10px; }
    .walas-container .badge-status { max-width: 100%; white-space: normal; }
}

@media (max-width: 480px) {
    .walas-container .walas-card { padding: 16px 13px; }
    .walas-container .report-item { padding: 14px; border-radius: 12px; }
    .walas-container .student-avatar { width: 36px; height: 36px; flex-basis: 36px; font-size: 15px; }
    .walas-container .student-name { font-size: 14px; }
    .walas-container .report-description { padding: 10px 12px; }
    .walas-container .walas-empty-state { padding: 30px 10px; }
}

@media (max-width: 400px) {
    .walas-container .walas-card { padding: 14px 10px; }
    .walas-container .badge-status { padding: 6px 10px; font-size: 11px; }
    .walas-container .points-badge { font-size: 10px; }
}
@endsection


@section('scripts')
<script>
(function () {
    'use strict';

    var tabs     = document.getElementById('filterTabs');
    var search   = document.getElementById('reportSearch');
    var counter  = document.getElementById('reportCount');
    var noResult = document.getElementById('noResult');
    var items    = document.querySelectorAll('.report-item');

    // Tidak ada laporan sama sekali: tidak ada yang perlu difilter.
    if (!tabs || !items.length) { return; }

    var activeStatus = 'all';

    // Filter status dan pencarian bekerja bersamaan.
    function applyFilter() {
        var q = (search.value || '').trim().toLowerCase();
        var shown = 0;

        items.forEach(function (item) {
            var okStatus = activeStatus === 'all' || item.dataset.status === activeStatus;
            var okSearch = !q || item.dataset.search.indexOf(q) !== -1;
            var show = okStatus && okSearch;

            item.hidden = !show;
            if (show) { shown++; }
        });

        counter.textContent = shown;
        noResult.classList.toggle('is-hidden', shown !== 0);
    }

    tabs.addEventListener('click', function (e) {
        var btn = e.target.closest('.tab-btn');
        if (!btn) { return; }

        activeStatus = btn.dataset.status;

        tabs.querySelectorAll('.tab-btn').forEach(function (b) {
            var isActive = b === btn;
            b.classList.toggle('active', isActive);
            b.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        applyFilter();
    });

    search.addEventListener('input', applyFilter);
})();
</script>
@endsection
