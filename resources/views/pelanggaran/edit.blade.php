@extends('layouts.app')

@section('styles')
<style>
    /* ── Base ── */
    *, *::before, *::after { box-sizing: border-box; }

    .pv-body {
        background: #f5f6fa;
        min-height: 100vh;
        /* padding: 2rem 1rem; */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* ── Page header ── */
    .pv-page-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 1.75rem;
    }
    .pv-page-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        background: #fff7ed;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .pv-page-icon svg { width: 22px; height: 22px; stroke: #ea580c; }
    .pv-page-title { font-size: 1.25rem; font-weight: 600; color: #1a1a2e; margin: 0; }
    .pv-page-sub   { font-size: 0.8rem; color: #6b7280; margin: 2px 0 0; }

    /* ── Cards ── */
    .pv-card {
        background: #fff;
        border: 1px solid #e8eaed;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
    }
    .pv-section-label {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #9ca3af;
        display: flex; align-items: center; gap: 6px;
        margin-bottom: 1rem;
    }
    .pv-section-label svg { width: 14px; height: 14px; flex-shrink: 0; }

    /* ── Siswa terpilih (select) ── */
    .pv-select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #e2e5ea;
        border-radius: 9px;
        font-size: 0.875rem;
        color: #1a1a2e;
        background: #fafbfc;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
        font-family: inherit;
        cursor: pointer;
    }
    .pv-select:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234,88,12,.1);
        background: #fff;
    }

    /* ── Form fields ── */
    .pv-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .pv-field   { margin-bottom: 14px; }
    .pv-field:last-child { margin-bottom: 0; }
    .pv-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }
    .pv-label span { color: #ea580c; }
    .pv-input, .pv-textarea {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #e2e5ea;
        border-radius: 9px;
        font-size: 0.875rem;
        color: #1a1a2e;
        background: #fafbfc;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
        font-family: inherit;
    }
    .pv-input:focus, .pv-textarea:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234,88,12,.1);
        background: #fff;
    }
    .pv-textarea { resize: vertical; min-height: 90px; }

    /* ── Poin ── */
    .pv-poin-wrap { display: flex; align-items: center; gap: 10px; }
    .pv-poin-wrap .pv-input { flex: 1; }
    .pv-poin-tag {
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #fed7aa;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 9px 14px;
        border-radius: 9px;
        white-space: nowrap;
        min-width: 80px;
        text-align: center;
        transition: all .15s;
    }

    /* ── Foto bukti saat ini ── */
    .pv-foto-existing {
        border: 1px solid #e2e5ea;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        display: inline-block;
    }
    .pv-foto-existing img {
        display: block;
        width: 160px;
        height: 120px;
        object-fit: cover;
    }
    .pv-foto-existing-label {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: rgba(0,0,0,.45);
        color: #fff;
        font-size: 0.68rem;
        padding: 4px 8px;
        text-align: center;
    }
    .pv-no-foto {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.85rem; color: #9ca3af;
        padding: 10px 0;
    }
    .pv-no-foto svg { width: 16px; height: 16px; stroke: #9ca3af; }

    /* ── Upload ── */
    .pv-upload-area {
        border: 2px dashed #e2e5ea;
        border-radius: 10px;
        padding: 1.25rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .15s, background .15s;
        position: relative;
        overflow: hidden;
        display: block;
        margin-top: 10px;
    }
    .pv-upload-area:hover   { border-color: #ea580c; background: #fff7ed; }
    .pv-upload-area.dragover { border-color: #ea580c; background: #fff7ed; }
    .pv-upload-area svg { width: 26px; height: 26px; stroke: #9ca3af; margin-bottom: 6px; }
    .pv-upload-text { font-size: 0.8rem; color: #6b7280; }
    .pv-upload-hint { font-size: 0.72rem; color: #9ca3af; margin-top: 3px; }
    .pv-upload-area input[type="file"] {
        position: absolute; inset: 0;
        opacity: 0; cursor: pointer;
        width: 100%; height: 100%;
    }

    /* ── Preview baru ── */
    .pv-foto-preview { margin-top: 10px; border-radius: 10px; overflow: hidden; display: none; position: relative; }
    .pv-foto-preview.visible { display: block; }
    .pv-foto-preview img { width: 100%; max-height: 180px; object-fit: cover; display: block; }
    .pv-foto-preview .pv-foto-remove {
        position: absolute; top: 8px; right: 8px;
        background: rgba(0,0,0,.55);
        border: none; border-radius: 50%;
        width: 28px; height: 28px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: background .15s;
    }
    .pv-foto-preview .pv-foto-remove:hover { background: rgba(0,0,0,.75); }
    .pv-foto-preview .pv-foto-remove svg { width: 14px; height: 14px; stroke: #fff; }

    /* ── Alert errors ── */
    .pv-alert-error {
        background: #fff1f1;
        border: 1px solid #fecaca;
        border-left: 4px solid #e53e3e;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 1rem;
        font-size: 0.85rem; color: #991b1b;
    }
    .pv-alert-error ul { margin: 6px 0 0 16px; }

    /* ── Footer ── */
    .pv-footer { display: flex; justify-content: flex-end; gap: 10px; padding-top: 0.5rem; }
    .pv-btn-primary {
        background: #ea580c; color: #fff; border: none;
        padding: 10px 24px; border-radius: 9px;
        font-size: 0.875rem; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 7px;
        transition: background .15s, transform .1s;
    }
    .pv-btn-primary:hover  { background: #c2410c; }
    .pv-btn-primary:active { transform: scale(.97); }
    .pv-btn-primary svg { width: 16px; height: 16px; stroke: #fff; }

    .pv-btn-secondary {
        background: #fff; color: #374151;
        border: 1px solid #e2e5ea;
        padding: 10px 20px; border-radius: 9px;
        font-size: 0.875rem; font-weight: 500;
        cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 7px;
        transition: background .15s;
    }
    .pv-btn-secondary:hover { background: #f9fafb; color: #374151; }
    .pv-btn-secondary svg { width: 15px; height: 15px; stroke: #6b7280; }

    /* ── Responsive ── */
    @media (max-width: 576px) {
        .pv-grid-2 { grid-template-columns: 1fr; }
        .pv-card   { padding: 1rem; }
        .pv-footer { flex-direction: column-reverse; }
        .pv-btn-primary, .pv-btn-secondary { width: 100%; justify-content: center; }
    }
    @media(max-width:768px){

    .pv-grid-2{
        grid-template-columns:1fr;
    }

    .pv-footer{
        flex-direction:column-reverse;
    }

    .pv-btn-primary,
    .pv-btn-secondary{
        width:100%;
        justify-content:center;
    }

}
</style>
@endsection

@section('content')
<div class="pv-body">
<div class="container" >

    {{-- Header --}}
    <div class="pv-page-header">
        <div class="pv-page-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </div>
        <div>
            <h1 class="pv-page-title">Edit Pelanggaran</h1>
            <p class="pv-page-sub">Perbarui data pelanggaran siswa</p>
        </div>
    </div>

    {{-- Validasi error --}}
    @if($errors->any())
    <div class="pv-alert-error">
        <strong>Data belum lengkap:</strong>
        <ul>
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('pelanggaran.update', $pelanggaran->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ── 1. Pilih Siswa ── --}}
        <div class="pv-card">
            <div class="pv-section-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="7" r="4"/><path d="M5.5 21a7 7 0 0 1 13 0"/>
                </svg>
                Data siswa
            </div>
            <div class="pv-field">
                <label class="pv-label" for="siswa_id">Siswa <span>*</span></label>
                <select name="siswa_id" id="siswa_id" class="pv-select" required>
                    @foreach($siswas as $siswa)
                        <option
                            value="{{ $siswa->id }}"
                            {{ $pelanggaran->siswa_id == $siswa->id ? 'selected' : '' }}>
                            {{ $siswa->nama }} — {{ $siswa->kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ── 2. Detail Pelanggaran ── --}}
        <div class="pv-card">
            <div class="pv-section-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Detail pelanggaran
            </div>

            <div class="pv-grid-2">
                <div class="pv-field">
                    <label class="pv-label" for="tanggal">Tanggal <span>*</span></label>
                    <input
                        type="date"
                        name="tanggal"
                        id="tanggal"
                        class="pv-input"
                        value="{{ old('tanggal', $pelanggaran->tanggal) }}"
                        required>
                </div>
                <div class="pv-field">
                    <label class="pv-label" for="jenis_pelanggaran">Jenis pelanggaran <span>*</span></label>
                    <input
                        type="text"
                        name="jenis_pelanggaran"
                        id="jenis_pelanggaran"
                        class="pv-input"
                        placeholder="Contoh: Terlambat, Tidak Memakai Dasi"
                        value="{{ old('jenis_pelanggaran', $pelanggaran->jenis_pelanggaran) }}"
                        required>
                </div>
            </div>

            <div class="pv-field">
                <label class="pv-label" for="poin">Poin keterlambatan <span>*</span></label>
                <div class="pv-poin-wrap">
                    <input
                        type="number"
                        name="poin"
                        id="poin"
                        class="pv-input"
                        placeholder="Masukkan poin"
                        min="1" max="100"
                        value="{{ old('poin', $pelanggaran->poin) }}"
                        required>
                    <div class="pv-poin-tag" id="poinTag">
                        {{ $pelanggaran->poin ? '−'.$pelanggaran->poin.' poin' : '— poin' }}
                    </div>
                </div>
            </div>

            <div class="pv-field">
                <label class="pv-label" for="catatan">Keterangan</label>
                <textarea
                    name="catatan"
                    id="catatan"
                    class="pv-textarea"
                    placeholder="Catatan tambahan...">{{ old('catatan', $pelanggaran->catatan) }}</textarea>
            </div>
        </div>

        {{-- ── 3. Foto Bukti ── --}}
        <div class="pv-card">
            <div class="pv-section-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Foto bukti
            </div>

            {{-- Foto saat ini --}}
            <div class="pv-field">
                <label class="pv-label">Foto saat ini</label>
                @if($pelanggaran->foto_bukti)
                    <div class="pv-foto-existing">
                        <img src="{{ asset('storage/'.$pelanggaran->foto_bukti) }}" alt="Foto bukti">
                        <div class="pv-foto-existing-label">Foto tersimpan</div>
                    </div>
                @else
                    <div class="pv-no-foto">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <line x1="3" y1="3" x2="21" y2="21"/>
                        </svg>
                        Belum ada foto terlampir
                    </div>
                @endif
            </div>

            {{-- Upload baru --}}
            <div class="pv-field" style="margin-bottom:0">
                <label class="pv-label">
                    {{ $pelanggaran->foto_bukti ? 'Ganti foto bukti' : 'Upload foto bukti' }}
                </label>
                <label class="pv-upload-area" id="uploadArea" for="foto_bukti">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 16 12 12 8 16"/>
                        <line x1="12" y1="12" x2="12" y2="21"/>
                        <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                    </svg>
                    <div class="pv-upload-text">Klik atau seret foto ke sini</div>
                    <div class="pv-upload-hint">JPG, PNG, WEBP · Maks. 5 MB</div>
                    <input type="file" name="foto_bukti" id="foto_bukti" accept="image/*">
                </label>

                <div class="pv-foto-preview" id="fotoPreview">
                    <img id="previewImg" src="" alt="Preview foto baru">
                    <button type="button" class="pv-foto-remove" id="removeBtn" title="Hapus foto baru">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="pv-footer">
            <a href="{{ route('pelanggaran.index') }}" class="pv-btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Kembali
            </a>
            <button type="submit" class="pv-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Simpan perubahan
            </button>
        </div>

    </form>
<footer style="
    padding: 120px 10px 10px 10px;
    text-align:center;
    color:#9CA3AF;
    font-size:13px; ">
    © {{ date('Y') }} VERITAS — Sistem Monitoring Pelanggaran Siswa.
    <br>
    Developed by
    <a href="https://kicauorgspark.my.id"
       target="_blank"
       style="color:#6D1408;font-weight:600;text-decoration:none;">
        KicawOrgspark
    </a>
</footer>
</div>
</div>

<script>
// Poin badge
const poinInput = document.getElementById('poin');
const poinTag   = document.getElementById('poinTag');
poinInput.addEventListener('input', function () {
    const v = parseInt(this.value);
    poinTag.textContent = v > 0 ? `−${v} poin` : '— poin';
});

// Preview foto baru
document.getElementById('foto_bukti').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('fotoPreview').classList.add('visible');
    };
    reader.readAsDataURL(file);
});

document.getElementById('removeBtn').addEventListener('click', function () {
    document.getElementById('foto_bukti').value = '';
    document.getElementById('fotoPreview').classList.remove('visible');
    document.getElementById('previewImg').src = '';
});

// Drag & drop
const area = document.getElementById('uploadArea');
['dragenter','dragover'].forEach(ev => area.addEventListener(ev, e => { e.preventDefault(); area.classList.add('dragover'); }));
['dragleave','drop'].forEach(ev => area.addEventListener(ev, () => area.classList.remove('dragover')));
</script>
@endsection
