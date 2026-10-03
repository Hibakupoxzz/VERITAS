@extends('layouts.app')

@section('title', 'Data Pelanggaran')
@section('page_title', 'Data Pelanggaran')

@section('content')

@php
    $isWalas   = auth()->user()->isWalas();
    $canAdd    = auth()->user()->canDirectAddPelanggaran();
    $kategoris = $pelanggarans->pluck('kategori')->filter()->unique()->values();
@endphp

<div class="pelanggaran-page">

    {{-- HEADER --}}
    <div class="pg-header">

        <div class="pg-heading">
            @if($isWalas)
                <h1>Pelanggaran Kelas {{ auth()->user()->kelas }}</h1>
                <p>Daftar pelanggaran siswa di kelas Anda</p>
            @else
                <h1>Data Pelanggaran</h1>
                <p>Daftar seluruh pelanggaran siswa</p>
            @endif
        </div>

        @if($canAdd)
        <a href="{{ route('pelanggaran.create') }}" class="pg-btn pg-btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Pelanggaran</span>
        </a>
        @elseif(auth()->user()->canReportPelanggaran())
        <a href="{{ route('lapor.index') }}" class="pg-btn pg-btn-primary">
            <i class="fa-solid fa-bullhorn"></i>
            <span>Lapor Pelanggaran</span>
        </a>
        @endif

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="pg-alert">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif


    {{-- RINGKASAN --}}
    <div class="pg-stats">

        <div class="pg-stat">
            <div class="pg-stat-icon"><i class="fa-solid fa-clipboard-list"></i></div>
            <div>
                <small>Total Pelanggaran</small>
                <strong>{{ $pelanggarans->count() }}</strong>
            </div>
        </div>

        <div class="pg-stat">
            <div class="pg-stat-icon pg-stat-warn"><i class="fa-solid fa-star-half-stroke"></i></div>
            <div>
                <small>Total Poin Dikurangi</small>
                <strong>-{{ $pelanggarans->sum('poin') }}</strong>
            </div>
        </div>

        <div class="pg-stat">
            <div class="pg-stat-icon pg-stat-info"><i class="fa-solid fa-user-group"></i></div>
            <div>
                <small>Siswa Terlibat</small>
                <strong>{{ $pelanggarans->pluck('siswa_id')->filter()->unique()->count() }}</strong>
            </div>
        </div>

    </div>


    {{-- TOOLBAR: CARI, FILTER, EXPORT --}}
    <div class="pg-toolbar">

        <div class="pg-filters">

            <div class="pg-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="search"
                    id="pgSearch"
                    placeholder="Cari siswa, pelanggaran, atau pelapor..."
                    autocomplete="off"
                >
            </div>

            <select id="pgKategori" class="pg-select" aria-label="Filter kategori">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ \Illuminate\Support\Str::lower($kat) }}">{{ $kat }}</option>
                @endforeach
            </select>

        </div>

        @if(!$isWalas)
        <div class="pg-exports">
            <a href="{{ route('pelanggaran.export.harian') }}" class="pg-btn pg-btn-outline">
                <i class="fa-solid fa-file-export"></i>
                <span>Export Hari Ini</span>
            </a>
            <a href="{{ route('pelanggaran.export.mingguan') }}" class="pg-btn pg-btn-outline">
                <i class="fa-solid fa-file-export"></i>
                <span>Export Mingguan</span>
            </a>
        </div>
        @endif

    </div>

    <div class="pg-result-info">
        Menampilkan <strong id="pgCount">{{ $pelanggarans->count() }}</strong> data
    </div>


    {{-- =========================
         DESKTOP TABLE
    ========================== --}}
    <div class="pg-card pg-desktop">

        <div class="pg-scroll">

            <table class="pg-table">

                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Pelanggaran</th>
                        <th>Pelapor</th>
                        <th>Poin</th>
                        <th>Foto</th>
                        @if(!$isWalas)
                        <th class="col-aksi">Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>

                    @forelse($pelanggarans as $pelanggaran)

                        @php
                            $namaSiswa = $pelanggaran->siswa?->nama ?? 'Laporan Umum (Tanpa Siswa)';
                            $katSlug   = \Illuminate\Support\Str::slug($pelanggaran->kategori ?? '');
                            $haystack  = \Illuminate\Support\Str::lower(
                                $namaSiswa . ' ' .
                                ($pelanggaran->siswa?->kelas ?? '') . ' ' .
                                $pelanggaran->jenis_pelanggaran . ' ' .
                                ($pelanggaran->pelapor?->name ?? '')
                            );
                        @endphp

                        <tr class="js-item"
                            data-search="{{ $haystack }}"
                            data-kategori="{{ \Illuminate\Support\Str::lower($pelanggaran->kategori ?? '') }}">

                            <td class="col-no">{{ $loop->iteration }}</td>

                            <td class="pg-nowrap">
                                {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y') }}
                            </td>

                            <td>
                                <div class="pg-student">
                                    <div class="pg-avatar">
                                        @if($pelanggaran->siswa)
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($pelanggaran->siswa->nama, 0, 1)) }}
                                        @else
                                            <i class="fa-solid fa-flag"></i>
                                        @endif
                                    </div>
                                    <div class="pg-student-text">
                                        <strong>{{ $namaSiswa }}</strong>
                                        <small>{{ $pelanggaran->siswa?->kelas ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>

                            <td class="col-violation">
                                <div class="pg-violation">
                                    <div class="pg-jenis">{{ $pelanggaran->jenis_pelanggaran }}</div>

                                    @if($pelanggaran->kategori)
                                        <span class="pg-kat pg-kat-{{ $katSlug }}">
                                            {{ $pelanggaran->kategori }}
                                        </span>
                                    @endif

                                    @if($pelanggaran->keterangan)
                                        <div class="pg-desc pg-clamp" title="{{ $pelanggaran->keterangan }}">
                                            {{ $pelanggaran->keterangan }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if($pelanggaran->pelapor)
                                    <div class="pg-reporter">
                                        <strong>{{ $pelanggaran->pelapor->name }}</strong>
                                        <small>{{ $pelanggaran->pelapor->role_label }}</small>
                                    </div>
                                @else
                                    <span class="pg-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="pg-poin">-{{ $pelanggaran->poin }}</span>

                                @if($pelanggaran->poin_sebelum !== null)
                                    <div class="pg-saldo">
                                        {{ $pelanggaran->poin_sebelum }}
                                        <i class="fa-solid fa-arrow-right"></i>
                                        {{ $pelanggaran->poin_sesudah }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($pelanggaran->foto_bukti)
                                    <button type="button"
                                            class="pg-thumb js-zoom"
                                            data-src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
                                            title="Klik untuk memperbesar">
                                        <img src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
                                             alt="Foto Bukti" loading="lazy">
                                    </button>
                                @else
                                    <span class="pg-muted">Tidak ada</span>
                                @endif
                            </td>

                            @if(!$isWalas)
                            <td class="col-aksi">
                                <div class="pg-actions">

                                    <a href="{{ route('pelanggaran.show', $pelanggaran->id) }}"
                                       class="pg-ico pg-ico-detail" title="Detail" aria-label="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    @if($canAdd)
                                    <a href="{{ route('pelanggaran.edit', $pelanggaran->id) }}"
                                       class="pg-ico pg-ico-edit" title="Edit" aria-label="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <form action="{{ route('pelanggaran.destroy', $pelanggaran->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="pg-ico pg-ico-delete"
                                                title="Hapus" aria-label="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif

                                </div>
                            </td>
                            @endif

                        </tr>

                    @empty

                        <tr>
                            <td colspan="{{ $isWalas ? 7 : 8 }}" class="pg-empty">
                                <i class="fa-solid fa-inbox"></i>
                                <span>Belum ada data pelanggaran</span>
                            </td>
                        </tr>

                    @endforelse

                    {{-- Muncul saat pencarian/filter tidak menemukan hasil --}}
                    <tr id="pgNoResultRow" hidden>
                        <td colspan="{{ $isWalas ? 7 : 8 }}" class="pg-empty">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Tidak ada data yang cocok dengan pencarian</span>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================
         MOBILE CARD
    ========================== --}}
    <div class="pg-mobile">

        @forelse($pelanggarans as $pelanggaran)

            @php
                $namaSiswa = $pelanggaran->siswa?->nama ?? 'Laporan Umum (Tanpa Siswa)';
                $katSlug   = \Illuminate\Support\Str::slug($pelanggaran->kategori ?? '');
                $haystack  = \Illuminate\Support\Str::lower(
                    $namaSiswa . ' ' .
                    ($pelanggaran->siswa?->kelas ?? '') . ' ' .
                    $pelanggaran->jenis_pelanggaran . ' ' .
                    ($pelanggaran->pelapor?->name ?? '')
                );
            @endphp

            <div class="pg-mcard js-item"
                 data-search="{{ $haystack }}"
                 data-kategori="{{ \Illuminate\Support\Str::lower($pelanggaran->kategori ?? '') }}">

                <div class="pg-mcard-top">
                    <div class="pg-mdate">
                        <i class="fa-regular fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->format('d/m/Y') }}
                    </div>
                    <span class="pg-poin">-{{ $pelanggaran->poin }}</span>
                </div>

                <div class="pg-student">
                    <div class="pg-avatar">
                        @if($pelanggaran->siswa)
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($pelanggaran->siswa->nama, 0, 1)) }}
                        @else
                            <i class="fa-solid fa-flag"></i>
                        @endif
                    </div>
                    <div class="pg-student-text">
                        <strong>{{ $namaSiswa }}</strong>
                        <small>{{ $pelanggaran->siswa?->kelas ?? '-' }}</small>
                    </div>
                </div>

                <div class="pg-violation pg-mviolation">
                    <div class="pg-jenis">{{ $pelanggaran->jenis_pelanggaran }}</div>

                    @if($pelanggaran->kategori)
                        <span class="pg-kat pg-kat-{{ $katSlug }}">{{ $pelanggaran->kategori }}</span>
                    @endif
                </div>

                @if($pelanggaran->keterangan)
                    <div class="pg-desc pg-clamp">{{ $pelanggaran->keterangan }}</div>
                @endif

                <div class="pg-mmeta">
                    @if($pelanggaran->poin_sebelum !== null)
                        <span>
                            <i class="fa-solid fa-scale-balanced"></i>
                            Saldo {{ $pelanggaran->poin_sebelum }}
                            <i class="fa-solid fa-arrow-right"></i>
                            {{ $pelanggaran->poin_sesudah }}
                        </span>
                    @endif

                    @if($pelanggaran->pelapor)
                        <span>
                            <i class="fa-solid fa-user"></i>
                            {{ $pelanggaran->pelapor->name }}
                            ({{ $pelanggaran->pelapor->role_label }})
                        </span>
                    @endif
                </div>

                @if($pelanggaran->foto_bukti)
                    <button type="button"
                            class="pg-thumb js-zoom"
                            data-src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}">
                        <img src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}"
                             alt="Foto Bukti" loading="lazy">
                    </button>
                @endif

                @if(!$isWalas)
                <div class="pg-mactions">

                    <a href="{{ route('pelanggaran.show', $pelanggaran->id) }}" class="pg-mbtn pg-ico-detail">
                        <i class="fa-solid fa-eye"></i> Detail
                    </a>

                    @if($canAdd)
                    <a href="{{ route('pelanggaran.edit', $pelanggaran->id) }}" class="pg-mbtn pg-ico-edit">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>

                    <form action="{{ route('pelanggaran.destroy', $pelanggaran->id) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pg-mbtn pg-ico-delete">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </form>
                    @endif

                </div>
                @endif

            </div>

        @empty

            <div class="pg-mempty">
                <i class="fa-solid fa-inbox"></i>
                <p>Belum ada data pelanggaran</p>
            </div>

        @endforelse

        <div class="pg-mempty" id="pgNoResultMobile" hidden>
            <i class="fa-solid fa-magnifying-glass"></i>
            <p>Tidak ada data yang cocok dengan pencarian</p>
        </div>

    </div>


    {{-- PAGINATION (hanya tampil jika $pelanggarans berupa paginator) --}}
    @if(method_exists($pelanggarans, 'hasPages') && $pelanggarans->hasPages())
        <div class="pg-pagination">
            {{ $pelanggarans->links() }}
        </div>
    @endif


    {{-- LIGHTBOX FOTO --}}
    <div class="pg-lightbox" id="pgLightbox" hidden>
        <button type="button" class="pg-lightbox-close" id="pgLightboxClose" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <img src="" alt="Foto Bukti" id="pgLightboxImg">
    </div>

