@extends('layouts.app')

@section('title', 'Pending Laporan')
@section('page_title', 'Pending Laporan')

@section('content')

<div class="pending-page">

    {{-- HEADER --}}
    <div class="pd-header">

        <div class="pd-heading">
            <h1>Pending Laporan</h1>
            <p>Laporan pelanggaran yang menunggu verifikasi Anda</p>
        </div>

        @if($pendingLaporans->isNotEmpty())
        <div class="pd-counter">
            <i class="fa-solid fa-hourglass-half"></i>
            <strong>{{ $pendingLaporans->count() }}</strong>
            <span>menunggu</span>
        </div>
        @endif

    </div>

    {{-- Catatan: flash session('success') / 'error' dan ringkasan
         $errors sudah otomatis ditampilkan oleh layouts/app.blade.php,
         jadi tidak perlu diulang di sini. --}}


    @if($pendingLaporans->isNotEmpty())

    {{-- TOOLBAR: INFO + CARI --}}
    <div class="pd-toolbar">

        <div class="pd-tip">
            <i class="fa-solid fa-circle-info"></i>
            <p>Tinjau dan verifikasi laporan berikut sebelum diterapkan ke rekam jejak siswa.</p>
        </div>

        <div class="pd-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
                type="search"
                id="pdSearch"
                placeholder="Cari siswa, pelanggaran, atau pelapor..."
                autocomplete="off"
            >
        </div>

    </div>

    <div class="pd-result-info">
        Menampilkan <strong id="pdCount">{{ $pendingLaporans->count() }}</strong>
        dari {{ $pendingLaporans->count() }} laporan
    </div>

    <div class="pd-cards">

        @foreach($pendingLaporans as $pending)

            @php
                /*
                 * Nama siswa bisa mengandung tanda kutip, backslash,
                 * atau newline. Menempelkannya langsung ke atribut
                 * onclick akan merusak JavaScript, jadi simpan di
                 * atribut data-* (Blade meng-escape otomatis) dan
                 * dibaca lewat dataset.
                 */
                $namaSiswaPending = $pending->siswa?->nama ?? 'Siswa tidak dipilih';
                $namaTampil       = $pending->siswa?->nama ?? 'Laporan Umum (Tanpa Siswa)';
                $katSlug          = \Illuminate\Support\Str::slug($pending->kategori ?? '');
                $haystack         = \Illuminate\Support\Str::lower(
                    $namaTampil . ' ' .
                    ($pending->siswa?->kelas ?? '') . ' ' .
                    ($pending->jenis_pelanggaran ?? '') . ' ' .
                    ($pending->pelapor?->name ?? '')
                );
            @endphp

            <article class="pd-card js-item" data-search="{{ $haystack }}">

                {{-- KEPALA --}}
                <header class="pd-card-top">

                    <div class="pd-student">
                        <div class="pd-avatar">
                            @if($pending->siswa)
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($pending->siswa->nama, 0, 1)) }}
                            @else
                                <i class="fa-solid fa-flag"></i>
                            @endif
                        </div>
                        <div class="pd-student-text">
                            <strong>{{ $namaTampil }}</strong>
                            <small>{{ $pending->siswa?->kelas ?? 'Tanpa kelas' }}</small>
                        </div>
                    </div>

                    <span class="pd-status">
                        <i class="fa-solid fa-clock"></i>
                        Pending
                    </span>

                </header>

                {{-- ISI --}}
                <div class="pd-card-body">

                    <dl class="pd-meta">

                        <div class="pd-meta-item">
                            <dt><i class="fa-regular fa-calendar"></i> Tanggal</dt>
                            <dd>{{ \Carbon\Carbon::parse($pending->tanggal)->format('d/m/Y') }}</dd>
                        </div>

                        <div class="pd-meta-item">
                            <dt><i class="fa-regular fa-clock"></i> Dilaporkan</dt>
                            <dd>{{ \Carbon\Carbon::parse($pending->created_at)->format('H:i') }}</dd>
                        </div>

                        <div class="pd-meta-item pd-meta-wide">
                            <dt><i class="fa-solid fa-user"></i> Pelapor</dt>
                            <dd>
                                @if($pending->pelapor)
                                    {{ $pending->pelapor->name }}
                                    <span class="pd-role">({{ $pending->pelapor->role_label }})</span>
                                @else
                                    <span class="pd-muted">Tidak diketahui</span>
                                @endif
                            </dd>
                        </div>

                    </dl>

                    <div class="pd-violation">
                        <div class="pd-jenis">{{ $pending->jenis_pelanggaran ?? 'Tidak ditentukan' }}</div>

                        <div class="pd-chips">
                            @if($pending->kategori)
                                <span class="pd-kat pd-kat-{{ $katSlug }}">{{ $pending->kategori }}</span>
                            @endif

                            @if($pending->poin)
                                <span class="pd-poin">-{{ $pending->poin }} Poin</span>
                            @endif
                        </div>
                    </div>

                    @if($pending->keterangan)
                        <p class="pd-desc" title="{{ $pending->keterangan }}">{{ $pending->keterangan }}</p>
                    @else
                        <p class="pd-desc pd-desc-empty">Tidak ada keterangan tambahan.</p>
                    @endif

                    @if($pending->foto_bukti)
                        <button type="button"
                                class="pd-photo js-zoom"
                                data-src="{{ asset('storage/' . $pending->foto_bukti) }}"
                                title="Klik untuk memperbesar">
                            <img src="{{ asset('storage/' . $pending->foto_bukti) }}"
                                 alt="Bukti foto pelanggaran {{ $pending->jenis_pelanggaran ?? '' }}"
                                 loading="lazy">
                            <span class="pd-photo-hint"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                        </button>
                    @endif

                </div>

                {{-- LINK RINCIAN --}}
                <a href="{{ route('pelanggaran.show', ['pelanggaran' => $pending->id, 'from' => 'pending']) }}"
                   class="pd-detail-link">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Lihat Rincian Laporan</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                {{-- AKSI --}}
                <footer class="pd-actions">

                    <button type="button"
                            class="pd-btn pd-btn-approve js-approve"
                            data-id="{{ $pending->id }}"
                            data-siswa="{{ $namaSiswaPending }}"
                            data-pelanggaran="{{ $pending->jenis_pelanggaran ?? '-' }}"
                            data-aturan-id="{{ $pending->aturan_pelanggaran_id ?? '' }}">
                        <i class="fa-solid fa-check"></i>
                        Verifikasi
                    </button>

                    <button type="button"
                            class="pd-btn pd-btn-reject js-reject"
                            data-id="{{ $pending->id }}"
                            data-siswa="{{ $namaSiswaPending }}">
                        <i class="fa-solid fa-xmark"></i>
                        Tolak
                    </button>

                </footer>

            </article>

        @endforeach

    </div>

    {{-- Muncul saat pencarian tidak menemukan hasil --}}
    <div class="pd-empty is-hidden" id="pdNoResult">
        <i class="fa-solid fa-magnifying-glass"></i>
        <h3>Tidak Ada Hasil</h3>
        <p>Tidak ada laporan yang cocok dengan pencarian Anda.</p>
    </div>

    @else

        <div class="pd-empty">
            <i class="fa-solid fa-circle-check pd-empty-ok"></i>
            <h3>Tidak Ada Laporan Pending</h3>
            <p>Semua laporan masuk sudah diproses. Tidak ada yang perlu diverifikasi saat ini.</p>
        </div>

    @endif


    {{-- LIGHTBOX FOTO --}}
    <div class="pd-lightbox is-hidden" id="pdLightbox">
        <button type="button" class="pd-lightbox-close" id="pdLightboxClose" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <img src="" alt="Foto Bukti" id="pdLightboxImg">
    </div>


    <!-- MODAL VERIFIKASI (APPROVE) -->
    <div class="pd-modal" id="approveModal" role="dialog" aria-modal="true" aria-labelledby="approveModalTitle">
        <div class="pd-modal-content">

            <div class="pd-modal-header">
                <h3 id="approveModalTitle">Verifikasi Laporan Pelanggaran</h3>
                <button type="button" data-close-modal="approveModal" class="pd-close" aria-label="Tutup modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="approveForm" method="POST" action="">
                @csrf

                <div class="pd-modal-body">

                    <div id="approveFormErrors" class="pd-alert-danger is-hidden" role="alert">
                        <ul id="approveFormErrorsList"></ul>
                    </div>

                    <div class="pd-summary">
                        <p><span>Siswa</span><strong id="approveSiswaName">-</strong></p>
                        <p><span>Pelanggaran</span><strong id="approvePelanggaran" class="is-danger">-</strong></p>
                    </div>

                    <div class="pd-field">
                        <label class="pd-label" for="approveAturan">
                            Aturan Pelanggaran Terkait <span class="pd-req">*</span>
                        </label>
                        <select name="aturan_pelanggaran_id" id="approveAturan" class="pd-input" required>
                            <option value="">-- Pilih Sesuai Pedoman Tata Tertib --</option>
                            @foreach($aturanPelanggarans as $aturan)
                                <option value="{{ $aturan->id }}">{{ $aturan->kode }} - {{ $aturan->nama }} (-{{ $aturan->poin }} poin)</option>
                            @endforeach
                            <option value="custom">-- Pelanggaran Lainnya (Custom) --</option>
                        </select>
                    </div>

                    <div id="customApproveDiv" class="pd-custom is-hidden">

                        <div class="pd-field">
                            <label class="pd-label" for="approveCustomNama">Nama Pelanggaran</label>
                            <input type="text" name="jenis_pelanggaran_custom" id="approveCustomNama"
                                   class="pd-input" placeholder="Contoh: Bermain judi online di kelas">
                        </div>

                        <div class="pd-row">
                            <div class="pd-field">
                                <label class="pd-label" for="approveCustomKategori">Kategori</label>
                                <select name="kategori" id="approveCustomKategori" class="pd-input">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Ringan">Ringan</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Berat">Berat</option>
                                    <option value="Luar Biasa">Luar Biasa</option>
                                </select>
                            </div>

                            <div class="pd-field">
                                <label class="pd-label" for="approveCustomPoin">Poin Sanksi</label>
                                <input type="number" name="poin_custom" id="approveCustomPoin"
                                       class="pd-input" min="0" placeholder="Misal: 20">
                            </div>
                        </div>

                    </div>

                    <div class="pd-field">
                        <label class="pd-label" for="approveCatatan">Catatan Verifikasi (Opsional)</label>
                        <textarea name="catatan_verifikasi" id="approveCatatan" class="pd-input" rows="3"
                                  placeholder="Pesan kepada siswa atau pelapor terkait keputusan ini..."></textarea>
                    </div>

                </div>

                <div class="pd-modal-footer">
                    <button type="button" class="pd-btn pd-btn-ghost" data-close-modal="approveModal">Batal</button>
                    <button type="submit" class="pd-btn pd-btn-approve">Verifikasi &amp; Terapkan</button>
                </div>
            </form>

        </div>
    </div>

    <!-- REJECT MODAL -->
    <div class="pd-modal" id="rejectModal" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle">
        <div class="pd-modal-content">

            <div class="pd-modal-header">
                <h3 id="rejectModalTitle">Tolak Laporan Pelanggaran</h3>
                <button type="button" data-close-modal="rejectModal" class="pd-close" aria-label="Tutup modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="rejectForm" method="POST" action="">
                @csrf

                <div class="pd-modal-body">

                    <div id="rejectFormErrors" class="pd-alert-danger is-hidden" role="alert">
                        <ul id="rejectFormErrorsList"></ul>
                    </div>

                    <div class="pd-summary">
                        <p><span>Siswa</span><strong id="rejectSiswaName">-</strong></p>
                    </div>

                    <div class="pd-field">
                        <label class="pd-label" for="rejectCatatan">
                            Alasan Penolakan <span class="pd-req">*</span>
                        </label>
                        <textarea name="catatan_verifikasi" id="rejectCatatan" class="pd-input" rows="3"
                                  placeholder="Jelaskan alasan penolakan (misal: Bukti tidak cukup kuat, laporan ganda, dll)"
                                  required></textarea>
                    </div>

                </div>

                <div class="pd-modal-footer">
                    <button type="button" class="pd-btn pd-btn-ghost" data-close-modal="rejectModal">Batal</button>
                    <button type="submit" class="pd-btn pd-btn-danger">Tolak Laporan</button>
                </div>
            </form>

        </div>
    </div>

