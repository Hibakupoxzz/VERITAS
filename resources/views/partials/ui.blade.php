{{--
    ============================================================
    DESIGN SYSTEM — PRIMITIF BERSAMA
    ============================================================
    Di-@include di awal @section('styles') setiap halaman, SEBELUM
    style milik halaman itu sendiri.

    Urutan ini disengaja: aturan halaman ada setelah partial ini,
    jadi halaman tetap bisa override lewat cascade tanpa
    !important. Jadi primitive di sini harus benar-benar generik,
    jangan taruh variasi per halaman di file ini.

    Semua nilai memakai design token dari :root di layouts/app.
    Bagian TOKEN FALLBACK di bawah hanya mengisi token yang belum
    didefinisikan layout (specificity 0 lewat :where), sehingga
    nilai dari layout selalu menang.

    Jangan tulis ulang primitive berikut di view:
    - .pv-card, .pv-field, .pv-label, .pv-input  → sudah ada di partial
    - radius / font-size / spacing literal       → pakai var()
    ============================================================
--}}

/* =========================================================
   TOKEN FALLBACK
   Aman: :where() = specificity 0, jadi :root milik
   layouts/app otomatis menimpa nilai di bawah ini.
   ========================================================= */

:where(:root) {
    --sp-1: 2px;
    --sp-2: 4px;
    --sp-3: 6px;
    --sp-4: 8px;
    --sp-5: 10px;
    --sp-6: 12px;
    --sp-7: 16px;
    --sp-8: 20px;
    --sp-9: 32px;

    --fs-2xs: 10px;
    --fs-xs: 11px;
    --fs-sm: 12px;
    --fs-base: 13px;
    --fs-md: 14px;
    --fs-lg: 20px;
    --fs-xl: 28px;

    --fw-medium: 500;
    --fw-semibold: 600;
    --fw-bold: 700;

    --lh-tight: 1.25;
    --lh-normal: 1.5;

    --r-md: 10px;
    --r-lg: 12px;
    --r-xl: 16px;
    --r-full: 999px;

    --primary: #6d1408;
    --primary-dark: #5a1006;
    --primary-light: #b45a4d;
    --dark-2: #374151;

    --c-text: #111827;
    --c-text-muted: #6b7280;
    --c-text-soft: #9ca3af;
    --c-border: #e5e7eb;
    --c-border-soft: #f3f4f6;
    --c-surface: #ffffff;
    --c-surface-alt: #f9fafb;
    --c-thead-bg: #f9fafb;
    --c-thead-text: #374151;
    --c-row-hover: #fafafa;

    --c-badge-danger-bg: #fee2e2;
    --c-badge-danger-text: #b91c1c;
    --c-badge-success-bg: #dcfce7;
    --c-badge-success-text: #166534;
    --c-badge-warning-bg: #fef3c7;
    --c-badge-warning-text: #92400e;
    --c-badge-info-bg: #e0e7ff;
    --c-badge-info-text: #3730a3;
    --c-badge-neutral-bg: #f3f4f6;

    --c-point-bg: var(--primary);
    --c-point-text: #ffffff;

    --dur: 0.2s;
    --focus-ring: 0 0 0 3px rgba(109, 20, 8, 0.15);
}


/* =========================================================
   BASE
   Link tidak bergaris bawah (termasuk menu sidebar).
   ========================================================= */

a {
    text-decoration: none;
}

.is-hidden {
    display: none !important;
}


/* =========================================================
   PAGE HEADER
   Dipakai semua halaman daftar & detail:
   judul + deskripsi di kiri, aksi di kanan.
   ========================================================= */

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: var(--sp-5);
    margin-bottom: var(--sp-8);
}