</div>{{-- /.pelanggaran-page --}}


<script>
(function () {
    var search   = document.getElementById('pgSearch');
    var kategori = document.getElementById('pgKategori');
    var count    = document.getElementById('pgCount');
    var items    = document.querySelectorAll('.pelanggaran-page .js-item');
    var noRow    = document.getElementById('pgNoResultRow');
    var noMobile = document.getElementById('pgNoResultMobile');

    function applyFilter() {
        var q = (search.value || '').trim().toLowerCase();
        var k = kategori.value;
        var shown = 0;

        items.forEach(function (el) {
            var okQ = !q || el.dataset.search.indexOf(q) !== -1;
            var okK = !k || el.dataset.kategori === k;
            var show = okQ && okK;
            el.hidden = !show;
            if (show && el.tagName === 'TR') shown++;
        });

        // hitung berdasarkan baris tabel (1 baris = 1 data)
        var totalRows = document.querySelectorAll('.pelanggaran-page tr.js-item').length;
        count.textContent = totalRows ? shown : 0;

        var empty = totalRows > 0 && shown === 0;
        if (noRow) noRow.hidden = !empty;
        if (noMobile) noMobile.hidden = !empty;
    }

    if (search && kategori) {
        search.addEventListener('input', applyFilter);
        kategori.addEventListener('change', applyFilter);
    }

    // Lightbox foto
    var box   = document.getElementById('pgLightbox');
    var img   = document.getElementById('pgLightboxImg');
    var close = document.getElementById('pgLightboxClose');

    document.querySelectorAll('.pelanggaran-page .js-zoom').forEach(function (btn) {
        btn.addEventListener('click', function () {
            img.src = btn.dataset.src;
            box.hidden = false;
            document.body.style.overflow = 'hidden';
        });
    });

    function closeBox() {
        box.hidden = true;
        img.src = '';
        document.body.style.overflow = '';
    }

    close.addEventListener('click', closeBox);
    box.addEventListener('click', function (e) { if (e.target === box) closeBox(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !box.hidden) closeBox(); });
})();
</script>