</div>{{-- /.pending-page --}}

@endsection


@section('styles')
@include('partials.ui')

/* =====================================================
   PENDING LAPORAN
   Semua di-scope ke .pending-page dengan prefix "pd-"
   supaya tidak bentrok dengan partials/ui.blade.
===================================================== */

.pending-page{
    width:100%;
    max-width:100%;
}

.pending-page a{
    text-decoration:none;
}

.pending-page .is-hidden{
    display:none !important;
}

.pending-page .pd-muted{
    color:#9ca3af;
    font-style:italic;
}


/* ---------- HEADER ---------- */

.pending-page .pd-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.pending-page .pd-heading h1{
    margin:0;
    font-size:28px;
    font-weight:800;
    color:#111827;
    letter-spacing:-.3px;
}

.pending-page .pd-heading p{
    margin:4px 0 0;
    font-size:14px;
    color:#6b7280;
}

.pending-page .pd-counter{
    display:inline-flex;
    align-items:center;
    gap:8px;

    padding:9px 16px;

    background:#fffbeb;
    border:1px solid #fde68a;
    border-radius:999px;

    color:#92400e;
    font-size:13px;
    white-space:nowrap;
}

.pending-page .pd-counter i{ color:#d97706; }
.pending-page .pd-counter strong{ font-size:16px; font-weight:800; }


/* ---------- TOOLBAR ---------- */

.pending-page .pd-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:10px;
}