.page-heading {
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.page-heading h1 {
    margin: 0 0 var(--sp-2);
    color: var(--c-text);
    font-size: var(--fs-xl);
    font-weight: var(--fw-bold);
    line-height: var(--lh-tight);
    overflow-wrap: anywhere;
}

.page-heading p {
    margin: 0;
    color: var(--c-text-muted);
    font-size: var(--fs-base);
    overflow-wrap: anywhere;
}

@media (max-width: 640px) {
    .page-header {
        align-items: flex-start;
        flex-direction: column;
        gap: var(--sp-4);
    }

    .page-heading h1 {
        font-size: var(--fs-lg);
    }
}


/* =========================================================
   PAGE HEADER — varian form/detail (prefix pv-)
   Dipakai halaman form & detail yang punya ikon + judul.
   ========================================================= */

.pv-page-header {
    display: flex;
    align-items: center;
    gap: var(--sp-5);
    margin-bottom: var(--sp-9);
}

.pv-page-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    border-radius: var(--r-lg);
    background: #FBEAE8;
    color: var(--primary);
}

.pv-page-icon svg {
    width: 22px;
    height: 22px;
    stroke: var(--primary);
}

.pv-page-headtext {
    min-width: 0;
}

.pv-page-title {
    margin: 0;
    color: var(--c-text);
    font-size: var(--fs-lg);
    font-weight: var(--fw-bold);
    line-height: var(--lh-tight);
    overflow-wrap: anywhere;
}

.pv-page-sub {
    margin: 3px 0 0;
    color: var(--c-text-muted);
    font-size: var(--fs-sm);
    overflow-wrap: anywhere;
}

/* Ikon di dalam header kartu */
.header-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    flex: 0 0 44px;
    border-radius: var(--r-lg);
    background: #FBEAE8;
    color: var(--primary);
    font-size: var(--fs-lg);
}

/* Label di atas angka pada kartu statistik */
.stat-label {
    display: block;
    margin-bottom: var(--sp-1);
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

/* Avatar siswa di daftar & tabel */
.student-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    border-radius: var(--r-full);
    background: rgba(109, 20, 8, 0.08);
    color: var(--primary);
    font-size: var(--fs-md);
    font-weight: var(--fw-semibold);
}


/* =========================================================
   BUTTONS
   ========================================================= */

.btn,
.btn-primary,
.btn-secondary,
.btn-danger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--sp-3);
    min-height: 42px;
    padding: 11px var(--sp-8);
    border: 1px solid transparent;
    border-radius: var(--r-lg);
    font-family: inherit;
    font-size: var(--fs-base);
    font-weight: var(--fw-semibold);
    line-height: 1;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background var(--dur) ease,
                border-color var(--dur) ease,
                transform var(--dur) ease;
}

.btn {
    padding: 0 var(--sp-7);
}