@endsection


@section('styles')

@include('partials.ui')

/* =====================================================
   DATA PELANGGARAN  (semua di-scope ke .pelanggaran-page
   dengan prefix "pg-" supaya tidak bentrok dengan ui.blade)
===================================================== */

.pelanggaran-page{
    width:100%;
    max-width:100%;
}

.pelanggaran-page a{
    text-decoration:none;
}

.pelanggaran-page [hidden]{
    display:none !important;
}

.pelanggaran-page .pg-muted{
    color:#9ca3af;
    font-size:12px;
    white-space:nowrap;
}

.pelanggaran-page .pg-nowrap{
    white-space:nowrap;
}


/* ---------- HEADER ---------- */

.pelanggaran-page .pg-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.pelanggaran-page .pg-heading h1{
    margin:0;
    font-size:28px;
    font-weight:800;
    color:#111827;
    letter-spacing:-.3px;
}

.pelanggaran-page .pg-heading p{
    margin:4px 0 0;
    font-size:14px;
    color:#6b7280;
}


/* ---------- BUTTONS ---------- */

.pelanggaran-page .pg-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;

    min-height:42px;
    padding:0 16px;

    border-radius:12px;
    border:1px solid transparent;

    font-size:13px;
    font-weight:600;
    font-family:inherit;
    line-height:1;
    white-space:nowrap;

    cursor:pointer;
    transition:.2s;
}