.pending-page .pd-tip{
    display:flex;
    align-items:flex-start;
    gap:10px;

    flex:1 1 320px;
    min-width:0;

    padding:11px 14px;

    background:#fffbeb;
    border:1px solid #fde68a;
    border-radius:12px;

    color:#92400e;
    font-size:13px;
    line-height:1.5;
}

.pending-page .pd-tip i{
    margin-top:2px;
    color:#d97706;
    flex-shrink:0;
}

.pending-page .pd-tip p{
    margin:0;
    overflow-wrap:anywhere;
}

.pending-page .pd-search{
    position:relative;
    flex:0 1 340px;
    min-width:220px;
}

.pending-page .pd-search i{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:#9ca3af;
    font-size:13px;
    pointer-events:none;
}

.pending-page .pd-search input{
    width:100%;
    height:44px;
    padding:0 14px 0 38px;

    background:#fff;
    color:#111827;

    border:1px solid #e5e7eb;
    border-radius:12px;

    font-size:13px;
    font-family:inherit;

    outline:none;
    transition:.2s;
}

.pending-page .pd-search input:focus{
    border-color:var(--primary, #d97706);
    box-shadow:0 0 0 3px rgba(217,119,6,.15);
}

.pending-page .pd-result-info{
    margin:0 2px 14px;
    color:#6b7280;
    font-size:12px;
}

.pending-page .pd-result-info strong{ color:#111827; }


/* ---------- GRID KARTU ---------- */

.pending-page .pd-cards{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(min(320px, 100%), 1fr));
    gap:16px;
    align-items:stretch;
}

.pending-page .pd-card{
    display:flex;
    flex-direction:column;
    min-width:0;

    background:#fff;
    border:1px solid #eee7e5;
    border-radius:16px;

    padding:16px;

    box-shadow:0 4px 14px rgba(0,0,0,.04);
    transition:box-shadow .2s ease, border-color .2s ease, transform .2s ease;
}

.pending-page .pd-card:hover{
    border-color:#fcd34d;
    box-shadow:0 10px 24px rgba(0,0,0,.08);
    transform:translateY(-2px);
}

.pending-page .pd-card[hidden]{
    display:none !important;
}


/* ---------- KEPALA KARTU ---------- */

.pending-page .pd-card-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:10px;

    padding-bottom:12px;
    margin-bottom:14px;

    border-bottom:1px dashed #e5e7eb;
}

