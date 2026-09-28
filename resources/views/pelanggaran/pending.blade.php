@extends('layouts.app')

@section('title', 'Pending Laporan')
@section('page_title', 'Pending Laporan Masuk')

@section('content')

<div class="pelanggaran-page">

    {{-- HEADER --}}
    <div class="page-header">

        <div class="page-heading">
            <h1>Pending Laporan</h1>
            <p>Laporan pelanggaran yang membutuhkan verifikasi Anda</p>
        </div>

    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif


    {{-- ===========================
         PENDING LAPORAN MASUK
    ============================ --}}

    @if($pendingLaporans->count() > 0)

    <div class="pending-section">

        <div class="pending-header">

            <div class="pending-title">
                <i class="fa-solid fa-hourglass-half"></i>
                <h2>Pending Laporan Masuk</h2>
                <span class="pending-count">{{ $pendingLaporans->count() }}</span>
            </div>

            <p>Tinjau dan verifikasi laporan berikut sebelum diterapkan ke rekam jejak siswa.</p>

        </div>

        <div class="pending-cards">

            @foreach($pendingLaporans as $pending)

            <div class="pending-card">

                <div class="pending-card-top">

                    <div class="pending-student">
                        <strong>{{ $pending->siswa->nama }}</strong>
                        <small>{{ $pending->siswa->kelas }}</small>
                    </div>

                    <span class="pending-badge">
                        <i class="fa-solid fa-clock"></i>
                        Pending
                    </span>

                </div>

                <div class="pending-card-body">

                    <div class="pending-meta">

                        <span>
                            <i class="fa-regular fa-calendar"></i>
                            {{ \Carbon\Carbon::parse($pending->tanggal)->format('d/m/Y') }}
                        </span>

                        @if($pending->pelapor)
                        <span>
                            <i class="fa-solid fa-user"></i>
                            {{ $pending->pelapor->name }} ({{ $pending->pelapor->role_label }})
                        </span>
                        @endif

                    </div>

                    <div class="pending-violation-label">
                        <span class="badge-danger">{{ $pending->jenis_pelanggaran }}</span>
                    </div>

                    @if($pending->keterangan)
                    <p class="pending-desc">{{ $pending->keterangan }}</p>
                    @endif

                    @if($pending->foto_bukti)
                    <img src="{{ asset('storage/' . $pending->foto_bukti) }}"
                         alt="Bukti"
                         class="pending-photo">
                    @endif

                </div>

                <div class="pending-card-actions">

                    <button type="button"
                            class="btn-approve"
                            onclick="openApproveModal({{ $pending->id }}, '{{ addslashes($pending->siswa->nama) }}', '{{ addslashes($pending->jenis_pelanggaran) }}')">
                        <i class="fa-solid fa-check"></i>
                        Verifikasi
                    </button>

                    <button type="button"
                            class="btn-reject"
                            onclick="openRejectModal({{ $pending->id }}, '{{ addslashes($pending->siswa->nama) }}')">
                        <i class="fa-solid fa-xmark"></i>
                        Tolak
                    </button>

                </div>

            </div>

            @endforeach

        </div>

    </div>

    @else
        <div style="background: white; padding: 40px; border-radius: 16px; text-align: center; border: 1px solid #e5e7eb;">
            <i class="fa-solid fa-inbox" style="font-size: 40px; color: #9ca3af; margin-bottom: 15px;"></i>
            <h3 style="font-size: 18px; color: #111827; margin: 0 0 5px;">Tidak Ada Laporan Pending</h3>
            <p style="color: #6b7280; font-size: 14px; margin: 0;">Semua laporan masuk telah diproses.</p>
        </div>
    @endif


    <!-- MODAL VERIFIKASI (APPROVE) -->
    <div class="modal-overlay" id="approveModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Verifikasi Laporan Pelanggaran</h3>
                <button type="button" onclick="closeModal('approveModal')" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="approveForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p style="font-size:13px; margin-bottom:15px;">Siswa: <strong id="approveSiswaName"></strong><br>
                       Pelanggaran Dilaporkan: <strong id="approvePelanggaran" style="color:red;"></strong>
                    </p>

                    <div class="form-group">
                        <label class="form-label">Aturan Pelanggaran Terkait <span style="color:red">*</span></label>
                        <select name="aturan_pelanggaran_id" class="form-control" onchange="toggleCustomApprove(this.value)" required>
                            <option value="">-- Pilih Sesuai Pedoman Tata Tertib --</option>
                            @foreach($aturanPelanggarans as $aturan)
                                <option value="{{ $aturan->id }}">{{ $aturan->kode }} - {{ $aturan->nama }} (-{{ $aturan->poin }} poin)</option>
                            @endforeach
                            <option value="custom">-- Pelanggaran Lainnya (Custom) --</option>
                        </select>
                    </div>

                    <div id="customApproveDiv" style="display:none; margin-top:10px;">
                        <div class="form-group">
                            <label class="form-label">Nama Pelanggaran</label>
                            <input type="text" name="jenis_pelanggaran_custom" class="form-control" placeholder="Contoh: Bermain judi online di kelas">
                        </div>
                        <div class="form-group" style="margin-top:10px;">
                            <label class="form-label">Poin Sanksi</label>
                            <input type="number" name="poin_custom" class="form-control" min="0" placeholder="Misal: 20">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Catatan Verifikasi (Opsional)</label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Pesan kepada siswa atau pelapor terkait keputusan ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('approveModal')">Batal</button>
                    <button type="submit" class="btn-primary">Verifikasi & Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- REJECT MODAL -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tolak Laporan Pelanggaran</h3>
                <button type="button" onclick="closeModal('rejectModal')" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p style="font-size:13px; margin-bottom:15px;">Anda akan menolak laporan untuk siswa: <strong id="rejectSiswaName"></strong>.</p>
                    <div class="form-group">
                        <label class="form-label">Alasan Penolakan <span style="color:red">*</span></label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan (misal: Bukti tidak cukup kuat, laporan ganda, dll)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('rejectModal')">Batal</button>
                    <button type="submit" class="btn-danger">Tolak Laporan</button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection


@section('styles')
.pelanggaran-page{
    width:100%;
    max-width:100%;
    overflow:hidden;
}

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:20px;
}

.page-heading h1{
    font-size:34px;
    color:var(--color-primary-gray);
    margin-bottom:5px;
    line-height:1.2;
}

.page-heading p{
    color:#6b7280;
    font-size:14px;
}

.alert-success{
    display:flex;
    align-items:center;
    gap:10px;
    background:#dcfce7;
    color:#166534;
    padding:14px 16px;
    border-radius:12px;
    margin-bottom:18px;
    font-size:14px;
}

/* PENDING SECTION */
.pending-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 5px 20px rgba(0,0,0,.03);
}

.pending-header {
    margin-bottom: 20px;
    border-bottom: 1px solid #f3f4f6;
    padding-bottom: 16px;
}

.pending-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.pending-title i {
    color: #d97706;
    font-size: 20px;
}

.pending-title h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #111827;
}

.pending-count {
    background: #fef3c7;
    color: #d97706;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
}

.pending-header p {
    color: #6b7280;
    font-size: 13px;
    margin: 6px 0 0 32px;
}

.pending-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.pending-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    background: #f9fafb;
    display: flex;
    flex-direction: column;
}

.pending-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px dashed #e5e7eb;
}

.pending-student strong {
    display: block;
    color: #111827;
    font-size: 14px;
    margin-bottom: 2px;
}

.pending-student small {
    color: #6b7280;
    font-size: 12px;
}

.pending-badge {
    background: #fef3c7;
    color: #d97706;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.pending-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 12px;
}

.pending-meta span {
    font-size: 12px;
    color: #4b5563;
    display: flex;
    align-items: center;
    gap: 6px;
}

.pending-violation-label {
    margin-bottom: 10px;
}

.pending-violation-label .badge-danger, .badge-danger {
    background: #fef2f2;
    color: #991b1b;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.pending-desc {
    font-size: 12px;
    color: #4b5563;
    line-height: 1.5;
    background: white;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    margin-bottom: 12px;
}

.pending-photo {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    margin-bottom: 16px;
}

.pending-card-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
}

.btn-approve {
    flex: 1;
    background: #16a34a;
    color: white;
    border: none;
    padding: 8px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: 0.2s;
}

.btn-approve:hover {
    background: #15803d;
}

.btn-reject {
    flex: 1;
    background: #dc2626;
    color: white;
    border: none;
    padding: 8px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: 0.2s;
}

.btn-reject:hover {
    background: #b91c1c;
}

/* =====================================================
   MODAL STYLES
===================================================== */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
}
.modal-overlay.active {
    display: flex;
}
.modal-content {
    background: #fff;
    width: 100%;
    max-width: 500px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    animation: modalSlideUp 0.3s ease;
}
@keyframes modalSlideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header h3 { margin: 0; font-size: 18px; color: #111827; }
.close-btn {
    background: none; border: none; font-size: 20px; color: #9ca3af; cursor: pointer;
}
.close-btn:hover { color: #111827; }
.modal-body { padding: 20px; }
.modal-footer {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.form-group { margin-bottom: 15px; }
.form-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #374151; }
.form-control {
    width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px;
    font-size: 14px;
}
.form-control:focus { outline: none; border-color: #d97706; box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1); }
.btn-secondary {
    background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;
}
.btn-primary {
    background: #16a34a; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;
}
.btn-danger {
    background: #dc2626; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;
}
@endsection

@section('scripts')
<script>
function openApproveModal(id, studentName, pelanggaranDesc) {
    document.getElementById('approveSiswaName').innerText = studentName;
    document.getElementById('approvePelanggaran').innerText = pelanggaranDesc;
    document.getElementById('approveForm').action = "/pelanggaran/" + id + "/approve";
    document.getElementById('approveModal').classList.add('active');
}

function openRejectModal(id, studentName) {
    document.getElementById('rejectSiswaName').innerText = studentName;
    document.getElementById('rejectForm').action = "/pelanggaran/" + id + "/reject";
    document.getElementById('rejectModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function toggleCustomApprove(val) {
    const customDiv = document.getElementById('customApproveDiv');
    if (val === 'custom') {
        customDiv.style.display = 'block';
        customDiv.querySelectorAll('input').forEach(el => el.setAttribute('required', 'true'));
    } else {
        customDiv.style.display = 'none';
        customDiv.querySelectorAll('input').forEach(el => el.removeAttribute('required'));
    }
}
</script>
@endsection