.pelanggaran-page .pg-btn-primary{
    background:var(--primary);
    color:#fff;
    box-shadow:0 6px 16px rgba(110,15,6,.22);
}

.pelanggaran-page .pg-btn-primary:hover{
    transform:translateY(-2px);
    filter:brightness(1.08);
}

.pelanggaran-page .pg-btn-outline{
    background:#fff;
    color:var(--primary);
    border-color:#e5d5d2;
}

.pelanggaran-page .pg-btn-outline:hover{
    background:var(--primary);
    color:#fff;
    border-color:var(--primary);
}


/* ---------- ALERT ---------- */

.pelanggaran-page .pg-alert{
    display:flex;
    align-items:flex-start;
    gap:10px;

    background:#dcfce7;
    color:#166534;

    padding:13px 16px;
    margin-bottom:18px;

    border:1px solid #bbf7d0;
    border-radius:12px;

    font-size:14px;
    overflow-wrap:anywhere;
}


/* ---------- STATS ---------- */

.pelanggaran-page .pg-stats{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:14px;
    margin-bottom:18px;
}

.pelanggaran-page .pg-stat{
    display:flex;
    align-items:center;
    gap:14px;

    background:#fff;
    border:1px solid #eee7e5;
    border-radius:16px;

    padding:16px 18px;

    box-shadow:0 4px 14px rgba(0,0,0,.04);
}