.pending-page .pd-student{
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
}

.pending-page .pd-avatar{
    flex-shrink:0;

    display:flex;
    align-items:center;
    justify-content:center;

    width:40px;
    height:40px;

    border-radius:50%;

    background:#fef3c7;
    color:#b45309;

    font-size:15px;
    font-weight:700;
}

.pending-page .pd-student-text{
    display:flex;
    flex-direction:column;
    min-width:0;
}

.pending-page .pd-student-text strong{
    color:#111827;
    font-size:14px;
    font-weight:700;
    line-height:1.35;
    overflow-wrap:anywhere;
}

.pending-page .pd-student-text small{
    margin-top:2px;
    color:#6b7280;
    font-size:12px;
}

.pending-page .pd-status{
    flex-shrink:0;

    display:inline-flex;
    align-items:center;
    gap:5px;

    padding:5px 10px;

    background:#fef3c7;
    color:#b45309;

    border-radius:999px;

    font-size:11px;
    font-weight:700;
    white-space:nowrap;
}


/* ---------- ISI KARTU ---------- */

.pending-page .pd-card-body{
    display:flex;
    flex-direction:column;
    flex:1 1 auto;
    min-width:0;
}

.pending-page .pd-meta{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:10px 12px;

    margin:0 0 14px;
    padding-bottom:14px;

    border-bottom:1px solid #f3f4f6;
}

.pending-page .pd-meta-item{ min-width:0; }
.pending-page .pd-meta-wide{ grid-column:1 / -1; }

.pending-page .pd-meta dt{
    display:flex;
    align-items:center;
    gap:5px;

    margin-bottom:2px;

    color:#9ca3af;
    font-size:11px;
    font-weight:700;
    letter-spacing:.03em;
    text-transform:uppercase;
}

.pending-page .pd-meta dt i{
    color:#d97706;
    font-size:11px;
}

.pending-page .pd-meta dd{
    margin:0;
    color:#374151;
    font-size:13px;
    overflow-wrap:anywhere;
}

.pending-page .pd-role{
    color:#6b7280;
    font-size:12px;
}


/* ---------- PELANGGARAN ---------- */

