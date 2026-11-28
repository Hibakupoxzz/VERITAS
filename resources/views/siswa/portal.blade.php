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

.portal-siswa-stat-hint {
    display: block;
    margin-top: 2px;
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
    line-height: var(--lh-tight);
}

/* ---------- Riwayat realtime ---------- */

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

.portal-live {
    display: inline-flex;
    align-items: center;
    gap: var(--sp-3);
    padding: var(--sp-2) var(--sp-4);
    border-radius: var(--r-full);
    background: rgba(22, 163, 74, .10);
    color: #15803d;
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
}

.portal-live-dot {
    width: 8px;
    height: 8px;
    border-radius: var(--r-full);
    background: #16a34a;
    animation: portal-live-pulse 1.6s ease-in-out infinite;
}

.portal-live.is-offline {
    background: rgba(107, 114, 128, .10);
    color: #6b7280;
}

.portal-live.is-offline .portal-live-dot {
    background: #9ca3af;
    animation: none;
}

@keyframes portal-live-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: .35; transform: scale(.75); }
}

.portal-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sp-3);
    padding: 0 var(--sp-8) var(--sp-6);
    border-bottom: 1px solid var(--c-border);
}

.portal-tab {
    padding: var(--sp-2) var(--sp-5);
    border: 1px solid var(--c-border);
    border-radius: var(--r-full);
    background: transparent;
    color: var(--c-text-muted);
    font-size: var(--fs-sm);
    font-weight: var(--fw-semibold);
    cursor: pointer;
    transition: all .15s ease;
}

.portal-tab:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.portal-tab.is-active {
    border-color: var(--primary);
    background: var(--primary);
    color: #fff;
}

.portal-timeline {
    list-style: none;
    margin: 0;
    padding: 0;
}

.portal-timeline-item {
    display: flex;
    align-items: flex-start;
    gap: var(--sp-6);
    padding: var(--sp-7) var(--sp-8);
    border-bottom: 1px solid var(--c-border);
}

.portal-timeline-item:last-child {
    border-bottom: 0;
}

.portal-timeline-item.is-baru {
    animation: portal-item-in .45s ease;
}

@keyframes portal-item-in {
    from { background: rgba(22, 163, 74, .10); }
    to { background: transparent; }
}

.portal-timeline-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    border-radius: var(--r-full);
    font-size: var(--fs-sm);
}

.portal-timeline-icon.is-pelanggaran {
    background: rgba(220, 38, 38, .10);
    color: #dc2626;
}

.portal-timeline-icon.is-prestasi {
    background: rgba(22, 163, 74, .10);
    color: #16a34a;
}

.portal-timeline-body {
    flex: 1 1 auto;
    min-width: 0;
}

.portal-timeline-title {
    margin: 0 0 2px;
    color: var(--c-text);
    font-size: var(--fs-sm);
    font-weight: var(--fw-semibold);
    line-height: var(--lh-normal);
    overflow-wrap: anywhere;
}

.portal-timeline-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--sp-3);
    margin: 0;
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
}

.portal-timeline-poin {
    flex: 0 0 auto;
    align-self: center;
    font-size: var(--fs-sm);
    font-weight: var(--fw-bold);
    white-space: nowrap;
}