.pelanggaran-page .pg-stat-icon{
    flex-shrink:0;

    display:flex;
    align-items:center;
    justify-content:center;

    width:44px;
    height:44px;

    border-radius:12px;

    background:#fee2e2;
    color:#b91c1c;

    font-size:17px;
}

.pelanggaran-page .pg-stat-warn{ background:#fef3c7; color:#b45309; }
.pelanggaran-page .pg-stat-info{ background:#dbeafe; color:#1d4ed8; }

.pelanggaran-page .pg-stat small{
    display:block;
    color:#6b7280;
    font-size:12px;
}

.pelanggaran-page .pg-stat strong{
    display:block;
    margin-top:2px;
    color:#111827;
    font-size:22px;
    font-weight:800;
    line-height:1.1;
}


/* ---------- TOOLBAR ---------- */

.pelanggaran-page .pg-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:10px;
}

.pelanggaran-page .pg-filters{
    display:flex;
    align-items:center;
    gap:10px;
    flex:1 1 380px;
    min-width:0;
}

.pelanggaran-page .pg-search{
    position:relative;
    flex:1 1 auto;
    min-width:0;
    max-width:420px;
}

.pelanggaran-page .pg-search i{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:#9ca3af;
    font-size:13px;
    pointer-events:none;
}

.pelanggaran-page .pg-search input,
.pelanggaran-page .pg-select{
    height:42px;

    background:#fff;
    color:#111827;

    border:1px solid #e5e7eb;
    border-radius:12px;

    font-size:13px;
    font-family:inherit;

    outline:none;
    transition:.2s;
}

.pelanggaran-page .pg-search input{
    width:100%;
    padding:0 14px 0 38px;
}

.pelanggaran-page .pg-select{
    padding:0 34px 0 14px;
    cursor:pointer;
}

.pelanggaran-page .pg-search input:focus,
.pelanggaran-page .pg-select:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(110,15,6,.12);
}