.pending-page .pd-violation{
    display:flex;
    flex-direction:column;
    gap:8px;
    margin-bottom:12px;
}

.pending-page .pd-jenis{
    color:#991b1b;
    font-size:13px;
    font-weight:600;
    line-height:1.5;
    overflow-wrap:anywhere;
}

.pending-page .pd-chips{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:6px;
}

.pending-page .pd-kat{
    display:inline-block;
    padding:3px 10px;

    background:#e0e7ff;
    color:#3730a3;

    border-radius:999px;

    font-size:11px;
    font-weight:700;
    white-space:nowrap;
}

.pending-page .pd-kat-ringan{ background:#dcfce7; color:#166534; }
.pending-page .pd-kat-sedang{ background:#fef3c7; color:#92400e; }
.pending-page .pd-kat-berat{ background:#fee2e2; color:#991b1b; }
.pending-page .pd-kat-luar-biasa{ background:#1f2937; color:#fff; }

.pending-page .pd-poin{
    display:inline-block;
    padding:3px 10px;

    background:#fff7ed;
    color:#9a3412;

    border-radius:999px;

    font-size:11px;
    font-weight:700;
    white-space:nowrap;
}

.pending-page .pd-desc{
    margin:0 0 12px;
    padding:10px 12px;

    background:#faf7f6;
    border:1px solid #f0e8e6;
    border-left:3px solid #fcd34d;
    border-radius:8px;

    color:#4b5563;
    font-size:12px;
    line-height:1.6;
    overflow-wrap:anywhere;

    display:-webkit-box;
    -webkit-line-clamp:3;
    -webkit-box-orient:vertical;
    overflow:hidden;
}

.pending-page .pd-desc-empty{
    color:#9ca3af;
    font-style:italic;
}


/* ---------- FOTO ---------- */

.pending-page .pd-photo{
    position:relative;
    display:block;

    width:100%;
    padding:0;
    margin:0 0 14px;

    border:1px solid #e5e7eb;
    border-radius:12px;

    background:#f3f4f6;

    overflow:hidden;
    cursor:zoom-in;
}

.pending-page .pd-photo img{
    display:block;
    width:100%;
    aspect-ratio:16 / 9;
    object-fit:cover;
    transition:transform .3s ease;
}

.pending-page .pd-photo:hover img{
    transform:scale(1.04);
}

.pending-page .pd-photo-hint{
    position:absolute;
    right:8px;
    bottom:8px;

    display:flex;
    align-items:center;
    justify-content:center;

    width:30px;
    height:30px;

    border-radius:50%;

    background:rgba(17,24,39,.65);
    color:#fff;

    font-size:12px;
}


/* ---------- LINK RINCIAN ---------- */

.pending-page .pd-detail-link{
    display:flex;
    align-items:center;
    gap:8px;

    margin:0 0 12px;
    padding:10px 12px;

    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:10px;

    color:#4b5563;
    font-size:12px;
    font-weight:600;

    transition:.18s ease;
}

.pending-page .pd-detail-link span{ flex:1; }

.pending-page .pd-detail-link:hover{
    background:#eef2ff;
    border-color:#c7d2fe;
    color:#3730a3;
}


/* ---------- AKSI ---------- */

.pending-page .pd-actions{
    display:flex;
    gap:8px;
    margin-top:auto;
}

.pending-page .pd-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;

    min-height:42px;
    padding:0 16px;

    border:1px solid transparent;
    border-radius:10px;

    font-size:13px;
    font-weight:600;
    font-family:inherit;
    line-height:1;
    white-space:nowrap;

    cursor:pointer;
    transition:.2s;
}

.pending-page .pd-actions .pd-btn{
    flex:1 1 0;
    min-width:0;
}

.pending-page .pd-btn-approve{
    background:#16a34a;
    border-color:#16a34a;
    color:#fff;
}

.pending-page .pd-btn-approve:hover{
    background:#15803d;
    border-color:#15803d;
}

.pending-page .pd-btn-reject{
    background:#fff;
    border-color:#fecaca;
    color:#b91c1c;
}

.pending-page .pd-btn-reject:hover{
    background:#fef2f2;
    border-color:#fca5a5;
}

.pending-page .pd-btn-danger{
    background:#dc2626;
    border-color:#dc2626;
    color:#fff;
}

.pending-page .pd-btn-danger:hover{
    background:#b91c1c;
    border-color:#b91c1c;
}

.pending-page .pd-btn-ghost{
    background:#fff;
    border-color:#e5e7eb;
    color:#4b5563;
}

.pending-page .pd-btn-ghost:hover{
    background:#f3f4f6;
}

.pending-page .pd-btn:focus-visible,
.pending-page .pd-close:focus-visible{
    outline:2px solid #d97706;
    outline-offset:2px;
}


/* ---------- EMPTY STATE ---------- */

.pending-page .pd-empty{
    background:#fff;
    border:1px solid #eee7e5;
    border-radius:16px;

    padding:56px 24px;
    text-align:center;
}

.pending-page .pd-empty i{
    display:block;
    margin-bottom:12px;
    font-size:32px;
    color:#9ca3af;
}

.pending-page .pd-empty .pd-empty-ok{ color:#10b981; }

.pending-page .pd-empty h3{
    margin:0 0 6px;
    font-size:17px;
    color:#111827;
}

.pending-page .pd-empty p{
    max-width:42ch;
    margin:0 auto;
    color:#6b7280;
    font-size:13px;
    overflow-wrap:anywhere;
}


/* ---------- LIGHTBOX ---------- */

.pending-page .pd-lightbox{
    position:fixed;
    inset:0;
    z-index:10000;

    display:flex;
    align-items:center;
    justify-content:center;

    padding:24px;
    background:rgba(17,24,39,.82);
}

.pending-page .pd-lightbox img{
    max-width:min(92vw, 900px);
    max-height:86vh;

    border-radius:14px;
    background:#fff;

    box-shadow:0 20px 60px rgba(0,0,0,.5);
}

.pending-page .pd-lightbox-close{
    position:absolute;
    top:18px;
    right:18px;

    width:42px;
    height:42px;

    border:none;
    border-radius:50%;

    background:rgba(255,255,255,.95);
    color:#111827;

    font-size:16px;
    cursor:pointer;
}


/* ---------- MODAL ---------- */

.pending-page .pd-modal{
    position:fixed;
    inset:0;
    z-index:9999;

    display:none;
    align-items:center;
    justify-content:center;

    padding:16px;

    background:rgba(0,0,0,.5);
    backdrop-filter:blur(4px);
}

.pending-page .pd-modal.active{
    display:flex;
}

.pending-page .pd-modal-content{
    width:100%;
    max-width:500px;
    max-height:92vh;

    overflow-y:auto;
    -webkit-overflow-scrolling:touch;

    background:#fff;
    border-radius:16px;

    box-shadow:0 20px 50px rgba(0,0,0,.25);

    animation:pdSlideUp .3s ease;
}

@keyframes pdSlideUp{
    from{ transform:translateY(20px); opacity:0; }
    to{ transform:translateY(0); opacity:1; }
}

.pending-page .pd-modal-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;

    padding:16px 20px;

    border-bottom:1px solid #e5e7eb;
}

.pending-page .pd-modal-header h3{
    margin:0;
    min-width:0;
    color:#111827;
    font-size:16px;
    font-weight:700;
    overflow-wrap:anywhere;
}

.pending-page .pd-close{
    flex-shrink:0;

    display:flex;
    align-items:center;
    justify-content:center;

    width:34px;
    height:34px;

    background:none;
    border:none;
    border-radius:8px;

    color:#9ca3af;
    font-size:18px;
    cursor:pointer;
}

.pending-page .pd-close:hover{
    background:#f3f4f6;
    color:#111827;
}

.pending-page .pd-modal-body{
    padding:20px;
}

.pending-page .pd-modal-footer{
    position:sticky;
    bottom:0;

    display:flex;
    flex-wrap:wrap;
    justify-content:flex-end;
    gap:10px;

    padding:14px 20px;

    background:#f9fafb;
    border-top:1px solid #e5e7eb;
}

.pending-page .pd-summary{
    margin-bottom:18px;
    padding:12px 14px;

    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:10px;
}

.pending-page .pd-summary p{
    display:flex;
    flex-wrap:wrap;
    gap:6px;

    margin:0;

    font-size:13px;
    line-height:1.6;
}

.pending-page .pd-summary p + p{ margin-top:4px; }

.pending-page .pd-summary span{
    min-width:90px;
    color:#6b7280;
}

.pending-page .pd-summary strong{
    color:#111827;
    overflow-wrap:anywhere;
}

.pending-page .pd-summary .is-danger{ color:#b91c1c; }


/* ---------- FORM ---------- */

.pending-page .pd-field{
    margin-bottom:16px;
}

.pending-page .pd-label{
    display:block;
    margin-bottom:6px;

    color:#374151;
    font-size:13px;
    font-weight:600;
}

.pending-page .pd-req{ color:#dc2626; }

.pending-page .pd-input{
    display:block;
    width:100%;

    padding:10px 12px;

    background:#fff;
    color:#111827;

    border:1px solid #d1d5db;
    border-radius:10px;

    font-size:13px;
    font-family:inherit;
    line-height:1.5;

    outline:none;
    transition:.2s;
}

.pending-page textarea.pd-input{
    resize:vertical;
    min-height:84px;
}

.pending-page .pd-input:focus{
    border-color:#d97706;
    box-shadow:0 0 0 3px rgba(217,119,6,.18);
}

.pending-page .pd-custom{
    margin-bottom:16px;
    padding:14px;

    background:#fffbeb;
    border:1px dashed #fcd34d;
    border-radius:12px;
}

.pending-page .pd-custom .pd-field:last-child{ margin-bottom:0; }

.pending-page .pd-row{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:12px;
}

.pending-page .pd-alert-danger{
    margin-bottom:16px;
    padding:12px 14px;

    background:#fef2f2;
    border:1px solid #fecaca;
    border-radius:10px;

    color:#991b1b;
    font-size:13px;
}

.pending-page .pd-alert-danger ul{
    margin:0;
    padding-left:18px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media(max-width:768px){

    .pending-page .pd-header{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
        margin-bottom:16px;
    }

    .pending-page .pd-heading h1{ font-size:22px; }
    .pending-page .pd-heading p{ font-size:12px; }

    .pending-page .pd-counter{
        font-size:12px;
        padding:7px 12px;
    }

    .pending-page .pd-toolbar{
        flex-direction:column;
        align-items:stretch;
    }

    .pending-page .pd-search{
        flex:1 1 auto;
        min-width:0;
        width:100%;
    }

    .pending-page .pd-cards{ gap:12px; }

    .pending-page .pd-card{ padding:14px; }

    .pending-page .pd-empty{ padding:36px 16px; }
}

@media(max-width:576px){

    .pending-page .pd-modal{
        align-items:flex-end;
        padding:0;
    }

    .pending-page .pd-modal-content{
        max-width:100%;
        border-radius:18px 18px 0 0;
    }

    .pending-page .pd-modal-header,
    .pending-page .pd-modal-body,
    .pending-page .pd-modal-footer{
        padding:14px 16px;
    }

    .pending-page .pd-modal-footer{
        flex-direction:column-reverse;
    }

    .pending-page .pd-modal-footer .pd-btn{
        width:100%;
    }

    .pending-page .pd-row{
        grid-template-columns:1fr;
        gap:0;
    }

    /* 16px mencegah iOS melakukan zoom otomatis saat input difokus */
    .pending-page .pd-input{ font-size:16px; }
}

@media(max-width:400px){

    .pending-page .pd-actions{
        flex-direction:column;
    }

    .pending-page .pd-actions .pd-btn{
        width:100%;
    }
}
@endsection


@section('scripts')
<script>
(function () {
    'use strict';

    // URL template dari route() Laravel, bukan string hardcode.
    const approveUrlTemplate = @json(route('pelanggaran.approve', ['id' => '__ID__']));
    const rejectUrlTemplate  = @json(route('pelanggaran.reject',  ['id' => '__ID__']));

    const approveModal = document.getElementById('approveModal');
    const rejectModal  = document.getElementById('rejectModal');
    const approveForm  = document.getElementById('approveForm');
    const rejectForm   = document.getElementById('rejectForm');
    const approveAturan = document.getElementById('approveAturan');
    const customDiv     = document.getElementById('customApproveDiv');

    function buildUrl(template, id) {
        return template.replace('__ID__', encodeURIComponent(id));
    }

    function clearErrors(boxId, listId) {
        document.getElementById(boxId).classList.add('is-hidden');
        document.getElementById(listId).innerHTML = '';
    }

    function showErrors(boxId, listId, messages) {
        const list = document.getElementById(listId);
        list.innerHTML = '';
        messages.forEach(function (msg) {
            const li = document.createElement('li');
            li.textContent = msg;
            list.appendChild(li);
        });
        document.getElementById(boxId).classList.remove('is-hidden');
    }

    /*
     * Tampilkan atau sembunyikan field pelanggaran custom.
     * Kolom custom harus dikosongkan saat disembunyikan, kalau tidak
     * nilainya ikut terkirim dan bisa menimpa pilihan lain.
     */
    function toggleCustomApprove(val) {
        const isCustom = val === 'custom';

        customDiv.classList.toggle('is-hidden', !isCustom);

        customDiv.querySelectorAll('input, select').forEach(function (el) {
            if (isCustom) {
                el.setAttribute('required', 'true');
            } else {
                el.removeAttribute('required');
                el.value = '';
            }
        });
    }

    function openApproveModal(btn) {
        const id           = btn.dataset.id;
        const studentName  = btn.dataset.siswa;
        const violation    = btn.dataset.pelanggaran;
        const preselected  = btn.dataset.aturanId;

        document.getElementById('approveSiswaName').innerText = studentName;
        document.getElementById('approvePelanggaran').innerText = violation;

        approveForm.action = buildUrl(approveUrlTemplate, id);
        approveForm.reset();

        // Preselect aturan yang awalnya dipilih pelapor, biar verifikator
        // tinggal mengkonfirmasi tanpa memilih ulang dari nol.
        if (preselected) {
            const exists = Array.prototype.some.call(approveAturan.options, function (o) {
                return o.value === preselected;
            });
            approveAturan.value = exists ? preselected : '';
        }

        toggleCustomApprove(approveAturan.value);
        clearErrors('approveFormErrors', 'approveFormErrorsList');

        approveModal.classList.add('active');
    }

    function openRejectModal(btn) {
        const id          = btn.dataset.id;
        const studentName = btn.dataset.siswa;

        document.getElementById('rejectSiswaName').innerText = studentName;

        rejectForm.action = buildUrl(rejectUrlTemplate, id);
        rejectForm.reset();
        clearErrors('rejectFormErrors', 'rejectFormErrorsList');

        rejectModal.classList.add('active');
    }

    function closeModal(modal) {
        modal.classList.remove('active');
    }

    document.querySelectorAll('.js-approve').forEach(function (btn) {
        btn.addEventListener('click', function () { openApproveModal(btn); });
    });

    document.querySelectorAll('.js-reject').forEach(function (btn) {
        btn.addEventListener('click', function () { openRejectModal(btn); });
    });

    approveAturan.addEventListener('change', function () {
        toggleCustomApprove(this.value);
    });

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            closeModal(document.getElementById(btn.dataset.closeModal));
        });
    });

    // Klik area gelap di luar modal untuk menutup.
    [approveModal, rejectModal].forEach(function (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });

    /*
     * Validasi sisi klien untuk field custom.
     *
     * Controller memeriksa `poin_custom === null`, tapi form yang kosong
     * mengirim string "" yang lolos aturan `nullable`, sehingga poin
     * tersimpan 0 tanpa warning. Cek di sini agar user dapat feedback.
     */
    approveForm.addEventListener('submit', function (e) {
        if (approveAturan.value !== 'custom') {
            return;
        }

        const nama = document.getElementById('approveCustomNama');
        const poin = document.getElementById('approveCustomPoin');
        const pesan = [];

        if (!nama.value.trim()) {
            pesan.push('Nama pelanggaran wajib diisi.');
        }

        if (poin.value === '' || Number(poin.value) < 0) {
            pesan.push('Poin sanksi wajib diisi dan tidak boleh negatif.');
        }

        if (pesan.length > 0) {
            e.preventDefault();
            showErrors('approveFormErrors', 'approveFormErrorsList', pesan);
        }
    });

    rejectForm.addEventListener('submit', function (e) {
        const catatan = document.getElementById('rejectCatatan');

        if (!catatan.value.trim()) {
            e.preventDefault();
            showErrors('rejectFormErrors', 'rejectFormErrorsList', [
                'Alasan penolakan wajib diisi.'
            ]);
        }
    });

    /* ---------- Pencarian kartu ---------- */
    const search   = document.getElementById('pdSearch');
    const count    = document.getElementById('pdCount');
    const noResult = document.getElementById('pdNoResult');
    const items    = document.querySelectorAll('.pending-page .js-item');

    if (search) {
        search.addEventListener('input', function () {
            const q = search.value.trim().toLowerCase();
            let shown = 0;

            items.forEach(function (el) {
                const match = !q || el.dataset.search.indexOf(q) !== -1;
                el.hidden = !match;
                if (match) shown++;
            });

            count.textContent = shown;
            noResult.classList.toggle('is-hidden', shown !== 0);
        });
    }

    /* ---------- Lightbox foto ---------- */
    const box      = document.getElementById('pdLightbox');
    const boxImg   = document.getElementById('pdLightboxImg');
    const boxClose = document.getElementById('pdLightboxClose');

    function closeLightbox() {
        box.classList.add('is-hidden');
        boxImg.src = '';
    }

    document.querySelectorAll('.js-zoom').forEach(function (btn) {
        btn.addEventListener('click', function () {
            boxImg.src = btn.dataset.src;
            box.classList.remove('is-hidden');
        });
    });

    boxClose.addEventListener('click', closeLightbox);
    box.addEventListener('click', function (e) {
        if (e.target === box) closeLightbox();
    });

    // Escape menutup lightbox atau modal yang sedang terbuka.
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        closeLightbox();
        closeModal(approveModal);
        closeModal(rejectModal);
    });
})();
</script>
@endsection
