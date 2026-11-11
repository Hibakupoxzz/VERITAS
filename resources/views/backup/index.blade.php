@extends('layouts.app')

@section('title', 'Backup Data Tahunan')
@section('page_title', 'Backup Data Tahunan')

@section('styles')
@include('partials.ui')

.bk-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: var(--sp-6);
    margin-bottom: var(--sp-8);
}

.bk-card {
    padding: var(--sp-8);
    border: 1px solid var(--c-border);
    border-radius: var(--r-lg);
    background: var(--c-surface);
    box-shadow: 0 4px 12px rgba(0, 0, 0, .03);
}

.bk-card .num {
    color: var(--c-text);
    font-size: 24px;
    font-weight: var(--fw-bold);
    line-height: 1;
}

.bk-card .lbl {
    margin-top: var(--sp-2);
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
    letter-spacing: .03em;
    text-transform: uppercase;
}

.bk-body {
    padding: var(--sp-9);
}

.bk-note {
    margin-top: var(--sp-5);
    color: var(--c-text-muted);
    font-size: var(--fs-sm);
    line-height: var(--lh-relaxed);
}
@endsection

@section('content')

<div class="pv-page-header">
    <div class="pv-page-icon">
        <i class="fa-solid fa-file-export"></i>
    </div>
    <div class="pv-page-headtext">
        <h1 class="pv-page-title">Backup Data Tahunan</h1>
        <p class="pv-page-sub">Unduh seluruh pelanggaran &amp; prestasi dalam satu tahun (all time per tahun).</p>
    </div>
</div>

<div class="bk-grid">
    <div class="bk-card">
        <div class="num">{{ number_format($totalPelanggaran) }}</div>
        <div class="lbl">Total Pelanggaran</div>
    </div>
    <div class="bk-card">
        <div class="num">{{ number_format($totalPrestasi) }}</div>
        <div class="lbl">Total Prestasi</div>
    </div>
    <div class="bk-card">
        <div class="num">{{ $years->count() }}</div>
        <div class="lbl">Tahun Tersedia</div>
    </div>
</div>

<div class="pv-card">
    <div class="bk-body">
        <div class="pv-section-label">
            <i class="fa-solid fa-calendar-days"></i>
            Pilih Tahun
        </div>

        <form action="{{ route('backup.export') }}" method="GET">
            <div class="form-row">
                <div class="pv-field">
                    <label class="pv-label" for="tahun">Tahun</label>

                    @if($years->isNotEmpty())
                        <select id="tahun" name="tahun" class="pv-select">
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected($year === $defaultYear)>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="number" id="tahun" name="tahun" class="pv-input"
                               value="{{ $defaultYear }}" min="2000" max="2100">
                    @endif
                </div>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-download"></i>
                    Unduh Backup (Excel)
                </button>
            </div>
        </form>

        <p class="bk-note">
            File berisi dua sheet: <strong>Pelanggaran</strong> dan <strong>Prestasi</strong>,
            termasuk seluruh status pelanggaran (pending, diverifikasi, ditolak) pada tahun yang dipilih.
            Simpan file sebagai arsip akhir tahun.
        </p>
    </div>
</div>

@endsection