.btn-primary {
    background: var(--primary);
    color: var(--c-point-text);
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-secondary {
    border-color: var(--c-border);
    background: var(--c-surface);
    color: var(--dark-2);
}

.btn-secondary:hover {
    background: var(--c-surface-alt);
}

.btn-danger {
    background: var(--c-badge-danger-text);
    color: #fff;
}

.btn-danger:hover {
    filter: brightness(0.9);
}

.btn:focus-visible,
.btn-primary:focus-visible,
.btn-secondary:focus-visible,
.btn-danger:focus-visible {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}


/* =========================================================
   CARD + TABLE
   ========================================================= */

.table-card {
    width: 100%;
    min-width: 0;
    overflow: hidden;
    border: 1px solid var(--c-border);
    border-radius: var(--r-xl);
    background: var(--c-surface);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.table-responsive {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: var(--c-thead-bg);
}

th {
    padding: var(--sp-6);
    color: var(--c-thead-text);
    font-size: var(--fs-base);
    font-weight: var(--fw-semibold);
    line-height: 1.3;
    text-align: left;
    white-space: nowrap;
}

/*
 * overflow-wrap: break-word (BUKAN anywhere).
 * "anywhere" ikut memperkecil lebar minimum kolom, sehingga kolom
 * tabel bisa mengerut dan teks terpotong huruf per huruf.
 */
td {
    padding: var(--sp-6);
    border-bottom: 1px solid var(--c-border-soft);
    color: var(--dark-2);
    font-size: var(--fs-base);
    vertical-align: middle;
    overflow-wrap: break-word;
}

tbody tr {
    transition: background var(--dur) ease;
}

tbody tr:hover {
    background: var(--c-row-hover);
}


/* =========================================================
   STUDENT CELL
   ========================================================= */

.student-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.student-info strong {
    display: block;
    margin-bottom: var(--sp-1);
    color: var(--c-text);
    font-size: var(--fs-base);
    font-weight: var(--fw-semibold);
    line-height: var(--lh-normal);
    overflow-wrap: anywhere;
}

.student-info small {
    color: var(--c-text-muted);
    font-size: var(--fs-xs);
}


/* =========================================================
   BADGES
   ========================================================= */

.badge-danger,
.badge-kategori,
.badge-point {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--sp-2);
    padding: var(--sp-2) var(--sp-5);
    border-radius: var(--r-full);
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
    line-height: var(--lh-normal);
    max-width: 100%;
    white-space: nowrap;
}

/*
 * Badge pelanggaran berisi teks panjang (nama aturan), jadi HARUS
 * boleh membungkus. Sebelumnya white-space:nowrap + pill penuh
 * membuat teks keluar dari latar badge. Radius diganti var(--r-md)
 * supaya badge multi-baris tetap rapi.
 */
.badge-danger {
    justify-content: flex-start;
    background: var(--c-badge-danger-bg);
    color: var(--c-badge-danger-text);
    font-weight: var(--fw-bold);
    border-radius: var(--r-md);
    text-align: left;
    white-space: normal;
    overflow-wrap: anywhere;
}

.badge-kategori {
    background: var(--c-badge-info-bg);
    color: var(--c-badge-info-text);
}

.badge-point {
    min-width: 42px;
    background: var(--c-point-bg);
    color: var(--c-point-text);
    font-weight: var(--fw-bold);
    font-size: var(--fs-base);
}


/* =========================================================
   ALERT
   ========================================================= */

.alert,
.alert-success {
    display: flex;
    align-items: flex-start;
    gap: var(--sp-4);
    padding: var(--sp-5) var(--sp-7);
    margin-bottom: var(--sp-8);
    border-radius: var(--r-md);
    font-size: var(--fs-md);
    line-height: var(--lh-normal);
    overflow-wrap: anywhere;
}

.alert ul {
    margin: var(--sp-2) 0 0;
    padding-left: var(--sp-8);
    list-style: disc;
}

.alert-success {
    background: var(--c-badge-success-bg);
    color: var(--c-badge-success-text);
}

.alert-danger {
    background: var(--c-badge-danger-bg);
    color: var(--c-badge-danger-text);
}

.alert-danger ul {
    margin: 0;
    padding-left: 18px;
}

.alert-info {
    background: var(--c-badge-info-bg);
    color: var(--c-badge-info-text);
}


/* =========================================================
   BADGE STATUS (pending / diverifikasi / ditolak)
   ========================================================= */

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: var(--sp-2);
    padding: var(--sp-2) var(--sp-5);
    border-radius: var(--r-full);
    font-size: var(--fs-xs);
    font-weight: var(--fw-semibold);
    line-height: var(--lh-normal);
    white-space: nowrap;
}

.badge-pending {
    background: var(--c-badge-warning-bg);
    color: var(--c-badge-warning-text);
}

.badge-verified {
    background: var(--c-badge-success-bg);
    color: var(--c-badge-success-text);
}

.badge-rejected {
    background: var(--c-badge-danger-bg);
    color: var(--c-badge-danger-text);
}

/* Poin positif (sumbangan prestasi) */
.point-positive {
    color: var(--c-badge-success-text);
}


/* =========================================================
   EMPTY STATE
   ========================================================= */

/* Baris kosong di dalam <tbody> */
.empty {
    padding: var(--sp-9) var(--sp-7) !important;
    color: var(--c-text-muted);
    font-size: var(--fs-md);
    text-align: center;
}

.empty i,
.mobile-empty i {
    display: block;
    margin-bottom: var(--sp-5);
    font-size: 28px;
}

/* Kartu kosong pada daftar versi mobile */
.mobile-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--sp-9) var(--sp-7);
    border: 1px solid var(--c-border);
    border-radius: var(--r-xl);
    background: var(--c-surface);
    color: var(--c-text-soft);
    text-align: center;
}

.mobile-empty p {
    margin: 0;
    font-size: var(--fs-sm);
    overflow-wrap: anywhere;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--sp-9) var(--sp-7);
    color: var(--c-text-soft);
    font-size: var(--fs-base);
    text-align: center;
}

.empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    margin: 0 auto var(--sp-5);
    border-radius: var(--r-lg);
    background: var(--c-badge-neutral-bg);
    color: var(--c-text-soft);
    font-size: var(--fs-xl);
}


/* =========================================================
   FORM PRIMITIVES (prefix pv-)
   Dipakai create/edit/show × pelanggaran, siswa, prestasi.

   Alias: create/edit pelanggaran + prestasi memakai prefix
   `form-` untuk design language yang sama persis.
   Dua penamaan, satu definisi — jangan diduplikasi.
   ========================================================= */

.pv-card,
.form-card {
    width: 100%;
    margin-bottom: var(--sp-8);
    overflow: hidden;
    border: 1px solid var(--c-border);
    border-radius: var(--r-xl);
    background: var(--c-surface);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.pv-section-label,
.form-section-label {
    display: flex;
    align-items: center;
    gap: var(--sp-2);
    margin-bottom: var(--sp-7);
    color: var(--c-text-soft);
    font-size: var(--fs-2xs);
    font-weight: var(--fw-bold);
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.pv-field,
.form-group {
    min-width: 0;
    margin-bottom: var(--sp-7);
}

.pv-label,
.form-label,
.form-group label {
    display: block;
    margin-bottom: var(--sp-2);
    color: var(--dark-2);
    font-size: var(--fs-sm);
    font-weight: var(--fw-medium);
    overflow-wrap: anywhere;
}

.req {
    color: var(--c-badge-danger-text);
}

.pv-input,
.pv-select,
.pv-textarea,
.form-control {
    box-sizing: border-box;
    width: 100%;
    max-width: 100%;
    padding: 10px var(--sp-5);
    border: 1px solid var(--c-border);
    border-radius: var(--r-md);
    background: var(--c-surface);
    color: var(--c-text);
    font-family: inherit;
    font-size: var(--fs-md);
    line-height: var(--lh-normal);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.pv-input:focus,
.pv-select:focus,
.pv-textarea:focus,
.form-control:focus {
    border-color: var(--primary);
    box-shadow: var(--focus-ring);
    outline: none;
}

.pv-textarea,
.form-control.textarea,
textarea.form-control {
    min-height: 96px;
    resize: vertical;
}

/* Dua kolom field berdampingan (turun jadi 1 kolom di HP) */
.form-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: var(--sp-6);
}

@media (max-width: 576px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    /* 16px mencegah iOS zoom otomatis saat input difokus */
    .pv-input,
    .pv-select,
    .pv-textarea,
    .form-control {
        font-size: 16px;
    }
}

.pv-footer,
.form-footer {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: var(--sp-4);
    margin-top: var(--sp-8);
}

.pv-btn-primary,
.pv-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--sp-3);
    min-height: 42px;
    padding: 10px var(--sp-8);
    border-radius: var(--r-lg);
    font-family: inherit;
    font-size: var(--fs-md);
    font-weight: var(--fw-semibold);
    line-height: 1;
    text-decoration: none;
    cursor: pointer;
    max-width: 100%;
    text-align: center;
}

.pv-btn-primary {
    border: none;
    background: var(--primary);
    color: var(--c-point-text);
}

.pv-btn-primary:hover {
    background: var(--primary-dark);
}

.pv-btn-secondary {
    border: 1px solid var(--c-border);
    background: var(--c-surface);
    color: var(--dark-2);
    font-weight: var(--fw-medium);
}

.pv-btn-secondary:hover {
    background: var(--c-surface-alt);
}


/* =========================================================
   UPLOAD BOX
   ========================================================= */

.upload-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: var(--sp-4);
    width: 100%;
    padding: var(--sp-9) var(--sp-8);
    border: 1.5px dashed var(--c-border);
    border-radius: var(--r-xl);
    background: var(--c-surface-alt);
    text-align: center;
    cursor: pointer;
    transition: border-color var(--dur) ease, background var(--dur) ease;
}

.upload-box:hover {
    border-color: var(--primary-light);
    background: #FFF8F6;
}

.upload-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    border-radius: var(--r-full);
    background: #F9E9E6;
    color: var(--primary);
    font-size: var(--fs-xl);
}
