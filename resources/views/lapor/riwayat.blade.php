@extends('layouts.app')

@section('title', 'Riwayat Laporan')
@section('page_title', 'Riwayat Laporan Saya')

@section('content')

<div class="walas-container">

    {{-- =========================================================
         RIWAYAT LAPORAN SAYA
    ========================================================= --}}
    <div class="walas-card" id="riwayat-lapor">

        <div class="walas-card-header" style="justify-content:space-between; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div class="walas-card-icon" style="background:#eef2ff; color:#4f46e5;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h2>Riwayat Laporan Saya</h2>
                    <p>Daftar seluruh laporan yang telah Anda kirimkan beserta perkembangan status verifikasinya.</p>
                </div>
            </div>

            <div class="walas-filter-tabs">
                <button type="button" class="tab-btn active" onclick="filterReports('all', this)">
                    Semua ({{ $stats['total'] }})
                </button>
                <button type="button" class="tab-btn" onclick="filterReports('pending', this)">
                    Menunggu ({{ $stats['pending'] }})
                </button>
                <button type="button" class="tab-btn" onclick="filterReports('diverifikasi', this)">
                    Diverifikasi ({{ $stats['verified'] }})
                </button>
                <button type="button" class="tab-btn" onclick="filterReports('ditolak', this)">
                    Ditolak ({{ $stats['rejected'] }})
                </button>
            </div>
        </div>

        <div class="walas-reports-list" id="reportsList">

            @forelse($laporans as $laporan)

                <div class="report-item" data-status="{{ $laporan->status }}">

                    {{-- Header Kartu --}}
                    <div class="report-header">
                        <div class="report-student">
                            <div class="student-avatar">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div>
                                <h3 class="student-name">{{ $laporan->siswa?->nama ?? 'Siswa Tidak Ditemukan' }}</h3>
                                <span class="student-class">{{ $laporan->siswa?->kelas ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        <div class="report-status-badge">
                            @if($laporan->status === 'pending')
                                <span class="badge-status badge-pending">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Menunggu Verifikasi
                                </span>
                            @elseif($laporan->status === 'diverifikasi')
                                <span class="badge-status badge-verified">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Diverifikasi
                                </span>
                            @elseif($laporan->status === 'ditolak')
                                <span class="badge-status badge-rejected">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Body Kartu --}}
                    <div class="report-body">

                        <div class="report-meta-tags">
                            <span class="meta-tag">
                                <i class="fa-regular fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d F Y') }}
                            </span>
                            <span class="meta-tag meta-violation">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                {{ $laporan->jenis_pelanggaran }}
                            </span>
                        </div>

                        @if($laporan->keterangan)
                            <p class="report-description">
                                {{ $laporan->keterangan }}
                            </p>
                        @endif

                        @if($laporan->foto_bukti)
                            <div class="report-photo">
                                <a href="{{ asset('storage/' . $laporan->foto_bukti) }}" target="_blank" title="Klik untuk memperbesar">
                                    <img src="{{ asset('storage/' . $laporan->foto_bukti) }}" alt="Foto Bukti">
                                    <span class="photo-hint"><i class="fa-solid fa-magnifying-glass-plus"></i> Lihat Bukti Penuh</span>
                                </a>
                            </div>
                        @endif

                    </div>

                    {{-- Footer Status Info Verifikasi --}}
                    @if($laporan->status === 'diverifikasi')
                        <div class="report-feedback feedback-verified">
                            <div class="feedback-icon">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                            <div class="feedback-text">
                                <strong>Laporan Diterima & Disetujui</strong>
                                <span>Diverifikasi oleh: <strong>{{ $laporan->verifikator->name ?? 'Petugas PDS' }}</strong></span>
                                <span class="points-badge">Poin Pengurang Dikenakan: -{{ $laporan->poin }}</span>
                                @if($laporan->catatan_verifikasi)
                                    <p class="feedback-note">"{{ $laporan->catatan_verifikasi }}"</p>
                                @endif
                            </div>
                        </div>
                    @elseif($laporan->status === 'ditolak')
                        <div class="report-feedback feedback-rejected">
                            <div class="feedback-icon">
                                <i class="fa-solid fa-ban"></i>
                            </div>
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
                            <div class="feedback-icon">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="feedback-text">
                                <strong>Dalam Antrean Peninjauan</strong>
                                <span>Laporan ini belum diverifikasi. Petugas PDS akan segera meninjau bukti dan menentukan poin pelanggaran.</span>
                            </div>
                        </div>
                    @endif

                </div>

            @empty

                <div class="walas-empty-state">
                    <div class="empty-icon">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                    <h3>Belum Ada Laporan</h3>
                    <p>Anda belum pernah mengirim laporan pelanggaran. Gunakan formulir di halaman Lapor untuk melapor.</p>
                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection

@section('styles')
/* =========================================================
   CONTAINER & LAYOUT
========================================================= */
.walas-container {
    width: 100%;
    max-width: 100%;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* =========================================================
   CARD & SECTIONS
========================================================= */
.walas-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 32px 36px;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
}

.walas-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f3f4f6;
}

.walas-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.walas-card-header h2 {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 4px;
}

.walas-card-header p {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

/* Filter Tabs */
.walas-filter-tabs {
    display: flex;
    gap: 8px;
}

.tab-btn {
    padding: 8px 16px;
    border: none;
    background: #f3f4f6;
    color: #4b5563;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.tab-btn:hover {
    background: #e5e7eb;
}

.tab-btn.active {
    background: #111827;
    color: #ffffff;
}

/* Reports List */
.walas-reports-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.report-item {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 20px;
    background: #ffffff;
    transition: transform 0.2s, box-shadow 0.2s;
}

.report-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px dashed #e5e7eb;
}

.report-student {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.student-name {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 2px;
}

.student-class {
    font-size: 12px;
    color: #6b7280;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge-pending {
    background: #fef3c7;
    color: #d97706;
}

.badge-verified {
    background: #dcfce7;
    color: #16a34a;
}

.badge-rejected {
    background: #fee2e2;
    color: #dc2626;
}

.report-body {
    margin-bottom: 16px;
}

.report-meta-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}

.meta-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #4b5563;
    background: #f3f4f6;
    padding: 6px 10px;
    border-radius: 8px;
}

.meta-violation {
    background: #fef2f2;
    color: #991b1b;
    font-weight: 600;
}

.report-description {
    font-size: 14px;
    color: #4b5563;
    line-height: 1.6;
    margin: 0 0 16px;
    background: #f9fafb;
    padding: 12px 16px;
    border-radius: 8px;
    border-left: 3px solid #d1d5db;
}

.report-photo {
    margin-top: 10px;
}

.report-photo img {
    max-width: 140px;
    max-height: 140px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    object-fit: cover;
}

.report-photo a {
    display: inline-block;
    text-decoration: none;
    position: relative;
}

.photo-hint {
    display: block;
    font-size: 10px;
    color: #6b7280;
    margin-top: 4px;
}

/* Feedback / Response Footer */
.report-feedback {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 12px;
}

.feedback-verified {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.feedback-rejected {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.feedback-pending {
    background: #fefce8;
    border: 1px solid #fef08a;
    color: #854d0e;
}

.feedback-icon {
    font-size: 16px;
    margin-top: 2px;
}

.feedback-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.points-badge {
    display: inline-block;
    background: #dc2626;
    color: #ffffff;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    margin-top: 4px;
    width: fit-content;
}

.feedback-note {
    font-style: italic;
    margin: 4px 0 0;
    font-size: 11px;
    opacity: 0.9;
}

/* Empty State */
.walas-empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f3f4f6;
    color: #9ca3af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 14px;
}

.walas-empty-state h3 {
    font-size: 16px;
    color: #111827;
    margin: 0 0 6px;
}

.walas-empty-state p {
    font-size: 13px;
    color: #6b7280;
    max-width: 400px;
    margin: 0 auto;
}

/* Responsive */
@media (max-width: 600px) {
    .walas-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .walas-filter-tabs {
        width: 100%;
        overflow-x: auto;
    }
}
@endsection


@section('scripts')
<script>
function filterReports(status, btn) {
    // update active tab
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // filter items
    const items = document.querySelectorAll('.report-item');
    items.forEach(item => {
        const itemStatus = item.getAttribute('data-status');
        if (status === 'all' || itemStatus === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endsection