.portal-timeline-poin.is-minus { color: #dc2626; }
.portal-timeline-poin.is-plus  { color: #16a34a; }

.portal-empty {
    padding: var(--sp-9) var(--sp-8);
    text-align: center;
    color: var(--c-text-muted);
    font-size: var(--fs-sm);
}

.portal-empty i {
    display: block;
    margin-bottom: var(--sp-4);
    font-size: var(--fs-xl);
    opacity: .5;
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

    .portal-timeline-item {
        padding: var(--sp-6) var(--sp-7);
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
         RINGKASAN
    ====================================================== --}}
    <div class="portal-siswa-stats">

        <div class="portal-siswa-stat">
            <div class="portal-siswa-stat-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span class="stat-label">Jumlah Pelanggaran</span>
                <div class="portal-siswa-stat-value" id="stat-pelanggaran">{{ $jumlahPelanggaran }}</div>
            </div>
        </div>

        <div class="portal-siswa-stat">
            <div class="portal-siswa-stat-icon">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <span class="stat-label">Jumlah Prestasi</span>
                <div class="portal-siswa-stat-value" id="stat-prestasi">{{ $jumlahPrestasi }}</div>
            </div>
        </div>

        <div class="portal-siswa-stat">
            <div class="portal-siswa-stat-icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <span class="stat-label">Poin Sekarang</span>
                <div class="portal-siswa-stat-value" id="stat-poin">{{ $saldoPoin }}</div>
            </div>
        </div>

    </div>


    {{-- =====================================================
         RIWAYAT REALTIME
    ====================================================== --}}
    <section class="table-card portal-siswa-section" id="portal-riwayat"
             data-endpoint="{{ route('siswa.portal.riwayat') }}"
             data-since="{{ $sinkronPada }}">

        <div class="portal-siswa-section-head">
            <h2 class="portal-siswa-section-title">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Riwayat Saya
            </h2>

            <span class="portal-live" id="portal-live">
                <span class="portal-live-dot"></span>
                <span id="portal-live-text">Realtime aktif</span>
            </span>
        </div>

        <div class="portal-tabs" role="tablist">
            <button type="button" class="portal-tab is-active" data-filter="semua">Semua</button>
            <button type="button" class="portal-tab" data-filter="pelanggaran">Pelanggaran</button>
            <button type="button" class="portal-tab" data-filter="prestasi">Prestasi</button>
        </div>

        <ul class="portal-timeline" id="portal-timeline">
            @forelse ($riwayat as $item)
                <li class="portal-timeline-item" data-tipe="{{ $item['tipe'] }}" data-id="{{ $item['id'] }}" data-tanggal="{{ $item['tanggal'] }}">
                    <span class="portal-timeline-icon is-{{ $item['tipe'] }}">
                        <i class="fa-solid {{ $item['tipe'] === 'pelanggaran' ? 'fa-shield-halved' : 'fa-award' }}"></i>
                    </span>
                    <div class="portal-timeline-body">
                        <p class="portal-timeline-title">{{ $item['judul'] }}</p>
                        <p class="portal-timeline-meta">
                            <span>{{ $item['tanggal_label'] ?? '-' }}</span>
                            <span>&middot;</span>
                            <span>{{ $item['keterangan'] }}</span>
                            @if ($item['tipe'] === 'pelanggaran' && $item['status'])
                                <span>&middot;</span>
                                <span>{{ ucfirst($item['status']) }}</span>
                            @endif
                        </p>
                    </div>
                    <span class="portal-timeline-poin {{ $item['tipe'] === 'pelanggaran' ? 'is-minus' : 'is-plus' }}">
                        {{ $item['tipe'] === 'pelanggaran' ? '-' : '+' }}{{ $item['poin'] }}
                    </span>
                </li>
            @empty
                <li class="portal-empty" id="portal-empty">
                    <i class="fa-solid fa-circle-check"></i>
                    Belum ada catatan pelanggaran maupun prestasi.
                </li>
            @endforelse
        </ul>
    </section>

    <p class="portal-siswa-note">
        Poin sekarang = 100 − poin pelanggaran yang sudah diverifikasi + poin prestasi.
        Riwayat diperbarui otomatis setiap beberapa detik.
    </p>

</div>

@endsection


@section('scripts')
<script>
(function () {
    var panel    = document.getElementById('portal-riwayat');
    if (!panel) return;

    var endpoint = panel.dataset.endpoint;
    var timeline = document.getElementById('portal-timeline');
    var liveBox  = document.getElementById('portal-live');
    var liveText = document.getElementById('portal-live-text');
    var since    = panel.dataset.since || null;
    var filter   = 'semua';
    var inFlight = false;

    function terapkanFilter() {
        timeline.querySelectorAll('.portal-timeline-item').forEach(function (el) {
            var cocok = filter === 'semua' || el.dataset.tipe === filter;
            el.style.display = cocok ? '' : 'none';
        });
    }

    function buatItem(item, baru) {
        var li = document.createElement('li');
        li.className = 'portal-timeline-item' + (baru ? ' is-baru' : '');
        li.dataset.tipe = item.tipe;

        var icon = document.createElement('span');
        icon.className = 'portal-timeline-icon is-' + item.tipe;
        var i = document.createElement('i');
        i.className = 'fa-solid ' + (item.tipe === 'pelanggaran' ? 'fa-shield-halved' : 'fa-award');
        icon.appendChild(i);

        var body = document.createElement('div');
        body.className = 'portal-timeline-body';

        var title = document.createElement('p');
        title.className = 'portal-timeline-title';
        title.textContent = item.judul || '-';
        body.appendChild(title);

        var meta = document.createElement('p');
        meta.className = 'portal-timeline-meta';
        var parts = [item.tanggal_label || '-', item.keterangan || ''];
        if (item.tipe === 'pelanggaran' && item.status) {
            parts.push(item.status.charAt(0).toUpperCase() + item.status.slice(1));
        }
        parts.filter(Boolean).forEach(function (text, index) {
            if (index > 0) {
                var sep = document.createElement('span');
                sep.innerHTML = '&middot;';
                meta.appendChild(sep);
            }
            var span = document.createElement('span');
            span.textContent = text;
            meta.appendChild(span);
        });
        body.appendChild(meta);

        var poin = document.createElement('span');
        poin.className = 'portal-timeline-poin ' + (item.tipe === 'pelanggaran' ? 'is-minus' : 'is-plus');
        poin.textContent = (item.tipe === 'pelanggaran' ? '-' : '+') + item.poin;

        li.appendChild(icon);
        li.appendChild(body);
        li.appendChild(poin);

        return li;
    }

    /**
     * Sisipkan item baru pada posisi sesuai urutan tanggal (terbaru di atas).
     */
    function sisipkanBerdasarkanTanggal(li) {
        var tanggal = li.dataset.tanggal || '';
        var anchor = null;

        timeline.querySelectorAll('.portal-timeline-item').forEach(function (el) {
            if (anchor) return;
            if ((el.dataset.tanggal || '') < tanggal) anchor = el;
        });

        if (anchor) {
            timeline.insertBefore(li, anchor);
        } else {
            timeline.appendChild(li);
        }
    }

    function setAngka(id, nilai) {
        var el = document.getElementById(id);
        if (el && nilai !== undefined && nilai !== null) {
            el.textContent = nilai;
        }
    }

    function setLive(aktif) {
        liveBox.classList.toggle('is-offline', !aktif);
        liveText.textContent = aktif ? 'Realtime aktif' : 'Koneksi terputus';
    }

    function tarikData() {
        if (inFlight || document.hidden) return;
        inFlight = true;

        var url = endpoint + (since ? '?since=' + encodeURIComponent(since) : '');

        fetch(url, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (res) {
                if (res.status === 401) {
                    window.location.reload();
                    return null;
                }
                if (!res.ok) throw new Error('Gagal memuat riwayat');
                return res.json();
            })
            .then(function (data) {
                if (!data) return;

                setAngka('stat-pelanggaran', data.jumlahPelanggaran);
                setAngka('stat-prestasi', data.jumlahPrestasi);
                setAngka('stat-poin', data.saldoPoin);

                var kosong = document.getElementById('portal-empty');
                if (data.riwayat && data.riwayat.length) {
                    if (kosong) kosong.remove();

                    // Sinkronkan: buang item lama yang id+tipenya muncul lagi.
                    data.riwayat.forEach(function (item) {
                        var lama = timeline.querySelector(
                            '.portal-timeline-item[data-tipe="' + item.tipe + '"][data-id="' + item.id + '"]'
                        );
                        if (lama) lama.remove();
                    });

                    data.riwayat.forEach(function (item) {
                        var li = buatItem(item, true);
                        li.dataset.id = item.id;
                        li.dataset.tanggal = item.tanggal || '';
                        sisipkanBerdasarkanTanggal(li);
                    });

                    terapkanFilter();
                    setLive(true);
                } else if (!timeline.querySelector('.portal-timeline-item')) {
                    if (!document.getElementById('portal-empty')) {
                        var li = document.createElement('li');
                        li.className = 'portal-empty';
                        li.id = 'portal-empty';
                        li.innerHTML = '<i class="fa-solid fa-circle-check"></i>Belum ada catatan pelanggaran maupun prestasi.';
                        timeline.appendChild(li);
                    }
                }

                since = data.sinkron_pada || since;
                setLive(true);
            })
            .catch(function () {
                setLive(false);
            })
            .finally(function () {
                inFlight = false;
            });
    }

    document.querySelectorAll('.portal-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.portal-tab').forEach(function (t) {
                t.classList.remove('is-active');
            });
            tab.classList.add('is-active');
            filter = tab.dataset.filter;
            terapkanFilter();
        });
    });

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) tarikData();
    });

    setInterval(tarikData, 15000);
})();
</script>
@endsection