.pelanggaran-page .pg-exports{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.pelanggaran-page .pg-result-info{
    margin:0 2px 10px;
    color:#6b7280;
    font-size:12px;
}

.pelanggaran-page .pg-result-info strong{
    color:#111827;
}


/* ---------- TABLE (DESKTOP) ---------- */

.pelanggaran-page .pg-card{
    background:#fff;
    border:1px solid #eee7e5;
    border-radius:18px;
    box-shadow:0 6px 20px rgba(0,0,0,.05);
    overflow:hidden;
}

.pelanggaran-page .pg-scroll{
    width:100%;
    overflow-x:auto;
}

.pelanggaran-page .pg-table{
    width:100%;
    min-width:1000px;
    border-collapse:collapse;
}

.pelanggaran-page .pg-table th{
    background:#faf7f6;
    color:#6b7280;

    font-size:11px;
    font-weight:700;
    letter-spacing:.5px;
    text-transform:uppercase;
    text-align:left;

    padding:13px 14px;

    border-bottom:1px solid #eee7e5;
    white-space:nowrap;
}

.pelanggaran-page .pg-table td{
    padding:14px;
    border-bottom:1px solid #f3eeec;

    font-size:13px;
    color:#374151;
    vertical-align:middle;
}

.pelanggaran-page .pg-table tbody tr:last-child td{
    border-bottom:none;
}

.pelanggaran-page .pg-table tbody tr.js-item:hover{
    background:#fffaf9;
}

.pelanggaran-page .col-no{ width:46px; color:#9ca3af; }
.pelanggaran-page .col-aksi{ width:130px; }
.pelanggaran-page .col-violation{ min-width:260px; max-width:380px; }


/* ---------- STUDENT ---------- */

.pelanggaran-page .pg-student{
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
}

.pelanggaran-page .pg-avatar{
    flex-shrink:0;

    display:flex;
    align-items:center;
    justify-content:center;

    width:36px;
    height:36px;

    border-radius:50%;

    background:#f3e4e1;
    color:var(--primary);

    font-size:14px;
    font-weight:700;
}

.pelanggaran-page .pg-student-text{
    display:flex;
    flex-direction:column;
    min-width:0;
}

.pelanggaran-page .pg-student-text strong{
    color:#111827;
    font-size:13px;
    font-weight:700;
    line-height:1.35;
    overflow-wrap:anywhere;
}

.pelanggaran-page .pg-student-text small{
    margin-top:2px;
    color:#6b7280;
    font-size:11px;
}


/* ---------- PELANGGARAN ---------- */

.pelanggaran-page .pg-violation{
    display:flex;
    flex-direction:column;
    align-items:flex-start;
    gap:6px;
}

.pelanggaran-page .pg-jenis{
    color:#991b1b;
    font-size:12.5px;
    font-weight:600;
    line-height:1.45;
    overflow-wrap:anywhere;
}

.pelanggaran-page .pg-kat{
    display:inline-block;

    padding:3px 10px;

    border-radius:999px;

    background:#e0e7ff;
    color:#3730a3;

    font-size:11px;
    font-weight:700;
    white-space:nowrap;
}

.pelanggaran-page .pg-kat-ringan{ background:#dcfce7; color:#166534; }
.pelanggaran-page .pg-kat-sedang{ background:#fef3c7; color:#92400e; }
.pelanggaran-page .pg-kat-berat{ background:#fee2e2; color:#991b1b; }

.pelanggaran-page .pg-desc{
    color:#6b7280;
    font-size:11.5px;
    line-height:1.5;
    overflow-wrap:anywhere;
}

.pelanggaran-page .pg-clamp{
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
}


/* ---------- PELAPOR ---------- */

.pelanggaran-page .pg-reporter{
    display:flex;
    flex-direction:column;
    min-width:90px;
}

.pelanggaran-page .pg-reporter strong{
    color:#111827;
    font-size:12.5px;
    font-weight:600;
}

.pelanggaran-page .pg-reporter small{
    margin-top:1px;
    color:#9ca3af;
    font-size:11px;
}


/* ---------- POIN ---------- */

.pelanggaran-page .pg-poin{
    display:inline-flex;
    align-items:center;
    justify-content:center;

    min-width:42px;
    height:28px;
    padding:0 11px;

    border-radius:999px;

    background:var(--primary);
    color:#fff;

    font-size:12px;
    font-weight:700;
}

.pelanggaran-page .pg-saldo{
    margin-top:5px;

    color:#6b7280;
    font-size:11px;
    font-weight:600;
    font-variant-numeric:tabular-nums;
    white-space:nowrap;
}

.pelanggaran-page .pg-saldo i{
    font-size:9px;
    margin:0 2px;
    color:#9ca3af;
}


/* ---------- FOTO ---------- */

.pelanggaran-page .pg-thumb{
    display:block;

    width:52px;
    height:52px;
    padding:0;

    border:1px solid #e5e7eb;
    border-radius:12px;

    background:#f3f4f6;

    overflow:hidden;
    cursor:zoom-in;

    transition:.2s;
}

.pelanggaran-page .pg-thumb:hover{
    transform:scale(1.06);
    border-color:var(--primary);
}

.pelanggaran-page .pg-thumb img{
    display:block;
    width:100%;
    height:100%;
    object-fit:cover;
}


/* ---------- AKSI (ICON BUTTON) ---------- */

.pelanggaran-page .pg-actions{
    display:flex;
    align-items:center;
    gap:6px;
}

.pelanggaran-page .pg-actions form{
    margin:0;
}

.pelanggaran-page .pg-ico{
    display:inline-flex;
    align-items:center;
    justify-content:center;

    width:34px;
    height:34px;
    padding:0;

    border:none;
    border-radius:10px;

    font-size:13px;
    cursor:pointer;

    transition:.15s;
}

.pelanggaran-page .pg-ico:hover{
    transform:translateY(-2px);
}

.pelanggaran-page .pg-ico-detail{ background:#dbeafe; color:#1d4ed8; }
.pelanggaran-page .pg-ico-edit  { background:#fef3c7; color:#92400e; }
.pelanggaran-page .pg-ico-delete{ background:#fee2e2; color:#b91c1c; }


/* ---------- EMPTY ---------- */

.pelanggaran-page .pg-empty,
.pelanggaran-page .pg-mempty{
    text-align:center;
    color:#9ca3af;
    padding:48px 16px;
}

.pelanggaran-page .pg-empty i,
.pelanggaran-page .pg-mempty i{
    display:block;
    margin-bottom:10px;
    font-size:30px;
}

.pelanggaran-page .pg-mempty{
    background:#fff;
    border:1px solid #eee7e5;
    border-radius:16px;
}

.pelanggaran-page .pg-mempty p{
    margin:0;
    font-size:13px;
}


/* ---------- PAGINATION ---------- */

.pelanggaran-page .pg-pagination{
    margin-top:18px;
    display:flex;
    justify-content:center;
}


/* ---------- MOBILE (BASE) ---------- */

.pelanggaran-page .pg-mobile{
    display:none;
}


/* ---------- LIGHTBOX ---------- */

.pelanggaran-page .pg-lightbox{
    position:fixed;
    inset:0;
    z-index:9999;

    display:flex;
    align-items:center;
    justify-content:center;

    background:rgba(17,24,39,.82);
    padding:24px;
}

.pelanggaran-page .pg-lightbox img{
    max-width:min(92vw, 900px);
    max-height:86vh;

    border-radius:14px;
    background:#fff;

    box-shadow:0 20px 60px rgba(0,0,0,.5);
}

.pelanggaran-page .pg-lightbox-close{
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


/* =====================================================
   RESPONSIVE
===================================================== */

@media(max-width:1100px){

    .pelanggaran-page .pg-toolbar{
        align-items:stretch;
    }

    .pelanggaran-page .pg-filters{
        flex:1 1 100%;
    }

    .pelanggaran-page .pg-search{
        max-width:none;
    }

}

@media(max-width:768px){

    .pelanggaran-page .pg-desktop{
        display:none;
    }

    .pelanggaran-page .pg-mobile{
        display:flex;
        flex-direction:column;
        gap:12px;
    }

    .pelanggaran-page .pg-header{
        flex-direction:column;
        align-items:stretch;
        gap:12px;
        margin-bottom:16px;
    }

    .pelanggaran-page .pg-heading h1{
        font-size:22px;
    }

    .pelanggaran-page .pg-heading p{
        font-size:12px;
    }

    .pelanggaran-page .pg-header .pg-btn{
        width:100%;
        min-height:44px;
    }

    .pelanggaran-page .pg-stats{
        grid-template-columns:1fr;
        gap:10px;
    }

    .pelanggaran-page .pg-stat{
        padding:12px 14px;
    }

    .pelanggaran-page .pg-stat strong{
        font-size:19px;
    }

    .pelanggaran-page .pg-filters{
        flex-direction:column;
        align-items:stretch;
    }

    .pelanggaran-page .pg-select{
        width:100%;
    }

    .pelanggaran-page .pg-exports{
        display:grid;
        grid-template-columns:1fr 1fr;
        width:100%;
    }

    .pelanggaran-page .pg-exports .pg-btn{
        padding:0 10px;
        font-size:12px;
    }

    /* kartu */
    .pelanggaran-page .pg-mcard{
        display:flex;
        flex-direction:column;
        gap:10px;

        background:#fff;
        border:1px solid #eee7e5;
        border-radius:16px;

        padding:14px;

        box-shadow:0 5px 18px rgba(0,0,0,.05);
        overflow:hidden;
    }

    .pelanggaran-page .pg-mcard-top{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;

        padding-bottom:10px;
        border-bottom:1px solid #f3eeec;
    }

    .pelanggaran-page .pg-mdate{
        display:flex;
        align-items:center;
        gap:6px;

        color:#9ca3af;
        font-size:11.5px;
    }

    .pelanggaran-page .pg-mviolation{
        gap:6px;
    }

    .pelanggaran-page .pg-mmeta{
        display:flex;
        flex-direction:column;
        gap:5px;

        color:#6b7280;
        font-size:11.5px;
        font-variant-numeric:tabular-nums;
    }

    .pelanggaran-page .pg-mmeta i{
        width:14px;
        color:#9ca3af;
        font-size:11px;
    }

    .pelanggaran-page .pg-mactions{
        display:grid;
        grid-template-columns:repeat(3, 1fr);
        gap:8px;

        padding-top:12px;
        border-top:1px solid #f3eeec;
    }

    .pelanggaran-page .pg-mactions form{
        margin:0;
    }

    .pelanggaran-page .pg-mbtn{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:6px;

        width:100%;
        min-height:40px;
        padding:0 6px;

        border:none;
        border-radius:10px;

        font-size:12px;
        font-weight:600;
        font-family:inherit;

        cursor:pointer;
    }

}

@media(max-width:380px){

    .pelanggaran-page .pg-mactions{
        grid-template-columns:1fr;
    }

    .pelanggaran-page .pg-exports{
        grid-template-columns:1fr;
    }

}
@endsection
